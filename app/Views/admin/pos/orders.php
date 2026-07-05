<div class="container-fluid">
    <div class="page-title-bar">
        <h1>Danh sách đơn hàng</h1>
        <a href="<?= admin_route('pos') ?>" class="btn-them-moi">
            <i class="fas fa-cash-register mr-1"></i> Về Thu ngân
        </a>
    </div>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px;border-left:4px solid #1cc88a">
            <i class="fas fa-check-circle mr-1"></i> <?= htmlspecialchars($_SESSION['flash_success']) ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" style="border-radius:10px;border-left:4px solid #e53e3e">
            <i class="fas fa-exclamation-circle mr-1"></i> <?= htmlspecialchars($_SESSION['flash_error']) ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
    <?php endif; ?>

    <!-- Tổng quan nhanh -->
    <?php if (!empty($orders)): ?>
    <div class="row mb-4">
        <div class="col-auto">
            <div class="stat-pill">
                <i class="fas fa-file-invoice-dollar mr-1"></i>
                <strong><?= $pag['total'] ?? count($orders) ?></strong> đơn hàng
            </div>
        </div>
        <div class="col-auto">
            <div class="stat-pill">
                <i class="fas fa-money-bill-wave mr-1" style="color:#1cc88a"></i>
                <strong><?= format_money(array_sum(array_column($orders, 'tong_cong'))) ?></strong>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (empty($orders)): ?>
        <div class="card card-salon shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-3">Chưa có đơn hàng nào.</p>
                <a href="<?= admin_route('pos') ?>" class="btn btn-primary">
                    <i class="fas fa-cash-register mr-1"></i> Về Thu ngân
                </a>
            </div>
        </div>
    <?php else: ?>
        <?php
        $payColors = [
            'cash'     => ['#1cc88a', 'fa-money-bill-wave'],
            'transfer' => ['#1e5bb8', 'fa-university'],
            'card'     => ['#6f42c1', 'fa-credit-card'],
        ];
        ?>
        <div class="card shadow-sm" style="border-radius:10px; overflow:hidden; border:none">
            <div class="card-body p-0">
                <?php foreach ($orders as $idx => $o):
                    $method = $o['phuong_thuc_thanh_toan'] ?? 'cash';
                    [$pColor, $pIcon] = $payColors[$method] ?? ['#6c757d', 'fa-money-bill'];
                    $clientName = trim(($o['ten'] ?? '') . ' ' . ($o['ho_dem'] ?? '')) ?: 'Khách lẻ';
                    $orderCode  = '#' . $o['ma_don_hang'];
                    $orderId    = (int) $o['ma_don_hang'];
                ?>
                <div class="order-item d-flex align-items-center px-4 py-3 <?= $idx < count($orders) - 1 ? 'border-bottom' : '' ?>"
                     style="transition: background .15s;">

                    <!-- STT -->
                    <div class="mr-3 text-center font-weight-bold"
                         style="width:32px; height:32px; border-radius:50%; background:#f0f4ff; color:#1e5bb8; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-size:0.8rem">
                        <?= $idx + 1 ?>
                    </div>

                    <!-- Mã HĐ + Khách -->
                    <div class="flex-grow-1 mr-3">
                        <div class="font-weight-bold" style="font-size:0.95rem; color:#2d3748">
                            <a href="<?= admin_route('pos/detail', ['id' => $orderId]) ?>"
                               style="color:#1e5bb8;text-decoration:none"
                               title="Xem chi tiết hóa đơn">
                                <?= htmlspecialchars($orderCode) ?>
                            </a>
                        </div>
                        <div class="text-muted" style="font-size:0.82rem">
                            <i class="fas fa-user mr-1" style="font-size:0.7rem"></i><?= htmlspecialchars($clientName) ?>
                        </div>
                    </div>

                    <!-- Thanh toán -->
                    <div class="mr-4 text-center" style="min-width:120px">
                        <div style="font-size:0.7rem; color:#9ca3af; text-transform:uppercase; letter-spacing:0.5px">Thanh toán</div>
                        <span class="badge text-white mt-1" style="background:<?= $pColor ?>; font-size:0.75rem; padding:4px 8px; border-radius:20px">
                            <i class="fas <?= $pIcon ?> mr-1" style="font-size:0.65rem"></i><?= htmlspecialchars(payment_method_label($method)) ?>
                        </span>
                    </div>

                    <!-- Thời gian -->
                    <div class="mr-4 text-center" style="min-width:100px">
                        <div style="font-size:0.7rem; color:#9ca3af; text-transform:uppercase; letter-spacing:0.5px">Thời gian</div>
                        <div style="font-size:0.82rem; color:#4a5568; margin-top:2px">
                            <?= date('d/m/Y H:i', strtotime($o['ngay_tao'])) ?>
                        </div>
                    </div>

                    <!-- Tổng tiền -->
                    <div class="mr-4 text-center" style="min-width:110px">
                        <div style="font-size:0.7rem; color:#9ca3af; text-transform:uppercase; letter-spacing:0.5px">Tổng tiền</div>
                        <div style="font-size:1rem; font-weight:700; color:#1cc88a; margin-top:2px">
                            <?= format_money($o['tong_cong']) ?>
                        </div>
                    </div>

                    <!-- Thao tác -->
                    <div class="d-flex" style="flex-shrink:0; gap:6px">
                        <!-- In -->
                        <a href="<?= admin_route('pos/print', ['id' => $orderId]) ?>" target="_blank"
                           title="In hóa đơn"
                           style="width:34px; height:34px; border-radius:8px; background:#1e5bb8; border:none; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; text-decoration:none">
                            <i class="fas fa-print" style="color:#fff; font-size:14px"></i>
                        </a>
                        <!-- Xóa -->
                        <button type="button"
                                title="Xóa hóa đơn"
                                onclick="confirmDelete(<?= $orderId ?>, '<?= htmlspecialchars($orderCode, ENT_QUOTES) ?>')"
                                style="width:34px; height:34px; border-radius:8px; background:#e53e3e; border:none; display:inline-flex; align-items:center; justify-content:center; cursor:pointer">
                            <i class="fas fa-trash" style="color:#fff; font-size:13px"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($pag)): ?>
        <div class="px-1 mt-3"><?= render_pagination($pag, $baseUrl) ?></div>
    <?php endif; ?>
