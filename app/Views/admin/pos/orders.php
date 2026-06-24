<div class="container-fluid">
    <div class="page-title-bar">
        <h1>Danh sách đơn hàng</h1>
        <a href="<?= admin_route('pos') ?>" class="btn-them-moi"><i class="fas fa-cash-register mr-1"></i> Về Thu ngân</a>
    </div>
    <div class="card card-salon shadow">
        <div class="card-body table-responsive">
            <table class="table table-salon table-bordered">
                <thead>
                    <tr>
                        <th class="text-center" style="width:50px">STT</th>
                        <th>Mã HĐ</th>
                        <th>Khách</th>
                        <th>Tổng tiền</th>
                        <th>Thanh toán</th>
                        <th>Thời gian</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr><td colspan="7" class="text-center text-muted">Chưa có đơn hàng.</td></tr>
                    <?php else: ?>
                        <?php foreach ($orders as $idx => $o): ?>
                            <tr>
                                <td class="text-center text-muted small"><?= $idx + 1 ?></td>
                                <td><?= htmlspecialchars($o['ma_don'] ?? ('#' . $o['ma_don_hang'])) ?></td>
                                <td><?= htmlspecialchars(trim(($o['ten'] ?? '') . ' ' . ($o['ho_dem'] ?? '')) ?: 'Khách lẻ') ?></td>
                                <td><?= format_money($o['tong_cong']) ?></td>
                                <td><?= htmlspecialchars(payment_method_label($o['phuong_thuc_thanh_toan'] ?? '')) ?></td>
                                <td><small><?= date('d/m/Y H:i', strtotime($o['ngay_tao'])) ?></small></td>
                                <td>
                                    <a href="<?= admin_route('pos/print', ['id' => $o['ma_don_hang']]) ?>" target="_blank" class="btn btn-sm btn-primary">In</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
