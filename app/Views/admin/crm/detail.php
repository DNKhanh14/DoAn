<div class="container-fluid">
    <div class="page-title-bar">
        <h1><?= htmlspecialchars($pageTitle) ?></h1>
        <div class="d-flex align-items-center" style="gap:8px">
            <span class="badge text-white" style="background:#17a2b8; font-size:0.82rem; padding:6px 14px; border-radius:20px">
                <i class="fas fa-scissors mr-1"></i><?= (int)$visitCount ?> lần dịch vụ
            </span>
            <a href="<?= admin_route('booking/create', ['client_id' => $client['ma_khach_hang']]) ?>"
               class="btn btn-sm" style="border-radius:8px; border:1px solid #1e5bb8; color:#1e5bb8; background:#fff; padding:6px 14px">
                <i class="fas fa-calendar-plus mr-1"></i>Đặt lịch
            </a>
            <a href="<?= admin_route('crm') ?>" class="btn btn-outline-secondary btn-sm" style="border-radius:8px">
                <i class="fas fa-arrow-left mr-1"></i>Danh sách
            </a>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-success py-2" style="border-radius:10px; border-left:4px solid #1cc88a">
            <i class="fas fa-check-circle mr-1"></i><?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger py-2" style="border-radius:10px; border-left:4px solid #e74a3b">
            <i class="fas fa-exclamation-circle mr-1"></i><?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Cột trái: thông tin khách -->
        <div class="col-lg-4 col-md-5 mb-4">
            <div class="card shadow-sm" style="border-radius:12px; overflow:hidden; border:none; height:100%">
                <!-- Avatar header -->
                <div style="background:linear-gradient(135deg, #1e5bb8, #3a7bd5); padding:24px; text-align:center">
                    <div style="width:72px; height:72px; border-radius:50%; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; margin:0 auto 12px; font-size:1.8rem; font-weight:700; color:#fff">
                        <?= strtoupper(mb_substr($client['ten'], 0, 1)) ?>
                    </div>
                    <div style="color:#fff; font-size:1.1rem; font-weight:600">
                        <?= htmlspecialchars(trim($client['ten'] . ' ' . $client['ho_dem'])) ?>
                    </div>
                    <div style="color:rgba(255,255,255,0.75); font-size:0.82rem; margin-top:4px">
                        <i class="fas fa-phone mr-1"></i><?= htmlspecialchars($client['so_dien_thoai'] ?? '—') ?>
                    </div>
                </div>

                <div class="card-body" style="padding:24px">
                    <form method="post" action="index.php">
                        <input type="hidden" name="route" value="crm/detail">
                        <input type="hidden" name="id" value="<?= (int)$client['ma_khach_hang'] ?>">

                        <div class="form-group">
                            <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Họ <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control mt-1" required
                                   value="<?= htmlspecialchars($client['ten']) ?>"
                                   style="border-radius:8px; height:40px; border:1px solid #e2e8f0">
                        </div>
                        <div class="form-group">
                            <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Tên</label>
                            <input type="text" name="last_name" class="form-control mt-1"
                                   value="<?= htmlspecialchars($client['ho_dem']) ?>"
                                   style="border-radius:8px; height:40px; border:1px solid #e2e8f0">
                        </div>
                        <div class="form-group">
                            <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="phone_number" class="form-control mt-1" required
                                   pattern="\d{10}" title="Số điện thoại phải đủ 10 chữ số"
                                   value="<?= htmlspecialchars($client['so_dien_thoai'] ?? '') ?>"
                                   style="border-radius:8px; height:40px; border:1px solid #e2e8f0">
                        </div>

                        <button type="submit" name="save_client" value="1"
                                class="btn btn-primary btn-block mt-2" style="border-radius:8px">
                            <i class="fas fa-save mr-1"></i>Lưu thông tin
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Cột phải: lịch sử đơn hàng -->
        <div class="col-lg-8 col-md-7 mb-4">
            <div class="card shadow-sm" style="border-radius:12px; overflow:hidden; border:none">
                <div class="card-header py-3 px-4 d-flex align-items-center justify-content-between"
                     style="background:#fff; border-bottom:1px solid #e2e8f0">
                    <div class="d-flex align-items-center" style="gap:10px">
                        <div style="width:36px; height:36px; border-radius:8px; background:#6f42c11a; color:#6f42c1; display:flex; align-items:center; justify-content:center">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <span class="font-weight-bold" style="color:#2d3748">Lịch sử cắt tóc / mua hàng</span>
                    </div>
                    <span class="badge text-white" style="background:#6f42c1; font-size:0.75rem; padding:4px 10px; border-radius:20px">
                        <?= count($orderHistory) ?> hóa đơn
                    </span>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($orderHistory)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-receipt fa-3x text-muted mb-3" style="opacity:0.3"></i>
                            <p class="text-muted mb-0">Chưa có lịch sử giao dịch</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($orderHistory as $idx => $o): ?>
                        <div class="d-flex align-items-center px-4 py-3 <?= $idx < count($orderHistory)-1 ? 'border-bottom' : '' ?>"
                             style="transition:background .15s"
                             onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                            <!-- STT -->
                            <div style="width:28px; height:28px; border-radius:50%; background:#6f42c115; color:#6f42c1; font-size:0.75rem; font-weight:700; line-height:28px; text-align:center; flex-shrink:0; margin-right:12px">
                                <?= $idx + 1 ?>
                            </div>
                            <!-- Mã HĐ -->
                            <div class="flex-grow-1 mr-3">
                                <a href="<?= admin_route('reports/order', ['id' => $o['ma_don_hang']]) ?>"
                                   style="color:#6f42c1; font-weight:700; text-decoration:none; font-size:0.9rem">
                                    <?= htmlspecialchars($o['ma_don'] ?? ('#' . $o['ma_don_hang'])) ?>
                                </a>
                                <div class="text-muted" style="font-size:0.78rem; margin-top:2px">
                                    <i class="fas fa-clock mr-1" style="font-size:0.65rem"></i><?= date('d/m/Y H:i', strtotime($o['ngay_tao'])) ?>
                                </div>
                            </div>
                            <!-- Thanh toán -->
                            <div class="text-center mr-4" style="min-width:100px">
                                <div style="font-size:0.7rem; color:#9ca3af; text-transform:uppercase; letter-spacing:0.5px">Thanh toán</div>
                                <div style="font-size:0.82rem; color:#4a5568; margin-top:2px">
                                    <?= htmlspecialchars(payment_method_label($o['phuong_thuc_thanh_toan'] ?? '')) ?>
                                </div>
                            </div>
                            <!-- Tổng tiền -->
                            <div class="text-right" style="min-width:110px">
                                <div style="font-size:0.7rem; color:#9ca3af; text-transform:uppercase; letter-spacing:0.5px">Tổng</div>
                                <div style="font-size:1rem; font-weight:700; color:#6f42c1; margin-top:2px">
                                    <?= format_money($o['tong_cong']) ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <!-- Tổng chi tiêu -->
                        <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center"
                             style="background:#f8fafc">
                            <span class="text-muted small">Tổng chi tiêu</span>
                            <span style="font-size:1.1rem; font-weight:700; color:#1e5bb8">
                                <?= format_money(array_sum(array_column($orderHistory, 'tong_cong'))) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
