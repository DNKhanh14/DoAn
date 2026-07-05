<div class="container-fluid">
    <div class="page-title-bar">
        <h1>Kho hàng</h1>
        <button type="button" class="btn-them-moi" data-toggle="modal" data-target="#modalThemSanPham">
            <i class="fas fa-plus mr-1"></i> Thêm mới
        </button>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show" style="border-radius:10px; border-left:4px solid #1cc88a">
            <i class="fas fa-check-circle mr-1"></i><?= htmlspecialchars($message) ?>
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($lowStock)): ?>
    <div class="alert alert-danger" style="border-radius:10px; border-left:4px solid #e74a3b">
        <i class="fas fa-exclamation-triangle mr-2"></i><strong>Cảnh báo tồn kho thấp:</strong>
        <?php foreach ($lowStock as $p): ?>
            <span class="badge badge-danger ml-1"><?= htmlspecialchars($p['ten_san_pham']) ?> (<?= $p['so_luong_ton'] ?> <?= htmlspecialchars($p['don_vi']) ?>)</span>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Tổng quan nhanh + Tìm kiếm -->
    <div class="d-flex align-items-center justify-content-between flex-wrap mb-4" style="gap:12px">
        <div class="d-flex" style="gap:8px">
            <div class="stat-pill">
                <i class="fas fa-boxes mr-1"></i>
                <strong><?= count($products) ?></strong> sản phẩm
            </div>
            <?php if (!empty($lowStock)): ?>
            <div class="stat-pill" style="border-color:#fee2e2; color:#e74a3b">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                <strong><?= count($lowStock) ?></strong> sắp hết
            </div>
            <?php endif; ?>
        </div>
        <div class="input-group" style="max-width:320px">
            <div class="input-group-prepend">
                <span class="input-group-text bg-white border-right-0">
                    <i class="fas fa-search text-muted" style="font-size:0.85rem"></i>
                </span>
            </div>
            <input type="text" class="form-control border-left-0" placeholder="Tìm trong bảng..." id="tableSearch" value="<?= htmlspecialchars($searchQuery ?? '') ?>">
        </div>
    </div>

    <?php if (empty($products)): ?>
        <div class="card card-salon shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-3">Chưa có sản phẩm nào.</p>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalThemSanPham">
                    <i class="fas fa-plus mr-1"></i> Thêm sản phẩm đầu tiên
                </button>
            </div>
        </div>
    <?php else: ?>
        <div class="card shadow-sm" style="border-radius:10px; overflow:hidden; border:none">
            <div class="card-body p-0">
                <?php foreach ($products as $i => $p):
                    $hetHang = $p['so_luong_ton'] <= $p['so_luong_toi_thieu'];
                    $stockPct = $p['so_luong_toi_thieu'] > 0
                        ? min(100, round($p['so_luong_ton'] / ($p['so_luong_toi_thieu'] * 3) * 100))
                        : 100;
                    $stockColor = $hetHang ? '#e74a3b' : '#1cc88a';
                ?>
                <div class="inv-item d-flex align-items-center px-4 py-3 <?= $i < count($products) - 1 ? 'border-bottom' : '' ?>"
                     data-search="<?= htmlspecialchars(strtolower($p['ten_san_pham'])) ?>"
                     style="transition: background .15s;">

                    <!-- Icon -->
                    <div class="mr-3" style="width:44px; height:44px; border-radius:10px; background:<?= $stockColor ?>1a; color:<?= $stockColor ?>; display:flex; align-items:center; justify-content:center; flex-shrink:0">
                        <i class="fas fa-box"></i>
                    </div>

                    <!-- Tên -->
                    <div class="flex-grow-1 mr-3">
                        <div class="font-weight-bold" style="font-size:0.95rem; color:#2d3748">
                            <?= htmlspecialchars($p['ten_san_pham']) ?>
                        </div>
                        <div class="text-muted" style="font-size:0.78rem">
                            <?= htmlspecialchars($p['don_vi']) ?>
                        </div>
                    </div>

                    <!-- Trạng thái -->
                    <div class="mr-4 text-center" style="min-width:90px">
                        <?php if ($hetHang): ?>
                            <span class="badge text-white" style="background:#e74a3b; font-size:0.75rem; padding:4px 10px; border-radius:20px">
                                <i class="fas fa-exclamation-circle mr-1" style="font-size:0.65rem"></i>Sắp hết
                            </span>
                        <?php else: ?>
                            <span class="badge text-white" style="background:#1cc88a; font-size:0.75rem; padding:4px 10px; border-radius:20px">
                                <i class="fas fa-check-circle mr-1" style="font-size:0.65rem"></i>Còn hàng
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Tồn kho -->
                    <div class="mr-4 text-center" style="min-width:90px">
                        <div style="font-size:0.7rem; color:#9ca3af; text-transform:uppercase; letter-spacing:0.5px">Tồn kho</div>
                        <div style="font-size:1rem; font-weight:700; color:<?= $stockColor ?>; margin-top:2px">
                            <?= $p['so_luong_ton'] ?>
                            <span style="font-size:0.72rem; font-weight:400; color:#9ca3af">/ <?= $p['so_luong_toi_thieu'] ?></span>
                        </div>
                    </div>

                    <!-- Giá -->
                    <div class="mr-4 text-center" style="min-width:110px">
                        <div style="font-size:0.7rem; color:#9ca3af; text-transform:uppercase; letter-spacing:0.5px">Đơn giá</div>
                        <div style="font-size:0.95rem; font-weight:700; color:#1e5bb8; margin-top:2px">
                            <?= format_money($p['gia_ban']) ?>
                        </div>
                    </div>

                    <!-- Thao tác -->
                    <div style="flex-shrink:0; display:flex; gap:6px">
                        <button type="button" title="Sửa"
                                onclick="editProduct(<?= (int)$p['ma_san_pham'] ?>, '<?= addslashes(htmlspecialchars($p['ten_san_pham'])) ?>', '<?= addslashes(htmlspecialchars($p['don_vi'])) ?>', <?= (int)$p['gia_ban'] ?>, <?= (int)$p['gia_nhap'] ?>, <?= (int)$p['so_luong_ton'] ?>, <?= (int)$p['so_luong_toi_thieu'] ?>)"
                                style="width:34px; height:34px; border-radius:8px; background:#f6c23e; border:none; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0">
                            <i class="fas fa-edit" style="color:#fff; font-size:14px"></i>
                        </button>
                        <form method="post" action="index.php" class="d-inline"
                              onsubmit="return confirm('Xóa sản phẩm \"<?= addslashes(htmlspecialchars($p['ten_san_pham'])) ?>\"?')">
                            <input type="hidden" name="route" value="inventory">
                            <input type="hidden" name="delete_product" value="1">
                            <input type="hidden" name="ma_san_pham" value="<?= (int)$p['ma_san_pham'] ?>">
                            <button type="submit" title="Xóa"
                                    style="width:34px; height:34px; border-radius:8px; background:#e74a3b; border:none; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; flex-shrink:0">
                                <i class="fas fa-trash" style="color:#fff; font-size:14px"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($pag)): ?>
        <div class="px-1 mt-3"><?= render_pagination($pag, $baseUrl) ?></div>
    <?php endif; ?>
