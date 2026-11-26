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
import { t } from '@/i18n';

// I nomi in italiano (16 nov 2026): nel campo colore comparivano «Primary», «Warning» accanto a «Testo» e «Sfondo».
// Si traducono quando servono (t() al momento della lettura, non al caricamento del modulo).
export const ROLE_SWATCHES = [
  { id: 'primary', label: 'Primario' }, { id: 'secondary', label: 'Secondario' },
  { id: 'success', label: 'Successo' }, { id: 'warning', label: 'Avviso' },
  { id: 'danger', label: 'Pericolo' }, { id: 'link', label: 'Link' },
  { id: 'text', label: 'Testo' }, { id: 'background', label: 'Sfondo' },
  { id: 'muted', label: 'Superficie' }, { id: 'border', label: 'Bordo' },
];

// Gli altri token della Palette, che non sono fra le swatch ma le tile usano: il loro nome leggibile.
const NOMI_TOKEN = {
  accent: 'Accento', dark: 'Scuro', light: 'Chiaro', surface: 'Superficie', 'surface-alt': 'Superficie alternativa',
  'text-muted': 'Testo tenue', 'primary-contrast': 'Sul primario', 'on-primary': 'Sul primario',
  'secondary-contrast': 'Sul secondario', info: 'Informazione',
  // Alias che lo stile emette sempre (Olobuild_Style_System::ALIAS_COLORI): prima comparivano in inglese («Text soft»).
  'text-soft': 'Testo tenue', 'text-faint': 'Testo tenue', error: 'Pericolo',
};

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
 * Swatch della palette: ruoli del tema (olo_styles.colors) + globali custom.
 * Un globale NASCOSTO (`hidden`, «Nascondi» del builder o della Configurazione)
 * è ancora qui, col suo flag: il sito continua a emetterne il token e le tile
 * che lo usano devono ancora risolverlo e chiamarlo per nome. Chi mostra le
 * swatch da scegliere (FieldColor) lo toglie dalla lista che rende.
 */
export function buildSwatchColors(stylesStore) {
  const c = stylesStore?.colors || {};
  const gcs = stylesStore?.globalColors || [];
  const roleIds = new Set([...ROLE_SWATCHES, ...ROLE_SWATCHES_ALTRI].map(r => r.id));
  const roles = ROLE_SWATCHES
    .filter(r => c[r.id])
    .map(r => ({ id: r.id, label: t(r.label), value: c[r.id], quick: false, hidden: false }));
  // gli altri ruoli: dal colore globale con lo stesso id, se il sito ce l'ha, se no da quello che la Palette emette
  const altri = ROLE_SWATCHES_ALTRI.map((r) => {
    const g = gcs.find(x => x && x.id === r.id && x.value);
    const v = g ? g.value : (c[r.id.replace(/-/g, '_')] || c[r.id] || templateColorTokens(stylesStore)[r.id] || tokenDalCanvas(r.id));
    return { id: r.id, label: t(r.label), value: dipingibile(v, stylesStore), quick: false, hidden: false };
  }).filter(r => r.value);
  const globals = gcs
    .filter(g => g && g.id && !roleIds.has(g.id))
    .map(g => ({ id: g.id, label: g.label || g.id, value: g.value, quick: !!g.quick, hidden: !!g.hidden }));
  return [...roles, ...altri, ...globals];
}

// Gli altri ruoli che la Palette emette SEMPRE (1.4.1438; Scuro, Chiaro e Accento li ricava dal primario se il sito non
// li ha, RUOLI_DERIVATI). Rifacendo a mano Elastan: i temi li usano a migliaia nei Pro Slider (Chiaro 4.563 volte,
// Scuro 2.288, Accento 695, Sul primario 276) ma fra le pastiglie non c'erano, e per sceglierli bisognava scrivere
// la variabile a mano. Si salvano come i ruoli: token nudi, senza riserva.
export const ROLE_SWATCHES_ALTRI = [
  { id: 'accent', label: 'Accento' }, { id: 'dark', label: 'Scuro' }, { id: 'light', label: 'Chiaro' },
  { id: 'primary-contrast', label: 'Sul primario' }, { id: 'secondary-contrast', label: 'Sul secondario' },
  { id: 'text-muted', label: 'Testo tenue' },
];

