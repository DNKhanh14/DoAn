<?php
$modalEmployeeId = $modalEmployeeId ?? 0;
$modalNetSalary  = $modalNetSalary  ?? 0;
?>

<!-- Modal Nghỉ -->
<div class="modal fade" id="hrLeaveModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none">
            <div class="modal-header" style="background:linear-gradient(135deg, #f6a623, #f6c23e); border:none; padding:20px 24px">
                <div class="d-flex align-items-center">
                    <div style="width:36px; height:36px; border-radius:8px; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; margin-right:12px">
                        <i class="fas fa-umbrella-beach" style="color:#fff; font-size:15px"></i>
                    </div>
                    <h5 class="modal-title mb-0" style="color:#fff; font-weight:600">Đăng ký nghỉ</h5>
                </div>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:0.8; text-shadow:none"><span>&times;</span></button>
            </div>
            <form id="hrLeaveForm">
                <div class="modal-body" style="padding:24px">
                    <input type="hidden" name="employee_id" id="hrLeaveEmployeeId" value="<?= (int)$modalEmployeeId ?>">
                    <div class="form-group">
                        <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Ngày nghỉ <span class="text-danger">*</span></label>
                        <div class="input-group mt-1">
                            <input type="date" class="form-control" name="date_from" id="hrLeaveFrom" value="<?= date('Y-m-d') ?>" required
                                   style="border-radius:8px 0 0 8px; height:42px; border:1px solid #e2e8f0">
                            <div class="input-group-prepend input-group-append">
                                <span class="input-group-text" style="border:1px solid #e2e8f0; background:#f8fafc">~</span>
                            </div>
                            <input type="date" class="form-control" name="date_to" id="hrLeaveTo" value="<?= date('Y-m-d') ?>" required
                                   style="border-radius:0 8px 8px 0; height:42px; border:1px solid #e2e8f0">
                        </div>
                        <div class="mt-1 text-muted small">Số ngày nghỉ: <strong id="hrLeaveDaysCount">1</strong></div>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Ghi chú</label>
                        <textarea class="form-control mt-1" name="note" rows="3" placeholder="Lý do nghỉ..."
                                  style="border-radius:8px; border:1px solid #e2e8f0"></textarea>
                    </div>
                    <div class="form-group mb-0">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" name="is_authorized" id="hrLeaveAuthorized" value="1" checked>
                            <label class="custom-control-label" for="hrLeaveAuthorized">Nghỉ có xin phép</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="padding:16px 24px; background:#f8fafc; border-top:1px solid #e2e8f0">
                    <button type="button" class="btn btn-light px-4" data-dismiss="modal" style="border-radius:8px">Huỷ</button>
                    <button type="submit" class="btn btn-warning px-4" style="border-radius:8px; color:#fff">
                        <i class="fas fa-check mr-1"></i>Xác nhận
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Thưởng/phạt -->
<div class="modal fade" id="hrBonusModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none">
            <div class="modal-header" style="background:linear-gradient(135deg, #1cc88a, #20c9a6); border:none; padding:20px 24px">
                <div class="d-flex align-items-center">
                    <div style="width:36px; height:36px; border-radius:8px; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; margin-right:12px">
                        <i class="fas fa-gift" style="color:#fff; font-size:15px"></i>
                    </div>
                    <h5 class="modal-title mb-0" style="color:#fff; font-weight:600">Thêm thưởng / phạt</h5>
                </div>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:0.8; text-shadow:none"><span>&times;</span></button>
            </div>
            <form id="hrBonusForm">
                <div class="modal-body" style="padding:24px">
                    <input type="hidden" name="employee_id" id="hrBonusEmployeeId" value="<?= (int)$modalEmployeeId ?>">
                    <div class="form-group">
                        <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Ngày <span class="text-danger">*</span></label>
                        <input type="date" class="form-control mt-1" name="record_date" value="<?= date('Y-m-d') ?>" required
                               style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Loại</label>
                        <div class="hr-toggle-group mt-1" id="hrBonusTypeGroup">
                            <button type="button" class="hr-toggle-btn active" data-value="bonus">Thưởng</button>
                            <button type="button" class="hr-toggle-btn" data-value="penalty">Phạt</button>
                        </div>
                        <input type="hidden" name="record_type" id="hrBonusType" value="bonus">
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Danh mục <span class="text-danger">*</span></label>
                        <select class="form-control mt-1" name="category" id="hrBonusCategory" required
                                style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                            <option value="">Chọn danh mục</option>
                            <?php foreach ($bonusCategories['bonus'] as $cat): ?>
                                <option value="<?= htmlspecialchars($cat) ?>" data-type="bonus"><?= htmlspecialchars($cat) ?></option>
                            <?php endforeach; ?>
                            <?php foreach ($bonusCategories['penalty'] as $cat): ?>
                                <option value="<?= htmlspecialchars($cat) ?>" data-type="penalty" hidden><?= htmlspecialchars($cat) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Số tiền <span class="text-danger">*</span></label>
                        <input type="text" inputmode="numeric" class="form-control money-input mt-1" name="amount"
                               placeholder="Nhập số tiền" required data-min="1000"
                               style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                    </div>
                </div>
                <div class="modal-footer" style="padding:16px 24px; background:#f8fafc; border-top:1px solid #e2e8f0">
                    <button type="button" class="btn btn-light px-4" data-dismiss="modal" style="border-radius:8px">Huỷ</button>
                    <button type="submit" class="btn btn-success px-4" style="border-radius:8px">
                        <i class="fas fa-check mr-1"></i>Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Thanh toán lương -->
