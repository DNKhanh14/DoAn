<div class="container-fluid hr-modern-page">
    <div class="page-title-bar">
        <h1>Quản lý lương</h1>
        <form method="get" action="index.php" class="d-flex align-items-center" style="gap:8px">
            <input type="hidden" name="route" value="hr">
            <input type="month" name="month" value="<?= htmlspecialchars($month) ?>"
                   class="form-control form-control-sm"
                   style="border-radius:8px; height:36px; border:1px solid #e2e8f0; width:160px"
                   onchange="this.form.submit()">
            <button type="submit" class="btn btn-sm btn-primary" style="border-radius:8px; height:36px; padding:0 14px">
                <i class="fas fa-search mr-1"></i>Xem
            </button>
        </form>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success" style="border-radius:10px; border-left:4px solid #1cc88a">
            <i class="fas fa-check-circle mr-1"></i><?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="d-flex mb-4" style="gap:8px; flex-wrap:wrap">
        <div class="stat-pill">
            <i class="fas fa-calendar-alt mr-1" style="color:#1e5bb8"></i>
            Kỳ lương: <strong><?= date('m/Y', strtotime($month . '-01')) ?></strong>
        </div>
        <div class="stat-pill">
            <i class="fas fa-users mr-1"></i>
            <strong><?= count($employees) ?></strong> nhân viên
        </div>
    </div>

    <?php if (empty($employees)): ?>
        <div class="card shadow-sm" style="border-radius:10px; overflow:hidden; border:none">
            <div class="card-body text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3" style="opacity:0.3"></i>
                <p class="text-muted">Chưa có nhân viên nào.</p>
            </div>
        </div>
    <?php else: ?>
        <?php
        $colors = ['#1e5bb8','#e74a3b','#1cc88a','#f6c23e','#6f42c1','#fd7e14','#20c9a6'];
        $hr     = new \App\Models\Hr();
        $upgrade = hr_payroll_upgrade_required();
        $empIdx  = 0;
        ?>
        <div class="card shadow-sm" style="border-radius:12px; overflow:hidden; border:none">
            <div class="table-responsive">
                <table class="table mb-0" style="font-size:0.85rem; border-collapse:collapse; min-width:900px">
                    <thead>
                        <tr style="background:#f8fafc; border-bottom:2px solid #e2e8f0">
                            <th style="padding:10px 16px; color:#6b7280; font-size:0.72rem; text-transform:uppercase; letter-spacing:.5px; font-weight:600; white-space:nowrap; width:200px">Nhân viên</th>
                            <th style="padding:10px 12px; color:#6b7280; font-size:0.72rem; text-transform:uppercase; letter-spacing:.5px; font-weight:600; text-align:right; white-space:nowrap">Lương CB</th>
                            <th style="padding:10px 12px; color:#1cc88a; font-size:0.72rem; text-transform:uppercase; letter-spacing:.5px; font-weight:600; text-align:right; white-space:nowrap">Hoa hồng</th>
                            <th style="padding:10px 12px; color:#f6a623; font-size:0.72rem; text-transform:uppercase; letter-spacing:.5px; font-weight:600; text-align:right; white-space:nowrap">Thưởng</th>
                            <th style="padding:10px 12px; color:#e74a3b; font-size:0.72rem; text-transform:uppercase; letter-spacing:.5px; font-weight:600; text-align:right; white-space:nowrap">Phạt</th>
                            <th style="padding:10px 12px; color:#6b7280; font-size:0.72rem; text-transform:uppercase; letter-spacing:.5px; font-weight:600; text-align:center; white-space:nowrap">Nghỉ CP</th>
                            <th style="padding:10px 12px; color:#6b7280; font-size:0.72rem; text-transform:uppercase; letter-spacing:.5px; font-weight:600; text-align:center; white-space:nowrap">Nghỉ KP</th>
                            <th style="padding:10px 12px; color:#e74a3b; font-size:0.72rem; text-transform:uppercase; letter-spacing:.5px; font-weight:600; text-align:right; white-space:nowrap">Trừ nghỉ</th>
                            <th style="padding:10px 12px; color:#1e5bb8; font-size:0.72rem; text-transform:uppercase; letter-spacing:.5px; font-weight:700; text-align:right; white-space:nowrap">Lương tổng</th>
                            <th style="padding:10px 16px; color:#6b7280; font-size:0.72rem; text-transform:uppercase; letter-spacing:.5px; font-weight:600; text-align:right; white-space:nowrap">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($employees as $e):
                        $eid = (int) $e['ma_nhan_vien'];
                        $leave       = $leaveSummary[$eid] ?? ['authorized_days' => 0, 'unauthorized_days' => 0];
                        $empName     = htmlspecialchars($e['ten'] . ' ' . $e['ho_dem']);
                        $summary     = $upgrade ? [] : $hr->getEmployeeSummary($eid, $from, $to);
                        $commission  = (float) ($summary['commission']      ?? 0);
                        $baseSalary  = (float) ($summary['base_salary']     ?? 0);
                        $grossSalary = (float) ($summary['gross_salary']    ?? 0);
                        $leaveDeduct = (float) ($summary['leave_deduction'] ?? 0);
                        $bonusTotal  = (float) ($summary['bonus_total']     ?? 0);
                        $penaltyTotal= (float) ($summary['penalty_total']   ?? 0);
                        $authDays    = (float) ($leave['authorized_days']   ?? 0);
                        $unauthDays  = (float) ($leave['unauthorized_days'] ?? 0);
                        $color       = $colors[$empIdx % count($colors)];
                        $empIdx++;
                    ?>
                    <tr class="hr-row" style="border-bottom:1px solid #f0f4ff">
                        <!-- Nhân viên -->
                        <td style="padding:12px 16px; white-space:nowrap">
                            <div style="display:flex; align-items:center; gap:10px">
                                <div style="width:36px; height:36px; border-radius:50%; background:<?= $color ?>1a; color:<?= $color ?>; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-weight:700; font-size:0.9rem">
                                    <?= strtoupper(mb_substr($e['ten'], 0, 1)) ?>
                                </div>
                                <div>
                                    <a href="<?= admin_route('hr/detail', ['employee_id' => $eid, 'month' => $month]) ?>"
                                       style="color:#2d3748; font-weight:600; text-decoration:none; font-size:0.88rem">
                                        <?= $empName ?>
                                    </a>
                                    <?php if (!empty($e['chuc_vu'])): ?>
                                        <div style="font-size:0.72rem; color:#9ca3af"><?= htmlspecialchars($e['chuc_vu']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>

                        <!-- Lương cơ bản -->
                        <td style="padding:12px; text-align:right; color:#4a5568; white-space:nowrap">
                            <?= $upgrade ? '<span class="text-muted">—</span>' : format_vnd($baseSalary) ?>
                        </td>

                        <!-- Hoa hồng -->
                        <td style="padding:12px; text-align:right; white-space:nowrap">
                            <?php if ($upgrade): ?>
                                <span class="text-muted">—</span>
                            <?php elseif ($commission > 0): ?>
                                <span style="color:#1cc88a; font-weight:700"><?= format_vnd($commission) ?></span>
                            <?php else: ?>
                                <span style="color:#9ca3af">0 VND</span>
                            <?php endif; ?>
                        </td>

                        <!-- Thưởng -->
                        <td style="padding:12px; text-align:right; white-space:nowrap">
                            <?php if ($upgrade): ?>
                                <span class="text-muted">—</span>
                            <?php elseif ($bonusTotal > 0): ?>
                                <span style="color:#f6a623; font-weight:700">+<?= format_vnd($bonusTotal) ?></span>
                            <?php else: ?>
                                <span style="color:#9ca3af">—</span>
                            <?php endif; ?>
                        </td>

                        <!-- Phạt -->
                        <td style="padding:12px; text-align:right; white-space:nowrap">
                            <?php if ($upgrade): ?>
                                <span class="text-muted">—</span>
                            <?php elseif ($penaltyTotal > 0): ?>
                                <span style="color:#e74a3b; font-weight:700">-<?= format_vnd($penaltyTotal) ?></span>
                            <?php else: ?>
                                <span style="color:#9ca3af">—</span>
                            <?php endif; ?>
                        </td>

                        <!-- Nghỉ có phép -->
                        <td style="padding:12px; text-align:center; white-space:nowrap">
                            <?php if ($authDays > 0): ?>
                                <span class="badge text-white" style="background:#f6c23e; font-size:0.72rem; padding:3px 8px; border-radius:20px"><?= $authDays ?>n</span>
                            <?php else: ?>
                                <span style="color:#9ca3af">0</span>
                            <?php endif; ?>
                        </td>

                        <!-- Nghỉ không phép -->
                        <td style="padding:12px; text-align:center; white-space:nowrap">
                            <?php if ($unauthDays > 0): ?>
                                <span class="badge text-white" style="background:#e74a3b; font-size:0.72rem; padding:3px 8px; border-radius:20px"><?= $unauthDays ?>n</span>
                            <?php else: ?>
                                <span style="color:#9ca3af">0</span>
                            <?php endif; ?>
                        </td>

                        <!-- Trừ nghỉ -->
                        <td style="padding:12px; text-align:right; white-space:nowrap">
                            <?php if ($upgrade): ?>
                                <span class="text-muted">—</span>
                            <?php elseif ($leaveDeduct > 0): ?>
                                <span style="color:#e74a3b; font-weight:600">-<?= format_vnd($leaveDeduct) ?></span>
                            <?php else: ?>
                                <span style="color:#9ca3af">—</span>
                            <?php endif; ?>
                        </td>

                        <!-- Lương tổng -->
                        <td style="padding:12px; text-align:right; white-space:nowrap">
                            <?php if ($upgrade): ?>
                                <span class="text-muted">—</span>
                            <?php else: ?>
                                <span style="font-size:0.95rem; font-weight:700; color:<?= $color ?>"><?= format_vnd($grossSalary) ?></span>
                            <?php endif; ?>
                        </td>

                        <!-- Thao tác -->
                        <td style="padding:12px 16px; text-align:right; white-space:nowrap">
                            <div style="display:inline-flex; gap:5px; align-items:center">
                                <button type="button" class="btn btn-sm hr-open-leave" data-employee-id="<?= $eid ?>"
                                        style="border-radius:7px; border:1px solid #e2e8f0; color:#4a5568; background:#fff; padding:3px 9px; font-size:0.75rem">
                                    <i class="fas fa-umbrella-beach mr-1" style="font-size:0.65rem"></i>Nghỉ
                                </button>
                                <button type="button" class="btn btn-sm hr-open-bonus" data-employee-id="<?= $eid ?>"
                                        style="border-radius:7px; border:1px solid #f6c23e; color:#856404; background:#fffbeb; padding:3px 9px; font-size:0.75rem">
                                    <i class="fas fa-gift mr-1" style="font-size:0.65rem"></i>Thưởng
                                </button>
                                <button type="button" class="btn btn-sm hr-open-payment"
                                        data-employee-id="<?= $eid ?>"
                                        data-net="<?= $upgrade ? 0 : (int)($summary['net_salary'] ?? 0) ?>"
                                        data-remaining="<?= $upgrade ? 0 : (int)($summary['salary_balance'] ?? 0) ?>"
                                        style="border-radius:7px; border:1px solid #1e5bb8; color:#1e5bb8; background:#eff6ff; padding:3px 9px; font-size:0.75rem">
                                    <i class="fas fa-money-bill-wave mr-1" style="font-size:0.65rem"></i>Trả lương
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$modalEmployeeId = 0;
$modalNetSalary  = 0;
require __DIR__ . '/modals.php';
?>

<style>
.stat-pill { background:#fff; border:1px solid #e2e8f0; border-radius:20px; padding:5px 14px; font-size:0.85rem; color:#4a5568; display:inline-block; }
.hr-row:hover td { background:#f8fafc; }
</style>

<script>
window.HR_CONFIG = {
    ajaxUrl: <?= json_encode(admin_route('ajax/hr')) ?>,
    month: <?= json_encode($month) ?>,
    netSalary: 0,
    salaryBalance: 0
};
</script>
<script src="Design/js/hr-payroll.js"></script>
