<div class="container-fluid crm-easy-page">
    <div class="easy-page-head d-flex align-items-center justify-content-between flex-wrap mb-3" style="gap:8px">
        <div class="d-flex align-items-center" style="gap:10px">
            <a href="<?= admin_route('crm') ?>" class="btn btn-outline-secondary btn-sm">← Danh sách</a>
            <h1 class="mb-0" style="font-size:1.3rem"><?= htmlspecialchars($pageTitle) ?></h1>
        </div>
        <div class="d-flex align-items-center" style="gap:8px">
            <span class="badge badge-info px-3 py-2" style="font-size:0.85rem">
                <i class="fas fa-scissors mr-1"></i> <?= (int) $visitCount ?> lần dịch vụ
            </span>
            <a href="<?= admin_route('booking/create', ['client_id' => $client['ma_khach_hang']]) ?>"
               class="btn btn-outline-primary btn-sm">
                <i class="fas fa-calendar-plus mr-1"></i> Đặt lịch
            </a>
        </div>
    </div>

    <?php if ($message): ?><div class="alert alert-success py-2"><?= htmlspecialchars($message) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <div class="row">
        <!-- Form chỉnh sửa thông tin -->
        <div class="col-lg-4 col-md-5 mb-4">
            <div class="card card-salon shadow-sm h-100">
                <div class="card-header py-2"><strong>Thông tin khách hàng</strong></div>
                <div class="card-body">
                    <form method="post" action="index.php">
                        <input type="hidden" name="route" value="crm/detail">
                        <input type="hidden" name="id" value="<?= (int) $client['ma_khach_hang'] ?>">
                        <div class="form-group">
                            <label class="small font-weight-bold">Họ <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control form-control-sm" required
                                   value="<?= htmlspecialchars($client['ten']) ?>">
                        </div>
                        <div class="form-group">
                            <label class="small font-weight-bold">Tên</label>
                            <input type="text" name="last_name" class="form-control form-control-sm"
                                   value="<?= htmlspecialchars($client['ho_dem']) ?>">
                        </div>
                        <div class="form-group">
                            <label class="small font-weight-bold">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="phone_number" class="form-control form-control-sm" required
                                   pattern="\d{10}" title="Số điện thoại phải đủ 10 chữ số"
                                   value="<?= htmlspecialchars($client['so_dien_thoai'] ?? '') ?>">
                        </div>
                        <div class="d-flex" style="gap:8px">
                            <button type="submit" name="save_client" value="1" class="btn btn-primary btn-sm flex-fill">
                                <i class="fas fa-save mr-1"></i> Lưu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Lịch sử đơn hàng -->
        <div class="col-lg-8 col-md-7 mb-4">
            <div class="card card-salon shadow-sm">
                <div class="card-header py-2 d-flex justify-content-between align-items-center">
                    <strong>Lịch sử cắt tóc / mua hàng</strong>
                    <span class="badge badge-secondary"><?= count($orderHistory) ?> hóa đơn</span>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($orderHistory)): ?>
                        <p class="text-muted text-center py-4 mb-0">
                            <i class="fas fa-receipt d-block fa-2x mb-2 text-light"></i>
                            Chưa có lịch sử
                        </p>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center" style="width:45px">STT</th>
                                    <th>Mã HĐ</th>
                                    <th>Tổng tiền</th>
                                    <th>Thanh toán</th>
                                    <th>Thời gian</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderHistory as $idx => $o): ?>
                                <tr>
                                    <td class="text-center text-muted small"><?= $idx + 1 ?></td>
                                    <td>
                                        <a href="<?= admin_route('reports/order', ['id' => $o['ma_don_hang']]) ?>"
                                           class="text-primary font-weight-bold" title="Xem chi tiết">
                                            <?= htmlspecialchars($o['ma_don'] ?? ('#' . $o['ma_don_hang'])) ?>
                                        </a>
                                    </td>
                                    <td class="text-nowrap"><?= format_money($o['tong_cong']) ?></td>
                                    <td><?= htmlspecialchars(payment_method_label($o['phuong_thuc_thanh_toan'] ?? '')) ?></td>
                                    <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($o['ngay_tao'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
