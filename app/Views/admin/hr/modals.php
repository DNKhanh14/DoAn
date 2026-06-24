<?php
$modalEmployeeId = $modalEmployeeId ?? 0;
$modalNetSalary = $modalNetSalary ?? 0;
?>
<!-- Modal Nghỉ -->
<div class="modal fade hr-modal" id="hrLeaveModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nghỉ</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="hrLeaveForm">
                <div class="modal-body">
                    <input type="hidden" name="employee_id" id="hrLeaveEmployeeId" value="<?= (int) $modalEmployeeId ?>">
                    <div class="hr-form-row">
                        <label class="hr-label"><span class="text-danger">*</span> Ngày nghỉ:</label>
                        <div class="hr-field-group">
                            <div class="input-group">
                                <input type="date" class="form-control" name="date_from" id="hrLeaveFrom" value="<?= date('Y-m-d') ?>" required>
                                <div class="input-group-prepend input-group-append"><span class="input-group-text">~</span></div>
                                <input type="date" class="form-control" name="date_to" id="hrLeaveTo" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="hr-leave-count mt-2">Số ngày nghỉ: <strong id="hrLeaveDaysCount">1</strong></div>
                        </div>
                    </div>
                    <div class="hr-form-row">
                        <label class="hr-label">Ghi chú:</label>
                        <textarea class="form-control" name="note" rows="3" placeholder="Lý do nghỉ..."></textarea>
                    </div>
                    <div class="hr-form-row">
                        <label class="hr-label">Trạng thái:</label>
                        <div>
                            <label class="hr-check d-block">
                                <input type="checkbox" name="is_authorized" id="hrLeaveAuthorized" value="1" checked>
                                Nghỉ có xin phép
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Huỷ</button>
                    <button type="submit" class="btn btn-primary">OK</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Thưởng/phạt -->
<div class="modal fade hr-modal" id="hrBonusModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm thưởng/phạt</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="hrBonusForm">
                <div class="modal-body">
                    <input type="hidden" name="employee_id" id="hrBonusEmployeeId" value="<?= (int) $modalEmployeeId ?>">
                    <div class="hr-form-row">
                        <label class="hr-label"><span class="text-danger">*</span> Ngày:</label>
                        <input type="date" class="form-control" name="record_date" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="hr-form-row">
                        <label class="hr-label">Loại:</label>
                        <div class="hr-toggle-group" id="hrBonusTypeGroup">
                            <button type="button" class="hr-toggle-btn active" data-value="bonus">Thưởng</button>
                            <button type="button" class="hr-toggle-btn" data-value="penalty">Phạt</button>
                        </div>
                        <input type="hidden" name="record_type" id="hrBonusType" value="bonus">
                    </div>
                    <div class="hr-form-row">
                        <label class="hr-label"><span class="text-danger">*</span> Danh mục:</label>
                        <select class="form-control" name="category" id="hrBonusCategory" required>
                            <option value="">Chọn danh mục</option>
                            <?php foreach ($bonusCategories['bonus'] as $cat): ?>
                                <option value="<?= htmlspecialchars($cat) ?>" data-type="bonus"><?= htmlspecialchars($cat) ?></option>
                            <?php endforeach; ?>
                            <?php foreach ($bonusCategories['penalty'] as $cat): ?>
                                <option value="<?= htmlspecialchars($cat) ?>" data-type="penalty" hidden><?= htmlspecialchars($cat) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="hr-form-row">
                        <label class="hr-label"><span class="text-danger">*</span> Số tiền:</label>
                        <input type="text" inputmode="numeric" class="form-control money-input" name="amount" placeholder="Nhập số tiền" required
                               data-min="1000">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Huỷ</button>
                    <button type="submit" class="btn btn-primary">OK</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Thanh toán -->
<div class="modal fade hr-modal" id="hrPaymentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thanh toán</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="hrPaymentForm">
                <div class="modal-body">
                    <input type="hidden" name="employee_id" id="hrPaymentEmployeeId" value="<?= (int) $modalEmployeeId ?>">
                    <div class="hr-form-row">
                        <label class="hr-label">Loại:</label>
                        <div class="hr-toggle-group" id="hrPaymentTypeGroup">
                            <button type="button" class="hr-toggle-btn active" data-value="advance">Tạm ứng</button>
                            <button type="button" class="hr-toggle-btn" data-value="salary">Trả lương</button>
                        </div>
                        <input type="hidden" name="payment_type" id="hrPaymentType" value="advance">
                    </div>
                    <div class="hr-form-row hr-salary-period-row d-none">
                        <label class="hr-label">Kỳ lương:</label>
                        <input type="month" class="form-control" name="salary_period" id="hrSalaryPeriod" value="<?= date('Y-m') ?>">
                    </div>
                    <div class="hr-form-row">
                        <label class="hr-label"><span class="text-danger">*</span> Phương thức:</label>
                        <select class="form-control" name="payment_method" required>
                            <option value="cash">Tiền mặt</option>
                            <option value="transfer">Chuyển khoản</option>
                            <option value="card">Thẻ</option>
                        </select>
                    </div>
                    <div class="hr-form-row">
                        <label class="hr-label"><span class="text-danger">*</span> Số tiền:</label>
                        <div class="hr-amount-wrap">
                            <input type="text" inputmode="numeric" class="form-control money-input" name="amount" id="hrPaymentAmount" placeholder="Nhập số tiền" required data-min="1000">
                            <span class="hr-amount-badge" id="hrPaymentNetBadge"><?= format_vnd($modalNetSalary) ?></span>
                        </div>
                    </div>
                    <div class="hr-form-row hr-remaining-row d-none">
                        <label class="hr-label">Còn lại:</label>
                        <div>
                            <input type="text" class="form-control" id="hrPaymentRemaining" readonly value="0">
                            <small class="text-muted">Số này sẽ được tính vào lương tồn</small>
                        </div>
                    </div>
                    <div class="hr-form-row">
                        <label class="hr-label">Ghi chú:</label>
                        <textarea class="form-control" name="note" rows="2" placeholder="Nhập ghi chú"></textarea>
                    </div>
                    <div class="hr-form-row">
                        <label class="hr-label">Thời gian:</label>
                        <input type="date" class="form-control" name="payment_date" value="<?= date('Y-m-d') ?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Huỷ</button>
                    <button type="submit" class="btn btn-primary">OK</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Cài đặt lương -->
<div class="modal fade hr-modal" id="hrSettingsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cài đặt lương</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="hrSettingsForm">
                <div class="modal-body">
                    <input type="hidden" name="employee_id" id="hrSettingsEmployeeId" value="<?= (int) $modalEmployeeId ?>">
                    <div class="form-group">
                        <label>Lương cơ bản (tháng)</label>
                        <input type="text" inputmode="numeric" class="form-control money-input" name="base_salary" id="hrBaseSalary" placeholder="VD: 2.000.000" value="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Huỷ</button>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>
