<div class="container-fluid">
    <div class="page-title-bar">
        <h1>Danh sách nhân viên</h1>
        <a href="<?= admin_route('employees', ['do' => 'Add']) ?>" class="btn-them-moi">
            <i class="fas fa-plus mr-1"></i> Thêm mới
        </a>
    </div>

    <!-- Tổng quan nhanh -->
    <div class="row mb-4">
        <div class="col-auto">
            <div class="stat-pill">
                <i class="fas fa-user-tie mr-1"></i>
                <strong><?= count($employees) ?></strong> nhân viên
            </div>
        </div>
        <div class="col-auto">
            <div class="stat-pill">
                <i class="fas fa-user-check mr-1" style="color:#1cc88a"></i>
                <strong><?= count(array_filter($employees, function($e){ return !empty($e['has_account']); })) ?></strong> có tài khoản
            </div>
        </div>
    </div>

    <?php if (empty($employees)): ?>
        <div class="card card-salon shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-3">Chưa có nhân viên nào.</p>
                <a href="<?= admin_route('employees', ['do' => 'Add']) ?>" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> Thêm nhân viên đầu tiên
                </a>
            </div>
        </div>
    <?php else: ?>
        <?php
        $colors = ['#1e5bb8','#e74a3b','#1cc88a','#f6c23e','#6f42c1','#fd7e14','#20c9a6'];
        $roleBadgeStyle = [
            'Quản lý'   => 'background:#1e5bb8',
            'Lễ tân'    => 'background:#20c9a6',
            'Thợ chính' => 'background:#1cc88a',
            'Thợ phụ'   => 'background:#6c757d',
        ];
        ?>

        <div class="card shadow-sm" style="border-radius:10px; overflow:hidden; border:none">
            <div class="card-body p-0">
                <?php foreach ($employees as $i => $employee): ?>
                    <?php 
                        $color = $colors[$i % count($colors)];
                        $deleteData = 'delete_employee_' . $employee['ma_nhan_vien'];
                        $roleBg = $roleBadgeStyle[$employee['chuc_vu'] ?? ''] ?? 'background:#6c757d';
                    ?>
                    <div class="emp-item d-flex align-items-center px-4 py-3 <?= $i < count($employees) - 1 ? 'border-bottom' : '' ?>"
                         style="transition: background .15s;">

                        <!-- STT / Avatar -->
                        <div class="emp-avatar mr-3 text-center font-weight-bold"
                             style="width:44px; height:44px; border-radius:50%; background:<?= $color ?>1a; color:<?= $color ?>; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1rem">
                            <?= strtoupper(mb_substr($employee['ten'], 0, 1)) ?>
                        </div>

                        <!-- Tên + chức vụ -->
                        <div class="flex-grow-1 mr-3">
                            <div class="font-weight-bold" style="font-size:0.95rem; color:#2d3748">
                                <?= htmlspecialchars(trim($employee['ho_dem'] . ' ' . $employee['ten'])) ?>
                            </div>
                            <div class="mt-1">
                                <span class="badge text-white" style="<?= $roleBg ?>; font-size:0.7rem; padding:3px 8px; border-radius:20px">
                                    <?= htmlspecialchars($employee['chuc_vu'] ?? '—') ?>
                                </span>
                            </div>
                        </div>

                        <!-- Liên hệ -->
                        <div class="emp-contact mr-4 text-muted" style="font-size:0.85rem; min-width:180px">
                            <?php if (!empty($employee['so_dien_thoai'])): ?>
                            <div><i class="fas fa-phone mr-1" style="width:14px"></i><?= htmlspecialchars($employee['so_dien_thoai']) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($employee['email'])): ?>
                            <div class="text-truncate" style="max-width:180px"><i class="fas fa-envelope mr-1" style="width:14px"></i><?= htmlspecialchars($employee['email']) ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Tài khoản -->
                        <div class="emp-account mr-4" style="min-width:130px">
                            <?php if (!empty($employee['has_account'])): ?>
                                <span style="font-size:0.82rem; color:#1cc88a; font-weight:600">
                                    <i class="fas fa-user-check mr-1"></i><?= htmlspecialchars($employee['account_username'] ?? '') ?>
                                </span>
                            <?php else: ?>
                                <span style="font-size:0.82rem; color:#9ca3af">
                                    <i class="fas fa-user-times mr-1"></i>Chưa có
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Thao tác -->
                        <div class="emp-actions" style="flex-shrink:0; display:flex; gap:6px">
                            <a href="<?= admin_route('employees', ['do' => 'Edit', 'ma_nhan_vien' => $employee['ma_nhan_vien']]) ?>"
                               title="Sửa"
                               style="width:34px; height:34px; border-radius:8px; background:#f6c23e; border:none; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; text-decoration:none; flex-shrink:0">
                                <i class="fas fa-edit" style="color:#fff; font-size:14px"></i>
                            </a>
                            <?php if (empty($employee['has_account'])): ?>
                            <button type="button" title="Tạo tài khoản" class="btn-create-account"
                                    data-id="<?= (int)$employee['ma_nhan_vien'] ?>"
                                    data-name="<?= htmlspecialchars(trim($employee['ho_dem'].' '.$employee['ten'])) ?>"
                                    data-email="<?= htmlspecialchars($employee['email']) ?>"
                                    data-chucvu="<?= htmlspecialchars($employee['chuc_vu'] ?? 'Lễ tân') ?>"
                                    style="width:34px; height:34px; border-radius:8px; background:#1cc88a; border:none; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0">
                                <i class="fas fa-user-plus" style="color:#fff; font-size:13px"></i>
                            </button>
                            <?php endif; ?>
                            <button type="button" title="Xóa"
                                    data-toggle="modal" data-target="#<?= $deleteData ?>"
                                    style="width:34px; height:34px; border-radius:8px; background:#e74a3b; border:none; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0">
                                <i class="fas fa-trash" style="color:#fff; font-size:14px"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Modal xóa -->
                    <div class="modal fade" id="<?= $deleteData ?>" tabindex="-1">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none">
                                <div class="modal-header" style="background:#fff5f5; border-bottom:1px solid #fee2e2; padding:16px 20px">
                                    <h6 class="modal-title text-danger mb-0">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>Xóa nhân viên
                                    </h6>
                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                </div>
                                <div class="modal-body text-center py-4">
                                    <div style="width:60px; height:60px; border-radius:50%; background:#fee2e2; display:flex; align-items:center; justify-content:center; margin:0 auto 16px">
                                        <i class="fas fa-user-minus text-danger fa-lg"></i>
                                    </div>
                                    <p class="mb-1 text-muted">Bạn có chắc muốn xóa nhân viên</p>
                                    <strong class="text-dark"><?= htmlspecialchars(trim($employee['ho_dem'] . ' ' . $employee['ten'])) ?></strong>
                                    <?php if (!empty($employee['has_account'])): ?>
                                        <div class="alert alert-warning mt-3 mb-0 py-2 text-left" style="font-size:0.82rem; border-radius:8px">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            Nhân viên có tài khoản <strong>"<?= htmlspecialchars($employee['account_username'] ?? '') ?>"</strong> — tài khoản sẽ mất liên kết nhưng vẫn tồn tại.
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted small mt-2 mb-0">Hành động này không thể hoàn tác.</p>
                                    <?php endif; ?>
                                </div>
                                <div class="modal-footer py-2 justify-content-center" style="background:#fafafa">
                                    <button type="button" class="btn btn-light btn-sm px-4" data-dismiss="modal" style="border-radius:8px">Hủy</button>
                                    <form method="post" action="index.php" class="d-inline">
                                        <input type="hidden" name="route" value="employees">
                                        <input type="hidden" name="delete_employee" value="1">
                                        <input type="hidden" name="ma_nhan_vien" value="<?= (int)$employee['ma_nhan_vien'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm px-4" style="border-radius:8px">
                                            <i class="fas fa-trash mr-1"></i>Xóa
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($pag)): ?>
        <div class="px-1 mt-3"><?= render_pagination($pag, $baseUrl) ?></div>
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

