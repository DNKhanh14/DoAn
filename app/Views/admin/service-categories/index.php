<div class="container-fluid">
    <div class="page-title-bar">
        <h1>Danh mục dịch vụ</h1>
        <button class="btn-them-moi" type="button" data-toggle="modal" data-target="#add_new_category">
            <i class="fas fa-plus mr-1"></i> Thêm mới
        </button>
    </div>

    <!-- Tổng quan nhanh -->
    <div class="row mb-4">
        <div class="col-auto">
            <div class="stat-pill">
                <i class="fas fa-layer-group mr-1"></i>
                <strong><?= count($categories) ?></strong> danh mục
            </div>
        </div>
        <div class="col-auto">
            <div class="stat-pill">
                <i class="fas fa-tags mr-1"></i>
                <strong><?= count(array_filter($categories, function($c) { return strtolower($c['ten_danh_muc']) !== 'uncategorized'; })) ?></strong> tùy chỉnh
            </div>
        </div>
    </div>

    <?php if (empty($categories)): ?>
        <div class="card card-salon shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-layer-group fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-3">Chưa có danh mục nào.</p>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add_new_category">
                    <i class="fas fa-plus mr-1"></i> Thêm danh mục đầu tiên
                </button>
            </div>
        </div>
    <?php else: ?>
        <?php
        // Màu sắc cho từng danh mục
        $catColors = ['#1e5bb8', '#e74a3b', '#1cc88a', '#f6c23e', '#6f42c1', '#fd7e14', '#20c9a6'];
        $catIcons  = ['fa-cut', 'fa-spa', 'fa-star', 'fa-gem', 'fa-magic', 'fa-heart', 'fa-leaf'];
        $catIndex = 0;
        ?>
        
        <div class="row">
            <?php foreach ($categories as $category): ?>
                <?php 
                    $deleteData = 'delete_' . $category['ma_danh_muc']; 
                    $editData = 'edit_' . $category['ma_danh_muc'];
                    $isSystem = strtolower($category['ten_danh_muc']) === 'uncategorized';
                    $color = $catColors[$catIndex % count($catColors)];
                    $icon = $catIcons[$catIndex % count($catIcons)];
                ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow-sm category-card" style="border-left: 4px solid <?= $color ?>; border-radius:10px; overflow:hidden; height:100%; transition: all 0.2s;">
                        <div class="card-body p-4">
                            <!-- Header với icon -->
                            <div class="d-flex align-items-start mb-3">
                                <div class="category-icon mr-3" style="background:<?= $color ?>1a; color:<?= $color ?>; width:50px; height:50px; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0">
                                    <i class="fas <?= $icon ?> fa-lg"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1 font-weight-bold" style="color:#2d3748; font-size:1.1rem">
                                        <?= htmlspecialchars($category['ten_danh_muc']) ?>
                                    </h5>
                                    <?php if ($isSystem): ?>
                                        <span class="badge badge-secondary" style="font-size:0.7rem; padding:3px 8px">
                                            <i class="fas fa-shield-alt mr-1" style="font-size:0.65rem"></i>Hệ thống
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Thao tác -->
                            <div class="d-flex justify-content-end" style="gap:8px; margin-top:20px">
                                <?php if (!$isSystem): ?>
                                    <button type="button" data-toggle="modal" data-target="#<?= $editData ?>" 
                                            class="btn btn-sm" 
                                            title="Sửa"
                                            style="width:36px; height:36px; border-radius:8px; background:#f6c23e; border:none; display:inline-flex; align-items:center; justify-content:center; cursor:pointer">
                                        <i class="fas fa-edit" style="color:#fff; font-size:14px"></i>
                                    </button>
                                    <button type="button" data-toggle="modal" data-target="#<?= $deleteData ?>" 
                                            class="btn btn-sm" 
                                            title="Xóa"
                                            style="width:36px; height:36px; border-radius:8px; background:#e74a3b; border:none; display:inline-flex; align-items:center; justify-content:center; cursor:pointer">
                                        <i class="fas fa-trash" style="color:#fff; font-size:14px"></i>
                                    </button>
                                <?php else: ?>
                                    <div class="text-muted" style="font-size:0.85rem; padding:8px 0">
                                        <i class="fas fa-info-circle mr-1"></i>Không thể chỉnh sửa
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php $catIndex++; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.category-card {
    transition: all 0.2s ease;
}
.category-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,.12) !important;
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

.category-icon {
    transition: transform 0.2s ease;
}

.category-card:hover .category-icon {
    transform: scale(1.1);
}

@media (max-width: 768px) {
    .col-md-6 {
        margin-bottom: 1rem;
    }
}
</style>

