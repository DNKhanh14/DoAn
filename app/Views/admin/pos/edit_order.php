<?php
$payLabels = ['cash' => 'Tiền mặt', 'transfer' => 'Chuyển khoản / VietQR', 'card' => 'Thẻ'];
$orderCode  = $order['ma_don'] ?? ('HD' . str_pad((string) $order['ma_don_hang'], 6, '0', STR_PAD_LEFT));
$clientName = trim(($order['ten'] ?? '') . ' ' . ($order['ho_dem'] ?? '')) ?: 'Khách lẻ';
?>

<div class="container-fluid">
    <div class="page-title-bar" style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
        <a href="<?= admin_route('pos/orders') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Danh sách đơn hàng
        </a>
        <h1 style="margin:0;font-size:1.2rem">Sửa hóa đơn: <strong><?= htmlspecialchars($orderCode) ?></strong></h1>
    </div>

    <div class="row mt-3">
        <div class="col-lg-7">
            <div class="card shadow-sm mb-4" style="border-radius:10px;border:none">
                <div class="card-header" style="background:#f8fafc;border-radius:10px 10px 0 0;border-bottom:1px solid #e2e8f0;padding:14px 20px">
                    <span style="font-weight:600;font-size:0.95rem;color:#2d3748">
                        <i class="fas fa-edit mr-2 text-primary"></i>Chỉnh sửa thông tin
                    </span>
                </div>
                <div class="card-body" style="padding:24px">
                    <form method="POST" action="<?= admin_route('pos/edit', ['id' => $order['ma_don_hang']]) ?>">
                        <input type="hidden" name="save_order" value="1">
                        <input type="hidden" name="ma_don_hang" value="<?= (int) $order['ma_don_hang'] ?>">

                        <div class="form-group">
                            <label class="small font-weight-bold text-muted" style="text-transform:uppercase;letter-spacing:.5px">Mã hóa đơn</label>
                            <input type="text" class="form-control mt-1" value="<?= htmlspecialchars($orderCode) ?>" disabled
                                   style="border-radius:8px;background:#f8fafc;border:1px solid #e2e8f0">
                        </div>

                        <div class="form-group">
                            <label class="small font-weight-bold text-muted" style="text-transform:uppercase;letter-spacing:.5px">Khách hàng</label>
                            <input type="text" class="form-control mt-1" value="<?= htmlspecialchars($clientName) ?>" disabled
                                   style="border-radius:8px;background:#f8fafc;border:1px solid #e2e8f0">
                        </div>

                        <div class="form-group">
                            <label class="small font-weight-bold text-muted" style="text-transform:uppercase;letter-spacing:.5px">
                                Phương thức thanh toán <span class="text-danger">*</span>
                            </label>
                            <select name="phuong_thuc_thanh_toan" class="form-control mt-1"
                                    style="border-radius:8px;height:42px;border:1px solid #e2e8f0">
                                <?php foreach ($payLabels as $val => $label): ?>
                                    <option value="<?= $val ?>" <?= ($order['phuong_thuc_thanh_toan'] ?? 'cash') === $val ? 'selected' : '' ?>>
                                        <?= $label ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-muted" style="text-transform:uppercase;letter-spacing:.5px">Ghi chú</label>
                            <textarea name="ghi_chu" class="form-control mt-1" rows="3"
                                      placeholder="Ghi chú thêm về hóa đơn..."
                                      style="border-radius:8px;border:1px solid #e2e8f0;resize:none"><?= htmlspecialchars($order['ghi_chu'] ?? '') ?></textarea>
                        </div>

                        <div class="d-flex" style="gap:10px">
                            <button type="submit" class="btn btn-primary px-4" style="border-radius:8px">
                                <i class="fas fa-save mr-2"></i>Lưu thay đổi
                            </button>
                            <a href="<?= admin_route('pos/orders') ?>" class="btn btn-outline-secondary px-4" style="border-radius:8px">
                                Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm mb-4" style="border-radius:10px;border:none">
                <div class="card-header" style="background:#f8fafc;border-radius:10px 10px 0 0;border-bottom:1px solid #e2e8f0;padding:14px 20px">
                    <span style="font-weight:600;font-size:0.95rem;color:#2d3748">
                        <i class="fas fa-list-ul mr-2 text-primary"></i>Chi tiết đơn hàng
                    </span>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($items)): ?>
                        <p class="text-muted text-center py-3 small">Không có chi tiết</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0" style="font-size:0.85rem">
                                <thead style="background:#f8fafc">
                                    <tr>
                                        <th class="pl-3 border-top-0" style="color:#718096;font-size:0.75rem;font-weight:600">Dịch vụ / Sản phẩm</th>
                                        <th class="text-right pr-3 border-top-0" style="color:#718096;font-size:0.75rem;font-weight:600">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($items as $it): ?>
                                    <tr>
                                        <td class="pl-3 align-middle">
                                            <div style="font-weight:500"><?= htmlspecialchars($it['ten']) ?></div>
                                            <?php if (!empty($it['emp_fname'])): ?>
                                                <div style="font-size:0.75rem;color:#718096">
                                                    NV: <?= htmlspecialchars(trim($it['emp_fname'] . ' ' . ($it['emp_lname'] ?? ''))) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right pr-3 align-middle font-weight-bold"><?= format_money($it['tong_dong']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="px-3 py-2 border-top d-flex justify-content-between font-weight-bold" style="font-size:0.9rem">
                            <span>Tổng cộng</span>
                            <span style="color:#1cc88a"><?= format_money($order['tong_cong']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
