<div class="container-fluid">
    <div class="page-title-bar">
        <h1>Quản lý khách hàng (CRM)</h1>
        <button type="button" class="btn-them-moi" data-toggle="modal" data-target="#modalThemKhach">
            <i class="fas fa-user-plus mr-1"></i> Thêm khách hàng
        </button>
    </div>

    <?php if (($_GET['msg'] ?? '') === 'deleted'): ?>
        <div class="alert alert-success py-2" style="border-radius:10px; border-left:4px solid #1cc88a">
            <i class="fas fa-check-circle mr-1"></i> Đã xóa khách hàng.
        </div>
    <?php endif; ?>

    <!-- Tìm kiếm + Thống kê -->
    <div class="d-flex align-items-center flex-wrap mb-4" style="gap:12px">
        <div class="d-flex" style="gap:8px">
            <div class="stat-pill">
                <i class="fas fa-users mr-1"></i>
                <strong><?= $pag['total'] ?? count($clients) ?></strong> khách hàng
            </div>
        </div>
        <div class="ml-auto">
            <form method="get" action="index.php" style="display:flex; gap:8px; align-items:center">
                <input type="hidden" name="route" value="crm">
                <div class="input-group" style="max-width:340px">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white border-right-0">
                            <i class="fas fa-search text-muted" style="font-size:0.85rem"></i>
                        </span>
                    </div>
                    <input type="text" name="q" class="form-control border-left-0"
                           placeholder="Tìm theo tên, SĐT..."
                           value="<?= htmlspecialchars($searchQuery ?? '') ?>">
                    <?php if (!empty($searchQuery)): ?>
                        <div class="input-group-append">
                            <a href="<?= admin_route('crm') ?>" class="btn btn-outline-secondary" title="Xóa bộ lọc">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">Tìm</button>
                        </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <?php if (empty($clients)): ?>
        <div class="card card-salon shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-3">
                    <?= !empty($searchQuery) ? 'Không tìm thấy khách hàng nào.' : 'Chưa có khách hàng nào.' ?>
                </p>
                <?php if (empty($searchQuery)): ?>
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalThemKhach">
                        <i class="fas fa-user-plus mr-1"></i> Thêm khách hàng đầu tiên
                    </button>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <?php
        $colors = ['#1e5bb8','#e74a3b','#1cc88a','#f6c23e','#6f42c1','#fd7e14','#20c9a6'];
        ?>
        <div class="card shadow-sm" style="border-radius:10px; overflow:hidden; border:none">
            <div class="card-body p-0">
                <?php foreach ($clients as $i => $c):
                    $code = client_display_code($c);
                    $name = trim($c['ten'] . ' ' . $c['ho_dem']);
                    $color = $colors[$i % count($colors)];
                ?>
                <div class="crm-item d-flex align-items-center px-4 py-3 <?= $i < count($clients) - 1 ? 'border-bottom' : '' ?>"
                     style="transition: background .15s;">

                    <!-- Avatar -->
                    <div class="crm-avatar mr-3"
                         style="width:44px; height:44px; border-radius:50%; background:<?= $color ?>1a; color:<?= $color ?>; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:1rem; font-weight:700">
                        <?= strtoupper(mb_substr($c['ten'], 0, 1)) ?>
                    </div>

                    <!-- Tên + Mã KH -->
                    <div class="flex-grow-1 mr-3">
                        <div class="font-weight-bold" style="font-size:0.95rem; color:#2d3748">
                            <?= htmlspecialchars($name) ?>
                        </div>
                        <div class="text-muted" style="font-size:0.78rem">
                            <a href="<?= admin_route('crm/detail', ['id' => $c['ma_khach_hang']]) ?>"
                               style="color:<?= $color ?>; font-weight:600; text-decoration:none">
                                <?= htmlspecialchars($code) ?>
                            </a>
                        </div>
                    </div>

                    <!-- Điện thoại -->
                    <div class="crm-phone mr-4 text-muted" style="min-width:130px; font-size:0.85rem">
                        <?php if (!empty($c['so_dien_thoai'])): ?>
                            <i class="fas fa-phone mr-1" style="color:<?= $color ?>; width:14px"></i>
                            <?= htmlspecialchars($c['so_dien_thoai']) ?>
                        <?php endif; ?>
                    </div>

                    <!-- Thao tác -->
                    <div style="flex-shrink:0; display:flex; gap:6px">
                        <a href="<?= admin_route('crm/detail', ['id' => $c['ma_khach_hang']]) ?>"
                           title="Xem & Sửa"
                           style="width:34px; height:34px; border-radius:8px; background:#f6c23e; border:none; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; text-decoration:none; flex-shrink:0">
                            <i class="fas fa-pencil-alt" style="color:#fff; font-size:14px"></i>
                        </a>
                        <a href="<?= admin_route('booking/create', ['client_id' => $c['ma_khach_hang']]) ?>"
                           title="Đặt lịch"
                           style="width:34px; height:34px; border-radius:8px; background:#1cc88a; border:none; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; text-decoration:none; flex-shrink:0">
                            <i class="fas fa-calendar-plus" style="color:#fff; font-size:13px"></i>
                        </a>
                        <button type="button" title="Xóa"
                                data-toggle="modal" data-target="#delClient<?= (int)$c['ma_khach_hang'] ?>"
                                style="width:34px; height:34px; border-radius:8px; background:#e74a3b; border:none; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0">
                            <i class="fas fa-trash" style="color:#fff; font-size:14px"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal xác nhận xóa -->
                <div class="modal fade" id="delClient<?= (int)$c['ma_khach_hang'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none">
                            <div class="modal-header" style="background:#fff5f5; border-bottom:1px solid #fee2e2; padding:16px 20px">
                                <h6 class="modal-title text-danger mb-0">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>Xóa khách hàng
                                </h6>
                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                            </div>
                            <div class="modal-body text-center py-4">
                                <div style="width:60px; height:60px; border-radius:50%; background:#fee2e2; display:flex; align-items:center; justify-content:center; margin:0 auto 16px">
                                    <i class="fas fa-user-times text-danger fa-lg"></i>
                                </div>
                                <p class="mb-1 text-muted">Bạn có chắc muốn xóa</p>
                                <strong class="text-dark"><?= htmlspecialchars($name) ?></strong>
                                <p class="text-muted small mt-2 mb-0">Hành động này không thể hoàn tác.</p>
                            </div>
                            <div class="modal-footer py-2 justify-content-center" style="background:#fafafa">
                                <button type="button" class="btn btn-light btn-sm px-4" data-dismiss="modal" style="border-radius:8px">Hủy</button>
                                <form method="post" action="index.php" class="d-inline">
                                    <input type="hidden" name="route" value="crm">
                                    <input type="hidden" name="delete_client_id" value="<?= (int)$c['ma_khach_hang'] ?>">
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

