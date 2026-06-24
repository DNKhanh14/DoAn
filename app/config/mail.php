<?php

/**
 * Cấu hình gửi email qua SMTP
 *
 * Hướng dẫn Gmail:
 * 1. Bật 2FA tại myaccount.google.com/security
 * 2. Tạo "App Password": myaccount.google.com/apppasswords
 *    → Chọn App: Mail, Device: Other → lấy mật khẩu 16 ký tự
 * 3. Điền vào MAIL_PASSWORD bên dưới (không dùng mật khẩu Gmail thật)
 */

return [
    'host'       => 'smtp.gmail.com',   // Gmail SMTP
    'port'       => 587,                 // TLS
    'encryption' => 'tls',              // 'tls' hoặc 'ssl'
    'username'   => 'duongngockhanh56@gmail.com',   // ← Thay bằng Gmail của bạn
    'password'   => 'uvdk aocg upvj mnbs',       // ← App Password 16 ký tự
    'from_email' => 'duongngockhanh56@gmail.com',    // ← Địa chỉ gửi
    'from_name'  => 'Barber Salon',
    'debug'      => 0,  // 0=tắt log, 2=bật log đầy đủ (dùng khi test)
];
