<?php
$employeesJson = json_encode(array_map(static function ($e) {
    return [
        'employee_id' => (int) $e['ma_nhan_vien'],
        'first_name'  => $e['ten'],
        'last_name'   => $e['ho_dem'],
    ];
}, $employees), JSON_UNESCAPED_UNICODE);
?>

<?php if ($upgradeRequired): ?>
<div class="container-fluid p-4">
    <div class="alert alert-warning">
        <strong>Chưa kích hoạt Thu ngân.</strong> Import <code>database/upgrade_salon_features.sql</code> và <code>database/upgrade_pos_ui.sql</code> trong phpMyAdmin.
    </div>
</div>
<?php else: ?>

<div class="pos-easy">
    <div class="pos-topbar">
        <h1>Thu ngân</h1>
        <div class="pos-time-wrap">
            <i class="far fa-calendar-alt"></i>
            <span>Chọn thời điểm</span>
            <input type="datetime-local" id="posDateTime">
        </div>
        <div class="pos-topbar-actions">
            <a href="<?= admin_route('pos/orders') ?>" class="pos-btn-outline">Danh sách đơn hàng</a>
            <button type="button" class="pos-btn-primary" id="posBtnNew">Tạo hóa đơn</button>
        </div>
    </div>

    <div class="pos-main">
        <div class="pos-invoice-panel">
            <div class="pos-tabs">
                <button type="button" class="pos-tab active" id="posTabLabel">Khách vãng lai</button>
            </div>

            <div class="pos-customer-bar">
                <div class="pos-search-client">
                    <i class="fas fa-search"></i>
                    <input type="text" id="posClientSearch" placeholder="Tìm khách hàng theo tên, SĐT..." autocomplete="off">
                    <div class="pos-client-dropdown" id="posClientDropdown"></div>
                </div>
                <button type="button" class="pos-btn-add-client" id="posBtnAddClient">+ Thêm khách mới</button>
            </div>

            <div class="pos-cart-table-wrap">
                <table class="pos-cart-table">
                    <thead>
                        <tr>
                            <th>Tên</th>
                            <th>Số lượng</th>
                            <th>Đơn giá</th>
                            <th>Giảm giá</th>
                            <th>Thành tiền</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="posCartBody"></tbody>
                </table>
                <div class="pos-empty-cart" id="posEmptyCart">
                    <i class="fas fa-receipt d-block"></i>
                    <p>Chọn dịch vụ hoặc sản phẩm bên phải để thêm vào hóa đơn</p>
                </div>
            </div>

            <div class="pos-summary">
                <div class="pos-summary-row">
                    <label>Thành tiền</label>
                    <span class="pos-amount" id="posSubtotal">0</span>
                </div>
                <div class="pos-summary-row">
                    <label>Giảm giá hoá đơn</label>
                    <div class="pos-invoice-discount">
                        <input type="number" id="posInvDiscount" value="0" min="0">
                        <button type="button" class="pos-discount-toggle" id="posInvDiscountToggle">đ</button>
                    </div>
                </div>
                <div class="pos-temp-total">
                    Tạm tính: <span id="posGrandTotal">0</span> đ
                    <small id="posOrderCodeDisplay">(<?= htmlspecialchars($draftOrderCode) ?>)</small>
                </div>
            </div>

            <div class="pos-footer-actions">
                <button type="button" class="pos-btn-cancel" id="posBtnCancel">Hủy hóa đơn</button>
                <button type="button" class="pos-btn-action" id="posBtnAssignStaff">Xếp nhân viên</button>
                <button type="button" class="pos-btn-action" id="posBtnPrint">In hóa đơn</button>
                <button type="button" class="pos-btn-pay" id="posBtnPay">Thanh toán</button>
            </div>
        </div>

        <div class="pos-catalog-panel">
            <div class="pos-catalog-search">
                <input type="text" id="posCatalogSearch" placeholder="Tìm kiếm dịch vụ, sản phẩm hoặc ...">
            </div>
            <div class="pos-catalog-tabs">
                <button type="button" class="active" data-catalog-tab="services">Dịch vụ</button>
                <button type="button" data-catalog-tab="products">Sản phẩm</button>

            </div>
            <div class="pos-catalog-list">
                <div data-catalog-pane="services">
                    <?php foreach ($servicesGrouped as $catName => $items): ?>
                        <div class="pos-cat-group">
                            <div class="pos-cat-header">
                                <span><?= htmlspecialchars(strtoupper($catName)) ?></span>
                                <i class="fas fa-plus text-muted"></i>
                            </div>
                            <div class="pos-cat-items">
                                <?php foreach ($items as $s): ?>
                                    <div class="pos-cat-item"
                                         data-type="service"
                                         data-id="<?= (int) $s['ma_dich_vu'] ?>"
                                         data-name="<?= htmlspecialchars($s['ten_dich_vu']) ?>"
                                         data-price="<?= (float) $s['gia'] ?>">
                                        <span><?= htmlspecialchars($s['ten_dich_vu']) ?></span>
                                        <span class="price"><?= format_money($s['gia']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($servicesGrouped)): ?>
                        <p class="text-muted p-3 small">Chưa có dịch vụ. Thêm tại menu Dịch vụ.</p>
                    <?php endif; ?>
                </div>
                <div data-catalog-pane="products" style="display:none">
                    <?php if (!empty($products)): ?>
                        <div class="pos-cat-group open">
                            <div class="pos-cat-header"><span>SẢN PHẨM BÁN LẺ</span><i class="fas fa-plus text-muted"></i></div>
                            <div class="pos-cat-items" style="display:block">
                                <?php foreach ($products as $p): ?>
                                    <div class="pos-cat-item"
                                         data-type="product"
                                         data-id="<?= (int) $p['ma_san_pham'] ?>"
                                         data-name="<?= htmlspecialchars($p['ten_san_pham']) ?>"
                                         data-price="<?= (float) $p['gia_ban'] ?>">
                                        <span><?= htmlspecialchars($p['ten_san_pham']) ?> <small class="text-muted">(<?= $p['so_luong_ton'] ?>)</small></span>
                                        <span class="price"><?= format_money($p['gia_ban']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="text-muted p-3 small">Chưa có sản phẩm. Thêm tại Kho hàng.</p>
                    <?php endif; ?>
                </div>
               
            </div>
            
        </div>
    </div>
</div>

<!-- Xếp nhân viên -->
<div class="modal fade" id="posStaffModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-users mr-2"></i>Xếp nhân viên</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body py-2" id="posStaffCheckboxList">
                <!-- JS render danh sách checkbox -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary btn-sm" id="posStaffSave">
                    <i class="fas fa-check mr-1"></i>Áp dụng
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Thanh toán -->
<div class="modal fade" id="posPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Thanh toán</h5><button type="button" class="close" data-dismiss="modal">&times;</button></div>
            <div class="modal-body">
                <p class="mb-3">Tổng thanh toán: <strong id="posPayTotal">0 VND</strong></p>
                <div class="form-group">
                    <label class="d-block"><input type="radio" name="pos_payment_method" value="cash" checked> Tiền mặt</label>
                    <label class="d-block"><input type="radio" name="pos_payment_method" value="transfer_qr"> Chuyển khoản / VietQR</label>
                    <label class="d-block"><input type="radio" name="pos_payment_method" value="card"> Quẹt thẻ</label>
                    <label class="d-block"><input type="radio" name="pos_payment_method" value="prepaid"> Thẻ trả trước</label>
                </div>
                <div class="form-group">
                    <label>Ghi chú</label>
                    <input type="text" class="form-control" id="posPayNote" placeholder="Ghi chú hóa đơn">
                </div>
                <label class="d-block mt-2"><input type="checkbox" id="posAutoPrint" checked> In hóa đơn sau khi thanh toán</label>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-success" id="posConfirmPay">Xác nhận thanh toán</button>
            </div>
        </div>
    </div>
</div>

<script>
window.POS_CONFIG = {
    employees: <?= $employeesJson ?>,
    defaultEmployeeId: <?= (int) $defaultEmployeeId ?>,
    draftOrderCode: <?= json_encode($draftOrderCode) ?>,
    prefill: <?= json_encode($prefill ?? null, JSON_UNESCAPED_UNICODE) ?>
};
</script>

<?php endif; ?>
