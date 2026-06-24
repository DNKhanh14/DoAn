<div class="container-fluid hr-modern-page">
    <div class="page-title-bar">
        <h1>Quản lý lương</h1>
    </div>

    <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>

    <div class="card card-salon shadow">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap hr-toolbar">
            <div class="d-flex" style="gap:8px">
                <span class="btn btn-outline-primary btn-sm active">Quản lý lương</span>
            </div>
            <div class="d-flex flex-wrap" style="gap:8px">
                <form method="get" action="index.php" class="form-inline">
                    <input type="hidden" name="route" value="hr">
                    <input type="month" name="month" value="<?= htmlspecialchars($month) ?>" class="form-control form-control-sm" onchange="this.form.submit()">
                </form>
            </div>
        </div>
        <div class="card-body table-responsive">
            <?php if (hr_payroll_upgrade_required()): ?>
            <div class="alert alert-warning mb-3">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                <strong>Chưa import SQL.</strong> Vui lòng chạy file <code>database/upgrade_hr_payroll.sql</code> trong phpMyAdmin để bật đầy đủ tính năng lương (nghỉ phép, thanh toán...).
            </div>
            <?php endif; ?>
            <table class="table table-salon table-hover">
                <thead>
                    <tr>
                        <th>Tên nhân viên</th>
                        <th class="text-right">Hoa hồng</th>
                        <th class="text-right">Lương cơ bản</th>
                        <th class="text-center">Nghỉ có phép</th>
                        <th class="text-center">Nghỉ không phép</th>
                        <th class="text-right">Trừ nghỉ</th>
                        <th class="text-right">Lương tổng</th>
                        <th class="text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($employees)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">Chưa có nhân viên</td></tr>
                    <?php else: ?>
                        <?php
                        $hr = new \App\Models\Hr();
                        $upgrade = hr_payroll_upgrade_required();
                        foreach ($employees as $e):
                            $eid = (int) $e['ma_nhan_vien'];
                            $leave = $leaveSummary[$eid] ?? ['authorized_days' => 0, 'unauthorized_days' => 0];
                            $name = htmlspecialchars($e['ten'] . ' ' . $e['ho_dem']);

                            // Tính tóm tắt lương
                            $summary     = $upgrade ? [] : $hr->getEmployeeSummary($eid, $from, $to);
                            $commission  = $upgrade ? 0 : ($summary['commission']      ?? 0);
                            $baseSalary  = $upgrade ? 0 : ($summary['base_salary']     ?? 0);
                            $grossSalary = $upgrade ? 0 : ($summary['gross_salary']    ?? 0);
                            $leaveDeduct = $upgrade ? 0 : ($summary['leave_deduction'] ?? 0);
                            $authDays    = (float) ($leave['authorized_days']   ?? 0);
                            $unauthDays  = (float) ($leave['unauthorized_days'] ?? 0);
                        ?>
                        <tr>
                            <td>
                                <a href="<?= admin_route('hr/detail', ['employee_id' => $eid, 'month' => $month]) ?>" class="hr-employee-link"><?= $name ?></a>
                            </td>
                            <td class="text-right">
                                <?= $upgrade ? '<span class="text-muted">—</span>' : '<span class="' . ($commission > 0 ? 'text-success font-weight-bold' : '') . '">' . format_vnd($commission) . '</span>' ?>
                            </td>
                            <td class="text-right">
                                <?= $upgrade ? '<span class="text-muted">—</span>' : format_vnd($baseSalary) ?>
                            </td>
                            <td class="text-center">
                                <?php if ($authDays > 0): ?>
                                    <span class="badge badge-warning"><?= $authDays ?> ngày</span>
                                <?php else: ?>
                                    <span class="text-muted">0</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($unauthDays > 0): ?>
                                    <span class="badge badge-danger"><?= $unauthDays ?> ngày</span>
                                <?php else: ?>
                                    <span class="text-muted">0</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <?php if ($upgrade): ?>
                                    <span class="text-muted">—</span>
                                <?php elseif ($leaveDeduct > 0): ?>
                                    <span class="text-danger font-weight-bold">-<?= format_vnd($leaveDeduct) ?></span>
                                <?php else: ?>
                                    <span class="text-muted">0 VND</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <?= $upgrade ? '<span class="text-muted">—</span>' : '<span class="font-weight-bold">' . format_vnd($grossSalary) . '</span>' ?>
                            </td>
                            <td class="text-right hr-action-cell">
                                <button type="button" class="btn btn-link btn-sm hr-open-leave" data-employee-id="<?= $eid ?>">Nghỉ</button>
                                <button type="button" class="btn btn-link btn-sm hr-open-bonus" data-employee-id="<?= $eid ?>">Thưởng/phạt</button>
                                <button type="button" class="btn btn-link btn-sm hr-open-payment"
                                    data-employee-id="<?= $eid ?>"
                                    data-net="<?= $upgrade ? 0 : (int) ($summary['salary_balance'] ?? 0) ?>">Thanh toán</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/modals.php'; ?>

<script>
window.HR_CONFIG = {
    ajaxUrl: <?= json_encode(admin_route('ajax/hr')) ?>,
    month: <?= json_encode($month) ?>
};
</script>
<script src="Design/js/hr-payroll.js"></script>
