/**
 * Token colore — lettura condivisa fra i campi dell'inspector.
 *
 * I token `var(--olo-color-*)` sono definiti su `.olo-template`, cioè DENTRO il
 * canvas: nel pannello di destra non risolvono. Ogni campo che deve mostrare
 * un'anteprima del colore (la pastiglia di FieldColor, il pallino della sintesi
 * tipografica) deve quindi risolverli in JS, leggendo la palette dallo store.
 *
 * Qui sta quella lettura, una volta sola: prima viveva dentro FieldColor.vue e
 * non era riusabile da nessun altro.
 */

// Ruoli del tema selezionabili come token, oltre ai globali custom.
export const ROLE_SWATCHES = [
  { id: 'primary', label: 'Primary' }, { id: 'secondary', label: 'Secondary' },
  { id: 'success', label: 'Success' }, { id: 'warning', label: 'Warning' },
  { id: 'danger', label: 'Danger' }, { id: 'link', label: 'Link' },
  { id: 'text', label: 'Testo' }, { id: 'background', label: 'Sfondo' },
  { id: 'muted', label: 'Superficie' }, { id: 'border', label: 'Bordo' },
];

/**
 * Scompone un token colore nelle sue due parti: l'id e l'eventuale RISERVA.
 *   var(--olo-color-dark)            -> { id: 'dark', fallback: '' }
 *   var(--olo-color-dark, #16263d)   -> { id: 'dark', fallback: '#16263d' }
 * Restituisce null se non e' un token.
 *
 * La riserva si prende con `(.+)` e non con `[^)]+`: dev'essere in grado di
 * attraversare una parentesi, perche' una riserva puo' essere a sua volta un
 * token — `var(--olo-color-accent, var(--olo-color-primary))`.
 */
export function tokenParts(val) {
  const m = /^var\(\s*--olo-color-([a-z0-9_-]+)\s*(?:,\s*(.+))?\)$/i.exec(String(val || '').trim());
  if (!m) return null;
  return { id: m[1].toLowerCase(), fallback: (m[2] || '').trim() };
}

/**
 * Swatch disponibili: ruoli del tema (olo_styles.colors) + globali custom.
 */
export function buildSwatchColors(stylesStore) {
  const c = stylesStore?.colors || {};
  const roleIds = new Set(ROLE_SWATCHES.map(r => r.id));
  const roles = ROLE_SWATCHES
    .filter(r => c[r.id])
    .map(r => ({ id: r.id, label: r.label, value: c[r.id], quick: false }));
  const globals = (stylesStore?.globalColors || [])
    .filter(g => g && g.id && !roleIds.has(g.id))
    .map(g => ({ id: g.id, label: g.label || g.id, value: g.value, quick: !!g.quick }));
  return [...roles, ...globals];
}

/**
 * Nome leggibile di un token: l'etichetta dello swatch se il colore è in
 * palette, altrimenti l'id ripulito — «text-faint» vale «Text faint», che si
 * legge, mentre `var(--olo-color-text-fai…` troncato non vuol dire niente.
 */
export function tokenLabel(val, stylesStore) {
  const tk = tokenParts(val);
  if (!tk) return '';
  const hit = buildSwatchColors(stylesStore).find(s => s.id === tk.id);
  if (hit) return hit.label;
  const pulito = tk.id.replace(/[-_]+/g, ' ').trim();
  return pulito.charAt(0).toUpperCase() + pulito.slice(1);
}

/**
 * I token colore del template aperto: gli STESSI che BuilderCanvas inietta nel
 * canvas (`stylesStore.cssVariables`, primo blocco `.olo-template`). Le swatch
 * della palette ne sono solo una parte: `text-muted`, `on-primary`, `surface`
 * e gli altri alias esistono in pagina ma non fra le swatch, e risolti da lì
 * restavano senza colore. Stessa lettura di BackgroundControls.
 */
