/**
 * Token tipografici — lettura condivisa fra i campi dell'inspector.
 *
 * Gemello di `colorToken.js`, e per lo stesso motivo: le variabili
 * `--olo-font-*` sono definite su `.olo-template`, cioè DENTRO il canvas. Nel
 * pannello di destra non risolvono, quindi un'anteprima che si limitasse a
 * scrivere `font-family: var(--olo-font-family-heading)` mostrerebbe sempre il
 * font di sistema — cioè mentirebbe.
 */

// I valori-codice che le vecchie select salvavano per gli stessi ruoli.
const LEGACY_RUOLI = {
  heading: 'heading', serif: 'heading',
  body: 'base', sans: 'base',
  mono: 'mono',
};

function famiglieDiRuolo(stylesStore) {
  const t = stylesStore?.typography || {};
  return {
    base: t.font_family || '',
    heading: t.font_family_heading || t.font_family || '',
    mono: t.font_family_mono || '',
  };
}

/**
 * Valore di una famiglia → stringa CSS realmente dipingibile nell'inspector.
 * Accetta le tre forme storiche: `var(--olo-font-family-heading)`, l'id di un
 * set globale via `var(--olo-font-<id>-family)`, il codice legacy ('heading',
 * 'body', 'mono') e il nome di un font così com'è. Torna '' se non si sa.
 */
export function resolveFontToken(val, stylesStore) {
  const v = String(val || '').trim();
  if (!v || v === 'inherit') return '';

  const ruoli = famiglieDiRuolo(stylesStore);

  const ruolo = /^var\(\s*--olo-font-family(?:-(heading|mono))?\s*(?:,.*)?\)$/i.exec(v);
  if (ruolo) return ruoli[ruolo[1] ? ruolo[1].toLowerCase() : 'base'] || '';

  const set = /^var\(\s*--olo-font-([a-z0-9_-]+)-family\s*(?:,.*)?\)$/i.exec(v);
  if (set) {
    const hit = (stylesStore?.globalTypography || []).find(g => g.id === set[1].toLowerCase());
    // La famiglia di un set può essere a sua volta un ruolo: si risolve ancora.
    if (hit?.family) return resolveFontToken(hit.family, stylesStore);
    return '';
  }

  if (LEGACY_RUOLI[v.toLowerCase()]) return ruoli[LEGACY_RUOLI[v.toLowerCase()]] || '';

  return v;
}

/**
 * Carica in admin il Google Font che serve all'anteprima. L'inspector non è la
 * pagina del sito: se il font non è nel documento, l'anteprima ricade sul
 * sans di sistema senza dirlo. Un solo <link>, esteso man mano.
 *
 * Salta stack e valori con virgolette: lì il font o è di sistema o è già
 * caricato da altri (font personalizzati, @font-face del tema).
 */
const famiglieChieste = new Set();
export function assicuraFontPreview(family) {
  const v = String(family || '').trim();
  if (!v || v.includes(',') || v.includes("'") || v.includes('"') || v.startsWith('var(')) return;
  if (!/^[A-Za-z0-9 ]+$/.test(v)) return;
  if (famiglieChieste.has(v)) return;
  famiglieChieste.add(v);

  let link = document.getElementById('olo-typo-preview-fonts');
  if (!link) {
    link = document.createElement('link');
    link.rel = 'stylesheet';
    link.id = 'olo-typo-preview-fonts';
    document.head.appendChild(link);
  }
  const q = [...famiglieChieste].map(f => 'family=' + f.replace(/ /g, '+')).join('&');
  link.href = 'https://fonts.googleapis.com/css2?' + q + '&display=swap';
}
