/**
 * Olo Calendar — Frontend (FullCalendar init + filter + modal)
 */
(function () {
    'use strict';

    var MONTH_NAMES = ['Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno',
        'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'];
    var DAY_NAMES = ['Domenica', 'Lunedì', 'Martedì', 'Mercoledì', 'Giovedì', 'Venerdì', 'Sabato'];

    // ── Initialize all calendars on page ──
    document.querySelectorAll('.olo-calendar-wrap').forEach(initCalendar);

    function initCalendar(wrap) {
        var container = wrap.querySelector('.olo-calendar-container');
        if (!container) return;

        var config;
        try { config = JSON.parse(wrap.dataset.config || '{}'); } catch (e) { config = {}; }

        // ── Build toolbar ──
        var leftBtns = 'prev,next';
        if (config.showToday !== false) leftBtns += ' today';

        var rightBtns = [];
        if (config.showMonth !== false) rightBtns.push('dayGridMonth');
        if (config.showWeek !== false) rightBtns.push('timeGridWeek');
        if (config.showDay !== false) rightBtns.push('timeGridDay');
        if (config.showList !== false) rightBtns.push('listMonth');

        var toolbarConfig = config.showToolbar !== false ? {
            left: leftBtns,
            center: 'title',
            right: rightBtns.join(',')
        } : false;

        // ── Init FullCalendar ──
        var calendar = new FullCalendar.Calendar(container, {
            initialView: config.defaultView || 'dayGridMonth',
            locale: config.locale || 'it',
            height: config.height || 'auto',
            headerToolbar: toolbarConfig,
            buttonText: {
                today: 'Oggi',
                month: 'Mese',
                week: 'Settimana',
                day: 'Giorno',
                list: 'Lista'
            },
            allDayText: 'Tutto il giorno',
            noEventsText: 'Nessun evento da mostrare',

            events: {
                url: config.restUrl || '/wp-json/olo-calendar/v1/events',
                method: 'GET',
                extraParams: function () {
                    var params = {};
                    if (config.categories) params.categories = config.categories;
                    return params;
                },
                failure: function () {
                    console.warn('[Olo Calendar] Errore nel caricamento degli eventi.');
                }
            },

            eventDisplay: 'block',
            dayMaxEvents: 3,
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                meridiem: false,
                hour12: false
            },

            eventClick: function (info) {
                info.jsEvent.preventDefault();
                info.jsEvent.stopPropagation();
                showModal(info.event);
            },

            windowResize: function () {
                if (window.innerWidth < 640) {
                    if (calendar.view.type !== 'listMonth') {
                        calendar.changeView('listMonth');
                    }
                }
            }
        });

        calendar.render();

        // Mobile auto-switch
        if (window.innerWidth < 640 && config.defaultView !== 'listMonth') {
            calendar.changeView('listMonth');
        }

        // ── Category filter ──
        initCategoryFilter(wrap, calendar);
    }

    // ── Category Filter Logic ──

    function initCategoryFilter(wrap, calendar) {
        var filterWrap = wrap.querySelector('.olo-cal-filters');
        if (!filterWrap) return;

        var allBtn = filterWrap.querySelector('.olo-cal-filter-all');
        var catItems = filterWrap.querySelectorAll('.olo-cal-filter-item:not(.olo-cal-filter-all)');
        var isCheckboxStyle = filterWrap.classList.contains('olo-cal-filters--checkboxes');

        // Pills/minimal: click toggles active class
        // Checkboxes: use checkbox state
        catItems.forEach(function (item) {
            item.addEventListener('click', function (e) {
                if (isCheckboxStyle) {
                    // Let checkbox handle itself, but deactivate "all" if needed
                    setTimeout(function () { syncFilter(); }, 10);
                    return;
                }
                e.preventDefault();
                item.classList.toggle('active');
                // Deactivate "all" when individual selected
                if (allBtn) allBtn.classList.remove('active');
                // If none active, re-activate all
                var anyActive = false;
                catItems.forEach(function (c) { if (c.classList.contains('active')) anyActive = true; });
                if (!anyActive && allBtn) {
                    allBtn.classList.add('active');
                    catItems.forEach(function (c) { c.classList.add('active'); });
                }
                applyFilter(calendar, wrap);
            });
        });

        if (allBtn) {
            allBtn.addEventListener('click', function (e) {
                if (isCheckboxStyle) {
                    var cb = allBtn.querySelector('input[type="checkbox"]');
                    setTimeout(function () {
                        var checked = cb ? cb.checked : true;
                        catItems.forEach(function (item) {
                            var icb = item.querySelector('input[type="checkbox"]');
                            if (icb) icb.checked = checked;
                            if (checked) item.classList.add('active');
                            else item.classList.remove('active');
                        });
                        if (checked) allBtn.classList.add('active');
                        applyFilter(calendar, wrap);
                    }, 10);
                    return;
                }
                e.preventDefault();
                allBtn.classList.add('active');
                catItems.forEach(function (c) { c.classList.add('active'); });
                applyFilter(calendar, wrap);
            });
        }

        function syncFilter() {
            var allChecked = true;
            var noneChecked = true;
            catItems.forEach(function (item) {
                var cb = item.querySelector('input[type="checkbox"]');
                if (cb && cb.checked) {
                    item.classList.add('active');
                    noneChecked = false;
                } else {
                    item.classList.remove('active');
                    allChecked = false;
                }
            });
            // Update "all" checkbox
            if (allBtn) {
                var allCb = allBtn.querySelector('input[type="checkbox"]');
                if (allCb) allCb.checked = allChecked || noneChecked;
                if (allChecked || noneChecked) allBtn.classList.add('active');
                else allBtn.classList.remove('active');
            }
            // If none checked, show all
            if (noneChecked) {
                catItems.forEach(function (item) {
                    item.classList.add('active');
                    var cb = item.querySelector('input[type="checkbox"]');
                    if (cb) cb.checked = true;
                });
                if (allBtn) {
                    allBtn.classList.add('active');
                    var allCb2 = allBtn.querySelector('input[type="checkbox"]');
                    if (allCb2) allCb2.checked = true;
                }
            }
            applyFilter(calendar, wrap);
        }
    }

    function applyFilter(calendar, wrap) {
        var filterWrap = wrap.querySelector('.olo-cal-filters');
        if (!filterWrap) return;

        var allBtn = filterWrap.querySelector('.olo-cal-filter-all');
        var showAll = allBtn && allBtn.classList.contains('active');

        var activeSlugs = [];
        if (!showAll) {
            filterWrap.querySelectorAll('.olo-cal-filter-item.active:not(.olo-cal-filter-all)').forEach(function (item) {
                var slug = item.dataset.cat;
                if (slug) activeSlugs.push(slug);
            });
        }

        calendar.getEvents().forEach(function (event) {
            var cats = (event.extendedProps.categories || []).map(function (c) { return c.slug; });
            var visible = showAll || cats.length === 0 || cats.some(function (c) { return activeSlugs.indexOf(c) !== -1; });
            event.setProp('display', visible ? 'auto' : 'none');
        });
    }

    // ── Modal ──

    var modal = document.getElementById('olo-cal-modal');
    if (!modal) return;

    var backdrop = modal.querySelector('.olo-cal-modal-backdrop');
    var closeBtn = modal.querySelector('.olo-cal-modal-close');

    if (backdrop) backdrop.addEventListener('click', closeModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.style.display !== 'none') closeModal();
    });

    function closeModal() {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }

    function showModal(event) {
        var props = event.extendedProps || {};

        modal.querySelector('.olo-cal-modal-title').textContent = event.title;

        // Image
        var imgWrap = modal.querySelector('.olo-cal-modal-image');
        var img = imgWrap.querySelector('img');
        if (props.image) {
            img.src = props.image;
            img.alt = event.title;
            imgWrap.style.display = '';
        } else {
            imgWrap.style.display = 'none';
        }

        // Categories
        var catsWrap = modal.querySelector('.olo-cal-modal-cats');
        catsWrap.innerHTML = '';
        if (props.categories && props.categories.length) {
            props.categories.forEach(function (cat) {
                var span = document.createElement('span');
                span.className = 'olo-cal-modal-cat';
                span.textContent = cat.name;
                span.style.backgroundColor = cat.color || event.backgroundColor || 'var(--olo-cal-primary)';
                catsWrap.appendChild(span);
            });
        }

        // Date
        modal.querySelector('.olo-cal-modal-date-text').textContent = formatDate(event.start, event.end, event.allDay);

        // Time
        var timeWrap = modal.querySelector('.olo-cal-modal-time');
        var timeText = modal.querySelector('.olo-cal-modal-time-text');
        if (!event.allDay && event.start) {
            var t = formatTime(event.start);
            if (event.end) t += ' \u2013 ' + formatTime(event.end);
            timeText.textContent = t;
            timeWrap.style.display = '';
        } else {
            timeWrap.style.display = 'none';
        }

        // Location
        var locWrap = modal.querySelector('.olo-cal-modal-location');
        var locText = modal.querySelector('.olo-cal-modal-location-text');
        if (props.location) {
            if (props.locationUrl) {
                locText.innerHTML = '<a href="' + escHtml(props.locationUrl) + '" target="_blank" rel="noopener" style="color:inherit;text-decoration:underline">' + escHtml(props.location) + '</a>';
            } else {
                locText.textContent = props.location;
            }
            locWrap.style.display = '';
        } else {
            locWrap.style.display = 'none';
        }

        // Excerpt
        modal.querySelector('.olo-cal-modal-excerpt').textContent = props.excerpt || '';

        // Actions
        var detailLink = modal.querySelector('.olo-cal-modal-link');
        detailLink.href = props.permalink || '#';
        detailLink.style.display = props.permalink ? '' : 'none';

        var extLink = modal.querySelector('.olo-cal-modal-extlink');
        extLink.href = props.eventUrl || '#';
        extLink.style.display = props.eventUrl ? '' : 'none';

        // Color accent
        var accent = event.backgroundColor || '';
        if (accent) modal.querySelector('.olo-cal-modal-link').style.backgroundColor = accent;

        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    // ── Helpers ──

    function formatDate(start, end, allDay) {
        if (!start) return '';
        var s = DAY_NAMES[start.getDay()] + ' ' + start.getDate() + ' ' + MONTH_NAMES[start.getMonth()] + ' ' + start.getFullYear();
        if (end && end.getTime() !== start.getTime()) {
            var endAdj = allDay ? new Date(end.getTime() - 86400000) : end;
            if (endAdj.toDateString() !== start.toDateString()) {
                s += ' \u2013 ' + endAdj.getDate() + ' ' + MONTH_NAMES[endAdj.getMonth()];
                if (endAdj.getFullYear() !== start.getFullYear()) s += ' ' + endAdj.getFullYear();
            }
        }
        return s;
    }

    function formatTime(date) {
        return String(date.getHours()).padStart(2, '0') + ':' + String(date.getMinutes()).padStart(2, '0');
    }

    function escHtml(str) {
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
})();
