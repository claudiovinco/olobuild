/**
 * Olobuild — palette di ricerca globale ⌘K.
 *
 * HAND-AUTHORED (non buildato da Vite): caricato su TUTTE le pagine admin
 * Olobuild da admin_enqueue_scripts. Sostituisce la palette che viveva in
 * dashboard.js (solo dashboard, solo voci di menu): questa cerca voci di
 * menu/azioni (dal localize), pagine e template (REST dashboard/palette,
 * caricati al primo uso) e schede + singoli campi della Configurazione
 * (JSON statico assets/data/settings-search-index.json). I campi aprono
 * la Configurazione con ?tab=…&field=…: la SettingsApp scorre al campo
 * e lo illumina.
 *
 * Nella pagina Configurazione la scorciatoia ⌘K resta alla ricerca interna
 * (più ricca lì): questo script la cede quando trova .cfg-root nel DOM.
 */
(function () {
    'use strict';

    var cfg = window.oloPaletteConfig || {};
    var i18n = cfg.i18n || {};
    function T(key, fallback) { return i18n[key] || fallback; }

    var state = {
        built: false,
        loaded: false,
        loading: false,
        pages: [],
        templates: [],
        settingsTabs: {},   // id -> {label, group}
        settingsFields: {}, // id -> [{kind,label,hint,section}]
        focusIdx: 0,
        flat: [],           // risultati correnti, piatti, per la navigazione
    };

    /* ── Stili: self-contained, prefisso .olopal- ─────────────────── */
    var CSS = '' +
        '.olopal-back{position:fixed;inset:0;z-index:100000;background:rgba(15,23,42,.4);display:none;align-items:flex-start;justify-content:center;padding:96px 16px 16px}' +
        '.olopal-back.open{display:flex}' +
        '.olopal{width:640px;max-width:100%;background:#fff;border-radius:14px;box-shadow:0 24px 60px rgba(15,23,42,.28),0 4px 12px rgba(16,24,40,.08);overflow:hidden;font-family:"Inter",-apple-system,"Segoe UI",Roboto,sans-serif;color:#1e293b}' +
        '.olopal-input{display:flex;align-items:center;gap:10px;padding:14px 16px;border-bottom:1px solid #f1f5f9}' +
        '.olopal-input svg{flex-shrink:0;color:#64748b}' +
        '.olopal-input input{flex:1;border:0;outline:0;background:none;font:inherit;font-size:15px;color:#111827;box-shadow:none}' +
        '.olopal-input kbd{font-size:11px;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:5px;padding:2px 7px;color:#94a3b8;font-family:inherit}' +
        '.olopal-results{max-height:56vh;overflow-y:auto;padding:6px 0 8px}' +
        '.olopal-ghead{font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#94a3b8;padding:10px 18px 4px}' +
        '.olopal-item{display:flex;align-items:center;gap:12px;padding:9px 18px;cursor:pointer;text-decoration:none;color:inherit;border-left:3px solid transparent}' +
        '.olopal-item.focus{background:#f8fafc;border-left-color:#94a3b8}' +
        '.olopal-item:hover{background:#f8fafc}' +
        '.olopal-item .ic{width:28px;height:28px;border-radius:7px;background:#f8fafc;border:1px solid #f1f5f9;color:#64748b;display:flex;align-items:center;justify-content:center;flex-shrink:0}' +
        '.olopal-item .lab{display:flex;flex-direction:column;min-width:0}' +
        '.olopal-item .lab .t{font-size:13.5px;font-weight:600;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}' +
        '.olopal-item .lab .h{font-size:11.5px;color:#64748b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}' +
        '.olopal-item .go{margin-left:auto;font-size:11px;color:#94a3b8;flex-shrink:0}' +
        '.olopal-item mark{background:#fde68a;color:inherit;border-radius:3px;padding:0 1px}' +
        '.olopal-empty,.olopal-loading{padding:22px 18px;font-size:13px;color:#64748b}' +
        '.olopal-foot{display:flex;align-items:center;gap:14px;padding:9px 18px;background:#f9fafb;border-top:1px solid #f1f5f9;font-size:11.5px;color:#94a3b8}' +
        '.olopal-foot b{color:#64748b;font-weight:600}' +
        '.olopal-foot .spc{flex:1}';

    var ICONS = {
        search:   '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>',
        page:     '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h4"/></svg>',
        template: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>',
        setting:  '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.87l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.7 1.7 0 0 0-1.87-.34 1.7 1.7 0 0 0-1 1.55V21a2 2 0 1 1-4 0v-.09a1.7 1.7 0 0 0-1-1.55 1.7 1.7 0 0 0-1.87.34l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.7 1.7 0 0 0 .34-1.87 1.7 1.7 0 0 0-1.55-1H3a2 2 0 1 1 0-4h.09a1.7 1.7 0 0 0 1.55-1 1.7 1.7 0 0 0-.34-1.87l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.7 1.7 0 0 0 1.87.34h0a1.7 1.7 0 0 0 1-1.55V3a2 2 0 1 1 4 0v.09a1.7 1.7 0 0 0 1 1.55h0a1.7 1.7 0 0 0 1.87-.34l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.7 1.7 0 0 0-.34 1.87v0a1.7 1.7 0 0 0 1.55 1H21a2 2 0 1 1 0 4h-.09a1.7 1.7 0 0 0-1.55 1z"/></svg>',
        arrow:    '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>',
    };

    function escapeHtml(s) {
        if (s == null) return '';
        return String(s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }
    function hl(text, q) {
        var esc = escapeHtml(text);
        if (!q) return esc;
        var safe = q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        return esc.replace(new RegExp('(' + safe + ')', 'ig'), '<mark>$1</mark>');
    }

    /* ── Dati ─────────────────────────────────────────────────────── */
    function loadData() {
        if (state.loaded || state.loading) return Promise.resolve();
        state.loading = true;
        var jobs = [];
        if (cfg.restUrl) {
            jobs.push(
                fetch(cfg.restUrl + 'dashboard/palette', { headers: { 'X-WP-Nonce': cfg.nonce || '' } })
                    .then(function (r) { return r.ok ? r.json() : { pages: [], templates: [] }; })
                    .then(function (d) {
                        state.pages = d.pages || [];
                        state.templates = d.templates || [];
                    })
                    .catch(function () {})
            );
        }
        if (cfg.indexUrl) {
            jobs.push(
                fetch(cfg.indexUrl)
                    .then(function (r) { return r.ok ? r.json() : { tabs: {}, fields: {} }; })
                    .then(function (d) {
                        state.settingsTabs = d.tabs || {};
                        state.settingsFields = d.fields || {};
                    })
                    .catch(function () {})
            );
        }
        return Promise.all(jobs).then(function () {
            state.loaded = true;
            state.loading = false;
        });
    }

    /* ── Ricerca ──────────────────────────────────────────────────── */
    function matches(q, text) {
        return text && text.toLowerCase().indexOf(q) !== -1;
    }

    function settingsTabHref(tabId) {
        return (cfg.adminUrl || '') + 'admin.php?page=olobuilder-settings&tab=' + encodeURIComponent(tabId);
    }

    function search(q) {
        var ql = q.toLowerCase().trim();
        var groups = [];

        // Vai a: voci di menu/azioni dal localize (sempre disponibili).
        var menu = (cfg.menu || []).filter(function (m) {
            return !ql || matches(ql, m.label) || matches(ql, m.hint || '');
        }).slice(0, ql ? 6 : 9).map(function (m) {
            return { icon: 'arrow', label: m.label, hint: m.hint || '', href: m.href };
        });
        if (menu.length) groups.push({ title: T('goto', 'Vai a'), items: menu });

        if (ql) {
            // Impostazioni: schede + singoli campi (deep-link col flash).
            var sItems = [];
            var tabs = state.settingsTabs;
            Object.keys(tabs).forEach(function (id) {
                var meta = tabs[id];
                if (matches(ql, meta.label)) {
                    sItems.push({ icon: 'setting', label: meta.label, hint: T('openTab', 'Configurazione') + ' · ' + meta.group, href: settingsTabHref(id) });
                }
                (state.settingsFields[id] || []).forEach(function (f) {
                    if (sItems.length >= 14) return;
                    if (matches(ql, f.label) || matches(ql, f.hint || '') || matches(ql, f.section || '')) {
                        sItems.push({
                            icon: 'setting',
                            label: f.label,
                            hint: meta.label + (f.section ? ' · ' + f.section : ''),
                            href: settingsTabHref(id) + '&field=' + encodeURIComponent(f.label),
                            go: T('goField', 'Vai al campo'),
                        });
                    }
                });
            });
            if (sItems.length) groups.push({ title: T('settings', 'Impostazioni'), items: sItems.slice(0, 6) });

            var pages = state.pages.filter(function (p) {
                return matches(ql, p.label) || matches(ql, p.hint || '');
            }).slice(0, 6).map(function (p) {
                return { icon: 'page', label: p.label, hint: p.hint || '', href: p.href, go: T('openEditor', 'Apri editor') };
            });
            if (pages.length) groups.push({ title: T('pages', 'Pagine'), items: pages });

            var tpls = state.templates.filter(function (t) {
                return matches(ql, t.label) || matches(ql, t.hint || '');
            }).slice(0, 6).map(function (t) {
                return { icon: 'template', label: t.label, hint: t.hint || '', href: t.href, go: T('openEditor', 'Apri editor') };
            });
            if (tpls.length) groups.push({ title: T('templates', 'Template'), items: tpls });
        }

        return groups;
    }

    /* ── UI ───────────────────────────────────────────────────────── */
    function build() {
        if (state.built) return;
        state.built = true;
        var style = document.createElement('style');
        style.textContent = CSS;
        document.head.appendChild(style);
        var back = document.createElement('div');
        back.className = 'olopal-back';
        back.innerHTML =
            '<div class="olopal" role="dialog" aria-modal="true" aria-label="' + escapeHtml(T('searchPh', 'Cerca pagine, template, impostazioni…')) + '">' +
                '<div class="olopal-input">' + ICONS.search +
                    '<input type="text" placeholder="' + escapeHtml(T('searchPh', 'Cerca pagine, template, impostazioni…')) + '" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" data-1p-ignore data-lpignore="true"/>' +
                    '<kbd>ESC</kbd>' +
                '</div>' +
                '<div class="olopal-results"></div>' +
                '<div class="olopal-foot"><span><b>&#8593;&#8595;</b> ' + escapeHtml(T('nav', 'scorri')) + '</span><span><b>&#8629;</b> ' + escapeHtml(T('open', 'apri')) + '</span><span class="spc"></span><span>' + escapeHtml(T('scope', 'pagine · template · impostazioni · azioni')) + '</span></div>' +
            '</div>';
        document.body.appendChild(back);

        back.addEventListener('click', function (e) { if (e.target === back) close(); });
        back.querySelector('input').addEventListener('input', function (e) { render(e.target.value); });
        back.querySelector('input').addEventListener('keydown', function (e) {
            if (e.key === 'ArrowDown') { e.preventDefault(); moveFocus(1); }
            else if (e.key === 'ArrowUp') { e.preventDefault(); moveFocus(-1); }
            else if (e.key === 'Enter') {
                e.preventDefault();
                var it = state.flat[state.focusIdx];
                if (it) { window.location.href = it.href; }
            }
        });
    }

    function render(q) {
        var out = document.querySelector('.olopal-back .olopal-results');
        if (!out) return;
        if (!state.loaded) {
            out.innerHTML = '<div class="olopal-loading">' + escapeHtml(T('loading', 'Carico l’indice…')) + '</div>';
            return;
        }
        var groups = search(q);
        state.flat = [];
        state.focusIdx = 0;
        if (!groups.length) {
            out.innerHTML = '<div class="olopal-empty">' + escapeHtml(T('noResults', 'Nessun risultato')) + '</div>';
            return;
        }
        var ql = q.toLowerCase().trim();
        var html = '';
        groups.forEach(function (g) {
            html += '<div class="olopal-ghead">' + escapeHtml(g.title) + '</div>';
            g.items.forEach(function (it) {
                var n = state.flat.length;
                state.flat.push(it);
                html += '<a class="olopal-item' + (n === 0 ? ' focus' : '') + '" data-idx="' + n + '" href="' + escapeHtml(it.href) + '">' +
                    '<span class="ic">' + (ICONS[it.icon] || ICONS.arrow) + '</span>' +
                    '<span class="lab"><span class="t">' + hl(it.label, ql) + '</span>' +
                    (it.hint ? '<span class="h">' + hl(it.hint, ql) + '</span>' : '') +
                    '</span>' +
                    (it.go ? '<span class="go">' + escapeHtml(it.go) + ' &#8594;</span>' : '') +
                '</a>';
            });
        });
        out.innerHTML = html;
    }

    function moveFocus(delta) {
        if (!state.flat.length) return;
        state.focusIdx = (state.focusIdx + delta + state.flat.length) % state.flat.length;
        var items = document.querySelectorAll('.olopal-item');
        items.forEach(function (el) { el.classList.remove('focus'); });
        var el = document.querySelector('.olopal-item[data-idx="' + state.focusIdx + '"]');
        if (el) {
            el.classList.add('focus');
            el.scrollIntoView({ block: 'nearest' });
        }
    }

    function open() {
        build();
        var back = document.querySelector('.olopal-back');
        back.classList.add('open');
        var input = back.querySelector('input');
        input.value = '';
        render('');
        loadData().then(function () {
            if (back.classList.contains('open')) render(input.value);
        });
        setTimeout(function () { input.focus(); }, 10);
    }
    function close() {
        var back = document.querySelector('.olopal-back');
        if (back) back.classList.remove('open');
    }
    function isOpen() {
        var back = document.querySelector('.olopal-back');
        return !!(back && back.classList.contains('open'));
    }

    /* ── Bind ─────────────────────────────────────────────────────── */
    function bind() {
        document.addEventListener('keydown', function (e) {
            if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                // Nella Configurazione ⌘K appartiene alla ricerca interna (a
                // livello di campo): la palette lì si apre solo dal trigger.
                if (document.querySelector('.cfg-root')) return;
                e.preventDefault();
                open();
                return;
            }
            if (e.key === 'Escape' && isOpen()) { e.preventDefault(); close(); }
        });
        document.addEventListener('click', function (e) {
            var trig = e.target.closest ? e.target.closest('[data-olo-palette-trigger]') : null;
            if (trig) { e.preventDefault(); open(); }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bind);
    } else {
        bind();
    }
})();
