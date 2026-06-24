<div class="container-fluid">
    <div class="page-title-bar">
        <h1>Danh sách nhân viên</h1>
        <a href="<?= admin_route('employees', ['do' => 'Add']) ?>" class="btn-them-moi"><i class="fas fa-plus mr-1"></i> Thêm mới</a>
    </div>
    <div class="card card-salon shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-salon table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:50px">STT</th>
                            <th>Họ</th>
                            <th>Tên</th>
                            <th>Chức vụ</th>
                            <th>Số điện thoại</th>
                            <th>Email</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employees as $i => $employee): ?>
                            <tr>
                                <td class="text-center text-muted small"><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($employee['ten']) ?></td>
                                <td><?= htmlspecialchars($employee['ho_dem']) ?></td>
                                <td><?= htmlspecialchars($employee['chuc_vu'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($employee['so_dien_thoai']) ?></td>
                                <td><?= htmlspecialchars($employee['email']) ?></td>
                                <td>
                                    <?php $deleteData = 'delete_employee_' . $employee['ma_nhan_vien']; ?>
                                    <button class="btn btn-warning btn-sm">
                                        <a href="<?= admin_route('employees', ['do' => 'Edit', 'ma_nhan_vien' => $employee['ma_nhan_vien']]) ?>" style="color:white"><i class="fa fa-edit"></i></a>
                                    </button>
                                    <button class="btn btn-danger btn-sm" type="button" data-toggle="modal" data-target="#<?= $deleteData ?>"><i class="fa fa-trash"></i></button>
                                    <div class="modal fade" id="<?= $deleteData ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Xóa nhân viên</h5>
                                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                </div>
                                                <div class="modal-body">Bạn có chắc muốn xóa nhân viên này?</div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                                                    <form method="post" action="index.php" class="d-inline">
                                                        <input type="hidden" name="route" value="employees">
                                                        <input type="hidden" name="delete_employee" value="1">
                                                        <input type="hidden" name="ma_nhan_vien" value="<?= (int) $employee['ma_nhan_vien'] ?>">
                                                        <button type="submit" class="btn btn-danger">Xóa</button>
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
