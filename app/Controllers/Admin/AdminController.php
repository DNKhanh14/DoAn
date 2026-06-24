<?php

namespace App\Controllers\Admin;

use App\Core\Controller;

abstract class AdminController extends Controller
{
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

    protected function adminView(string $view, array $data = [], bool $withSweetAlert = false): void
    {
        $this->requireAuth();

        $data['con'] = $data['con'] ?? \App\Core\Database::getConnection();
        $data['currentRoute'] = $data['currentRoute'] ?? ($_GET['route'] ?? 'dashboard');

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

        extract($data, EXTR_SKIP);

        $viewFile = APP_PATH . '/Views/admin/' . $view . '.php';
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
        // Xóa mọi output PHP warning/notice đã in ra trước đó
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
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