<div class="modal fade" id="hrPaymentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none">
            <div class="modal-header" style="background:linear-gradient(135deg, #1e5bb8, #3a7bd5); border:none; padding:20px 24px">
                <div class="d-flex align-items-center">
                    <div style="width:36px; height:36px; border-radius:8px; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; margin-right:12px">
                        <i class="fas fa-money-bill-wave" style="color:#fff; font-size:15px"></i>
                    </div>
                    <h5 class="modal-title mb-0" style="color:#fff; font-weight:600">Thanh toán lương</h5>
                </div>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:0.8; text-shadow:none"><span>&times;</span></button>
            </div>
            <form id="hrPaymentForm">
                <div class="modal-body" style="padding:24px">
                    <input type="hidden" name="employee_id" id="hrPaymentEmployeeId" value="<?= (int)$modalEmployeeId ?>">
                    <div class="form-group">
                        <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Loại</label>
                        <div class="hr-toggle-group mt-1" id="hrPaymentTypeGroup">
                            <button type="button" class="hr-toggle-btn active" data-value="advance">Tạm ứng</button>
                            <button type="button" class="hr-toggle-btn" data-value="salary">Trả lương</button>
                        </div>
                        <input type="hidden" name="payment_type" id="hrPaymentType" value="advance">
                    </div>
                    <div class="form-group hr-salary-period-row d-none">
                        <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Kỳ lương</label>
                        <input type="month" class="form-control mt-1" name="salary_period" id="hrSalaryPeriod" value="<?= date('Y-m') ?>"
                               style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Phương thức <span class="text-danger">*</span></label>
                        <select class="form-control mt-1" name="payment_method" required
                                style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                            <option value="cash">Tiền mặt</option>
                            <option value="transfer">Chuyển khoản</option>
                            <option value="card">Thẻ</option>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Số tiền <span class="text-danger">*</span></label>
                        <div class="input-group mt-1">
                            <input type="text" inputmode="numeric" class="form-control money-input" name="amount"
                                   id="hrPaymentAmount" placeholder="Nhập số tiền" required data-min="1000"
                                   style="border-radius:8px 0 0 8px; height:42px; border:1px solid #e2e8f0">
                            <div class="input-group-append">
                                <span class="input-group-text" id="hrPaymentNetBadge"
                                      title="Tiền còn lại phải trả nhân viên tháng này — click để điền nhanh"
                                      style="border-radius:0 8px 8px 0; background:#1e5bb81a; color:#1e5bb8; border:1px solid #e2e8f0; font-weight:600; font-size:0.82rem; cursor:pointer; white-space:nowrap">
                                    <?= format_vnd($modalNetSalary) ?>
                                </span>
                            </div>
                        </div>
                        <small class="text-muted mt-1 d-block">
                            <i class="fas fa-info-circle mr-1"></i>
                            Số badge = tổng còn nợ nhân viên. Click vào để điền nhanh.
                        </small>
                    </div>
                    <div class="form-group hr-remaining-row d-none mt-3">
                        <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Còn lại</label>
                        <input type="text" class="form-control mt-1" id="hrPaymentRemaining" readonly value="0"
                               style="border-radius:8px; height:42px; border:1px solid #e2e8f0; background:#f8fafc">
                        <small class="text-muted">Số này sẽ được tính vào lương tồn</small>
                    </div>
                    <div class="form-group mt-3">
                        <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Ghi chú</label>
                        <textarea class="form-control mt-1" name="note" rows="2" placeholder="Nhập ghi chú"
                                  style="border-radius:8px; border:1px solid #e2e8f0"></textarea>
                    </div>
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Thời gian</label>
                        <input type="date" class="form-control mt-1" name="payment_date" value="<?= date('Y-m-d') ?>"
                               style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                    </div>
                </div>
                <div class="modal-footer" style="padding:16px 24px; background:#f8fafc; border-top:1px solid #e2e8f0">
                    <button type="button" class="btn btn-light px-4" data-dismiss="modal" style="border-radius:8px">Huỷ</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius:8px">
                        <i class="fas fa-check mr-1"></i>Thanh toán
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Cài đặt lương -->
<div class="modal fade" id="hrSettingsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content" style="border-radius:12px; overflow:hidden; border:none">
            <div class="modal-header" style="background:linear-gradient(135deg, #6f42c1, #8a5cf6); border:none; padding:20px 24px">
                <div class="d-flex align-items-center">
                    <div style="width:36px; height:36px; border-radius:8px; background:rgba(255,255,255,0.2); display:flex; align-items:center; justify-content:center; margin-right:12px">
                        <i class="fas fa-cog" style="color:#fff; font-size:15px"></i>
                    </div>
                    <h5 class="modal-title mb-0" style="color:#fff; font-weight:600">Cài đặt lương</h5>
                </div>
                <button type="button" class="close" data-dismiss="modal" style="color:#fff; opacity:0.8; text-shadow:none"><span>&times;</span></button>
            </div>
            <form id="hrSettingsForm">
                <div class="modal-body" style="padding:24px">
                    <input type="hidden" name="employee_id" id="hrSettingsEmployeeId" value="<?= (int)$modalEmployeeId ?>">
                    <div class="form-group mb-0">
                        <label class="small font-weight-bold text-muted" style="text-transform:uppercase; letter-spacing:0.5px">Lương cơ bản (tháng)</label>
                        <input type="text" inputmode="numeric" class="form-control money-input mt-1"
                               name="base_salary" id="hrBaseSalary" placeholder="VD: 2.000.000" value="0"
                               style="border-radius:8px; height:42px; border:1px solid #e2e8f0">
                    </div>
                </div>
                <div class="modal-footer" style="padding:16px 24px; background:#f8fafc; border-top:1px solid #e2e8f0">
                    <button type="button" class="btn btn-light px-4" data-dismiss="modal" style="border-radius:8px">Huỷ</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius:8px">
                        <i class="fas fa-save mr-1"></i>Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
