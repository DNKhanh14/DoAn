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
        $pageTitle = 'Nhân viên';
        $do = 'Manage';

        if (isset($_GET['do']) && in_array($_GET['do'], ['Add', 'Edit'], true)) {
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
        $model = new Employee();

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['delete_employee'])) {
            try {
                $model->delete((int) ($_POST['ma_nhan_vien'] ?? 0));
            } catch (Exception $e) { /* bỏ qua */ }
        }

        $this->adminView('employees/manage', [
            'pageTitle' => $pageTitle,
            'employees' => $model->getAll(),
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
                    ]);
                    $successScript = admin_route('employees');
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
                    $model->update((int) $_POST['ma_nhan_vien'], [
                        'ten'           => test_input($_POST['ten'] ?? ''),
                        'ho_dem'        => test_input($_POST['ho_dem'] ?? ''),
                        'so_dien_thoai' => test_input($_POST['so_dien_thoai'] ?? ''),
                        'email'         => test_input($_POST['email'] ?? ''),
                        'chuc_vu'       => test_input($_POST['chuc_vu'] ?? ''),
                    ]);
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
