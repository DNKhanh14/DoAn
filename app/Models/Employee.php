<?php

namespace App\Models;

use App\Core\Model;

class Employee extends Model
{
    private ?bool $hasJobTitleColumn = null;

    public function getAll(): array
    {
        $stmt = $this->db->query(
            'SELECT nv.*, COALESCE(cv.ten_chuc_vu, \'\') AS chuc_vu
             FROM nhan_vien nv
             LEFT JOIN chuc_vu_quyen cv ON nv.ma_chuc_vu = cv.ma_chuc_vu
             ORDER BY nv.ma_nhan_vien ASC'
        );
        $rows = $stmt->fetchAll();
        $seen = [];
        $result = [];
        foreach ($rows as $row) {
            $id = (int) $row['ma_nhan_vien'];
            if (!isset($seen[$id])) {
                $seen[$id] = true;
                $result[] = $row;
            }
        }
        return $result;
    }

    /**
     * Lấy danh sách nhân viên cho trang đặt lịch khách hàng.
     * Ưu tiên lấy thợ có chức vụ 'Thợ chính', nếu không có thì trả về tất cả.
     */
    public function getBarbers(): array
    {
        $stmt = $this->db->prepare(
            "SELECT nv.*,
                    COALESCE(cv.ten_chuc_vu, '') AS chuc_vu
             FROM nhan_vien nv
             LEFT JOIN chuc_vu_quyen cv ON nv.ma_chuc_vu = cv.ma_chuc_vu
             ORDER BY nv.ma_nhan_vien ASC"
        );
        $stmt->execute();
        $all = $stmt->fetchAll();

        // Lọc 'Thợ chính' nếu có
        $barbers = array_values(array_filter($all, fn($e) => ($e['chuc_vu'] ?? '') === 'Thợ chính'));

        // Nếu không có thợ chính → trả về tất cả để trang đặt lịch không trống
        $rows = !empty($barbers) ? $barbers : $all;

        $seen = [];
        $result = [];
        foreach ($rows as $row) {
            $id = (int) $row['ma_nhan_vien'];
            if (!isset($seen[$id])) {
                $seen[$id] = true;
                $result[] = $row;
            }
        }
        return $result;
    }

    /**
     * Tìm nhân viên ít lịch đặt nhất trong 30 ngày tới.
     */
    public function getLeastBusyBarber(): ?int
    {
        $stmt = $this->db->prepare(
            "SELECT nv.ma_nhan_vien,
                    COUNT(lh.ma_lich_hen) AS so_lich
             FROM nhan_vien nv
             LEFT JOIN lich_hen lh
                ON lh.ma_nhan_vien = nv.ma_nhan_vien
               AND lh.da_huy = 0
               AND lh.thoi_gian_bat_dau BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 30 DAY)
             GROUP BY nv.ma_nhan_vien
             ORDER BY so_lich ASC, nv.ma_nhan_vien ASC
             LIMIT 1"
        );
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ? (int) $row['ma_nhan_vien'] : null;
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM nhan_vien')->fetchColumn();
    }

    public function getPaginated(int $offset, int $limit): array
    {
        $stmt = $this->db->prepare(
            'SELECT nv.*, COALESCE(cv.ten_chuc_vu, \'\') AS chuc_vu
             FROM nhan_vien nv
             LEFT JOIN chuc_vu_quyen cv ON nv.ma_chuc_vu = cv.ma_chuc_vu
             ORDER BY nv.ma_nhan_vien ASC LIMIT ? OFFSET ?'
        );
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT nv.*, COALESCE(cv.ten_chuc_vu, \'\') AS chuc_vu
             FROM nhan_vien nv
             LEFT JOIN chuc_vu_quyen cv ON nv.ma_chuc_vu = cv.ma_chuc_vu
             WHERE nv.ma_nhan_vien = ?'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** Tra ma_chuc_vu từ tên chức vụ */
    private function resolveRoleId(string $tenChucVu): ?int
    {
        if ($tenChucVu === '') return null;
        $stmt = $this->db->prepare('SELECT ma_chuc_vu FROM chuc_vu_quyen WHERE ten_chuc_vu = ? LIMIT 1');
        $stmt->execute([$tenChucVu]);
        $row = $stmt->fetch();
        return $row ? (int) $row['ma_chuc_vu'] : null;
    }

    public function create(array $data): void
    {
        // Nếu truyền tên chức vụ (string) thì resolve sang ma_chuc_vu
        $maChucVu = isset($data['ma_chuc_vu'])
            ? ($data['ma_chuc_vu'] ?: null)
            : $this->resolveRoleId($data['chuc_vu'] ?? '');

        $stmt = $this->db->prepare(
            'INSERT INTO nhan_vien (ten, ho_dem, so_dien_thoai, email, ma_chuc_vu, luong_co_ban) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['ten'],
            $data['ho_dem'],
            $data['so_dien_thoai'],
            $data['email'],
            $maChucVu,
            (float) ($data['luong_co_ban'] ?? 0),
        ]);
    }

    public function update(int $id, array $data): void
    {
        // Nếu truyền tên chức vụ (string) thì resolve sang ma_chuc_vu
        $maChucVu = isset($data['ma_chuc_vu'])
            ? ($data['ma_chuc_vu'] ?: null)
            : $this->resolveRoleId($data['chuc_vu'] ?? '');

        $stmt = $this->db->prepare(
            'UPDATE nhan_vien SET ten = ?, ho_dem = ?, so_dien_thoai = ?, email = ?, ma_chuc_vu = ?, luong_co_ban = ? WHERE ma_nhan_vien = ?'
        );
        $stmt->execute([
            $data['ten'],
            $data['ho_dem'],
            $data['so_dien_thoai'],
            $data['email'],
            $maChucVu,
            (float) ($data['luong_co_ban'] ?? 0),
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM nhan_vien WHERE ma_nhan_vien = ?');
        $stmt->execute([$id]);
    }

    private function ensureBaseSalaryColumn(): void
    {
        // Không cần kiểm tra — cột đã có trong schema mới
    }
}