</div>

<!-- Modal xác nhận xóa -->
<div class="modal fade" id="modalDeleteOrder" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px">
        <div class="modal-content" style="border-radius:14px;border:none;overflow:hidden">
            <div class="modal-body text-center" style="padding:36px 32px 24px">
                <div style="width:60px;height:60px;border-radius:50%;background:#fff5f5;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
                    <i class="fas fa-trash-alt" style="font-size:24px;color:#e53e3e"></i>
                </div>
                <h5 style="font-weight:700;color:#1a202c;margin-bottom:8px">Xóa hóa đơn?</h5>
                <p class="text-muted" style="font-size:0.9rem;margin-bottom:4px">
                    Hóa đơn <strong id="deleteOrderCode"></strong> sẽ bị xóa vĩnh viễn.
                </p>
                <p class="text-muted small">Hành động này không thể hoàn tác.</p>
            </div>
            <div class="modal-footer justify-content-center border-0 pb-4" style="gap:10px">
                <button type="button" class="btn btn-outline-secondary px-4" data-dismiss="modal" style="border-radius:8px">
                    Hủy
                </button>
                <form id="deleteOrderForm" method="POST" action="<?= admin_route('pos/delete') ?>">
                    <input type="hidden" name="ma_don_hang" id="deleteOrderId">
                    <button type="submit" class="btn btn-danger px-4" style="border-radius:8px">
                        <i class="fas fa-trash mr-1"></i> Xóa hóa đơn
                    </button>
                </form>
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
    display: inline-block;
}
.order-item:hover { background: #f8fafc; }
@media (max-width: 768px) {
    .order-item .mr-4 { display: none; }
}
</style>

<script>
function confirmDelete(id, code) {
    document.getElementById('deleteOrderId').value  = id;
    document.getElementById('deleteOrderCode').textContent = code;
    $('#modalDeleteOrder').modal('show');
}
</script>
