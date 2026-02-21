/**
 * Olo Booking — Frontend Booking Widget (v2 — accommodation model)
 */
(function () {
    'use strict';

    // Helper traduzione: cerca in window.oloLangStrings (iniettato da Olo Lang)
    function __t(str) {
        return (window.oloLangStrings && window.oloLangStrings[str]) || str;
    }

    document.querySelectorAll('.olob-widget').forEach(initWidget);

    function initWidget(widget) {
        var config = JSON.parse(widget.dataset.config || '{}');
        var restUrl = config.restUrl || '';
        var nonce = config.nonce || '';
        var serviceId = config.serviceId || 0;
        var serviceType = config.serviceType || 'accommodation';

        var state = {
            serviceId: serviceId,
            year: new Date().getFullYear(),
            month: new Date().getMonth(),
            checkinDate: null,
            checkoutDate: null,
            monthData: {},
        };

        var calWrap = widget.querySelector('.olob-cal-wrap');
        var calMonth = widget.querySelector('.olob-cal-month');
        var calGrid = widget.querySelector('.olob-cal-grid');
        var dateSummary = widget.querySelector('.olob-date-summary');
        var checkinVal = widget.querySelector('.olob-checkin-val');
        var checkoutVal = widget.querySelector('.olob-checkout-val');
        var nightsRow = widget.querySelector('.olob-nights-row');
        var nightsVal = widget.querySelector('.olob-nights-val');
        var bookForm = widget.querySelector('.olob-book-form');
        var formTitle = widget.querySelector('.olob-form-title');
        var successPanel = widget.querySelector('.olob-success');
        var serviceList = widget.querySelector('[id$="-services"]');

        // Nav
        widget.querySelector('.olob-cal-prev').addEventListener('click', function () {
            state.month--;
            if (state.month < 0) { state.month = 11; state.year--; }
            renderMonth();
        });
        widget.querySelector('.olob-cal-next').addEventListener('click', function () {
            state.month++;
            if (state.month > 11) { state.month = 0; state.year++; }
            renderMonth();
        });

        // Back button
        var backBtn = widget.querySelector('.olob-btn-back');
        if (backBtn) {
            backBtn.addEventListener('click', function () {
                bookForm.style.display = 'none';
                calWrap.style.display = '';
                if (dateSummary) dateSummary.style.display = '';
            });
        }

        // Confirm button
        var confirmBtn = widget.querySelector('.olob-btn-confirm');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', submitBooking);
        }

        // New booking button
        var newBtn = widget.querySelector('.olob-btn-new');
        if (newBtn) {
            newBtn.addEventListener('click', function () {
                successPanel.style.display = 'none';
                calWrap.style.display = '';
                bookForm.style.display = 'none';
                if (dateSummary) dateSummary.style.display = 'none';
                state.checkinDate = null;
                state.checkoutDate = null;
                renderMonth();
            });
        }

        // ── Init ──
        if (!serviceId && serviceList) {
            loadServices();
        } else {
            renderMonth();
        }

        // ── Load services list ──
        function loadServices() {
            apiFetch('/services').then(function (services) {
                serviceList.innerHTML = '';
                if (!services.length) {
                    serviceList.innerHTML = '<p class="olob-loading">' + escHtml(__t('Nessun servizio disponibile.')) + '</p>';
                    calWrap.style.display = 'none';
                    return;
                }
                calWrap.style.display = 'none';
                services.forEach(function (s) {
                    var card = document.createElement('div');
                    card.className = 'olob-service-card';
                    card.innerHTML =
                        '<div class="olob-service-card-color" style="background:' + (s.color || '#6366F1') + '"></div>' +
                        '<div class="olob-service-card-info">' +
                        '<h4>' + escHtml(s.title) + '</h4>' +
                        '<span>' + (s.price ? '&euro; ' + parseFloat(s.price).toFixed(2).replace('.', ',') + escHtml(__t('/notte')) : '') +
                        '</span></div>';
                    card.addEventListener('click', function () {
                        state.serviceId = s.id;
                        serviceType = s.service_type || 'accommodation';
                        serviceList.style.display = 'none';
                        calWrap.style.display = '';
                        renderMonth();
                    });
                    serviceList.appendChild(card);
                });
            });
        }

        // ── Render month ──
        function renderMonth() {
            var months = [__t('Gennaio'), __t('Febbraio'), __t('Marzo'), __t('Aprile'), __t('Maggio'), __t('Giugno'),
                __t('Luglio'), __t('Agosto'), __t('Settembre'), __t('Ottobre'), __t('Novembre'), __t('Dicembre')];
            calMonth.textContent = months[state.month] + ' ' + state.year;

            var monthStr = state.year + '-' + pad(state.month + 1);

            if (!state.serviceId) {
                renderCalendarGrid({});
                return;
            }

            apiFetch('/services/' + state.serviceId + '/month?month=' + monthStr).then(function (data) {
                state.monthData = data;
                renderCalendarGrid(data);
            });
        }

        function renderCalendarGrid(monthData) {
            calGrid.innerHTML = '';

            var first = new Date(state.year, state.month, 1);
            var startDay = (first.getDay() + 6) % 7; // Monday = 0
            var daysInMonth = new Date(state.year, state.month + 1, 0).getDate();

            var today = new Date();
            var todayStr = today.getFullYear() + '-' + pad(today.getMonth() + 1) + '-' + pad(today.getDate());

            // Previous month padding
            var prevMonthDays = new Date(state.year, state.month, 0).getDate();
            for (var p = startDay - 1; p >= 0; p--) {
                calGrid.appendChild(createDayEl(prevMonthDays - p, 'other-month'));
            }

            // Current month
            for (var d = 1; d <= daysInMonth; d++) {
                var dateStr = state.year + '-' + pad(state.month + 1) + '-' + pad(d);
                var classes = [];
                var isPast = dateStr < todayStr;
                var isToday = dateStr === todayStr;

                if (isToday) classes.push('today');
                if (isPast) classes.push('past');

                var dayInfo = monthData[dateStr];
                if (dayInfo && !isPast) {
                    if (dayInfo.status === 'available') classes.push('available');
                    else if (dayInfo.status === 'full') classes.push('full');
                    else if (dayInfo.status === 'closed') classes.push('closed');
                } else if (!isPast && state.serviceId) {
                    classes.push('closed');
                }

                // Highlight selected range
                if (state.checkinDate && dateStr === state.checkinDate) classes.push('checkin');
                if (state.checkoutDate && dateStr === state.checkoutDate) classes.push('checkout');
                if (state.checkinDate && state.checkoutDate && dateStr > state.checkinDate && dateStr < state.checkoutDate) {
                    classes.push('in-range');
                }

                var dayEl = createDayEl(d, classes.join(' '));
                if (classes.indexOf('available') !== -1 || classes.indexOf('today') !== -1) {
                    dayEl.dataset.date = dateStr;
                    dayEl.addEventListener('click', onDayClick);
                }
                calGrid.appendChild(dayEl);
            }

            // Next month padding
            var total = startDay + daysInMonth;
            var remaining = total % 7 === 0 ? 0 : 7 - (total % 7);
            for (var n = 1; n <= remaining; n++) {
                calGrid.appendChild(createDayEl(n, 'other-month'));
            }

            updateDateSummary();
        }

        function createDayEl(num, className) {
            var el = document.createElement('div');
            el.className = 'olob-cal-day ' + className;
            el.textContent = num;
            return el;
        }

        function onDayClick(e) {
            var dateStr = e.currentTarget.dataset.date;
            var dayInfo = state.monthData[dateStr];

            // Solo giorni disponibili
            if (dayInfo && dayInfo.status !== 'available') return;

            if (!state.checkinDate || state.checkoutDate) {
                // Primo click o reset: imposta check-in
                state.checkinDate = dateStr;
                state.checkoutDate = null;
                bookForm.style.display = 'none';
            } else {
                // Secondo click: imposta check-out
                if (dateStr <= state.checkinDate) {
                    // Se cliccato prima o uguale al checkin, resetta
                    state.checkinDate = dateStr;
                    state.checkoutDate = null;
                    bookForm.style.display = 'none';
                } else {
                    // Verifica che non ci siano giorni occupati nel range
                    var rangeOk = true;
                    var cur = nextDay(state.checkinDate);
                    while (cur < dateStr) {
                        var info = state.monthData[cur];
                        if (info && info.status === 'full') {
                            rangeOk = false;
                            break;
                        }
                        cur = nextDay(cur);
                    }

                    if (!rangeOk) {
                        // Range contiene giorni occupati, resetta con nuovo checkin
                        state.checkinDate = dateStr;
                        state.checkoutDate = null;
                        bookForm.style.display = 'none';
                    } else {
                        state.checkoutDate = dateStr;
                        showBookingForm();
                    }
                }
            }

            renderCalendarGrid(state.monthData);
        }

        function nextDay(dateStr) {
            var d = new Date(dateStr);
            d.setDate(d.getDate() + 1);
            return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
        }

        function updateDateSummary() {
            if (!dateSummary) return;

            if (!state.checkinDate) {
                dateSummary.style.display = 'none';
                return;
            }

            dateSummary.style.display = '';
            checkinVal.textContent = formatDateIt(state.checkinDate);

            if (state.checkoutDate) {
                checkoutVal.textContent = formatDateIt(state.checkoutDate);
                var nights = daysBetween(state.checkinDate, state.checkoutDate);
                nightsRow.style.display = '';
                nightsVal.textContent = nights + (nights === 1 ? __t(' notte') : __t(' notti'));
            } else {
                checkoutVal.textContent = __t('Seleziona data uscita');
                nightsRow.style.display = 'none';
            }
        }

        function daysBetween(a, b) {
            var da = new Date(a), db = new Date(b);
            return Math.round((db - da) / (1000 * 60 * 60 * 24));
        }

        function formatDateIt(dateStr) {
            var d = new Date(dateStr);
            var dayNames = [__t('Dom'), __t('Lun'), __t('Mar'), __t('Mer'), __t('Gio'), __t('Ven'), __t('Sab')];
            return dayNames[d.getDay()] + ' ' + d.getDate() + '/' + (d.getMonth() + 1) + '/' + d.getFullYear();
        }

        // ── Show booking form ──
        function showBookingForm() {
            bookForm.style.display = '';
            var nights = daysBetween(state.checkinDate, state.checkoutDate);
            formTitle.textContent = formatDateIt(state.checkinDate) + ' → ' + formatDateIt(state.checkoutDate) + ' (' + nights + (nights === 1 ? __t(' notte') : __t(' notti')) + ')';

            // Clear previous input
            bookForm.querySelectorAll('input, textarea').forEach(function (inp) {
                if (inp.type !== 'number') inp.value = '';
            });
        }

        // ── Submit booking ──
        function submitBooking() {
            var nameInput = bookForm.querySelector('[name="guest_name"]');
            var emailInput = bookForm.querySelector('[name="guest_email"]');

            if (!nameInput.value.trim()) {
                nameInput.focus();
                nameInput.style.borderColor = '#EF4444';
                return;
            }
            if (!emailInput.value.trim()) {
                emailInput.focus();
                emailInput.style.borderColor = '#EF4444';
                return;
            }

            nameInput.style.borderColor = '';
            emailInput.style.borderColor = '';

            confirmBtn.disabled = true;
            confirmBtn.textContent = __t('Invio in corso...');

            var guestCount = bookForm.querySelector('[name="guest_count"]');

            var data = {
                service_id: state.serviceId,
                checkin_date: state.checkinDate,
                checkout_date: state.checkoutDate,
                guest_name: nameInput.value.trim(),
                guest_email: emailInput.value.trim(),
                guest_phone: bookForm.querySelector('[name="guest_phone"]').value.trim(),
                guest_count: guestCount ? parseInt(guestCount.value) || 1 : 1,
                notes: bookForm.querySelector('[name="notes"]').value.trim(),
                source: 'website',
            };

            apiFetch('/bookings', 'POST', data).then(function () {
                bookForm.style.display = 'none';
                calWrap.style.display = 'none';
                if (dateSummary) dateSummary.style.display = 'none';
                successPanel.style.display = '';
            }).catch(function (err) {
                var msg = (err && err.message) ? err.message : __t('Errore nella prenotazione');
                alert(msg);
            }).finally(function () {
                confirmBtn.disabled = false;
                confirmBtn.textContent = __t('Conferma prenotazione');
            });
        }

        // ── API helper ──
        function apiFetch(endpoint, method, data) {
            var opts = {
                method: method || 'GET',
                headers: { 'Content-Type': 'application/json' },
            };
            if (nonce) {
                opts.headers['X-WP-Nonce'] = nonce;
            }
            if (data) {
                opts.body = JSON.stringify(data);
            }
            return fetch(restUrl + endpoint, opts).then(function (res) {
                if (!res.ok) return res.json().then(function (j) { throw j; });
                return res.json();
            });
        }

        function pad(n) {
            return n < 10 ? '0' + n : '' + n;
        }

        function escHtml(str) {
            var div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }
    }
})();
