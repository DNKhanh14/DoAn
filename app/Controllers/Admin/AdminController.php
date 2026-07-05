<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Permission;

abstract class AdminController extends Controller
{
    // ── Auth helpers ──────────────────────────────────────────────────────

    protected function requireGuest(): void
    {
        if ($this->isLoggedIn()) {
            $this->redirect('barber-admin/index.php?route=dashboard');
        }
    }

    protected function requireAuth(): void
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('barber-admin/index.php?route=login');
        }
    }

    protected function isLoggedIn(): bool
    {
        return isset($_SESSION['username_barbershop_Xw211qAAsq4'], $_SESSION['password_barbershop_Xw211qAAsq4']);
    }

    // ── Phân quyền ───────────────────────────────────────────────────────

    /**
     * Lấy chức vụ hiện tại từ session
     */
    protected function getRole(): string
    {
        return $_SESSION['admin_role_barbershop'] ?? 'super_admin';
    }

    /**
     * Kiểm tra quyền truy cập module
     */
    protected function hasPermission(string $module): bool
    {
        $role = $this->getRole();

        // super_admin luôn có toàn quyền
        if ($role === 'super_admin') return true;

        // Lấy từ cache session
        $permissions = $_SESSION['admin_permissions_barbershop'] ?? [];
        return (bool) ($permissions[$module] ?? false);
    }

    /**
     * Yêu cầu quyền — nếu không có thì redirect về dashboard với thông báo
     */
    protected function requirePermission(string $module): void
    {
        $this->requireAuth();

        if (!$this->hasPermission($module)) {
            $_SESSION['permission_denied'] = 'Bạn không có quyền truy cập chức năng này.';
            $this->redirect('barber-admin/index.php?route=dashboard');
        }
    }

    /**
     * Các chức vụ chỉ được xem (không thêm/sửa/xóa) ở các module nhạy cảm
     */
    protected function isReadOnly(): bool
    {
        return in_array($this->getRole(), ['Lễ tân', 'Thợ chính', 'Thợ phụ'], true);
    }

    /**
     * Chặn thao tác ghi nếu là read-only role — trả về 403 JSON hoặc redirect
     */
    protected function requireWritePermission(): void
    {
        if ($this->isReadOnly()) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || !empty($_POST['action'])) {
                http_response_code(403);
                $this->jsonResponse(['error' => 'Bạn không có quyền thực hiện thao tác này.']);
            }
            $_SESSION['permission_denied'] = 'Bạn không có quyền thực hiện thao tác này.';
            $this->redirect('barber-admin/index.php?route=dashboard');
        }
    }

    // ── View ─────────────────────────────────────────────────────────────

    protected function adminView(string $view, array $data = [], bool $withSweetAlert = false): void
    {
        $this->requireAuth();

        $data['con']          = $data['con'] ?? \App\Core\Database::getConnection();
        $data['currentRoute'] = $data['currentRoute'] ?? ($_GET['route'] ?? 'dashboard');

        $role = $this->getRole();

        // Nếu session chưa có permissions (lần đầu hoặc mới migrate DB)
        // thì tự động load lại từ DB và lưu vào session
        $permissions = $_SESSION['admin_permissions_barbershop'] ?? null;
        if ($permissions === null) {
            $permissions = (new Permission())->getByRole($role);
            $_SESSION['admin_permissions_barbershop'] = $permissions;
        }

        // Truyền biến vào view
        $data['adminRole']        = $role;
        $data['adminPermissions'] = $permissions;
        $data['adminName']        = $_SESSION['admin_name_barbershop']
                                    ?? $_SESSION['username_barbershop_Xw211qAAsq4']
                                    ?? 'Admin';

        // Badge số lịch hẹn hôm nay cho nav sidebar
        if (!isset($data['todayBookingCount'])) {
            try {
                $data['todayBookingCount'] = table_exists('lich_hen')
                    ? (new \App\Models\Appointment())->countToday()
                    : 0;
            } catch (\Throwable $e) {
                $data['todayBookingCount'] = 0;
            }
        }

        $__view = $view; // Lưu tên view trước khi extract() có thể ghi đè biến $view
        extract($data, EXTR_OVERWRITE);

        // Đảm bảo GLOBALS luôn được cập nhật đúng cho hàm admin_can() trong header
        $GLOBALS['_adminRole']        = $adminRole;
        $GLOBALS['_adminPermissions'] = $adminPermissions;

        $viewFile   = APP_PATH  . '/Views/admin/' . $__view . '.php';
        $headerFile = ADMIN_PATH . '/Includes/templates/header.php';
        $footerFile = ADMIN_PATH . '/Includes/templates/footer.php';

        if (!is_file($viewFile)) {
            http_response_code(500);
            echo 'Admin view not found: ' . htmlspecialchars($view);
            return;
        }

        if (!is_file($headerFile) || !is_file($footerFile)) {
            require $viewFile;
            return;
        }

        require $headerFile;

        if ($withSweetAlert) {
            echo "<script src='https://unpkg.com/sweetalert/dist/sweetalert.min.js'></script>";
        }

        require $viewFile;
        require $footerFile;
    }

    protected function jsonResponse(array $data): void
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function redirect(string $url): void
    {
        if ($url === '' || $url[0] === '/' || str_starts_with($url, 'http')) {
            header('Location: ' . $url);
        } else {
            header('Location: ' . base_url($url));
        }
        exit;
    }
}
