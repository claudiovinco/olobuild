/**
 * OloThemePicker — selettore temi condiviso (vanilla, zero dipendenze).
 *
 * Una sola implementazione di: card con mini-anteprima reale, colonna filtri
 * categoria, ricerca live, conteggi. Usato in due contesti:
 *   - builder  → mode 'modal'  (overlay + header, azione "Importa tema" per card)
 *   - wizard   → mode 'embed'  (montato in un contenitore, card selezionabili)
 *
 * Le AZIONI (import via REST, import via AJAX del wizard, conferme, toast,
 * reload) restano al chiamante via callback: il modulo è solo UI + filtri.
 */

const ICON = {
  brand: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a9 9 0 1 0 0 18c.83 0 1.5-.67 1.5-1.5 0-.39-.15-.74-.39-1-.24-.27-.39-.62-.39-1 0-.83.67-1.5 1.5-1.5H16a5 5 0 0 0 5-5c0-4.42-4.03-8-9-8z"/><circle cx="7.5" cy="11.5" r="1.2" fill="currentColor"/><circle cx="12" cy="8" r="1.2" fill="currentColor"/><circle cx="16.5" cy="11.5" r="1.2" fill="currentColor"/></svg>',
  close: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>',
  search: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>',
  import: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14"/></svg>',
  blank: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="3 3"><rect x="5" y="3" width="14" height="18" rx="2"/></svg>',
  ext: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 4h6v6M20 4l-9 9M19 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h5"/></svg>',
};

const DEFAULT_I18N = {
  title: 'Temi Olobuild',
  subtitle: 'Scegli un tema: configura colori, font e sezioni del sito in un click.',
  searchPlaceholder: 'Cerca un tema…',
  categoriesLabel: 'Categorie',
  allLabel: 'Tutti i temi',
  importLabel: 'Importa tema',
  detailsLabel: 'Dettagli',
  loading: 'Caricamento temi…',
  emptyFiltered: 'Nessun tema per questi filtri.',
  emptyNone: 'Nessun tema disponibile.',
  loadError: 'Non è stato possibile caricare i temi.',
  closeLabel: 'Chiudi',
  themeWord: 'tema',
  themesWord: 'temi',
  pageWord: 'pagina',
  pagesWord: 'pagine',
};

function esc(s) {
  return String(s == null ? '' : s).replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
}

/* Carica i Google Font display dei temi (una sola volta per documento). */
function injectFonts(themes) {
  const fams = new Set();
  themes.forEach(t => (t.google_fonts || []).forEach(f => f && fams.add(f)));
  if (!fams.size) return;
  // niente asse weight: alcuni font display hanno un solo peso → evita 400 di css2
  const href = 'https://fonts.googleapis.com/css2?'
    + [...fams].map(f => 'family=' + encodeURIComponent(f).replace(/%20/g, '+')).join('&')
    + '&display=swap';
  const id = 'olo-theme-picker-fonts';
  let link = document.getElementById(id);
  if (link) { if (link.getAttribute('href') !== href) link.setAttribute('href', href); return; }
  link = document.createElement('link');
  link.id = id; link.rel = 'stylesheet'; link.href = href;
  document.head.appendChild(link);
}

function injectStyle() {
  const id = 'olo-theme-picker-style';
  if (document.getElementById(id)) return;
  const style = document.createElement('style');
  style.id = id;
  style.textContent = CSS;
  document.head.appendChild(style);
}

