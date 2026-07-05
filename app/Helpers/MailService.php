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

    /**
     * Gửi email xác nhận đặt lịch thành công cho khách hàng
     *
     * @param string $toEmail    Email khách
     * @param string $toName     Tên khách
     * @param array  $booking    Thông tin lịch hẹn: start_time, services[], barber_name
     * @throws Exception nếu gửi thất bại
     */
    public function sendBookingConfirmation(string $toEmail, string $toName, array $booking): bool
    {
        if (empty($toEmail) || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $mail = $this->createMailer();

        $mail->addAddress($toEmail, $toName);
        $mail->Subject = 'Xác nhận đặt lịch — Barber Salon';
        $mail->isHTML(true);
        $mail->Body    = $this->bookingConfirmTemplate($toName, $booking);
        $mail->AltBody = $this->bookingConfirmText($toName, $booking);

        return $mail->send();
    }

    /**
     * Gửi email thông báo hủy lịch hẹn cho khách hàng
     *
     * @param string $toEmail  Email khách
     * @param string $toName   Tên khách
     * @param array  $booking  Thông tin lịch: start_time, services[], barber_name, cancel_reason
     * @throws Exception nếu gửi thất bại
     */
    public function sendBookingCancellation(string $toEmail, string $toName, array $booking): bool
    {
        if (empty($toEmail) || !filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $mail = $this->createMailer();

        $mail->addAddress($toEmail, $toName);
        $mail->Subject = 'Thông báo hủy lịch hẹn — Barber Salon';
        $mail->isHTML(true);
        $mail->Body    = $this->bookingCancelTemplate($toName, $booking);
        $mail->AltBody = "Xin chào $toName,\n\nLịch hẹn của bạn đã bị hủy.\nLý do: " . ($booking['cancel_reason'] ?? 'Không có') . "\n\nBarber Salon";

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

    private function bookingConfirmTemplate(string $name, array $b): string
    {
        $year        = date('Y');
        $startTime   = isset($b['start_time']) ? date('H:i - d/m/Y', strtotime($b['start_time'])) : '';
        $barber      = htmlspecialchars($b['barber_name'] ?? 'Chưa xác định');
        $serviceRows = '';
        foreach ($b['services'] ?? [] as $svc) {
            $svcName  = htmlspecialchars($svc['name'] ?? $svc['ten_dich_vu'] ?? '');
            $svcPrice = !empty($svc['price'] ?? $svc['gia'])
                ? number_format((float) ($svc['price'] ?? $svc['gia']), 0, ',', '.') . ' VND'
                : '';
            $serviceRows .= "<tr>
                <td style='padding:8px 12px;border-bottom:1px solid #f0f4ff;font-size:14px;color:#374151'>$svcName</td>
                <td style='padding:8px 12px;border-bottom:1px solid #f0f4ff;font-size:14px;color:#1e5bb8;text-align:right;font-weight:600'>$svcPrice</td>
            </tr>";
        }
        $nameHtml = htmlspecialchars($name);

        return <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f0f4ff;font-family:'Segoe UI',Arial,sans-serif">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4ff;padding:40px 0">
  <tr><td align="center">
    <table width="540" cellpadding="0" cellspacing="0"
           style="background:#fff;border-radius:14px;box-shadow:0 4px 24px rgba(30,91,184,.1);overflow:hidden;max-width:100%">

      <!-- Header -->
      <tr>
        <td style="background:#1e5bb8;padding:28px 40px;text-align:center">
          <span style="font-size:32px;color:#fff">✂</span>
          <h1 style="margin:8px 0 0;color:#fff;font-size:20px;font-weight:800;letter-spacing:1px">BARBER SALON</h1>
          <p style="margin:6px 0 0;color:#93c5fd;font-size:13px">Xác nhận đặt lịch thành công</p>
        </td>
      </tr>

      <!-- Body -->
      <tr>
        <td style="padding:32px 40px">
          <p style="margin:0 0 8px;font-size:15px;color:#1e2d3d;font-weight:700">Xin chào, {$nameHtml}!</p>
          <p style="margin:0 0 24px;font-size:14px;color:#4b5563;line-height:1.7">
            Lịch hẹn của bạn đã được <strong style="color:#1cc88a">đặt thành công</strong>.
            Chúng tôi mong gặp bạn đúng giờ. Nếu có thay đổi vui lòng liên hệ tiệm để sắp xếp lại.
          </p>

          <!-- Thông tin lịch hẹn -->
          <table width="100%" cellpadding="0" cellspacing="0"
                 style="background:#f0f4ff;border-radius:10px;margin-bottom:24px">
            <tr>
              <td style="padding:16px 20px">
                <table width="100%" cellpadding="0" cellspacing="0">
                  <tr>
                    <td style="font-size:13px;color:#6b7280;padding-bottom:8px">🕐 Thời gian</td>
                    <td style="font-size:14px;font-weight:700;color:#1e2d3d;text-align:right;padding-bottom:8px">{$startTime}</td>
                  </tr>
                  <tr>
                    <td style="font-size:13px;color:#6b7280">💈 Thợ phục vụ</td>
                    <td style="font-size:14px;font-weight:700;color:#1e5bb8;text-align:right">{$barber}</td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>

          <!-- Dịch vụ -->
          <p style="margin:0 0 10px;font-size:14px;font-weight:700;color:#1e2d3d">Dịch vụ đã chọn:</p>
          <table width="100%" cellpadding="0" cellspacing="0"
                 style="border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;margin-bottom:24px">
            {$serviceRows}
          </table>

          <hr style="border:none;border-top:1px solid #e5e7eb;margin:0 0 20px">
          <p style="margin:0;font-size:13px;color:#9ca3af;line-height:1.6">
            Cảm ơn bạn đã tin tưởng <strong>Barber Salon</strong>.<br>
            Nếu cần hỗ trợ, vui lòng liên hệ trực tiếp hoặc gọi điện cho tiệm.
          </p>
        </td>
      </tr>

      <!-- Footer -->
      <tr>
        <td style="background:#f9fafb;padding:16px 40px;text-align:center;
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

    private function bookingConfirmText(string $name, array $b): string
    {
        $startTime = isset($b['start_time']) ? date('H:i d/m/Y', strtotime($b['start_time'])) : '';
        $barber    = $b['barber_name'] ?? 'Chưa xác định';
        $services  = implode(', ', array_map(
            fn ($s) => $s['name'] ?? $s['ten_dich_vu'] ?? '',
            $b['services'] ?? []
        ));
        return "Xin chào $name,\n\nLịch hẹn của bạn đã đặt thành công!\n\nThời gian: $startTime\nThợ phục vụ: $barber\nDịch vụ: $services\n\nCảm ơn bạn đã tin tưởng Barber Salon.";
    }

    private function bookingCancelTemplate(string $name, array $b): string
    {
        $year      = date('Y');
        $startTime = isset($b['start_time']) ? date('H:i - d/m/Y', strtotime($b['start_time'])) : '';
        $reason    = htmlspecialchars($b['cancel_reason'] ?? 'Không có lý do cụ thể');
        $nameHtml  = htmlspecialchars($name);

        return <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f0f4ff;font-family:'Segoe UI',Arial,sans-serif">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4ff;padding:40px 0">
  <tr><td align="center">
    <table width="520" cellpadding="0" cellspacing="0"
           style="background:#fff;border-radius:14px;box-shadow:0 4px 24px rgba(30,91,184,.1);overflow:hidden;max-width:100%">
      <tr>
        <td style="background:#dc2626;padding:28px 40px;text-align:center">
          <span style="font-size:32px;color:#fff">✂</span>
          <h1 style="margin:8px 0 0;color:#fff;font-size:20px;font-weight:800">BARBER SALON</h1>
          <p style="margin:6px 0 0;color:#fca5a5;font-size:13px">Thông báo hủy lịch hẹn</p>
        </td>
      </tr>
      <tr>
        <td style="padding:32px 40px">
          <p style="margin:0 0 8px;font-size:15px;color:#1e2d3d;font-weight:700">Xin chào, {$nameHtml}!</p>
          <p style="margin:0 0 20px;font-size:14px;color:#4b5563;line-height:1.7">
            Lịch hẹn của bạn vào lúc <strong>{$startTime}</strong> đã được <strong style="color:#dc2626">hủy</strong>.
          </p>
          <table width="100%" cellpadding="0" cellspacing="0" style="background:#fff5f5;border-radius:10px;margin-bottom:24px">
            <tr><td style="padding:16px 20px;font-size:14px;color:#374151">
              <strong>Lý do hủy:</strong> {$reason}
            </td></tr>
          </table>
          <p style="margin:0;font-size:13px;color:#9ca3af;line-height:1.6">
            Nếu bạn muốn đặt lịch lại, vui lòng liên hệ hoặc truy cập website của chúng tôi.<br>
            Xin lỗi vì sự bất tiện này.
          </p>
        </td>
      </tr>
      <tr>
        <td style="background:#f9fafb;padding:16px 40px;text-align:center;font-size:12px;color:#9ca3af;border-top:1px solid #e5e7eb">
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
