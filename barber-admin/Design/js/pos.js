(function () {
    const cfg = window.POS_CONFIG || {};
    const employees = cfg.employees || [];

    let state = {
        clientId: 0,
        clientName: 'Khách vãng lai',
        lines: [],
        invoiceDiscount: 0,
        invoiceDiscountPercent: false,
        orderCode: cfg.draftOrderCode || '',
        selectedLineIndex: null,
        appointmentId: 0,
    };

    /* ─── Tiện ích ─────────────────────────────────────── */

    function formatVnd(n) {
        return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }
    const formatMoney = formatVnd;

    function escapeHtml(s) {
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    function parseMoney(str) {
        return parseFloat(String(str).replace(/\./g, '').replace(/,/g, '')) || 0;
    }

    function uid() {
        return 'l' + Date.now() + Math.random().toString(36).slice(2, 7);
    }

    function lineSubtotal(line) {
        const base = line.unitPrice * line.qty;
        const disc = line.discountPercent ? (base * line.discount / 100) : line.discount;
        return Math.max(0, base - disc);
    }

    function calcTotals() {
        let subtotal = 0;
        state.lines.forEach(l => { subtotal += lineSubtotal(l); });
        const invDisc = state.invoiceDiscountPercent
            ? subtotal * state.invoiceDiscount / 100
            : state.invoiceDiscount;
        return { subtotal, invDisc, total: Math.max(0, subtotal - invDisc) };
    }

    /* ─── Cart ─────────────────────────────────────────── */

    function addLine(type, refId, name, unitPrice) {
        state.lines.push({
            uid: uid(), type, refId, name,
            qty: 1,
            unitPrice: parseFloat(unitPrice) || 0,
            discount: 0, discountPercent: false,
            employeeId: 0,
            employeeName: '— Chưa chọn —',
            employeeIds: [],    // danh sách nhiều NV
            employeeNames: [],  // tên tương ứng
        });
        renderCart();
    }

    function renderCart() {
        const tbody = document.getElementById('posCartBody');
        const empty = document.getElementById('posEmptyCart');
        if (!tbody) return;

        if (state.lines.length === 0) {
            tbody.innerHTML = '';
            if (empty) empty.style.display = 'block';
        } else {
            if (empty) empty.style.display = 'none';
            tbody.innerHTML = state.lines.map((line, idx) => {
                const total = lineSubtotal(line);
                // Hiển thị tất cả tên NV đã chọn
                let empLabel;
                if (line.employeeNames && line.employeeNames.length > 0) {
                    empLabel = line.employeeNames.map(n => escapeHtml(n)).join(' &amp; ');
                } else {
                    empLabel = '<span style="color:#dc2626;font-size:11px"><i class="fas fa-exclamation-circle"></i> Chưa chọn NV</span>';
                }
                return `<tr data-idx="${idx}">
                    <td>
                        <div class="pos-line-name">${escapeHtml(line.name)}</div>
                        <div class="pos-line-staff" data-assign="${idx}" title="Click để phân công nhân viên" style="cursor:pointer">
                            NV: ${empLabel}
                        </div>
                    </td>
                    <td><input type="number" class="pos-qty-input" min="1" value="${line.qty}" data-qty="${idx}"></td>
                    <td>
                        <div class="pos-price-cell">
                            <input type="text" value="${formatMoney(line.unitPrice)}" data-price="${idx}">
                            <i class="fas fa-pen fa-xs text-muted"></i>
                        </div>
                    </td>
                    <td>
                        <div class="pos-discount-cell">
                            <input type="number" min="0" value="${line.discount}" data-disc="${idx}">
                            <button type="button" class="pos-discount-toggle ${line.discountPercent ? 'active' : ''}" data-disc-toggle="${idx}">${line.discountPercent ? '%' : 'đ'}</button>
                        </div>
                    </td>
                    <td class="pos-line-total">${formatMoney(total)}</td>
                    <td><button type="button" class="pos-btn-del-line" data-del="${idx}"><i class="fas fa-trash"></i></button></td>
                </tr>`;
            }).join('');
        }

        const { subtotal, total } = calcTotals();
        const elSub   = document.getElementById('posSubtotal');
        const elTotal = document.getElementById('posGrandTotal');
        const elCode  = document.getElementById('posOrderCodeDisplay');
        if (elSub)   elSub.textContent   = formatMoney(subtotal);
        if (elTotal) elTotal.textContent = formatMoney(total);
        if (elCode)  elCode.textContent  = '(' + (state.orderCode || '') + ')';
        document.getElementById('posTabLabel').textContent = state.clientName;
    }

    /* ─── Prefill từ lịch hẹn ──────────────────────────── */

    function initFromPrefill(prefill) {
        if (!prefill) return;
        state.appointmentId = parseInt(prefill.appointment_id, 10) || 0;
        state.clientId      = parseInt(prefill.client_id, 10) || 0;
        state.clientName    = prefill.client_name || 'Khách vãng lai';
        const searchEl = document.getElementById('posClientSearch');
        if (searchEl) searchEl.value = state.clientName;
        const tab = document.getElementById('posTabLabel');
        if (tab) tab.textContent = state.clientName;
        if (!prefill.lines || !prefill.lines.length) { renderCart(); return; }
        state.lines = prefill.lines.map(l => {
            const empId  = parseInt(l.employee_id, 10) || 0;
            const empObj = employees.find(e => parseInt(e.employee_id, 10) === empId);
            const empName = empObj
                    ? (empObj.first_name + ' ' + empObj.last_name).toUpperCase()
                    : (l.employee_name || '');
            return {
                uid: uid(),
                type: l.type || 'service',
                refId: parseInt(l.ref_id, 10),
                name: l.name,
                qty: parseInt(l.qty, 10) || 1,
                unitPrice: parseFloat(l.unit_price) || 0,
                discount: 0, discountPercent: false,
                employeeId: empId,
                employeeName: empName,
                employeeIds:   empId > 0 ? [empId]     : [],
                employeeNames: empName   ? [empName]   : [],
            };
        });
        renderCart();
    }

    /* ─── Reset hóa đơn ────────────────────────────────── */

    function resetInvoice() {
        state.lines = [];
        state.clientId = 0;
        state.clientName = 'Khách vãng lai';
        state.invoiceDiscount = 0;
        state.invoiceDiscountPercent = false;
        state.appointmentId = 0;
        state.orderCode = cfg.draftOrderCode || ('HD' + Date.now().toString().slice(-6));
        document.getElementById('posClientSearch').value = '';
        document.getElementById('posInvDiscount').value = '0';
        renderCart();
    }

    /* ─── Catalog ──────────────────────────────────────── */

    function bindCatalog() {
        document.querySelectorAll('.pos-cat-item').forEach(el => {
            el.addEventListener('click', function () {
                addLine(this.dataset.type, parseInt(this.dataset.id, 10), this.dataset.name, this.dataset.price);
            });
        });
        document.querySelectorAll('.pos-cat-header').forEach(h => {
            h.addEventListener('click', function () {
                this.closest('.pos-cat-group').classList.toggle('open');
            });
        });
        const search = document.getElementById('posCatalogSearch');
        if (search) {
            search.addEventListener('input', function () {
                const q = this.value.toLowerCase();
                document.querySelectorAll('.pos-cat-item').forEach(item => {
                    item.classList.toggle('hidden', q && !(item.dataset.name || '').toLowerCase().includes(q));
                });
            });
        }
        document.querySelectorAll('[data-catalog-tab]').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('[data-catalog-tab]').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const tab = this.dataset.catalogTab;
                document.querySelectorAll('[data-catalog-pane]').forEach(p => {
                    p.style.display = p.dataset.catalogPane === tab ? 'block' : 'none';
                });
            });
        });
    }

    /* ─── Sự kiện trong cart ───────────────────────────── */

    function bindCartEvents() {
        const tbody = document.getElementById('posCartBody');
        if (!tbody) return;
        tbody.addEventListener('click', function (e) {
            const del = e.target.closest('[data-del]');
            if (del) { state.lines.splice(parseInt(del.dataset.del, 10), 1); renderCart(); return; }

            const assign = e.target.closest('[data-assign]');
            if (assign) {
                state.selectedLineIndex = parseInt(assign.dataset.assign, 10);
                openStaffModal();
                return;
            }
            const discToggle = e.target.closest('[data-disc-toggle]');
            if (discToggle) {
                const idx = parseInt(discToggle.dataset.discToggle, 10);
                state.lines[idx].discountPercent = !state.lines[idx].discountPercent;
                renderCart();
            }
        });
        tbody.addEventListener('change', function (e) {
            const qty = e.target.closest('[data-qty]');
            if (qty) { const i = parseInt(qty.dataset.qty, 10); state.lines[i].qty = Math.max(1, parseInt(qty.value, 10) || 1); renderCart(); }
            const price = e.target.closest('[data-price]');
            if (price) { const i = parseInt(price.dataset.price, 10); state.lines[i].unitPrice = parseMoney(price.value); renderCart(); }
            const disc = e.target.closest('[data-disc]');
            if (disc) { const i = parseInt(disc.dataset.disc, 10); state.lines[i].discount = parseFloat(disc.value) || 0; renderCart(); }
        });
    }

    /* ─── Tìm kiếm khách ──────────────────────────────── */

    function bindClientSearch() {
        const input    = document.getElementById('posClientSearch');
        const dropdown = document.getElementById('posClientDropdown');
        let timer;
        input.addEventListener('input', function () {
            clearTimeout(timer);
            const q = this.value.trim();
            if (q.length < 1) { dropdown.classList.remove('show'); return; }
            timer = setTimeout(() => {
                fetch('index.php?route=ajax/pos&action=search_clients&q=' + encodeURIComponent(q))
                    .then(r => r.json())
                    .then(data => {
                        dropdown.innerHTML = (data.clients || []).map(c =>
                            `<button type="button" data-id="${c.client_id}" data-name="${escapeHtml(c.first_name + ' ' + c.last_name)}">
                                ${escapeHtml(c.first_name + ' ' + c.last_name)} — ${escapeHtml(c.phone_number || '')}
                            </button>`
                        ).join('') || '<div class="p-2 text-muted small">Không tìm thấy</div>';
                        dropdown.classList.add('show');
                    });
            }, 300);
        });
        dropdown.addEventListener('click', function (e) {
            const btn = e.target.closest('button[data-id]');
            if (!btn) return;
            state.clientId   = parseInt(btn.dataset.id, 10);
            state.clientName = btn.dataset.name;
            input.value = state.clientName;
            dropdown.classList.remove('show');
            renderCart();
        });
        document.addEventListener('click', function (e) {
            if (!input.contains(e.target) && !dropdown.contains(e.target)) dropdown.classList.remove('show');
        });
    }

    /* ─── Modal xếp nhân viên (checkbox đơn giản) ─────── */

    function openStaffModal() {
        const list = document.getElementById('posStaffCheckboxList');
        if (!list) return;

        // Gom tất cả NV đang được chọn trong hóa đơn (từ bất kỳ dòng nào)
        const selected = new Set();
        state.lines.forEach(l => {
            (l.employeeIds || []).forEach(id => { if (id > 0) selected.add(id); });
            if (l.employeeId > 0) selected.add(l.employeeId);
        });

        if (employees.length === 0) {
            list.innerHTML = '<p class="text-muted text-center py-3 small">Chưa có nhân viên nào.</p>';
        } else {
            list.innerHTML = employees.map(e => {
                const id      = parseInt(e.employee_id, 10);
                const name    = e.first_name + ' ' + e.last_name;
                const checked = selected.has(id) ? 'checked' : '';
                return `<label class="d-flex align-items-center px-3 py-2" style="gap:10px;cursor:pointer;border-bottom:1px solid #f0f0f0;margin:0">
                    <input type="checkbox" class="pos-staff-cb" value="${id}" ${checked}
                           style="width:16px;height:16px;cursor:pointer;flex-shrink:0">
                    <span style="font-size:14px;font-weight:500">${escapeHtml(name)}</span>
                </label>`;
            }).join('');
        }

        $('#posStaffModal').modal('show');
    }

    function bindStaffModal() {
        document.getElementById('posStaffSave').addEventListener('click', function () {
            // Thu thập danh sách NV được tích
            const checked = [];
            document.querySelectorAll('#posStaffCheckboxList .pos-staff-cb:checked').forEach(cb => {
                const id  = parseInt(cb.value, 10);
                const emp = employees.find(e => parseInt(e.employee_id, 10) === id);
                if (emp) checked.push({
                    id,
                    name: (emp.first_name + ' ' + emp.last_name).toUpperCase()
                });
            });

            // Gán cho tất cả dòng trong hóa đơn
            state.lines.forEach(l => {
                if (checked.length === 0) {
                    // Bỏ chọn tất cả
                    l.employeeId    = 0;
                    l.employeeName  = '— Chưa chọn —';
                    l.employeeIds   = [];
                    l.employeeNames = [];
                } else {
                    // Lưu toàn bộ danh sách NV, dùng NV đầu làm primary
                    l.employeeId    = checked[0].id;
                    l.employeeName  = checked[0].name;
                    l.employeeIds   = checked.map(c => c.id);
                    l.employeeNames = checked.map(c => c.name);
                }
            });

            $('#posStaffModal').modal('hide');
            renderCart();
        });
    }

    /* ─── Thanh toán ───────────────────────────────────── */

    function checkout() {
        if (state.lines.length === 0) {
            swal('Cảnh báo', 'Chưa có dịch vụ hoặc sản phẩm trong hóa đơn', 'warning');
            return;
        }
        // Nếu dòng nào chưa có NV và hệ thống có NV mặc định → tự điền NV đầu tiên
        if (employees.length > 0) {
            state.lines.forEach(l => {
                if (!l.employeeIds || l.employeeIds.length === 0) {
                    const def = employees[0];
                    l.employeeId    = parseInt(def.employee_id, 10);
                    l.employeeName  = (def.first_name + ' ' + def.last_name).toUpperCase();
                    l.employeeIds   = [l.employeeId];
                    l.employeeNames = [l.employeeName];
                }
            });
            renderCart();
        }
        const missing = state.lines.filter(l => !l.employeeIds || l.employeeIds.length === 0);
        if (missing.length > 0) {
            swal({
                title: 'Chưa chọn nhân viên',
                text: 'Các dòng sau chưa có NV: ' + missing.map(l => l.name).join(', ') + '\nVẫn tiếp tục?',
                icon: 'warning',
                buttons: { cancel: 'Quay lại', confirm: { text: 'Tiếp tục', className: 'btn-warning' } },
            }).then(ok => { if (ok) openPaymentModal(); });
            return;
        }
        openPaymentModal();
    }

    function openPaymentModal() {
        const { total } = calcTotals();
        const payEl = document.getElementById('posPayTotal');
        if (payEl) payEl.textContent = formatVnd(total) + ' VND';
        $('#posPaymentModal').modal('show');
    }

    function confirmPayment() {
        const method = document.querySelector('input[name="pos_payment_method"]:checked')?.value || 'cash';
        const note   = document.getElementById('posPayNote')?.value || '';
        const deduct = document.getElementById('posDeductMaterials')?.checked;

        const payload = {
            client_id: state.clientId,
            appointment_id: state.appointmentId || 0,
            payment_method: method,
            note, order_code: state.orderCode,
            invoice_discount: state.invoiceDiscount,
            invoice_discount_percent: state.invoiceDiscountPercent,
            deduct_materials: deduct ? 1 : 0,
            lines: state.lines.map(l => ({
                type: l.type, ref_id: l.refId, qty: l.qty,
                unit_price: l.unitPrice, discount: l.discount,
                discount_percent: l.discountPercent,
                employee_id: l.employeeId,
                employee_ids: l.employeeIds || (l.employeeId > 0 ? [l.employeeId] : []),
            })),
        };

        fetch('index.php?route=ajax/pos', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'checkout', ...payload }),
        })
            .then(r => r.json())
            .then(data => {
                if (data.error) throw new Error(data.error);
                $('#posPaymentModal').modal('hide');
                swal('Thanh toán thành công', 'Mã hóa đơn: ' + (data.order_code || data.order_id), 'success').then(() => {
                    if (document.getElementById('posAutoPrint')?.checked) {
                        window.open('index.php?route=pos/print&id=' + data.order_id, '_blank');
                    }
                    state.orderCode = cfg.draftOrderCode || ('HD' + Date.now().toString().slice(-6));
                    resetInvoice();
                });
            })
            .catch(err => swal('Lỗi', err.message || 'Không thể thanh toán', 'error'));
    }

    /* ─── Khởi động ────────────────────────────────────── */

    document.addEventListener('DOMContentLoaded', function () {
        if (!document.querySelector('.pos-easy')) return;

        const now = new Date();
        const pad = n => String(n).padStart(2, '0');
        const timeEl = document.getElementById('posDateTime');
        if (timeEl) {
            timeEl.value = now.getFullYear() + '-' + pad(now.getMonth()+1) + '-' + pad(now.getDate())
                         + 'T' + pad(now.getHours()) + ':' + pad(now.getMinutes());
        }

        bindCatalog();
        bindCartEvents();
        bindClientSearch();
        bindStaffModal();

        document.getElementById('posBtnNew')?.addEventListener('click', resetInvoice);
        document.getElementById('posBtnCancel')?.addEventListener('click', function () {
            if (state.lines.length && !confirm('Hủy hóa đơn hiện tại?')) return;
            resetInvoice();
        });
        document.getElementById('posBtnPay')?.addEventListener('click', checkout);
        document.getElementById('posConfirmPay')?.addEventListener('click', confirmPayment);

        /* Nút footer "Xếp nhân viên" → mở modal tổng hợp tất cả dòng */
        document.getElementById('posBtnAssignStaff')?.addEventListener('click', function () {
            if (state.lines.length === 0) { swal('', 'Thêm dịch vụ vào hóa đơn trước', 'info'); return; }
            state.selectedLineIndex = null;
            openStaffModal();
        });

        document.getElementById('posBtnPrint')?.addEventListener('click', function () {
            swal('In hóa đơn', 'Thanh toán xong để in bill hoặc mở Danh sách đơn hàng', 'info');
        });

        const invDisc       = document.getElementById('posInvDiscount');
        const invDiscToggle = document.getElementById('posInvDiscountToggle');
        invDisc?.addEventListener('input', function () {
            state.invoiceDiscount = parseFloat(this.value) || 0; renderCart();
        });
        invDiscToggle?.addEventListener('click', function () {
            state.invoiceDiscountPercent = !state.invoiceDiscountPercent;
            this.classList.toggle('active');
            this.textContent = state.invoiceDiscountPercent ? '%' : 'đ';
            renderCart();
        });

        document.getElementById('posBtnAddClient')?.addEventListener('click', function () {
            const name = prompt('Họ tên khách (VD: Nguyễn Văn A):');
            if (!name) return;
            const parts = name.trim().split(/\s+/);
            const first = parts.shift() || 'Khách';
            const last  = parts.join(' ') || 'Mới';
            const phone = prompt('Số điện thoại:') || '';
            const fd = new FormData();
            fd.append('action', 'quick_client');
            fd.append('first_name', first);
            fd.append('last_name', last);
            fd.append('phone', phone);
            fetch('index.php?route=ajax/pos', { method: 'POST', body: fd })
                .then(r => r.json())
                .then(data => {
                    if (data.client) {
                        state.clientId   = data.client.client_id;
                        state.clientName = data.client.first_name + ' ' + data.client.last_name;
                        document.getElementById('posClientSearch').value = state.clientName;
                        renderCart();
                    }
                });
        });

        document.querySelectorAll('.pos-cat-group').forEach((g, i) => { if (i < 3) g.classList.add('open'); });

        if (cfg.prefill) initFromPrefill(cfg.prefill);
        else renderCart();
    });
})();
