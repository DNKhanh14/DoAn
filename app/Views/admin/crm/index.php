<div class="container-fluid crm-easy-page">
    <div class="crm-page-head">
        <h1 class="crm-page-title">Khách hàng</h1>
            <div class="crm-head-toolbar" style="display:flex; align-items:center; gap:8px;">
                <form method="get" action="index.php" class="crm-search-form" style="flex:1;">
                    <input type="hidden" name="route" value="crm">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" name="q" class="form-control" placeholder="Tìm theo tên, SĐT..." value="<?= htmlspecialchars($searchQuery ?? '') ?>">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-outline-primary">Tìm</button>
                        </div>
                    </div>
                </form>
                    <button type="button" class="btn btn-primary btn-sm flex-shrink-0" data-toggle="modal" data-target="#modalThemKhach">
                        <i class="fas fa-user-plus mr-1"></i> Thêm khách hàng
                    </button>
            </div>
        </div>

    <?php if (($_GET['msg'] ?? '') === 'deleted'): ?>
        <div class="alert alert-success py-2">Đã xóa khách hàng.</div>
    <?php endif; ?>
    <?php if (!empty($searchQuery)): ?>
        <div class="alert alert-info py-2 mb-3">Kết quả: <strong><?= htmlspecialchars($searchQuery) ?></strong> — <?= count($clients) ?> khách</div>
    <?php endif; ?>

    <div class="easy-panel">
        <div class="table-responsive">
            <table class="table easy-table" id="crmTable">
                <thead>
                    <tr>
                        <th style="width:50px" class="text-center">STT</th>
                        <th>Mã KH</th>
                        <th>Họ tên</th>
                        <th>Điện thoại</th>
                        <th class="text-right" style="width:160px">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($clients)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Không có khách hàng</td></tr>
                    <?php else: ?>
                    <?php foreach ($clients as $i => $c):
                        $code = client_display_code($c);
                        $name = trim($c['ten'] . ' ' . $c['ho_dem']);
                    ?>
                    <tr>
                        <td class="text-center text-muted small"><?= $i + 1 ?></td>
                        <td>
                            <a href="<?= admin_route('crm/detail', ['id' => $c['ma_khach_hang']]) ?>"
                               class="font-weight-bold text-primary" title="Xem lịch sử">
                                <?= htmlspecialchars($code) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($name) ?></td>
                        <td><?= htmlspecialchars($c['so_dien_thoai'] ?? '') ?></td>
                        <td class="text-right text-nowrap">
                            <a class="btn btn-warning btn-sm" href="<?= admin_route('crm/detail', ['id' => $c['ma_khach_hang']]) ?>" title="Sửa">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <a class="btn btn-outline-primary btn-sm" href="<?= admin_route('booking/create', ['client_id' => $c['ma_khach_hang']]) ?>" title="Đặt lịch">
                                <i class="fas fa-calendar-plus"></i>
                            </a>
                            <button class="btn btn-danger btn-sm" type="button"
                                    data-toggle="modal" data-target="#delClient<?= (int) $c['ma_khach_hang'] ?>" title="Xóa">
                                <i class="fas fa-trash"></i>
                            </button>

                            <!-- Modal xác nhận xóa -->
                            <div class="modal fade" id="delClient<?= (int) $c['ma_khach_hang'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content">
                                        <div class="modal-header py-2">
                                            <h6 class="modal-title">Xóa khách hàng</h6>
                                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                        </div>
                                        <div class="modal-body py-2">
                                            Xóa <strong><?= htmlspecialchars($name) ?></strong>?
                                        </div>
                                        <div class="modal-footer py-2">
                                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Hủy</button>
                                            <form method="post" action="index.php" class="d-inline">
                                                <input type="hidden" name="route" value="crm">
                                                <input type="hidden" name="delete_client_id" value="<?= (int) $c['ma_khach_hang'] ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal thêm khách hàng -->
<div class="modal fade" id="modalThemKhach" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm khách hàng</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="crmAddForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Họ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label>Tên</label>
                        <input type="text" class="form-control" name="last_name">
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="phone" required
                               pattern="\d{10}" title="Số điện thoại phải đủ 10 chữ số">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Huỷ</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    document.getElementById('crmAddForm')?.addEventListener('submit', function (e) {
        e.preventDefault();
        const fd = new FormData(this);
        fd.append('action', 'quick_client');
        fetch('index.php?route=ajax/pos', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(function (data) {
                if (data.client) location.reload();
                else alert(data.error || 'Không thêm được khách');
            });
    });
})();
</script>
