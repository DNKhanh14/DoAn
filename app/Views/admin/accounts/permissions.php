<div class="container-fluid">
    <div class="page-title-bar">
        <h1><i class="fas fa-shield-alt mr-2"></i>Phân quyền theo chức vụ</h1>
        <a href="<?= admin_route('accounts') ?>" class="btn btn-outline-secondary btn-sm" style="border-radius:8px">
            <i class="fas fa-arrow-left mr-1"></i> Quay lại
        </a>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px; border-left:4px solid #1cc88a">
            <i class="fas fa-check-circle mr-1"></i> <?= htmlspecialchars($message) ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <!-- Chú thích -->
    <div class="d-flex align-items-center mb-4" style="gap:12px; flex-wrap:wrap">
        <div class="stat-pill">
            <i class="fas fa-toggle-on mr-1" style="color:#1cc88a"></i> Bật = có quyền truy cập
        </div>
        <div class="stat-pill">
            <i class="fas fa-toggle-off mr-1" style="color:#9ca3af"></i> Tắt = không có quyền
        </div>
        <div class="stat-pill">
            <i class="fas fa-crown mr-1" style="color:#f6c23e"></i> Super Admin luôn có toàn quyền
        </div>
    </div>

    <div class="card shadow-sm" style="border-radius:12px; overflow:hidden; border:none">
        <form method="POST" id="permissionsForm">

            <!-- Header bảng -->
            <?php
            $roleColors = [
                'Quản lý'   => '#1e5bb8',
                'Lễ tân'    => '#20c9a6',
                'Thợ chính' => '#1cc88a',
                'Thợ phụ'   => '#6c757d',
            ];
            $roleIcons = [
                'Quản lý'   => 'fa-user-tie',
                'Lễ tân'    => 'fa-user-check',
                'Thợ chính' => 'fa-cut',
                'Thợ phụ'   => 'fa-user',
            ];
            $visibleRoles = array_filter($allRoles, fn($r) => $r !== 'super_admin');
            ?>

            <div class="perm-header d-flex align-items-center px-4 py-3 border-bottom" style="background:#f8fafc">
                <div style="min-width:200px; font-weight:600; color:#4a5568; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px">Chức năng</div>
                <?php foreach ($visibleRoles as $role):
                    $rc = $roleColors[$role] ?? '#6c757d';
                    $ri = $roleIcons[$role] ?? 'fa-user';
                ?>
                <div class="text-center flex-fill">
                    <div style="display:inline-flex; align-items:center; gap:6px; background:<?= $rc ?>1a; color:<?= $rc ?>; border-radius:20px; padding:5px 14px; font-size:0.82rem; font-weight:600">
                        <i class="fas <?= $ri ?>" style="font-size:0.75rem"></i><?= htmlspecialchars($role) ?>
                    </div>
                    <div class="mt-1" style="font-size:0.72rem">
                        <a href="#" class="select-all" data-role="<?= htmlspecialchars($role) ?>"
                           style="color:<?= $rc ?>; text-decoration:none; font-weight:600">Tất cả</a>
                        <span class="text-muted mx-1">|</span>
                        <a href="#" class="deselect-all" data-role="<?= htmlspecialchars($role) ?>"
                           style="color:#e74a3b; text-decoration:none; font-weight:600">Xóa</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Rows module -->
            <?php
            $moduleIcons = [
                'dashboard' => ['fa-home',               '#17a2b8'],
                'booking'   => ['fa-calendar-alt',        '#1e5bb8'],
                'pos'       => ['fa-file-invoice-dollar', '#1cc88a'],
                'crm'       => ['fa-users',               '#f6c23e'],
                'services'  => ['fa-cut',                 '#e74a3b'],
                'inventory' => ['fa-boxes',               '#6c757d'],
                'employees' => ['fa-user-tie',            '#1e5bb8'],
                'hr'        => ['fa-money-bill-wave',      '#1cc88a'],
                'reports'   => ['fa-chart-bar',           '#6f42c1'],
               
            ];
            $totalModules = count($modules);
            $modIdx = 0;
            ?>

            <?php foreach ($modules as $moduleKey => $moduleName):
                [$mIcon, $mColor] = $moduleIcons[$moduleKey] ?? ['fa-circle', '#6c757d'];
                $modIdx++;
            ?>
            <div class="perm-row d-flex align-items-center px-4 py-3 <?= $modIdx < $totalModules ? 'border-bottom' : '' ?>"
                 style="transition:background .15s">
                <!-- Tên module -->
                <div style="min-width:200px; display:flex; align-items:center; gap:10px">
                    <div style="width:32px; height:32px; border-radius:8px; background:<?= $mColor ?>1a; color:<?= $mColor ?>; display:flex; align-items:center; justify-content:center; flex-shrink:0">
                        <i class="fas <?= $mIcon ?>" style="font-size:13px"></i>
                    </div>
                    <span style="font-weight:600; color:#2d3748; font-size:0.9rem"><?= htmlspecialchars($moduleName) ?></span>
                </div>

                <!-- Switches cho từng role -->
                <?php foreach ($visibleRoles as $role):
                    $checked = (bool)($allPermissions[$role][$moduleKey] ?? false);
                    $rc = $roleColors[$role] ?? '#6c757d';
                ?>
                <div class="text-center flex-fill">
                    <div class="custom-control custom-switch d-inline-block">
                        <input type="checkbox"
                               class="custom-control-input perm-check"
                               id="perm_<?= $role ?>_<?= $moduleKey ?>"
                               name="permissions[<?= htmlspecialchars($role) ?>][<?= $moduleKey ?>]"
                               value="1"
                               data-role="<?= htmlspecialchars($role) ?>"
                               <?= $checked ? 'checked' : '' ?>>
                        <label class="custom-control-label"
                               for="perm_<?= $role ?>_<?= $moduleKey ?>"></label>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>

            <!-- Super Admin row -->
            <div class="perm-row d-flex align-items-center px-4 py-3" style="background:#fffbeb; border-top:2px solid #fde68a">
                <div style="min-width:200px; display:flex; align-items:center; gap:10px">
                    <div style="width:32px; height:32px; border-radius:8px; background:#f6c23e1a; color:#f6c23e; display:flex; align-items:center; justify-content:center; flex-shrink:0">
                        <i class="fas fa-crown" style="font-size:13px"></i>
                    </div>
                    <span style="font-weight:600; color:#92400e; font-size:0.9rem">Super Admin</span>
                </div>
                <div class="flex-grow-1 text-center text-muted small" style="color:#92400e !important">
                    <i class="fas fa-infinity mr-1"></i> Luôn có toàn quyền — không thể thay đổi
                </div>
            </div>

            <!-- Footer save -->
            <div class="px-4 py-3 border-top d-flex align-items-center justify-content-between" style="background:#f8fafc">
                <span class="text-muted small">
                    <i class="fas fa-info-circle mr-1"></i>Thay đổi có hiệu lực ngay khi lưu
                </span>
                <button type="submit" name="save_permissions" class="btn btn-primary px-4" style="border-radius:8px">
                    <i class="fas fa-save mr-2"></i>Lưu phân quyền
                </button>
            </div>
        </form>
    </div>

    <!-- Chú thích chức vụ -->
    <div class="alert alert-info mt-4" style="border-radius:10px; border-left:4px solid #17a2b8">
        <i class="fas fa-info-circle mr-2"></i>
        Hệ thống sử dụng 4 chức vụ cố định: <strong>Quản lý</strong>, <strong>Lễ tân</strong>, <strong>Thợ chính</strong>, <strong>Thợ phụ</strong>.
        Bật switch để cấp quyền, tắt để thu hồi. Nhấn <strong>Lưu phân quyền</strong> để áp dụng.
    </div>
</div>

<style>
.stat-pill {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 5px 14px;
    font-size: 0.82rem;
    color: #4a5568;
    display: inline-block;
}
.perm-row:hover { background: #f8fafc; }
.perm-header { background: #f8fafc !important; }
</style>

<script>
document.querySelectorAll('.select-all').forEach(function(link) {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        var role = this.dataset.role;
        document.querySelectorAll('.perm-check[data-role="' + role + '"]').forEach(function(cb) {
            cb.checked = true;
        });
    });
});
document.querySelectorAll('.deselect-all').forEach(function(link) {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        var role = this.dataset.role;
        document.querySelectorAll('.perm-check[data-role="' + role + '"]').forEach(function(cb) {
            cb.checked = false;
        });
    });
});
</script>