<!-- Modal Thêm khách hàng -->
<div class="modal fade" id="modalThemKhach" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none">
            <div class="modal-header" style="background:linear-gradient(135deg, #1cc88a, #20c9a6); border:none; padding:20px 24px">
                <div class="d-flex align-items-center">
                    <div style="width:36px; height:36px; border-radius:8px; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; margin-right:12px">
                        <i class="fas fa-user-plus" style="color:#fff; font-size:15px"></i>
                    </div>
                    <h5 class="modal-title mb-0" style="color:#fff; font-weight:600">Thêm khách hàng mới</h5>
                </div>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:0.8; text-shadow:none"><span>&times;</span></button>
            </div>
            <form id="crmAddForm">
                <div class="modal-body" style="padding:24px">
                    <div class="form-group">
                        <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Họ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control mt-1" name="first_name" required style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Tên</label>
                        <input type="text" class="form-control mt-1" name="last_name" style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="text" class="form-control mt-1" name="phone" required
                               pattern="\d{10}" title="Số điện thoại phải đủ 10 chữ số"
                               style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                    </div>
                </div>
                <div class="modal-footer" style="padding:16px 24px; background:#f8fafc; border-top:1px solid #e2e8f0">
                    <button type="button" class="btn btn-light px-4" data-dismiss="modal" style="border-radius:8px">Huỷ</button>
                    <button type="submit" class="btn btn-success px-4" style="border-radius:8px">
                        <i class="fas fa-user-plus mr-1"></i> Thêm khách
                    </button>
                </div>
            </form>
        </div>
    </div>
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
.crm-item:hover { background: #f8fafc; }
@media (max-width: 768px) {
    .crm-phone { display: none; }
}
</style>

<script>
(function () {
    document.getElementById('crmAddForm')?.addEventListener('submit', function (e) {
        e.preventDefault();
        const fd = new FormData(this);
        fd.append('action', 'quick_client');
        fetch('index.php?route=ajax/pos', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(function (data) {
                if (data.client) location.reload();
                else alert(data.error || 'Không thêm được khách');
            });
    });
})();
</script>
