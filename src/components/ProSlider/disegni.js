/**
 * «Colori del disegno» (1.4.1433): i disegni SVG dello Slider Pro coi colori della palette al posto di quelli del file.
 * Gemello di includes/class-disegni.php: il server dà il disegno pulito e i suoi colori (rotta olobuild/v1/disegno),
 * qui si ricolora per la tela dell'editor come fa il sito, da immagine (data URI): i disegni hanno animazioni e stili
 * interni coi nomi corti che, stampati nella pagina, toccherebbero il resto.
 */
import { reactive } from 'vue';

const archivio = reactive({}); // src → { stato: 'carico' | 'ok' | 'errore', svg, colori: [{ colore, volte }] }
const fatti = new Map();       // src + mappa + valori → data URI
const HEX = /#(?:[0-9a-fA-F]{6}|[0-9a-fA-F]{3})(?![0-9a-zA-Z])/g;
const SOLO_HEX = /^#(?:[0-9a-fA-F]{6}|[0-9a-fA-F]{3})$/;
const ATTRIBUTI = ['fill', 'stroke', 'stop-color', 'flood-color', 'lighting-color', 'color'];

export const eDisegno = (src) => /\.svg(\?.*)?$/i.test(String(src || ''));
export function normHex(h) {
  let x = String(h || '').replace('#', '').toUpperCase();
  if (x.length === 3) x = x.split('').map((c) => c + c).join('');
  return '#' + x;
}

/** Il disegno pulito e i suoi colori, letti una volta dal server. */
export function disegno(src) {
  if (!eDisegno(src)) return null;
  if (!archivio[src]) {
    archivio[src] = { stato: 'carico', svg: '', colori: [] };
    const o = window.oloData || {};
    fetch(`${o.restUrl}disegno?src=${encodeURIComponent(src)}`, { headers: { 'X-WP-Nonce': o.nonce }, credentials: 'same-origin' })
      .then((r) => (r.ok ? r.json() : Promise.reject(r)))
      .then((d) => { archivio[src] = { stato: 'ok', svg: String(d.svg || ''), colori: Array.isArray(d.colori) ? d.colori : [] }; })
      .catch(() => { archivio[src] = { stato: 'errore', svg: '', colori: [] }; });
  }
  return archivio[src];
}

/** var(--olo-color-x[, riserva]) → il valore che quel colore ha adesso (dentro il disegno le variabili non arrivano). */
export function risolviColore(v, el) {
  let out = String(v || '');
  const cs = el ? getComputedStyle(el) : null;
  for (let i = 0; i < 6 && out.includes('var('); i++) {
    out = out.replace(/var\(\s*(--olo-color-[a-z0-9_-]+)\s*(?:,\s*([^()]*(?:\([^()]*\))?[^()]*))?\)/gi, (m, nome, riserva) => {
      const c = cs ? cs.getPropertyValue(nome).trim() : '';
      return c || (riserva ? riserva.trim() : 'currentColor');
    });
  }
  return out;
}

/** Il disegno coi colori nuovi come data URI, o '' (niente da cambiare, disegno non leggibile). */
export function disegnoRicolorato(src, mappa, el) {
  const d = disegno(src);
  if (!d || d.stato !== 'ok' || !mappa || !Object.keys(mappa).length) return '';
  const map = {};
  for (const [da, a] of Object.entries(mappa)) {
    const r = risolviColore(a, el);
    if (r && !/[;{}<>"]/.test(r)) map[normHex(da)] = r;
  }
  if (!Object.keys(map).length) return '';
  const chiave = src + '|' + JSON.stringify(map);
  if (fatti.has(chiave)) return fatti.get(chiave);
  const doc = new DOMParser().parseFromString(d.svg, 'image/svg+xml');
  if (doc.querySelector('parsererror')) return '';
  const sost = (t) => String(t).replace(HEX, (m) => map[normHex(m)] || m);
  doc.querySelectorAll('*').forEach((n) => {
    if (n.localName === 'style') { n.textContent = sost(n.textContent); return; }
    let st = n.getAttribute('style') || '';
    for (const at of ATTRIBUTI) {
      const v = (n.getAttribute(at) || '').trim();
      if (SOLO_HEX.test(v) && map[normHex(v)]) {
        n.removeAttribute(at);
        st = (st ? st.replace(/;?\s*$/, ';') : '') + at + ':' + map[normHex(v)];
      }
    }
    if (st) n.setAttribute('style', sost(st));
  });
  const uri = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(new XMLSerializer().serializeToString(doc.documentElement));
  if (fatti.size > 200) fatti.clear();
  fatti.set(chiave, uri);
  return uri;
}
