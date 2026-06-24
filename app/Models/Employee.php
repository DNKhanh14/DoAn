<?php

namespace App\Models;

use App\Core\Model;

class Employee extends Model
{
    private ?bool $hasJobTitleColumn = null;

    public function getAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM nhan_vien ORDER BY ma_nhan_vien ASC');
        $rows = $stmt->fetchAll();
        // Loại bỏ trùng lặp theo ma_nhan_vien
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

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM nhan_vien WHERE ma_nhan_vien = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function create(array $data): void
    {
        if ($this->hasJobTitleColumn()) {
            $stmt = $this->db->prepare(
                'INSERT INTO nhan_vien (ten, ho_dem, so_dien_thoai, email, chuc_vu) VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $data['ten'],
                $data['ho_dem'],
                $data['so_dien_thoai'],
                $data['email'],
                $data['chuc_vu'] ?? null,
            ]);
            return;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO nhan_vien (ten, ho_dem, so_dien_thoai, email) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['ten'],
            $data['ho_dem'],
            $data['so_dien_thoai'],
            $data['email'],
        ]);
    }

    public function update(int $id, array $data): void
    {
        if ($this->hasJobTitleColumn()) {
            $stmt = $this->db->prepare(
                'UPDATE nhan_vien SET ten = ?, ho_dem = ?, so_dien_thoai = ?, email = ?, chuc_vu = ? WHERE ma_nhan_vien = ?'
            );
            $stmt->execute([
                $data['ten'],
                $data['ho_dem'],
                $data['so_dien_thoai'],
                $data['email'],
                $data['chuc_vu'] ?? null,
                $id,
            ]);
            return;
        }

        $stmt = $this->db->prepare(
            'UPDATE nhan_vien SET ten = ?, ho_dem = ?, so_dien_thoai = ?, email = ? WHERE ma_nhan_vien = ?'
        );
        $stmt->execute([
            $data['ten'],
            $data['ho_dem'],
            $data['so_dien_thoai'],
            $data['email'],
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM nhan_vien WHERE ma_nhan_vien = ?');
        $stmt->execute([$id]);
    }

    private function hasJobTitleColumn(): bool
    {
        if ($this->hasJobTitleColumn === null) {
            $stmt = $this->db->query("SHOW COLUMNS FROM nhan_vien LIKE 'chuc_vu'");
            $exists = (bool) $stmt->fetch();

            if (!$exists) {
                // Tự động thêm cột nếu chưa có
                $this->db->exec("ALTER TABLE nhan_vien ADD COLUMN `chuc_vu` varchar(100) DEFAULT NULL");
            }

            $this->hasJobTitleColumn = true;
        }

        return $this->hasJobTitleColumn;
    }
}
