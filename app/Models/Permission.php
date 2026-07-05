<?php

namespace App\Models;

use App\Core\Model;

class Permission extends Model
{
    /** Danh sách tất cả modules trong hệ thống */
    public const MODULES = [
        'dashboard'  => 'Bản tin',
        'booking'    => 'Lịch hẹn',
        'pos'        => 'Thu ngân',
        'crm'        => 'Khách hàng',
        'services'   => 'Dịch vụ',
        'inventory'  => 'Kho hàng',
        'employees'  => 'Nhân viên',
        'hr'         => 'Lương & HR',
        'reports'    => 'Thống kê',
        // accounts: chỉ super_admin được dùng, kiểm tra hard-code trong AccountsController
    ];

    /** Danh sách chức vụ cố định (không thay đổi) */
    public const DEFAULT_ROLES = ['Quản lý', 'Lễ tân', 'Thợ chính', 'Thợ phụ'];

    /**
     * Lấy tất cả quyền, trả về dạng [ten_chuc_vu => [module => co_phep]]
     */
    public function getAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM chuc_vu_quyen ORDER BY ma_chuc_vu');
        $rows = $stmt->fetchAll();

        $result = [];
        $modules = array_keys(self::MODULES);
        foreach ($rows as $row) {
            $role = $row['ten_chuc_vu'];
            foreach ($modules as $module) {
                $result[$role][$module] = (bool) ($row[$module] ?? false);
            }
        }
        return $result;
    }

    /**
     * Lấy quyền của một chức vụ, trả về [module => bool]
     */
    public function getByRole(string $role): array
    {
        // super_admin luôn có toàn quyền — không cần tra DB
        if ($role === 'super_admin') {
            return array_fill_keys(array_keys(self::MODULES), true);
        }

        $stmt = $this->db->prepare('SELECT * FROM chuc_vu_quyen WHERE ten_chuc_vu = ?');
        $stmt->execute([$role]);
        $row = $stmt->fetch();

        $result = [];
        foreach (array_keys(self::MODULES) as $module) {
            $result[$module] = (bool) ($row[$module] ?? false);
        }

        return $result;
    }

    /**
     * Kiểm tra một quyền cụ thể
     */
    public function check(string $role, string $module): bool
    {
        // super_admin luôn có toàn quyền
        if ($role === 'super_admin') {
            return true;
        }

        $permissions = $this->getByRole($role);
        return $permissions[$module] ?? false;
    }

    /**
     * Lưu toàn bộ quyền từ form (dạng [role][module] = 0/1)
     */
    public function saveAll(array $data): void
    {
        $modules = array_keys(self::MODULES);

        foreach ($data as $role => $modulePerms) {
            if ($role === 'super_admin') continue; // super_admin không thay đổi quyền

            // Xây dựng SET clause
            $sets = [];
            $values = [];
            foreach ($modules as $module) {
                $sets[]   = "`{$module}` = ?";
                $values[] = isset($modulePerms[$module]) ? 1 : 0;
            }
            $values[] = $role; // WHERE

            $sql = 'UPDATE chuc_vu_quyen SET ' . implode(', ', $sets) . ' WHERE ten_chuc_vu = ?';
            $this->db->prepare($sql)->execute($values);
        }
    }

    /**
     * Lấy danh sách tất cả chức vụ — luôn trả về 4 chức vụ cố định + super_admin
     */
    public function getAllRoles(): array
    {
        return ['super_admin', 'Quản lý', 'Lễ tân', 'Thợ chính', 'Thợ phụ'];
    }

    /**
     * Thêm chức vụ mới với quyền mặc định (không có quyền gì)
     */
    public function addRole(string $role): void
    {
        $cols   = implode(', ', array_map(fn($m) => "`{$m}`", array_keys(self::MODULES)));
        $zeros  = implode(', ', array_fill(0, count(self::MODULES), '0'));
        $this->db->prepare(
            "INSERT IGNORE INTO chuc_vu_quyen (ten_chuc_vu, {$cols}) VALUES (?, {$zeros})"
        )->execute([$role]);
    }

    /**
     * Xóa chức vụ (không được xóa super_admin)
     */
    public function deleteRole(string $role): bool
    {
        if ($role === 'super_admin') return false;
        $stmt = $this->db->prepare('DELETE FROM chuc_vu_quyen WHERE ten_chuc_vu = ?');
        $stmt->execute([$role]);
        return true;
    }

    /**
     * ensureTable() — không cần tạo bảng vì đã có trong SQL schema
     * Giữ lại phương thức để không phá vỡ các call cũ bên trong class
     */
    private function ensureTable(): void
    {
        // Không cần thực hiện gì — bảng đã được tạo sẵn bằng databarber.sql
    }

    private function seedDefaults(): void
    {
        $defaults = [
            'super_admin' => ['dashboard'=>1,'booking'=>1,'pos'=>1,'crm'=>1,'services'=>1,'inventory'=>1,'employees'=>1,'hr'=>1,'reports'=>1],
            'Quản lý'     => ['dashboard'=>1,'booking'=>1,'pos'=>1,'crm'=>1,'services'=>1,'inventory'=>1,'employees'=>1,'hr'=>1,'reports'=>1],
            'Lễ tân'      => ['dashboard'=>1,'booking'=>1,'pos'=>1,'crm'=>1,'services'=>1,'inventory'=>1,'employees'=>1,'hr'=>1,'reports'=>1],
            'Thợ chính'   => ['dashboard'=>1,'booking'=>1,'pos'=>1,'crm'=>1,'services'=>1,'inventory'=>1,'employees'=>1,'hr'=>1,'reports'=>1],
            'Thợ phụ'     => ['dashboard'=>1,'booking'=>1,'pos'=>1,'crm'=>1,'services'=>1,'inventory'=>1,'employees'=>1,'hr'=>1,'reports'=>1],
        ];

        $stmt = $this->db->prepare(
            'INSERT IGNORE INTO chuc_vu_quyen (chuc_vu, module, co_phep) VALUES (?, ?, ?)'
        );

        foreach ($defaults as $role => $modules) {
            foreach ($modules as $module => $allowed) {
                $stmt->execute([$role, $module, $allowed]);
            }
        }
    }
}
