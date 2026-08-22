/* ═══════════════════════════════════════════════════════════════════
   Olobuild Dashboard — Cockpit interactions
   Vanilla JS, no framework — fetch REST + UI state + persist via REST
   ═══════════════════════════════════════════════════════════════════ */
(function() {
    'use strict';

    const cfg = window.oloDashboardData || {};
    const root = document.querySelector('.olo-cockpit-wrap');
    if (!root) return;

    const grid = root.querySelector('.olo-cockpit-grid');
    const headers = { 'X-WP-Nonce': cfg.nonce, 'Content-Type': 'application/json' };

    /* ─────────────────────────────────────────────────────────────────
       Helpers
       ───────────────────────────────────────────────────────────────── */
    const T = (k, fb) => (cfg.i18n && cfg.i18n[k]) || fb;

    function api(path, opts) {
        return fetch(cfg.restUrl + path, Object.assign({ headers, credentials: 'same-origin' }, opts || {}))
            .then(r => r.ok ? r.json() : Promise.reject(new Error('HTTP ' + r.status)));
    }

    function debounce(fn, ms) {
        let t; return function() {
            clearTimeout(t);
            t = setTimeout(fn.bind(this, ...arguments), ms);
        };
    }

    /* Inline icons — minimal stroke set, viewBox 24 ─────────────────── */
    const ICONS = {
        fileText:  '<rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h4"/>',
        template:  '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>',
        form:      '<path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>',
        alert:     '<path d="M12 4l9 16H3z"/><path d="M12 11v4M12 18h.01"/>',
        warn:      '<path d="M12 4l9 16H3z"/><path d="M12 11v4M12 18h.01"/>',
        trendUp:   '<path d="M3 17l6-6 4 4 8-8M14 7h7v7"/>',
        trendFlat: '<path d="M3 12h18"/>',
        rocket:    '<path d="M5 19l3-3a4 4 0 015.7 0l.3.3 4-9-9 4 .3.3a4 4 0 010 5.7L5 19zM4 14a3 3 0 00-1 6 3 3 0 006-1"/>',
        play:      '<path d="M7 5l12 7-12 7z"/>',
        question:  '<circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 015 0c0 1.5-2.5 2-2.5 4M12 17h.01"/>',
        bell:      '<path d="M6 16V11a6 6 0 0112 0v5l2 2H4l2-2zM10 20a2 2 0 004 0"/>',
        panelRight:'<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M15 4v16"/>',
        collapse:  '<path d="M9 6l-6 6 6 6M15 6l6 6-6 6"/>',
        pin2:      '<path d="M14 3l7 7-3 3 1 5-5-1-7 7v-4l-3-3 7-7-1-5z"/>',
        pinFill:   '<path d="M14 3l7 7-3 3 1 5-5-1-7 7v-4l-3-3 7-7-1-5z" fill="currentColor"/>',
        external:  '<path d="M14 4h6v6M20 4l-8 8M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4"/>',
        users:     '<circle cx="9" cy="8" r="3"/><path d="M3 20a6 6 0 0112 0"/><circle cx="17" cy="9" r="2.5"/><path d="M14 20a5 5 0 017-4.5"/>',
        search:    '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>',
        x:         '<path d="M6 6l12 12M18 6L6 18"/>',
        edit:      '<path d="M4 20l4-1 11-11-3-3L5 16l-1 4z"/>',
        plus:      '<path d="M12 5v14M5 12h14"/>',
        arrow:     '<path d="M5 12h14M13 6l6 6-6 6"/>',
        globe:     '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 010 18M12 3a14 14 0 000 18"/>',
    };
    function svgIcon(name, size) {
        size = size || 16;
        const path = ICONS[name] || '';
        return '<svg width="' + size + '" height="' + size + '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">' + path + '</svg>';
    }

    /* ─────────────────────────────────────────────────────────────────
       Preferenze: state cache + save (debounced)
       ───────────────────────────────────────────────────────────────── */
    // Sotto 1366px lo stato di default è 'collapsed' (mini-bar laterale):
    // sopra serve spazio per le card "Impara/Aiuto", sotto non c'è e schiacciarle
    // produceva strisce verticali colorate (bug). Salva sempre la preferenza
    // utente: chi tocca il toggle vince.
    const defaultRail = window.innerWidth < 1366 ? 'collapsed' : 'expanded';
    let prefs = Object.assign({
        pinned: ['tpl', 'cfg', 'media'],
        rail: defaultRail,
        // Spento di serie: deve dire lo stesso di admin_body_class() in PHP.
        app_mode: false,
        banners_off: []
    }, cfg.prefs || {});

    const savePrefs = debounce(() => {
        api('dashboard/prefs', {
            method: 'POST',
            body: JSON.stringify(prefs)
        }).catch(() => {});
    }, 400);

    /* ─────────────────────────────────────────────────────────────────
       KPI strip — fetch e render
       ───────────────────────────────────────────────────────────────── */
    function renderKpis(data) {
        const wrap = root.querySelector('.olo-kpi-strip');
        if (!wrap || !Array.isArray(data)) return;
        wrap.innerHTML = data.map(k => {
            const trendCls = k.trend === 'up' ? 'up' : (k.trend === 'warn' ? 'warn' : '');
            const trendIc  = k.trend === 'up' ? 'trendUp' : (k.trend === 'warn' ? 'warn' : 'trendFlat');
            const href = k.href ? ('href="' + k.href + '"') : '';
            const tag  = k.href ? 'a' : 'div';
            return '<' + tag + ' class="olo-kpi ' + trendCls + '" ' + href + '>' +
                '<div class="kpi-h"><span class="kpi-ic">' + svgIcon(k.icon, 13) + '</span><span>' + escapeHtml(k.label) + '</span></div>' +
                '<div class="kpi-val">' + escapeHtml(String(k.value)) + '</div>' +
                '<div class="kpi-d">' + svgIcon(trendIc, 11) + ' ' + escapeHtml(k.delta || '') + '</div>' +
            '</' + tag + '>';
        }).join('');
    }

    /* ─────────────────────────────────────────────────────────────────
       Recent strip
       ───────────────────────────────────────────────────────────────── */
    function renderRecent(data) {
        const wrap = root.querySelector('.olo-recent-strip');
        if (!wrap) return;
        if (!Array.isArray(data) || !data.length) {
            wrap.innerHTML = '<div class="olo-recent-empty">' + escapeHtml(T('emptyRecent', 'Nessuna attività recente. Crea o modifica una pagina per iniziare.')) + '</div>';
            return;
        }
        wrap.innerHTML = data.map(r => {
            const bg = r.thumb
                ? 'background-image:url(' + encodeURI(r.thumb) + ');'
                : 'background:' + r.thumb_grad + ';';
            const pillTxt = r.status === 'live' ? T('live', 'live') : T('draft', 'bozza');
            const pillCls = r.status || '';
            return '<a class="olo-recent-card" href="' + r.href + '">' +
                '<div class="thumb" style="' + bg + '">' +
                  (r.status ? '<span class="pill ' + pillCls + '">' + pillTxt + '</span>' : '') +
                '</div>' +
                '<div class="body">' +
                  '<div class="title">' + escapeHtml(r.title) + '</div>' +
                  '<div class="meta"><span class="type">' + escapeHtml(r.type) + '</span> · ' + escapeHtml(r.time) + '</div>' +
                '</div>' +
            '</a>';
        }).join('');
    }

    /* ─────────────────────────────────────────────────────────────────
       Changelog (rail)
       ───────────────────────────────────────────────────────────────── */
    function renderChangelog(data) {
        const wrap = root.querySelector('[data-olo-changelog]');
        if (!wrap || !Array.isArray(data)) return;
        if (!data.length) { wrap.innerHTML = ''; return; }
        wrap.innerHTML = data.map((c, i) => {
            const tagCls = (c.tag || '').toLowerCase().replace('à', 'a');
            const items = (c.items || []).map(it => '<li>' + escapeHtml(it) + '</li>').join('');
            return '<div class="olo-cl-item ' + (i > 0 ? 'old' : '') + '">' +
                '<div class="v">' + escapeHtml(c.v) +
                (c.date ? ' <span class="date">· ' + escapeHtml(c.date) + '</span>' : '') +
                (c.tag ? ' <span class="tag ' + tagCls + '">' + escapeHtml(c.tag) + '</span>' : '') +
                '</div>' +
                (items ? '<ul>' + items + '</ul>' : '') +
            '</div>';
        }).join('');
    }

    /* ─────────────────────────────────────────────────────────────────
       Manage tile pin
       ───────────────────────────────────────────────────────────────── */
    function applyPinSort() {
        const grid = root.querySelector('.olo-manage-grid');
        if (!grid) return;
        const tiles = Array.from(grid.querySelectorAll('.olo-manage-tile'));
        tiles.sort((a, b) => {
            const ap = prefs.pinned.includes(a.dataset.id) ? 1 : 0;
            const bp = prefs.pinned.includes(b.dataset.id) ? 1 : 0;
            if (ap !== bp) return bp - ap;
            return parseInt(a.dataset.order || 0, 10) - parseInt(b.dataset.order || 0, 10);
        });
        tiles.forEach(t => grid.appendChild(t));
        // Update pin icons
        tiles.forEach(t => {
            const id = t.dataset.id;
            const btn = t.querySelector('.pin');
            if (!btn) return;
            const on = prefs.pinned.includes(id);
            btn.classList.toggle('on', on);
            btn.innerHTML = svgIcon(on ? 'pinFill' : 'pin2', 13);
            btn.setAttribute('title', on ? T('unpin', 'Rimuovi dai preferiti') : T('pin', 'Aggiungi ai preferiti'));
        });
    }

    function bindPin() {
        root.addEventListener('click', e => {
            const btn = e.target.closest('.olo-manage-tile .pin');
            if (!btn) return;
            e.preventDefault();
            e.stopPropagation();
            const tile = btn.closest('.olo-manage-tile');
            const id = tile.dataset.id;
            if (!id) return;
            const idx = prefs.pinned.indexOf(id);
            if (idx >= 0) prefs.pinned.splice(idx, 1);
            else prefs.pinned.push(id);
            applyPinSort();
            savePrefs();
        });
    }

    /* ─────────────────────────────────────────────────────────────────
       Rail toggle
       ───────────────────────────────────────────────────────────────── */
    function applyRail() {
        if (!grid) return;
        grid.classList.toggle('collapsed', prefs.rail === 'collapsed');
        const head = root.querySelector('.olo-rail .rail-head');
        if (head) {
            const h2 = head.querySelector('h2');
            if (h2) h2.style.display = prefs.rail === 'collapsed' ? 'none' : '';
            const tg = head.querySelector('.toggle');
            if (tg) {
                tg.innerHTML = svgIcon(prefs.rail === 'collapsed' ? 'panelRight' : 'collapse', 13);
                tg.setAttribute('title', prefs.rail === 'collapsed' ? T('expand', 'Espandi pannello') : T('collapse', 'Comprimi pannello'));
            }
        }
    }

    function bindRail() {
        root.addEventListener('click', e => {
            const tg = e.target.closest('.olo-rail .toggle, .olo-rail-mini button');
            if (!tg) return;
            // mini buttons espandono il rail
            if (tg.matches('.olo-rail-mini button')) {
                prefs.rail = 'expanded';
            } else {
                prefs.rail = prefs.rail === 'collapsed' ? 'expanded' : 'collapsed';
            }
            applyRail();
            savePrefs();
        });
    }

    /* ─────────────────────────────────────────────────────────────────
       Banner dismiss
       ───────────────────────────────────────────────────────────────── */
    function bindBanner() {
        root.addEventListener('click', e => {
            const x = e.target.closest('.olo-banner .x');
            if (!x) return;
            const banner = x.closest('.olo-banner');
            const id = banner.dataset.banner;
            banner.style.display = 'none';
            if (id && !prefs.banners_off.includes(id)) {
                prefs.banners_off.push(id);
                savePrefs();
            }
        });
    }

    /* ─────────────────────────────────────────────────────────────────
       New page: crea template type=page (auto-crea anche pagina WP
       collegata via maybe_auto_create_linked_page) → redirect builder
       ───────────────────────────────────────────────────────────────── */
    function bindNewPage() {
        root.addEventListener('click', e => {
            const btn = e.target.closest('[data-olo-new-page]');
            if (!btn) return;
            e.preventDefault();
            if (btn.disabled) return;
            btn.disabled = true;
            const origLabel = btn.querySelector('.t');
            const origText = origLabel ? origLabel.textContent : '';
            if (origLabel) origLabel.textContent = T('creating', 'Creazione…');

            api('templates', {
                method: 'POST',
                body: JSON.stringify({
                    title: T('untitled', 'Senza titolo'),
                    type: 'page',
                    content: [],
                    settings: {},
                    status: 'draft',
                }),
            }).then(tpl => {
                if (!tpl || !tpl.id) throw new Error('no id returned');
                // Redirect al builder fullscreen
                const adminUrl = (cfg.adminUrl || '/wp-admin/');
                window.location.href = adminUrl + 'admin.php?page=olobuilder-templates&template_id=' + tpl.id;
            }).catch(err => {
                btn.disabled = false;
                if (origLabel) origLabel.textContent = origText;
                console.error('[olo-dashboard] new page error', err);
                alert(T('newPageError', 'Errore nella creazione della pagina. Riprova.'));
            });
        });
    }

    /* ─────────────────────────────────────────────────────────────────
       App-mode toggle (icona nella topbar)
       ───────────────────────────────────────────────────────────────── */
    function bindAppModeToggle() {
        const btn = root.querySelector('[data-olo-app-mode-toggle]');
        if (!btn) return;
        btn.addEventListener('click', () => {
            prefs.app_mode = !prefs.app_mode;
            document.body.classList.toggle('olobuild-app-mode', prefs.app_mode);
            // strip back-to-wp visibile solo in app-mode
            const back = root.querySelector('.olo-cockpit-appback');
            if (back) back.style.display = prefs.app_mode ? '' : 'none';
            savePrefs();
        });
    }

    /* ─────────────────────────────────────────────────────────────────
       Search palette ⌘K
       ───────────────────────────────────────────────────────────────── */
    function buildPalette() {
        if (root.querySelector('.olo-palette-back')) return;
        const html = '<div class="olo-palette-back" data-olo-palette>' +
            '<div class="olo-palette" role="dialog" aria-label="Cerca">' +
                '<div class="olo-palette-input">' + svgIcon('search', 16) +
                    '<input type="text" placeholder="' + escapeHtml(T('searchPh', 'Cerca pagine, template, impostazioni…')) + '" autocomplete="off"/>' +
                    '<kbd>ESC</kbd>' +
                '</div>' +
                '<div class="olo-palette-results" data-olo-palette-results></div>' +
            '</div>' +
        '</div>';
        document.body.insertAdjacentHTML('beforeend', html);
    }

    let paletteIndex = null;
    function loadPaletteIndex() {
        if (paletteIndex) return Promise.resolve(paletteIndex);
        // Costruisce un indice statico dalle voci di menu + dati pagina
        const idx = (cfg.searchIndex || []).slice();
        paletteIndex = idx;
        return Promise.resolve(idx);
    }

    function openPalette() {
        buildPalette();
        loadPaletteIndex();
        const back = document.querySelector('.olo-palette-back');
        back.classList.add('open');
        const input = back.querySelector('input');
        input.value = '';
        renderPaletteResults('');
        setTimeout(() => input.focus(), 10);
    }
    function closePalette() {
        const back = document.querySelector('.olo-palette-back');
        if (back) back.classList.remove('open');
    }

    function renderPaletteResults(q) {
        const out = document.querySelector('[data-olo-palette-results]');
        if (!out) return;
        const ql = q.toLowerCase().trim();
        let items = paletteIndex || [];
        if (ql) {
            items = items.filter(i =>
                i.label.toLowerCase().includes(ql) ||
                (i.hint || '').toLowerCase().includes(ql)
            );
        }
        items = items.slice(0, 12);
        if (!items.length) {
            out.innerHTML = '<div class="olo-palette-empty">' + escapeHtml(T('noResults', 'Nessun risultato')) + '</div>';
            return;
        }
        out.innerHTML = items.map((i, n) => {
            return '<a class="olo-palette-item ' + (n === 0 ? 'focus' : '') + '" href="' + i.href + '">' +
                '<span class="ic">' + svgIcon(i.icon || 'arrow', 16) + '</span>' +
                '<span class="lab"><span class="t">' + escapeHtml(i.label) + '</span>' +
                (i.hint ? '<span class="h">' + escapeHtml(i.hint) + '</span>' : '') +
                '</span>' +
            '</a>';
        }).join('');
    }

    function bindPalette() {
        document.addEventListener('keydown', e => {
            const isCmd = e.metaKey || e.ctrlKey;
            if (isCmd && e.key.toLowerCase() === 'k') {
                e.preventDefault(); openPalette(); return;
            }
            const back = document.querySelector('.olo-palette-back.open');
            if (!back) return;
            if (e.key === 'Escape') { e.preventDefault(); closePalette(); }
        });
        document.addEventListener('click', e => {
            const back = e.target.closest('.olo-palette-back');
            if (back && e.target === back) closePalette();
        });
        document.addEventListener('input', e => {
            if (e.target.matches('.olo-palette-input input')) {
                renderPaletteResults(e.target.value);
            }
        });
        const trigger = root.querySelector('[data-olo-palette-trigger]');
        if (trigger) trigger.addEventListener('click', openPalette);
    }

    /* ─────────────────────────────────────────────────────────────────
       Utility
       ───────────────────────────────────────────────────────────────── */
    function escapeHtml(s) {
        if (s == null) return '';
        return String(s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    /* ─────────────────────────────────────────────────────────────────
       Boot
       ───────────────────────────────────────────────────────────────── */
    function boot() {
        // Render iniziali con dati già preloaded da PHP, se ci sono
        if (cfg.boot && cfg.boot.kpis) renderKpis(cfg.boot.kpis);
        if (cfg.boot && cfg.boot.recent) renderRecent(cfg.boot.recent);
        if (cfg.boot && cfg.boot.changelog) renderChangelog(cfg.boot.changelog);

        // Refresh in background per dati freschi
        api('dashboard/kpis').then(renderKpis).catch(() => {});
        api('dashboard/recent?limit=6').then(renderRecent).catch(() => {});
        api('dashboard/changelog?limit=3').then(renderChangelog).catch(() => {});

        applyPinSort();
        applyRail();
        bindPin();
        bindRail();
        bindBanner();
        bindAppModeToggle();
        bindPalette();
        bindNewPage();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