export function createThemePicker(opts = {}) {
  const mode = opts.mode === 'embed' ? 'embed' : 'modal';
  const i18n = Object.assign({}, DEFAULT_I18N, opts.i18n || {});
  // 'browse' = solo da sfogliare (importazione disattivata sul sito, come nella demo):
  // card senza «Importa tema», non selezionabili.
  const cardAction = opts.card && opts.card.action;
  const action = cardAction === 'select' || cardAction === 'browse' ? cardAction : 'import';
  const blank = opts.blank || null;
  // Colori: chiari come «Blocchi & Pagine»; tema:'scuro' per una pagina scura (la procedura di configurazione)
  const scuro = opts.tema === 'scuro';

  let themes = Array.isArray(opts.themes) ? opts.themes.slice() : [];
  // Il server può rispondere con un errore ({ code, message }) invece dell'elenco: prima
  // populate() si fermava su forEach e il modale restava su «Caricamento temi…» per sempre.
  let loadError = '';
  let activeCat = 'Tutti';
  let query = '';
  let CATS = ['Tutti'];
  let catBtns = {};
  let selectedId = null;
  let rootEl = null;     // .otmp (sidebar+main) — anche figlio dell'overlay in modal
  let overlayEl = null;  // modal only
  let onKey = null;

  injectStyle();

  // ── markup ──
  function bodyHTML() {
    return `
      <aside class="otmp-side">
        <label class="otmp-search">${ICON.search}<input type="text" placeholder="${esc(i18n.searchPlaceholder)}" data-search aria-label="${esc(i18n.searchPlaceholder)}"/></label>
        <div>
          <div class="otmp-side-lab">${esc(i18n.categoriesLabel)}</div>
          <div class="otmp-cats" data-cats></div>
        </div>
      </aside>
      <div class="otmp-main">
        <div class="otmp-main-top"><h2 data-title>${esc(i18n.allLabel)}</h2><span class="otmp-count" data-count></span></div>
        <div class="otmp-grid" data-grid><div class="otmp-loading">${esc(i18n.loading)}</div></div>
      </div>`;
  }

  function build() {
    if (mode === 'modal') {
      overlayEl = document.createElement('div');
      overlayEl.className = 'otmp-overlay' + (scuro ? ' otmp--scuro' : '');
      overlayEl.innerHTML = `
        <div class="otmp-backdrop" data-overlay></div>
        <div class="otmp-modal" role="dialog" aria-modal="true" aria-label="${esc(i18n.title)}">
          <div class="otmp-hd">
            <span class="otmp-hd-mark">${ICON.brand}</span>
            <div class="otmp-hd-t">
              <h1>${esc(i18n.title)}</h1>
              <p>${esc(i18n.subtitle)}</p>
            </div>
            <button class="otmp-hd-x" data-close aria-label="${esc(i18n.closeLabel)}">${ICON.close}</button>
          </div>
          <div class="otmp otmp--modal">${bodyHTML()}</div>
          <div class="otmp-busy" data-busy hidden><div class="otmp-spin"></div><span>${esc(opts.busyLabel || 'Importazione in corso…')}</span></div>
        </div>`;
      rootEl = overlayEl.querySelector('.otmp');
    } else {
      rootEl = document.createElement('div');
      rootEl.className = 'otmp otmp--embed' + (scuro ? ' otmp--scuro' : '');
      rootEl.innerHTML = bodyHTML();
    }
  }

  function q(sel) { return rootEl ? rootEl.querySelector(sel) : null; }
  function host() { return mode === 'modal' ? overlayEl : rootEl; }

  function headline(name) {
    const parts = String(name || '').trim().split(/\s+/).filter(Boolean);
    if (parts.length > 1) { const last = esc(parts.pop()); return `${esc(parts.join(' '))} <em>${last}</em>`; }
    return `${esc(name || '')}<em>.</em>`;
  }

  // Anteprima sintetica dai token (barra + headline + riga + swatches). Usata sia per i
  // temi senza screenshot, sia come fallback se lo screenshot remoto non carica.
  function synthPreviewInner(t) {
    const pal = (t.pal || []).slice(0, 4).map(c => `<i style="background:${esc(c)}"></i>`).join('');
    return `<div class="otmp-pv-bar"><span class="otmp-pv-logo"></span><span class="otmp-pv-nav"><i></i><i></i><i></i></span></div>
        <div class="otmp-pv-h">${headline(t.name)}</div>
        <div class="otmp-pv-row"><span class="otmp-pv-btn"></span><span class="otmp-pv-line"></span></div>
        <span class="otmp-pv-sw">${pal}</span>`;
  }

  function previewHTML(t) {
    const zone = t.zone ? `<span class="otmp-pv-zone"><span class="d"></span>${esc(t.zone)}</span>` : '';
    if (t.screenshot) {
      // miniatura reale del tema (screenshot remoto da olotheme.com). Se l'immagine non
      // carica (offline / non ancora pubblicata sul server) l'onerror ripiega sull'anteprima
      // sintetica dai token (HTML conservato in data-synth), così non si vede mai un'immagine rotta.
      const synth = synthPreviewInner(t).replace(/&/g, '&amp;').replace(/"/g, '&quot;');
      return `<div class="otmp-pv otmp-pv-shot${t.light ? ' light' : ''}" data-synth="${synth}">
          <img class="otmp-shot" src="${esc(t.screenshot)}" alt="${esc(t.name || '')} — anteprima" loading="lazy" decoding="async" onerror="var p=this.parentNode;p.classList.remove('otmp-pv-shot');p.innerHTML=p.getAttribute('data-synth')||'';"/>
          ${zone}
        </div>`;
    }
    // anteprima sintetica dai token (tema senza screenshot)
    return `<div class="otmp-pv${t.light ? ' light' : ''}">${synthPreviewInner(t)}${zone}</div>`;
  }

  function cardHTML(t) {
    if (t._blank) {
      return `<article class="otmp-card is-blank is-select${selectedId === t.id ? ' is-selected' : ''}" role="button" tabindex="0" data-pick="${esc(t.id)}">
          <div class="otmp-pv otmp-blankpv">${ICON.blank}</div>
          <div class="otmp-cb">
            <div class="otmp-cb-name">${esc(t.name || '')}</div>
            <p class="otmp-cb-desc">${esc(t.desc || t.description || '')}</p>
          </div>
        </article>`;
    }
    const tags = (t.tags || []).slice(0, 4).map(x => `<span>${esc(x)}</span>`).join('');
    const style = `--c-bg:${esc(t.bg || '#0e1626')};--c-ink:${esc(t.ink || '#f3f5fb')};`
      + `--c-acc:${esc(t.accent || '#e8622a')};--c-font:${esc(t.font || 'inherit')}`;
    const selectMode = action === 'select';
    const link = t.url
      ? `<a class="otmp-cb-link" href="${esc(t.url)}" target="_blank" rel="noopener" data-stop>${esc(i18n.detailsLabel)}${ICON.ext}</a>`
      : '';
    const n = parseInt(t.pages, 10) || 0;
    const ver = n
      ? `<span class="otmp-cb-ver">${n} ${esc(n === 1 ? i18n.pageWord : i18n.pagesWord)}</span>`
      : `<span class="otmp-cb-ver">v${esc(t.version || '1.0')}</span>`;
    const foot = selectMode || action === 'browse'
      ? `<div class="otmp-cb-foot">${ver}${link ? `<span class="otmp-cb-spacer"></span>${link}` : ''}</div>`
      : `<div class="otmp-cb-foot">
           ${ver}${link}
           <button class="otmp-cb-btn" data-import="${esc(t.id)}">${ICON.import}${esc(i18n.importLabel)}</button>
         </div>`;
    return `<article class="otmp-card${selectMode ? ' is-select' : ''}${selectMode && selectedId === t.id ? ' is-selected' : ''}" style="${style}"${selectMode ? ` role="button" tabindex="0" data-pick="${esc(t.id)}"` : ''}>
        ${previewHTML(t)}
        <div class="otmp-cb">
          ${t.category ? `<div class="otmp-cb-cat">${esc(t.category)}</div>` : ''}
          <div class="otmp-cb-name">${esc(t.name || '')}</div>
          <p class="otmp-cb-desc">${esc(t.description || '')}</p>
          ${tags ? `<div class="otmp-cb-tags">${tags}</div>` : ''}
          ${foot}
        </div>
      </article>`;
  }

  function selectCard(id, theme) {
    selectedId = id;
    const grid = q('[data-grid]');
    if (grid) grid.querySelectorAll('.otmp-card.is-select').forEach(c => c.classList.toggle('is-selected', c.getAttribute('data-pick') === id));
    if (typeof opts.onSelect === 'function') opts.onSelect(id, theme || null);
  }

  function renderGrid() {
    const grid = q('[data-grid]');
    if (!grid) return;
    const bySearch = themes.filter(t => !query
      || (`${t.name} ${t.category} ${(t.tags || []).join(' ')} ${t.description}`).toLowerCase().includes(query));
    CATS.forEach(c => {
      const n = c === 'Tutti' ? bySearch.length : bySearch.filter(t => t.category === c).length;
      const b = catBtns[c];
      if (b) b.querySelector('.n').textContent = n;
    });
    const list = bySearch.filter(t => activeCat === 'Tutti' || t.category === activeCat);
    // card "Vuoto" (solo senza filtri attivi)
    const showBlank = blank && action === 'select' && activeCat === 'Tutti' && !query;
    const cards = list.map(cardHTML);
    if (showBlank) cards.push(cardHTML(Object.assign({ _blank: true }, blank)));
    const emptyTxt = themes.length ? i18n.emptyFiltered : (loadError || i18n.emptyNone);
    grid.innerHTML = cards.join('') + `<div class="otmp-empty${(list.length || showBlank) ? '' : ' show'}">${esc(emptyTxt)}</div>`;

    grid.querySelectorAll('[data-import]').forEach(btn => {
      btn.onclick = () => {
        const t = themes.find(x => String(x.id) === btn.getAttribute('data-import'));
        if (typeof opts.onImport === 'function') opts.onImport(t || { id: btn.getAttribute('data-import') });
      };
    });
    grid.querySelectorAll('[data-pick]').forEach(card => {
      const pick = () => {
        const id = card.getAttribute('data-pick');
        selectCard(id, themes.find(x => String(x.id) === id) || (blank && id === blank.id ? Object.assign({ _blank: true }, blank) : null));
      };
      card.onclick = pick;
      card.onkeydown = e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); pick(); } };
    });
    // il link "Dettagli" non deve selezionare/attivare la card
    grid.querySelectorAll('[data-stop]').forEach(a => a.addEventListener('click', e => e.stopPropagation()));

    const titleEl = q('[data-title]'); if (titleEl) titleEl.textContent = activeCat === 'Tutti' ? i18n.allLabel : activeCat;
    const countEl = q('[data-count]'); if (countEl) countEl.textContent = `${list.length} ${list.length === 1 ? i18n.themeWord : i18n.themesWord}`;
  }

  function populate() {
    const seen = [];
    themes.forEach(t => { if (t.category && !seen.includes(t.category)) seen.push(t.category); });
    seen.sort((a, b) => a.localeCompare(b, 'it'));
    CATS = ['Tutti', ...seen];
    if (activeCat !== 'Tutti' && !CATS.includes(activeCat)) activeCat = 'Tutti';

    const catsEl = q('[data-cats]');
    catBtns = {};
    if (catsEl) {
      catsEl.innerHTML = '';
      CATS.forEach(c => {
        const b = document.createElement('button');
        b.className = 'otmp-catbtn' + (c === activeCat ? ' on' : '');
        b.innerHTML = `<span>${c === 'Tutti' ? esc(i18n.allLabel) : esc(c)}</span><span class="n"></span>`;
        b.onclick = () => {
          activeCat = c;
          catsEl.querySelectorAll('.otmp-catbtn').forEach(x => x.classList.remove('on'));
          b.classList.add('on');
          renderGrid();
        };
        catsEl.appendChild(b);
        catBtns[c] = b;
      });
    }
    if (opts.fonts !== false) injectFonts(themes);
    renderGrid();
  }

  function wire() {
    const search = q('[data-search]');
    if (search) search.addEventListener('input', e => { query = e.target.value.trim().toLowerCase(); renderGrid(); });
    if (mode === 'modal') {
      overlayEl.querySelector('[data-overlay]').onclick = close;
      overlayEl.querySelector('[data-close]').onclick = close;
      const focusSel = 'a[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),textarea:not([disabled]),[tabindex]:not([tabindex="-1"])';
      onKey = e => {
        if (e.key === 'Escape') { close(); return; }
        if (e.key !== 'Tab') return;
        const f = overlayEl.querySelectorAll(focusSel);
        if (!f.length) return;
        const first = f[0], last = f[f.length - 1];
        if (e.shiftKey) { if (document.activeElement === first) { e.preventDefault(); last.focus(); } }
        else if (document.activeElement === last) { e.preventDefault(); first.focus(); }
      };
      document.addEventListener('keydown', onKey);
    }
  }

  async function loadIfNeeded() {
    if (!themes.length && typeof opts.loadThemes === 'function') {
      loadError = '';
      try {
        const r = await opts.loadThemes();
        themes = Array.isArray(r) ? r : [];
        if (!Array.isArray(r)) loadError = (r && typeof r.message === 'string' && r.message) || i18n.loadError;
      }
      catch (e) { console.error('themePicker loadThemes error:', e); themes = []; loadError = i18n.loadError; }
    }
    populate();
  }

  function setBusy(on) {
    if (mode !== 'modal') return;
    const busy = overlayEl && overlayEl.querySelector('[data-busy]');
    if (busy) busy.hidden = !on;
  }

  function open() {
    if (mode !== 'modal') return api;
    if (overlayEl && overlayEl.parentNode) return api;
    build();
    document.body.appendChild(overlayEl);
    wire();
    const s = q('[data-search]'); if (s) s.focus();
    loadIfNeeded();
    return api;
  }

  function close() {
    if (mode === 'modal') {
      if (onKey) { document.removeEventListener('keydown', onKey); onKey = null; }
      if (overlayEl && overlayEl.parentNode) overlayEl.parentNode.removeChild(overlayEl);
      overlayEl = null; rootEl = null;
    } else {
      destroy();
    }
    if (typeof opts.onClose === 'function') opts.onClose();
  }

  function mountEmbed() {
    build();
    const target = typeof opts.target === 'string' ? document.querySelector(opts.target) : opts.target;
    if (target) { target.innerHTML = ''; target.appendChild(rootEl); }
    wire();
    loadIfNeeded();
    return api;
  }

  function destroy() {
    if (onKey) { document.removeEventListener('keydown', onKey); onKey = null; }
    if (rootEl && rootEl.parentNode) rootEl.parentNode.removeChild(rootEl);
    if (overlayEl && overlayEl.parentNode) overlayEl.parentNode.removeChild(overlayEl);
    rootEl = null; overlayEl = null;
  }

  const api = {
    open, close, destroy, setBusy,
    get el() { return host(); },
    getSelected() { return selectedId; },
    setThemes(arr) { themes = Array.isArray(arr) ? arr.slice() : []; populate(); },
  };

  if (mode === 'embed') mountEmbed();
  return api;
}