// Un valore della Palette può nominare altri token (`color-mix(in srgb, var(--olo-color-primary) 12%, #14161c)`):
// nel pannello non esistono, e la pastiglia resterebbe vuota. Si sostituiscono coi loro valori (senza passare da
// buildSwatchColors, che chiama questa funzione).
function dipingibile(v, stylesStore, prof = 0) {
  const s = String(v || '').trim();
  if (!s || prof > 6 || !/var\(\s*--olo-color-/i.test(s)) return s;
  const c = stylesStore?.colors || {};
  const mappa = templateColorTokens(stylesStore);
  const fuori = s.replace(/var\(\s*--olo-color-([a-z0-9_-]+)\s*(?:,\s*([^()]*))?\)/gi, (tutto, id, riserva) => {
    const k = id.toLowerCase();
    const gc = (stylesStore?.globalColors || []).find(x => x && x.id === k && x.value);
    return (gc && gc.value) || c[k] || c[k.replace(/-/g, '_')] || mappa[k] || tokenDalCanvas(k) || (riserva || '').trim() || tutto;
  });
  return fuori === s ? s : dipingibile(fuori, stylesStore, prof + 1);
}

/**
 * Il token da salvare quando si sceglie una swatch.
 * Un globale custom porta con sé la RISERVA, l'hex che ha adesso:
 * `var(--olo-color-c1a2b3, #1e3a8a)`. Se un giorno il colore sparisse dalla
 * palette (eliminato, o un sito importato senza), la tile resta del suo colore
 * invece di perdere la dichiarazione in silenzio. La riserva è SEMPRE un hex
 * (#rrggbb, #rrggbbaa con l'alfa): il PHP (safe_color_css) accetta solo una
 * riserva senza parentesi, e un rgba() la farebbe scartare per intero.
 * I ruoli del tema (primary, text…) non si eliminano: restano token nudi.
 */
export function tokenDaSalvare(id, stylesStore) {
  const tok = `var(--olo-color-${id})`;
  if (ROLE_SWATCHES.some(r => r.id === id) || ROLE_SWATCHES_ALTRI.some(r => r.id === id)) return tok;
  const d = describeColor(tok, stylesStore);
  if (d.kind !== 'color' || !Array.isArray(d.rgb)) return tok;
  const hex2 = n => Math.min(255, Math.max(0, Math.round(n))).toString(16).padStart(2, '0');
  let riserva = '#' + d.rgb.map(hex2).join('');
  if (d.alpha < 1) riserva += hex2(d.alpha * 255);
  return `var(--olo-color-${id}, ${riserva})`;
}

/**
 * Nome leggibile di un token: l'etichetta dello swatch se il colore è in
 * palette, altrimenti l'id ripulito — «text-faint» vale «Text faint», che si
 * legge, mentre `var(--olo-color-text-fai…` troncato non vuol dire niente.
 */
export function tokenLabel(val, stylesStore) {
  const v = String(val || '').trim();
  // «transparent» (il nessuno sfondo di molti predefiniti: Sfondo grafico, Sfondo sezione…) si legge in italiano,
  // non come la parola grezza in monospazio (16 nov 2026).
  if (/^transparent$/i.test(v)) return t('Trasparente');
  // Un token con la trasparenza (`color-mix(in srgb, var(--olo-color-x) 35%, transparent)`, come lo salvano i temi e
  // le partenze delle tile) ha lo stesso nome: l'alfa la mostra già la sua barra. Prima il campo mostrava «color-mix(».
  const mix = /^color-mix\(\s*in\s+srgb\s*,\s*(var\(\s*--olo-color-[a-z0-9_-]+\s*(?:,[^()]*)?\))\s*\d+(?:\.\d+)?%\s*,\s*transparent\s*\)$/i
    .exec(v);
  // Un token mescolato a un altro (`color-mix(in srgb, var(--olo-color-warning) 30%, var(--olo-color-background))`,
  // le tinte degli stili delle tile): «Avviso 30%», col secondo colore solo se non è lo Sfondo.
  const due = /^color-mix\(\s*in\s+srgb\s*,\s*(var\(\s*--olo-color-[a-z0-9_-]+\s*(?:,[^()]*)?\))\s*(\d+(?:\.\d+)?)%\s*,\s*(var\(\s*--olo-color-[a-z0-9_-]+\s*(?:,[^()]*)?\))\s*\)$/i
    .exec(v);
  if (due) {
    const a = tokenLabel(due[1], stylesStore);
    const b = tokenParts(due[3]);
    if (a && b) return a + ' ' + due[2] + '%' + (b.id === 'background' ? '' : ' · ' + tokenLabel(due[3], stylesStore));
  }
  const tk = tokenParts(mix ? mix[1] : v);
  if (!tk) return '';
  const hit = buildSwatchColors(stylesStore).find(s => s.id === tk.id);
  if (hit) return hit.label;
  if (NOMI_TOKEN[tk.id]) return t(NOMI_TOKEN[tk.id]);
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
// Gli alias che il tema definisce fuori dalla Palette (`dark`, `light`, `surface`… nel CSS del tema o di OLObuild)
// non stanno in `cssVariables`: la pagina li conosce, il pannello no, e il campo mostrava il quadratino a righe
// («Scuro», «Chiaro» nel Listino prezzi, 16 nov 2026). Si leggono dal canvas, che È la pagina: il valore calcolato
// di una proprietà personalizzata ha già i var() sostituiti. Mentre il canvas si ricarica non si trovano: vale
// l'ultimo letto (si rilegge ogni pochi secondi, così un cambio di Palette arriva).
const vivoCache = new Map();
function tokenDalCanvas(id) {
  const ora = Date.now();
  const c = vivoCache.get(id);
  if (c && ora - c.t < 3000) return c.v;
  let v = '';
  try {
    const doc = document.querySelector('iframe.olo-live-iframe')?.contentDocument;
    const el = doc && (doc.querySelector('.olo-template') || doc.documentElement);
    if (el) v = (doc.defaultView.getComputedStyle(el).getPropertyValue('--olo-color-' + id) || '').trim();
  } catch (_) { v = ''; }
  if (v) vivoCache.set(id, { t: ora, v });
  return v || (c ? c.v : '');
}

export function resolveColorToken(val, stylesStore, depth = 0) {
  const v = String(val || '').trim();
  if (!v) return '';
  const t = tokenParts(v);
  if (!t) return v;
  if (depth > 8) return ''; // token che rimandano l'uno all'altro in cerchio
  const def = templateColorTokens(stylesStore)[t.id]
    || buildSwatchColors(stylesStore).find(s => s.id === t.id)?.value
    || tokenDalCanvas(t.id);
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
function sostituisciToken(str, stylesStore, giro = 0) {
  if (giro > 6) return null;
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
    let risolto = resolveColorToken(str.slice(j, k + 1), stylesStore);
    // Un token definito come mescola di un altro (`--olo-color-dark: color-mix(…, var(--olo-color-primary) 12%, …)`):
    // si risolvono anche i var() annidati. Prima il campo si arrendeva e mostrava il quadratino a righe.
    if (risolto && /var\(/i.test(risolto)) risolto = sostituisciToken(risolto, stylesStore, giro + 1);
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
