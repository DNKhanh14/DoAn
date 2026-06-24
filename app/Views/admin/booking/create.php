<?php
$preselectClientId = (int) ($preselectClientId ?? 0);
$preClient = $preselectClient ?? null;
$preClientName  = $preClient ? trim(($preClient['ten'] ?? '') . ' ' . ($preClient['ho_dem'] ?? '')) : '';
$preClientPhone = $preClient ? ($preClient['so_dien_thoai'] ?? '') : '';
?>

<div class="container-fluid booking-create-wrap">
    <div class="page-title-bar">
        <h1>Đặt lịch hẹn</h1>
        <a href="<?= admin_route('booking') ?>" class="btn btn-outline-primary btn-sm">← Danh sách lịch</a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-<?= $message['type'] ?>"><?= htmlspecialchars($message['text']) ?></div>
    <?php endif; ?>

    <form method="post" id="bookingForm">
        <input type="hidden" name="save_booking" value="1">
        <input type="hidden" name="client_id" id="bookingClientId" value="<?= $preselectClientId ?>">
        <input type="hidden" name="client_name" id="bookingClientName" value="<?= htmlspecialchars($preClientName) ?>">
        <input type="hidden" name="start_time" id="bookingStartTime">
        <input type="hidden" name="booking_source" value="hotline">

        <div class="booking-create-grid">
            <div class="booking-panel booking-panel-left">
                <h3 class="booking-section-title">Thông tin khách hàng</h3>
                <?php if ($preClient): ?>
                <div class="alert alert-success py-2 mb-3 d-flex align-items-center justify-content-between">
                    <span><i class="fas fa-user-check mr-2"></i><strong><?= htmlspecialchars($preClientName) ?></strong>
                        <?php if ($preClientPhone): ?> — <?= htmlspecialchars($preClientPhone) ?><?php endif; ?>
                    </span>
                    <a href="<?= admin_route('booking/create') ?>" class="btn btn-sm btn-outline-secondary ml-2">Đổi khách</a>
                </div>
                <?php endif; ?>
                <div class="form-group">
                    <label>Tên KH hoặc Mã KH</label>
                    <div class="booking-client-search">
                        <input type="text" class="form-control" id="bookingClientSearch"
                               placeholder="Tìm theo tên hoặc số điện thoại..."
                               autocomplete="off"
                               value="<?= htmlspecialchars($preClientName) ?>">
                        <div class="booking-client-dropdown" id="bookingClientDropdown"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Số điện thoại</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="client_phone" id="bookingPhone"
                               placeholder="Số điện thoại (10 số)"
                               pattern="\d{10}" title="Số điện thoại phải đủ 10 chữ số"
                               value="<?= htmlspecialchars($preClientPhone) ?>">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" id="bookingNewClientBtn" title="Tạo khách mới"><i class="fas fa-user-plus"></i></button>
                        </div>
                    </div>
                    <small class="<?= $preselectClientId ? 'text-success' : 'text-muted' ?>" id="bookingClientHint">
                        <?= $preselectClientId ? 'Đã chọn khách #' . $preselectClientId : 'Tìm khách hoặc nhập SĐT + tên để tạo mới' ?>
                    </small>
                </div>

                <h3 class="booking-section-title mt-4">Thông tin lịch hẹn</h3>
                <div class="form-row">
                    <div class="form-group col-6">
                        <label>Ngày</label>
                        <input type="date" id="bookingDateOnly" class="form-control" required>
                    </div>
                    <div class="form-group col-6">
                        <label>Giờ</label>
                        <input type="time" id="bookingTimeOnly" class="form-control" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Số khách</label>
                    <input type="number" class="form-control" name="guest_count" value="1" min="1" max="10" id="bookingGuests">
                </div>
                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="status" class="form-control">
                        <option value="confirmed">Đã xác nhận</option>
                        <option value="pending">Chờ xác nhận</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Ghi chú</label>
                    <textarea name="admin_note" class="form-control" rows="3" placeholder="Nhập ghi chú"></textarea>
                </div>
              
            </div>

            <div class="booking-guests-area" id="guestPanels">
                <div class="booking-right-title">Dịch vụ theo từng khách</div>
            </div>
        </div>

        <div class="booking-form-footer">
            <a href="<?= admin_route('booking') ?>" class="btn btn-outline-secondary btn-lg">Hủy</a>
            <button type="submit" class="btn btn-primary btn-lg">Lưu</button>
        </div>
    </form>
</div>

<script>
window.BOOKING_CONFIG = {
    categories: <?= json_encode($categoriesJson ?? [], JSON_UNESCAPED_UNICODE) ?>,
    employees: <?= json_encode($employeesJson ?? [], JSON_UNESCAPED_UNICODE) ?>,
    preselectClient: <?= json_encode($preselectClient ? [
        'id'    => (int) $preselectClient['ma_khach_hang'],
        'name'  => trim(($preselectClient['ten'] ?? '') . ' ' . ($preselectClient['ho_dem'] ?? '')),
        'phone' => $preselectClient['so_dien_thoai'] ?? '',
    ] : null, JSON_UNESCAPED_UNICODE) ?>
};
</script>
<script src="Design/js/booking-create.js"></script>
