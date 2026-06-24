<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng nhập quản trị | Barber Salon</title>
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
      max-width: 420px;
    }
    .auth-logo { text-align: center; margin-bottom: 32px; }
    .auth-logo i  { font-size: 2.4rem; color: #1e5bb8; }
    .auth-logo h1 { font-size: 1.45rem; font-weight: 800; color: #1e2d3d; margin: 10px 0 4px; }
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
      padding: 12px;
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
    .auth-links { text-align: center; margin-top: 22px; font-size: 0.85rem; }
    .auth-links a { color: #1e5bb8; font-weight: 600; text-decoration: none; }
    .auth-links a:hover { text-decoration: underline; }
    .auth-divider { color: #d1d5db; margin: 0 8px; }
    .pass-toggle { position: relative; }
    .pass-toggle .toggle-eye {
      position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
      cursor: pointer; color: #9ca3af; font-size: 14px;
    }
    .forgot-link { float: right; font-size: 0.8rem; font-weight: 600; color: #1e5bb8; text-decoration: none; }
    .forgot-link:hover { text-decoration: underline; }
    @media (max-width: 480px) {
      .auth-card { padding: 32px 22px; }
    }
  </style>
</head>
<body>
<div class="auth-wrap">
  <div class="auth-card">

    <div class="auth-logo">
      <i class="fas fa-cut"></i>
      <h1>Barber Salon</h1>
      <p>Hệ thống quản trị tiệm tóc</p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger d-flex align-items-center" style="border-radius:8px;font-size:0.9rem">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="index.php?route=login" name="login-form" onsubmit="return validateLogInForm()">

      <div class="form-group">
        <label for="username">Tên đăng nhập</label>
        <input type="text" id="username" name="username" class="form-control"
               placeholder="Nhập tên đăng nhập"
               oninput="document.getElementById('required_username').style.display='none'"
               autocomplete="username">
        <span class="invalid-feedback" id="required_username" style="display:none">
          Vui lòng nhập tên đăng nhập!
        </span>
      </div>

      <div class="form-group">
        <label for="password">
          Mật khẩu
          <a href="index.php?route=forgot-password" class="forgot-link">Quên mật khẩu?</a>
        </label>
        <div class="pass-toggle">
          <input type="password" id="password" name="password" class="form-control"
                 placeholder="Nhập mật khẩu"
                 oninput="document.getElementById('required_password').style.display='none'"
                 autocomplete="current-password">
          <i class="fas fa-eye toggle-eye" onclick="togglePw()"></i>
        </div>
        <span class="invalid-feedback" id="required_password" style="display:none">
          Vui lòng nhập mật khẩu!
        </span>
      </div>

      <button type="submit" name="signin-button" class="btn-auth">
        <i class="fas fa-sign-in-alt mr-2"></i> Đăng nhập
      </button>

    </form>

    <div class="auth-links">
      Chưa có tài khoản?
      <a href="index.php?route=register">Đăng ký ngay</a>
    </div>

  </div>
</div>

<script src="Design/js/jquery.min.js"></script>
<script src="Design/js/bootstrap.bundle.min.js"></script>
<script src="Design/js/main.js"></script>
<script>
function togglePw() {
  var inp  = document.getElementById('password');
  var icon = document.querySelector('.toggle-eye');
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
