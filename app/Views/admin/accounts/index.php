<div class="container-fluid">
    <div class="page-title-bar">
        <h1><i class="fas fa-user-shield mr-2"></i>Quản lý tài khoản</h1>
        <a href="<?= admin_route('accounts/create') ?>" class="btn-them-moi">
            <i class="fas fa-plus mr-1"></i> Tạo tài khoản
        </a>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px; border-left:4px solid #1cc88a">
            <i class="fas fa-check-circle mr-1"></i> <?= htmlspecialchars($message) ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" style="border-radius:10px; border-left:4px solid #e74a3b">
            <i class="fas fa-exclamation-circle mr-1"></i> <?= htmlspecialchars($error) ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <!-- Tổng quan nhanh -->
    <div class="row mb-4">
        <div class="col-auto">
            <div class="stat-pill">
                <i class="fas fa-users mr-1"></i>
                <strong><?= count($accounts) ?></strong> tài khoản
            </div>
        </div>
        <div class="col-auto">
            <div class="stat-pill">
                <i class="fas fa-link mr-1" style="color:#1cc88a"></i>
                <strong><?= count(array_filter($accounts, function($a){ return !empty($a['ma_nhan_vien']); })) ?></strong> liên kết NV
            </div>
        </div>
    </div>

    <?php if (empty($accounts)): ?>
        <div class="card card-salon shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-user-shield fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-3">Chưa có tài khoản nào.</p>
                <a href="<?= admin_route('accounts/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> Tạo tài khoản đầu tiên
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="card shadow-sm" style="border-radius:10px; overflow:hidden; border:none">
            <div class="card-body p-0">
                <?php foreach ($accounts as $i => $acc): ?>
                    <?php
                    $isSelf    = (int)$acc['ma_nguoi_dung'] === (int)($_SESSION['admin_id_barbershop_Xw211qAAsq4'] ?? 0);
                    $roleBadge = [
                        'super_admin' => ['bg' => '#e74a3b', 'icon' => 'fa-crown'],
                        'Quản lý'     => ['bg' => '#1e5bb8', 'icon' => 'fa-user-tie'],
                        'Lễ tân'      => ['bg' => '#20c9a6', 'icon' => 'fa-user-check'],
                        'Thợ chính'   => ['bg' => '#1cc88a', 'icon' => 'fa-cut'],
                        'Thợ phụ'     => ['bg' => '#6c757d', 'icon' => 'fa-user'],
                    ][$acc['chuc_vu']] ?? ['bg' => '#6c757d', 'icon' => 'fa-user'];
                    ?>
                    <div class="acc-item d-flex align-items-center px-4 py-3 <?= $i < count($accounts) - 1 ? 'border-bottom' : '' ?>"
                         style="transition: background .15s;">

                        <!-- Avatar / Icon -->
                        <div class="acc-avatar mr-3"
                             style="width:44px; height:44px; border-radius:50%; background:<?= $roleBadge['bg'] ?>1a; color:<?= $roleBadge['bg'] ?>; display:flex; align-items:center; justify-content:center; flex-shrink:0">
                            <i class="fas <?= $roleBadge['icon'] ?> fa-lg"></i>
                        </div>

                        <!-- Username + Tên -->
                        <div class="flex-grow-1 mr-3">
                            <div class="font-weight-bold" style="font-size:0.95rem; color:#2d3748">
                                <?= htmlspecialchars($acc['ten_dang_nhap']) ?>
                                <?php if ($isSelf): ?>
                                    <span class="badge badge-warning ml-1" style="font-size:0.65rem; padding:3px 6px">Bạn</span>
                                <?php endif; ?>
                            </div>
                            <div class="text-muted" style="font-size:0.82rem"><?= htmlspecialchars($acc['ho_ten']) ?></div>
                        </div>

                        <!-- Email -->
                        <div class="acc-email mr-4 text-muted" style="font-size:0.85rem; min-width:180px">
                            <i class="fas fa-envelope mr-1" style="width:14px"></i><?= htmlspecialchars($acc['email']) ?>
                        </div>

                        <!-- Chức vụ badge -->
                        <div class="acc-role mr-4" style="min-width:110px">
                            <span class="badge text-white" style="background:<?= $roleBadge['bg'] ?>; font-size:0.75rem; padding:5px 10px; border-radius:20px">
                                <i class="fas <?= $roleBadge['icon'] ?> mr-1" style="font-size:0.7rem"></i><?= htmlspecialchars($acc['chuc_vu']) ?>
                            </span>
                        </div>

                        <!-- Liên kết NV -->
                        <div class="acc-link mr-4" style="min-width:160px; font-size:0.82rem">
                            <?php if ($acc['ma_nhan_vien']): ?>
                                <span style="color:#1cc88a; font-weight:600">
                                    <i class="fas fa-link mr-1"></i><?= htmlspecialchars(trim(($acc['nv_ho_dem'] ?? '') . ' ' . ($acc['nv_ten'] ?? ''))) ?>
                                </span>
                                <div class="text-muted" style="font-size:0.72rem"><?= htmlspecialchars($acc['nv_chuc_vu'] ?? '') ?></div>
                            <?php else: ?>
                                <span class="text-muted"><i class="fas fa-unlink mr-1"></i>Chưa liên kết</span>
                            <?php endif; ?>
                        </div>

                        <!-- Thao tác -->
                        <div class="acc-actions" style="flex-shrink:0; display:flex; gap:6px">
                        <a href="<?= admin_route('accounts/edit', ['ma_nguoi_dung' => $acc['ma_nguoi_dung']]) ?>"
                               title="Sửa"
                               style="width:34px; height:34px; border-radius:8px; background:#f6c23e; border:none; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; text-decoration:none; flex-shrink:0">
                                <i class="fas fa-edit" style="color:#fff; font-size:14px"></i>
                            </a>
                            <button class="btn-reset-pw" title="Reset mật khẩu"
                                    data-id="<?= (int)$acc['ma_nguoi_dung'] ?>"
                                    data-name="<?= htmlspecialchars($acc['ten_dang_nhap']) ?>"
                                    style="width:34px; height:34px; border-radius:8px; background:#17a2b8; border:none; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0">
                                <i class="fas fa-key" style="color:#fff; font-size:14px"></i>
                            </button>
                            <?php if (!$isSelf): ?>
                            <button type="button" title="Xóa"
                                    data-toggle="modal" data-target="#del<?= $acc['ma_nguoi_dung'] ?>"
                                    style="width:34px; height:34px; border-radius:8px; background:#e74a3b; border:none; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0">
                                <i class="fas fa-trash" style="color:#fff; font-size:14px"></i>
                            </button>
                            <!-- Modal xóa -->
                            <div class="modal fade" id="del<?= $acc['ma_nguoi_dung'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none">
                                        <div class="modal-header" style="background:#fff5f5; border-bottom:1px solid #fee2e2; padding:16px 20px">
                                            <h6 class="modal-title text-danger mb-0">
                                                <i class="fas fa-exclamation-triangle mr-2"></i>Xóa tài khoản
                                            </h6>
                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <div class="modal-body text-center py-4">
                                            <div style="width:60px; height:60px; border-radius:50%; background:#fee2e2; display:flex; align-items:center; justify-content:center; margin:0 auto 16px">
                                                <i class="fas fa-user-times text-danger fa-lg"></i>
                                            </div>
                                            <p class="mb-1 text-muted">Xóa tài khoản</p>
                                            <strong class="text-dark"><?= htmlspecialchars($acc['ten_dang_nhap']) ?></strong>
                                            <p class="text-muted small mt-2 mb-0">Hành động này không thể hoàn tác.</p>
                                        </div>
                                        <div class="modal-footer py-2 justify-content-center" style="background:#fafafa">
                                            <button type="button" class="btn btn-light btn-sm px-4" data-dismiss="modal" style="border-radius:8px">Hủy</button>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="delete_account" value="1">
                                                <input type="hidden" name="ma_nguoi_dung" value="<?= (int)$acc['ma_nguoi_dung'] ?>">
                                                <button type="submit" class="btn btn-danger btn-sm px-4" style="border-radius:8px">
                                                    <i class="fas fa-trash mr-1"></i>Xóa
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($pag)): ?>
        <div class="px-1 mt-3"><?= render_pagination($pag, $baseUrl) ?></div>
    <?php endif; ?>

    <!-- Card phân quyền -->
    <?php if ($adminRole === 'super_admin'): ?>
    <div class="card shadow-sm mt-4" style="border-left:4px solid #6f42c1; border-radius:10px; overflow:hidden">
        <div class="card-body d-flex align-items-center justify-content-between py-3">
            <div class="d-flex align-items-center">
                <div style="width:44px; height:44px; border-radius:10px; background:#6f42c11a; color:#6f42c1; display:flex; align-items:center; justify-content:center; margin-right:16px">
                    <i class="fas fa-shield-alt fa-lg"></i>
                </div>
                <div>
                    <strong style="color:#2d3748">Phân quyền theo chức vụ</strong>
                    <div class="text-muted small">Chỉnh sửa quyền truy cập cho từng chức vụ</div>
                </div>
            </div>
            <a href="<?= admin_route('accounts/permissions') ?>" class="btn btn-primary" style="border-radius:8px">
                <i class="fas fa-cog mr-1"></i> Quản lý phân quyền
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.stat-pill {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 5px 14px;
    font-size: 0.85rem;
    color: #4a5568;
    display: inline-block;
}

.acc-item:hover {
    background: #f8fafc;
}

@media (max-width: 992px) {
    .acc-email,
    .acc-link { display: none; }
}
</style>

<script>
document.querySelectorAll('.btn-reset-pw').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var id   = this.dataset.id;
        var name = this.dataset.name;
        swal({
            title: 'Reset mật khẩu?',
            text: 'Mật khẩu của "' + name + '" sẽ được đặt lại thành: 123456789',
            icon: 'warning',
            buttons: ['Hủy', 'Reset'],
            dangerMode: true,
        }).then(function(ok) {
            if (!ok) return;
            fetch('index.php?route=ajax/accounts', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'do=ResetPassword&ma_nguoi_dung=' + id
            })
            .then(function(r){ return r.json(); })
            .then(function(res) {
                if (res.success) {
                    swal('Đã reset!', res.message, 'success');
                } else {
                    swal('Lỗi', res.error || 'Có lỗi xảy ra', 'error');
                }
            });
        });
    });
});
</script>
