<?php
$cust     = trim(($order['ten'] ?? '') . ' ' . ($order['ho_dem'] ?? ''));
if ($cust === '') $cust = 'Khách vãng lai';
$subtotal = (float)($order['thanh_tien'] ?? 0);
$discount = (float)($order['giam_gia']   ?? 0);
$total    = (float)($order['tong_cong']  ?? 0);
$backUrl  = admin_route('reports', ['tab' => $tab, 'from' => $from, 'to' => $to]);
$payMethod = payment_method_label($order['phuong_thuc_thanh_toan'] ?? 'cash');
$payColors = [
    'Tiền mặt'      => ['#1cc88a', 'fa-money-bill-wave'],
    'Chuyển khoản'  => ['#1e5bb8', 'fa-university'],
    'Thẻ'           => ['#6f42c1', 'fa-credit-card'],
];
[$payColor, $payIcon] = $payColors[$payMethod] ?? ['#6c757d', 'fa-money-bill'];
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="page-title-bar">
        <div class="d-flex align-items-center" style="gap:12px">
            <a href="<?= htmlspecialchars($backUrl) ?>"
               class="btn btn-outline-secondary btn-sm" style="border-radius:8px">
                <i class="fas fa-arrow-left mr-1"></i>Quay lại
            </a>
            <h1>Chi tiết hoá đơn
                <span style="color:#6b7280; font-size:0.9rem">#<?= htmlspecialchars($orderCode) ?></span>
            </h1>
        </div>
        <div class="d-flex align-items-center" style="gap:8px">
            <span class="badge text-white" style="background:#1cc88a; font-size:0.82rem; padding:6px 14px; border-radius:20px">
                <i class="fas fa-check-circle mr-1"></i>Đã thanh toán
            </span>
            <a href="<?= admin_route('pos/print', ['id' => $order['ma_don_hang']]) ?>" target="_blank"
               class="btn btn-sm btn-outline-secondary" style="border-radius:8px">
                <i class="fas fa-print mr-1"></i>In hóa đơn
            </a>
        </div>
    </div>

    <!-- Meta info -->
    <div class="d-flex flex-wrap mb-4" style="gap:10px">
        <div class="stat-pill">
            <i class="fas fa-user mr-1" style="color:#1e5bb8"></i><?= htmlspecialchars($cust) ?>
        </div>
        <div class="stat-pill">
            <i class="far fa-clock mr-1" style="color:#6b7280"></i><?= date('H:i d/m/Y', strtotime($order['ngay_tao'])) ?>
        </div>
        <div class="stat-pill" style="color:<?= $payColor ?>; border-color:<?= $payColor ?>3a">
            <i class="fas <?= $payIcon ?> mr-1"></i><?= htmlspecialchars($payMethod) ?>
        </div>
    </div>

    <!-- Bảng items -->
    <div class="card shadow-sm mb-4" style="border-radius:12px; overflow:hidden; border:none">
        <div class="card-header py-3 px-4" style="background:#fff; border-bottom:1px solid #e2e8f0">
            <div class="d-flex align-items-center" style="gap:10px">
                <div style="width:36px; height:36px; border-radius:8px; background:#1e5bb81a; color:#1e5bb8; display:flex; align-items:center; justify-content:center">
                    <i class="fas fa-list-ul"></i>
                </div>
                <span class="font-weight-bold" style="color:#2d3748">Danh sách dịch vụ / sản phẩm</span>
            </div>
        </div>
        <div class="card-body p-0">
            <!-- Header -->
            <div class="d-flex px-4 py-2 border-bottom"
                 style="background:#f8fafc; font-size:0.72rem; color:#6b7280; text-transform:uppercase; letter-spacing:0.5px; font-weight:600">
                <div style="min-width:70px">Loại</div>
                <div class="flex-grow-1 mr-3">Tên</div>
                <div style="min-width:150px">Nhân viên</div>
                <div class="text-right" style="min-width:90px">Đơn giá</div>
                <div class="text-right" style="min-width:60px">SL</div>
                <div class="text-right" style="min-width:80px">Giảm giá</div>
                <div class="text-right" style="min-width:100px">Thành tiền</div>
            </div>

            <?php
            // Nhóm các dòng cùng loại + cùng ref_id lại để hiện 2 NV trên 1 hàng
            $grouped = [];
            foreach ($items as $it) {
                $key = ($it['ma_dich_vu'] ? 'service_' . $it['ma_dich_vu'] : 'product_' . ($it['ma_san_pham'] ?? 0));
                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'item'      => $it,
                        'employees' => [],
                        'total'     => 0,
                    ];
                }
                $empName = trim(($it['emp_fname'] ?? '') . ' ' . ($it['emp_lname'] ?? ''));
                if ($empName !== '') $grouped[$key]['employees'][] = mb_strtoupper($empName, 'UTF-8');
                $grouped[$key]['total'] += (float)($it['tong_dong']);
            }
            $groupedItems = array_values($grouped);
            ?>
            <?php foreach ($groupedItems as $idx => $g):
                $it       = $g['item'];
                $empNames = array_unique($g['employees']);
                $rowTotal = $g['total'];
                $lineDisc = (float)($it['line_discount'] ?? 0);
                // Xác định loại từ ma_dich_vu/ma_san_pham (không có cột 'loai' trong DB)
                $isService = !empty($it['ma_dich_vu']);
                $typeColor = $isService ? '#1e5bb8' : '#fd7e14';
                $typeLabel = $isService ? 'Dịch vụ' : 'Sản phẩm';
                $typeIcon  = $isService ? 'fa-cut' : 'fa-box';
            ?>
            <div class="d-flex align-items-center px-4 py-3 <?= $idx < count($groupedItems)-1 ? 'border-bottom' : '' ?>"
                 style="transition:background .15s; font-size:0.88rem"
                 onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                <!-- Loại -->
                <div style="min-width:70px">
                    <span class="badge text-white" style="background:<?= $typeColor ?>; font-size:0.7rem; padding:3px 8px; border-radius:20px">
                        <i class="fas <?= $typeIcon ?> mr-1" style="font-size:0.6rem"></i><?= $typeLabel ?>
                    </span>
                </div>
                <!-- Tên -->
                <div class="flex-grow-1 mr-3">
                    <div style="font-weight:700; color:#2d3748"><?= htmlspecialchars(mb_strtoupper($it['ten'], 'UTF-8')) ?></div>
                </div>
                <!-- Nhân viên (có thể nhiều) -->
                <div style="min-width:150px">
                    <?php if (!empty($empNames)): ?>
                        <div style="font-size:0.82rem; color:#4a5568; font-weight:600">
                            <i class="fas fa-user-tie mr-1" style="font-size:0.7rem; color:<?= $typeColor ?>"></i>
                            <?= htmlspecialchars(implode(' &amp; ', $empNames)) ?>
                        </div>
                    <?php else: ?>
                        <span class="text-muted small">—</span>
                    <?php endif; ?>
                </div>
                <!-- Đơn giá -->
                <div class="text-right" style="min-width:90px; color:#4a5568">
                    <?= format_vnd($it['don_gia'] * count($empNames ?: [1])) ?>
                </div>
                <!-- SL -->
                <div class="text-right" style="min-width:60px; font-weight:600; color:#2d3748">
                    <?= (int)$it['so_luong'] ?>
                </div>
                <!-- Giảm giá -->
                <div class="text-right" style="min-width:80px; color:#e74a3b">
                    <?= $lineDisc > 0 ? '-' . format_vnd($lineDisc * count($empNames ?: [1])) : '<span class="text-muted">—</span>' ?>
                </div>
                <!-- Thành tiền -->
                <div class="text-right" style="min-width:100px; font-weight:700; color:<?= $typeColor ?>">
                    <?= format_vnd($rowTotal) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Tổng kết -->
    <div class="row justify-content-end">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-sm" style="border-radius:12px; overflow:hidden; border:none">
                <div class="card-body" style="padding:20px 24px">
                    <div class="d-flex justify-content-between mb-2" style="font-size:0.9rem; color:#4a5568">
                        <span>Thành tiền</span>
                        <strong><?= format_vnd($subtotal) ?></strong>
                    </div>
                    <?php if ($discount > 0): ?>
                    <div class="d-flex justify-content-between mb-2" style="font-size:0.9rem; color:#e74a3b">
                        <span>Giảm giá</span>
                        <strong>-<?= format_vnd($discount) ?></strong>
                    </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-between mb-2 pt-2 border-top" style="font-size:0.9rem; color:#4a5568">
                        <span>Cần thanh toán</span>
                        <strong><?= format_vnd($total) ?></strong>
                    </div>
                    <!-- Thanh toán -->
                    <div class="d-flex justify-content-between align-items-center pt-2 mt-1 border-top">
                        <div>
                            <div style="font-size:0.72rem; color:#9ca3af; text-transform:uppercase; letter-spacing:0.5px">Đã thanh toán</div>
                            <div style="font-size:0.8rem; color:<?= $payColor ?>; font-weight:600; margin-top:2px">
                                <i class="fas <?= $payIcon ?> mr-1" style="font-size:0.7rem"></i><?= htmlspecialchars($payMethod) ?>
                            </div>
                        </div>
                        <strong style="font-size:1.2rem; color:#1cc88a"><?= format_vnd($total) ?></strong>
                    </div>
                    <div class="text-muted text-right mt-1" style="font-size:0.75rem">
                        <?= date('H:i d/m/Y', strtotime($order['ngay_tao'])) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-pill {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 5px 14px;
    font-size: 0.85rem;
    color: #4a5568;
    display: inline-flex;
    align-items: center;
}
</style>
