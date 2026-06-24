<div class="container-fluid booking-list-page">
    <div class="page-title-bar">
        <h1>Lịch hẹn</h1>
        <a href="<?= admin_route('booking/create') ?>" class="btn btn-primary btn-sm">
            <i class="fas fa-plus mr-1"></i> Tạo mới
        </a>
    </div>

    <?php if (!empty($_GET['msg']) && $_GET['msg'] === 'created'): ?>
        <div class="alert alert-success py-2">Đã tạo lịch hẹn thành công!</div>
    <?php endif; ?>

    <!-- Bộ lọc -->
    <div class="card card-salon mb-3">
        <div class="card-body py-2">
            <form class="booking-filter-row d-flex flex-wrap align-items-center" method="get" action="index.php" style="gap:8px">
                <input type="hidden" name="route" value="booking">
                <button class="btn btn-outline-primary btn-sm" type="button"
                    onclick="window.location='<?= admin_route('booking', ['view' => 'day', 'date' => date('Y-m-d')]) ?>'">
                    Hôm nay
                </button>
                <button class="btn btn-outline-secondary btn-sm" type="button"
                    onclick="window.location='<?= admin_route('booking', ['view' => 'week', 'date' => date('Y-m-d')]) ?>'">
                    7 ngày đến
                </button>
                <input type="date" name="date" class="form-control form-control-sm" style="width:150px"
                       value="<?= htmlspecialchars($date) ?>">
                <input type="date" name="date_to" class="form-control form-control-sm" style="width:150px"
                       value="<?= htmlspecialchars($to) ?>">
                <button class="btn btn-primary btn-sm" type="submit">Lọc</button>
                <input type="text" class="form-control form-control-sm" style="width:200px"
                       placeholder="Tìm khách hoặc nhân viên..."
                       oninput="bookingSearch(this.value)">
            </form>
        </div>
    </div>

    <!-- Bảng lịch hẹn -->
    <div class="card card-salon shadow">
        <div class="card-header d-flex justify-content-between align-items-center py-2">
            <span class="text-muted small">Lịch từ <strong><?= htmlspecialchars($from) ?></strong> đến <strong><?= htmlspecialchars($to) ?></strong></span>
           
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-salon table-bordered mb-0" id="bookingTable">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:110px">Ngày đặt</th>
                            <th style="width:55px">Giờ</th>
                            <th style="width:90px">Mã</th>
                            <th style="width:170px">Người đặt</th>
                            <th>Ghi chú & dịch vụ</th>
                            <th style="width:150px">Thanh toán</th>
                            <th style="width:180px" class="text-center">
                                Trạng thái <i class="fas fa-filter text-muted ml-1" style="font-size:0.75rem"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($appointments)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-calendar-times fa-2x d-block mb-2"></i>
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
                            <td colspan="7" class="py-1 px-3" style="background:#f0f4ff;font-weight:600;font-size:0.85rem;color:#1e5bb8">
                                <?= $dayLabel ?>
                                <?php if ($dayLabel === date('d/m/Y')): ?>
                                    <span class="badge badge-primary ml-2">Hôm nay</span>
                                <?php endif; ?>
                                <span class="badge badge-secondary ml-1"><?= count($rows) ?></span>
                            </td>
                        </tr>

                        <?php foreach ($rows as $a):
                            $st = strtolower(trim($a['trang_thai'] ?? ''));
                            if ($a['da_huy'] ?? false) $st = 'cancelled';

                            // Normalize tất cả giá trị cũ/mới về 3 trạng thái chuẩn
                            if (in_array($st, ['pending', 'confirmed', ''])) $st = 'confirmed';
                            elseif (in_array($st, ['arrived', 'in_service', 'check_in', 'checkin'])) $st = 'check_in';
                            elseif (in_array($st, ['completed', 'check_out', 'checkout'])) $st = 'check_out';

                            $isPaid = !empty($a['has_paid_order']);
                            $clientName = trim(($a['ten'] ?? '') . ' ' . ($a['ho_dem'] ?? ''));
                            $empName    = trim(($a['emp_fname'] ?? '') . ' ' . ($a['emp_lname'] ?? ''));
                        ?>
                        <tr data-search="<?= htmlspecialchars(strtolower($clientName . ' ' . $empName . ' ' . ($a['so_dien_thoai'] ?? ''))) ?>">
                            <td class="text-muted small"><?= date('d/m/Y', strtotime($a['thoi_gian_bat_dau'])) ?></td>
                            <td class="font-weight-bold"><?= date('H:i', strtotime($a['thoi_gian_bat_dau'])) ?></td>
                            <td>
                                <a class="easy-link font-weight-bold" href="<?= admin_route('booking/edit', ['id' => $a['ma_lich_hen']]) ?>">
                                    <?= htmlspecialchars(appointment_display_code((int) $a['ma_lich_hen'])) ?>
                                </a>
                            </td>
                            <td>
                                <div class="font-weight-bold"><?= htmlspecialchars($clientName) ?></div>
                                <small class="text-muted"><?= htmlspecialchars($a['so_dien_thoai'] ?? '') ?></small>
                                <?php if ($empName): ?>
                                    <br><small class="text-primary"><i class="fas fa-user-tie fa-xs mr-1"></i><?= htmlspecialchars($empName) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($a['services_text'])): ?>
                                    <div class="small">
                                        <?php
                                        $svcs = $a['booked_services'] ?? [];
                                        foreach ($svcs as $svc):
                                        ?>
                                        <span>· <?= htmlspecialchars($svc['ten_dich_vu']) ?></span><br>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($a['ghi_chu_quan_tri'])): ?>
                                    <small class="text-muted"><?= htmlspecialchars($a['ghi_chu_quan_tri']) ?></small>
                                <?php endif; ?>
                            </td>

                            <!-- Cột Thanh toán -->
                            <td>
                                <?php if ($isPaid): ?>
                                    <span class="badge badge-success px-2 py-1">
                                        <i class="fas fa-check-circle mr-1"></i> Đã thanh toán
                                    </span>
                                <?php elseif ($st === 'check_in'): ?>
                                    <a href="<?= admin_route('pos', ['appointment_id' => (int) $a['ma_lich_hen']]) ?>"
                                       class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-cash-register mr-1"></i> Chọn thanh toán
                                        <?php if (!empty($a['services_total'])): ?>
                                            <span class="ml-1 font-weight-bold"><?= format_vnd($a['services_total']) ?></span>
                                        <?php endif; ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">—</span>
                                <?php endif; ?>
                            </td>

                            <!-- Cột Trạng thái -->
                            <td class="text-center">
                                <?php if ($isPaid): ?>
                                    <span class="badge badge-success px-3 py-2" style="font-size:0.8rem">
                                        <i class="fas fa-money-bill-wave mr-1"></i> Đã thanh toán
                                    </span>
                                <?php else: ?>
                                <form method="post" action="<?= admin_route('booking/update-status') ?>"
                                      class="d-inline-flex align-items-center" style="gap:4px"
                                      id="stForm<?= (int) $a['ma_lich_hen'] ?>">
                                    <input type="hidden" name="ma_lich_hen" value="<?= (int) $a['ma_lich_hen'] ?>">
                                    <input type="hidden" name="redirect_date" value="<?= htmlspecialchars($date) ?>">
                                    <input type="hidden" name="status" id="stVal<?= (int) $a['ma_lich_hen'] ?>" value="<?= $st ?>">
                                    <select class="form-control form-control-sm booking-status-select"
                                            style="min-width:130px"
                                            onchange="
                                                document.getElementById('stVal<?= (int) $a['ma_lich_hen'] ?>').value = this.value;
                                                document.getElementById('stForm<?= (int) $a['ma_lich_hen'] ?>').submit();
                                            ">
                                        <option value="confirmed" <?= $st === 'confirmed' ? 'selected' : '' ?>>✅ Đã xác nhận</option>
                                        <option value="check_in"  <?= $st === 'check_in'  ? 'selected' : '' ?>>🔵 Check-in</option>
                                        <option value="check_out" <?= $st === 'check_out' ? 'selected' : '' ?>>✔ Check-out</option>
                                    </select>
                                    <!-- Nút hủy lịch -->
                                    <button type="button" class="btn btn-sm btn-danger"
                                            onclick="
                                                if(confirm('Hủy lịch hẹn này?')) {
                                                    document.getElementById('stVal<?= (int) $a['ma_lich_hen'] ?>').value = 'cancelled';
                                                    document.getElementById('stForm<?= (int) $a['ma_lich_hen'] ?>').submit();
                                                }
                                            "
                                            title="Hủy lịch">
                                        <i class="fas fa-trash"></i>
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
