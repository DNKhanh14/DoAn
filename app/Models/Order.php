<?php

namespace App\Models;

use App\Core\Model;
use Exception;

class Order extends Model
{
    public function getRecent(int $limit = 50): array
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, c.ten, c.ho_dem FROM don_hang o
             LEFT JOIN khach_hang c ON o.ma_khach_hang = c.ma_khach_hang
             ORDER BY o.ngay_tao DESC LIMIT ?'
        );
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM don_hang')->fetchColumn();
    }

    public function getPaginated(int $offset, int $limit): array
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, c.ten, c.ho_dem FROM don_hang o
             LEFT JOIN khach_hang c ON o.ma_khach_hang = c.ma_khach_hang
             ORDER BY o.ngay_tao DESC LIMIT ? OFFSET ?'
        );
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, c.ten, c.ho_dem, c.so_dien_thoai
             FROM don_hang o
             LEFT JOIN khach_hang c ON o.ma_khach_hang = c.ma_khach_hang
             WHERE o.ma_don_hang = ?'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function getItems(int $orderId): array
    {
        if ($this->hasColumn('chi_tiet_don_hang', 'ma_nhan_vien')) {
            $stmt = $this->db->prepare(
                'SELECT oi.*, e.ten AS emp_fname, e.ho_dem AS emp_lname
                 FROM chi_tiet_don_hang oi
                 LEFT JOIN nhan_vien e ON oi.ma_nhan_vien = e.ma_nhan_vien
                 WHERE oi.ma_don_hang = ?'
            );
        } else {
            $stmt = $this->db->prepare('SELECT * FROM chi_tiet_don_hang WHERE ma_don_hang = ?');
        }
        $stmt->execute([$orderId]);

        return $stmt->fetchAll();
    }

    public function generateOrderCode(): string
    {
        $stmt = $this->db->query('SELECT ma_don_hang FROM don_hang ORDER BY ma_don_hang DESC LIMIT 1');
        $last = (int) ($stmt->fetchColumn() ?: 0);
        return 'HD' . str_pad((string) ($last + 1), 6, '0', STR_PAD_LEFT);
    }

    public function create(array $order, array $items): int
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO don_hang (ma_khach_hang, ma_lich_hen, tong_truoc_giam, giam_gia, tong_cong, phuong_thuc_thanh_toan, trang_thai, ghi_chu)
                 VALUES (?,?,?,?,?,?,?,?)'
            );
            $stmt->execute([
                $order['ma_khach_hang'] ?: null,
                $order['ma_lich_hen'] ?: null,
                $order['tong_truoc_giam'],
                $order['giam_gia'],
                $order['tong_cong'],
                $order['phuong_thuc_thanh_toan'],
                $order['trang_thai'] ?? 'completed',
                $order['ghi_chu'] ?? null,
            ]);

            $orderId = (int) $this->db->lastInsertId();

            $itemStmt = $this->db->prepare(
                'INSERT INTO chi_tiet_don_hang
                    (ma_don_hang, ma_dich_vu, ma_san_pham, ten, so_luong, don_gia, tong_dong, ma_nhan_vien, giam_gia_dong, giam_gia_phan_tram)
                 VALUES (?,?,?,?,?,?,?,?,?,?)'
            );

            $productModel = new Product();

            foreach ($items as $item) {
                $type      = $item['item_type'] ?? '';
                $refId     = (int) ($item['ref_id'] ?? 0);
                $maDichVu  = ($type === 'service')  ? $refId : null;
                $maSanPham = ($type === 'product')  ? $refId : null;

                $itemStmt->execute([
                    $orderId,
                    $maDichVu,
                    $maSanPham,
                    $item['item_name'],
                    $item['quantity'],
                    $item['unit_price'],
                    $item['line_total'],
                    $item['ma_nhan_vien'] ?? null,
                    $item['line_discount'] ?? 0,
                    !empty($item['discount_is_percent']) ? 1 : 0,
                ]);

                if ($maSanPham) {
                    $productModel->adjustStock($maSanPham, -1 * (int) $item['quantity']);
                }
            }

            $this->db->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getByClient(int $clientId, int $limit = 50): array
    {
        if (!table_exists('don_hang')) {
            return [];
        }
        $stmt = $this->db->prepare(
            'SELECT o.*,
                    (SELECT COUNT(*) FROM chi_tiet_don_hang oi WHERE oi.ma_don_hang = o.ma_don_hang AND oi.ma_dich_vu IS NOT NULL) AS so_dich_vu
             FROM don_hang o
             WHERE o.ma_khach_hang = ?
             ORDER BY o.ngay_tao DESC
             LIMIT ?'
        );
        $stmt->bindValue(1, $clientId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countServiceVisitsByClient(int $clientId): int
    {
        if (!table_exists('don_hang') || !table_exists('chi_tiet_don_hang')) {
            return 0;
        }
        $stmt = $this->db->prepare(
            "SELECT COUNT(DISTINCT o.ma_don_hang)
             FROM don_hang o
             INNER JOIN chi_tiet_don_hang oi ON oi.ma_don_hang = o.ma_don_hang AND oi.ma_dich_vu IS NOT NULL
             WHERE o.ma_khach_hang = ? AND o.trang_thai = 'completed'"
        );
        $stmt->execute([$clientId]);
        return (int) $stmt->fetchColumn();
    }

    public function delete(int $id): bool
    {
        $this->db->beginTransaction();
        try {
            // Hoàn kho sản phẩm trước khi xóa
            $items = $this->getItems($id);
            $productModel = new Product();
            foreach ($items as $item) {
                if (($item['ma_san_pham'] ?? null)) {
                    $productModel->adjustStock((int) $item['ma_san_pham'], (int) $item['so_luong']);
                }
            }
            $this->db->prepare('DELETE FROM chi_tiet_don_hang WHERE ma_don_hang = ?')->execute([$id]);
            $this->db->prepare('DELETE FROM don_hang WHERE ma_don_hang = ?')->execute([$id]);
            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function updateNote(int $id, string $note, string $paymentMethod): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE don_hang SET ghi_chu = ?, phuong_thuc_thanh_toan = ? WHERE ma_don_hang = ?'
        );
        return $stmt->execute([trim($note), $paymentMethod, $id]);
    }

    private function hasColumn(string $table, string $column): bool
    {
        try {
            $stmt = $this->db->prepare("SHOW COLUMNS FROM `{$table}` LIKE ?");
            $stmt->execute([$column]);

            return $stmt->rowCount() > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
