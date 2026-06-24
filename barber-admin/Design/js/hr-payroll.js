(function () {
    const cfg = window.HR_CONFIG || {};
    const ajaxUrl = cfg.ajaxUrl || 'index.php?route=ajax/hr';

    function postForm(form) {
        const fd = new FormData(form);
        fd.append('action', form.dataset.action);
        return fetch(ajaxUrl, { method: 'POST', body: fd }).then(r => r.json());
    }

    function refreshAfterSave() {
        window.location.reload();
    }

    function bindToggleGroup(groupId, hiddenId, onChange) {
        const group = document.getElementById(groupId);
        const hidden = document.getElementById(hiddenId);
        if (!group || !hidden) return;

        group.querySelectorAll('.hr-toggle-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                group.querySelectorAll('.hr-toggle-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                hidden.value = this.dataset.value;
                if (onChange) onChange(this.dataset.value);
            });
        });
    }

    function updateBonusCategories(type) {
        const sel = document.getElementById('hrBonusCategory');
        if (!sel) return;
        sel.value = '';
        sel.querySelectorAll('option[data-type]').forEach(opt => {
            opt.hidden = opt.dataset.type !== type;
        });
    }

    function updatePaymentTypeUI(type) {
        const periodRow = document.querySelector('.hr-salary-period-row');
        const remainingRow = document.querySelector('.hr-remaining-row');
        const showPeriod = type === 'salary' || type === 'salary_balance';
        const showRemaining = type === 'salary';

        if (periodRow) periodRow.classList.toggle('d-none', !showPeriod);
        if (remainingRow) remainingRow.classList.toggle('d-none', !showRemaining);
    }

    function updateRemainingAmount() {
        const amountEl = document.getElementById('hrPaymentAmount');
        const remainingEl = document.getElementById('hrPaymentRemaining');
        const type = document.getElementById('hrPaymentType')?.value;
        if (!amountEl || !remainingEl || type !== 'salary') return;

        const net = cfg.netSalary || 0;
        // Đọc raw value (bỏ dấu chấm phân cách nghìn)
        const paid = parseInt(amountEl.value.replace(/\./g, '').replace(/\D/g,''), 10) || 0;
        remainingEl.value = Math.max(0, net - paid).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') + ' VND';
    }

    function calcLeaveDays() {
        const from = document.getElementById('hrLeaveFrom')?.value;
        const to = document.getElementById('hrLeaveTo')?.value;
        const half = document.getElementById('hrLeaveHalfDay')?.checked ? '1' : '';
        const countEl = document.getElementById('hrLeaveDaysCount');
        if (!from || !to || !countEl) return;

        fetch(ajaxUrl + '&action=calc_leave_days&date_from=' + encodeURIComponent(from) +
            '&date_to=' + encodeURIComponent(to) + '&is_half_day=' + half)
            .then(r => r.json())
            .then(data => {
                countEl.textContent = data.days ?? '1';
            })
            .catch(() => {
                countEl.textContent = '1';
            });
    }

    function openModal(modalId, employeeId, extra) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        const idFields = modal.querySelectorAll('[id$="EmployeeId"]');
        idFields.forEach(el => { el.value = employeeId; });

        if (modalId === 'hrPaymentModal' && extra) {
            const badge = document.getElementById('hrPaymentNetBadge');
            const remaining = document.getElementById('hrPaymentRemaining');
            const fmt = (n) => Math.round(Number(n) || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') + ' VND';
            if (badge) badge.textContent = fmt(extra.net || 0);
            if (remaining) remaining.value = fmt(extra.remaining || 0);
            cfg.netSalary = extra.net || 0;
        }

        if (modalId === 'hrSettingsModal' && extra) {
            const base = document.getElementById('hrBaseSalary');
            if (base) {
                const val = extra.baseSalary || 0;
                // Format dấu chấm nghìn khi set giá trị vào ô tiền
                base.value = val > 0 ? val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '0';
            }
        }

        $(modal).modal('show');
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (!document.querySelector('.hr-modern-page') && !document.querySelector('.hr-detail-page')) return;

        bindToggleGroup('hrBonusTypeGroup', 'hrBonusType', updateBonusCategories);
        bindToggleGroup('hrPaymentTypeGroup', 'hrPaymentType', updatePaymentTypeUI);
        updatePaymentTypeUI('advance');

        document.querySelectorAll('.hr-open-leave').forEach(btn => {
            btn.addEventListener('click', () => openModal('hrLeaveModal', btn.dataset.employeeId));
        });

        document.querySelectorAll('.hr-open-bonus').forEach(btn => {
            btn.addEventListener('click', () => openModal('hrBonusModal', btn.dataset.employeeId));
        });

        document.querySelectorAll('.hr-open-payment').forEach(btn => {
            btn.addEventListener('click', () => openModal('hrPaymentModal', btn.dataset.employeeId, {
                net: btn.dataset.net,
                remaining: btn.dataset.remaining
            }));
        });

        document.querySelectorAll('.hr-open-settings').forEach(btn => {
            btn.addEventListener('click', () => openModal('hrSettingsModal', btn.dataset.employeeId, {
                baseSalary: btn.dataset.baseSalary
            }));
        });

        ['hrLeaveFrom', 'hrLeaveTo'].forEach(id => {
            document.getElementById(id)?.addEventListener('change', calcLeaveDays);
        });

        document.getElementById('hrPaymentAmount')?.addEventListener('input', updateRemainingAmount);

        const leaveForm = document.getElementById('hrLeaveForm');
        if (leaveForm) {
            leaveForm.dataset.action = 'save_leave';
            leaveForm.addEventListener('submit', function (e) {
                e.preventDefault();
                postForm(leaveForm).then(res => {
                    if (res.success) {
                        $('#hrLeaveModal').modal('hide');
                        refreshAfterSave();
                    } else {
                        swal('Lỗi', res.error || 'Không lưu được', 'error');
                    }
                });
            });
        }

        const bonusForm = document.getElementById('hrBonusForm');
        if (bonusForm) {
            bonusForm.dataset.action = 'save_bonus_penalty';
            bonusForm.addEventListener('submit', function (e) {
                e.preventDefault();
                postForm(bonusForm).then(res => {
                    if (res.success) {
                        $('#hrBonusModal').modal('hide');
                        refreshAfterSave();
                    } else {
                        swal('Lỗi', res.error || 'Không lưu được', 'error');
                    }
                });
            });
        }

        const paymentForm = document.getElementById('hrPaymentForm');
        if (paymentForm) {
            paymentForm.dataset.action = 'save_payment';
            paymentForm.addEventListener('submit', function (e) {
                e.preventDefault();
                postForm(paymentForm).then(res => {
                    if (res.success) {
                        $('#hrPaymentModal').modal('hide');
                        refreshAfterSave();
                    } else {
                        swal('Lỗi', res.error || 'Không lưu được', 'error');
                    }
                });
            });
        }

        const settingsForm = document.getElementById('hrSettingsForm');
        if (settingsForm) {
            settingsForm.dataset.action = 'save_settings';
            settingsForm.addEventListener('submit', function (e) {
                e.preventDefault();
                postForm(settingsForm).then(res => {
                    if (res.success) {
                        $('#hrSettingsModal').modal('hide');
                        refreshAfterSave();
                    } else {
                        swal('Lỗi', res.error || 'Không lưu được', 'error');
                    }
                });
            });
        }

        calcLeaveDays();
    });
})();
