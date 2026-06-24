/**
 * validation.js — Validation toàn hệ thống admin
 * Áp dụng cho tất cả form có class "needs-validation" hoặc chạy tự động khi submit.
 */

(function () {
    'use strict';

    /* ── Thông báo lỗi tiếng Việt ── */
    var MSG = {
        required : 'Trường này không được bỏ trống.',
        phone    : 'Số điện thoại phải đủ 10 chữ số.',
        email    : 'Email không hợp lệ.',
        number   : 'Vui lòng nhập số hợp lệ.',
        minVal   : function(m){ return 'Giá trị tối thiểu là ' + m + '.'; },
    };

    /* ── Tiện ích ── */
    function showErr(input, msg) {
        input.classList.add('is-invalid');
        var fb = input.parentElement.querySelector('.invalid-feedback');
        if (!fb) {
            fb = document.createElement('div');
            fb.className = 'invalid-feedback';
            input.parentElement.appendChild(fb);
        }
        fb.textContent = msg;
        fb.style.display = 'block';
    }

    function clearErr(input) {
        input.classList.remove('is-invalid');
        input.classList.remove('is-valid');
        var fb = input.parentElement.querySelector('.invalid-feedback');
        if (fb) fb.style.display = 'none';
    }

    function validateInput(input) {
        var val = input.value.trim();
        var name = (input.name || input.id || '').toLowerCase();
        var type = (input.type || '').toLowerCase();

        /* 1. Required */
        if (input.hasAttribute('required') || input.dataset.required === '1') {
            if (val === '') { showErr(input, MSG.required); return false; }
        } else {
            if (val === '') { clearErr(input); return true; } // không required, bỏ qua
        }

        /* 2. Phone — 10 chữ số */
        if (
            name.includes('phone') || name.includes('dien_thoai') ||
            name.includes('sdt') || type === 'tel'
        ) {
            if (!/^\d{10}$/.test(val)) { showErr(input, MSG.phone); return false; }
        }

        /* 3. Email */
        if (type === 'email' || name.includes('email')) {
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) { showErr(input, MSG.email); return false; }
        }

        /* 4. Number / min */
        if (type === 'number') {
            if (isNaN(parseFloat(val))) { showErr(input, MSG.number); return false; }
            var minAttr = input.getAttribute('min');
            if (minAttr !== null && parseFloat(val) < parseFloat(minAttr)) {
                showErr(input, MSG.minVal(minAttr)); return false;
            }
        }

        clearErr(input);
        return true;
    }

    /* ── Validate toàn form ── */
    function validateForm(form) {
        var valid = true;
        var fields = form.querySelectorAll('input:not([type=hidden]):not([type=checkbox]):not([type=radio]):not([type=submit]):not([type=button]), textarea, select');
        fields.forEach(function (f) {
            if (!f.closest('[disabled]') && f.style.display !== 'none') {
                if (!validateInput(f)) valid = false;
            }
        });
        return valid;
    }

    /* ── Live feedback khi người dùng nhập ── */
    document.addEventListener('input', function (e) {
        var t = e.target;
        if (t.tagName === 'INPUT' || t.tagName === 'TEXTAREA' || t.tagName === 'SELECT') {
            validateInput(t);
        }
    }, true);

    document.addEventListener('blur', function (e) {
        var t = e.target;
        if (t.tagName === 'INPUT' || t.tagName === 'TEXTAREA') {
            validateInput(t);
        }
    }, true);

    /* ── Chặn submit nếu có lỗi ── */
    document.addEventListener('submit', function (e) {
        var form = e.target;
        // Bỏ qua form không phải admin form thông thường (AJAX modal)
        if (form.id && ['hrLeaveForm','hrBonusForm','hrPaymentForm','hrSettingsForm','crmAddForm'].includes(form.id)) return;
        if (!validateForm(form)) {
            e.preventDefault();
            e.stopPropagation();
            // Cuộn đến lỗi đầu tiên
            var first = form.querySelector('.is-invalid');
            if (first) first.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }, true);

    /* ── Validation riêng cho AJAX modal forms (HR, CRM) ── */
    function validateModalForm(formId) {
        var form = document.getElementById(formId);
        if (!form) return true;
        return validateForm(form);
    }

    /* Export để các script khác có thể gọi */
    window.salonValidate = { validateForm: validateModalForm, validateInput: validateInput };

})();
