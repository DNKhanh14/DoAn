<div class="container-fluid">
    <div class="page-title-bar">
        <h1>Sửa dịch vụ</h1>
        <a href="<?= admin_route('services') ?>" class="btn btn-outline-secondary btn-sm" style="border-radius:8px">
            <i class="fas fa-arrow-left mr-1"></i> Danh sách
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm" style="border-radius:12px; overflow:hidden; border:none">
                <!-- Header gradient -->
                <div style="background:linear-gradient(135deg, #f6a623, #f6c23e); padding:24px 28px">
                    <div class="d-flex align-items-center">
                        <div style="width:52px; height:52px; border-radius:50%; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; margin-right:16px; flex-shrink:0">
                            <i class="fas fa-edit fa-lg" style="color:#fff"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" style="color:#fff; font-weight:600">
                                <?= htmlspecialchars($service['ten_dich_vu']) ?>
                            </h5>
                            <div style="color:rgba(255,255,255,0.85); font-size:0.85rem">Chỉnh sửa thông tin dịch vụ</div>
                        </div>
                    </div>
                </div>

                <div class="card-body" style="padding:28px">
                    <?php if (!empty($errors['general'])): ?>
                        <div class="alert alert-danger" style="border-radius:8px; border-left:4px solid #e74a3b">
                            <i class="fas fa-exclamation-circle mr-1"></i><?= htmlspecialchars($errors['general']) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= admin_route('services', ['do' => 'Edit', 'ma_dich_vu' => $service['ma_dich_vu']]) ?>">
                        <input type="hidden" name="ma_dich_vu" value="<?= (int)$service['ma_dich_vu'] ?>">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Tên dịch vụ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control mt-1 <?= !empty($errors['service_name']) ? 'is-invalid' : '' ?>"
                                           name="service_name" required
                                           value="<?= htmlspecialchars($service['ten_dich_vu']) ?>"
                                           style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                                    <?php if (!empty($errors['service_name'])): ?>
                                        <div class="invalid-feedback"><?= $errors['service_name'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Danh mục</label>
                                    <select class="form-control mt-1" name="service_category"
                                            style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= (int)$category['ma_danh_muc'] ?>"
                                                <?= ($category['ma_danh_muc'] == $service['ma_danh_muc']) ? 'selected' : '' ?>>
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
                                    <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Thời lượng (phút) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control mt-1 <?= !empty($errors['service_duration']) ? 'is-invalid' : '' ?>"
                                           name="service_duration" required
                                           value="<?= htmlspecialchars($service['thoi_luong']) ?>"
                                           style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                                    <?php if (!empty($errors['service_duration'])): ?>
                                        <div class="invalid-feedback"><?= $errors['service_duration'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Giá (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="text" inputmode="numeric" class="form-control money-input mt-1 <?= !empty($errors['service_price']) ? 'is-invalid' : '' ?>"
                                           name="service_price" required
                                           value="<?= htmlspecialchars($service['gia']) ?>"
                                           style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                                    <?php if (!empty($errors['service_price'])): ?>
                                        <div class="invalid-feedback"><?= $errors['service_price'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Mô tả</label>
                            <textarea class="form-control mt-1" name="service_description" rows="3"
                                      style="border-radius:8px; border:1px solid #e2e8f0; resize:none"><?= htmlspecialchars($service['mo_ta']) ?></textarea>
                            <?php if (!empty($errors['service_description'])): ?>
                                <div class="text-danger small mt-1"><?= $errors['service_description'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-end" style="gap:10px; padding-top:16px; border-top:1px solid #e2e8f0; margin-top:8px">
                            <a href="<?= admin_route('services') ?>" class="btn btn-light px-4" style="border-radius:8px">Hủy</a>
                            <button type="submit" name="edit_service_sbmt" class="btn btn-warning px-4" style="border-radius:8px; color:#fff">
                                <i class="fas fa-save mr-1"></i>Lưu thay đổi
                            </button>
                        </div>
                    </form>

                    <?php if ($successScript): ?>
                        <script>swal("Thành công","Đã cập nhật dịch vụ", "success").then(() => { window.location.replace("<?= $successScript ?>"); });</script>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