const CSS = `
/* Colori: tutti in variabili sulla radice (l'overlay del modale o il riquadro embed).
   Predefinito CHIARO, come «Blocchi & Pagine» (TemplateLibrary.vue). La serie scura resta
   come variante (.otmp--scuro, opzione tema:'scuro'): la usa la procedura di configurazione,
   che è una pagina scura, e la userà il tema scuro del builder. */
.otmp-overlay, .otmp--embed{
  --modal:rgba(255,255,255,.97);--side:rgba(0,0,0,.025);--area:rgba(0,0,0,.015);--panel:#fff;--field:#fff;
  --fill:rgba(0,0,0,.04);--line:rgba(0,0,0,.08);--line-2:rgba(0,0,0,.12);
  --txt:#1a1a1a;--txt-2:#555;--mute:#777;--faint:#a3a3a3;
  --accent:#e8622a;--accent-d:#d4541f;--accent-soft:rgba(232,98,42,.1);--ring:rgba(232,98,42,.18);--on-accent:#fff;
  --backdrop:rgba(17,17,17,.38);--shadow:0 24px 80px rgba(0,0,0,.18),0 0 0 1px rgba(0,0,0,.04);
  --card-line-h:var(--accent);--card-shadow:0 1px 2px rgba(0,0,0,.04);--card-shadow-h:0 14px 30px -14px rgba(0,0,0,.28);
  --mark:rgba(232,98,42,.1);--mark-line:transparent;--busy:rgba(255,255,255,.82);--scroll:rgba(0,0,0,.16);--img:#f3f4f6;
  --cat-dot:inline-block;}
.otmp-overlay.otmp--scuro, .otmp--embed.otmp--scuro{
  --modal:#0e1626;--side:#0b1320;--area:transparent;--panel:#121d31;--field:#0e1626;
  --fill:#121d31;--line:rgba(255,255,255,.08);--line-2:rgba(255,255,255,.14);
  --txt:#e7ecf4;--txt-2:#9aa7bd;--mute:#9aa7bd;--faint:#64718a;
  --accent-soft:rgba(232,98,42,.14);
  --backdrop:rgba(6,9,15,.66);--shadow:0 40px 120px -30px rgba(0,0,0,.7),0 0 0 1px rgba(0,0,0,.4);
  --card-line-h:var(--line-2);--card-shadow:none;--card-shadow-h:0 22px 48px -28px rgba(0,0,0,.8);
  --mark:linear-gradient(140deg,#1b2942,#101a2e);--mark-line:var(--line-2);--busy:rgba(8,12,20,.74);--scroll:rgba(255,255,255,.14);--img:#0b1320;
  --cat-dot:none;}
.otmp, .otmp-overlay{font-family:"Inter",-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;line-height:1.5;-webkit-font-smoothing:antialiased;}
.otmp *,.otmp-overlay *{box-sizing:border-box;margin:0;padding:0;}

.otmp-overlay{position:fixed;inset:0;z-index:999999;display:flex;align-items:center;justify-content:center;padding:30px 18px;}
.otmp-backdrop{position:absolute;inset:0;background:var(--backdrop);-webkit-backdrop-filter:blur(2px);backdrop-filter:blur(2px);}
.otmp-modal{position:relative;z-index:1;width:100%;max-width:1180px;max-height:92vh;background:var(--modal);color:var(--txt);
  -webkit-backdrop-filter:blur(24px) saturate(180%);backdrop-filter:blur(24px) saturate(180%);
  border:1px solid var(--line-2);border-radius:14px;overflow:hidden;display:flex;flex-direction:column;box-shadow:var(--shadow);}
.otmp-hd{display:flex;align-items:center;gap:14px;padding:16px 22px;border-bottom:1px solid var(--line);flex:none;}
.otmp-hd-mark{width:38px;height:38px;border-radius:10px;flex:none;display:grid;place-items:center;background:var(--mark);border:1px solid var(--mark-line);}
.otmp-hd-mark svg{width:20px;height:20px;color:var(--accent);}
.otmp-hd-t h1{font-size:16px;font-weight:700;letter-spacing:-.01em;line-height:1.2;color:var(--txt);}
.otmp-hd-t p{font-size:12.5px;color:var(--mute);margin-top:2px;}
.otmp-hd-x{margin-left:auto;width:34px;height:34px;border-radius:8px;border:0;background:transparent;
  color:var(--mute);cursor:pointer;display:grid;place-items:center;transition:background .15s,color .15s;flex:none;}
.otmp-hd-x:hover{background:var(--fill);color:var(--txt);}
.otmp-hd-x:focus-visible{outline:2px solid var(--accent);outline-offset:1px;}
.otmp-hd-x svg{width:17px;height:17px;}

.otmp{display:flex;align-items:stretch;min-width:0;color:var(--txt);}
.otmp--modal{flex:1;min-height:0;}
.otmp--embed{border:1px solid var(--line-2);border-radius:14px;overflow:hidden;background:var(--modal);}
.otmp-side{width:216px;flex:none;border-right:1px solid var(--line);background:var(--side);
  padding:16px 14px;display:flex;flex-direction:column;gap:16px;overflow-y:auto;}
.otmp-side::-webkit-scrollbar{width:8px;}
.otmp-side::-webkit-scrollbar-thumb{background:var(--scroll);border-radius:5px;}
.otmp-search{display:flex;align-items:center;gap:9px;background:var(--field);border:1px solid var(--line-2);border-radius:9px;padding:0 11px;height:36px;
  cursor:text;transition:border-color .15s,box-shadow .15s;}
.otmp-search:focus-within{border-color:var(--accent);box-shadow:0 0 0 3px var(--ring);}
.otmp-search svg{width:15px;height:15px;color:var(--faint);flex:none;}
/* Il riquadro è la label, il campo è solo testo: forms.css di WordPress (fuori dal builder) gli
   darebbe bordo e anello blu al focus con input[type=text]:focus (0,2,1). */
.otmp .otmp-search input[type="text"],.otmp .otmp-search input[type="text"]:focus{background:transparent;border:0;border-radius:0;outline:none;box-shadow:none;
  color:var(--txt);font-size:13px;font-family:inherit;width:100%;height:auto;min-height:0;margin:0;padding:0;}
.otmp-search input::placeholder{color:var(--faint);opacity:1;}
.otmp-side-lab{font-size:10px;font-weight:700;letter-spacing:.11em;text-transform:uppercase;color:var(--faint);padding:0 10px 7px;}
.otmp-cats{display:flex;flex-direction:column;gap:2px;}
.otmp-catbtn{position:relative;display:flex;align-items:center;gap:10px;width:100%;text-align:left;background:transparent;
  border:0;border-radius:8px;padding:8px 10px;cursor:pointer;color:var(--txt-2);font-family:inherit;font-size:13px;font-weight:500;transition:background .14s,color .14s;}
.otmp-catbtn:hover{background:var(--fill);color:var(--txt);}
.otmp-catbtn:focus-visible{outline:2px solid var(--accent);outline-offset:1px;}
.otmp-catbtn.on{background:var(--accent-soft);color:var(--txt);font-weight:600;box-shadow:inset 3px 0 0 var(--accent);}
.otmp-catbtn span:first-child{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
.otmp-catbtn .n{margin-left:auto;flex:none;font-size:11px;color:var(--mute);font-variant-numeric:tabular-nums;
  background:var(--panel);border:1px solid var(--line);border-radius:999px;min-width:22px;text-align:center;padding:1px 6px;}
.otmp-catbtn.on .n{color:var(--on-accent);background:var(--accent);border-color:var(--accent);}

.otmp-main{flex:1;min-width:0;display:flex;flex-direction:column;background:var(--area);}
.otmp-main-top{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:13px 22px;border-bottom:1px solid var(--line);flex:none;}
.otmp-main-top h2{font-size:14px;font-weight:600;color:var(--txt);}
.otmp-count{font-size:12px;color:var(--mute);white-space:nowrap;font-variant-numeric:tabular-nums;}
.otmp-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(244px,1fr));grid-auto-rows:max-content;
  gap:18px;padding:22px;overflow-y:auto;overflow-x:hidden;max-height:64vh;}
.otmp--embed .otmp-grid{max-height:50vh;}
.otmp-grid::-webkit-scrollbar{width:9px;}
.otmp-grid::-webkit-scrollbar-thumb{background:var(--scroll);border-radius:5px;}
.otmp-loading{grid-column:1/-1;text-align:center;color:var(--mute);font-size:14px;padding:50px 0;}

.otmp-card{display:flex;flex-direction:column;background:var(--panel);border:1px solid var(--line-2);border-radius:12px;overflow:hidden;box-shadow:var(--card-shadow);
  transition:transform .18s cubic-bezier(.2,.7,.3,1),border-color .18s,box-shadow .18s;}
.otmp-card:hover{transform:translateY(-3px);border-color:var(--card-line-h);box-shadow:var(--card-shadow-h);}
.otmp-card.is-select{cursor:pointer;}
.otmp-card.is-select:focus-visible{outline:2px solid var(--accent);outline-offset:2px;}
.otmp-card.is-selected{border-color:var(--accent);box-shadow:0 0 0 1px var(--accent);}

.otmp-pv{position:relative;height:172px;flex:none;overflow:hidden;border-bottom:1px solid var(--line);
  background:var(--c-bg);display:flex;flex-direction:column;padding:13px 14px 0;}
.otmp-pv-bar{display:flex;align-items:center;gap:6px;margin-bottom:auto;}
.otmp-pv-logo{width:13px;height:13px;border-radius:4px;background:var(--c-acc);}
.otmp-pv-nav{display:flex;gap:6px;margin-left:4px;}
.otmp-pv-nav i{width:18px;height:4px;border-radius:2px;background:var(--c-ink);opacity:.32;}
.otmp-pv-nav i:first-child{opacity:.6;background:var(--c-acc);}
.otmp-pv-h{font-family:var(--c-font);font-size:23px;line-height:.98;color:var(--c-ink);letter-spacing:-.01em;max-width:84%;}
.otmp-pv-h em{font-style:normal;color:var(--c-acc);}
.otmp-pv-row{display:flex;align-items:center;gap:8px;margin:9px 0 11px;}
.otmp-pv-btn{height:15px;border-radius:8px;background:var(--c-acc);width:54px;}
.otmp-pv-line{flex:1;height:5px;border-radius:3px;background:var(--c-ink);opacity:.16;}
.otmp-pv-sw{position:absolute;right:12px;bottom:11px;display:flex;gap:5px;}
.otmp-pv-sw i{width:14px;height:14px;border-radius:50%;box-shadow:inset 0 0 0 1px rgba(0,0,0,.12);}
.otmp-pv-zone{position:absolute;left:12px;bottom:11px;display:inline-flex;align-items:center;gap:6px;
  background:rgba(8,10,16,.52);-webkit-backdrop-filter:blur(5px);backdrop-filter:blur(5px);
  border:1px solid rgba(255,255,255,.18);border-radius:999px;padding:4px 9px 4px 8px;
  font-size:9.5px;font-weight:700;letter-spacing:.05em;text-transform:uppercase;color:#fff;}
.otmp-pv-zone .d{width:6px;height:6px;border-radius:50%;background:var(--c-acc);}
.otmp-pv.light .otmp-pv-zone{background:rgba(255,255,255,.62);border-color:rgba(0,0,0,.1);color:#1a1a1a;}
.otmp-blankpv{align-items:center;justify-content:center;background:var(--side);color:var(--faint);}
.otmp-card.is-selected .otmp-blankpv,.otmp-card:hover .otmp-blankpv{color:var(--accent);}
.otmp-blankpv svg{width:34px;height:34px;}
.otmp-pv-shot{padding:0;background:var(--img);}
.otmp-shot{width:100%;height:100%;object-fit:cover;object-position:top center;display:block;transition:object-position .6s ease;}
.otmp-card:hover .otmp-shot,.otmp-card:focus-within .otmp-shot{object-position:bottom center;transition:object-position 9s linear;}

.otmp-cb{padding:15px 16px 16px;display:flex;flex-direction:column;flex:1;}
/* La categoria: sul chiaro testo neutro col pallino del colore del tema (certi accenti, lime e
   oro, sul bianco non si leggerebbero); sullo scuro il colore del tema. */
.otmp-cb-cat{display:flex;align-items:center;gap:7px;font-size:10.5px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--mute);margin-bottom:6px;}
.otmp--scuro .otmp-cb-cat{color:var(--c-acc);}
.otmp-cb-cat::before{content:"";display:var(--cat-dot);width:8px;height:8px;border-radius:50%;flex:none;background:var(--c-acc);box-shadow:inset 0 0 0 1px rgba(0,0,0,.12);}
.otmp-cb-name{font-size:17px;font-weight:700;letter-spacing:-.01em;color:var(--txt);}
.otmp-cb-desc{font-size:12.8px;line-height:1.55;color:var(--txt-2);margin:8px 0 13px;
  display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;}
.otmp-cb-tags{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:16px;}
.otmp-cb-tags span{font-size:11px;font-weight:500;color:var(--txt-2);background:var(--fill);border:1px solid var(--line);border-radius:6px;padding:3px 9px;}
.otmp-cb-foot{display:flex;align-items:center;gap:12px;margin-top:auto;}
.otmp-cb-ver{font-size:11.5px;color:var(--mute);font-variant-numeric:tabular-nums;}
.otmp-cb-spacer{flex:1;}
.otmp-cb-link{display:inline-flex;align-items:center;gap:5px;font-size:11.5px;font-weight:500;color:var(--txt-2);text-decoration:none;transition:color .15s;}
.otmp-cb-link:hover{color:var(--txt);}
.otmp-cb-link:focus-visible{outline:2px solid var(--accent);outline-offset:2px;border-radius:4px;}
.otmp-cb-link svg{width:12px;height:12px;}
.otmp-cb-btn{margin-left:auto;display:inline-flex;align-items:center;gap:7px;background:var(--accent);color:var(--on-accent);border:0;border-radius:9px;
  padding:9px 15px;font-family:inherit;font-size:13px;font-weight:600;cursor:pointer;transition:background .15s,transform .12s;}
.otmp-cb-btn:hover{background:var(--accent-d);transform:translateY(-1px);}
.otmp-cb-btn:focus-visible{outline:2px solid var(--accent);outline-offset:2px;}
.otmp-cb-btn svg{width:14px;height:14px;}

.otmp-empty{grid-column:1/-1;text-align:center;color:var(--mute);font-size:14px;padding:50px 0;display:none;}
.otmp-empty.show{display:block;}

.otmp-busy{position:absolute;inset:0;z-index:5;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px;
  background:var(--busy);-webkit-backdrop-filter:blur(3px);backdrop-filter:blur(3px);color:var(--txt);font-size:14px;font-weight:600;}
.otmp-busy[hidden]{display:none;}
.otmp-spin{width:34px;height:34px;border:3px solid var(--line-2);border-top-color:var(--accent);border-radius:50%;animation:otmp-spin .8s linear infinite;}
@keyframes otmp-spin{to{transform:rotate(360deg);}}

@media(max-width:760px){
  .otmp-overlay{padding:0;}
  .otmp-modal{max-width:100%;max-height:100vh;border-radius:0;border:0;}
  .otmp{flex-direction:column;}
  .otmp-side{width:auto;border-right:0;border-bottom:1px solid var(--line);}
  .otmp-cats{flex-direction:row;flex-wrap:wrap;}
  .otmp-catbtn{width:auto;}
  .otmp-catbtn.on{box-shadow:none;}
  .otmp-grid,.otmp--embed .otmp-grid{max-height:none;}
}
@media(prefers-reduced-motion:reduce){
  .otmp-card,.otmp-cb-btn,.otmp-shot{transition:none;}
  .otmp-card:hover .otmp-shot,.otmp-card:focus-within .otmp-shot{object-position:top center;}
  .otmp-spin{animation-duration:1.6s;}
}`;
