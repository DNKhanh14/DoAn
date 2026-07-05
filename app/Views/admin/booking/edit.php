<?php
$clientName = trim(($appointment['ten'] ?? '') . ' ' . ($appointment['ho_dem'] ?? ''));
$empName    = trim(($appointment['emp_fname'] ?? '') . ' ' . ($appointment['emp_lname'] ?? ''));

$stColors = [
    'confirmed' => ['#f6a623', '#fffbeb', '✅ Đã xác nhận'],
    'check_in'  => ['#1e5bb8', '#eff6ff', '🔵 Check-in'],
    'check_out' => ['#1cc88a', '#f0fdf4', '✔ Check-out'],
];
[$stColor, $stBg, $stBadgeLabel] = $stColors[$st] ?? ['#6c757d', '#f8fafc', $st];

$startDt   = $appointment['thoi_gian_bat_dau']           ?? '';
$startDate = $startDt ? date('Y-m-d', strtotime($startDt)) : '';
$startTime = $startDt ? date('H:i',   strtotime($startDt)) : '';
$endDt     = $appointment['thoi_gian_ket_thuc_du_kien']  ?? '';
$total     = array_sum(array_map(fn($s) => (float)$s['gia'], $services));
?>

<div class="container-fluid">
    <!-- Header -->
    <div class="page-title-bar">
        <div class="d-flex align-items-center" style="gap:10px">
            <a href="<?= admin_route('booking', ['date' => $startDate]) ?>"
               class="btn btn-outline-secondary btn-sm" style="border-radius:8px">
                <i class="fas fa-arrow-left mr-1"></i>Danh sách
            </a>
            <h1 class="mb-0">
                Sửa lịch hẹn
                <span style="color:#9ca3af; font-size:0.85rem; font-weight:400">
                    — <?= htmlspecialchars(appointment_display_code((int)$appointment['ma_lich_hen'])) ?>
                </span>
            </h1>
        </div>
        <div class="d-flex align-items-center" style="gap:8px">
            <!-- Badge trạng thái -->
            <span class="badge text-white" style="background:<?= $stColor ?>; font-size:0.82rem; padding:6px 14px; border-radius:20px">
                <?= $stBadgeLabel ?>
            </span>
            <?php if ($isPaid): ?>
                <span class="badge text-white" style="background:#1cc88a; font-size:0.82rem; padding:6px 14px; border-radius:20px">
                    <i class="fas fa-money-bill-wave mr-1"></i>Đã thanh toán
                </span>
            <?php elseif ($st === 'check_in'): ?>
                <a href="<?= admin_route('pos', ['appointment_id' => (int)$appointment['ma_lich_hen']]) ?>"
                   class="btn btn-sm btn-success" style="border-radius:8px">
                    <i class="fas fa-cash-register mr-1"></i>Thanh toán — <?= format_vnd($total) ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $message['type'] ?> py-2" style="border-radius:10px">
            <?= htmlspecialchars($message['text']) ?>
        </div>
    <?php endif; ?>

    <?php if ($isLocked): ?>
        <div class="alert alert-light border py-2 small text-muted" style="border-radius:10px">
            <i class="fas fa-lock mr-2"></i>Lịch hẹn đã hoàn tất — không thể chỉnh sửa.
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Cột trái -->
        <div class="col-lg-5 mb-4">

            <!-- Card khách hàng -->
            <div class="card shadow-sm mb-3" style="border-left:4px solid #20c9a6; border-radius:10px; overflow:hidden; border-right:none; border-top:none; border-bottom:none">
                <div class="card-body py-3 px-4">
                    <div class="d-flex align-items-center" style="gap:12px">
                        <div style="width:44px; height:44px; border-radius:50%; background:#20c9a61a; color:#20c9a6; display:flex; align-items:center; justify-content:center; flex-shrink:0; font-weight:700; font-size:1rem">
                            <?= strtoupper(mb_substr($appointment['ten'] ?? 'K', 0, 1)) ?>
                        </div>
                        <div>
                            <div style="font-weight:600; color:#2d3748"><?= htmlspecialchars($clientName) ?></div>
                            <div style="font-size:0.82rem; color:#9ca3af">
                                <i class="fas fa-phone mr-1" style="font-size:0.7rem"></i>
                                <?= htmlspecialchars($appointment['so_dien_thoai'] ?? '—') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card thông tin lịch hẹn -->
            <div class="card shadow-sm" style="border-radius:10px; overflow:hidden; border:none">
                <div class="card-header py-3 px-4" style="background:#fff; border-bottom:1px solid #e2e8f0">
                    <div class="d-flex align-items-center" style="gap:10px">
                        <div style="width:32px; height:32px; border-radius:8px; background:#1e5bb81a; color:#1e5bb8; display:flex; align-items:center; justify-content:center">
                            <i class="fas fa-calendar-alt" style="font-size:13px"></i>
                        </div>
                        <span class="font-weight-bold" style="color:#2d3748">Thông tin lịch hẹn</span>
                    </div>
                </div>
                <form method="post" action="<?= admin_route('booking/edit', ['id' => (int)$appointment['ma_lich_hen']]) ?>">
                    <input type="hidden" name="update_booking" value="1">
                    <div class="card-body" style="padding:20px">

                        <!-- Ngày & Giờ -->
                        <div class="form-row">
                            <div class="form-group col-6">
                                <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Ngày</label>
                                <input type="date" name="booking_date" id="editDate"
                                       class="form-control mt-1"
                                       value="<?= $startDate ?>"
                                       style="border-radius:8px; height:40px; border:1px solid #e2e8f0"
                                       <?= $isLocked ? 'disabled' : '' ?>>
                            </div>
                            <div class="form-group col-6">
                                <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Giờ</label>
                                <input type="time" name="booking_time" id="editTime"
                                       class="form-control mt-1"
                                       value="<?= $startTime ?>"
                                       style="border-radius:8px; height:40px; border:1px solid #e2e8f0"
                                       <?= $isLocked ? 'disabled' : '' ?>>
                            </div>
                        </div>
                        <input type="hidden" name="start_time" id="editStartTime" value="<?= htmlspecialchars($startDt) ?>">
                        <input type="hidden" name="end_time"   id="editEndTime"   value="<?= htmlspecialchars($endDt) ?>">

                        <!-- Nhân viên -->
                        <div class="form-group">
                            <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Nhân viên</label>
                            <select name="ma_nhan_vien" class="form-control mt-1"
                                    style="border-radius:8px; height:40px; border:1px solid #e2e8f0"
                                    <?= $isLocked ? 'disabled' : '' ?>>
                                <?php foreach ($employees as $e): ?>
                                    <option value="<?= (int)$e['ma_nhan_vien'] ?>"
                                        <?= $appointment['ma_nhan_vien'] == $e['ma_nhan_vien'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars(trim($e['ten'] . ' ' . $e['ho_dem'])) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Trạng thái -->
                        <?php if (!$isLocked): ?>
                        <div class="form-group mb-0">
                            <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Trạng thái</label>
                            <select name="status" class="form-control mt-1"
                                    style="border-radius:8px; height:40px; border:1px solid <?= $stColor ?>; background:<?= $stBg ?>; color:<?= $stColor ?>; font-weight:600">
                                <option value="confirmed" <?= $st==='confirmed'?'selected':'' ?>>✅ Đã xác nhận</option>
                                <option value="check_in"  <?= $st==='check_in' ?'selected':'' ?>>🔵 Check-in</option>
                                <option value="check_out" <?= $st==='check_out'?'selected':'' ?>>✔ Check-out</option>
                            </select>
                        </div>
                        <?php endif; ?>

                    </div>
                    <?php if (!$isLocked): ?>
                    <div class="card-footer py-2 px-4 d-flex justify-content-end" style="background:#f8fafc; border-top:1px solid #e2e8f0; gap:8px">
                        <a href="<?= admin_route('booking', ['date' => $startDate]) ?>"
                           class="btn btn-light btn-sm px-4" style="border-radius:8px">Hủy</a>
                        <button type="submit" class="btn btn-primary btn-sm px-4" style="border-radius:8px">
                            <i class="fas fa-save mr-1"></i>Lưu thay đổi
                        </button>
                    </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Cột phải: dịch vụ đã đặt -->
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm" style="border-radius:10px; overflow:hidden; border:none">
                <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between"
                     style="background:#fff; border-bottom:1px solid #e2e8f0">
                    <div class="d-flex align-items-center" style="gap:10px">
                        <div style="width:32px; height:32px; border-radius:8px; background:#e74a3b1a; color:#e74a3b; display:flex; align-items:center; justify-content:center">
                            <i class="fas fa-cut" style="font-size:13px"></i>
                        </div>
                        <span class="font-weight-bold" style="color:#2d3748">Dịch vụ đã đặt</span>
                    </div>
                    <span class="badge text-white" style="background:#e74a3b; font-size:0.75rem; padding:4px 10px; border-radius:20px">
                        <?= count($services) ?> dịch vụ
                    </span>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($services)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-cut fa-2x mb-2" style="opacity:0.3"></i>
                            <p class="mb-0">Không có dịch vụ nào.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($services as $idx => $s): ?>
                        <div class="d-flex align-items-center px-4 py-3 <?= $idx < count($services)-1 ? 'border-bottom' : '' ?>"
                             style="transition:background .15s"
                             onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                            <!-- STT -->
                            <div style="width:28px; height:28px; border-radius:50%; background:#e74a3b15; color:#e74a3b; font-size:0.75rem; font-weight:700; line-height:28px; text-align:center; flex-shrink:0; margin-right:12px">
                                <?= $idx + 1 ?>
                            </div>
                            <!-- Tên -->
                            <div class="flex-grow-1">
                                <div style="font-weight:600; color:#2d3748; font-size:0.9rem">
                                    <?= htmlspecialchars($s['ten_dich_vu']) ?>
                                </div>
                                <div style="font-size:0.75rem; color:#9ca3af; margin-top:2px">
                                    <i class="fas fa-clock mr-1"></i><?= (int)$s['thoi_luong'] ?> phút
                                </div>
                            </div>
                            <!-- Giá -->
                            <div style="font-size:1rem; font-weight:700; color:#e74a3b; flex-shrink:0">
                                <?= format_vnd($s['gia']) ?>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <!-- Tổng -->
                        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top"
                             style="background:#fafafa">
                            <span style="font-weight:600; color:#4a5568">Tổng cộng</span>
                            <span style="font-size:1.1rem; font-weight:700; color:#1e5bb8"><?= format_vnd($total) ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (!$isLocked && $st === 'check_in'): ?>
                <div class="card-footer py-2 px-4" style="background:#f0fdf4">
                    <a href="<?= admin_route('pos', ['appointment_id' => (int)$appointment['ma_lich_hen']]) ?>"
                       class="btn btn-success btn-block" style="border-radius:8px">
                        <i class="fas fa-cash-register mr-1"></i>Thanh toán ngay — <?= format_vnd($total) ?>
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <!-- Nguồn đặt -->
            <div class="mt-3 px-1">
                <span class="text-muted small">
                    <i class="fas fa-info-circle mr-1"></i>Nguồn đặt:
                    <strong><?= htmlspecialchars($appointment['nguon_dat'] ?? 'hotline') ?></strong>
                </span>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelector('form[action*="booking/edit"]')?.addEventListener('submit', function() {
    var d = document.getElementById('editDate').value;
    var t = document.getElementById('editTime').value;
    if (d && t) {
        document.getElementById('editStartTime').value = d + ' ' + t + ':00';
    }
});
</script>
