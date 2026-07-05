<div class="container-fluid">
    <div class="page-title-bar">
        <h1>Thêm nhân viên mới</h1>
        <a href="<?= admin_route('employees') ?>" class="btn btn-outline-secondary btn-sm" style="border-radius:8px">
            <i class="fas fa-arrow-left mr-1"></i> Danh sách
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm" style="border-radius:12px; overflow:hidden; border:none">
                <!-- Header card -->
                <div style="background:linear-gradient(135deg, #1e5bb8, #3a7bd5); padding:24px 28px">
                    <div class="d-flex align-items-center">
                        <div style="width:52px; height:52px; border-radius:50%; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; margin-right:16px; flex-shrink:0">
                            <i class="fas fa-user-plus fa-lg" style="color:#fff"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="color:#fff; font-weight:600">Thông tin nhân viên</h5>
                            <div style="color:rgba(255,255,255,0.75); font-size:0.85rem">Điền đầy đủ thông tin để tạo hồ sơ nhân viên</div>
                        </div>
                    </div>
                </div>

                <div class="card-body" style="padding:28px">
                    <?php if (!empty($errors['general'])): ?>
                        <div class="alert alert-danger" style="border-radius:8px; border-left:4px solid #e74a3b">
                            <i class="fas fa-exclamation-circle mr-1"></i><?= htmlspecialchars($errors['general']) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= admin_route('employees', ['do' => 'Add']) ?>">

                        <!-- Họ tên -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control mt-1 <?= !empty($errors['ten']) ? 'is-invalid' : '' ?>"
                                           name="ten" required value="<?= htmlspecialchars($old['ten'] ?? '') ?>"
                                           placeholder="Nhập tên"
                                           style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                                    <?php if (!empty($errors['ten'])): ?><div class="invalid-feedback"><?= $errors['ten'] ?></div><?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Họ đệm <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control mt-1 <?= !empty($errors['ho_dem']) ? 'is-invalid' : '' ?>"
                                           name="ho_dem" required value="<?= htmlspecialchars($old['ho_dem'] ?? '') ?>"
                                           placeholder="Nhập họ đệm"
                                           style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                                    <?php if (!empty($errors['ho_dem'])): ?><div class="invalid-feedback"><?= $errors['ho_dem'] ?></div><?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Chức vụ -->
                        <div class="form-group">
                            <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Chức vụ</label>
                            <select class="form-control mt-1" name="chuc_vu" style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                                <option value="">-- Chọn chức vụ --</option>
                                <?php foreach (['Quản lý', 'Lễ tân', 'Thợ chính', 'Thợ phụ'] as $cv): ?>
                                    <option value="<?= $cv ?>" <?= ($old['chuc_vu'] ?? '') === $cv ? 'selected' : '' ?>><?= $cv ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Liên hệ -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control mt-1 <?= !empty($errors['so_dien_thoai']) ? 'is-invalid' : '' ?>"
                                           name="so_dien_thoai" required pattern="\d{10}"
                                           title="Số điện thoại phải đủ 10 chữ số"
                                           value="<?= htmlspecialchars($old['so_dien_thoai'] ?? '') ?>"
                                           placeholder="0xxxxxxxxx"
                                           style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                                    <?php if (!empty($errors['so_dien_thoai'])): ?><div class="invalid-feedback"><?= $errors['so_dien_thoai'] ?></div><?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Email <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control mt-1 <?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
                                           name="email" required value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                                           placeholder="example@email.com"
                                           style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                                    <?php if (!empty($errors['email'])): ?><div class="invalid-feedback"><?= $errors['email'] ?></div><?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Lương cơ bản -->
                        <div class="form-group">
                            <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Lương cơ bản (VND)</label>
                            <input type="text" class="form-control mt-1"
                                   name="luong_co_ban"
                                   value="<?= htmlspecialchars($old['luong_co_ban'] ?? '') ?>"
                                   placeholder="VD: 5.000.000"
                                   style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                            <small class="text-muted">Nhập số không cần ký hiệu VND, ví dụ: 5000000 hoặc 5.000.000</small>
                        </div>

                        <!-- Nút -->
                        <div class="d-flex justify-content-end" style="gap:10px; padding-top:16px; border-top:1px solid #e2e8f0; margin-top:8px">
                            <a href="<?= admin_route('employees') ?>" class="btn btn-light px-4" style="border-radius:8px">Hủy</a>
                            <button type="submit" name="add_new_employee" class="btn btn-primary px-4" style="border-radius:8px">
                                <i class="fas fa-user-plus mr-1"></i> Thêm nhân viên
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($successScript): ?>
    <?php
    $accountInfo = $_SESSION['new_account_info'] ?? null;
    unset($_SESSION['new_account_info']);
    ?>
    <?php if ($accountInfo): ?>
    <script>
    swal({
        title: "Đã thêm nhân viên!",
        content: (function(){
            var d = document.createElement('div');
            d.innerHTML = '<p style="margin-bottom:8px">Tài khoản đăng nhập đã được tạo tự động:</p>'
                + '<div style="background:#f0f4ff;border-radius:8px;padding:12px;text-align:left;font-size:0.95rem">'
                + '<div><b>Tên đăng nhập:</b> <code><?= htmlspecialchars($accountInfo['username']) ?></code></div>'
                + '<div><b>Mật khẩu mặc định:</b> <code><?= htmlspecialchars($accountInfo['password']) ?></code></div>'
                + '</div>'
                + '<p style="margin-top:8px;color:#dc2626;font-size:0.85rem"><i class="fas fa-exclamation-triangle"></i> Yêu cầu đổi mật khẩu khi đăng nhập lần đầu!</p>';
            return d;
        })(),
        icon: "success"
    }).then(function(){ window.location.replace("<?= $successScript ?>"); });
    </script>
    <?php else: ?>
    <script>swal("Thành công","Đã thêm nhân viên mới", "success").then(() => { window.location.replace("<?= $successScript ?>"); });</script>
    <?php endif; ?>
<?php endif; ?>
