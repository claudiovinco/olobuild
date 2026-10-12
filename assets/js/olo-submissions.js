/* ═══════════════════════════════════════════════════════════════════
   Olobuild — Form Submissions cockpit (Fase 3)
   Vanilla JS: filter, search live, drawer dettaglio, mark-read, delete
   ═══════════════════════════════════════════════════════════════════ */
(function() {
    'use strict';

    const cfg = window.oloSubmissionsConfig || {};
    if (!cfg.restUrl || !cfg.nonce) return;

    const T = (k, fb) => (cfg.i18n && cfg.i18n[k]) || fb;

    /* ─── State ───────────────────────────────────────────────────── */
    const state = {
        status: 'all',     // 'all' | 'unread' | 'read' | 'admitted'
        formName: '',      // filtro per form_name
        q: '',
        page: 1,
        items: [],
        total: 0,
        currentDetail: null, // {id, ...}
    };

    /* ─── DOM refs ────────────────────────────────────────────────── */
    const listEl    = document.querySelector('[data-olo-submissions]');
    const drawer    = document.querySelector('[data-olo-drawer]');
    const drawerTitle = document.querySelector('[data-olo-drawer-title]');
    const drawerMeta  = document.querySelector('[data-olo-drawer-meta]');
    const drawerBody  = document.querySelector('[data-olo-drawer-body]');
    const toolbar   = document.querySelector('.olo-toolbar');
    const searchInp = document.getElementById('olo-sub-search');

    if (!listEl) return;

    /* ─── API ─────────────────────────────────────────────────────── */
    function api(path, opts) {
        return fetch(cfg.restUrl + path, Object.assign({
            headers: { 'X-WP-Nonce': cfg.nonce, 'Content-Type': 'application/json' },
            credentials: 'same-origin',
        }, opts || {})).then(r => r.ok ? r.json() : Promise.reject(new Error('HTTP ' + r.status)));
    }

    /* ─── Helpers ─────────────────────────────────────────────────── */
    function escapeHtml(s) {
        if (s == null) return '';
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }
    function debounce(fn, ms) {
        let t; return function() { clearTimeout(t); t = setTimeout(fn.bind(this, ...arguments), ms); };
    }
    // Un valore del campo: i link si aprono; i file allegati (admin-post.php?action=olobuild_form_file) mostrano
    // «Scarica <nome>» col nome che il file aveva, senza il prefisso casuale (1.4.700).
    function valueToHtml(v) {
        const s = v == null ? '' : String(v);
        if (/^https?:\/\/\S+$/.test(s)) {
            let label = s;
            try {
                const u = new URL(s);
                const f = u.searchParams.get('action') === 'olobuild_form_file' ? u.searchParams.get('f') : null;
                if (f) label = T('downloadFile', 'Scarica %s').replace('%s', f.replace(/^[a-z0-9]{16}-/, ''));
            } catch (e) { /* resta l'indirizzo */ }
            return '<a href="' + escapeHtml(s) + '" target="_blank" rel="noopener">' + escapeHtml(label) + '</a>';
        }
        return escapeHtml(s).replace(/\n/g, '<br/>');
    }
    function fieldsToHtml(fields) {
        const rows = Object.entries(fields || {}).map(([k, v]) => {
            const label = k.replace(/[_-]+/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
            const val = Array.isArray(v) ? v.map(valueToHtml).join(', ') : valueToHtml(v);
            return '<dt>' + escapeHtml(label) + '</dt><dd>' + val + '</dd>';
        }).join('');
        return rows ? '<dl class="olo-sub-fields">' + rows + '</dl>' : '<p style="color:var(--olo-text-muted);font-size:13px">' + escapeHtml(T('noResults', '—')) + '</p>';
    }

    /* ─── Render list ─────────────────────────────────────────────── */
    function renderList() {
        if (!state.items.length) {
            listEl.innerHTML =
                '<div class="olo-empty-state">' +
                  '<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" style="opacity:.3"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>' +
                  '<h3>' + escapeHtml(T('noResults', 'Nessun invio trovato.')) + '</h3>' +
                  '<p>' + escapeHtml(T('noResultsHint', '')) + '</p>' +
                '</div>';
            return;
        }
        listEl.innerHTML = state.items.map(s => {
            const dot = s.read_status ? 'read' : 'unread';
            const display = s.name || s.email || ('#' + s.id);
            return '<button type="button" class="olo-sub-card ' + (s.read_status ? '' : 'is-unread') + '" data-sub-id="' + s.id + '">' +
                '<span class="olo-sub-dot ' + dot + '" title="' + (s.read_status ? T('markUnread') : T('markRead')) + '"></span>' +
                '<div class="olo-sub-card-body">' +
                  '<div class="olo-sub-card-head">' +
                    '<span class="olo-sub-name">' + escapeHtml(display) + '</span>' +
                    '<span class="olo-sub-form">' + escapeHtml(s.form_name) + '</span>' +
                    (s.admitted_at ? '<span class="olo-sub-ammesso">' + escapeHtml(T('admitted', 'Ammesso')) + '</span>' : '') +
                    '<span class="olo-sub-time">' + escapeHtml(s.time_diff) + '</span>' +
                  '</div>' +
                  (s.email && s.email !== display ? '<div class="olo-sub-email">' + escapeHtml(s.email) + '</div>' : '') +
                  (s.preview ? '<div class="olo-sub-preview">' + escapeHtml(s.preview) + '</div>' : '') +
                  '<div class="olo-sub-meta-row">' +
                    '<span>' + s.fields_count + ' campi</span>' +
                    (s.ip_address ? '<span>· IP ' + escapeHtml(s.ip_address) + '</span>' : '') +
                  '</div>' +
                '</div>' +
            '</button>';
        }).join('');
    }

    /* ─── Fetch ───────────────────────────────────────────────────── */
    function fetchList() {
        listEl.innerHTML =
            '<div class="olo-empty-state" data-olo-loading>' +
              '<div class="loader-spinner"></div>' +
            '</div>';
        const params = new URLSearchParams();
        if (state.status !== 'all' && !state.formName) params.set('status', state.status);
        if (state.formName) params.set('form_name', state.formName);
        if (state.q) params.set('q', state.q);
        params.set('page', state.page);
        params.set('per_page', 50);

        api('submissions?' + params.toString())
            .then(data => {
                state.items = data.items || [];
                state.total = data.total || 0;
                renderList();
                refreshChipsState();
            })
            .catch(err => {
                console.error('[olo-sub] fetch:', err);
                listEl.innerHTML = '<div class="olo-empty-state"><h3>' + escapeHtml(T('loadFailed')) + '</h3></div>';
            });
    }
    function fetchStats() {
        return api('submissions/stats').then(stats => {
            // Aggiorna i counter dei chip
            document.querySelectorAll('.olo-toolbar .chips .olo-chip').forEach(chip => {
                const id = chip.getAttribute('data-chip-id');
                const num = chip.querySelector('.num');
                if (!num) return;
                if (id === 'all')    num.textContent = stats.total  || 0;
                if (id === 'unread') num.textContent = stats.unread || 0;
                if (id === 'read')   num.textContent = stats.read   || 0;
                if (id === 'admitted') num.textContent = stats.admitted || 0;
                if (id && id.indexOf('form:') === 0) {
                    const name = id.substring(5);
                    const f = (stats.forms || []).find(x => x.name === name);
                    if (f) num.textContent = f.count;
                }
            });
            aggiornaPosti(stats.posti || []);
            return stats;
        }).catch(()=>{});
    }

    /* ─── Posti a numero chiuso: la riga sotto il titolo ──────────── */
    function aggiornaPosti(posti) {
        const el = document.querySelector('[data-olo-posti]');
        if (!el) return;
        el.textContent = posti.map(p => p.al_mese > 0
            ? T('postiMese', '%1$s: questo mese %2$s su %3$s · in tutto %4$s su %5$s').replace('%1$s', p.name).replace('%2$s', p.ammessi_mese).replace('%3$s', p.al_mese).replace('%4$s', p.ammessi).replace('%5$s', p.totale)
            : T('postiTot', '%1$s: ammessi %2$s su %3$s').replace('%1$s', p.name).replace('%2$s', p.ammessi).replace('%3$s', p.totale)
        ).join(' · ');
    }

    /* ─── Chips state sync ────────────────────────────────────────── */
    function refreshChipsState() {
        document.querySelectorAll('.olo-toolbar .chips .olo-chip').forEach(chip => {
            const id = chip.getAttribute('data-chip-id');
            let active = false;
            if (id && id.indexOf('form:') === 0) {
                active = state.formName === id.substring(5);
            } else if (id === 'all') {
                active = (state.status === 'all' && !state.formName);
            } else {
                active = state.status === id && !state.formName;
            }
            chip.classList.toggle('on', active);
        });
    }

    /* ─── Drawer dettaglio ────────────────────────────────────────── */
    function openDrawer(id) {
        drawer.hidden = false;
        document.body.classList.add('olo-drawer-open');
        drawerTitle.textContent = '#' + id;
        drawerMeta.textContent = '';
        drawerBody.innerHTML = '<div class="olo-empty-state"><div class="loader-spinner"></div></div>';

        api('submissions/' + id)
            .then(d => {
                state.currentDetail = d;
                const display = (d.fields && (d.fields.name || d.fields.nome || d.fields.email)) || ('#' + d.id);
                drawerTitle.textContent = display;
                drawerMeta.innerHTML =
                    '<span>' + escapeHtml(d.form_name || '') + '</span>' +
                    ' · <span>' + escapeHtml(d.submitted_at) + '</span>' +
                    (d.ip_address ? ' · <span>IP ' + escapeHtml(d.ip_address) + '</span>' : '') +
                    (d.admitted_at ? ' · <span class="olo-sub-ammesso">' + escapeHtml(T('admittedAt', 'Ammesso il')) + ' ' + escapeHtml(d.admitted_at) + '</span>' : '');
                drawerBody.innerHTML =
                    '<h3 class="olo-sub-section-title">' + escapeHtml(T('fields', 'Campi')) + '</h3>' +
                    fieldsToHtml(d.fields) +
                    '<h3 class="olo-sub-section-title" style="margin-top:18px">' + escapeHtml(T('metadata')) + '</h3>' +
                    '<dl class="olo-sub-fields">' +
                      '<dt>' + escapeHtml(T('submittedAt')) + '</dt><dd>' + escapeHtml(d.submitted_at) + '</dd>' +
                      '<dt>' + escapeHtml(T('ip')) + '</dt><dd><code>' + escapeHtml(d.ip_address) + '</code></dd>' +
                      '<dt>' + escapeHtml(T('userAgent')) + '</dt><dd style="word-break:break-all;font-size:11px;color:var(--olo-text-muted)">' + escapeHtml(d.user_agent) + '</dd>' +
                    '</dl>';

                // Update il toggle-read button label
                const tg = document.querySelector('[data-olo-drawer-toggle-read]');
                if (tg) tg.textContent = d.read_status ? T('markUnread') : T('markRead');
                aggiornaPulsanteAmmetti(d);

                // Aggiorna lo stato della card nella lista (se l'auto-mark-read l'ha cambiata)
                const card = document.querySelector('.olo-sub-card[data-sub-id="' + d.id + '"]');
                if (card) {
                    card.classList.toggle('is-unread', !d.read_status);
                    const dotEl = card.querySelector('.olo-sub-dot');
                    if (dotEl) dotEl.classList.toggle('unread', !d.read_status);
                    if (dotEl) dotEl.classList.toggle('read', !!d.read_status);
                }
                fetchStats();
            })
            .catch(err => {
                drawerBody.innerHTML = '<div class="olo-empty-state"><h3>' + escapeHtml(T('loadFailed')) + '</h3></div>';
            });
    }
    function closeDrawer() {
        drawer.hidden = true;
        document.body.classList.remove('olo-drawer-open');
        state.currentDetail = null;
    }

    /* ─── Bindings ────────────────────────────────────────────────── */
    function bindToolbar() {
        if (!toolbar) return;
        toolbar.addEventListener('click', e => {
            const chip = e.target.closest('.olo-chip');
            if (!chip) return;
            const id = chip.getAttribute('data-chip-id') || '';
            if (id.indexOf('form:') === 0) {
                state.formName = id.substring(5);
                state.status = 'all';
            } else {
                state.status = id || 'all';
                state.formName = '';
            }
            state.page = 1;
            fetchList();
        });

        if (searchInp) {
            const onSearch = debounce(() => {
                state.q = searchInp.value.trim();
                state.page = 1;
                fetchList();
            }, 250);
            searchInp.addEventListener('input', onSearch);
        }
    }

    function bindList() {
        listEl.addEventListener('click', e => {
            const dot = e.target.closest('.olo-sub-dot');
            if (dot) {
                e.stopPropagation();
                const card = dot.closest('.olo-sub-card');
                if (card) toggleRead(parseInt(card.getAttribute('data-sub-id'), 10));
                return;
            }
            const card = e.target.closest('.olo-sub-card');
            if (card) {
                openDrawer(parseInt(card.getAttribute('data-sub-id'), 10));
            }
        });
    }

    function bindDrawer() {
        if (!drawer) return;
        drawer.addEventListener('click', e => {
            if (e.target === drawer) closeDrawer();
            if (e.target.closest('[data-olo-drawer-close]')) closeDrawer();
            if (e.target.closest('[data-olo-drawer-admit]')) {
                if (state.currentDetail) ammetti(state.currentDetail.id);
            }
            if (e.target.closest('[data-olo-drawer-toggle-read]')) {
                if (state.currentDetail) toggleRead(state.currentDetail.id, /*fromDrawer*/ true);
            }
            if (e.target.closest('[data-olo-drawer-delete]')) {
                if (state.currentDetail && confirm(T('confirmDelete'))) {
                    deleteOne(state.currentDetail.id);
                }
            }
        });
        document.addEventListener('keydown', e => {
            if (!drawer.hidden && e.key === 'Escape') closeDrawer();
        });
    }

    /* ─── Actions ─────────────────────────────────────────────────── */
    function toggleRead(id, fromDrawer) {
        const item = state.items.find(x => x.id === id);
        const newStatus = item ? (item.read_status ? 0 : 1) : 1;
        api('submissions/' + id + '/read', {
            method: 'POST',
            body: JSON.stringify({ read: newStatus }),
        }).then(res => {
            if (item) item.read_status = res.read_status;
            renderList();
            fetchStats();
            if (fromDrawer && state.currentDetail) {
                state.currentDetail.read_status = res.read_status;
                const tg = document.querySelector('[data-olo-drawer-toggle-read]');
                if (tg) tg.textContent = res.read_status ? T('markUnread') : T('markRead');
            }
        }).catch(err => console.error('[olo-sub] toggle-read:', err));
    }

    /* Ammettere un invio di un modulo a posti (o togliere l'ammissione): il pulsante c'è solo per quei moduli. */
    function aggiornaPulsanteAmmetti(d) {
        const b = document.querySelector('[data-olo-drawer-admit]');
        if (!b) return;
        b.hidden = !d.seats;
        b.textContent = d.admitted_at ? T('unadmit', "Togli l'ammissione") : T('admit', 'Ammetti');
    }
    function ammetti(id) {
        const d = state.currentDetail;
        const b = document.querySelector('[data-olo-drawer-admit]');
        if (b) b.disabled = true;
        api('submissions/' + id + '/admit', {
            method: 'POST',
            body: JSON.stringify({ admitted: !(d && d.admitted_at) }),
        }).then(res => {
            const item = state.items.find(x => x.id === id);
            if (item) { item.admitted_at = res.admitted_at; if (res.admitted_at) item.read_status = 1; }
            if (d && d.id === id) d.admitted_at = res.admitted_at;
            renderList();
            fetchStats();
            if (d && d.id === id) openDrawer(id);
        }).catch(err => alert(T('admitFailed', 'Ammissione non riuscita') + ': ' + err.message))
          .finally(() => { if (b) b.disabled = false; });
    }

    function deleteOne(id) {
        api('submissions/' + id, { method: 'DELETE' })
            .then(() => {
                state.items = state.items.filter(x => x.id !== id);
                state.total = Math.max(0, state.total - 1);
                renderList();
                fetchStats();
                closeDrawer();
            })
            .catch(err => alert(T('deleteFailed') + ': ' + err.message));
    }

    /* ─── Boot ────────────────────────────────────────────────────── */
    function boot() {
        bindToolbar();
        bindList();
        bindDrawer();
        fetchList();
        fetchStats();
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
