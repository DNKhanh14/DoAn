<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Thêm dịch vụ mới</h6>
        </div>
        <div class="card-body">
            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
            <?php endif; ?>
            <form method="POST" action="<?= admin_route('services', ['do' => 'Add']) ?>">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tên dịch vụ</label>
                            <input type="text" class="form-control" name="service_name" required value="<?= htmlspecialchars($old['service_name'] ?? '') ?>">
                            <?php if (!empty($errors['service_name'])): ?><div class="invalid-feedback" style="display:block;"><?= $errors['service_name'] ?></div><?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Danh mục</label>
                            <select class="custom-select" name="service_category">
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= (int) $category['ma_danh_muc'] ?>" <?= (($old['service_category'] ?? '') == $category['ma_danh_muc']) ? 'selected' : '' ?>>
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
                            <input type="text" class="form-control" name="service_duration" required value="<?= htmlspecialchars($old['service_duration'] ?? '') ?>">
                            <?php if (!empty($errors['service_duration'])): ?><div class="invalid-feedback" style="display:block;"><?= $errors['service_duration'] ?></div><?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Giá (VND)</label>
                            <input type="text" inputmode="numeric" class="form-control money-input" name="service_price" required placeholder="VD: 300.000" value="<?= htmlspecialchars($old['service_price'] ?? '') ?>">
                            <small class="text-muted">Nhập số tiền, hệ thống tự thêm dấu phân cách nghìn.</small>
                            <?php if (!empty($errors['service_price'])): ?><div class="invalid-feedback" style="display:block;"><?= $errors['service_price'] ?></div><?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea class="form-control" name="service_description" style="resize: none;"><?= htmlspecialchars($old['service_description'] ?? '') ?></textarea>
                    <?php if (!empty($errors['service_description'])): ?><div class="invalid-feedback" style="display:block;"><?= $errors['service_description'] ?></div><?php endif; ?>
                </div>
                <button type="submit" name="add_new_service" class="btn btn-primary">Thêm dịch vụ</button>
            </form>
            <?php if ($successScript): ?>
                <script>swal("Thành công","Đã thêm dịch vụ mới", "success").then(() => { window.location.replace("<?= $successScript ?>"); });</script>
            <?php endif; ?>
        </div>
    </div>
</div>
