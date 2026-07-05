<?php
$map = $rateMap ?? ['dich_vu' => [], 'san_pham' => []];
$selectedEmployeeId = $selectedEmployeeId ?? 0;
$catColors = ['#1e5bb8','#e74a3b','#1cc88a','#f6c23e','#6f42c1','#fd7e14','#20c9a6'];
?>

<div class="container-fluid">
    <div class="page-title-bar">
        <h1>Tùy chỉnh hoa hồng</h1>
        <a href="<?= admin_route('employees') ?>" class="btn btn-outline-secondary btn-sm" style="border-radius:8px">
            <i class="fas fa-arrow-left mr-1"></i> Danh sách nhân viên
        </a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px; border-left:4px solid #1cc88a">
            <i class="fas fa-check-circle mr-1"></i><?= htmlspecialchars($message) ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>
    <?php if (!table_exists('chi_tiet_hoa_hong')): ?>
        <div class="alert alert-info" style="border-radius:10px; border-left:4px solid #17a2b8">
            <i class="fas fa-info-circle mr-1"></i>Chạy file SQL để tạo bảng hoa hồng.
        </div>
    <?php endif; ?>

    <!-- Card chọn nhân viên -->
    <div class="card shadow-sm mb-4" style="border-left:4px solid #6f42c1; border-radius:10px; overflow:hidden; border-right:none; border-top:none; border-bottom:none">
        <div class="card-body d-flex align-items-center py-3" style="gap:16px">
            <div style="width:44px; height:44px; border-radius:10px; background:#6f42c11a; color:#6f42c1; display:flex; align-items:center; justify-content:center; flex-shrink:0">
                <i class="fas fa-user-tag fa-lg"></i>
            </div>
            <div class="flex-grow-1">
                <div class="font-weight-bold" style="color:#2d3748; margin-bottom:6px">Chọn nhân viên để chỉnh sửa hoa hồng</div>
                <form method="get" action="index.php" class="d-flex align-items-center" style="gap:10px">
                    <input type="hidden" name="route" value="employees/commission">
                    <select name="ma_nhan_vien" class="form-control" style="max-width:300px; border-radius:8px; height:40px; border:1px solid #e2e8f0" onchange="this.form.submit()">
                        <option value="0">-- Chọn nhân viên --</option>
                        <?php foreach ($employees as $emp): ?>
                            <option value="<?= $emp['ma_nhan_vien'] ?>"
                                <?= $selectedEmployeeId == $emp['ma_nhan_vien'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars(trim($emp['ten'] . ' ' . $emp['ho_dem'])) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary" style="border-radius:8px; height:40px">
                        <i class="fas fa-search mr-1"></i> Xem
                    </button>
                </form>
            </div>
        </div>
    </div>

    <?php if ($selectedEmployeeId <= 0): ?>
        <div class="card shadow-sm" style="border-radius:10px; overflow:hidden; border:none">
            <div class="card-body text-center py-5">
                <i class="fas fa-user-tag fa-3x text-muted mb-3"></i>
                <p class="text-muted">Vui lòng chọn nhân viên để tùy chỉnh hoa hồng.</p>
            </div>
        </div>
    <?php else: ?>

    <form method="post" action="index.php">
        <input type="hidden" name="route" value="employees/commission">
        <input type="hidden" name="save_commission_rates" value="1">
        <input type="hidden" name="ma_nhan_vien" value="<?= $selectedEmployeeId ?>">

        <!-- Dịch vụ theo danh mục — accordion -->
        <div class="accordion mb-4" id="commissionAccordion">
            <?php $catIdx = 0; foreach ($servicesByCategory as $catName => $services): ?>
            <?php
                $colId = 'commCat_' . $catIdx;
                $open = ($catIdx === 0);
                $color = $catColors[$catIdx % count($catColors)];
            ?>
            <div class="card shadow-sm mb-3" style="border-left:4px solid <?= $color ?>; border-radius:10px; overflow:hidden">
                <div class="card-header p-0" style="background:#fff">
                    <button class="btn btn-block text-left d-flex align-items-center py-3 px-4<?= $open ? '' : ' collapsed' ?>"
                            type="button" data-toggle="collapse" data-target="#<?= $colId ?>"
                            aria-expanded="<?= $open ? 'true' : 'false' ?>"
                            style="border:none; transition:background .15s">
                        <!-- Icon -->
                        <div style="width:36px; height:36px; border-radius:8px; background:<?= $color ?>1a; color:<?= $color ?>; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-right:12px">
                            <i class="fas fa-cut" style="font-size:14px"></i>
                        </div>
                        <div class="flex-grow-1 text-left">
                            <div class="font-weight-bold" style="color:#2d3748"><?= htmlspecialchars($catName) ?></div>
                            <div class="text-muted" style="font-size:0.78rem"><?= count($services) ?> dịch vụ</div>
                        </div>
                        <span class="badge mr-3" style="background:<?= $color ?>1a; color:<?= $color ?>; font-size:0.8rem; padding:4px 10px; border-radius:20px">
                            <?= count($services) ?>
                        </span>
                        <i class="fas fa-chevron-<?= $open ? 'up' : 'down' ?> text-muted" style="font-size:0.8rem"></i>
                    </button>
                </div>
                <div id="<?= $colId ?>" class="collapse<?= $open ? ' show' : '' ?>">
                    <div class="card-body p-0">
                        <?php foreach ($services as $idx => $s):
                            $sid = (int)$s['ma_dich_vu'];
                            $curVal = (int)($map['dich_vu'][$sid] ?? 0);
                        ?>
                        <div class="d-flex align-items-center px-4 py-3 <?= $idx < count($services)-1 ? 'border-bottom' : '' ?>"
                             style="transition:background .15s"
                             onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                            <!-- STT -->
                            <div style="width:28px; height:28px; border-radius:50%; background:<?= $color ?>15; color:<?= $color ?>; font-size:0.75rem; font-weight:700; line-height:28px; text-align:center; flex-shrink:0; margin-right:12px">
                                <?= $idx + 1 ?>
                            </div>
                            <!-- Tên -->
                            <div class="flex-grow-1">
                                <div style="font-size:0.92rem; font-weight:600; color:#2d3748"><?= htmlspecialchars($s['ten_dich_vu']) ?></div>
                                <div class="text-muted" style="font-size:0.78rem">Giá: <?= format_vnd($s['gia']) ?></div>
                            </div>
                            <!-- Input hoa hồng -->
                            <div class="d-flex align-items-center" style="gap:8px">
                                <div class="input-group" style="width:130px">
                                    <input type="number" step="1" min="0" max="100"
                                           class="form-control text-center"
                                           name="service[<?= $sid ?>][commission]"
                                           placeholder="0"
                                           value="<?= $curVal ?: '' ?>"
                                           style="border-radius:8px 0 0 8px; height:38px; border:1px solid #e2e8f0; font-weight:600; color:<?= $color ?>">
                                    <div class="input-group-append">
                                        <span class="input-group-text" style="border-radius:0 8px 8px 0; background:<?= $color ?>1a; color:<?= $color ?>; border:1px solid #e2e8f0; font-weight:600">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php $catIdx++; endforeach; ?>
        </div>

    <!-- Sản phẩm -->
<?php if (!empty($products)): ?>
<div class="card shadow-sm mb-4" style="border-left:4px solid #fd7e14; border-radius:10px; overflow:hidden">

    <div class="card-header p-0" style="background:#fff">
        <button class="btn btn-block text-left d-flex align-items-center py-3 px-4 collapsed"
                type="button"
                data-toggle="collapse"
                data-target="#productCollapse"
                aria-expanded="false"
                style="border:none; transition:background .15s">

            <div style="width:36px;height:36px;border-radius:8px;
                        background:#fd7e141a;color:#fd7e14;
                        display:flex;align-items:center;
                        justify-content:center;margin-right:12px">
                <i class="fas fa-box"></i>
            </div>

            <div class="flex-grow-1 text-left">
                <div class="font-weight-bold" style="color:#2d3748">
                    Sản phẩm
                </div>
                <div class="text-muted" style="font-size:.78rem">
                    <?= count($products) ?> sản phẩm
                </div>
            </div>

            <span class="badge mr-3"
                  style="background:#fd7e141a;
                         color:#fd7e14;
                         font-size:.8rem;
                         padding:4px 10px;
                         border-radius:20px">
                <?= count($products) ?>
            </span>

            <i class="fas fa-chevron-down text-muted product-arrow"></i>
        </button>
    </div>

    <div id="productCollapse" class="collapse">
        <div class="card-body p-0">

            <?php foreach ($products as $idx => $p):
                $pid = (int)$p['ma_san_pham'];
                $curVal = (int)($map['san_pham'][$pid] ?? 0);
            ?>

            <div class="d-flex align-items-center px-4 py-3 <?= $idx < count($products)-1 ? 'border-bottom' : '' ?>"
                 style="transition:background .15s"
                 onmouseover="this.style.background='#f8fafc'"
                 onmouseout="this.style.background=''">

                <div style="width:28px;height:28px;border-radius:50%;
                            background:#fd7e1415;
                            color:#fd7e14;
                            font-size:.75rem;
                            font-weight:700;
                            line-height:28px;
                            text-align:center;
                            flex-shrink:0;
                            margin-right:12px">
                    <?= $idx + 1 ?>
                </div>

                <div class="flex-grow-1">
                    <div style="font-size:.92rem;font-weight:600;color:#2d3748">
                        <?= htmlspecialchars($p['ten_san_pham']) ?>
                    </div>

                    <div class="text-muted" style="font-size:.78rem">
                        Giá: <?= format_vnd($p['gia_ban']) ?>
                    </div>
                </div>

                <div class="input-group" style="width:130px">

                    <input type="number"
                           step="1"
                           min="0"
                           max="100"
                           class="form-control text-center"
                           name="product[<?= $pid ?>][commission]"
                           placeholder="0"
                           value="<?= $curVal ?: '' ?>"
                           style="border-radius:8px 0 0 8px;
                                  height:38px;
                                  border:1px solid #e2e8f0;
                                  font-weight:600;
                                  color:#fd7e14">

                    <div class="input-group-append">
                        <span class="input-group-text"
                              style="border-radius:0 8px 8px 0;
                                     background:#fd7e141a;
                                     color:#fd7e14;
                                     border:1px solid #e2e8f0;
                                     font-weight:600">
                            %
                        </span>
                    </div>

                </div>

            </div>

            <?php endforeach; ?>

        </div>
    </div>

</div>
<?php endif; ?>

        <!-- Nút lưu -->
        <div class="d-flex justify-content-end pb-4" style="gap:10px">
            <a href="<?= admin_route('employees') ?>" class="btn btn-light px-4" style="border-radius:8px">Hủy</a>
            <button type="submit" class="btn btn-primary px-5" style="border-radius:8px">
                <i class="fas fa-save mr-2"></i>Lưu hoa hồng
            </button>
        </div>
    </form>

    <?php endif; ?>
</div>

<style>
.accordion .btn:focus { box-shadow: none; outline: none; }
.accordion .btn:hover { background: #f8fafc !important; }
</style>
