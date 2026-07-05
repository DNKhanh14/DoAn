<div class="container-fluid">
    <div class="page-title-bar">
        <h1>Sửa nhân viên</h1>
        <a href="<?= admin_route('employees') ?>" class="btn btn-outline-secondary btn-sm" style="border-radius:8px">
            <i class="fas fa-arrow-left mr-1"></i> Danh sách
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm" style="border-radius:12px; overflow:hidden; border:none">
                <!-- Header card -->
                <div style="background:linear-gradient(135deg, #f6a623, #f6c23e); padding:24px 28px">
                    <div class="d-flex align-items-center">
                        <div style="width:52px; height:52px; border-radius:50%; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; margin-right:16px; flex-shrink:0; font-size:1.3rem; font-weight:700; color:#fff">
                            <?= strtoupper(mb_substr($employee['ten'], 0, 1)) ?>
                        </div>
                        <div>
                            <h5 class="mb-0" style="color:#fff; font-weight:600">
                                <?= htmlspecialchars(trim($employee['ho_dem'] . ' ' . $employee['ten'])) ?>
                            </h5>
                            <div style="color:rgba(255,255,255,0.85); font-size:0.85rem">
                                <?= htmlspecialchars($employee['chuc_vu'] ?? '—') ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body" style="padding:28px">
                    <?php if (!empty($errors['general'])): ?>
                        <div class="alert alert-danger" style="border-radius:8px; border-left:4px solid #e74a3b">
                            <i class="fas fa-exclamation-circle mr-1"></i><?= htmlspecialchars($errors['general']) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= admin_route('employees', ['do' => 'Edit', 'ma_nhan_vien' => $employee['ma_nhan_vien']]) ?>">
                        <input type="hidden" name="ma_nhan_vien" value="<?= (int)$employee['ma_nhan_vien'] ?>">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control mt-1 <?= !empty($errors['ten']) ? 'is-invalid' : '' ?>"
                                           name="ten" required value="<?= htmlspecialchars($employee['ten']) ?>"
                                           style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                                    <?php if (!empty($errors['ten'])): ?><div class="invalid-feedback"><?= $errors['ten'] ?></div><?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Họ đệm <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control mt-1 <?= !empty($errors['ho_dem']) ? 'is-invalid' : '' ?>"
                                           name="ho_dem" required value="<?= htmlspecialchars($employee['ho_dem']) ?>"
                                           style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                                    <?php if (!empty($errors['ho_dem'])): ?><div class="invalid-feedback"><?= $errors['ho_dem'] ?></div><?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Chức vụ</label>
                            <select class="form-control mt-1" name="chuc_vu" style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                                <option value="">-- Chọn chức vụ --</option>
                                <?php foreach (['Quản lý', 'Lễ tân', 'Thợ chính', 'Thợ phụ'] as $cv): ?>
                                    <option value="<?= $cv ?>" <?= ($employee['chuc_vu'] ?? '') === $cv ? 'selected' : '' ?>><?= $cv ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control mt-1 <?= !empty($errors['so_dien_thoai']) ? 'is-invalid' : '' ?>"
                                           name="so_dien_thoai" required pattern="\d{10}"
                                           value="<?= htmlspecialchars($employee['so_dien_thoai']) ?>"
                                           style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                                    <?php if (!empty($errors['so_dien_thoai'])): ?><div class="invalid-feedback"><?= $errors['so_dien_thoai'] ?></div><?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Email <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control mt-1 <?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
                                           name="email" required value="<?= htmlspecialchars($employee['email']) ?>"
                                           style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                                    <?php if (!empty($errors['email'])): ?><div class="invalid-feedback"><?= $errors['email'] ?></div><?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Lương cơ bản (VND)</label>
                            <input type="text" class="form-control mt-1"
                                   name="luong_co_ban"
                                   value="<?= number_format((float)($employee['luong_co_ban'] ?? 0), 0, ',', '.') ?>"
                                   placeholder="VD: 5.000.000"
                                   style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                            <small class="text-muted">Nhập số không cần ký hiệu VND, ví dụ: 5000000 hoặc 5.000.000</small>
                        </div>

                        <div class="d-flex justify-content-end" style="gap:10px; padding-top:16px; border-top:1px solid #e2e8f0; margin-top:8px">
                            <a href="<?= admin_route('employees') ?>" class="btn btn-light px-4" style="border-radius:8px">Hủy</a>
                            <button type="submit" name="edit_employee_sbmt" class="btn btn-warning px-4" style="border-radius:8px; color:#fff">
                                <i class="fas fa-save mr-1"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>

                    <?php if ($successScript): ?>
                        <script>swal("Thành công","Đã cập nhật nhân viên", "success").then(() => { window.location.replace("<?= $successScript ?>"); });</script>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
