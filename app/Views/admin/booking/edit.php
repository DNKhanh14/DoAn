<?php
$clientName = trim(($appointment['ten'] ?? '') . ' ' . ($appointment['ho_dem'] ?? ''));
$empName    = trim(($appointment['emp_fname'] ?? '') . ' ' . ($appointment['emp_lname'] ?? ''));
$badgeMap   = [
    'confirmed' => ['warning', '✅ Đã xác nhận'],
    'check_in'  => ['primary', '🔵 Check-in'],
    'check_out' => ['success', '✔ Check-out'],
];
[$badgeColor, $badgeLabel] = $badgeMap[$st] ?? ['secondary', $st];

$startDt = $appointment['thoi_gian_bat_dau'] ?? '';
$startDate = $startDt ? date('Y-m-d', strtotime($startDt)) : '';
$startTime = $startDt ? date('H:i', strtotime($startDt)) : '';
$endDt = $appointment['thoi_gian_ket_thuc_du_kien'] ?? '';
$endTime = $endDt ? date('H:i', strtotime($endDt)) : '';

$total = array_sum(array_map(fn($s) => (float) $s['gia'], $services));
?>

<div class="container-fluid">
    <div class="page-title-bar d-flex align-items-center justify-content-between flex-wrap" style="gap:8px">
        <div class="d-flex align-items-center" style="gap:10px">
            <a href="<?= admin_route('booking', ['date' => $startDate]) ?>"
               class="btn btn-outline-secondary btn-sm">← Danh sách</a>
            <h1 class="mb-0 h5">
                Sửa lịch hẹn
                <span class="text-muted">— <?= htmlspecialchars(appointment_display_code((int) $appointment['ma_lich_hen'])) ?></span>
            </h1>
        </div>
        <div class="d-flex align-items-center" style="gap:8px">
            <span class="badge badge-<?= $badgeColor ?> px-3 py-2"><?= $badgeLabel ?></span>
            <?php if ($isPaid): ?>
                <span class="badge badge-success px-3 py-2"><i class="fas fa-money-bill-wave mr-1"></i> Đã thanh toán</span>
            <?php elseif ($st === 'check_in'): ?>
                <a href="<?= admin_route('pos', ['appointment_id' => (int) $appointment['ma_lich_hen']]) ?>"
                   class="btn btn-success btn-sm">
                    <i class="fas fa-cash-register mr-1"></i> Thanh toán — <?= format_vnd($total) ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $message['type'] ?> py-2"><?= htmlspecialchars($message['text']) ?></div>
    <?php endif; ?>

    <?php if ($isLocked): ?>
        <div class="alert alert-light border py-2 small text-muted">
            <i class="fas fa-lock mr-1"></i> Lịch hẹn đã hoàn tất (Check-out / Đã thanh toán) — không thể chỉnh sửa.
        </div>
    <?php endif; ?>

    <form method="post" action="<?= admin_route('booking/edit', ['id' => (int) $appointment['ma_lich_hen']]) ?>">
        <input type="hidden" name="update_booking" value="1">

        <div class="row">
            <!-- Cột trái: thông tin chính -->
            <div class="col-lg-5 mb-4">
                <div class="card card-salon shadow-sm">
                    <div class="card-header py-2"><strong>Thông tin khách hàng</strong></div>
                    <div class="card-body pb-2">
                        <div class="mb-3">
                            <div class="font-weight-bold"><?= htmlspecialchars($clientName) ?></div>
                            <div class="text-muted small"><?= htmlspecialchars($appointment['so_dien_thoai'] ?? '') ?></div>
                        </div>
                    </div>
                </div>

                <div class="card card-salon shadow-sm mt-3">
                    <div class="card-header py-2"><strong>Thông tin lịch hẹn</strong></div>
                    <div class="card-body">
                        <!-- Ngày & Giờ -->
                        <div class="form-row">
                            <div class="form-group col-6">
                                <label class="small font-weight-bold">Ngày</label>
                                <input type="date" name="booking_date" id="editDate"
                                       class="form-control form-control-sm"
                                       value="<?= $startDate ?>"
                                       <?= $isLocked ? 'disabled' : '' ?>>
                            </div>
                            <div class="form-group col-6">
                                <label class="small font-weight-bold">Giờ bắt đầu</label>
                                <input type="time" name="booking_time" id="editTime"
                                       class="form-control form-control-sm"
                                       value="<?= $startTime ?>"
                                       <?= $isLocked ? 'disabled' : '' ?>>
                            </div>
                        </div>
                        <input type="hidden" name="start_time" id="editStartTime"
                               value="<?= htmlspecialchars($startDt) ?>">
                        <input type="hidden" name="end_time" id="editEndTime"
                               value="<?= htmlspecialchars($endDt) ?>">

                        <!-- Nhân viên -->
                        <div class="form-group">
                            <label class="small font-weight-bold">Nhân viên phụ trách</label>
                            <select name="ma_nhan_vien" class="form-control form-control-sm"
                                    <?= $isLocked ? 'disabled' : '' ?>>
                                <?php foreach ($employees as $e): ?>
                                    <option value="<?= (int) $e['ma_nhan_vien'] ?>"
                                        <?= $appointment['ma_nhan_vien'] == $e['ma_nhan_vien'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars(trim($e['ten'] . ' ' . $e['ho_dem'])) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Trạng thái -->
                        <?php if (!$isLocked): ?>
                        <div class="form-group">
                            <label class="small font-weight-bold">Trạng thái</label>
                            <select name="status" class="form-control form-control-sm">
                                <option value="confirmed" <?= $st === 'confirmed' ? 'selected' : '' ?>>✅ Đã xác nhận</option>
                                <option value="check_in"  <?= $st === 'check_in'  ? 'selected' : '' ?>>🔵 Check-in</option>
                                <option value="check_out" <?= $st === 'check_out' ? 'selected' : '' ?>>✔ Check-out</option>
                            </select>
                        </div>
                        <?php endif; ?>

                        <!-- Nguồn đặt -->
                        <div class="form-group mb-0">
                            <label class="small font-weight-bold">Nguồn đặt</label>
                            <div class="text-muted small"><?= htmlspecialchars($appointment['nguon_dat'] ?? 'hotline') ?></div>
                        </div>
                    </div>
                    <?php if (!$isLocked): ?>
                    <div class="card-footer py-2 text-right">
                        <a href="<?= admin_route('booking', ['date' => $startDate]) ?>"
                           class="btn btn-outline-secondary btn-sm mr-2">Hủy</a>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-save mr-1"></i> Lưu thay đổi
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Cột phải: dịch vụ -->
            <div class="col-lg-7 mb-4">
                <div class="card card-salon shadow-sm">
                    <div class="card-header py-2 d-flex justify-content-between align-items-center">
                        <strong>Dịch vụ đã đặt</strong>
                        <span class="badge badge-secondary"><?= count($services) ?></span>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($services)): ?>
                            <p class="text-center text-muted py-4 mb-0">Không có dịch vụ.</p>
                        <?php else: ?>
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Dịch vụ</th>
                                    <th style="width:110px">Thời lượng</th>
                                    <th style="width:130px" class="text-right">Giá</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($services as $s): ?>
                                <tr>
                                    <td><?= htmlspecialchars($s['ten_dich_vu']) ?></td>
                                    <td><?= (int) $s['thoi_luong'] ?> phút</td>
                                    <td class="text-right text-nowrap"><?= format_vnd($s['gia']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold">
                                    <td colspan="2" class="text-right">Tổng cộng</td>
                                    <td class="text-right text-nowrap"><?= format_vnd($total) ?></td>
                                </tr>
                            </tfoot>
                        </table>
                        <?php endif; ?>
                    </div>
                    <?php if (!$isLocked && $st === 'check_in'): ?>
                    <div class="card-footer py-2">
                        <a href="<?= admin_route('pos', ['appointment_id' => (int) $appointment['ma_lich_hen']]) ?>"
                           class="btn btn-success btn-sm btn-block">
                            <i class="fas fa-cash-register mr-1"></i> Thanh toán ngay
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Gộp date + time thành datetime khi submit
document.querySelector('form').addEventListener('submit', function() {
    var d = document.getElementById('editDate').value;
    var t = document.getElementById('editTime').value;
    if (d && t) {
        document.getElementById('editStartTime').value = d + ' ' + t + ':00';
    }
});
</script>
