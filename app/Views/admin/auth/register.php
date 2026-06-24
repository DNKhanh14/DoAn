<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng ký tài khoản | Barber Salon</title>
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
      max-width: 480px;
    }
    .auth-logo {
      text-align: center;
      margin-bottom: 28px;
    }
    .auth-logo i { font-size: 2.2rem; color: #1e5bb8; }
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
    .auth-links { text-align: center; margin-top: 20px; font-size: 0.85rem; color: #6b7280; }
    .auth-links a { color: #1e5bb8; font-weight: 600; text-decoration: none; }
    .auth-links a:hover { text-decoration: underline; }
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
      <p>Tạo tài khoản quản trị mới</p>
    </div>

    <?php if ($success): ?>
      <div class="alert alert-success d-flex align-items-center" style="border-radius:8px;font-size:0.9rem">
        <i class="fas fa-check-circle mr-2"></i>
        <?= $success ?>
      </div>
      <div class="auth-links">
        <a href="index.php?route=login"><i class="fas fa-arrow-left mr-1"></i> Quay lại đăng nhập</a>
      </div>
    <?php else: ?>

    <?php if ($error): ?>
      <div class="alert alert-danger d-flex align-items-center" style="border-radius:8px;font-size:0.9rem">
        <i class="fas fa-exclamation-circle mr-2"></i> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="index.php?route=register" novalidate>

      <div class="form-group">
        <label>Họ và tên <span style="color:#dc2626">*</span></label>
        <input type="text" name="full_name" class="form-control"
               placeholder="Nguyễn Văn A"
               value="<?= htmlspecialchars($old['full_name'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label>Tên đăng nhập <span style="color:#dc2626">*</span></label>
        <input type="text" name="username" class="form-control"
               placeholder="admin123"
               value="<?= htmlspecialchars($old['username'] ?? '') ?>"
               autocomplete="off" required>
        <small class="text-muted">Chỉ dùng chữ cái, số và dấu gạch dưới. Tối thiểu 4 ký tự.</small>
      </div>

      <div class="form-group">
        <label>Email <span style="color:#dc2626">*</span></label>
        <input type="email" name="email" class="form-control"
               placeholder="example@email.com"
               value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
      </div>

      <div class="form-group">
        <label>Mật khẩu <span style="color:#dc2626">*</span></label>
        <div class="pass-toggle">
          <input type="password" name="password" id="pw1" class="form-control"
                 placeholder="Tối thiểu 6 ký tự" required>
          <i class="fas fa-eye toggle-eye" onclick="togglePw('pw1',this)"></i>
        </div>
      </div>

      <div class="form-group">
        <label>Nhập lại mật khẩu <span style="color:#dc2626">*</span></label>
        <div class="pass-toggle">
          <input type="password" name="password2" id="pw2" class="form-control"
                 placeholder="Nhập lại mật khẩu" required>
          <i class="fas fa-eye toggle-eye" onclick="togglePw('pw2',this)"></i>
        </div>
      </div>

      <button type="submit" name="register-button" class="btn-auth">
        <i class="fas fa-user-plus mr-2"></i> Tạo tài khoản
      </button>

    </form>

    <div class="auth-links">
      Đã có tài khoản?
      <a href="index.php?route=login">Đăng nhập ngay</a>
    </div>

    <?php endif; ?>
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