let tokenCacheCss = null;
let tokenCacheMap = {};
export function templateColorTokens(stylesStore) {
  const css = stylesStore?.cssVariables || '';
  if (css === tokenCacheCss) return tokenCacheMap;
  const map = {};
  const open = css.indexOf('.olo-template {');
  if (open >= 0) {
    const end = css.indexOf('}', open);
    const block = css.slice(open, end < 0 ? undefined : end);
    // Come in CSS, la dichiarazione successiva vince sulla precedente.
    const re = /--olo-color-([\w-]+)\s*:\s*([^;]+);/g;
    let m;
    while ((m = re.exec(block)) !== null) map[m[1].toLowerCase()] = m[2].trim();
  }
  tokenCacheCss = css;
  tokenCacheMap = map;
  return map;
}

/**
 * Valore colore → stringa CSS dipingibile nell'inspector.
 * Un token si risolve come nel canvas; se lì non esiste vale la sua riserva, e
 * se non ne ha torna '' — meglio nessun pallino che un pallino nero (il nero era
 * il vecchio ripiego di FieldColor, e mostrava centinaia di pastiglie sbagliate).
 */
export function resolveColorToken(val, stylesStore, depth = 0) {
  const v = String(val || '').trim();
  if (!v) return '';
  const t = tokenParts(v);
  if (!t) return v;
  if (depth > 8) return ''; // token che rimandano l'uno all'altro in cerchio
  const def = templateColorTokens(stylesStore)[t.id]
    || buildSwatchColors(stylesStore).find(s => s.id === t.id)?.value;
  if (def) {
    const r = resolveColorToken(def, stylesStore, depth + 1);
    if (r) return r;
  }
  if (t.fallback) return resolveColorToken(t.fallback, stylesStore, depth + 1);
  return '';
}

// ─── Cosa dipinge un valore colore ───────────────────────────────────────────

// Parole chiave che non sono un colore: lo decide il contesto (il testo, il
// genitore, il foglio di stile), e nel pannello quel contesto non c'è.
const PAROLE_DI_CONTESTO = new Set(['currentcolor', 'inherit', 'initial', 'unset', 'revert', 'revert-layer']);

let canvasCtx;
function contestoCanvas() {
  if (canvasCtx !== undefined) return canvasCtx;
  try { canvasCtx = document.createElement('canvas').getContext('2d'); } catch (e) { canvasCtx = null; }
  return canvasCtx;
}

/**
 * Normalizza con il parser del browser ciò che le regex non leggono (nomi come
 * `white`, `hsl()`, sintassi a spazi, `color-mix()` di colori concreti). Il
 * canvas ignora un valore non valido: due sentinelle diverse lo smascherano.
 * ⚠️ `currentColor` qui vale NERO: le parole di contesto vanno escluse prima.
 */
function normalizzaViaCanvas(s) {
  const ctx = contestoCanvas();
  if (!ctx) return '';
  ctx.fillStyle = '#000000'; ctx.fillStyle = s; const a = ctx.fillStyle;
  ctx.fillStyle = '#ffffff'; ctx.fillStyle = s; const b = ctx.fillStyle;
  return a === b ? String(a) : '';
}

const alfa01 = x => (Number.isFinite(x) ? Math.min(1, Math.max(0, x)) : 1);
const canale = x => Math.min(255, Math.max(0, Math.round(x)));

