<div class="container-fluid booking-list-page">
    <div class="page-title-bar">
        <h1>Lịch hẹn</h1>
        <a href="<?= admin_route('booking/create') ?>" class="btn-them-moi">
            <i class="fas fa-plus mr-1"></i> Tạo lịch hẹn
        </a>
    </div>

    <?php if (!empty($_GET['msg']) && $_GET['msg'] === 'created'): ?>
        <div class="alert alert-success py-2" style="border-radius:10px; border-left:4px solid #1cc88a">
            <i class="fas fa-check-circle mr-1"></i> Đã tạo lịch hẹn thành công!
        </div>
    <?php endif; ?>

    <!-- Bộ lọc -->
    <div class="card shadow-sm mb-4" style="border-radius:10px; overflow:hidden; border:none">
        <div class="card-body py-3 px-4">
            <form class="d-flex flex-wrap align-items-center" method="get" action="index.php" style="gap:8px">
                <input type="hidden" name="route" value="booking">
                <button class="btn btn-sm" type="button"
                        onclick="window.location='<?= admin_route('booking', ['view' => 'day', 'date' => date('Y-m-d'), 'date_to' => date('Y-m-d')]) ?>'"
                        style="border-radius:8px; border:1px solid #1e5bb8; color:#1e5bb8; background:#fff; padding:6px 14px">
                    <i class="fas fa-calendar-day mr-1"></i>Hôm nay
                </button>
                <button class="btn btn-sm" type="button"
                        onclick="window.location='<?= admin_route('booking', ['view' => 'week', 'date' => date('Y-m-d'), 'date_to' => date('Y-m-d', strtotime('+6 days'))]) ?>'"
                        style="border-radius:8px; border:1px solid #e2e8f0; color:#4a5568; background:#fff; padding:6px 14px">
                    <i class="fas fa-calendar-week mr-1"></i>7 ngày tới
                </button>
                <div style="width:1px; height:28px; background:#e2e8f0; margin:0 4px"></div>
                <input type="date" name="date" class="form-control form-control-sm"
                       style="width:150px; border-radius:8px; height:36px; border:1px solid #e2e8f0"
                       value="<?= htmlspecialchars($from) ?>">
                <span class="text-muted">~</span>
                <input type="date" name="date_to" class="form-control form-control-sm"
                       style="width:150px; border-radius:8px; height:36px; border:1px solid #e2e8f0"
                       value="<?= htmlspecialchars($to) ?>">
                <button class="btn btn-primary btn-sm" type="submit" style="border-radius:8px; height:36px; padding:0 16px">
                    <i class="fas fa-search mr-1"></i>Lọc
                </button>
                <div class="ml-auto">
                    <div class="input-group" style="width:240px">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0" style="border-radius:8px 0 0 8px">
                                <i class="fas fa-search text-muted" style="font-size:0.8rem"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control border-left-0 form-control-sm"
                               style="border-radius:0 8px 8px 0"
                               placeholder="Tìm khách, nhân viên..."
                               oninput="bookingSearch(this.value)">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Stat pills -->
    <?php if (!empty($appointments)): ?>
    <div class="d-flex mb-3" style="gap:8px; flex-wrap:wrap">
        <div class="stat-pill">
            <i class="fas fa-calendar-check mr-1"></i>
            <strong><?= count($appointments) ?></strong> lịch hẹn
        </div>
        <div class="stat-pill">
            <i class="fas fa-calendar-alt mr-1" style="color:#1e5bb8"></i>
            Từ <strong><?= htmlspecialchars($from) ?></strong> ~ <strong><?= htmlspecialchars($to) ?></strong>
        </div>
    </div>
    <?php endif; ?>

    <!-- Bảng lịch hẹn -->
    <div class="card shadow-sm" style="border-radius:10px; overflow:hidden; border:none">
        <div class="card-header d-flex justify-content-between align-items-center py-2 px-4" style="background:#f8fafc; border-bottom:1px solid #e2e8f0">
            <span class="text-muted small"><i class="fas fa-list mr-1"></i>Danh sách lịch hẹn</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0" id="bookingTable" style="font-size:0.88rem">
                    <thead style="background:#f8fafc">
                        <tr style="border-bottom:2px solid #e2e8f0">
                            <th style="width:100px; padding:12px 16px; color:#6b7280; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px">Ngày</th>
                            <th style="width:55px; padding:12px 8px; color:#6b7280; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px">Giờ</th>
                            <th style="width:90px; padding:12px 8px; color:#6b7280; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px">Mã</th>
                            <th style="padding:12px 8px; color:#6b7280; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px">Người đặt</th>
                            <th style="padding:12px 8px; color:#6b7280; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px">Dịch vụ</th>
                            <th style="width:160px; padding:12px 8px; color:#6b7280; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px">Thanh toán</th>
                            <th style="width:190px; padding:12px 8px; text-align:center; color:#6b7280; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($appointments)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-calendar-times fa-2x d-block mb-2" style="opacity:0.3"></i>
                                Không có lịch hẹn trong khoảng thời gian này
                            </td>
                        </tr>
                    <?php else: ?>

                    <?php
                    // Nhóm lịch theo ngày
                    $grouped = [];
                    foreach ($appointments as $a) {
                        $day = date('d/m/Y', strtotime($a['thoi_gian_bat_dau']));
                        $grouped[$day][] = $a;
                    }
                    ?>

                    <?php foreach ($grouped as $dayLabel => $rows): ?>
                        <tr class="table-group-row">
                            <td colspan="7" style="background:#f0f4ff; font-weight:600; font-size:0.82rem; color:#1e5bb8; padding:8px 16px; border-top:2px solid #dbeafe">
                                <i class="fas fa-calendar-day mr-2"></i><?= $dayLabel ?>
                                <?php if ($dayLabel === date('d/m/Y')): ?>
                                    <span class="badge ml-2" style="background:#1e5bb8; color:#fff; font-size:0.7rem; padding:3px 8px; border-radius:20px">Hôm nay</span>
                                <?php endif; ?>
                                <span class="badge ml-1" style="background:#e2e8f0; color:#4a5568; font-size:0.7rem; padding:3px 8px; border-radius:20px"><?= count($rows) ?> lịch</span>
                            </td>
                        </tr>

                        <?php foreach ($rows as $a):
                            $st = strtolower(trim($a['trang_thai'] ?? ''));
                            if ($a['da_huy'] ?? false) $st = 'cancelled';
                            if (in_array($st, ['pending', 'confirmed', ''])) $st = 'confirmed';
                            elseif (in_array($st, ['arrived', 'in_service', 'check_in', 'checkin'])) $st = 'check_in';
                            elseif (in_array($st, ['completed', 'check_out', 'checkout'])) $st = 'check_out';
                            $isPaid = !empty($a['has_paid_order']);
                            $clientName = trim(($a['ten'] ?? '') . ' ' . ($a['ho_dem'] ?? ''));
                            $empName    = trim(($a['emp_fname'] ?? '') . ' ' . ($a['emp_lname'] ?? ''));
                            $stConfig = [
                                'confirmed' => ['#f6a623', '#fffbeb', '✅ Xác nhận'],
                                'check_in'  => ['#1e5bb8', '#eff6ff', '🔵 Check-in'],
                                'check_out' => ['#1cc88a', '#f0fdf4', '✔ Check-out'],
                            ];
                            [$stColor, $stBg, $stLabel] = $stConfig[$st] ?? ['#6c757d', '#f8fafc', $st];
                        ?>
                        <tr data-search="<?= htmlspecialchars(strtolower($clientName . ' ' . $empName . ' ' . ($a['so_dien_thoai'] ?? ''))) ?>"
                            style="transition:background .15s"
                            onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                            <td style="padding:10px 16px; color:#6b7280; font-size:0.82rem"><?= date('d/m/Y', strtotime($a['thoi_gian_bat_dau'])) ?></td>
                            <td style="padding:10px 8px; font-weight:700; color:#2d3748"><?= date('H:i', strtotime($a['thoi_gian_bat_dau'])) ?></td>
                            <td style="padding:10px 8px">
                                <a style="color:#1e5bb8; font-weight:700; text-decoration:none; font-size:0.85rem"
                                   href="<?= admin_route('booking/edit', ['id' => $a['ma_lich_hen']]) ?>">
                                    <?= htmlspecialchars(appointment_display_code((int)$a['ma_lich_hen'])) ?>
                                </a>
                            </td>
                            <td style="padding:10px 8px">
                                <div style="font-weight:600; color:#2d3748; font-size:0.88rem"><?= htmlspecialchars($clientName) ?></div>
                                <div style="font-size:0.78rem; color:#9ca3af"><?= htmlspecialchars($a['so_dien_thoai'] ?? '') ?></div>
                                <?php if ($empName): ?>
                                    <div style="font-size:0.78rem; color:#1e5bb8; margin-top:2px">
                                        <i class="fas fa-user-tie fa-xs mr-1"></i><?= htmlspecialchars($empName) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td style="padding:10px 8px">
                                <?php
                                $svcs = $a['booked_services'] ?? [];
                                foreach ($svcs as $svc):
                                ?>
                                <div style="font-size:0.8rem; color:#4a5568">· <?= htmlspecialchars($svc['ten_dich_vu']) ?></div>
                                <?php endforeach; ?>
                                <?php if (!empty($a['ghi_chu_quan_tri'])): ?>
                                    <div style="font-size:0.78rem; color:#9ca3af; margin-top:2px">
                                        <i class="fas fa-sticky-note mr-1"></i><?= htmlspecialchars($a['ghi_chu_quan_tri']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td style="padding:10px 8px">
                                <?php if ($isPaid): ?>
                                    <span class="badge text-white" style="background:#1cc88a; font-size:0.73rem; padding:4px 8px; border-radius:20px">
                                        <i class="fas fa-check-circle mr-1" style="font-size:0.65rem"></i>Đã thanh toán
                                    </span>
                                <?php elseif ($st === 'check_in'): ?>
                                    <a href="<?= admin_route('pos', ['appointment_id' => (int)$a['ma_lich_hen']]) ?>"
                                       class="btn btn-sm" style="border-radius:8px; border:1px solid #1cc88a; color:#1cc88a; background:#f0fdf4; font-size:0.78rem; padding:4px 10px; white-space:nowrap">
                                        <i class="fas fa-cash-register mr-1"></i>Thanh toán
                                        <?php if (!empty($a['services_total'])): ?>
                                            <span style="font-weight:700"><?= format_vnd($a['services_total']) ?></span>
                                        <?php endif; ?>
                                    </a>
                                <?php else: ?>
                                    <span style="color:#d1d5db; font-size:0.85rem">—</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding:10px 8px; text-align:center">
                                <?php if ($isPaid): ?>
                                    <span class="badge text-white" style="background:#1cc88a; font-size:0.73rem; padding:5px 12px; border-radius:20px">
                                        <i class="fas fa-money-bill-wave mr-1" style="font-size:0.65rem"></i>Đã thanh toán
                                    </span>
                                <?php else: ?>
                                <form method="post" action="<?= admin_route('booking/update-status') ?>"
                                      class="d-inline-flex align-items-center" style="gap:4px"
                                      id="stForm<?= (int)$a['ma_lich_hen'] ?>">
                                    <input type="hidden" name="ma_lich_hen" value="<?= (int)$a['ma_lich_hen'] ?>">
                                    <input type="hidden" name="redirect_date" value="<?= htmlspecialchars($from) ?>">
                                    <input type="hidden" name="redirect_date_to" value="<?= htmlspecialchars($to) ?>">
                                    <input type="hidden" name="status" id="stVal<?= (int)$a['ma_lich_hen'] ?>" value="<?= $st ?>">
                                    <select class="form-control form-control-sm booking-status-select"
                                            style="min-width:130px; border-radius:8px; height:34px; border:1px solid <?= $stColor ?>; background:<?= $stBg ?>; color:<?= $stColor ?>; font-weight:600; font-size:0.8rem"
                                            onchange="document.getElementById('stVal<?= (int)$a['ma_lich_hen'] ?>').value=this.value; document.getElementById('stForm<?= (int)$a['ma_lich_hen'] ?>').submit();">
                                        <option value="confirmed" <?= $st==='confirmed'?'selected':'' ?>>✅ Xác nhận</option>
                                        <option value="check_in"  <?= $st==='check_in' ?'selected':'' ?>>🔵 Check-in</option>
                                        <option value="check_out" <?= $st==='check_out'?'selected':'' ?>>✔ Check-out</option>
                                    </select>
                                    <button type="button"
                                            onclick="if(confirm('Hủy lịch hẹn này?')){document.getElementById('stVal<?= (int)$a['ma_lich_hen'] ?>').value='cancelled';document.getElementById('stForm<?= (int)$a['ma_lich_hen'] ?>').submit();}"
                                            title="Hủy lịch"
                                            style="width:32px; height:32px; border-radius:8px; background:#fee2e2; border:none; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0">
                                        <i class="fas fa-times" style="color:#e74a3b; font-size:12px"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.booking-status-select option[value="confirmed"] { color: #856404; }
.booking-status-select option[value="check_in"]  { color: #004085; }
.booking-status-select option[value="check_out"] { color: #155724; }
.table-group-row td { border-top: 2px solid #dee2e6 !important; }
</style>

<script>
function bookingSearch(q) {
    q = (q || '').toLowerCase().trim();
    document.querySelectorAll('#bookingTable tbody tr:not(.table-group-row)').forEach(function(tr) {
        var hay = (tr.dataset.search || '') + ' ' + tr.innerText.toLowerCase();
        tr.style.display = (!q || hay.indexOf(q) >= 0) ? '' : 'none';
    });
}
</script>
