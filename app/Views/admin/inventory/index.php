<div class="container-fluid">
    <div class="page-title-bar">
        <h1>Danh sách sản phẩm (Kho hàng)</h1>
        <button type="button" class="btn-them-moi" data-toggle="modal" data-target="#modalThemSanPham">
            <i class="fas fa-plus mr-1"></i> Thêm mới
        </button>
    </div>

    <?php if ($message): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>

    <?php if (!empty($searchQuery)): ?>
        <div class="alert alert-info py-2">Kết quả tìm kiếm: <strong><?= htmlspecialchars($searchQuery) ?></strong></div>
    <?php endif; ?>

    <?php if (!empty($lowStock)): ?>
    <div class="alert alert-danger">
        <strong><i class="fas fa-exclamation-triangle"></i> Cảnh báo tồn kho thấp:</strong>
        <?php foreach ($lowStock as $p): ?>
            <?= htmlspecialchars($p['ten_san_pham']) ?> (còn <?= $p['so_luong_ton'] ?> <?= htmlspecialchars($p['don_vi']) ?>);
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="card card-salon mb-4">
        <div class="card-body">
            <div class="inventory-table-toolbar">
                <input type="text" class="form-control form-control-sm" placeholder="Tìm trong bảng..." id="tableSearch" value="<?= htmlspecialchars($searchQuery ?? '') ?>">
            </div>

            <div class="table-responsive">
                <table class="table table-salon table-bordered table-hover" id="dataTable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:50px">STT</th>
                            <th>Tên sản phẩm</th>
                            <th>Đơn vị</th>
                            <th>Trạng thái</th>
                            <th>Đơn giá</th>
                            <th>Tồn kho</th>
                            <th>Tối thiểu</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr><td colspan="8" class="text-center text-muted py-4">Chưa có sản phẩm. Bấm <strong>Thêm mới</strong> để tạo.</td></tr>
                        <?php else: ?>
                            <?php foreach ($products as $i => $p): ?>
                                <?php $hetHang = $p['so_luong_ton'] <= $p['so_luong_toi_thieu']; ?>
                                <tr>
                                    <td class="text-center text-muted small"><?= $i + 1 ?></td>
                                    <td><strong><?= htmlspecialchars($p['ten_san_pham']) ?></strong><br>
                                        <small class="text-muted">SKU: <?= htmlspecialchars($p['ma_sku'] ?? '-') ?></small></td>
                                    <td><?= htmlspecialchars($p['don_vi']) ?></td>
                                    <td>
                                        <?php if ($hetHang): ?>
                                            <span class="badge badge-het-hang">Sắp hết</span>
                                        <?php else: ?>
                                            <span class="badge badge-co-hang">Còn hàng</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= format_money($p['gia_ban']) ?></td>
                                    <td><?= $p['so_luong_ton'] ?></td>
                                    <td><?= $p['so_luong_toi_thieu'] ?></td>
                                    <td>
                                        <button class="btn-action-sm btn-sua" onclick="editProduct(<?= (int) $p['ma_san_pham'] ?>, '<?= htmlspecialchars($p['ten_san_pham']) ?>', '<?= htmlspecialchars($p['ma_sku'] ?? '') ?>', '<?= htmlspecialchars($p['don_vi']) ?>', <?= (int) $p['gia_ban'] ?>, <?= (int) $p['gia_nhap'] ?>, <?= (int) $p['so_luong_ton'] ?>, <?= (int) $p['so_luong_toi_thieu'] ?>)" title="Sửa"><i class="fas fa-edit"></i></button>
                                        <form method="post" action="index.php" class="d-inline" onsubmit="return confirm('Xóa sản phẩm này?');">
                                            <input type="hidden" name="route" value="inventory">
                                            <input type="hidden" name="delete_product" value="1">
                                            <input type="hidden" name="ma_san_pham" value="<?= (int) $p['ma_san_pham'] ?>">
                                            <button type="submit" class="btn-action-sm btn-xoa" title="Xóa"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalThemSanPham" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm sản phẩm mới</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="post" action="index.php">
                <input type="hidden" name="route" value="inventory">
                <input type="hidden" name="save_product" value="1">
                <div class="modal-body">
                    <div class="form-group"><label>Tên sản phẩm <span class="text-danger">*</span></label><input name="ten_san_pham" class="form-control" required placeholder="Nhập tên sản phẩm"></div>
                    <div class="form-group"><label>Mã SKU</label><input name="ma_sku" class="form-control"></div>
                    <div class="form-row">
                        <div class="col form-group"><label>Giá bán</label><input name="gia_ban" type="text" inputmode="numeric" class="form-control money-input" required placeholder="0"></div>
                        <div class="col form-group"><label>Giá vốn</label><input name="gia_nhap" type="text" inputmode="numeric" class="form-control money-input" placeholder="0"></div>
                    </div>
                    <div class="form-row">
                        <div class="col form-group"><label>Tồn kho</label><input name="so_luong_ton" type="number" step="1" class="form-control" value="0"></div>
                        <div class="col form-group"><label>Tồn tối thiểu</label><input name="so_luong_toi_thieu" type="number" step="1" class="form-control" value="5"></div>
                    </div>
                    <div class="form-group"><label>Đơn vị</label><input name="don_vi" class="form-control" value="chai"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Huỷ</button>
                    <button type="submit" class="btn btn-primary">Lưu sản phẩm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sửa sản phẩm -->
<div class="modal fade" id="modalSuaSanPham" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sửa sản phẩm</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="post" action="index.php">
                <input type="hidden" name="route" value="inventory">
                <input type="hidden" name="save_product" value="1">
                <input type="hidden" name="ma_san_pham" id="editProductId">
                <div class="modal-body">
                    <div class="form-group"><label>Tên sản phẩm</label><input name="ten_san_pham" id="editProductName" class="form-control" required></div>
                    <div class="form-group"><label>Mã SKU</label><input name="ma_sku" id="editProductSku" class="form-control"></div>
                    <div class="form-row">
                        <div class="col form-group"><label>Giá bán</label><input name="gia_ban" id="editProductSalePrice" type="text" inputmode="numeric" class="form-control money-input" required placeholder="0"></div>
                        <div class="col form-group"><label>Giá vốn</label><input name="gia_nhap" id="editProductCostPrice" type="text" inputmode="numeric" class="form-control money-input" placeholder="0"></div>
                    </div>
                    <div class="form-row">
                        <div class="col form-group"><label>Tồn kho</label><input name="so_luong_ton" id="editProductStock" type="number" step="1" class="form-control"></div>
                        <div class="col form-group"><label>Tồn tối thiểu</label><input name="so_luong_toi_thieu" id="editProductMinStock" type="number" step="1" class="form-control"></div>
                    </div>
                    <div class="form-group"><label>Đơn vị</label><input name="don_vi" id="editProductUnit" class="form-control"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Huỷ</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$('#tableSearch').on('keyup', function() {
    var v = $(this).val().toLowerCase();
    $('#dataTable tbody tr').filter(function() { $(this).toggle($(this).text().toLowerCase().indexOf(v) > -1); });
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
