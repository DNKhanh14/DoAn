<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * MailService — gửi email qua SMTP (Gmail)
 * Cấu hình trong app/config/mail.php
 */
class MailService
{
    private array $cfg;

    public function __construct()
    {
        $this->cfg = require APP_PATH . '/config/mail.php';
    }

    /**
     * Gửi email đặt lại mật khẩu
     *
     * @throws Exception nếu gửi thất bại
     */
    public function sendPasswordReset(string $toEmail, string $toName, string $resetLink): bool
    {
        $mail = $this->createMailer();

        $mail->addAddress($toEmail, $toName);
        $mail->Subject = 'Đặt lại mật khẩu — Barbershop';
        $mail->isHTML(true);
        $mail->Body    = $this->resetTemplate($toName, $resetLink);
        $mail->AltBody = "Xin chào $toName,\n\nLink đặt lại mật khẩu (hiệu lực 1 giờ):\n$resetLink\n\nNếu bạn không yêu cầu, hãy bỏ qua email này.\n\nBarber Salon";

        return $mail->send();
    }

    // ── Private helpers ───────────────────────────────────────────────────

    private function createMailer(): PHPMailer
    {
        $cfg  = $this->cfg;
        $mail = new PHPMailer(true); // true = throw exceptions

        $mail->isSMTP();
        $mail->Host        = $cfg['host'];
        $mail->SMTPAuth    = true;
        $mail->Username    = $cfg['username'];
        $mail->Password    = $cfg['password'];
        $mail->SMTPSecure  = $cfg['encryption'] === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port        = (int) $cfg['port'];
        $mail->CharSet     = 'UTF-8';
        $mail->SMTPDebug   = (int) ($cfg['debug'] ?? 0);

        $mail->setFrom($cfg['from_email'], $cfg['from_name']);
        $mail->addReplyTo($cfg['from_email'], $cfg['from_name']);

        return $mail;
    }

    private function resetTemplate(string $name, string $link): string
    {
        $year = date('Y');
        return <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body style="margin:0;padding:0;background:#f0f4ff;font-family:'Segoe UI',Arial,sans-serif">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4ff;padding:40px 0">
  <tr><td align="center">
    <table width="520" cellpadding="0" cellspacing="0"
           style="background:#fff;border-radius:14px;box-shadow:0 4px 24px rgba(30,91,184,.1);overflow:hidden;max-width:100%">

      <!-- Header -->
      <tr>
        <td style="background:#1e5bb8;padding:28px 40px;text-align:center">
          <span style="font-size:28px;color:#fff">✂</span>
          <h1 style="margin:8px 0 0;color:#fff;font-size:20px;font-weight:800;letter-spacing:1px">
            BARBERSHOP
          </h1>
        </td>
      </tr>

      <!-- Body -->
      <tr>
        <td style="padding:36px 40px">
          <p style="margin:0 0 14px;font-size:15px;color:#1e2d3d;font-weight:700">
            Xin chào, {$name}!
          </p>
          <p style="margin:0 0 20px;font-size:14px;color:#4b5563;line-height:1.7">
            Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản quản trị của bạn.
            Nhấn vào nút bên dưới để tiếp tục. Liên kết có hiệu lực trong <strong>1 giờ</strong>.
          </p>

          <table width="100%" cellpadding="0" cellspacing="0">
            <tr><td align="center" style="padding:10px 0 24px">
              <a href="{$link}" target="_blank"
                 style="display:inline-block;background:#1e5bb8;color:#fff;font-size:15px;
                        font-weight:700;padding:13px 36px;border-radius:8px;
                        text-decoration:none;letter-spacing:.4px">
                Đặt lại mật khẩu
              </a>
            </td></tr>
          </table>

          <p style="margin:0 0 10px;font-size:13px;color:#6b7280">
            Hoặc copy đường dẫn này vào trình duyệt:
          </p>
          <p style="margin:0;font-size:12px;word-break:break-all;background:#f3f4f6;
                    padding:10px 14px;border-radius:6px;color:#374151">
            {$link}
          </p>

          <hr style="border:none;border-top:1px solid #e5e7eb;margin:28px 0">
          <p style="margin:0;font-size:13px;color:#9ca3af;line-height:1.6">
            Nếu bạn <strong>không</strong> yêu cầu đặt lại mật khẩu, hãy bỏ qua email này.
            Tài khoản của bạn vẫn an toàn.
          </p>
        </td>
      </tr>

      <!-- Footer -->
      <tr>
        <td style="background:#f9fafb;padding:18px 40px;text-align:center;
                   font-size:12px;color:#9ca3af;border-top:1px solid #e5e7eb">
          © {$year} Barber Salon — Email tự động, vui lòng không trả lời.
        </td>
      </tr>

    </table>
  </td></tr>
</table>
</body>
</html>
HTML;
    }
}
