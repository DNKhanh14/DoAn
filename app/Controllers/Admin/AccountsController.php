<?php

namespace App\Controllers\Admin;

use App\Models\Admin;
use App\Models\Employee;
use App\Models\Permission;

class AccountsController extends AdminController
{
    // ── Danh sách tài khoản ───────────────────────────────────────────────
    public function index(): void
    {
        $this->requirePermission('accounts');

        $message = null;
        $error   = null;
        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = max(5, min(100, (int) ($_GET['per_page'] ?? 20)));

        // Xử lý xóa
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['delete_account'])) {
            $id = (int) ($_POST['ma_nguoi_dung'] ?? 0);
            if ($id === (int) ($_SESSION['admin_id_barbershop_Xw211qAAsq4'] ?? 0)) {
                $error = 'Không thể xóa tài khoản đang đăng nhập.';
            } elseif (!(new Admin())->deleteAccount($id)) {
                $error = 'Không thể xóa tài khoản super_admin duy nhất.';
            } else {
                $message = 'Đã xóa tài khoản.';
            }
        }

        $model      = new Admin();
        $totalItems = $model->countAll();
        $pag        = paginate($totalItems, $page, $perPage);
        $pag['total'] = $totalItems;
        $accounts   = $model->getAllPaginated($pag['offset'], $perPage);
        $employees  = (new Employee())->getAll();
        $roles      = (new Permission())->getAllRoles();
        $baseUrl    = admin_route('accounts');

        $this->adminView('accounts/index', compact('accounts', 'employees', 'roles', 'message', 'error', 'pag', 'baseUrl'), true);
    }

    // ── Thêm tài khoản ────────────────────────────────────────────────────
    public function create(): void
    {
        $this->requirePermission('accounts');

        $errors = [];
        $old    = [];
        $roles     = (new Permission())->getAllRoles();
        $employees = (new Employee())->getAll();

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['create_account'])) {
            $old = $_POST;
            $errors = $this->validateAccount($_POST);

            if (empty($errors)) {
                $model = new Admin();
                if ($model->findByUsername(test_input($_POST['ten_dang_nhap']))) {
                    $errors['ten_dang_nhap'] = 'Tên đăng nhập đã tồn tại.';
                } elseif ($model->findByEmail(test_input($_POST['email']))) {
                    $errors['email'] = 'Email đã được sử dụng.';
                } else {
                    $model->createFull([
                        'ten_dang_nhap' => test_input($_POST['ten_dang_nhap']),
                        'email'         => test_input($_POST['email']),
                        'ho_ten'        => test_input($_POST['ho_ten']),
                        'mat_khau'      => sha1($_POST['mat_khau']),
                        'chuc_vu'       => test_input($_POST['chuc_vu']),
                        'ma_nhan_vien'  => (int) ($_POST['ma_nhan_vien'] ?? 0) ?: null,
                    ]);
                    $_SESSION['account_success'] = 'Đã tạo tài khoản thành công.';
                    $this->redirect('barber-admin/index.php?route=accounts');
                }
            }
        }

        $this->adminView('accounts/create', compact('errors', 'old', 'roles', 'employees'), true);
    }

    // ── Sửa tài khoản ────────────────────────────────────────────────────
    public function edit(): void
    {
        $this->requirePermission('accounts');

        $id      = (int) ($_GET['ma_nguoi_dung'] ?? 0);
        $model   = new Admin();
        $account = $model->findById($id);

        if (!$account) {
            $this->redirect('barber-admin/index.php?route=accounts');
        }

        $errors    = [];
        $roles     = (new Permission())->getAllRoles();
        $employees = (new Employee())->getAll();

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['edit_account'])) {
            $errors = $this->validateAccountEdit($_POST, $id);

            if (empty($errors)) {
                $model->updateAccount($id, [
                    'ho_ten'        => test_input($_POST['ho_ten']),
                    'email'         => test_input($_POST['email']),
                    'chuc_vu'       => test_input($_POST['chuc_vu']),
                    'ma_nhan_vien'  => (int) ($_POST['ma_nhan_vien'] ?? 0) ?: null,
                ]);

                // Đổi mật khẩu nếu có nhập
                if (!empty($_POST['mat_khau'])) {
                    $model->updatePassword($id, sha1($_POST['mat_khau']));
                }

                // Cập nhật session nếu đang sửa chính mình
                if ($id === (int) ($_SESSION['admin_id_barbershop_Xw211qAAsq4'] ?? 0)) {
                    $newRole = test_input($_POST['chuc_vu']);
                    $_SESSION['admin_role_barbershop']        = $newRole;
                    $_SESSION['admin_permissions_barbershop'] = (new Permission())->getByRole($newRole);
                    $_SESSION['admin_name_barbershop']        = test_input($_POST['ho_ten']);
                }

                $_SESSION['account_success'] = 'Đã cập nhật tài khoản.';
                $this->redirect('barber-admin/index.php?route=accounts');
            }

            $account = array_merge($account, array_intersect_key($_POST, $account));
        }

        $this->adminView('accounts/edit', compact('account', 'errors', 'roles', 'employees'), true);
    }

    // ── Phân quyền theo chức vụ ───────────────────────────────────────────
    public function permissions(): void
    {
        $this->requirePermission('accounts');

        // Chỉ super_admin mới được chỉnh quyền
        if ($this->getRole() !== 'super_admin') {
            $_SESSION['permission_denied'] = 'Chỉ Super Admin mới được chỉnh sửa phân quyền.';
            $this->redirect('barber-admin/index.php?route=dashboard');
        }

        $permModel = new Permission();
        $message   = null;

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['save_permissions'])) {
            $permModel->saveAll($_POST['permissions'] ?? []);
            $message = 'Đã lưu phân quyền thành công.';

            // Refresh permissions trong session hiện tại
            $currentRole = $this->getRole();
            $_SESSION['admin_permissions_barbershop'] = $permModel->getByRole($currentRole);
        }

        // Xử lý thêm chức vụ mới
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['add_role'])) {
            $newRole = trim($_POST['new_role'] ?? '');
            if ($newRole !== '') {
                $permModel->addRole($newRole);
                $message = "Đã thêm chức vụ \"$newRole\".";
            }
        }

        // Xử lý xóa chức vụ
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['delete_role'])) {
            $delRole = trim($_POST['del_role'] ?? '');
            if ($permModel->deleteRole($delRole)) {
                $message = "Đã xóa chức vụ \"$delRole\".";
            } else {
                $message = 'Không thể xóa chức vụ super_admin.';
            }
        }

        $allPermissions = $permModel->getAll();
        $allRoles       = $permModel->getAllRoles();
        $modules        = Permission::MODULES;

        $this->adminView('accounts/permissions', compact('allPermissions', 'allRoles', 'modules', 'message'), true);
    }

    // ── AJAX: xóa tài khoản ───────────────────────────────────────────────
    public function ajax(): void
    {
        $this->requirePermission('accounts');

        if (($_POST['do'] ?? '') === 'Delete') {
            $id = (int) ($_POST['ma_nguoi_dung'] ?? 0);
            if ($id === (int) ($_SESSION['admin_id_barbershop_Xw211qAAsq4'] ?? 0)) {
                $this->jsonResponse(['error' => 'Không thể xóa tài khoản đang đăng nhập.']);
            }
            $ok = (new Admin())->deleteAccount($id);
            $this->jsonResponse($ok ? ['success' => true] : ['error' => 'Không thể xóa tài khoản super_admin duy nhất.']);
        }

        // Reset mật khẩu về mặc định
        if (($_POST['do'] ?? '') === 'ResetPassword') {
            $id = (int) ($_POST['ma_nguoi_dung'] ?? 0);
            if ($id > 0) {
                (new Admin())->updatePassword($id, sha1('123456789'));
                $this->jsonResponse(['success' => true, 'message' => 'Đã reset mật khẩu về: 123456789']);
            }
        }

        // Tạo tài khoản nhanh từ nhân viên (gọi từ trang manage nhân viên)
        if (($_POST['do'] ?? '') === 'QuickCreate') {
            $empId  = (int) ($_POST['ma_nhan_vien'] ?? 0);
            $email  = test_input($_POST['email'] ?? '');
            $name   = test_input($_POST['ten_nhan_vien'] ?? '');
            $chucVu = test_input($_POST['chuc_vu'] ?? 'Lễ tân');

            if ($empId <= 0 || $email === '') {
                $this->jsonResponse(['error' => 'Thiếu thông tin nhân viên.']);
            }

            $model = new Admin();

            // Kiểm tra đã có tài khoản liên kết chưa
            if ($model->getAccountByEmployeeId($empId)) {
                $this->jsonResponse(['error' => 'Nhân viên này đã có tài khoản đăng nhập.']);
            }

            // Tạo username từ email
            $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', explode('@', $email)[0]));
            if (strlen($baseUsername) < 4) $baseUsername = 'user' . $baseUsername;
            $username = $baseUsername;
            $suffix   = 1;
            while ($model->findByUsername($username)) {
                $username = $baseUsername . $suffix++;
            }

            // Dùng email làm email nếu chưa tồn tại, hoặc tạo email giả
            $useEmail = $model->findByEmail($email) ? $username . '@noreply.local' : $email;

            $model->createFull([
                'ten_dang_nhap' => $username,
                'email'         => $useEmail,
                'ho_ten'        => $name,
                'mat_khau'      => sha1('123456789'),
                'chuc_vu'       => $chucVu,
                'ma_nhan_vien'  => $empId,
            ]);

            $this->jsonResponse(['success' => true, 'username' => $username]);
        }

        $this->jsonResponse(['error' => 'Hành động không hợp lệ.']);
    }

    // ── Validate ──────────────────────────────────────────────────────────

    private function validateAccount(array $data): array
    {
        $errors = [];
        if (empty(trim($data['ten_dang_nhap'] ?? ''))) $errors['ten_dang_nhap'] = 'Vui lòng nhập tên đăng nhập.';
        elseif (strlen($data['ten_dang_nhap']) < 4)    $errors['ten_dang_nhap'] = 'Tên đăng nhập phải từ 4 ký tự.';
        if (empty(trim($data['ho_ten'] ?? '')))         $errors['ho_ten']        = 'Vui lòng nhập họ tên.';
        if (empty(trim($data['email'] ?? '')))          $errors['email']         = 'Vui lòng nhập email.';
        elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Email không hợp lệ.';
        if (empty($data['mat_khau']))                   $errors['mat_khau']      = 'Vui lòng nhập mật khẩu.';
        elseif (strlen($data['mat_khau']) < 6)          $errors['mat_khau']      = 'Mật khẩu phải từ 6 ký tự.';
        if (empty(trim($data['chuc_vu'] ?? '')))        $errors['chuc_vu']       = 'Vui lòng chọn chức vụ.';
        return $errors;
    }

    private function validateAccountEdit(array $data, int $currentId): array
    {
        $errors = [];
        if (empty(trim($data['ho_ten'] ?? '')))  $errors['ho_ten'] = 'Vui lòng nhập họ tên.';
        if (empty(trim($data['email'] ?? '')))   $errors['email']  = 'Vui lòng nhập email.';
        elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Email không hợp lệ.';
        if (empty(trim($data['chuc_vu'] ?? ''))) $errors['chuc_vu'] = 'Vui lòng chọn chức vụ.';

        // Kiểm tra email trùng (bỏ qua chính mình)
        if (empty($errors['email'])) {
            $existing = (new Admin())->findByEmail(test_input($data['email']));
            if ($existing && (int) $existing['ma_nguoi_dung'] !== $currentId) {
                $errors['email'] = 'Email đã được sử dụng bởi tài khoản khác.';
            }
        }

        // Mật khẩu chỉ validate nếu có nhập
        if (!empty($data['mat_khau']) && strlen($data['mat_khau']) < 6) {
            $errors['mat_khau'] = 'Mật khẩu phải từ 6 ký tự.';
        }
        return $errors;
    }
}