<div class="modal fade" id="add_new_category" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none">
            <div class="modal-header" style="background:linear-gradient(135deg, #1e5bb8, #3a7bd5); border:none; padding:20px 24px">
                <div class="d-flex align-items-center">
                    <div style="width:36px; height:36px; border-radius:8px; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; margin-right:12px">
                        <i class="fas fa-layer-group" style="color:#fff; font-size:15px"></i>
                    </div>
                    <h5 class="modal-title mb-0" style="color:#fff; font-weight:600">Thêm danh mục mới</h5>
                </div>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:0.8; text-shadow:none"><span>&times;</span></button>
            </div>
            <div class="modal-body" style="padding:24px">
                <div class="form-group mb-0">
                    <label for="category_name_input" class="font-weight-600 text-muted" style="font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px">Tên danh mục</label>
                    <input type="text" id="category_name_input" class="form-control mt-1" placeholder="Ví dụ: Cắt tóc, Nhuộm tóc..." style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                    <div class="text-danger small mt-1" id="required_category_name" style="display: none;"><i class="fas fa-exclamation-circle mr-1"></i>Vui lòng nhập tên danh mục!</div>
                </div>
            </div>
            <div class="modal-footer" style="padding:16px 24px; background:#f8fafc; border-top:1px solid #e2e8f0">
                <button type="button" class="btn btn-light px-4" data-dismiss="modal" style="border-radius:8px">Hủy</button>
                <button type="button" class="btn btn-primary px-4" id="add_category_bttn" style="border-radius:8px">
                    <i class="fas fa-plus mr-1"></i> Thêm
                </button>
            </div>
        </div>
    </div>
</div>

<?php foreach ($categories as $category): ?>
    <?php if (strtolower($category['ten_danh_muc']) !== 'uncategorized'): ?>
        <?php 
            $deleteData = 'delete_' . $category['ma_danh_muc']; 
            $editData = 'edit_' . $category['ma_danh_muc']; 
        ?>
        
        <div class="modal fade" id="<?= $editData ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none">
                    <div class="modal-header" style="background:linear-gradient(135deg, #f6a623, #f6c23e); border:none; padding:20px 24px">
                        <div class="d-flex align-items-center">
                            <div style="width:36px; height:36px; border-radius:8px; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; margin-right:12px">
                                <i class="fas fa-edit" style="color:#fff; font-size:15px"></i>
                            </div>
                            <h5 class="modal-title mb-0" style="color:#fff; font-weight:600">Sửa danh mục</h5>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:0.8; text-shadow:none"><span>&times;</span></button>
                    </div>
                    <div class="modal-body" style="padding:24px">
                        <div class="form-group mb-0">
                            <label class="font-weight-600 text-muted" style="font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px">Tên danh mục mới</label>
                            <input type="text" class="form-control mt-1" 
                                   id="input_category_name_<?= (int) $category['ma_danh_muc'] ?>" 
                                   value="<?= htmlspecialchars($category['ten_danh_muc']) ?>"
                                   style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                            <div class="text-danger small mt-1" id="invalid_input_<?= (int) $category['ma_danh_muc'] ?>" style="display:none">
                                <i class="fas fa-exclamation-circle mr-1"></i>Vui lòng nhập tên danh mục.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="padding:16px 24px; background:#f8fafc; border-top:1px solid #e2e8f0">
                        <button type="button" class="btn btn-light px-4" data-dismiss="modal" style="border-radius:8px">Hủy</button>
                        <button type="button" data-id="<?= (int) $category['ma_danh_muc'] ?>" class="btn btn-warning px-4 edit_category_bttn" style="border-radius:8px; color:#fff">
                            <i class="fas fa-save mr-1"></i> Lưu
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="<?= $deleteData ?>" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none">
                    <div class="modal-header" style="background:#fff5f5; border-bottom:1px solid #fee2e2; padding:16px 20px">
                        <h6 class="modal-title text-danger mb-0">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Xóa danh mục
                        </h6>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <div style="width:60px; height:60px; border-radius:50%; background:#fee2e2; display:flex; align-items:center; justify-content:center; margin:0 auto 16px">
                            <i class="fas fa-trash text-danger fa-lg"></i>
                        </div>
                        <p class="mb-1 text-muted">Bạn có chắc muốn xóa danh mục</p>
                        <strong class="text-dark">"<?= htmlspecialchars($category['ten_danh_muc']) ?>"</strong>
                        <p class="text-muted small mt-2 mb-0">Hành động này không thể hoàn tác.</p>
                    </div>
                    <div class="modal-footer py-2 justify-content-center border-top" style="background:#fafafa">
                        <button type="button" class="btn btn-light btn-sm px-4" data-dismiss="modal" style="border-radius:8px">Hủy</button>
                        <button type="button" data-id="<?= (int) $category['ma_danh_muc'] ?>" class="btn btn-danger btn-sm px-4 delete_category_bttn" style="border-radius:8px">
                            <i class="fas fa-trash mr-1"></i>Xóa
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>