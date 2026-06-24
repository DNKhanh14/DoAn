<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Khách hàng</h1>
    </div>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách khách hàng</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID#</th>
                            <th>Họ</th>
                            <th>Tên</th>
                            <th>Số điện thoại</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clients as $client): ?>
                            <tr>
                                <td><?= (int) $client['ma_khach_hang'] ?></td>
                                <td><?= htmlspecialchars($client['ten']) ?></td>
                                <td><?= htmlspecialchars($client['ho_dem']) ?></td>
                                <td><?= htmlspecialchars($client['so_dien_thoai']) ?></td>
                                <td><?= htmlspecialchars($client['email']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
