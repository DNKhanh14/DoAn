<?php

namespace App\Models;

use App\Core\Model;

class Admin extends Model
{
    // ── Xác thực ─────────────────────────────────────────────────────────

    public function authenticate(string $username, string $hashedPassword): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT nd.*, cv.ten_chuc_vu AS chuc_vu
             FROM nguoi_dung nd
             JOIN chuc_vu_quyen cv ON nd.ma_chuc_vu = cv.ma_chuc_vu
             WHERE nd.ten_dang_nhap = ? AND nd.mat_khau = ?'
        );
        $stmt->execute([$username, $hashedPassword]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT nd.*, cv.ten_chuc_vu AS chuc_vu
             FROM nguoi_dung nd
             JOIN chuc_vu_quyen cv ON nd.ma_chuc_vu = cv.ma_chuc_vu
             WHERE nd.ten_dang_nhap = ?'
        );
        $stmt->execute([$username]);
        return $stmt->fetch() ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT nd.*, cv.ten_chuc_vu AS chuc_vu
             FROM nguoi_dung nd
             JOIN chuc_vu_quyen cv ON nd.ma_chuc_vu = cv.ma_chuc_vu
             WHERE nd.email = ?'
        );
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT nd.*, cv.ten_chuc_vu AS chuc_vu
             FROM nguoi_dung nd
             JOIN chuc_vu_quyen cv ON nd.ma_chuc_vu = cv.ma_chuc_vu
             WHERE nd.ma_nguoi_dung = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    // ── CRUD tài khoản ───────────────────────────────────────────────────

    /**
     * Lấy danh sách tất cả tài khoản, kèm thông tin nhân viên liên kết
     */
    public function getAll(): array
    {
        $sql = "SELECT nd.*, cv.ten_chuc_vu AS chuc_vu,
                       nv.ten AS nv_ten, nv.ho_dem AS nv_ho_dem
                FROM nguoi_dung nd
                JOIN chuc_vu_quyen cv ON nd.ma_chuc_vu = cv.ma_chuc_vu
                LEFT JOIN nhan_vien nv ON nd.ma_nhan_vien = nv.ma_nhan_vien
                ORDER BY nd.ma_nguoi_dung ASC";
        return $this->db->query($sql)->fetchAll();
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM nguoi_dung')->fetchColumn();
    }

    public function getAllPaginated(int $offset, int $limit): array
    {
        $stmt = $this->db->prepare(
            "SELECT nd.*, cv.ten_chuc_vu AS chuc_vu,
                    nv.ten AS nv_ten, nv.ho_dem AS nv_ho_dem
             FROM nguoi_dung nd
             JOIN chuc_vu_quyen cv ON nd.ma_chuc_vu = cv.ma_chuc_vu
             LEFT JOIN nhan_vien nv ON nd.ma_nhan_vien = nv.ma_nhan_vien
             ORDER BY nd.ma_nguoi_dung ASC LIMIT ? OFFSET ?"
        );
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create(string $username, string $email, string $fullName, string $hashedPassword): void
    {
        // Mặc định tạo tài khoản với chức vụ Lễ tân (ma_chuc_vu = 3)
        $stmt = $this->db->prepare(
            'INSERT INTO nguoi_dung (ten_dang_nhap, email, ho_ten, mat_khau, ma_chuc_vu) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$username, $email, $fullName, $hashedPassword, 3]);
    }

    /**
     * Tạo tài khoản đầy đủ (có chức vụ + liên kết nhân viên)
     */
    public function createFull(array $data): int
    {
        $maChucVu = $this->resolveChucVu($data['chuc_vu'] ?? null, $data['ma_chuc_vu'] ?? null);
        $stmt = $this->db->prepare(
            'INSERT INTO nguoi_dung (ten_dang_nhap, email, ho_ten, mat_khau, ma_chuc_vu, ma_nhan_vien)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['ten_dang_nhap'],
            $data['email'],
            $data['ho_ten'],
            $data['mat_khau'],
            $maChucVu,
            $data['ma_nhan_vien'] ?: null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Cập nhật thông tin tài khoản (không đổi mật khẩu)
     */
    public function updateAccount(int $id, array $data): void
    {
        $maChucVu = $this->resolveChucVu($data['chuc_vu'] ?? null, $data['ma_chuc_vu'] ?? null);
        $stmt = $this->db->prepare(
            'UPDATE nguoi_dung
             SET ho_ten = ?, email = ?, ma_chuc_vu = ?, ma_nhan_vien = ?
             WHERE ma_nguoi_dung = ?'
        );
        $stmt->execute([
            $data['ho_ten'],
            $data['email'],
            $maChucVu,
            $data['ma_nhan_vien'] ?: null,
            $id,
        ]);
    }

    /**
     * Đổi mật khẩu
     */
    public function updatePassword(int $id, string $hashedPassword): void
    {
        $stmt = $this->db->prepare('UPDATE nguoi_dung SET mat_khau = ? WHERE ma_nguoi_dung = ?');
        $stmt->execute([$hashedPassword, $id]);
    }

    /**
     * Xóa tài khoản (không được xóa super_admin duy nhất)
     */
    public function deleteAccount(int $id): bool
    {
        // Không cho xóa nếu chỉ còn 1 super_admin
        $count = (int) $this->db->query(
            "SELECT COUNT(*) FROM nguoi_dung nd
             JOIN chuc_vu_quyen cv ON nd.ma_chuc_vu = cv.ma_chuc_vu
             WHERE cv.ten_chuc_vu = 'super_admin'"
        )->fetchColumn();

        $current = $this->findById($id);
        if ($current && ($current['chuc_vu'] ?? '') === 'super_admin' && $count <= 1) {
            return false;
        }

        $stmt = $this->db->prepare('DELETE FROM nguoi_dung WHERE ma_nguoi_dung = ?');
        $stmt->execute([$id]);
        return true;
    }

    /**
     * Cập nhật chức vụ
     */
    public function updateRole(int $id, string $role): void
    {
        $maChucVu = $this->resolveChucVu($role, null);
        $stmt = $this->db->prepare('UPDATE nguoi_dung SET ma_chuc_vu = ? WHERE ma_nguoi_dung = ?');
        $stmt->execute([$maChucVu, $id]);
    }

    /**
     * Đồng bộ chức vụ từ nhân viên sang tài khoản liên kết
     */
    public function syncRoleFromEmployee(int $employeeId, string $chucVu): void
    {
        $maChucVu = $this->resolveChucVu($chucVu, null);
        $stmt = $this->db->prepare(
            'UPDATE nguoi_dung nd
             JOIN chuc_vu_quyen cv ON nd.ma_chuc_vu = cv.ma_chuc_vu
             SET nd.ma_chuc_vu = ?
             WHERE nd.ma_nhan_vien = ? AND cv.ten_chuc_vu != \'super_admin\''
        );
        $stmt->execute([$maChucVu, $employeeId]);
    }

    /**
     * Hủy liên kết tài khoản khi nhân viên bị xóa
     */
    public function unlinkEmployee(int $employeeId): void
    {
        $stmt = $this->db->prepare(
            'UPDATE nguoi_dung SET ma_nhan_vien = NULL WHERE ma_nhan_vien = ?'
        );
        $stmt->execute([$employeeId]);
    }

    /**
     * Kiểm tra nhân viên đã có tài khoản chưa
     */
    public function getAccountByEmployeeId(int $employeeId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT nd.*, cv.ten_chuc_vu AS chuc_vu
             FROM nguoi_dung nd
             JOIN chuc_vu_quyen cv ON nd.ma_chuc_vu = cv.ma_chuc_vu
             WHERE nd.ma_nhan_vien = ?'
        );
        $stmt->execute([$employeeId]);
        return $stmt->fetch() ?: null;
    }

    // ── Reset mật khẩu ────────────────────────────────────────────────────

    public function resetPassword(string $email, string $hashedPassword): void
    {
        $stmt = $this->db->prepare('UPDATE nguoi_dung SET mat_khau = ? WHERE email = ?');
        $stmt->execute([$hashedPassword, $email]);
    }

    public function saveResetToken(int $adminId, string $token, string $expiry): void
    {
        $stmt = $this->db->prepare(
            'UPDATE nguoi_dung SET reset_token = ?, reset_expires = ? WHERE ma_nguoi_dung = ?'
        );
        $stmt->execute([$token, $expiry, $adminId]);
    }

    public function findByResetToken(string $token): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT nd.*, cv.ten_chuc_vu AS chuc_vu
             FROM nguoi_dung nd
             JOIN chuc_vu_quyen cv ON nd.ma_chuc_vu = cv.ma_chuc_vu
             WHERE nd.reset_token = ? AND nd.reset_expires > NOW()'
        );
        $stmt->execute([$token]);
        return $stmt->fetch() ?: null;
    }

    public function clearResetToken(int $adminId): void
    {
        $stmt = $this->db->prepare(
            'UPDATE nguoi_dung SET reset_token = NULL, reset_expires = NULL WHERE ma_nguoi_dung = ?'
        );
        $stmt->execute([$adminId]);
    }

    // ── Helper ────────────────────────────────────────────────────────────

    /**
     * Chuyển tên chức vụ (string) sang ma_chuc_vu (int FK)
     * Ưu tiên ma_chuc_vu nếu đã có, fallback tra theo ten_chuc_vu
     */
    private function resolveChucVu(?string $tenChucVu, ?int $maChucVu): int
    {
        if ($maChucVu) return $maChucVu;

        if ($tenChucVu) {
            $stmt = $this->db->prepare('SELECT ma_chuc_vu FROM chuc_vu_quyen WHERE ten_chuc_vu = ?');
            $stmt->execute([$tenChucVu]);
            $row = $stmt->fetch();
            if ($row) return (int) $row['ma_chuc_vu'];
        }

        // Default: Lễ tân = 3
        return 3;
    }
}