.emp-item:hover {
    background: #f8fafc;
}

@media (max-width: 768px) {
    .emp-contact,
    .emp-account { display: none; }
}
</style>

<!-- Form ẩn tạo tài khoản nhanh -->
<form id="formCreateAccountQuick" method="POST" action="<?= admin_route('ajax/accounts') ?>" style="display:none">
    <input type="hidden" name="do" value="QuickCreate">
    <input type="hidden" name="ma_nhan_vien" id="qca_emp_id">
    <input type="hidden" name="ten_nhan_vien" id="qca_emp_name">
    <input type="hidden" name="email" id="qca_email">
    <input type="hidden" name="chuc_vu" id="qca_chucvu">
</form>

<script>
document.querySelectorAll('.btn-create-account').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var empId  = this.dataset.id;
        var name   = this.dataset.name;
        var email  = this.dataset.email;
        var chucvu = this.dataset.chucvu || 'Lễ tân';

        swal({
            title: 'Tạo tài khoản cho ' + name + '?',
            text: 'Tài khoản sẽ được tạo tự động với mật khẩu mặc định: 123456789\nEmail: ' + email,
            icon: 'info',
            buttons: ['Hủy', 'Tạo tài khoản'],
        }).then(function(ok) {
            if (!ok) return;
            var data = new URLSearchParams();
            data.append('do', 'QuickCreate');
            data.append('ma_nhan_vien', empId);
            data.append('ten_nhan_vien', name);
            data.append('email', email);
            data.append('chuc_vu', chucvu);
            fetch('index.php?route=ajax/accounts', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: data.toString()
            })
            .then(function(r){ return r.json(); })
            .then(function(res) {
                if (res.success) {
                    swal({
                        title: 'Tạo tài khoản thành công!',
                        content: (function(){
                            var d = document.createElement('div');
                            d.innerHTML = '<div style="background:#f0f4ff;border-radius:8px;padding:12px;text-align:left">'
                                + '<div><b>Tên đăng nhập:</b> <code>' + res.username + '</code></div>'
                                + '<div><b>Mật khẩu:</b> <code>123456789</code></div>'
                                + '</div>'
                                + '<p style="color:#dc2626;font-size:0.85rem;margin-top:8px">Yêu cầu đổi mật khẩu khi đăng nhập lần đầu!</p>';
                            return d;
                        })(),
                        icon: 'success'
                    }).then(function(){ location.reload(); });
                } else {
                    swal('Lỗi', res.error || 'Không thể tạo tài khoản', 'error');
                }
            });
        });
    });
});
</script>
