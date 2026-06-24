/**
 * money-input.js
 * Tự động format dấu chấm phân cách nghìn cho các ô tiền.
 * Dùng: thêm class "money-input" vào input.
 * Giá trị thực (số nguyên) được đồng bộ về input gốc qua data-raw.
 */
(function () {
    'use strict';

    function formatVND(raw) {
        var n = parseInt(String(raw).replace(/\D/g, ''), 10);
        if (isNaN(n) || n === 0) return '';
        return n.toLocaleString('vi-VN').replace(/\./g, '.');
        // vi-VN dùng dấu chấm làm phân cách nghìn
    }

    function parseRaw(display) {
        return parseInt(display.replace(/\./g, '').replace(/\D/g, ''), 10) || 0;
    }

    function applyToInput(input) {
        if (input.dataset.moneyInit === '1') return;
        input.dataset.moneyInit = '1';

        /* Nếu có giá trị sẵn → format ngay */
        var initVal = input.value.replace(/\./g, '').replace(/\D/g, '');
        if (initVal && parseInt(initVal) > 0) {
            input.value = formatVND(initVal);
        }

        input.addEventListener('input', function () {
            var raw = parseRaw(this.value);
            var pos = this.selectionStart;
            var oldLen = this.value.length;
            this.value = raw > 0 ? formatVND(raw) : '';
            /* Giữ vị trí con trỏ */
            var newLen = this.value.length;
            var diff = newLen - oldLen;
            this.setSelectionRange(Math.max(0, pos + diff), Math.max(0, pos + diff));
            /* Cập nhật hidden raw */
            syncHidden(this, raw);
        });

        input.addEventListener('blur', function () {
            var raw = parseRaw(this.value);
            this.value = raw > 0 ? formatVND(raw) : '';
            syncHidden(this, raw);
        });

        input.addEventListener('focus', function () {
            /* Khi focus: hiển thị đầy đủ, không thay đổi */
        });

        /* Ngăn chữ cái, chỉ cho số và dấu chấm */
        input.addEventListener('keydown', function (e) {
            var allowed = ['Backspace','Delete','ArrowLeft','ArrowRight','ArrowUp','ArrowDown','Tab','Home','End'];
            if (allowed.includes(e.key)) return;
            if (e.ctrlKey || e.metaKey) return; // copy/paste
            if (!/^\d$/.test(e.key)) e.preventDefault();
        });
    }

    function syncHidden(input, raw) {
        var hiddenId = input.dataset.rawTarget;
        if (hiddenId) {
            var hid = document.getElementById(hiddenId);
            if (hid) { hid.value = raw; return; }
        }
        /* Nếu không có hidden riêng, tạo hidden name=<tên gốc>_raw và swap tên */
        /* Thực ra: ghi thẳng giá trị số vào attribute trước khi submit */
        input.dataset.rawValue = raw;
    }

    /* Trước khi submit: đặt lại value = số thô để PHP nhận đúng */
    document.addEventListener('submit', function (e) {
        var form = e.target;
        form.querySelectorAll('.money-input').forEach(function (inp) {
            var raw = parseRaw(inp.value);
            inp.value = raw > 0 ? raw : '';
        });
    }, true);

    /* Khởi tạo cho tất cả .money-input hiện có và tương lai (MutationObserver) */
    function initAll() {
        document.querySelectorAll('.money-input').forEach(applyToInput);
    }

    /* Chạy khi DOM sẵn sàng */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    /* Theo dõi các modal/dynamic content thêm input sau */
    var observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (m) {
            m.addedNodes.forEach(function (node) {
                if (node.nodeType === 1) {
                    if (node.classList && node.classList.contains('money-input')) applyToInput(node);
                    node.querySelectorAll && node.querySelectorAll('.money-input').forEach(applyToInput);
                }
            });
        });
    });
    observer.observe(document.body, { childList: true, subtree: true });

    /* Export để dùng ngoài */
    window.moneyInput = { format: formatVND, parse: parseRaw, apply: applyToInput };
})();
