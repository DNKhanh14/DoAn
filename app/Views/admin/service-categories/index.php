<div class="container-fluid">
    <div class="page-title-bar">
        <h1>Danh mục dịch vụ</h1>
        <button class="btn-them-moi" type="button" data-toggle="modal" data-target="#add_new_category">
            <i class="fas fa-plus mr-1"></i> Thêm mới
        </button>
    </div>
    
    <div class="card card-salon shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-salon table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Tên danh mục</th>
                            <th style="width: 150px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td class="align-middle"><?= htmlspecialchars($category['ten_danh_muc']) ?></td>
                                <td class="align-middle">
                                    <?php if (strtolower($category['ten_danh_muc']) !== 'uncategorized'): ?>
                                        <?php 
                                            $deleteData = 'delete_' . $category['ma_danh_muc']; 
                                            $editData = 'edit_' . $category['ma_danh_muc']; 
                                        ?>
                                        <div class="d-inline-flex" style="gap: 6px;">
                                            <button class="btn btn-warning btn-sm text-white" type="button" data-toggle="modal" data-target="#<?= $editData ?>" title="Sửa">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm" type="button" data-toggle="modal" data-target="#<?= $deleteData ?>" title="Xóa">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge badge-secondary py-1 px-2" style="font-weight: 500; opacity: 0.7;">Hệ thống</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="add_new_category" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm danh mục</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="category_name_input">Tên danh mục</label>
                    <input type="text" id="category_name_input" class="form-control" placeholder="Ví dụ: Cắt tóc">
                    <div class="invalid-feedback" id="required_category_name" style="display: none;">Vui lòng nhập tên danh mục!</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="add_category_bttn">Thêm</button>
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
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Sửa danh mục</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Tên danh mục mới</label>
                            <input type="text" class="form-control" id="input_category_name_<?= (int) $category['ma_danh_muc'] ?>" value="<?= htmlspecialchars($category['ten_danh_muc']) ?>">
                            <div class="invalid-feedback" id="invalid_input_<?= (int) $category['ma_danh_muc'] ?>">Vui lòng nhập tên danh mục.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                        <button type="button" data-id="<?= (int) $category['ma_danh_muc'] ?>" class="btn btn-success edit_category_bttn">Lưu</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="<?= $deleteData ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Xóa danh mục</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        Bạn có chắc chắn muốn xóa danh mục <strong>"<?= htmlspecialchars($category['ten_danh_muc']) ?>"</strong> không?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                        <button type="button" data-id="<?= (int) $category['ma_danh_muc'] ?>" class="btn btn-danger delete_category_bttn">Xóa</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>