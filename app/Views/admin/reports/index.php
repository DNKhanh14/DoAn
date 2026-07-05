<?php
$tabs = [
    'revenue' => 'DOANH THU',
    'staff' => 'NHÂN VIÊN',
    'customers' => 'KHÁCH HÀNG',
    'tax' => 'THỐNG KÊ THUẾ',
    'cashflow' => 'THU CHI',
];
$summary = $invoiceSummary ?? ['count' => 0, 'revenue' => 0, 'paid' => 0, 'debt' => 0];
$reportBackParams = ['from' => $from, 'to' => $to];
if (!empty($cfType ?? '')) {
    $reportBackParams['cf_type'] = $cfType;
}
if (!empty($cfPayment ?? '')) {
    $reportBackParams['cf_payment'] = $cfPayment;
}
$cashflowRows = $cashflowRows ?? [];
$cfType = $cfType ?? '';
$cfPayment = $cfPayment ?? '';
?>

<div class="container-fluid reports-easy-page">
    <div class="easy-page-head">
        <h1>Báo cáo</h1>
        <form class="reports-date-form" method="get" action="index.php">
            <input type="hidden" name="route" value="reports">
            <input type="hidden" name="tab" value="<?= htmlspecialchars($tab) ?>">
            <input type="date" name="from" class="form-control form-control-sm" value="<?= htmlspecialchars($from) ?>">
            <span class="text-muted mx-1">~</span>
            <input type="date" name="to" class="form-control form-control-sm" value="<?= htmlspecialchars($to) ?>">
            <button type="submit" class="btn btn-outline-secondary btn-sm"><i class="far fa-calendar-alt"></i></button>
        </form>
    </div>

    <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
    <?php if (salon_upgrade_required()): ?>
        
    <?php endif; ?>

    <ul class="reports-tabs">
        <?php foreach ($tabs as $key => $label): ?>
            <li class="<?= $tab === $key ? 'active' : '' ?>">
                <a href="<?= admin_route('reports', array_merge($reportBackParams, ['tab' => $key])) ?>"><?= $label ?></a>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php if ($tab === 'revenue'): ?>
        <div class="reports-toolbar">

            <div class="reports-toolbar-actions">
         </div>
        </div>

        <div class="reports-summary-row">
            <div class="reports-stat-card">
                <div class="label">SỐ LƯỢNG HOÁ ĐƠN</div>
                <div class="value"><?= (int) $summary['count'] ?></div>
            </div>
            <div class="reports-stat-card">
                <div class="label">DOANH THU HOÁ ĐƠN</div>
                <div class="value"><?= format_vnd($summary['revenue']) ?></div>
            </div>
            <div class="reports-stat-card">
                <div class="label">ĐÃ THANH TOÁN</div>
                <div class="value"><?= format_vnd($summary['paid']) ?></div>
            </div>
            <div class="reports-stat-card">
                <div class="label">NỢ</div>
                <div class="value"><?= format_vnd($summary['debt']) ?></div>
            </div>
        </div>

        <div class="easy-panel">
            <div class="table-responsive">
                <table class="table easy-table">
                    <thead>
                        <tr>
                            <th>Mã hóa đơn</th>
                            <th>Ngày tạo</th>
                            <th>Khách hàng</th>
                            <th class="text-right">Tổng tiền</th>
                            <th class="text-right">Giảm giá</th>
                            <th class="text-right">Phụ thu</th>
                            <th class="text-right">Thuế VAT</th>
                            <th class="text-right">Đã thanh toán</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr><td colspan="8" class="text-center text-muted py-4">Chưa có hóa đơn trong kỳ</td></tr>
                        <?php else: ?>
                            <?php foreach ($orders as $o):
                                $cust = trim(($o['ten'] ?? '') . ' ' . ($o['ho_dem'] ?? ''));
                                if ($cust === '') {
                                    $cust = 'Khách lẻ';
                                }
                                $code = '#' . $o['ma_don_hang'];
                                $subtotal = (float) ($o['subtotal'] ?? $o['tong_cong']);
                                $discount = (float) ($o['giam_gia'] ?? 0);
                                $paid = (float) $o['tong_cong'];
                            ?>
                            <tr>
                                <td><a class="easy-link" href="<?= admin_route('reports/order', array_merge($reportBackParams, ['tab' => 'revenue', 'id' => $o['ma_don_hang']])) ?>"><?= htmlspecialchars($code) ?></a></td>
                                <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($o['ngay_tao']))) ?></td>
                                <td><?= htmlspecialchars($cust) ?></td>
                                <td class="text-right"><?= format_vnd($subtotal) ?></td>
                                <td class="text-right"><?= format_vnd($discount) ?></td>
                                <td class="text-right">0</td>
                                <td class="text-right">0</td>
                                <td class="text-right"><?= format_vnd($paid) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="reports-table-footer">Hiển thị từ <?= count($orders) > 0 ? 1 : 0 ?> đến <?= count($orders) ?> trên tổng số <?= count($orders) ?></div>
        </div>

    <?php elseif ($tab === 'staff'): ?>
        <div class="reports-toolbar">

        </div>

        <div class="easy-panel">
            <div class="table-responsive">
                <table class="table easy-table">
                    <thead>
                        <tr>
                           
                            <th>Tên nhân viên</th>
                            <th class="text-right">Doanh thu dịch vụ</th>
                            <th class="text-right">Hoa hồng ước tính</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($staffCommissions)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">Chưa có dữ liệu nhân viên</td></tr>
                        <?php else: ?>
                            <?php foreach ($staffCommissions as $row):
                                $name   = htmlspecialchars(trim($row['ten'] . ' ' . $row['ho_dem']));
                                $comm   = (float) ($row['commission_est'] ?? 0);
                                $svcRev = (float) ($row['service_revenue'] ?? 0);
                                $month  = substr($from, 0, 7);
                            ?>
                            <tr>
                                
                                <td>
                                    <a class="easy-link" href="<?= admin_route('hr/detail', ['employee_id' => $row['ma_nhan_vien'], 'month' => $month, 'tab' => 'commission']) ?>">
                                        <?= mb_strtoupper($name, 'UTF-8') ?>
                                    </a>
                                </td>
                                <td class="text-right"><?= format_vnd($svcRev) ?></td>
                                <td class="text-right <?= $comm > 0 ? 'text-success font-weight-bold' : 'text-muted' ?>">
                                    <?= $comm > 0 ? format_vnd($comm) : '—' ?>
                                </td>
                                <td class="text-right">
                                    <a class="easy-link small" href="<?= admin_route('hr/detail', ['employee_id' => $row['ma_nhan_vien'], 'month' => $month, 'tab' => 'commission']) ?>">
                                        Xem chi tiết
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="reports-total-row">
                                <td colspan="2"><strong>Tổng cộng</strong></td>
                                <td class="text-right"><strong><?= format_vnd(array_sum(array_column($staffCommissions, 'service_revenue'))) ?></strong></td>
                                <td class="text-right"><strong><?= format_vnd($staffTotal ?? 0) ?></strong></td>
                                <td></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if (!empty($staffCommissions) && array_sum(array_column($staffCommissions, 'commission_est')) == 0): ?>
                <p class="text-muted small px-3 pb-3 mb-0">
                    <i class="fas fa-info-circle mr-1"></i>
                    Hoa hồng bằng 0 do chưa thiết lập tỉ lệ hoa hồng hoặc chưa gán nhân viên vào hóa đơn.
                    <a class="easy-link" href="<?= admin_route('employees/commission') ?>">Thiết lập hoa hồng</a>
                </p>
            <?php endif; ?>
        </div>

    <?php elseif ($tab === 'customers'): ?>
        <div class="easy-panel">
            <div class="table-responsive">
                <table class="table easy-table">
                    <thead>
                        <tr>
                            <th>Khách hàng</th>
                            <th>Số điện thoại</th>
                            <th class="text-right">Số đơn</th>
                            <th class="text-right">Tổng chi tiêu</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($topCustomers)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">Chưa có khách phát sinh đơn trong kỳ</td></tr>
                        <?php else: ?>
                            <?php foreach ($topCustomers as $c): ?>
                            <tr>
                                <td><?= htmlspecialchars($c['ten'] . ' ' . $c['ho_dem']) ?></td>
                                <td><?= htmlspecialchars($c['so_dien_thoai'] ?? '') ?></td>
                                <td class="text-right"><?= (int) $c['order_count'] ?></td>
                                <td class="text-right"><?= format_vnd($c['total_spent']) ?></td>
                                <td><a class="easy-link small" href="<?= admin_route('crm/detail', ['id' => $c['ma_khach_hang']]) ?>">Xem</a></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php elseif ($tab === 'tax'): ?>
        <?php
        $taxRows = $taxStats['rows'] ?? [];
        $taxTotal = (float) ($taxStats['total_tax'] ?? 0);
        $taxRevenue = (float) ($taxStats['total_revenue'] ?? 0);
        $taxOrders = (int) ($taxStats['total_orders'] ?? 0);
        $periodLabel = match ($taxGroupBy ?? 'day') {
            'month' => 'tháng',
            'year' => 'năm',
            default => 'ngày',
        };
        ?>
        <form class="reports-toolbar" method="get" action="index.php">
            <input type="hidden" name="route" value="reports">
            <input type="hidden" name="tab" value="tax">
            <input type="hidden" name="from" value="<?= htmlspecialchars($from) ?>">
            <input type="hidden" name="to" value="<?= htmlspecialchars($to) ?>">
            <select name="tax_group" class="form-control form-control-sm reports-filter-select" onchange="this.form.submit()">
                <option value="day" <?= ($taxGroupBy ?? 'day') === 'day' ? 'selected' : '' ?>>Theo ngày</option>
                <option value="month" <?= ($taxGroupBy ?? '') === 'month' ? 'selected' : '' ?>>Theo tháng</option>
                <option value="year" <?= ($taxGroupBy ?? '') === 'year' ? 'selected' : '' ?>>Theo năm</option>
            </select>
            <div class="d-flex align-items-center" style="gap:6px">
                <label class="small text-muted mb-0">VAT (%):</label>
                <input type="number" name="tax_rate" class="form-control form-control-sm" style="width:70px" value="<?= (float) ($taxRate ?? 10) ?>" min="0" max="100" step="0.1">
            </div>
            <button type="submit" class="btn btn-outline-primary btn-sm">Áp dụng</button>
        </form>

        <div class="reports-summary-row">
            <div class="reports-stat-card">
                <div class="label">TỔNG DOANH THU (ĐÃ GỒM VAT)</div>
                <div class="value"><?= format_vnd($taxRevenue) ?></div>
            </div>
            <div class="reports-stat-card reports-stat-card-tax">
                <div class="label">TỔNG THUẾ CỬA HÀNG</div>
                <div class="value"><?= format_vnd($taxTotal) ?></div>
            </div>
            <div class="reports-stat-card">
                <div class="label">SỐ HÓA ĐƠN</div>
                <div class="value"><?= $taxOrders ?></div>
            </div>
            <div class="reports-stat-card">
                <div class="label">THUẾ SUẤT ÁP DỤNG</div>
                <div class="value"><?= (float) ($taxRate ?? 10) ?>%</div>
            </div>
        </div>

        <div class="easy-panel">
            <div class="table-responsive">
                <table class="table easy-table">
                    <thead>
                        <tr>
                            <th>Kỳ (<?= $periodLabel ?>)</th>
                            <th class="text-right">Số HĐ</th>
                            <th class="text-right">Doanh thu</th>
                            <th class="text-right">Thuế VAT</th>
                            <th class="text-right">Doanh thu trước thuế</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($taxRows)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-4">Chưa có dữ liệu thuế trong kỳ</td></tr>
                        <?php else: ?>
                            <?php foreach ($taxRows as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars(\App\Models\Report::formatTaxPeriod($row['period'], $taxGroupBy ?? 'day')) ?></td>
                                <td class="text-right"><?= (int) $row['order_count'] ?></td>
                                <td class="text-right"><?= format_vnd($row['revenue']) ?></td>
                                <td class="text-right text-primary font-weight-bold"><?= format_vnd($row['tax_amount']) ?></td>
                                <td class="text-right"><?= format_vnd($row['net_revenue']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="reports-total-row">
                                <td><strong>Tổng cộng</strong></td>
                                <td class="text-right"><strong><?= $taxOrders ?></strong></td>
                                <td class="text-right"><strong><?= format_vnd($taxRevenue) ?></strong></td>
                                <td class="text-right"><strong><?= format_vnd($taxTotal) ?></strong></td>
                                <td class="text-right"><strong><?= format_vnd($taxRevenue - $taxTotal) ?></strong></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <p class="text-muted small px-3 pb-3 mb-0">* Thuế tính trên doanh thu hóa đơn đã thanh toán (giá đã bao gồm VAT). Công thức: Thuế = Doanh thu × <?= (float) ($taxRate ?? 10) ?> ÷ (100 + <?= (float) ($taxRate ?? 10) ?>)</p>
        </div>

    <?php elseif ($tab === 'cashflow'): ?>
        <form method="get" action="index.php" class="reports-toolbar reports-cashflow-toolbar" id="cashflowFilterForm">
            <input type="hidden" name="route" value="reports">
            <input type="hidden" name="tab" value="cashflow">
            <input type="hidden" name="from" value="<?= htmlspecialchars($from) ?>">
            <input type="hidden" name="to" value="<?= htmlspecialchars($to) ?>">
            <select name="cf_type" class="form-control form-control-sm reports-filter-select" onchange="this.form.submit()">
                <option value="">Tất cả loại</option>
                <option value="THU" <?= $cfType === 'THU' ? 'selected' : '' ?>>Thu</option>
                <option value="CHI" <?= $cfType === 'CHI' ? 'selected' : '' ?>>Chi</option>
            </select>
            <select name="cf_payment" class="form-control form-control-sm reports-filter-select" onchange="this.form.submit()">
                <option value="">Phương thức thanh toán</option>
                <option value="cash" <?= $cfPayment === 'cash' ? 'selected' : '' ?>>Tiền mặt</option>
                <option value="transfer" <?= $cfPayment === 'transfer' ? 'selected' : '' ?>>Chuyển khoản</option>
                <option value="card" <?= $cfPayment === 'card' ? 'selected' : '' ?>>Thẻ</option>
                <option value="prepaid" <?= $cfPayment === 'prepaid' ? 'selected' : '' ?>>Thẻ trả trước</option>
            </select>
            <div class="reports-toolbar-actions">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalThemChiPhi">
                    <i class="fas fa-plus mr-1"></i> Thêm chi phí
                </button>
            </div>
        </form>

        <div class="reports-cashflow-formula">
            
            
            <div class="reports-stat-card reports-stat-card--income">
                <div class="label">TỔNG THU</div>
                <div class="value"><?= format_vnd($revenue ?? 0) ?></div>
            </div>
            <span class="reports-formula-op">−</span>
            <div class="reports-stat-card reports-stat-card--expense">
                <div class="label">TỔNG CHI</div>
                <div class="value"><?= format_vnd($expenses ?? 0) ?></div>
            </div>
            <span class="reports-formula-op">=</span>
            <div class="reports-stat-card reports-stat-card--profit">
                <div class="label">LỢI NHUẬN</div>
                <div class="value"><?= format_vnd($profit ?? 0) ?></div>
            </div>
        </div>

        <div class="easy-panel">
            <div class="table-responsive">
                <table class="table easy-table">
                    <thead>
                        <tr>
                            <th>Loại phiếu</th>
                            <th>Ngày</th>
                            <th>Mã phiếu</th>
                            <th>Người nhập/nộp</th>
                            <th>Danh mục</th>
                            <th>Lý do</th>
                            <th>Thanh toán bằng</th>
                            <th class="text-right">Số tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($cashflowRows)): ?>
                            <tr><td colspan="8" class="text-center text-muted py-4">Chưa có giao dịch trong kỳ</td></tr>
                        <?php else: ?>
                            <?php foreach ($cashflowRows as $row): ?>
                            <tr>
                                <td><span class="badge badge-<?= $row['is_income'] ? 'success' : 'danger' ?>"><?= $row['type'] ?></span></td>
                                <td><?= htmlspecialchars(date('H:i d/m/Y', strtotime($row['date']))) ?></td>
                                <td>
                                    <?php if ($row['is_income']): ?>
                                        <a class="easy-link" href="<?= admin_route('reports/order', array_merge($reportBackParams, ['tab' => 'cashflow', 'id' => $row['order_id']])) ?>"><?= htmlspecialchars($row['code']) ?></a>
                                    <?php else: ?>
                                        <?= htmlspecialchars($row['code']) ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['person']) ?></td>
                                <td><?= htmlspecialchars($row['category']) ?></td>
                                <td>
                                    <?= htmlspecialchars($row['reason']) ?>
                                    <?php if ($row['is_income']): ?>
                                        <br><a class="easy-link small" href="<?= admin_route('reports/order', array_merge($reportBackParams, ['tab' => 'cashflow', 'id' => $row['order_id']])) ?>">Xem hóa đơn</a>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['method']) ?></td>
                                <td class="text-right"><?= format_vnd($row['amount']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="modalThemChiPhi" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none">
                    <div class="modal-header" style="background:linear-gradient(135deg, #e74a3b, #f06048); border:none; padding:20px 24px">
                        <div class="d-flex align-items-center">
                            <div style="width:36px; height:36px; border-radius:8px; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; margin-right:12px">
                                <i class="fas fa-file-invoice-dollar" style="color:#fff; font-size:15px"></i>
                            </div>
                            <h5 class="modal-title mb-0" style="color:#fff; font-weight:600">Thêm chi phí</h5>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:0.8; text-shadow:none"><span>&times;</span></button>
                    </div>
                    <form method="post" action="index.php">
                        <input type="hidden" name="route" value="reports">
                        <input type="hidden" name="add_expense" value="1">
                        <input type="hidden" name="from" value="<?= htmlspecialchars($from) ?>">
                        <input type="hidden" name="to" value="<?= htmlspecialchars($to) ?>">
                        <input type="hidden" name="cf_type" value="<?= htmlspecialchars($cfType) ?>">
                        <input type="hidden" name="cf_payment" value="<?= htmlspecialchars($cfPayment) ?>">
                        <div class="modal-body" style="padding:24px">
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Danh mục <span class="text-danger">*</span></label>
                                <input name="category" class="form-control mt-1" list="expenseCategoryList"
                                       placeholder="VD: Tiền điện, Lương..." required
                                       style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                                <datalist id="expenseCategoryList">
                                    <option value="Thanh toán lương">
                                    <option value="Tạm ứng lương">
                                    <option value="Tiền điện">
                                    <option value="Tiền thuê">
                                    <option value="Vật tư">
                                    <option value="Marketing">
                                </datalist>
                            </div>
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Số tiền <span class="text-danger">*</span></label>
                                <input name="amount" type="text" inputmode="numeric" class="form-control money-input mt-1"
                                       required placeholder="VD: 100.000"
                                       style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                            </div>
                            <div class="form-group">
                                <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Ngày <span class="text-danger">*</span></label>
                                <input name="expense_date" type="date" class="form-control mt-1"
                                       value="<?= date('Y-m-d') ?>" required
                                       style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                            </div>
                            <div class="form-group mb-0">
                                <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Ghi chú</label>
                                <input name="note" class="form-control mt-1" placeholder="Mô tả chi phí"
                                       style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                            </div>
                        </div>
                        <div class="modal-footer" style="padding:16px 24px; background:#f8fafc; border-top:1px solid #e2e8f0">
                            <button type="button" class="btn btn-light px-4" data-dismiss="modal" style="border-radius:8px">Huỷ</button>
                            <button type="submit" class="btn btn-danger px-4" style="border-radius:8px">
                                <i class="fas fa-plus mr-1"></i>Lưu chi phí
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        
    <?php endif; ?>
</div>
