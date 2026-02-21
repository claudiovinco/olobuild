/**
 * Olo Calendar — Admin Dashboard (FullCalendar + CRUD)
 */
(function () {
    'use strict';

    var cfg = window.oloCalAdmin || {};
    var restUrl = cfg.restUrl || '/wp-json/olo-calendar/v1';
    var nonce = cfg.nonce || '';
    var categories = cfg.categories || [];

    var calendarEl = document.getElementById('olo-dash-calendar');
    if (!calendarEl) return;

    // ── Populate category dropdown ──
    var catSelect = document.getElementById('olo-ev-category');
    if (catSelect) {
        categories.forEach(function (cat) {
            var opt = document.createElement('option');
            opt.value = cat.id;
            opt.textContent = cat.name;
            opt.dataset.color = cat.color;
            catSelect.appendChild(opt);
        });
    }

    // ── Stats ──
    function updateStats() {
        api('GET', '/events?start=1970-01-01&end=2099-12-31').then(function (events) {
            var total = events.length;
            var now = new Date();
            var upcoming = events.filter(function (e) { return new Date(e.start) >= now; }).length;
            var past = total - upcoming;

            var statsEl = document.getElementById('olo-dash-stats');
            if (statsEl) {
                statsEl.innerHTML =
                    stat('#6366F1', 'dashicons-calendar-alt', total, 'Eventi totali') +
                    stat('#10B981', 'dashicons-arrow-right-alt', upcoming, 'Prossimi') +
                    stat('#9CA3AF', 'dashicons-backup', past, 'Passati') +
                    stat('#EC4899', 'dashicons-category', categories.length, 'Categorie');
            }
        });
    }

    function stat(color, icon, num, label) {
        return '<div class="olo-dash-stat">' +
            '<div class="olo-dash-stat-icon" style="background:' + color + '">' +
            '<span class="dashicons ' + icon + '"></span></div>' +
            '<div class="olo-dash-stat-text">' +
            '<div class="olo-dash-stat-num">' + num + '</div>' +
            '<div class="olo-dash-stat-label">' + label + '</div>' +
            '</div></div>';
    }

    updateStats();

    // ── FullCalendar ──
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'it',
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listMonth'
        },
        buttonText: {
            today: 'Oggi',
            month: 'Mese',
            week: 'Settimana',
            list: 'Lista'
        },
        allDayText: 'Tutto il giorno',
        noEventsText: 'Nessun evento',

        events: {
            url: restUrl + '/events',
            method: 'GET',
            failure: function () { toast('Errore nel caricamento eventi', 'error'); }
        },

        editable: true,
        selectable: true,
        eventDisplay: 'block',
        dayMaxEvents: 4,
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', meridiem: false, hour12: false },

        // Click on day → new event
        dateClick: function (info) {
            openModal(null, info.dateStr);
        },

        // Click on event → edit
        eventClick: function (info) {
            info.jsEvent.preventDefault();
            info.jsEvent.stopPropagation();
            openModal(info.event);
        },

        // Drag event to new date
        eventDrop: function (info) {
            var e = info.event;
            var data = {
                start_date: formatDateISO(e.start),
                start_time: e.allDay ? '' : formatTimeISO(e.start),
            };
            if (e.end) {
                var endDate = e.allDay ? new Date(e.end.getTime() - 86400000) : e.end;
                data.end_date = formatDateISO(endDate);
                data.end_time = e.allDay ? '' : formatTimeISO(e.end);
            }
            api('PUT', '/events/' + e.id, data).then(function () {
                toast('Evento spostato');
                updateStats();
            }).catch(function () {
                info.revert();
                toast('Errore nello spostamento', 'error');
            });
        },

        // Resize event
        eventResize: function (info) {
            var e = info.event;
            if (e.end) {
                var endDate = e.allDay ? new Date(e.end.getTime() - 86400000) : e.end;
                api('PUT', '/events/' + e.id, {
                    end_date: formatDateISO(endDate),
                    end_time: e.allDay ? '' : formatTimeISO(e.end),
                }).then(function () {
                    toast('Durata aggiornata');
                }).catch(function () {
                    info.revert();
                    toast('Errore nell\'aggiornamento', 'error');
                });
            }
        }
    });

    calendar.render();

    // ── Modal ──
    var modal = document.getElementById('olo-dash-modal');
    var form = document.getElementById('olo-dash-event-form');
    var modalTitle = modal.querySelector('.olo-dash-modal-title');
    var deleteBtn = document.getElementById('olo-dash-delete');
    var saveBtn = document.getElementById('olo-dash-save');
    var cancelBtn = document.getElementById('olo-dash-cancel');
    var closeBtn = modal.querySelector('.olo-dash-modal-close');
    var backdrop = modal.querySelector('.olo-dash-modal-backdrop');

    var allDayCheck = document.getElementById('olo-ev-allday');
    var timeFields = document.querySelectorAll('.olo-ev-time-field');
    var colorInput = document.getElementById('olo-ev-color');
    var colorReset = modal.querySelector('.olo-dash-color-reset');

    var currentEventId = null;

    // All day toggle
    allDayCheck.addEventListener('change', function () {
        timeFields.forEach(function (f) {
            f.style.opacity = allDayCheck.checked ? '0.3' : '1';
            f.style.pointerEvents = allDayCheck.checked ? 'none' : '';
        });
    });

    // Color reset
    if (colorReset) {
        colorReset.addEventListener('click', function () {
            colorInput.value = '#6366F1';
        });
    }

    // Category change → set color from category
    if (catSelect) {
        catSelect.addEventListener('change', function () {
            var opt = catSelect.options[catSelect.selectedIndex];
            if (opt && opt.dataset.color) {
                colorInput.value = opt.dataset.color;
            }
        });
    }

    // Auto-fill end date
    var startDateInput = document.getElementById('olo-ev-start-date');
    var endDateInput = document.getElementById('olo-ev-end-date');
    if (startDateInput && endDateInput) {
        startDateInput.addEventListener('change', function () {
            if (!endDateInput.value || endDateInput.value < startDateInput.value) {
                endDateInput.value = startDateInput.value;
            }
        });
    }

    function openModal(event, dateStr) {
        form.reset();
        currentEventId = null;

        if (event) {
            // Edit mode
            currentEventId = event.id;
            modalTitle.textContent = 'Modifica evento';
            deleteBtn.style.display = '';
            form.elements.event_id.value = event.id;
            form.elements.title.value = event.title;

            var ep = event.extendedProps || {};
            form.elements.start_date.value = ep.startDate || formatDateISO(event.start);
            form.elements.start_time.value = ep.startTime || (event.allDay ? '09:00' : formatTimeISO(event.start));
            form.elements.end_date.value = ep.endDate || (event.end ? formatDateISO(event.allDay ? new Date(event.end.getTime() - 86400000) : event.end) : '');
            form.elements.end_time.value = ep.endTime || (event.end && !event.allDay ? formatTimeISO(event.end) : '10:00');
            form.elements.all_day.checked = event.allDay;
            form.elements.location.value = ep.location || '';
            form.elements.event_url.value = ep.eventUrl || '';
            form.elements.excerpt.value = ep.excerpt || '';
            form.elements.color.value = ep.eventColor || event.backgroundColor || '#6366F1';
            form.elements.category_id.value = ep.categoryId || '0';

            // Trigger allDay visual
            allDayCheck.dispatchEvent(new Event('change'));
        } else {
            // New event
            modalTitle.textContent = 'Nuovo evento';
            deleteBtn.style.display = 'none';
            form.elements.start_date.value = dateStr || formatDateISO(new Date());
            form.elements.start_time.value = '09:00';
            form.elements.end_time.value = '10:00';
            form.elements.color.value = '#6366F1';

            allDayCheck.checked = false;
            allDayCheck.dispatchEvent(new Event('change'));
        }

        modal.style.display = 'flex';
        setTimeout(function () { form.elements.title.focus(); }, 100);
    }

    function closeModal() {
        modal.style.display = 'none';
        currentEventId = null;
    }

    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    backdrop.addEventListener('click', closeModal);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.style.display !== 'none') closeModal();
    });

    // New event button
    document.getElementById('olo-dash-add-event').addEventListener('click', function () {
        openModal(null);
    });

    // ── Save ──
    saveBtn.addEventListener('click', function () {
        var title = form.elements.title.value.trim();
        if (!title) {
            form.elements.title.focus();
            toast('Il titolo è obbligatorio', 'error');
            return;
        }
        if (!form.elements.start_date.value) {
            form.elements.start_date.focus();
            toast('La data di inizio è obbligatoria', 'error');
            return;
        }

        var data = {
            title: title,
            start_date: form.elements.start_date.value,
            start_time: allDayCheck.checked ? '' : form.elements.start_time.value,
            end_date: form.elements.end_date.value || '',
            end_time: allDayCheck.checked ? '' : form.elements.end_time.value,
            all_day: allDayCheck.checked,
            location: form.elements.location.value,
            event_url: form.elements.event_url.value,
            color: form.elements.color.value,
            category_id: parseInt(form.elements.category_id.value) || 0,
            excerpt: form.elements.excerpt.value,
        };

        saveBtn.classList.add('olo-dash-loading');

        var method = currentEventId ? 'PUT' : 'POST';
        var endpoint = currentEventId ? '/events/' + currentEventId : '/events';

        api(method, endpoint, data).then(function () {
            closeModal();
            calendar.refetchEvents();
            updateStats();
            toast(currentEventId ? 'Evento aggiornato' : 'Evento creato', 'success');
        }).catch(function (err) {
            toast('Errore: ' + (err.message || 'sconosciuto'), 'error');
        }).finally(function () {
            saveBtn.classList.remove('olo-dash-loading');
        });
    });

    // ── Delete ──
    var confirmDialog = document.getElementById('olo-dash-confirm');
    var confirmName = confirmDialog.querySelector('.olo-dash-confirm-name');
    var deleteEventId = null;

    deleteBtn.addEventListener('click', function () {
        if (!currentEventId) return;
        deleteEventId = currentEventId;
        confirmName.textContent = form.elements.title.value;
        modal.style.display = 'none';
        confirmDialog.style.display = 'flex';
    });

    document.getElementById('olo-dash-confirm-no').addEventListener('click', function () {
        confirmDialog.style.display = 'none';
    });

    confirmDialog.querySelector('.olo-dash-modal-backdrop').addEventListener('click', function () {
        confirmDialog.style.display = 'none';
    });

    document.getElementById('olo-dash-confirm-yes').addEventListener('click', function () {
        if (!deleteEventId) return;
        api('DELETE', '/events/' + deleteEventId).then(function () {
            confirmDialog.style.display = 'none';
            calendar.refetchEvents();
            updateStats();
            toast('Evento eliminato', 'success');
        }).catch(function () {
            toast('Errore nell\'eliminazione', 'error');
        });
        deleteEventId = null;
    });

    // ── API Helper ──
    function api(method, endpoint, data) {
        var opts = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': nonce,
            },
        };
        if (data && method !== 'GET') {
            opts.body = JSON.stringify(data);
        }
        return fetch(restUrl + endpoint, opts).then(function (res) {
            if (!res.ok) return res.json().then(function (j) { throw j; });
            return res.json();
        });
    }

    // ── Toast ──
    function toast(msg, type) {
        var el = document.getElementById('olo-dash-toast');
        el.textContent = msg;
        el.className = 'olo-dash-toast' + (type ? ' ' + type : ' success');
        el.style.display = 'block';
        clearTimeout(el._timer);
        el._timer = setTimeout(function () { el.style.display = 'none'; }, 3000);
    }

    // ── Date Helpers ──
    function formatDateISO(d) {
        if (!d) return '';
        return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
    }

    function formatTimeISO(d) {
        if (!d) return '';
        return pad(d.getHours()) + ':' + pad(d.getMinutes());
    }

    function pad(n) {
        return n < 10 ? '0' + n : '' + n;
    }

})();
