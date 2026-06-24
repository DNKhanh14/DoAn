<div class="container-fluid">
    <div class="page-title-bar">
        <h1>Danh sách dịch vụ<?php if (!empty($searchQuery)): ?> <small class="text-muted">— "<?= htmlspecialchars($searchQuery) ?>"</small><?php endif; ?></h1>
        <a href="<?= admin_route('services', ['do' => 'Add']) ?>" class="btn-them-moi"><i class="fas fa-plus mr-1"></i> Thêm dịch vụ</a>
    </div>

    <!-- Tìm kiếm -->
    <form method="get" action="index.php" class="mb-3 d-flex" style="gap:8px">
        <input type="hidden" name="route" value="services">
        <input type="text" name="q" class="form-control form-control-sm" style="max-width:280px" placeholder="Tìm dịch vụ..." value="<?= htmlspecialchars($searchQuery ?? '') ?>">
        <button type="submit" class="btn btn-outline-secondary btn-sm">Tìm</button>
        <?php if (!empty($searchQuery)): ?>
            <a href="<?= admin_route('services') ?>" class="btn btn-link btn-sm">Xóa bộ lọc</a>
        <?php endif; ?>
    </form>

    <?php
    // Gom dịch vụ theo danh mục
    $grouped = [];
    foreach ($services as $svc) {
        $catName = $svc['ten_danh_muc'] ?? 'Chưa phân loại';
        $grouped[$catName][] = $svc;
    }
    $catIndex = 0;
    ?>

    <?php if (empty($services)): ?>
        <div class="alert alert-info">Chưa có dịch vụ. <a href="<?= admin_route('services', ['do' => 'Add']) ?>">Thêm ngay</a>.</div>
    <?php else: ?>
        <div class="accordion services-accordion" id="servicesAccordion">
            <?php foreach ($grouped as $catName => $items):
                $collapseId = 'cat_collapse_' . $catIndex;
                $headingId  = 'cat_heading_' . $catIndex;
                $isOpen     = ($catIndex === 0 || !empty($searchQuery));
            ?>
            <div class="card card-salon shadow mb-2">
                <div class="card-header p-0" id="<?= $headingId ?>">
                    <button class="btn btn-block text-left d-flex justify-content-between align-items-center py-3 px-4 services-cat-toggle<?= $isOpen ? '' : ' collapsed' ?>"
                            type="button"
                            data-toggle="collapse"
                            data-target="#<?= $collapseId ?>"
                            aria-expanded="<?= $isOpen ? 'true' : 'false' ?>"
                            aria-controls="<?= $collapseId ?>">
                        <span><strong><?= htmlspecialchars($catName) ?></strong> <span class="badge badge-secondary ml-2"><?= count($items) ?></span></span>
                        <i class="fas fa-chevron-<?= $isOpen ? 'up' : 'down' ?> text-muted cat-chevron"></i>
                    </button>
                </div>
                <div id="<?= $collapseId ?>" class="collapse<?= $isOpen ? ' show' : '' ?>" aria-labelledby="<?= $headingId ?>" data-parent="#servicesAccordion">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-salon table-bordered mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Tên dịch vụ</th>
                                        <th style="width:30%">Mô tả</th>
                                        <th>Giá</th>
                                        <th>Thời lượng</th>
                                        <th style="width:110px">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $service): ?>
                                        <?php $deleteId = 'del_svc_' . $service['ma_dich_vu']; ?>
                                        <tr>
                                            <td><?= htmlspecialchars($service['ten_dich_vu']) ?></td>
                                            <td><?= htmlspecialchars($service['mo_ta']) ?></td>
                                            <td class="text-nowrap"><?= format_money((float) $service['gia']) ?></td>
                                            <td class="text-nowrap"><?= (int) $service['thoi_luong'] ?> phút</td>
                                            <td>
                                                <a href="<?= admin_route('services', ['do' => 'Edit', 'ma_dich_vu' => $service['ma_dich_vu']]) ?>"
                                                   class="btn btn-warning btn-sm" title="Sửa"><i class="fa fa-edit"></i></a>
                                                <button class="btn btn-danger btn-sm" type="button"
                                                        data-toggle="modal" data-target="#<?= $deleteId ?>" title="Xóa">
                                                    <i class="fa fa-trash"></i>
                                                </button>

                                                <!-- Modal xóa -->
                                                <div class="modal fade" id="<?= $deleteId ?>" tabindex="-1">
                                                    <div class="modal-dialog modal-sm">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h6 class="modal-title">Xóa dịch vụ</h6>
                                                                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                            </div>
                                                            <div class="modal-body">Xóa <strong>"<?= htmlspecialchars($service['ten_dich_vu']) ?>"</strong>?</div>
                                                            <div class="modal-footer py-2">
                                                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Hủy</button>
                                                                <form method="post" action="index.php" class="d-inline">
                                                                    <input type="hidden" name="route" value="services">
                                                                    <input type="hidden" name="delete_service" value="1">
                                                                    <input type="hidden" name="ma_dich_vu" value="<?= (int) $service['ma_dich_vu'] ?>">
                                                                    <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php $catIndex++; endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.services-cat-toggle { background: #f8f9fc; border: none; font-size: 0.95rem; }
.services-cat-toggle:hover { background: #eaecf4; }
.services-cat-toggle:focus { box-shadow: none; }
.services-cat-toggle.collapsed .cat-chevron { transform: none; }
.services-cat-toggle:not(.collapsed) .cat-chevron { }
</style>

<script>
/* Cập nhật icon chevron khi accordion mở/đóng */
document.querySelectorAll('.services-cat-toggle').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var icon = this.querySelector('.cat-chevron');
        if (!icon) return;
        var isExpanded = this.getAttribute('aria-expanded') === 'true';
        icon.classList.toggle('fa-chevron-up', !isExpanded);
        icon.classList.toggle('fa-chevron-down', isExpanded);
    });
});
</script>
