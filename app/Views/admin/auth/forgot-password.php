<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quên mật khẩu | Barber Salon</title>
  <link href="Design/fonts/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,600,700,800" rel="stylesheet">
  <link href="Design/css/sb-admin-2.min.css" rel="stylesheet">
  <style>
    body { background: #f0f4ff; }
    .auth-wrap {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px 16px;
    }
    .auth-card {
      background: #fff;
      border-radius: 14px;
      box-shadow: 0 8px 40px rgba(30,91,184,.13);
      padding: 44px 48px;
      width: 100%;
      max-width: 460px;
    }
    .auth-logo { text-align: center; margin-bottom: 28px; }
    .auth-logo i  { font-size: 2.2rem; color: #1e5bb8; }
    .auth-logo h1 { font-size: 1.4rem; font-weight: 800; color: #1e2d3d; margin: 8px 0 2px; }
    .auth-logo p  { font-size: 0.85rem; color: #6b7280; margin: 0; }
    .auth-card label { font-size: 0.8rem; font-weight: 700; color: #374151; margin-bottom: 5px; }
    .auth-card .form-control {
      border-radius: 8px;
      border: 1.5px solid #d1d5db;
      padding: 10px 14px;
      font-size: 0.9rem;
      transition: border-color .15s, box-shadow .15s;
    }
    .auth-card .form-control:focus {
      border-color: #1e5bb8;
      box-shadow: 0 0 0 3px rgba(30,91,184,.12);
      outline: none;
    }
    .btn-auth {
      width: 100%;
      padding: 11px;
      background: #1e5bb8;
      color: #fff;
      font-weight: 700;
      font-size: 0.95rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background .2s;
      margin-top: 6px;
    }
    .btn-auth:hover { background: #164a9a; }
    .btn-auth.btn-success-color { background: #16a34a; }
    .btn-auth.btn-success-color:hover { background: #15803d; }
    .auth-links { text-align: center; margin-top: 20px; font-size: 0.85rem; color: #6b7280; }
    .auth-links a { color: #1e5bb8; font-weight: 600; text-decoration: none; }
    .auth-links a:hover { text-decoration: underline; }
    .step-indicator {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      margin-bottom: 24px;
      font-size: 0.8rem;
    }
    .step-dot {
      width: 28px; height: 28px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-weight: 700; font-size: 0.8rem;
    }
    .step-dot.active { background: #1e5bb8; color: #fff; }
    .step-dot.done   { background: #16a34a; color: #fff; }
    .step-dot.idle   { background: #e5e7eb; color: #9ca3af; }
    .step-line { width: 40px; height: 2px; background: #e5e7eb; border-radius: 2px; }
    .step-line.done { background: #16a34a; }
    .pass-toggle { position: relative; }
    .pass-toggle .toggle-eye {
      position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
      cursor: pointer; color: #9ca3af; font-size: 14px;
    }
    @media (max-width: 540px) {
      .auth-card { padding: 32px 24px; }
    }
  </style>
</head>
<body>
<div class="auth-wrap">
  <div class="auth-card">

    <div class="auth-logo">
      <i class="fas fa-cut"></i>
      <h1>Barber Salon</h1>
      <p>Đặt lại mật khẩu quản trị</p>
    </div>

    <!-- Thanh tiến trình 2 bước -->
    <div class="step-indicator">
      <div class="step-dot <?= ($step==='request'||$step==='done') ? ($step==='done'?'done':'active') : 'done' ?>">
        <?php if($step==='done'): ?><i class="fas fa-check" style="font-size:11px"></i><?php else: ?>1<?php endif; ?>
      </div>
      <div class="step-line <?= $step!=='request'?'done':'' ?>"></div>
      <div class="step-dot <?= $step==='reset'?'active':($step==='done'?'done':'idle') ?>">
        <?php if($step==='done'): ?><i class="fas fa-check" style="font-size:11px"></i><?php else: ?>2<?php endif; ?>
      </div>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger d-flex align-items-center" style="border-radius:8px;font-size:0.9rem">
        <i class="fas fa-exclamation-circle mr-2"></i> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="alert alert-success" style="border-radius:8px;font-size:0.9rem">
        <i class="fas fa-check-circle mr-2"></i> <?= $success ?>
      </div>
    <?php endif; ?>

    <?php if ($step === 'request' && !$success): ?>
    <!-- BƯỚC 1: Nhập email -->
    <p style="font-size:0.88rem;color:#6b7280;margin-bottom:20px;text-align:center">
      Nhập email tài khoản quản trị. Hệ thống sẽ tạo liên kết đặt lại mật khẩu cho bạn.
    </p>
    <form method="POST" action="index.php?route=forgot-password">
      <div class="form-group">
        <label>Email tài khoản <span style="color:#dc2626">*</span></label>
        <input type="email" name="email" class="form-control"
               placeholder="admin@email.com" required autofocus>
      </div>
      <button type="submit" name="request-button" class="btn-auth">
        <i class="fas fa-paper-plane mr-2"></i> Gửi yêu cầu đặt lại
      </button>
    </form>

    <?php elseif ($step === 'reset' && $admin && !$success): ?>
    <!-- BƯỚC 2: Đặt mật khẩu mới -->
    <p style="font-size:0.88rem;color:#6b7280;margin-bottom:20px;text-align:center">
      Xin chào <strong><?= htmlspecialchars($admin['ten_dang_nhap']) ?></strong>,
      nhập mật khẩu mới cho tài khoản của bạn.
    </p>
    <form method="POST" action="index.php?route=forgot-password?step=reset">
      <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
      <div class="form-group">
        <label>Mật khẩu mới <span style="color:#dc2626">*</span></label>
        <div class="pass-toggle">
          <input type="password" name="new_password" id="pw1" class="form-control"
                 placeholder="Tối thiểu 6 ký tự" required>
          <i class="fas fa-eye toggle-eye" onclick="togglePw('pw1',this)"></i>
        </div>
      </div>
      <div class="form-group">
        <label>Nhập lại mật khẩu mới <span style="color:#dc2626">*</span></label>
        <div class="pass-toggle">
          <input type="password" name="new_password2" id="pw2" class="form-control"
                 placeholder="Nhập lại mật khẩu" required>
          <i class="fas fa-eye toggle-eye" onclick="togglePw('pw2',this)"></i>
        </div>
      </div>
      <button type="submit" name="reset-button" class="btn-auth btn-success-color">
        <i class="fas fa-lock mr-2"></i> Đặt lại mật khẩu
      </button>
    </form>

    <?php elseif ($step === 'done'): ?>
    <!-- HOÀN TẤT -->
    <div class="text-center py-3">
      <i class="fas fa-check-circle fa-3x" style="color:#16a34a"></i>
      <p class="mt-3" style="font-size:0.95rem;color:#374151">
        Mật khẩu đã được đặt lại thành công!
      </p>
    </div>
    <?php endif; ?>

    <div class="auth-links">
      <a href="index.php?route=login"><i class="fas fa-arrow-left mr-1"></i> Quay lại đăng nhập</a>
    </div>

  </div>
</div>

<script>
function togglePw(id, icon) {
  var inp = document.getElementById(id);
  if (inp.type === 'password') {
    inp.type = 'text';
    icon.classList.replace('fa-eye', 'fa-eye-slash');
  } else {
    inp.type = 'password';
    icon.classList.replace('fa-eye-slash', 'fa-eye');
  }
}
</script>
</body>
</html>
