<?php
$eid = (int) $employee['ma_nhan_vien'];
$empName = htmlspecialchars($employee['ten'] . ' ' . $employee['ho_dem']);
$s = $summary ?? [
    'total_leave_days' => 0, 'authorized_leave' => 0, 'unauthorized_leave' => 0,
    'bonus_total' => 0, 'penalty_total' => 0, 'advance_total' => 0,
    'revenue_bonus_total' => 0, 'salary_balance' => 0, 'commission' => 0,
    'working_days' => 0, 'days_in_period' => 30, 'base_salary' => 0,
    'daily_salary' => 0, 'net_salary' => 0,
];
$periodLabel = date('d/m/Y', strtotime($from)) . ' - ' . date('d/m/Y', strtotime($to));
$tabs = [
    'leave'      => 'Ngày nghỉ',
    'bonus'      => 'Thưởng/phạt',
    'payment'    => 'Thanh toán',
    'commission' => 'Hoa hồng',
];
?>

<div class="container-fluid hr-detail-page">
    <div class="page-title-bar">
        <h1>Quản lý lương</h1>
        <a href="<?= admin_route('hr', ['month' => $month]) ?>" class="btn btn-outline-secondary btn-sm">← Danh sách</a>
    </div>

    <?php if (hr_payroll_upgrade_required()): ?>
        <div class="alert alert-warning">Import <code>database/upgrade_hr_payroll.sql</code> để dùng đầy đủ tính năng.</div>
    <?php endif; ?>

    <div class="card card-salon shadow hr-summary-card">
        <div class="card-body">
            <div class="hr-detail-head">
                <div class="hr-detail-head-left">
                    <form method="get" action="index.php" class="d-flex flex-wrap align-items-center" style="gap:8px">
                        <input type="hidden" name="route" value="hr/detail">
                        <input type="hidden" name="tab" value="<?= htmlspecialchars($tab) ?>">
                        <select name="employee_id" class="form-control form-control-sm hr-employee-select" onchange="this.form.submit()">
                            <?php foreach ($employees as $e): ?>
                                <option value="<?= (int) $e['ma_nhan_vien'] ?>" <?= $eid === (int) $e['ma_nhan_vien'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars(strtoupper($e['ten'] . ' ' . $e['ho_dem'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="text-muted small">Kỳ lương: <strong><?= $periodLabel ?></strong></span>
                        <input type="month" name="month" value="<?= htmlspecialchars($month) ?>" class="form-control form-control-sm" style="width:auto" onchange="this.form.submit()">
                    </form>
                </div>
                <div class="hr-detail-actions">
                    <button type="button" class="btn btn-outline-primary btn-sm hr-open-leave" data-employee-id="<?= $eid ?>">Nghỉ</button>
                    <button type="button" class="btn btn-outline-primary btn-sm hr-open-bonus" data-employee-id="<?= $eid ?>">Thêm thưởng phạt</button>
                    <button type="button" class="btn btn-outline-primary btn-sm hr-open-payment" data-employee-id="<?= $eid ?>" data-net="<?= (int) $s['net_salary'] ?>" data-remaining="<?= (int) $s['salary_balance'] ?>">Thanh toán</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm hr-open-settings" data-employee-id="<?= $eid ?>" data-base-salary="<?= (int) $payrollSettings['base_salary'] ?>"><i class="fas fa-cog mr-1"></i> Cài đặt</button>
                </div>
            </div>

            <div class="hr-summary-grid">
                <div class="hr-summary-col">
                    <div>Số ngày nghỉ: <strong><?= (float) $s['total_leave_days'] ?></strong></div>
                    <div>Số tiền thưởng: <strong><?= format_vnd($s['bonus_total']) ?></strong></div>
                    <div>Tạm ứng: <strong><?= format_vnd($s['advance_total']) ?></strong></div>
                </div>
                <div class="hr-summary-col">
                    <div>Nghỉ có phép: <strong><?= (float) $s['authorized_leave'] ?></strong> <small class="text-muted">(-<?= format_vnd((float)$s['authorized_leave'] * 20000) ?>)</small></div>
                    <div>Số tiền phạt: <strong><?= format_vnd($s['penalty_total']) ?></strong></div>
                    <div>Lương tồn: <strong><?= format_vnd($s['salary_balance']) ?></strong></div>
                </div>
                <div class="hr-summary-col">
                    <div>Nghỉ không phép: <strong><?= (float) $s['unauthorized_leave'] ?></strong> <small class="text-muted">(-<?= format_vnd((float)$s['unauthorized_leave'] * 50000) ?>)</small></div>
                    <div>Hoa hồng: <strong><?= format_vnd($s['commission']) ?></strong></div>
                    <div>Trừ nghỉ: <strong class="text-danger">-<?= format_vnd($s['leave_deduction'] ?? 0) ?></strong></div>
                </div>
                <div class="hr-summary-totals">
                    <div class="hr-total-item">
                        <span>Ngày công</span>
                        <strong><?= (int) $s['working_days'] ?>/<?= (int) $s['days_in_period'] ?></strong>
                    </div>
                    <div class="hr-total-item">
                        <span>Lương cơ bản</span>
                        <strong><?= format_vnd($s['base_salary']) ?></strong>
                    </div>
                    <div class="hr-total-item">
                        <span>Lương ngày</span>
                        <strong><?= format_vnd($s['daily_salary']) ?></strong>
                    </div>
                    <div class="hr-total-item hr-net-salary">
                        <span>Lương thực lĩnh</span>
                        <strong><?= format_vnd($s['net_salary']) ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-salon shadow mt-3">
        <div class="card-body">
            <div class="hr-tab-bar">
                <?php foreach ($tabs as $key => $label): ?>
                    <a href="<?= admin_route('hr/detail', ['employee_id' => $eid, 'month' => $month, 'tab' => $key]) ?>"
                       class="hr-tab-link<?= $tab === $key ? ' active' : '' ?>"><?= $label ?></a>
                <?php endforeach; ?>
            </div>

            <div class="table-responsive mt-3">
                <?php if ($tab === 'leave'): ?>
                    <table class="table table-salon table-hover">
                        <thead>
                            <tr>
                                <th>Ngày nghỉ</th>
                                <th>Lí do nghỉ</th>
                                <th>Trạng thái</th>
                                <th class="text-center">Số ngày</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($leaveRecords)): ?>
                                <tr><td colspan="4" class="text-center py-5 text-muted"><i class="fas fa-inbox fa-2x d-block mb-2"></i>Trống</td></tr>
                            <?php else: ?>
                                <?php foreach ($leaveRecords as $row): ?>
                                <tr>
                                    <?php
                                        $tuNgay    = $row['tu_ngay']      ?? '';
                                        $denNgay   = $row['den_ngay']     ?? '';
                                        $soNgay    = $row['so_ngay_nghi'] ?? 0;
                                        $ghiChu    = $row['ghi_chu']      ?? '';
                                        $trangThai = $row['trang_thai']   ?? 'co_phep';
                                    ?>
                                    <td><?= $tuNgay ? date('d/m/Y', strtotime($tuNgay)) : '' ?><?= ($denNgay && $denNgay !== $tuNgay) ? ' ~ ' . date('d/m/Y', strtotime($denNgay)) : '' ?></td>
                                    <td><?= htmlspecialchars($ghiChu) ?></td>
                                    <td>
                                        <?php if ($trangThai === 'co_phep'): ?>
                                            <span class="badge badge-success">Có phép</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Không phép</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?= (float) $soNgay ?> ngày</td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                <?php elseif ($tab === 'bonus'): ?>
                    <table class="table table-salon table-hover">
                        <thead>
                            <tr>
                                <th>Ngày</th>
                                <th>Loại</th>
                                <th>Danh mục</th>
                                <th class="text-right">Số tiền</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($bonusPenaltyRecords)): ?>
                                <tr><td colspan="5" class="text-center py-5 text-muted"><i class="fas fa-inbox fa-2x d-block mb-2"></i>Trống</td></tr>
                            <?php else: ?>
                                <?php foreach ($bonusPenaltyRecords as $row): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($row['ngay_ghi'])) ?></td>
                                    <td><?= ($row['loai_ghi'] ?? '') === 'penalty' ? 'Phạt' : 'Thưởng' ?></td>
                                    <td><?= htmlspecialchars($row['danh_muc'] ?? '') ?></td>
                                    <td class="text-right"><?= format_vnd($row['so_tien'] ?? 0) ?></td>
                                    <td><?= htmlspecialchars($row['ghi_chu'] ?? '') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                <?php elseif ($tab === 'payment'): ?>
                    <table class="table table-salon table-hover">
                        <thead>
                            <tr>
                                <th>Ngày</th>
                                <th>Loại</th>
                                <th>Phương thức</th>
                                <th class="text-right">Số tiền</th>
                                <th>Ghi chú</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($paymentRecords)): ?>
                                <tr><td colspan="5" class="text-center py-5 text-muted"><i class="fas fa-inbox fa-2x d-block mb-2"></i>Trống</td></tr>
                            <?php else: ?>
                                <?php foreach ($paymentRecords as $row): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($row['ngay_thanh_toan'])) ?></td>
                                    <td><?= htmlspecialchars(\App\Models\Hr::paymentTypeLabel($row['loai_thanh_toan'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars(\App\Models\Hr::paymentMethodLabel($row['phuong_thuc'] ?? '')) ?></td>
                                    <td class="text-right"><?= format_vnd($row['so_tien'] ?? 0) ?></td>
                                    <td><?= htmlspecialchars($row['ghi_chu'] ?? '') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                <?php elseif ($tab === 'commission'): ?>
                    <table class="table table-salon table-hover">
                        <thead>
                            <tr>
                                <th>Ngày</th>
                                <th>Hóa đơn</th>
                                <th>Dịch vụ</th>
                                <th class="text-right">Doanh thu</th>
                                <th class="text-right">Hoa hồng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($commissionRecords)): ?>
                                <tr><td colspan="5" class="text-center py-5 text-muted"><i class="fas fa-inbox fa-2x d-block mb-2"></i>Trống — cần gán nhân viên khi thanh toán ở Thu ngân</td></tr>
                            <?php else: ?>
                                <?php foreach ($commissionRecords as $row):
                                    $orderId  = (int) ($row['ma_don_hang'] ?? 0);
                                    $code     = $row['ma_don'] ?? ('HD' . str_pad((string) $orderId, 6, '0', STR_PAD_LEFT));
                                ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($row['ngay_tao'])) ?></td>
                                    <td>
                                        <a class="easy-link" href="<?= admin_route('pos/detail', ['id' => $orderId]) ?>">
                                            <?= htmlspecialchars($code) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($row['ten'] ?? '') ?></td>
                                    <td class="text-right"><?= format_vnd($row['tong_dong'] ?? 0) ?></td>
                                    <td class="text-right font-weight-bold" style="color:#1cc88a"><?= format_vnd($row['commission'] ?? 0) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>

            <?php
            $recordCount = match ($tab) {
                'leave'      => count($leaveRecords),
                'bonus'      => count($bonusPenaltyRecords),
                'payment'    => count($paymentRecords),
                'commission' => count($commissionRecords),
                default      => 0,
            };
            ?>
            <div class="hr-table-footer text-muted small">
                Hiển thị từ <?= $recordCount > 0 ? 1 : 0 ?> đến <?= $recordCount ?> trên tổng số <?= $recordCount ?>
            </div>
        </div>
    </div>
</div>

<?php
$modalEmployeeId = $eid;
$modalNetSalary = $s['net_salary'];
require __DIR__ . '/modals.php';
?>

<script>
window.HR_CONFIG = {
    ajaxUrl: <?= json_encode(admin_route('ajax/hr')) ?>,
    month: <?= json_encode($month) ?>,
    netSalary: <?= (int) $s['net_salary'] ?>,
    salaryBalance: <?= (int) $s['salary_balance'] ?>
};
</script>
<script src="Design/js/hr-payroll.js"></script>
