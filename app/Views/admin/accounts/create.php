<div class="container-fluid">
    <div class="page-title-bar">
        <h1>Tạo tài khoản mới</h1>
        <a href="<?= admin_route('accounts') ?>" class="btn btn-outline-secondary btn-sm" style="border-radius:8px">
            <i class="fas fa-arrow-left mr-1"></i> Quay lại
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form method="POST">
                <!-- Section 1: Thông tin đăng nhập -->
                <div class="card shadow-sm mb-4" style="border-radius:12px; overflow:hidden; border:none">
                    <div class="card-header d-flex align-items-center py-3 px-4" style="background:#fff; border-bottom:1px solid #e2e8f0">
                        <div style="width:36px; height:36px; border-radius:8px; background:#1e5bb81a; color:#1e5bb8; display:flex; align-items:center; justify-content:center; margin-right:12px; flex-shrink:0">
                            <i class="fas fa-key"></i>
                        </div>
                        <div>
                            <div class="font-weight-bold" style="color:#2d3748">Thông tin đăng nhập</div>
                            <div class="text-muted" style="font-size:0.78rem">Tên đăng nhập và mật khẩu</div>
                        </div>
                    </div>
                    <div class="card-body" style="padding:24px">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Tên đăng nhập <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control mt-1 <?= !empty($errors['ten_dang_nhap']) ? 'is-invalid' : '' ?>"
                                           name="ten_dang_nhap" value="<?= htmlspecialchars($old['ten_dang_nhap'] ?? '') ?>"
                                           placeholder="Ít nhất 4 ký tự" autocomplete="off"
                                           style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                                    <?php if (!empty($errors['ten_dang_nhap'])): ?>
                                        <div class="invalid-feedback"><?= $errors['ten_dang_nhap'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Mật khẩu <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control mt-1 <?= !empty($errors['mat_khau']) ? 'is-invalid' : '' ?>"
                                           name="mat_khau" placeholder="Ít nhất 6 ký tự" autocomplete="new-password"
                                           style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                                    <?php if (!empty($errors['mat_khau'])): ?>
                                        <div class="invalid-feedback"><?= $errors['mat_khau'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Thông tin cá nhân -->
                <div class="card shadow-sm mb-4" style="border-radius:12px; overflow:hidden; border:none">
                    <div class="card-header d-flex align-items-center py-3 px-4" style="background:#fff; border-bottom:1px solid #e2e8f0">
                        <div style="width:36px; height:36px; border-radius:8px; background:#1cc88a1a; color:#1cc88a; display:flex; align-items:center; justify-content:center; margin-right:12px; flex-shrink:0">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <div class="font-weight-bold" style="color:#2d3748">Thông tin cá nhân</div>
                            <div class="text-muted" style="font-size:0.78rem">Họ tên và email</div>
                        </div>
                    </div>
                    <div class="card-body" style="padding:24px">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Họ tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control mt-1 <?= !empty($errors['ho_ten']) ? 'is-invalid' : '' ?>"
                                           name="ho_ten" value="<?= htmlspecialchars($old['ho_ten'] ?? '') ?>"
                                           style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                                    <?php if (!empty($errors['ho_ten'])): ?>
                                        <div class="invalid-feedback"><?= $errors['ho_ten'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control mt-1 <?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
                                           name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                                           style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                                    <?php if (!empty($errors['email'])): ?>
                                        <div class="invalid-feedback"><?= $errors['email'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Phân quyền & liên kết -->
                <div class="card shadow-sm mb-4" style="border-radius:12px; overflow:hidden; border:none">
                    <div class="card-header d-flex align-items-center py-3 px-4" style="background:#fff; border-bottom:1px solid #e2e8f0">
                        <div style="width:36px; height:36px; border-radius:8px; background:#6f42c11a; color:#6f42c1; display:flex; align-items:center; justify-content:center; margin-right:12px; flex-shrink:0">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <div class="font-weight-bold" style="color:#2d3748">Chức vụ & Liên kết</div>
                            <div class="text-muted" style="font-size:0.78rem">Phân quyền truy cập hệ thống</div>
                        </div>
                    </div>
                    <div class="card-body" style="padding:24px">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Chức vụ <span class="text-danger">*</span></label>
                                    <select class="form-control mt-1 <?= !empty($errors['chuc_vu']) ? 'is-invalid' : '' ?>" name="chuc_vu"
                                            style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                                        <option value="">-- Chọn chức vụ --</option>
                                        <?php foreach ($roles as $role): ?>
                                            <option value="<?= htmlspecialchars($role) ?>"
                                                <?= ($old['chuc_vu'] ?? '') === $role ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($role) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (!empty($errors['chuc_vu'])): ?>
                                        <div class="invalid-feedback"><?= $errors['chuc_vu'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Liên kết nhân viên</label>
                                    <select class="form-control mt-1" name="ma_nhan_vien"
                                            style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                                        <option value="">-- Không liên kết --</option>
                                        <?php foreach ($employees as $emp): ?>
                                            <option value="<?= (int)$emp['ma_nhan_vien'] ?>"
                                                <?= (int)($old['ma_nhan_vien'] ?? 0) === (int)$emp['ma_nhan_vien'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars(trim($emp['ho_dem'] . ' ' . $emp['ten'])) ?>
                                                (<?= htmlspecialchars($emp['chuc_vu'] ?? '—') ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">Liên kết để xem hồ sơ nhân viên khi đăng nhập</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Nút submit -->
                <div class="d-flex justify-content-end pb-4" style="gap:10px">
                    <a href="<?= admin_route('accounts') ?>" class="btn btn-light px-4" style="border-radius:8px">Hủy</a>
                    <button type="submit" name="create_account" class="btn btn-primary px-5" style="border-radius:8px">
                        <i class="fas fa-user-plus mr-2"></i>Tạo tài khoản
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
