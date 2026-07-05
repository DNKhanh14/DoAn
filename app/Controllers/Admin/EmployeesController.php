<?php

namespace App\Controllers\Admin;

use App\Models\CommissionRate;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Service;
use Exception;

class EmployeesController extends AdminController
{
    // ─── Routes: GET/POST employees, POST ajax/employees ───────────────────

    public function index(): void
    {
        $this->requirePermission('employees');
        $pageTitle = 'Nhân viên';
        $do = 'Manage';

        // Read-only roles chỉ được xem danh sách
        if ($this->isReadOnly()) {
            $do = 'Manage';
        } elseif (isset($_GET['do']) && in_array($_GET['do'], ['Add', 'Edit'], true)) {
            $do = htmlspecialchars($_GET['do']);
        }

        if ($do === 'Manage') {
            $this->handleManage($pageTitle);
            return;
        }
        if ($do === 'Add') {
            $this->handleAdd($pageTitle);
            return;
        }
        $this->handleEdit($pageTitle);
    }

    /** Hoa hồng nhân viên — route employees/commission */
    public function commission(): void
    {
        $pageTitle = 'Tùy chỉnh hoa hồng';
        $message = null;
        $selectedEmployeeId = (int) ($_GET['ma_nhan_vien'] ?? $_POST['ma_nhan_vien'] ?? 0);

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_commission_rates'])) {
            $rates = new CommissionRate();
            $selectedEmployeeId = (int) ($_POST['ma_nhan_vien'] ?? 0);
            $parse = static function ($v): ?float {
                if ($v === '' || $v === null) {
                    return null;
                }
                return (float) str_replace(',', '.', (string) $v);
            };
            foreach ($_POST['service'] ?? [] as $id => $row) {
                $rates->saveItem('service', (int) $id, $parse($row['commission'] ?? ''), $selectedEmployeeId);
            }
            foreach ($_POST['product'] ?? [] as $id => $row) {
                $rates->saveItem('product', (int) $id, $parse($row['commission'] ?? ''), $selectedEmployeeId);
            }
            $message = 'Đã lưu hoa hồng.';
        } elseif (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['change_employee'])) {
            $selectedEmployeeId = (int) ($_POST['ma_nhan_vien'] ?? 0);
        }

        $rateMap = table_exists('chi_tiet_hoa_hong')
            ? (new CommissionRate())->getAllMapped($selectedEmployeeId)
            : ['dich_vu' => [], 'san_pham' => []];

        $employees        = (new Employee())->getAll();
        $servicesByCategory = (new Service())->getGroupedByCategory();
        $products         = table_exists('san_pham') ? (new Product())->getAll() : [];

        $this->adminView('employees/commission', [
            'pageTitle'          => $pageTitle,
            'currentRoute'       => 'employees/commission',
            'selectedEmployeeId' => $selectedEmployeeId,
            'rateMap'            => $rateMap,
            'servicesByCategory' => $servicesByCategory,
            'products'           => $products,
            'employees'          => $employees,
            'message'            => $message,
        ], true);
    }

    /** AJAX: xóa nhân viên — route ajax/employees */
    public function ajax(): void
    {
        ob_start();
        $this->requireAuth();
        $this->requireWritePermission();

        if (($_POST['do'] ?? '') === 'Delete') {
            $id = (int) ($_POST['ma_nhan_vien'] ?? $_POST['employee_id'] ?? 0);
            if ($id > 0) {
                (new Employee())->delete($id);
            }
            $this->jsonResponse(['success' => true]);
        }

        $this->jsonResponse(['error' => 'Hành động không hợp lệ']);
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    private function handleManage(string $pageTitle): void
    {
        $model   = new Employee();
        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = max(5, min(100, (int) ($_GET['per_page'] ?? 20)));

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['delete_employee'])) {
            try {
                $empId = (int) ($_POST['ma_nhan_vien'] ?? 0);
                (new \App\Models\Admin())->unlinkEmployee($empId);
                $model->delete($empId);
            } catch (Exception $e) { /* bỏ qua */ }
        }

        $adminModel = new \App\Models\Admin();
        $totalItems = $model->count();
        $pag        = paginate($totalItems, $page, $perPage);
        $pag['total'] = $totalItems;
        $employees  = $model->getPaginated($pag['offset'], $perPage);

        foreach ($employees as &$emp) {
            $acc = $adminModel->getAccountByEmployeeId((int) $emp['ma_nhan_vien']);
            $emp['has_account']      = $acc !== null;
            $emp['account_username'] = $acc['ten_dang_nhap'] ?? null;
        }
        unset($emp);

        $baseUrl = admin_route('employees');

        $this->adminView('employees/manage', compact('pageTitle', 'employees', 'pag', 'baseUrl') + [
            'readOnly' => $this->isReadOnly(),
        ], true);
    }

    private function handleAdd(string $pageTitle): void
    {
        $errors = [];
        $old = $_POST;
        $successScript = null;

        if (isset($_POST['add_new_employee']) && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            $errors = $this->validate($_POST);
            if ($errors === []) {
                try {
                    (new Employee())->create([
                        'ten'           => test_input($_POST['ten'] ?? ''),
                        'ho_dem'        => test_input($_POST['ho_dem'] ?? ''),
                        'so_dien_thoai' => test_input($_POST['so_dien_thoai'] ?? ''),
                        'email'         => test_input($_POST['email'] ?? ''),
                        'chuc_vu'       => test_input($_POST['chuc_vu'] ?? ''),
                        'luong_co_ban'  => parse_vnd_input($_POST['luong_co_ban'] ?? 0),
                    ]);

                    // Lấy ID nhân viên vừa tạo
                    $newEmpId = (int) \App\Core\Database::getConnection()->lastInsertId();
                    $autoAccount = null;

                    // Tự động tạo tài khoản nếu có email và chức vụ
                    $email   = test_input($_POST['email'] ?? '');
                    $chucVu  = test_input($_POST['chuc_vu'] ?? '');
                    $adminModel = new \App\Models\Admin();

                    if ($email !== '' && $newEmpId > 0 && !$adminModel->findByEmail($email)) {
                        // Tạo tên đăng nhập từ email (phần trước @)
                        $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', explode('@', $email)[0]));
                        if (strlen($baseUsername) < 4) $baseUsername = 'user' . $baseUsername;
                        $username = $baseUsername;
                        $suffix   = 1;
                        while ($adminModel->findByUsername($username)) {
                            $username = $baseUsername . $suffix++;
                        }

                        $defaultPassword = '123456789';
                        $adminModel->createFull([
                            'ten_dang_nhap' => $username,
                            'email'         => $email,
                            'ho_ten'        => trim(test_input($_POST['ho_dem'] ?? '') . ' ' . test_input($_POST['ten'] ?? '')),
                            'mat_khau'      => sha1($defaultPassword),
                            'chuc_vu'       => $chucVu ?: 'Lễ tân',
                            'ma_nhan_vien'  => $newEmpId,
                        ]);
                        $autoAccount = ['username' => $username, 'password' => $defaultPassword];
                    }

                    $successScript = admin_route('employees');
                    // Truyền thông tin tài khoản vừa tạo qua session
                    if ($autoAccount) {
                        $_SESSION['new_account_info'] = $autoAccount;
                    }
                } catch (Exception $e) {
                    $errors['general'] = $e->getMessage();
                }
            }
        }

        $this->adminView('employees/add', compact('pageTitle', 'errors', 'old', 'successScript'), true);
    }

    private function handleEdit(string $pageTitle): void
    {
        $employeeId = (int) ($_GET['ma_nhan_vien'] ?? 0);
        $model = new Employee();
        $employee = $model->find($employeeId);

        if (!$employee) {
            $this->redirect(admin_route('employees'));
        }

        $errors = [];
        $successScript = null;

        if (isset($_POST['edit_employee_sbmt']) && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            $errors = $this->validate($_POST);
            if ($errors === []) {
                try {
                    $empId  = (int) $_POST['ma_nhan_vien'];
                    $chucVu = test_input($_POST['chuc_vu'] ?? '');
                    $model->update($empId, [
                        'ten'           => test_input($_POST['ten'] ?? ''),
                        'ho_dem'        => test_input($_POST['ho_dem'] ?? ''),
                        'so_dien_thoai' => test_input($_POST['so_dien_thoai'] ?? ''),
                        'email'         => test_input($_POST['email'] ?? ''),
                        'chuc_vu'       => $chucVu,
                        'luong_co_ban'  => parse_vnd_input($_POST['luong_co_ban'] ?? 0),
                    ]);
                    // Đồng bộ chức vụ sang tài khoản liên kết (nếu có)
                    if ($chucVu !== '') {
                        (new \App\Models\Admin())->syncRoleFromEmployee($empId, $chucVu);
                    }
                    $successScript = admin_route('employees');
                } catch (Exception $e) {
                    $errors['general'] = $e->getMessage();
                }
            }
            $employee = array_merge($employee, array_intersect_key($_POST, $employee));
        }

        $this->adminView('employees/edit', compact('pageTitle', 'employee', 'errors', 'successScript'), true);
    }

    private function validate(array $data): array
    {
        $errors = [];
        foreach (['ten' => 'Tên', 'ho_dem' => 'Họ đệm', 'so_dien_thoai' => 'Số điện thoại', 'email' => 'Email'] as $field => $label) {
            if (empty(test_input($data[$field] ?? ''))) {
                $errors[$field] = "$label là bắt buộc.";
            }
        }
        return $errors;
    }
}
