<?php

namespace App\Controllers\Admin;

use App\Models\Admin;

class AuthController extends AdminController
{
    // ── Đăng nhập ─────────────────────────────────────────────────────────
    public function login(): void
    {
        $this->requireGuest();
        $error = null;

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['signin-button'])) {
            $username   = test_input($_POST['username'] ?? '');
            $password   = test_input($_POST['password'] ?? '');
            $hashedPass = sha1($password);

            $admin = (new Admin())->authenticate($username, $hashedPass);

            if ($admin) {
                $_SESSION['username_barbershop_Xw211qAAsq4'] = $username;
                $_SESSION['password_barbershop_Xw211qAAsq4'] = $password;
                $_SESSION['admin_id_barbershop_Xw211qAAsq4'] = $admin['ma_quan_tri'];

                $this->redirect('barber-admin/index.php?route=dashboard');
            }

            $error = 'Tên đăng nhập hoặc mật khẩu không đúng!';
        }

        require APP_PATH . '/Views/admin/auth/login.php';
    }

    // ── Đăng xuất ─────────────────────────────────────────────────────────
    public function logout(): void
    {
        session_unset();
        session_destroy();
        $this->redirect('barber-admin/index.php?route=login');
    }

    // ── Đăng ký ───────────────────────────────────────────────────────────
    public function register(): void
    {
        $this->requireGuest();
        $error   = null;
        $success = null;
        $old     = [];

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['register-button'])) {
            $username  = test_input($_POST['username']  ?? '');
            $email     = test_input($_POST['email']     ?? '');
            $fullName  = test_input($_POST['full_name'] ?? '');
            $password  = $_POST['password']  ?? '';
            $password2 = $_POST['password2'] ?? '';
            $old       = ['username' => $username, 'email' => $email, 'full_name' => $fullName];

            // Validate
            $error = $this->validateRegister($username, $email, $fullName, $password, $password2);

            if (!$error) {
                $model = new Admin();

                if ($model->findByUsername($username)) {
                    $error = 'Tên đăng nhập đã tồn tại.';
                } elseif ($model->findByEmail($email)) {
                    $error = 'Email này đã được sử dụng.';
                } else {
                    $model->create($username, $email, $fullName, sha1($password));
                    $success = 'Tạo tài khoản thành công! Bạn có thể đăng nhập ngay.';
                    $old = [];
                }
            }
        }

        require APP_PATH . '/Views/admin/auth/register.php';
    }

    // ── Quên mật khẩu ─────────────────────────────────────────────────────
    public function forgotPassword(): void
    {
        $this->requireGuest();
        $step    = $_GET['step'] ?? 'request';   // request | reset
        $token   = $_GET['token'] ?? '';
        $error   = null;
        $success = null;
        $admin   = null;

        // Bước 2: kiểm tra token hợp lệ
        if ($step === 'reset' && $token !== '') {
            $model = new Admin();
            $admin = $model->findByResetToken($token);
            if (!$admin) {
                $error = 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.';
                $step  = 'request';
            }
        }

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            $model = new Admin();

            // Bước 1: yêu cầu reset
            if (isset($_POST['request-button'])) {
                $email = test_input($_POST['email'] ?? '');
                if ($email === '') {
                    $error = 'Vui lòng nhập email.';
                } else {
                    $found = $model->findByEmail($email);
                    if ($found) {
                        $newToken  = bin2hex(random_bytes(24));
                        $expiry    = date('Y-m-d H:i:s', strtotime('+1 hour'));
                        $model->saveResetToken((int) $found['ma_quan_tri'], $newToken, $expiry);

                        $resetLink = (isset($_SERVER['HTTPS']) ? 'https' : 'http')
                            . '://' . $_SERVER['HTTP_HOST']
                            . '/barbershop-website-php-mysql-main/barbershop-website-php-mysql-main'
                            . '/barber-admin/index.php?route=forgot-password&step=reset&token=' . $newToken;

                        // Gửi email thật qua SMTP
                        try {
                            $mailer = new \MailService();
                            $mailer->sendPasswordReset(
                                $found['email'],
                                $found['ho_ten'] ?: $found['ten_dang_nhap'],
                                $resetLink
                            );
                            $success = 'Email đặt lại mật khẩu đã được gửi đến <strong>'
                                . htmlspecialchars($email)
                                . '</strong>. Vui lòng kiểm tra hộp thư (kể cả thư mục Spam).';
                        } catch (\Exception $e) {
                            // Ghi lỗi vào log nhưng không lộ chi tiết ra ngoài
                            error_log('[MailService] ' . $e->getMessage());
                            $error = 'Không thể gửi email. Vui lòng kiểm tra cấu hình SMTP trong app/config/mail.php.';
                        }
                    } else {
                        // Không tiết lộ email có tồn tại không (bảo mật)
                        $success = 'Nếu email tồn tại trong hệ thống, liên kết đặt lại đã được gửi.';
                    }
                }
            }

            // Bước 2: đặt lại mật khẩu
            if (isset($_POST['reset-button'])) {
                $token     = test_input($_POST['token']     ?? '');
                $password  = $_POST['new_password']  ?? '';
                $password2 = $_POST['new_password2'] ?? '';
                $admin     = $model->findByResetToken($token);

                if (!$admin) {
                    $error = 'Liên kết không hợp lệ hoặc đã hết hạn.';
                } elseif (strlen($password) < 6) {
                    $error = 'Mật khẩu phải có ít nhất 6 ký tự.';
                } elseif ($password !== $password2) {
                    $error = 'Mật khẩu nhập lại không khớp.';
                } else {
                    $model->resetPassword($admin['email'], sha1($password));
                    $model->clearResetToken((int) $admin['ma_quan_tri']);
                    $success = 'Đặt lại mật khẩu thành công! <a href="index.php?route=login">Đăng nhập ngay</a>';
                    $step    = 'done';
                }
            }
        }

        require APP_PATH . '/Views/admin/auth/forgot-password.php';
    }

    // ── Helper validate đăng ký ───────────────────────────────────────────
    private function validateRegister(
        string $username, string $email, string $fullName,
        string $password, string $password2
    ): ?string {
        if ($username === '')   return 'Vui lòng nhập tên đăng nhập.';
        if (strlen($username) < 4) return 'Tên đăng nhập phải từ 4 ký tự trở lên.';
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) return 'Tên đăng nhập chỉ chứa chữ cái, số và dấu gạch dưới.';
        if ($fullName === '')   return 'Vui lòng nhập họ tên.';
        if ($email === '')      return 'Vui lòng nhập email.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return 'Email không hợp lệ.';
        if (strlen($password) < 6) return 'Mật khẩu phải có ít nhất 6 ký tự.';
        if ($password !== $password2) return 'Mật khẩu nhập lại không khớp.';
        return null;
    }
}
