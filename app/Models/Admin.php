<?php

namespace App\Models;

use App\Core\Model;

class Admin extends Model
{
    public function authenticate(string $username, string $hashedPassword): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT ma_quan_tri, ten_dang_nhap, mat_khau FROM quan_tri_vien WHERE ten_dang_nhap = ? AND mat_khau = ?'
        );
        $stmt->execute([$username, $hashedPassword]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM quan_tri_vien WHERE ten_dang_nhap = ?');
        $stmt->execute([$username]);
        return $stmt->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM quan_tri_vien WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function create(string $username, string $email, string $fullName, string $hashedPassword): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO quan_tri_vien (ten_dang_nhap, email, ho_ten, mat_khau) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$username, $email, $fullName, $hashedPassword]);
    }

    public function resetPassword(string $email, string $hashedPassword): void
    {
        $stmt = $this->db->prepare('UPDATE quan_tri_vien SET mat_khau = ? WHERE email = ?');
        $stmt->execute([$hashedPassword, $email]);
    }

    public function saveResetToken(int $adminId, string $token, string $expiry): void
    {
        // Đảm bảo cột tồn tại
        $this->ensureResetColumns();
        $stmt = $this->db->prepare(
            'UPDATE quan_tri_vien SET reset_token = ?, reset_expires = ? WHERE ma_quan_tri = ?'
        );
        $stmt->execute([$token, $expiry, $adminId]);
    }

    public function findByResetToken(string $token): ?array
    {
        $this->ensureResetColumns();
        $stmt = $this->db->prepare(
            'SELECT * FROM quan_tri_vien WHERE reset_token = ? AND reset_expires > NOW()'
        );
        $stmt->execute([$token]);
        return $stmt->fetch() ?: null;
    }

    public function clearResetToken(int $adminId): void
    {
        $stmt = $this->db->prepare(
            'UPDATE quan_tri_vien SET reset_token = NULL, reset_expires = NULL WHERE ma_quan_tri = ?'
        );
        $stmt->execute([$adminId]);
    }

    private function ensureResetColumns(): void
    {
        static $checked = false;
        if ($checked) return;
        $checked = true;

        $cols = $this->db->query("SHOW COLUMNS FROM quan_tri_vien LIKE 'reset_token'")->fetch();
        if (!$cols) {
            $this->db->exec(
                "ALTER TABLE quan_tri_vien
                 ADD COLUMN `reset_token`   varchar(64)  DEFAULT NULL,
                 ADD COLUMN `reset_expires` datetime     DEFAULT NULL"
            );
        }
    }
}
