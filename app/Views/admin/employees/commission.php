<?php
$map = $rateMap ?? ['dich_vu' => [], 'san_pham' => []];
$selectedEmployeeId = $selectedEmployeeId ?? 0;
?>

<div class="container-fluid settings-page">
    <div class="easy-page-head">
        <h1>Tùy chỉnh hoa hồng</h1>
        <a href="<?= admin_route('employees') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Danh sách nhân viên
        </a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if (!table_exists('chi_tiet_hoa_hong')): ?>
        <div class="alert alert-info small">Chạy file SQL để tạo bảng hoa hồng.</div>
    <?php endif; ?>

    <!-- Chọn nhân viên -->
    <div class="card card-salon shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="get" action="index.php" class="form-inline" style="gap:12px">
                <input type="hidden" name="route" value="employees/commission">
                <label class="mr-2 mb-0 font-weight-bold">Nhân viên:</label>
                <select name="ma_nhan_vien" class="form-control form-control-sm" onchange="this.form.submit()">
                    <option value="0">-- Chọn nhân viên --</option>
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?= $emp['ma_nhan_vien'] ?>"
                            <?= $selectedEmployeeId == $emp['ma_nhan_vien'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars(trim($emp['ten'] . ' ' . $emp['ho_dem'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>

    <?php if ($selectedEmployeeId <= 0): ?>
        <div class="alert alert-info">Vui lòng chọn nhân viên để tùy chỉnh hoa hồng.</div>
    <?php else: ?>

    <form method="post" action="index.php">
        <input type="hidden" name="route" value="employees/commission">
        <input type="hidden" name="save_commission_rates" value="1">
        <input type="hidden" name="ma_nhan_vien" value="<?= $selectedEmployeeId ?>">

        <!-- Dịch vụ theo danh mục — accordion -->
        <div class="accordion commission-accordion mb-3" id="commissionAccordion">
            <?php $catIdx = 0; foreach ($servicesByCategory as $catName => $services): ?>
            <?php $colId = 'commCat_' . $catIdx; $headId = 'commHead_' . $catIdx; $open = ($catIdx === 0); ?>
            <div class="card card-salon shadow-sm mb-2">
                <div class="card-header p-0" id="<?= $headId ?>">
                    <button class="btn btn-block text-left d-flex justify-content-between align-items-center py-2 px-3<?= $open ? '' : ' collapsed' ?>"
                            type="button" data-toggle="collapse" data-target="#<?= $colId ?>"
                            aria-expanded="<?= $open ? 'true' : 'false' ?>">
                        <span><strong><?= htmlspecialchars($catName) ?></strong>
                            <span class="badge badge-secondary ml-2"><?= count($services) ?></span>
                        </span>
                        <i class="fas fa-chevron-<?= $open ? 'up' : 'down' ?> text-muted"></i>
                    </button>
                </div>
                <div id="<?= $colId ?>" class="collapse<?= $open ? ' show' : '' ?>">
                    <div class="card-body p-0">
                        <table class="table table-sm table-bordered mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Dịch vụ</th>
                                    <th style="width:140px">Giá</th>
                                    <th style="width:160px">Hoa hồng (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($services as $s):
                                $sid = (int) $s['ma_dich_vu'];
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($s['ten_dich_vu']) ?></td>
                                <td class="text-nowrap"><?= format_vnd($s['gia']) ?></td>
                                <td>
                                    <input type="number" step="1" min="0" max="100"
                                           class="form-control form-control-sm"
                                           name="service[<?= $sid ?>][commission]"
                                           placeholder="0"
                                           value="<?= (int) ($map['dich_vu'][$sid] ?? 0) ?: '' ?>">
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php $catIdx++; endforeach; ?>
        </div>

        <!-- Sản phẩm -->
        <?php if (!empty($products)): ?>
        <div class="card card-salon shadow-sm mb-3">
            <div class="card-header py-2"><strong>Sản phẩm</strong></div>
            <div class="card-body p-0">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="thead-light">
                        <tr>
                            <th>Sản phẩm</th>
                            <th style="width:140px">Giá bán</th>
                            <th style="width:160px">Hoa hồng (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($products as $p):
                        $pid = (int) $p['ma_san_pham'];
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($p['ten_san_pham']) ?></td>
                        <td class="text-nowrap"><?= format_vnd($p['gia_ban']) ?></td>
                        <td>
                            <input type="number" step="1" min="0" max="100"
                                   class="form-control form-control-sm"
                                   name="product[<?= $pid ?>][commission]"
                                   placeholder="0"
                                   value="<?= (int) ($map['san_pham'][$pid] ?? 0) ?: '' ?>">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <div class="text-center mt-3 mb-4">
            <button type="submit" class="btn btn-primary px-5">
                <i class="fas fa-save mr-1"></i> Lưu hoa hồng
            </button>
        </div>
    </form>

    <?php endif; ?>
</div>

<style>
.commission-accordion .btn:focus { box-shadow: none; }
.commission-accordion .btn { background: #f8f9fc; border: none; font-size: 0.9rem; }
.commission-accordion .btn:hover { background: #eaecf4; }
</style>
