(function () {
    const cfg = window.BOOKING_CONFIG || {};
    const categories = cfg.categories || [];
    const employees = cfg.employees || [];

    let lastSearchQuery = '';

    function escapeHtml(str) {
        return String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;');
    }

    function syncStartFromParts() {
        const dateEl = document.getElementById('bookingDateOnly');
        const timeEl = document.getElementById('bookingTimeOnly');
        const start = document.getElementById('bookingStartTime');
        if (!dateEl || !timeEl || !start || !dateEl.value || !timeEl.value) return;
        start.value = dateEl.value + 'T' + timeEl.value + ':00';
    }

    function setClient(id, name, phone) {
        document.getElementById('bookingClientId').value = id || '';
        document.getElementById('bookingClientName').value = name || '';
        document.getElementById('bookingClientSearch').value = name || '';
        document.getElementById('bookingPhone').value = phone || '';
        const hint = document.getElementById('bookingClientHint');
        if (hint) {
            hint.textContent = id ? 'Đã chọn khách #' + id : 'Khách mới — sẽ tạo khi lưu';
            hint.className = id ? 'text-success small' : 'text-muted';
        }
    }

    function bindClientSearch() {
        const input = document.getElementById('bookingClientSearch');
        const dropdown = document.getElementById('bookingClientDropdown');
        if (!input || !dropdown) return;

        let timer = null;

        function renderDropdown(clients, q) {
            let html = '';
            if (clients.length) {
                html = clients.map(c => {
                    const name = (c.first_name + ' ' + c.last_name).trim();
                    return '<button type="button" class="booking-client-option" data-id="' + c.client_id + '" data-phone="' + escapeHtml(c.phone_number || '') + '" data-name="' + escapeHtml(name) + '">' +
                        '<strong>' + escapeHtml(name) + '</strong><br><small class="text-muted">' + escapeHtml(c.phone_number || 'Không có SĐT') + '</small></button>';
                }).join('');
            } else {
                html = '<div class="p-2 text-muted small">Không tìm thấy khách hàng</div>';
            }
            html += '<button type="button" class="booking-client-create" data-query="' + escapeHtml(q) + '"><i class="fas fa-plus mr-1"></i> Tạo khách mới' + (q ? ': "' + escapeHtml(q) + '"' : '') + '</button>';
            dropdown.innerHTML = html;
            dropdown.classList.add('show');
        }

        input.addEventListener('input', function () {
            clearTimeout(timer);
            const q = this.value.trim();
            lastSearchQuery = q;
            document.getElementById('bookingClientId').value = '';
            document.getElementById('bookingClientName').value = q;

            if (q.length < 1) {
                dropdown.classList.remove('show');
                return;
            }

            timer = setTimeout(() => {
                fetch('index.php?route=ajax/pos&action=search_clients&q=' + encodeURIComponent(q))
                    .then(r => r.json())
                    .then(data => renderDropdown(data.clients || [], q));
            }, 300);
        });

        dropdown.addEventListener('click', function (e) {
            const createBtn = e.target.closest('.booking-client-create');
            if (createBtn) {
                const q = createBtn.dataset.query || input.value.trim();
                setClient('', q, document.getElementById('bookingPhone').value.trim());
                dropdown.classList.remove('show');
                document.getElementById('bookingPhone').focus();
                return;
            }

            const btn = e.target.closest('.booking-client-option');
            if (!btn) return;
            setClient(btn.dataset.id, btn.dataset.name, btn.dataset.phone);
            dropdown.classList.remove('show');
        });

        document.getElementById('bookingNewClientBtn')?.addEventListener('click', function () {
            const name = input.value.trim() || 'Khách Mới';
            setClient('', name, document.getElementById('bookingPhone').value.trim());
            document.getElementById('bookingPhone').focus();
        });

        document.addEventListener('click', function (e) {
            if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });
    }

    function buildCategoryOptions(selectedId) {
        let html = '<option value="">Nhóm dịch vụ</option>';
        categories.forEach(cat => {
            html += '<option value="' + cat.id + '"' + (String(cat.id) === String(selectedId) ? ' selected' : '') + '>' + escapeHtml(cat.name) + '</option>';
        });
        return html;
    }

    function buildServiceOptions(catId, selectedServiceId) {
        let html = '<option value="">Chọn dịch vụ</option>';
        const cat = categories.find(c => String(c.id) === String(catId));
        if (cat) {
            cat.services.forEach(s => {
                html += '<option value="' + s.id + '" data-price="' + s.price + '" data-duration="' + s.duration + '"' +
                    (String(s.id) === String(selectedServiceId) ? ' selected' : '') + '>' + escapeHtml(s.name) + '</option>';
            });
        }
        return html;
    }

    function buildEmployeeOptions(selectedId) {
        let html = '<option value="">Chọn nhân viên</option>';
        employees.forEach(e => {
            html += '<option value="' + e.id + '"' + (String(e.id) === String(selectedId) ? ' selected' : '') + '>' + escapeHtml(e.name) + '</option>';
        });
        return html;
    }

    function createServiceRow(guestIndex, rowIndex, data) {
        data = data || {};
        const row = document.createElement('div');
        row.className = 'booking-service-row';
        row.dataset.rowIndex = rowIndex;
        row.innerHTML =
            '<select class="form-control form-control-sm guest-cat" name="guest[' + guestIndex + '][rows][' + rowIndex + '][category_id]">' +
            buildCategoryOptions(data.category_id) + '</select>' +
            '<select class="form-control form-control-sm guest-service" name="guest[' + guestIndex + '][rows][' + rowIndex + '][service_id]">' +
            buildServiceOptions(data.category_id, data.service_id) + '</select>' +
            '<select class="form-control form-control-sm guest-employee" name="guest[' + guestIndex + '][rows][' + rowIndex + '][employee_id]">' +
            buildEmployeeOptions(data.employee_id || (employees[0] && employees[0].id)) + '</select>' +
            '<button type="button" class="btn btn-link text-danger btn-sm booking-row-remove" title="Xóa dòng"><i class="fas fa-trash"></i></button>';

        const catSel = row.querySelector('.guest-cat');
        const svcSel = row.querySelector('.guest-service');
        catSel.addEventListener('change', function () {
            svcSel.innerHTML = buildServiceOptions(this.value, '');
            updateGuestSummary(guestIndex);
        });
        svcSel.addEventListener('change', () => updateGuestSummary(guestIndex));
        row.querySelector('.guest-employee').addEventListener('change', () => updateGuestSummary(guestIndex));
        row.querySelector('.booking-row-remove').addEventListener('click', function () {
            const panel = row.closest('.booking-guest-panel');
            if (panel.querySelectorAll('.booking-service-row').length <= 1) return;
            row.remove();
            updateGuestSummary(guestIndex);
        });

        return row;
    }

    function updateGuestSummary(guestIndex) {
        const panel = document.querySelector('.booking-guest-panel[data-guest="' + guestIndex + '"]');
        if (!panel) return;
        let total = 0;
        let minutes = 0;
        panel.querySelectorAll('.booking-service-row').forEach(row => {
            const opt = row.querySelector('.guest-service').selectedOptions[0];
            if (opt && opt.value) {
                total += parseFloat(opt.dataset.price || 0);
                minutes += parseInt(opt.dataset.duration || 0, 10);
            }
        });
        const el = panel.querySelector('.booking-guest-summary');
        if (el) {
            el.innerHTML = '<span>' + Math.round(total).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.') + ' VND</span> | <span>' + minutes + ' Phút</span>';
        }
    }

    function createGuestPanel(guestIndex, canRemove) {
        const panel = document.createElement('div');
        panel.className = 'booking-panel booking-guest-panel';
        panel.dataset.guest = guestIndex;

        panel.innerHTML =
            '<div class="booking-guest-head">' +
            '<h3 class="booking-section-title mb-0"><i class="fas fa-user mr-2"></i>Khách #' + guestIndex + '</h3>' +
            (canRemove ? '<button type="button" class="btn btn-link text-muted booking-guest-remove p-0" title="Xóa khách"><i class="fas fa-times"></i></button>' : '') +
            '</div>' +
            '<div class="booking-service-row-header">' +
            '<span>Nhóm DV</span><span>Dịch vụ</span><span>Nhân viên</span><span></span>' +
            '</div>' +
            '<div class="booking-service-rows"></div>' +
            '<button type="button" class="btn btn-outline-primary btn-sm booking-add-service mt-1">+ Thêm dịch vụ</button>' +
            '<div class="booking-guest-summary mt-2">0 VND | 0 Phút</div>';

        const rowsWrap = panel.querySelector('.booking-service-rows');
        rowsWrap.appendChild(createServiceRow(guestIndex, 0));

        panel.querySelector('.booking-add-service').addEventListener('click', function () {
            const idx = rowsWrap.querySelectorAll('.booking-service-row').length;
            rowsWrap.appendChild(createServiceRow(guestIndex, idx));
        });

        if (canRemove) {
            panel.querySelector('.booking-guest-remove').addEventListener('click', function () {
                const guestsInput = document.getElementById('bookingGuests');
                guestsInput.value = Math.max(1, parseInt(guestsInput.value, 10) - 1);
                renderGuestPanels(guestsInput.value);
            });
        }

        return panel;
    }

    function renderGuestPanels(count) {
        const wrap = document.getElementById('guestPanels');
        if (!wrap) return;
        count = Math.max(1, Math.min(10, parseInt(count, 10) || 1));

        const title = wrap.querySelector('.booking-right-title');
        wrap.innerHTML = '';
        if (title) {
            wrap.appendChild(title);
        } else {
            const h = document.createElement('div');
            h.className = 'booking-right-title';
            h.textContent = 'Dịch vụ theo từng khách';
            wrap.appendChild(h);
        }

        for (let i = 1; i <= count; i++) {
            wrap.appendChild(createGuestPanel(i, i > 1));
        }
    }

    function validateForm(e) {
        syncStartFromParts();
        const clientId = document.getElementById('bookingClientId').value;
        const phone = document.getElementById('bookingPhone').value.trim();
        const name = document.getElementById('bookingClientSearch').value.trim();

        if (!clientId && !phone) {
            e.preventDefault();
            swal('Cảnh báo', 'Vui lòng chọn khách hàng hoặc nhập số điện thoại', 'warning');
            return;
        }
        if (!clientId && !name) {
            document.getElementById('bookingClientName').value = 'Khách Mới';
        } else {
            document.getElementById('bookingClientName').value = name;
        }

        let hasService = false;
        document.querySelectorAll('.guest-service').forEach(sel => {
            if (sel.value) hasService = true;
        });
        if (!hasService) {
            e.preventDefault();
            swal('Cảnh báo', 'Vui lòng chọn ít nhất một dịch vụ', 'warning');
        }
    }

    function loadPreselectClient() {
        const c = cfg.preselectClient;
        if (c && c.id) {
            setClient(c.id, c.name, c.phone || '');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (!document.querySelector('.booking-create-wrap')) return;

        const dateEl = document.getElementById('bookingDateOnly');
        const timeEl = document.getElementById('bookingTimeOnly');
        const now = new Date();
        now.setMinutes(Math.ceil(now.getMinutes() / 15) * 15);
        const pad = n => String(n).padStart(2, '0');
        if (dateEl && !dateEl.value) {
            dateEl.value = now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate());
        }
        if (timeEl && !timeEl.value) {
            timeEl.value = pad(now.getHours()) + ':' + pad(now.getMinutes());
        }

        renderGuestPanels(1);
        syncStartFromParts();
        bindClientSearch();
        loadPreselectClient();

        document.getElementById('bookingGuests')?.addEventListener('change', function () {
            renderGuestPanels(this.value);
        });
        document.getElementById('bookingGuests')?.addEventListener('input', function () {
            renderGuestPanels(this.value);
        });

        document.getElementById('bookingDateOnly')?.addEventListener('change', syncStartFromParts);
        document.getElementById('bookingTimeOnly')?.addEventListener('change', syncStartFromParts);
        document.getElementById('bookingForm')?.addEventListener('submit', validateForm);
    });
})();