</div>

<!-- Modal Thêm sản phẩm -->
<div class="modal fade" id="modalThemSanPham" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none">
            <div class="modal-header" style="background:linear-gradient(135deg, #1e5bb8, #3a7bd5); border:none; padding:20px 24px">
                <div class="d-flex align-items-center">
                    <div style="width:36px; height:36px; border-radius:8px; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; margin-right:12px">
                        <i class="fas fa-box" style="color:#fff; font-size:15px"></i>
                    </div>
                    <h5 class="modal-title mb-0" style="color:#fff; font-weight:600">Thêm sản phẩm mới</h5>
                </div>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:0.8; text-shadow:none"><span>&times;</span></button>
            </div>
            <form method="post" action="index.php">
                <input type="hidden" name="route" value="inventory">
                <input type="hidden" name="save_product" value="1">
                <div class="modal-body" style="padding:24px">
                    <div class="form-group">
                        <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input name="ten_san_pham" class="form-control mt-1" required placeholder="Nhập tên sản phẩm" style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                    </div>

                    <div class="form-row">
                        <div class="col form-group">
                            <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Giá bán</label>
                            <input name="gia_ban" type="text" inputmode="numeric" class="form-control money-input mt-1" required placeholder="0" style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                        </div>
                        <div class="col form-group">
                            <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Giá vốn</label>
                            <input name="gia_nhap" type="text" inputmode="numeric" class="form-control money-input mt-1" placeholder="0" style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col form-group">
                            <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Tồn kho</label>
                            <input name="so_luong_ton" type="number" step="1" class="form-control mt-1" value="0" style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                        </div>
                        <div class="col form-group">
                            <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Tồn tối thiểu</label>
                            <input name="so_luong_toi_thieu" type="number" step="1" class="form-control mt-1" value="5" style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Đơn vị</label>
                        <input name="don_vi" class="form-control mt-1" value="chai" style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                    </div>
                </div>
                <div class="modal-footer" style="padding:16px 24px; background:#f8fafc; border-top:1px solid #e2e8f0">
                    <button type="button" class="btn btn-light px-4" data-dismiss="modal" style="border-radius:8px">Huỷ</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius:8px">
                        <i class="fas fa-plus mr-1"></i> Lưu sản phẩm
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sửa sản phẩm -->
<div class="modal fade" id="modalSuaSanPham" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none">
            <div class="modal-header" style="background:linear-gradient(135deg, #f6a623, #f6c23e); border:none; padding:20px 24px">
                <div class="d-flex align-items-center">
                    <div style="width:36px; height:36px; border-radius:8px; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; margin-right:12px">
                        <i class="fas fa-edit" style="color:#fff; font-size:15px"></i>
                    </div>
                    <h5 class="modal-title mb-0" style="color:#fff; font-weight:600">Sửa sản phẩm</h5>
                </div>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:0.8; text-shadow:none"><span>&times;</span></button>
            </div>
            <form method="post" action="index.php">
                <input type="hidden" name="route" value="inventory">
                <input type="hidden" name="save_product" value="1">
                <input type="hidden" name="ma_san_pham" id="editProductId">
                <div class="modal-body" style="padding:24px">
                    <div class="form-group">
                        <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Tên sản phẩm</label>
                        <input name="ten_san_pham" id="editProductName" class="form-control mt-1" required style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                    </div>

                    <div class="form-row">
                        <div class="col form-group">
                            <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Giá bán</label>
                            <input name="gia_ban" id="editProductSalePrice" type="text" inputmode="numeric" class="form-control money-input mt-1" required placeholder="0" style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                        </div>
                        <div class="col form-group">
                            <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Giá vốn</label>
                            <input name="gia_nhap" id="editProductCostPrice" type="text" inputmode="numeric" class="form-control money-input mt-1" placeholder="0" style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col form-group">
                            <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Tồn kho</label>
                            <input name="so_luong_ton" id="editProductStock" type="number" step="1" class="form-control mt-1" style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                        </div>
                        <div class="col form-group">
                            <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Tồn tối thiểu</label>
                            <input name="so_luong_toi_thieu" id="editProductMinStock" type="number" step="1" class="form-control mt-1" style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Đơn vị</label>
                        <input name="don_vi" id="editProductUnit" class="form-control mt-1" style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                    </div>
                </div>
                <div class="modal-footer" style="padding:16px 24px; background:#f8fafc; border-top:1px solid #e2e8f0">
                    <button type="button" class="btn btn-light px-4" data-dismiss="modal" style="border-radius:8px">Huỷ</button>
                    <button type="submit" class="btn btn-warning px-4" style="border-radius:8px; color:#fff">
                        <i class="fas fa-save mr-1"></i> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.stat-pill {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 5px 14px;
    font-size: 0.85rem;
    color: #4a5568;
    display: inline-block;
}
.inv-item:hover { background: #f8fafc; }
@media (max-width: 768px) {
    .inv-item .mr-4 { display: none; }
}
</style>

<script>
$('#tableSearch').on('keyup', function() {
    var v = $(this).val().toLowerCase();
    document.querySelectorAll('.inv-item').forEach(function(row) {
        var hay = (row.dataset.search || '') + ' ' + row.innerText.toLowerCase();
        row.style.display = (!v || hay.indexOf(v) >= 0) ? '' : 'none';
    });
});

function editProduct(id, name, sku, unit, salePrice, costPrice, stockQty, minStock) {
    $('#editProductId').val(id);
    $('#editProductName').val(name);
    $('#editProductSku').val(sku);
    $('#editProductUnit').val(unit);
    $('#editProductSalePrice').val(salePrice);
    $('#editProductCostPrice').val(costPrice);
    $('#editProductStock').val(Math.round(stockQty));
    $('#editProductMinStock').val(Math.round(minStock));
    $('#modalSuaSanPham').modal('show');
}
</script>
