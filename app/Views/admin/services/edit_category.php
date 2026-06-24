<div class="container-fluid">
    <div class="page-title-bar"><h1>Sửa danh mục dịch vụ</h1></div>
    <div class="card card-salon shadow mb-4">
        <div class="card-body">
            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
            <?php endif; ?>
            <form method="POST" action="index.php">
                <input type="hidden" name="route" value="services">
                <input type="hidden" name="do" value="EditCategory">
                <input type="hidden" name="ma_danh_muc" value="<?= (int) $category['ma_danh_muc'] ?>">
                <div class="form-group">
                    <label>Tên danh mục</label>
                    <input type="text" class="form-control" name="category_name" value="<?= htmlspecialchars($category['ten_danh_muc']) ?>" required>
                    <?php if (!empty($errors['category_name'])): ?><div class="invalid-feedback" style="display:block;"><?= $errors['category_name'] ?></div><?php endif; ?>
                </div>
                <button type="submit" name="edit_category_sbmt" class="btn btn-primary">Lưu thay đổi</button>
                <a href="<?= admin_route('services') ?>" class="btn btn-secondary">Hủy</a>
            </form>
            <?php if ($successScript): ?>
                <script>swal("Thành công","Đã cập nhật danh mục dịch vụ", "success").then(() => { window.location.replace("<?= $successScript ?>"); });</script>
            <?php endif; ?>
        </div>
    </div>
</div>
