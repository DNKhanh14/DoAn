<div class="container-fluid">
    <div class="page-title-bar"><h1>Sửa dịch vụ</h1></div>
    <div class="card card-salon shadow mb-4">
        <div class="card-body">
            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
            <?php endif; ?>
            <form method="POST" action="<?= admin_route('services', ['do' => 'Edit', 'ma_dich_vu' => $service['ma_dich_vu']]) ?>">
                <input type="hidden" name="ma_dich_vu" value="<?= (int) $service['ma_dich_vu'] ?>">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tên dịch vụ</label>
                            <input type="text" class="form-control" name="service_name" required value="<?= htmlspecialchars($service['ten_dich_vu']) ?>">
                            <?php if (!empty($errors['service_name'])): ?><div class="invalid-feedback" style="display:block;"><?= $errors['service_name'] ?></div><?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Danh mục</label>
                            <select class="custom-select" name="service_category">
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= (int) $category['ma_danh_muc'] ?>" <?= ($category['ma_danh_muc'] == $service['ma_danh_muc']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['ten_danh_muc']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Thời lượng (phút)</label>
                            <input type="text" class="form-control" name="service_duration" required value="<?= htmlspecialchars($service['thoi_luong']) ?>">
                            <?php if (!empty($errors['service_duration'])): ?><div class="invalid-feedback" style="display:block;"><?= $errors['service_duration'] ?></div><?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Giá (VNĐ)</label>
                            <input type="text" inputmode="numeric" class="form-control money-input" name="service_price" required placeholder="VD: 300.000" value="<?= htmlspecialchars($service['gia']) ?>">
                            <?php if (!empty($errors['service_price'])): ?><div class="invalid-feedback" style="display:block;"><?= $errors['service_price'] ?></div><?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea class="form-control" name="service_description" style="resize: none;"><?= htmlspecialchars($service['mo_ta']) ?></textarea>
                    <?php if (!empty($errors['service_description'])): ?><div class="invalid-feedback" style="display:block;"><?= $errors['service_description'] ?></div><?php endif; ?>
                </div>
                <button type="submit" name="edit_service_sbmt" class="btn btn-primary">Lưu thay đổi</button>
            </form>
            <?php if ($successScript): ?>
                <script>swal("Thành công","Đã cập nhật dịch vụ", "success").then(() => { window.location.replace("<?= $successScript ?>"); });</script>
            <?php endif; ?>
        </div>
    </div>
</div>
