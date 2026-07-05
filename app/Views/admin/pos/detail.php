<?php
$payLabels = ['cash' => 'Tiền mặt', 'transfer' => 'Chuyển khoản / VietQR', 'card' => 'Thẻ'];
$payColors = ['cash' => '#1cc88a', 'transfer' => '#1e5bb8', 'card' => '#6f42c1'];
$method     = $order['phuong_thuc_thanh_toan'] ?? 'cash';
$clientName = trim(($order['ten'] ?? '') . ' ' . ($order['ho_dem'] ?? '')) ?: 'Khách lẻ';
// Ưu tiên ma_don (HD000001), fallback sang số ID
$orderCode  = $order['ma_don'] ?? ('HD' . str_pad((string) $order['ma_don_hang'], 6, '0', STR_PAD_LEFT));
// backUrl: do ReportsController hoặc PosController truyền vào
$backHref   = $backUrl ?? admin_route('pos/orders');
$backLabel  = isset($backUrl) ? 'Quay lại báo cáo' : 'Danh sách đơn hàng';
?>

<div class="container-fluid">
    <div class="page-title-bar" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
        <a href="<?= $backHref ?>" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> <?= $backLabel ?>
        </a>
        <h1 style="margin:0;font-size:1.25rem">Chi tiết hóa đơn: <strong><?= htmlspecialchars($orderCode) ?></strong></h1>
        <div class="ml-auto d-flex" style="gap:8px">
            <a href="<?= admin_route('pos/print', ['id' => $order['ma_don_hang']]) ?>" target="_blank"
               class="btn btn-sm btn-primary">
                <i class="fas fa-print mr-1"></i> In hóa đơn
            </a>
        </div>
    </div>

    <div class="row mt-3">
        <!-- Cột trái: thông tin chung -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm" style="border-radius:10px;border:none">
                <div class="card-header" style="background:#f8fafc;border-radius:10px 10px 0 0;border-bottom:1px solid #e2e8f0;padding:14px 20px">
                    <span style="font-weight:600;font-size:0.95rem;color:#2d3748">
                        <i class="fas fa-info-circle mr-2 text-primary"></i>Thông tin hóa đơn
                    </span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0" style="font-size:0.88rem">
                        <tbody>
                            <tr>
                                <td class="pl-4 text-muted" style="width:44%;border-top:none">Mã hóa đơn</td>
                                <td class="pr-4 font-weight-bold" style="border-top:none"><?= htmlspecialchars($orderCode) ?></td>
                            </tr>
                            <tr>
                                <td class="pl-4 text-muted">Thời gian</td>
                                <td class="pr-4"><?= date('H:i - d/m/Y', strtotime($order['ngay_tao'])) ?></td>
                            </tr>
                            <tr>
                                <td class="pl-4 text-muted">Khách hàng</td>
                                <td class="pr-4">
                                    <i class="fas fa-user mr-1 text-muted" style="font-size:0.75rem"></i>
                                    <?= htmlspecialchars($clientName) ?>
                                    <?php if (!empty($order['so_dien_thoai'])): ?>
                                        <div style="font-size:0.78rem;color:#718096"><?= htmlspecialchars($order['so_dien_thoai']) ?></div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                           
                            <tr>
                                <td class="pl-4 text-muted">Thanh toán</td>
                                <td class="pr-4">
                                    <span class="badge text-white" style="background:<?= $payColors[$method] ?? '#6c757d' ?>;font-size:0.78rem;padding:4px 9px;border-radius:20px">
                                        <?= htmlspecialchars($payLabels[$method] ?? $method) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php if (!empty($order['ghi_chu'])): ?>
                            <tr>
                                <td class="pl-4 text-muted">Ghi chú</td>
                                <td class="pr-4"><?= htmlspecialchars($order['ghi_chu']) ?></td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tóm tắt tiền -->
            <div class="card shadow-sm mt-3" style="border-radius:10px;border:none">
                <div class="card-header" style="background:#f8fafc;border-radius:10px 10px 0 0;border-bottom:1px solid #e2e8f0;padding:14px 20px">
                    <span style="font-weight:600;font-size:0.95rem;color:#2d3748">
                        <i class="fas fa-receipt mr-2 text-primary"></i>Tóm tắt thanh toán
                    </span>
                </div>
                <div class="card-body" style="font-size:0.9rem">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Tạm tính</span>
                        <span><?= format_money($order['tong_truoc_giam'] ?? $order['tong_cong']) ?></span>
                    </div>
                    <?php if (!empty($order['giam_gia']) && $order['giam_gia'] > 0): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Giảm giá</span>
                        <span style="color:#e53e3e">- <?= format_money($order['giam_gia']) ?></span>
                    </div>
                    <?php endif; ?>
                    <hr style="margin:8px 0">
                    <div class="d-flex justify-content-between">
                        <span style="font-weight:700;font-size:1rem">Tổng cộng</span>
                        <span style="font-weight:700;font-size:1.1rem;color:#1cc88a"><?= format_money($order['tong_cong']) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột phải: chi tiết dịch vụ / sản phẩm -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm" style="border-radius:10px;border:none">
                <div class="card-header" style="background:#f8fafc;border-radius:10px 10px 0 0;border-bottom:1px solid #e2e8f0;padding:14px 20px">
                    <span style="font-weight:600;font-size:0.95rem;color:#2d3748">
                        <i class="fas fa-list-ul mr-2 text-primary"></i>Dịch vụ &amp; Sản phẩm
                    </span>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($items)): ?>
                        <div class="text-center text-muted py-4" style="font-size:0.88rem">Không có chi tiết</div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="font-size:0.88rem">
                            <thead style="background:#f8fafc">
                                <tr>
                                    <th class="pl-4 border-top-0" style="font-weight:600;color:#718096;font-size:0.78rem;text-transform:uppercase">#</th>
                                    <th class="border-top-0" style="font-weight:600;color:#718096;font-size:0.78rem;text-transform:uppercase">Tên</th>
                                    <th class="text-center border-top-0" style="font-weight:600;color:#718096;font-size:0.78rem;text-transform:uppercase">SL</th>
                                    <th class="text-right border-top-0" style="font-weight:600;color:#718096;font-size:0.78rem;text-transform:uppercase">Đơn giá</th>
                                    <th class="text-right border-top-0" style="font-weight:600;color:#718096;font-size:0.78rem;text-transform:uppercase">Giảm</th>
                                    <th class="text-right pr-4 border-top-0" style="font-weight:600;color:#718096;font-size:0.78rem;text-transform:uppercase">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($items as $i => $it): ?>
                                <tr>
                                    <td class="pl-4 text-muted align-middle"><?= $i + 1 ?></td>
                                    <td class="align-middle">
                                        <div style="font-weight:500"><?= htmlspecialchars($it['ten']) ?></div>
                                        <?php
                                        $empName = '';
                                        if (!empty($it['emp_fname'])) {
                                            $empName = trim($it['emp_fname'] . ' ' . ($it['emp_lname'] ?? ''));
                                        }
                                        ?>
                                        <?php if ($empName): ?>
                                            <div style="font-size:0.78rem;color:#718096">
                                                <i class="fas fa-user-tie mr-1" style="font-size:0.7rem"></i>NV: <?= htmlspecialchars($empName) ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php
                                        // Xác định loại từ cột ma_dich_vu/ma_san_pham (không có cột 'loai' trong DB)
                                        $isService = !empty($it['ma_dich_vu']);
                                        ?>
                                        <span class="badge" style="font-size:0.68rem;background:<?= $isService ? '#ebf4ff' : '#f0fdf4' ?>;color:<?= $isService ? '#1e5bb8' : '#16a34a' ?>;border-radius:4px;padding:2px 6px">
                                            <?= $isService ? 'Dịch vụ' : 'Sản phẩm' ?>
                                        </span>
                                    </td>
                                    <td class="text-center align-middle"><?= (int) $it['so_luong'] ?></td>
                                    <td class="text-right align-middle"><?= format_money($it['don_gia']) ?></td>
                                    <td class="text-right align-middle">
                                        <?php $disc = $it['giam_gia_dong'] ?? 0; ?>
                                        <?= $disc > 0 ? '<span style="color:#e53e3e">- ' . format_money($disc) . '</span>' : '—' ?>
                                    </td>
                                    <td class="text-right pr-4 align-middle font-weight-bold"><?= format_money($it['tong_dong']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
