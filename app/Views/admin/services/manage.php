<div class="container-fluid">
    <div class="page-title-bar">
        <h1>Danh sách dịch vụ<?php if (!empty($searchQuery)): ?> <small class="text-muted" style="font-size:1rem">— "<?= htmlspecialchars($searchQuery) ?>"</small><?php endif; ?></h1>
        <a href="<?= admin_route('services', ['do' => 'Add']) ?>" class="btn-them-moi"><i class="fas fa-plus mr-1"></i> Thêm dịch vụ</a>
    </div>

    <!-- Tìm kiếm -->
    <form method="get" action="index.php" class="mb-4">
        <div class="input-group" style="max-width:380px">
            <input type="hidden" name="route" value="services">
            <div class="input-group-prepend">
                <span class="input-group-text bg-white border-right-0">
                    <i class="fas fa-search text-muted" style="font-size:0.85rem"></i>
                </span>
            </div>
            <input type="text" name="q" class="form-control border-left-0"
                   placeholder="Tìm tên dịch vụ..."
                   value="<?= htmlspecialchars($searchQuery ?? '') ?>">
            <?php if (!empty($searchQuery)): ?>
                <div class="input-group-append">
                    <a href="<?= admin_route('services') ?>" class="btn btn-outline-secondary" title="Xóa bộ lọc">
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

    <?php
    $grouped = [];
    foreach ($services as $svc) {
        $catName = $svc['ten_danh_muc'] ?? 'Chưa phân loại';
        $grouped[$catName][] = $svc;
    }
    $catIndex = 0;

    // Màu sắc cho từng danh mục
    $catColors = ['#1e5bb8', '#e74a3b', '#1cc88a', '#f6c23e', '#6f42c1', '#fd7e14', '#20c9a6'];
    $catIcons  = ['fa-cut', 'fa-spa', 'fa-star', 'fa-gem', 'fa-magic', 'fa-heart', 'fa-leaf'];
    ?>

    <?php if (empty($services)): ?>
        <div class="card card-salon shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-cut fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-3">Chưa có dịch vụ nào.</p>
                <a href="<?= admin_route('services', ['do' => 'Add']) ?>" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> Thêm dịch vụ đầu tiên
                </a>
            </div>
        </div>
    <?php else: ?>

        <!-- Tổng quan nhanh -->
        <div class="row mb-4">
            <div class="col-auto">
                <div class="stat-pill">
                    <i class="fas fa-layer-group mr-1"></i>
                    <strong><?= count($grouped) ?></strong> danh mục
                </div>
            </div>
            <div class="col-auto">
                <div class="stat-pill">
                    <i class="fas fa-cut mr-1"></i>
                    <strong><?= count($services) ?></strong> dịch vụ
                </div>
            </div>
        </div>

        <div class="accordion" id="servicesAccordion">
        <?php foreach ($grouped as $catName => $items):
            $collapseId = 'cat_collapse_' . $catIndex;
            $isOpen     = ($catIndex === 0 || !empty($searchQuery));
            $color      = $catColors[$catIndex % count($catColors)];
            $icon       = $catIcons[$catIndex % count($catIcons)];
            $totalMin   = array_sum(array_column($items, 'thoi_luong'));
            $minPrice   = min(array_column($items, 'gia'));
            $maxPrice   = max(array_column($items, 'gia'));
        ?>
        <div class="card shadow-sm mb-3 svc-card" style="border-left: 4px solid <?= $color ?>; border-radius:10px; overflow:hidden">

            <!-- Header danh mục -->
            <div class="card-header svc-cat-header p-0" style="background:#fff">
                <button class="btn btn-block text-left d-flex align-items-center py-3 px-4 svc-cat-toggle<?= $isOpen ? '' : ' collapsed' ?>"
                        type="button"
                        data-toggle="collapse"
                        data-target="#<?= $collapseId ?>"
                        aria-expanded="<?= $isOpen ? 'true' : 'false' ?>">

                    <!-- Icon danh mục -->
                    <div class="svc-cat-icon mr-3" style="background:<?= $color ?>1a; color:<?= $color ?>; width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0">
                        <i class="fas <?= $icon ?>"></i>
                    </div>

                    <!-- Tên + thống kê -->
                    <div class="flex-grow-1">
                        <div class="font-weight-bold" style="font-size:1rem; color:#2d3748">
                            <?= htmlspecialchars($catName) ?>
                        </div>
                        <div class="text-muted" style="font-size:0.78rem; margin-top:2px">
                            <?= count($items) ?> dịch vụ
                            <?php if ($minPrice === $maxPrice): ?>
                                &middot; <?= format_money($minPrice) ?>
                            <?php else: ?>
                                &middot; <?= format_money($minPrice) ?> – <?= format_money($maxPrice) ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Badge số lượng -->
                    <span class="badge mr-3" style="background:<?= $color ?>1a; color:<?= $color ?>; font-size:0.8rem; padding:5px 10px; border-radius:20px">
                        <?= count($items) ?>
                    </span>

                    <!-- Chevron -->
                    <i class="fas fa-chevron-<?= $isOpen ? 'up' : 'down' ?> text-muted svc-chevron" style="font-size:0.8rem"></i>
                </button>
            </div>

            <!-- Danh sách dịch vụ -->
            <div id="<?= $collapseId ?>" class="collapse<?= $isOpen ? ' show' : '' ?>">
                <div class="card-body p-0">
                    <?php foreach ($items as $idx => $service): ?>
                        <?php $deleteId = 'del_svc_' . $service['ma_dich_vu']; ?>
                        <div class="svc-item d-flex align-items-center px-4 py-3 <?= $idx < count($items) - 1 ? 'border-bottom' : '' ?>">

                            <!-- STT -->
                            <div class="svc-no mr-3 text-center" style="width:28px; height:28px; border-radius:50%; background:<?= $color ?>15; color:<?= $color ?>; font-size:0.75rem; font-weight:700; line-height:28px; flex-shrink:0">
                                <?= $idx + 1 ?>
                            </div>

                            <!-- Tên + mô tả -->
                            <div class="flex-grow-1 mr-3">
                                <div class="font-weight-bold" style="font-size:0.92rem; color:#2d3748">
                                    <?= htmlspecialchars($service['ten_dich_vu']) ?>
                                </div>
                                <?php if (!empty($service['mo_ta'])): ?>
                                <div class="text-muted svc-desc" style="font-size:0.8rem; margin-top:2px">
                                    <?= htmlspecialchars($service['mo_ta']) ?>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Thời lượng -->
                            <div class="svc-meta text-center mr-4" style="min-width:70px">
                                <div style="font-size:0.72rem; color:#9ca3af; text-transform:uppercase; letter-spacing:0.5px">Thời gian</div>
                                <div style="font-size:0.9rem; font-weight:600; color:#4a5568">
                                    <i class="fas fa-clock mr-1" style="font-size:0.75rem; color:<?= $color ?>"></i><?= (int)$service['thoi_luong'] ?> phút
                                </div>
                            </div>

                            <!-- Giá -->
                            <div class="svc-meta text-center mr-4" style="min-width:110px">
                                <div style="font-size:0.72rem; color:#9ca3af; text-transform:uppercase; letter-spacing:0.5px">Giá</div>
                                <div style="font-size:0.95rem; font-weight:700; color:<?= $color ?>">
                                    <?= format_money((float)$service['gia']) ?>
                                </div>
                            </div>

                            <!-- Thao tác -->
                            <div class="svc-actions" style="flex-shrink:0; display:flex; gap:6px">
                                <a href="<?= admin_route('services', ['do' => 'Edit', 'ma_dich_vu' => $service['ma_dich_vu']]) ?>"
                                   title="Sửa"
                                   style="width:34px; height:34px; border-radius:8px; background:#f6c23e; border:none; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; text-decoration:none; flex-shrink:0">
                                    <i class="fas fa-edit" style="color:#fff; font-size:14px"></i>
                                </a>
                                <button type="button" title="Xóa"
                                        data-toggle="modal" data-target="#<?= $deleteId ?>"
                                        style="width:34px; height:34px; border-radius:8px; background:#e74a3b; border:none; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0">
                                    <i class="fas fa-trash" style="color:#fff; font-size:14px"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Modal xóa -->
                        <div class="modal fade" id="<?= $deleteId ?>" tabindex="-1">
                            <div class="modal-dialog modal-sm">
                                <div class="modal-content" style="border-radius:12px; overflow:hidden">
                                    <div class="modal-header" style="background:#fff5f5; border-bottom:1px solid #fee2e2">
                                        <h6 class="modal-title text-danger"><i class="fas fa-exclamation-triangle mr-1"></i> Xóa dịch vụ</h6>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body text-center py-3">
                                        <p class="mb-1">Xóa dịch vụ</p>
                                        <strong>"<?= htmlspecialchars($service['ten_dich_vu']) ?>"</strong>?
                                        <p class="text-muted small mt-2 mb-0">Hành động này không thể hoàn tác.</p>
                                    </div>
                                    <div class="modal-footer py-2 justify-content-center">
                                        <button type="button" class="btn btn-light btn-sm px-4" data-dismiss="modal">Hủy</button>
                                        <form method="post" action="index.php" class="d-inline">
                                            <input type="hidden" name="route" value="services">
                                            <input type="hidden" name="delete_service" value="1">
                                            <input type="hidden" name="ma_dich_vu" value="<?= (int)$service['ma_dich_vu'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm px-4">Xóa</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php $catIndex++; endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.svc-card { transition: box-shadow .2s; }
.svc-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.08) !important; }

.svc-cat-toggle { background: #fff; border: none; transition: background .15s; }
.svc-cat-toggle:hover { background: #f8fafc; }
.svc-cat-toggle:focus { box-shadow: none; outline: none; }

.svc-item { transition: background .15s; }
.svc-item:hover { background: #f8fafc; }

.svc-desc {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
    max-width: 320px;
}

.stat-pill {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 5px 14px;
    font-size: 0.85rem;
    color: #4a5568;
    display: inline-block;
}

@media (max-width: 576px) {
    .svc-meta { display: none; }
    .svc-desc { display: none; }
}
</style>

<script>
document.querySelectorAll('.svc-cat-toggle').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var icon = this.querySelector('.svc-chevron');
        if (!icon) return;
        setTimeout(function() {
            var expanded = btn.getAttribute('aria-expanded') === 'true';
            icon.classList.toggle('fa-chevron-up', expanded);
            icon.classList.toggle('fa-chevron-down', !expanded);
        }, 50);
    });
});
</script>
