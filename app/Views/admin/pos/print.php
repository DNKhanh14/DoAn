<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>In hóa đơn <?= htmlspecialchars($order['ma_don'] ?? ('HD' . str_pad((string) $order['ma_don_hang'], 6, '0', STR_PAD_LEFT))) ?></title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 320px; margin: 20px auto; font-size: 13px; }
        h2 { text-align: center; margin: 0 0 8px; }
        table { width: 100%; border-collapse: collapse; margin: 12px 0; }
        td { padding: 4px 0; }
        .total { font-weight: bold; font-size: 15px; border-top: 1px dashed #000; padding-top: 8px; }
        @media print { button { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <h2>BARBER SALON</h2>
    <?php $printCode = $order['ma_don'] ?? ('HD' . str_pad((string) $order['ma_don_hang'], 6, '0', STR_PAD_LEFT)); ?>
    <p style="text-align:center;margin:0">Hóa đơn: <?= htmlspecialchars($printCode) ?></p>
    <p style="text-align:center;margin:4px 0 12px"><?= htmlspecialchars($order['ngay_tao']) ?></p>
    <p>Khách: <?= htmlspecialchars(trim(($order['ten'] ?? '') . ' ' . ($order['ho_dem'] ?? '')) ?: 'Khách lẻ') ?></p>
    <table>
        <?php foreach ($items as $it): ?>
            <tr>
                <td><?= htmlspecialchars($it['ten']) ?> x<?= (int) $it['so_luong'] ?></td>
                <td style="text-align:right"><?= format_money($it['tong_dong']) ?></td>
            </tr>
            <?php if (!empty($it['emp_fname'])): ?>
                <tr><td colspan="2" style="font-size:11px;color:#666">NV: <?= htmlspecialchars($it['emp_fname'] . ' ' . $it['emp_lname']) ?></td></tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </table>
    <p>Giảm giá: <?= format_money($order['giam_gia']) ?></p>
    <p class="total">Tổng: <?= format_money($order['tong_cong']) ?></p>
    <p>PTTT: <?= htmlspecialchars($order['phuong_thuc_thanh_toan']) ?></p>
    <p style="text-align:center;margin-top:16px">Cảm ơn quý khách!</p>
    <button onclick="window.print()">In lại</button>
</body>
</html>
