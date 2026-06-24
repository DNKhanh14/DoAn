<div class="container-fluid">
    <div class="page-title-bar"><h1>Thêm nhân viên</h1></div>
    <div class="card card-salon shadow mb-4">
        <div class="card-body">
            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
            <?php endif; ?>
            <form method="POST" action="<?= admin_route('employees', ['do' => 'Add']) ?>">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tên</label>
                            <input type="text" class="form-control" name="ten" required value="<?= htmlspecialchars($old['ten'] ?? '') ?>">
                            <?php if (!empty($errors['ten'])): ?><div class="invalid-feedback" style="display:block;"><?= $errors['ten'] ?></div><?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Họ đệm</label>
                            <input type="text" class="form-control" name="ho_dem" required value="<?= htmlspecialchars($old['ho_dem'] ?? '') ?>">
                            <?php if (!empty($errors['ho_dem'])): ?><div class="invalid-feedback" style="display:block;"><?= $errors['ho_dem'] ?></div><?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Chức vụ</label>
                    <input type="text" class="form-control" name="chuc_vu" list="jobTitleList" value="<?= htmlspecialchars($old['chuc_vu'] ?? '') ?>" placeholder="VD: Thợ chính, Lễ tân...">
                    <datalist id="jobTitleList">
                        <option value="Thợ chính"><option value="Thợ phụ"><option value="Lễ tân">
                        <option value="Thu ngân"><option value="Quản lý">
                    </datalist>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="text" class="form-control" name="so_dien_thoai" required pattern="\d{10}" title="Số điện thoại phải đủ 10 chữ số" value="<?= htmlspecialchars($old['so_dien_thoai'] ?? '') ?>">
                            <?php if (!empty($errors['so_dien_thoai'])): ?><div class="invalid-feedback" style="display:block;"><?= $errors['so_dien_thoai'] ?></div><?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" class="form-control" name="email" required value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                            <?php if (!empty($errors['email'])): ?><div class="invalid-feedback" style="display:block;"><?= $errors['email'] ?></div><?php endif; ?>
                        </div>
                    </div>
                </div>
                <button type="submit" name="add_new_employee" class="btn btn-primary">Thêm nhân viên</button>
            </form>
            <?php if ($successScript): ?>
                <script>swal("Thành công","Đã thêm nhân viên mới", "success").then(() => { window.location.replace("<?= $successScript ?>"); });</script>
            <?php endif; ?>
        </div>
    </div>
</div>
