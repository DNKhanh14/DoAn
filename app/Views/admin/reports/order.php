<?php
$cust = trim(($order['ten'] ?? '') . ' ' . ($order['ho_dem'] ?? ''));
if ($cust === '') {
    $cust = 'Khách vãng lai';
}
$subtotal = (float) ($order['thanh_tien'] ?? 0);
$discount = (float) ($order['giam_gia'] ?? 0);
$total = (float) ($order['tong_cong'] ?? 0);
$backUrl = admin_route('reports', ['tab' => $tab, 'from' => $from, 'to' => $to]);
?>

<div class="container-fluid order-detail-page">
    <div class="order-detail-head">
        <a href="<?= htmlspecialchars($backUrl) ?>" class="order-detail-back"><i class="fas fa-arrow-left"></i></a>
        <h1>Chi tiết hoá đơn #<?= htmlspecialchars($orderCode) ?></h1>
    </div>

    <div class="order-detail-meta">
        <span><i class="fas fa-user mr-1"></i> <?= htmlspecialchars($cust) ?></span>
        <span><i class="far fa-clock mr-1"></i> <?= htmlspecialchars(date('H:i d/m/Y', strtotime($order['ngay_tao']))) ?></span>
        <span class="badge badge-success">Đã thanh toán</span>
    </div>

    <div class="easy-panel order-detail-panel">
        <div class="table-responsive">
            <table class="table easy-table order-items-table">
                <thead>
                    <tr>
                        <th>Loại</th>
                        <th>Tên</th>
                        <th>Nhân viên</th>
                        <th class="text-right">Đơn giá</th>
                        <th class="text-right">Số lượng</th>
                        <th class="text-right">Giảm giá</th>
                        <th class="text-right">Tổng tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $it):
                        $empName = trim(($it['emp_fname'] ?? '') . ' ' . ($it['emp_lname'] ?? ''));
                        $lineDisc = (float) ($it['line_discount'] ?? 0);
                        $typeLabel = ($it['item_type'] ?? '') === 'product' ? 'Sản phẩm' : 'Dịch vụ';
                    ?>
                    <tr>
                        <td><?= $typeLabel ?></td>
                        <td><strong><?= htmlspecialchars(strtoupper($it['ten'])) ?></strong></td>
                        <td>
                            <?php if ($empName !== ''): ?>
                                <span class="order-staff-name">• <?= strtoupper(htmlspecialchars($empName)) ?></span>
                                <?php if (!empty($it['commission'])): ?>
                                    <span class="order-staff-comm">: <?= format_vnd($it['commission']) ?></span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-right"><?= format_vnd($it['don_gia']) ?></td>
                        <td class="text-right"><?= (int) $it['so_luong'] ?></td>
                        <td class="text-right"><?= format_vnd($lineDisc) ?></td>
                        <td class="text-right"><?= format_vnd($it['tong_dong']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="order-detail-bottom">
            <div class="order-payment-box">
                <div class="order-payment-title">Phương thức thanh toán:</div>
                <div><?= htmlspecialchars(payment_method_label($order['phuong_thuc_thanh_toan'] ?? 'cash')) ?> : <?= format_vnd($total) ?></div>
            </div>
            <div class="order-totals">
                <div class="order-total-row"><span>Thành tiền</span><strong><?= format_vnd($subtotal) ?></strong></div>
                <?php if ($discount > 0): ?>
                <div class="order-total-row"><span>Giảm giá</span><strong><?= format_vnd($discount) ?></strong></div>
                <?php endif; ?>
                <div class="order-total-row"><span>Cần thanh toán</span><strong><?= format_vnd($total) ?></strong></div>
                <div class="order-total-row order-paid"><span>Đã thanh toán</span><strong><?= format_vnd($total) ?></strong></div>
                <div class="order-paid-time"><?= htmlspecialchars(date('H:i d/m/Y', strtotime($order['ngay_tao']))) ?></div>
            </div>
        </div>
    </div>

    <div class="order-detail-actions">
        <div class="order-actions-left">
            <a href="<?= admin_route('pos/print', ['id' => $order['ma_don_hang']]) ?>" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="fas fa-print mr-1"></i> In hóa đơn</a>
        </div>
    </div>
</div>
