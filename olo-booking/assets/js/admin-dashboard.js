/**
 * Olo Booking — Admin Dashboard (Gantt Timeline)
 */
(function () {
    'use strict';

    var cfg = window.oloBookAdmin || {};
    var restUrl = cfg.restUrl || '/wp-json/olo-booking/v1';
    var nonce = cfg.nonce || '';
    var services = cfg.services || [];

    var CELL_W = 48;
    var ROW_H = 48;
    var HEAD_H = 52;
    var dayNames = ['Do','Lu','Ma','Me','Gi','Ve','Sa'];
    var monthNames = ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'];

    var currentYear, currentMonth;
    var bookings = [];
    var currentFilter = '';

    var titleEl = document.getElementById('olob-tl-title');
    var gridEl = document.getElementById('olob-tl-grid');
    var scrollEl = document.getElementById('olob-tl-scroll');
    var sidebarEl = document.getElementById('olob-tl-sidebar');
    var popupEl = document.getElementById('olob-popup');

    if (!gridEl) return;

    // ── Init date ──
    var today = new Date();
    var todayStr = fmtDate(today);
    currentYear = today.getFullYear();
    currentMonth = today.getMonth();

    // ── Populate service dropdowns ──
    var serviceFilter = document.getElementById('olob-service-filter');
    var serviceSelect = document.getElementById('olob-service');

    // Add "Strutture" group option (accommodation only)
    var hasAccommodation = services.some(function (s) { return s.type === 'accommodation'; });
    if (serviceFilter && hasAccommodation) {
        var optStrutture = document.createElement('option');
        optStrutture.value = '__accommodation__';
        optStrutture.textContent = 'Strutture (alloggi)';
        serviceFilter.appendChild(optStrutture);
    }

    services.forEach(function (s) {
        if (serviceFilter) {
            var opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = s.title;
            serviceFilter.appendChild(opt);
        }
        if (serviceSelect) {
            var opt2 = document.createElement('option');
            opt2.value = s.id;
            opt2.textContent = s.title;
            opt2.dataset.color = s.color;
            serviceSelect.appendChild(opt2);
        }
    });

    // ── Stats ──
    function updateStats() {
        api('GET', '/stats').then(function (stats) {
            var el = document.getElementById('olob-dash-stats');
            if (!el) return;
            el.innerHTML =
                statCard('#6366F1', 'dashicons-clipboard', stats.total, 'Totali') +
                statCard('#F59E0B', 'dashicons-clock', stats.pending, 'In attesa') +
                statCard('#10B981', 'dashicons-yes-alt', stats.confirmed, 'Confermate') +
                statCard('#EF4444', 'dashicons-dismiss', stats.cancelled, 'Annullate') +
                statCard('#8B5CF6', 'dashicons-calendar-alt', stats.today, 'Oggi');
        });
    }

    function statCard(color, icon, num, label) {
        return '<div class="olob-dash-stat">' +
            '<div class="olob-dash-stat-icon" style="background:' + color + '">' +
            '<span class="dashicons ' + icon + '"></span></div>' +
            '<div class="olob-dash-stat-text">' +
            '<div class="olob-dash-stat-num">' + num + '</div>' +
            '<div class="olob-dash-stat-label">' + label + '</div>' +
            '</div></div>';
    }

    updateStats();

    // ── Build sidebar ──
    function buildSidebar() {
        var html = '<div class="olob-tl-corner">Strutture</div>';
        var list = getFilteredServices();
        list.forEach(function (s) {
            html += '<div class="olob-tl-svc-label">' +
                '<span class="olob-svc-dot" style="background:' + (s.color || '#6366F1') + '"></span>' +
                '<span class="olob-tl-svc-text">' + escHtml(s.title) + '</span></div>';
        });
        sidebarEl.innerHTML = html;
    }

    function getFilteredServices() {
        if (!currentFilter) return services;
        if (currentFilter === '__accommodation__') {
            return services.filter(function (s) { return s.type === 'accommodation'; });
        }
        return services.filter(function (s) { return String(s.id) === String(currentFilter); });
    }

    // ── Build grid ──
    function buildGrid() {
        var days = getDays();
        var svcs = getFilteredServices();
        var totalW = days.length * CELL_W;
        gridEl.style.width = totalW + 'px';

        var html = '';

        // Day headers
        html += '<div class="olob-tl-days-head">';
        days.forEach(function (d) {
            var cls = 'olob-tl-dh';
            if (d.isToday) cls += ' olob-tl-dh-today';
            if (d.isWe) cls += ' olob-tl-dh-we';
            html += '<div class="' + cls + '" style="width:' + CELL_W + 'px">' +
                '<span class="olob-tl-dh-name">' + d.dayShort + '</span>' +
                '<span class="olob-tl-dh-num">' + d.num + '</span></div>';
        });
        html += '</div>';

        // Service rows
        svcs.forEach(function (svc) {
            html += '<div class="olob-tl-row" data-service="' + svc.id + '">';
            // Cell backgrounds
            days.forEach(function (d) {
                var cls = 'olob-tl-cell';
                if (d.isToday) cls += ' olob-tl-c-today';
                if (d.isWe) cls += ' olob-tl-c-we';
                html += '<div class="' + cls + '" style="width:' + CELL_W + 'px" data-date="' + d.date + '" data-svc="' + svc.id + '"></div>';
            });
            // Today line
            var todayIdx = days.findIndex(function (d) { return d.isToday; });
            if (todayIdx >= 0) {
                html += '<div class="olob-tl-today-line" style="left:' + (todayIdx * CELL_W + CELL_W / 2) + 'px"></div>';
            }
            // Booking bars
            var bars = barsForService(svc.id, days);
            bars.forEach(function (bar) {
                var style = barStyle(bar, days);
                var statusCls = 'olob-tl-bar olob-tl-bar-' + bar.status;
                html += '<div class="' + statusCls + '" style="' + style + '" data-booking-id="' + bar.id + '"' +
                    ' data-bar=\'' + JSON.stringify({ id: bar.id, guest_name: bar.guest_name, service_name: bar.service_name, service_color: bar.service_color, checkin_date: bar.checkin_date, checkout_date: bar.checkout_date, nights: bar.nights, guest_count: bar.guest_count, price_total: bar.price_total, status: bar.status, guest_email: bar.guest_email, guest_phone: bar.guest_phone, notes: bar.notes }).replace(/'/g, '&#39;') + '\'>' +
                    '<span class="olob-tl-bar-name">' + escHtml(bar.guest_name) + '</span>' +
                    '<span class="olob-tl-bar-info">' + bar.nights + 'n</span></div>';
            });
            html += '</div>';
        });

        gridEl.innerHTML = html;
        attachBarEvents();
    }

    function getDays() {
        var daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        var result = [];
        for (var d = 1; d <= daysInMonth; d++) {
            var date = new Date(currentYear, currentMonth, d);
            var dateStr = fmtDate(date);
            var dow = date.getDay();
            result.push({
                date: dateStr,
                num: d,
                dayShort: dayNames[dow],
                isToday: dateStr === todayStr,
                isWe: dow === 0 || dow === 6,
            });
        }
        return result;
    }

    function barsForService(serviceId, days) {
        if (!days.length) return [];
        var first = days[0].date;
        var last = days[days.length - 1].date;
        return bookings.filter(function (b) {
            return b.service_id === serviceId &&
                b.checkin_date <= last &&
                b.checkout_date > first;
        });
    }

    function barStyle(bar, days) {
        var first = days[0].date;
        var totalDays = days.length;
        var startIdx = Math.max(0, daysDiff(first, bar.checkin_date));
        var endIdx = Math.min(totalDays, daysDiff(first, bar.checkout_date));
        var left = startIdx * CELL_W + 2;
        var width = Math.max((endIdx - startIdx) * CELL_W - 4, 20);
        var isClipL = bar.checkin_date < first;
        var isClipR = daysDiff(first, bar.checkout_date) > totalDays;
        var br = (isClipL ? '0' : '6px') + ' ' + (isClipR ? '0' : '6px') + ' ' + (isClipR ? '0' : '6px') + ' ' + (isClipL ? '0' : '6px');
        return 'left:' + left + 'px;width:' + width + 'px;border-radius:' + br + ';--bar-color:' + (bar.service_color || '#6366F1');
    }

    // ── Bar events ──
    function attachBarEvents() {
        // Click on bar → open edit modal
        gridEl.querySelectorAll('.olob-tl-bar').forEach(function (el) {
            el.addEventListener('click', function (e) {
                e.stopPropagation();
                var data = JSON.parse(el.getAttribute('data-bar'));
                openModal(data);
            });
            // Hover → popup
            el.addEventListener('mouseenter', function (e) {
                showPopup(el, e);
            });
            el.addEventListener('mouseleave', function () {
                popupEl.style.display = 'none';
            });
            // Drag
            el.addEventListener('mousedown', function (e) {
                e.preventDefault();
                var data = JSON.parse(el.getAttribute('data-bar'));
                if (data.status === 'cancelled') return;
                startDrag(e, el, data);
            });
        });

        // Click on empty cell → new booking
        gridEl.querySelectorAll('.olob-tl-cell').forEach(function (cell) {
            cell.addEventListener('click', function () {
                var date = cell.getAttribute('data-date');
                var svcId = cell.getAttribute('data-svc');
                openModal(null, date, svcId);
            });
        });
    }

    // ── Popup ──
    function showPopup(barEl, ev) {
        var data = JSON.parse(barEl.getAttribute('data-bar'));
        var statusLabels = { pending: 'In attesa', confirmed: 'Confermata', cancelled: 'Annullata', completed: 'Completata' };
        var html = '<div class="olob-popup-head"><strong>' + escHtml(data.guest_name) + '</strong></div>' +
            '<div class="olob-popup-body">' +
            '<div class="olob-popup-row"><span class="olob-svc-dot" style="background:' + data.service_color + '"></span> ' + escHtml(data.service_name) + '</div>' +
            '<div class="olob-popup-row">' + fmtDateHuman(data.checkin_date) + ' &rarr; ' + fmtDateHuman(data.checkout_date) + '</div>' +
            '<div class="olob-popup-grid"><span>' + data.nights + ' notti</span><span>' + data.guest_count + ' ospiti</span><span>&euro; ' + (data.price_total ? data.price_total.toFixed(2) : '—') + '</span></div>' +
            '<div class="olob-popup-row"><span class="olob-status-badge olob-status-' + data.status + '">' + (statusLabels[data.status] || data.status) + '</span></div>';
        if (data.guest_email) html += '<div class="olob-popup-row olob-popup-contact">' + escHtml(data.guest_email) + '</div>';
        if (data.guest_phone) html += '<div class="olob-popup-row olob-popup-contact">' + escHtml(data.guest_phone) + '</div>';
        html += '</div>';
        popupEl.innerHTML = html;

        var rect = barEl.getBoundingClientRect();
        var x = Math.min(rect.left, window.innerWidth - 310);
        var y = rect.bottom + 8;
        if (y > window.innerHeight - 260) y = rect.top - 260;
        popupEl.style.left = Math.max(8, x) + 'px';
        popupEl.style.top = y + 'px';
        popupEl.style.display = 'block';
    }

    // ── Drag & Drop ──
    var dragState = null;

    function startDrag(e, barEl, data) {
        dragState = { el: barEl, data: data, startX: e.clientX, offsetX: 0 };
        popupEl.style.display = 'none';
        document.addEventListener('mousemove', onDragMove);
        document.addEventListener('mouseup', onDragEnd);
    }

    function onDragMove(e) {
        if (!dragState) return;
        dragState.offsetX = e.clientX - dragState.startX;
        dragState.el.style.transform = 'translateX(' + dragState.offsetX + 'px)';
        dragState.el.style.zIndex = '20';
        dragState.el.style.boxShadow = '0 4px 12px rgba(0,0,0,0.2)';
        dragState.el.style.cursor = 'grabbing';
    }

    function onDragEnd() {
        if (!dragState) return;
        var deltaDays = Math.round(dragState.offsetX / CELL_W);
        dragState.el.style.transform = '';
        dragState.el.style.zIndex = '';
        dragState.el.style.boxShadow = '';
        dragState.el.style.cursor = '';

        if (deltaDays !== 0) {
            var d = dragState.data;
            var newCheckin = addDays(d.checkin_date, deltaDays);
            var newCheckout = addDays(d.checkout_date, deltaDays);
            api('PUT', '/bookings/' + d.id, { checkin_date: newCheckin, checkout_date: newCheckout }).then(function () {
                toast('Prenotazione spostata');
                fetchBookings();
                updateStats();
            }).catch(function () {
                toast('Errore nello spostamento', 'error');
                buildGrid();
            });
        }

        dragState = null;
        document.removeEventListener('mousemove', onDragMove);
        document.removeEventListener('mouseup', onDragEnd);
    }

    // ── Navigation ──
    document.getElementById('olob-prev').addEventListener('click', function () { changeMonth(-1); });
    document.getElementById('olob-next').addEventListener('click', function () { changeMonth(1); });
    document.getElementById('olob-today').addEventListener('click', function () {
        currentYear = today.getFullYear();
        currentMonth = today.getMonth();
        fetchBookings();
        scrollToToday();
    });

    function changeMonth(delta) {
        currentMonth += delta;
        if (currentMonth < 0) { currentMonth = 11; currentYear--; }
        if (currentMonth > 11) { currentMonth = 0; currentYear++; }
        popupEl.style.display = 'none';
        fetchBookings();
    }

    function updateTitle() {
        titleEl.textContent = monthNames[currentMonth] + ' ' + currentYear;
    }

    function scrollToToday() {
        var days = getDays();
        var idx = days.findIndex(function (d) { return d.isToday; });
        if (idx >= 0 && scrollEl) {
            var targetX = idx * CELL_W - scrollEl.clientWidth / 2 + CELL_W / 2;
            scrollEl.scrollLeft = Math.max(0, targetX);
        }
    }

    // ── Service filter ──
    if (serviceFilter) {
        serviceFilter.addEventListener('change', function () {
            currentFilter = serviceFilter.value;
            buildSidebar();
            buildGrid();
        });
    }

    // ── Fetch bookings ──
    function fetchBookings() {
        updateTitle();
        var daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        var start = currentYear + '-' + pad(currentMonth + 1) + '-01';
        var end = currentYear + '-' + pad(currentMonth + 1) + '-' + pad(daysInMonth);
        var url = '/bookings?start=' + start + '&end=' + end;
        if (currentFilter && currentFilter !== '__accommodation__') {
            url += '&service_id=' + currentFilter;
        }
        api('GET', url).then(function (data) {
            // If accommodation filter, keep only bookings for accommodation services
            if (currentFilter === '__accommodation__') {
                var accIds = {};
                services.forEach(function (s) { if (s.type === 'accommodation') accIds[s.id] = true; });
                data = data.filter(function (b) { return accIds[b.service_id]; });
            }
            bookings = data;
            buildSidebar();
            buildGrid();
        });
    }

    // Initial load
    fetchBookings();
    setTimeout(scrollToToday, 200);

    // ── Modal ──
    var modal = document.getElementById('olob-modal');
    var form = document.getElementById('olob-form');
    var modalTitle = modal.querySelector('.olob-modal-title');
    var deleteBtn = document.getElementById('olob-delete');
    var saveBtn = document.getElementById('olob-save');
    var cancelBtn = document.getElementById('olob-cancel');
    var closeBtn = modal.querySelector('.olob-modal-close');
    var backdrop = modal.querySelector('.olob-modal-backdrop');

    var currentBookingId = null;

    function openModal(booking, dateStr, svcId) {
        form.reset();
        currentBookingId = null;
        popupEl.style.display = 'none';

        if (booking) {
            // Edit mode
            currentBookingId = booking.id;
            modalTitle.textContent = 'Modifica prenotazione';
            deleteBtn.style.display = '';
            form.elements.booking_id.value = booking.id;
            form.elements.service_id.value = booking.service_id || '';
            form.elements.status.value = booking.status;
            form.elements.guest_name.value = booking.guest_name;
            form.elements.guest_email.value = booking.guest_email || '';
            form.elements.guest_phone.value = booking.guest_phone || '';
            form.elements.checkin_date.value = booking.checkin_date;
            form.elements.checkout_date.value = booking.checkout_date;
            form.elements.guest_count.value = booking.guest_count || 1;
            form.elements.price_total.value = booking.price_total || 0;
            form.elements.notes.value = booking.notes || '';
        } else {
            // New mode
            modalTitle.textContent = 'Nuova prenotazione';
            deleteBtn.style.display = 'none';
            if (dateStr) {
                form.elements.checkin_date.value = dateStr;
                // Default checkout = next day
                form.elements.checkout_date.value = addDays(dateStr, 1);
            } else {
                form.elements.checkin_date.value = fmtDate(new Date());
                form.elements.checkout_date.value = addDays(fmtDate(new Date()), 1);
            }
            if (svcId) form.elements.service_id.value = svcId;
            else if (currentFilter) form.elements.service_id.value = currentFilter;
        }

        modal.style.display = 'flex';
        setTimeout(function () { form.elements.guest_name.focus(); }, 100);
    }

    function closeModal() {
        modal.style.display = 'none';
        currentBookingId = null;
    }

    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    backdrop.addEventListener('click', closeModal);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.style.display !== 'none') closeModal();
    });

    // New booking button
    document.getElementById('olob-add-booking').addEventListener('click', function () {
        openModal(null);
    });

    // ── Save ──
    saveBtn.addEventListener('click', function () {
        var name = form.elements.guest_name.value.trim();
        if (!name) { form.elements.guest_name.focus(); toast('Il nome è obbligatorio', 'error'); return; }
        if (!form.elements.service_id.value) { toast('Seleziona un servizio', 'error'); return; }
        if (!form.elements.checkin_date.value) { toast('La data check-in è obbligatoria', 'error'); return; }
        if (!form.elements.checkout_date.value) { toast('La data check-out è obbligatoria', 'error'); return; }

        var data = {
            service_id: parseInt(form.elements.service_id.value),
            status: form.elements.status.value,
            guest_name: name,
            guest_email: form.elements.guest_email.value,
            guest_phone: form.elements.guest_phone.value,
            checkin_date: form.elements.checkin_date.value,
            checkout_date: form.elements.checkout_date.value,
            guest_count: parseInt(form.elements.guest_count.value) || 1,
            price_total: parseFloat(form.elements.price_total.value) || 0,
            notes: form.elements.notes.value,
        };

        saveBtn.classList.add('olob-loading-state');

        var method = currentBookingId ? 'PUT' : 'POST';
        var endpoint = currentBookingId ? '/bookings/' + currentBookingId : '/bookings';

        api(method, endpoint, data).then(function () {
            closeModal();
            fetchBookings();
            updateStats();
            toast(currentBookingId ? 'Prenotazione aggiornata' : 'Prenotazione creata', 'success');
        }).catch(function (err) {
            toast('Errore: ' + (err.message || 'sconosciuto'), 'error');
        }).finally(function () {
            saveBtn.classList.remove('olob-loading-state');
        });
    });

    // ── Delete ──
    var confirmDialog = document.getElementById('olob-confirm');
    var confirmName = confirmDialog.querySelector('.olob-confirm-name');
    var deleteBookingId = null;

    deleteBtn.addEventListener('click', function () {
        if (!currentBookingId) return;
        deleteBookingId = currentBookingId;
        confirmName.textContent = form.elements.guest_name.value;
        modal.style.display = 'none';
        confirmDialog.style.display = 'flex';
    });

    document.getElementById('olob-confirm-no').addEventListener('click', function () {
        confirmDialog.style.display = 'none';
    });

    confirmDialog.querySelector('.olob-modal-backdrop').addEventListener('click', function () {
        confirmDialog.style.display = 'none';
    });

    document.getElementById('olob-confirm-yes').addEventListener('click', function () {
        if (!deleteBookingId) return;
        api('DELETE', '/bookings/' + deleteBookingId).then(function () {
            confirmDialog.style.display = 'none';
            fetchBookings();
            updateStats();
            toast('Prenotazione eliminata', 'success');
        }).catch(function () {
            toast('Errore nell\'eliminazione', 'error');
        });
        deleteBookingId = null;
    });

    // ── API Helper ──
    function api(method, endpoint, data) {
        var opts = {
            method: method,
            headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': nonce },
        };
        if (data && method !== 'GET') opts.body = JSON.stringify(data);
        return fetch(restUrl + endpoint, opts).then(function (res) {
            if (!res.ok) return res.json().then(function (j) { throw j; });
            return res.json();
        });
    }

    // ── Toast ──
    function toast(msg, type) {
        var el = document.getElementById('olob-toast');
        el.textContent = msg;
        el.className = 'olob-toast' + (type ? ' ' + type : ' success');
        el.style.display = 'block';
        clearTimeout(el._timer);
        el._timer = setTimeout(function () { el.style.display = 'none'; }, 3000);
    }

    // ── Helpers ──
    function fmtDate(d) {
        return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
    }

    function parseDate(s) {
        var p = s.split('-');
        return new Date(parseInt(p[0]), parseInt(p[1]) - 1, parseInt(p[2]));
    }

    function daysDiff(a, b) {
        return Math.round((parseDate(b) - parseDate(a)) / 86400000);
    }

    function addDays(dateStr, n) {
        var d = parseDate(dateStr);
        d.setDate(d.getDate() + n);
        return fmtDate(d);
    }

    function fmtDateHuman(s) {
        var d = parseDate(s);
        return pad(d.getDate()) + ' ' + monthNames[d.getMonth()].substring(0, 3).toLowerCase() + ' ' + d.getFullYear();
    }

    function pad(n) { return n < 10 ? '0' + n : '' + n; }

    function escHtml(s) {
        var div = document.createElement('div');
        div.textContent = s || '';
        return div.innerHTML;
    }

    // Close popup on scroll
    if (scrollEl) {
        scrollEl.addEventListener('scroll', function () { popupEl.style.display = 'none'; });
    }

    // Close popup on click outside
    document.addEventListener('click', function () { popupEl.style.display = 'none'; });

})();
