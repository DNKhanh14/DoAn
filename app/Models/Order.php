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
        if (!$this->hasColumn('don_hang', 'ma_don')) {
            return 'HD' . date('ymd') . rand(100, 999);
        }

        $stmt = $this->db->query('SELECT ma_don_hang FROM don_hang ORDER BY ma_don_hang DESC LIMIT 1');
        $last = (int) ($stmt->fetchColumn() ?: 0);

        return 'HD' . str_pad((string) ($last + 1), 6, '0', STR_PAD_LEFT);
    }

    public function create(array $order, array $items): int
    {
        $this->db->beginTransaction();
        try {
            $hasCode = $this->hasColumn('don_hang', 'ma_don');
            $orderCode = $order['ma_don'] ?? $this->generateOrderCode();

            if ($hasCode) {
                $stmt = $this->db->prepare(
                    'INSERT INTO don_hang (ma_khach_hang, ma_lich_hen, tong_truoc_giam, giam_gia, tong_cong, phuong_thuc_thanh_toan, trang_thai, ghi_chu, ma_don)
                     VALUES (?,?,?,?,?,?,?,?,?)'
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
                    $orderCode,
                ]);
            } else {
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
            }

            $orderId = (int) $this->db->lastInsertId();
            $hasEmp = $this->hasColumn('chi_tiet_don_hang', 'ma_nhan_vien');

            if ($hasEmp) {
                $itemStmt = $this->db->prepare(
                    'INSERT INTO chi_tiet_don_hang (ma_don_hang, loai, ma_tham_chieu, ten, so_luong, don_gia, tong_dong, ma_nhan_vien, giam_gia_dong, giam_gia_phan_tram)
                     VALUES (?,?,?,?,?,?,?,?,?,?)'
                );
            } else {
                $itemStmt = $this->db->prepare(
                    'INSERT INTO chi_tiet_don_hang (ma_don_hang, loai, ma_tham_chieu, ten, so_luong, don_gia, tong_dong)
                     VALUES (?,?,?,?,?,?,?)'
                );
            }

            $productModel = new Product();

            foreach ($items as $item) {
                if ($hasEmp) {
                    $itemStmt->execute([
                        $orderId, $item['item_type'], $item['ref_id'], $item['item_name'],
                        $item['quantity'], $item['unit_price'], $item['line_total'],
                        $item['ma_nhan_vien'] ?? null,
                        $item['line_discount'] ?? 0,
                        !empty($item['discount_is_percent']) ? 1 : 0,
                    ]);
                } else {
                    $itemStmt->execute([
                        $orderId, $item['item_type'], $item['ref_id'], $item['item_name'],
                        $item['quantity'], $item['unit_price'], $item['line_total'],
                    ]);
                }

                if ($item['item_type'] === 'product') {
                    $productModel->adjustStock((int) $item['ref_id'], -1 * (int) $item['quantity']);
                }
            }

            if (!empty($order['ma_khach_hang']) && !empty($order['diem_tich_luy'])) {
                (new Client())->addLoyaltyPoints((int) $order['ma_khach_hang'], (int) $order['diem_tich_luy']);
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
                    (SELECT COUNT(*) FROM chi_tiet_don_hang oi WHERE oi.ma_don_hang = o.ma_don_hang AND oi.loai = \'service\') AS so_dich_vu
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
             INNER JOIN chi_tiet_don_hang oi ON oi.ma_don_hang = o.ma_don_hang AND oi.loai = 'service'
             WHERE o.ma_khach_hang = ? AND o.trang_thai = 'completed'"
        );
        $stmt->execute([$clientId]);
        return (int) $stmt->fetchColumn();
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