/** `#hex`, `rgb()/rgba()` a virgole e `color(srgb …)` → { rgb:[r,g,b], alpha }. */
function scomponi(s) {
  let m = /^#([0-9a-f]{3,4}|[0-9a-f]{6}|[0-9a-f]{8})$/i.exec(s);
  if (m) {
    let h = m[1];
    if (h.length <= 4) h = h.split('').map(c => c + c).join('');
    const n = i => parseInt(h.slice(i, i + 2), 16);
    return { rgb: [n(0), n(2), n(4)], alpha: h.length === 8 ? Math.round((n(6) / 255) * 100) / 100 : 1 };
  }
  m = /^rgba?\(\s*([\d.]+)\s*,\s*([\d.]+)\s*,\s*([\d.]+)\s*(?:,\s*([\d.]+)\s*)?\)$/i.exec(s);
  if (m) {
    return {
      rgb: [m[1], m[2], m[3]].map(x => canale(parseFloat(x))),
      alpha: m[4] !== undefined ? alfa01(parseFloat(m[4])) : 1,
    };
  }
  // color-mix() esce dal canvas in questa forma, canali da 0 a 1.
  m = /^color\(srgb\s+([-\d.e]+)\s+([-\d.e]+)\s+([-\d.e]+)\s*(?:\/\s*([-\d.e]+)\s*)?\)$/i.exec(s);
  if (m) {
    return {
      rgb: [m[1], m[2], m[3]].map(x => canale(parseFloat(x) * 255)),
      alpha: m[4] !== undefined ? alfa01(parseFloat(m[4])) : 1,
    };
  }
  return null;
}

/**
 * Sostituisce ogni `var(--olo-color-*)` di un valore — intero o dentro una
 * funzione (`color-mix(…, var(--olo-color-primary, #e1474f) 12%, …)`) — col
 * colore del template. null se una variabile non si risolve: dipinta così
 * com'è prenderebbe il valore del CHROME del builder, non quello della pagina.
 */
function sostituisciToken(str, stylesStore) {
  let out = '';
  let i = 0;
  while (i < str.length) {
    const j = str.toLowerCase().indexOf('var(', i);
    if (j < 0) { out += str.slice(i); break; }
    let prof = 0;
    let k = j + 3;
    for (; k < str.length; k++) {
      if (str[k] === '(') prof++;
      else if (str[k] === ')' && --prof === 0) break;
    }
    if (k >= str.length) return null;
    const risolto = resolveColorToken(str.slice(j, k + 1), stylesStore);
    if (!risolto || /var\(/i.test(risolto)) return null;
    out += str.slice(i, j) + risolto;
    i = k + 1;
  }
  return out;
}

/**
 * Cosa dipinge un valore colore, per le anteprime dell'inspector:
 *   { kind, rgb, alpha, paint, reason }
 *   'color'   colore concreto: rgb [r,g,b] + alpha 0..1 — `transparent` compreso,
 *             che per CSS è nero ad alfa 0: la scacchiera lo mostra per quello che è
 *   'paint'   dipingibile ma non scomponibile (sfumatura, oklch…): niente alfa
 *   'empty'   nessun valore: decide la tile
 *   'keyword' currentColor, inherit…: dipende da dove sta, qui non si vede
 *   'unknown' token senza definizione né riserva (reason 'token'), altra var()
 *             (reason 'var'), testo che non è un colore (reason 'invalid')
 * Mai un nero di ripiego: dove il colore non si può sapere, lo si dice.
 */
export function describeColor(val, stylesStore) {
  const nulla = { rgb: null, alpha: null, paint: '', reason: '' };
  const v = String(val ?? '').trim();
  if (!v) return { ...nulla, kind: 'empty' };

  let s = v;
  if (/var\(/i.test(s)) {
    s = sostituisciToken(s, stylesStore);
    if (!s) return { ...nulla, kind: 'unknown', reason: tokenParts(v) ? 'token' : 'var' };
  }

  const low = s.toLowerCase();
  if (PAROLE_DI_CONTESTO.has(low) || /\bcurrentcolor\b/.test(low)) return { ...nulla, kind: 'keyword' };

  const c = scomponi(s) || scomponi(normalizzaViaCanvas(s));
  if (c) return { kind: 'color', rgb: c.rgb, alpha: c.alpha, paint: `rgba(${c.rgb.join(', ')}, ${c.alpha})`, reason: '' };

  const supporta = (prop, x) => typeof CSS !== 'undefined' && !!CSS.supports?.(prop, x);
  if (supporta('color', s) || (/gradient\(/i.test(s) && supporta('background-image', s))) {
    return { ...nulla, kind: 'paint', paint: s };
  }
  return { ...nulla, kind: 'unknown', reason: 'invalid' };
}
