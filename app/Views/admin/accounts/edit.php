<div class="container-fluid">
    <div class="page-title-bar">
        <h1>Sửa tài khoản</h1>
        <a href="<?= admin_route('accounts') ?>" class="btn btn-outline-secondary btn-sm" style="border-radius:8px">
            <i class="fas fa-arrow-left mr-1"></i> Quay lại
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Badge tài khoản -->
            <div class="card shadow-sm mb-4" style="border-left:4px solid #1e5bb8; border-radius:12px; overflow:hidden; border-right:none; border-top:none; border-bottom:none">
                <div class="card-body d-flex align-items-center py-3" style="gap:16px">
                    <?php
                    $roleBadge = [
                        'super_admin' => ['#e74a3b','fa-crown'],
                        'Quản lý'     => ['#1e5bb8','fa-user-tie'],
                        'Lễ tân'      => ['#20c9a6','fa-user-check'],
                        'Thợ chính'   => ['#1cc88a','fa-cut'],
                        'Thợ phụ'     => ['#6c757d','fa-user'],
                    ][$account['chuc_vu']] ?? ['#6c757d','fa-user'];
                    ?>
                    <div style="width:52px; height:52px; border-radius:50%; background:<?= $roleBadge[0] ?>1a; color:<?= $roleBadge[0] ?>; display:flex; align-items:center; justify-content:center; flex-shrink:0">
                        <i class="fas <?= $roleBadge[1] ?> fa-lg"></i>
                    </div>
                    <div>
                        <div class="font-weight-bold" style="font-size:1.1rem; color:#2d3748"><?= htmlspecialchars($account['ten_dang_nhap']) ?></div>
                        <span class="badge text-white" style="background:<?= $roleBadge[0] ?>; font-size:0.75rem; padding:4px 10px; border-radius:20px">
                            <?= htmlspecialchars($account['chuc_vu']) ?>
                        </span>
                    </div>
                </div>
            </div>

            <form method="POST">
                <input type="hidden" name="ma_nguoi_dung" value="<?= (int)$account['ma_nguoi_dung'] ?>">

                <!-- Section 1: Tên đăng nhập (readonly) -->
                <div class="card shadow-sm mb-4" style="border-radius:12px; overflow:hidden; border:none">
                    <div class="card-header d-flex align-items-center py-3 px-4" style="background:#f8fafc; border-bottom:1px solid #e2e8f0">
                        <div style="width:32px; height:32px; border-radius:8px; background:#6c757d1a; color:#6c757d; display:flex; align-items:center; justify-content:center; margin-right:10px; flex-shrink:0">
                            <i class="fas fa-at" style="font-size:13px"></i>
                        </div>
                        <span class="font-weight-bold" style="color:#6c757d">Tên đăng nhập (không thể thay đổi)</span>
                    </div>
                    <div class="card-body" style="padding:20px 24px">
                        <input type="text" class="form-control" value="<?= htmlspecialchars($account['ten_dang_nhap']) ?>" disabled
                               style="border-radius:8px; height:42px; border:1px solid #e2e8f0; background:#f8fafc; color:#6c757d">
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
                        </div>
                    </div>
                    <div class="card-body" style="padding:24px">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Họ tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control mt-1 <?= !empty($errors['ho_ten']) ? 'is-invalid' : '' ?>"
                                           name="ho_ten" value="<?= htmlspecialchars($account['ho_ten']) ?>"
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
                                           name="email" value="<?= htmlspecialchars($account['email']) ?>"
                                           style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                                    <?php if (!empty($errors['email'])): ?>
                                        <div class="invalid-feedback"><?= $errors['email'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Chức vụ & Liên kết -->
                <div class="card shadow-sm mb-4" style="border-radius:12px; overflow:hidden; border:none">
                    <div class="card-header d-flex align-items-center py-3 px-4" style="background:#fff; border-bottom:1px solid #e2e8f0">
                        <div style="width:36px; height:36px; border-radius:8px; background:#6f42c11a; color:#6f42c1; display:flex; align-items:center; justify-content:center; margin-right:12px; flex-shrink:0">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <div class="font-weight-bold" style="color:#2d3748">Chức vụ & Liên kết</div>
                        </div>
                    </div>
                    <div class="card-body" style="padding:24px">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Chức vụ <span class="text-danger">*</span></label>
                                    <?php if ($account['chuc_vu'] === 'super_admin' && $adminRole !== 'super_admin'): ?>
                                        <input type="text" class="form-control mt-1" value="super_admin" disabled
                                               style="border-radius:8px; height:42px; border:1px solid #e2e8f0; background:#f8fafc">
                                        <input type="hidden" name="chuc_vu" value="super_admin">
                                    <?php else: ?>
                                        <select class="form-control mt-1 <?= !empty($errors['chuc_vu']) ? 'is-invalid' : '' ?>" name="chuc_vu"
                                                style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                                            <?php foreach ($roles as $role): ?>
                                                <option value="<?= htmlspecialchars($role) ?>"
                                                    <?= $account['chuc_vu'] === $role ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($role) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if (!empty($errors['chuc_vu'])): ?>
                                            <div class="invalid-feedback"><?= $errors['chuc_vu'] ?></div>
                                        <?php endif; ?>
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
                                                <?= (int)($account['ma_nhan_vien'] ?? 0) === (int)$emp['ma_nhan_vien'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars(trim($emp['ho_dem'] . ' ' . $emp['ten'])) ?>
                                                (<?= htmlspecialchars($emp['chuc_vu'] ?? '—') ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Đổi mật khẩu -->
                <div class="card shadow-sm mb-4" style="border-radius:12px; overflow:hidden; border:none">
                    <div class="card-header d-flex align-items-center py-3 px-4" style="background:#fff; border-bottom:1px solid #e2e8f0">
                        <div style="width:36px; height:36px; border-radius:8px; background:#e74a3b1a; color:#e74a3b; display:flex; align-items:center; justify-content:center; margin-right:12px; flex-shrink:0">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div>
                            <div class="font-weight-bold" style="color:#2d3748">Đổi mật khẩu</div>
                            <div class="text-muted" style="font-size:0.78rem">Để trống nếu không muốn thay đổi</div>
                        </div>
                    </div>
                    <div class="card-body" style="padding:24px">
                        <div class="col-md-6 px-0">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Mật khẩu mới</label>
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

                <!-- Nút submit -->
                <div class="d-flex justify-content-end pb-4" style="gap:10px">
                    <a href="<?= admin_route('accounts') ?>" class="btn btn-light px-4" style="border-radius:8px">Hủy</a>
                    <button type="submit" name="edit_account" class="btn btn-warning px-5" style="border-radius:8px; color:#fff">
                        <i class="fas fa-save mr-2"></i>Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
