/**
 * «Gradini di luce» (1.4.1218): barre accostate che salgono verso un picco, ognuna con le stesse fasce di colore a
 * quote fisse (il colore cambia salendo, uguale in tutte le barre), e un alone morbido ai lati che le fonde. Il
 * gradiente a gradini di Sombra, fatto coi gradienti CSS (niente SVG), così i colori del tema restano token.
 * Gemello PHP: Olobuild_CSS_Builder::build_gradini_css(). Stesse chiavi, stessi conti.
 *
 * Chiavi: gradini_colori[] (dal basso in su), gradini_base, gradini_n (barre, 3–16), gradini_picco (dove sta la barra
 * più alta, 0–100 da sinistra), gradini_altezza (quanto sale il picco, % dell'altezza), gradini_curva (quanto
 * scendono le barre lontane dal picco, 0 = tutte uguali), gradini_alone (0–100), gradini_alto (dall'alto invece che
 * dal basso).
 */
import { meshColorToCss } from '@/utils/meshCSS';

export const GRADINI_DEFAULTS = {
  gradini_base: 'var(--olo-color-background, #0b0a0d)',
  gradini_n: 9,
  gradini_picco: 50,
  gradini_altezza: 72,
  gradini_curva: 45,
  gradini_alone: 60,
};

const intero = (v, d) => {
  const n = parseInt(v, 10);
  return Number.isFinite(n) ? n : d;
};
const tra = (v, a, b) => Math.max(a, Math.min(b, v));

/** I colori dal basso in su; senza scelta, i ruoli del tema (con la riserva dentro il var). */
export function getGradiniColors(bg = {}) {
  const lista = Array.isArray(bg.gradini_colori) ? bg.gradini_colori.filter((c) => c !== '' && c != null) : [];
  if (lista.length) return lista;
  return ['var(--olo-color-primary)', 'var(--olo-color-secondary, var(--olo-color-primary))', 'var(--olo-color-accent, var(--olo-color-primary))'];
}

export function getGradiniCSS(bg = {}) {
  const v = (k) => (bg[k] === undefined || bg[k] === null || bg[k] === '' ? GRADINI_DEFAULTS[k] : bg[k]);
  const n = tra(intero(v('gradini_n'), 9), 3, 16);
  const picco = tra(intero(v('gradini_picco'), 50), 0, 100);
  const alt = tra(intero(v('gradini_altezza'), 72), 10, 100);
  const curva = tra(intero(v('gradini_curva'), 45), 0, 100);
  const alone = tra(intero(v('gradini_alone'), 60), 0, 100);
  const verso = bg.gradini_alto ? 'to bottom' : 'to top';
  const colori = getGradiniColors(bg);
  const m = colori.length;
  const w = 100 / n;
  const pk = (picco / 100) * (n - 1);
  const barra = (h, alfa) => {
    // le fasce a quote fisse: il colore k sta a alt·k/(m−1) in tutte le barre; la barra si ferma a h e sfuma
    const stop = [];
    let ultimo = meshColorToCss(colori[0], alfa);
    for (let k = 0; k < m; k++) {
      const q = m === 1 ? 0 : (alt * k) / (m - 1);
      if (q > h - 4 && k > 0) break;
      ultimo = meshColorToCss(colori[k], alfa);
      stop.push(`${ultimo} ${q.toFixed(2)}%`);
    }
    stop.push(`${ultimo} ${Math.max(0, h - 4).toFixed(2)}%`, `transparent ${h.toFixed(2)}%`);
    return `linear-gradient(${verso}, ${stop.join(', ')})`;
  };
  const img = [];
  const size = [];
  const pos = [];
  const fila = (largh, spost, alfa) => {
    for (let i = 0; i < n; i++) {
      const dist = Math.abs(i - pk) / Math.max(1, n - 1);
      const h = alt * Math.max(0.1, 1 - (curva / 100) * 1.8 * dist);
      const sinistra = i * w - spost;
      img.push(barra(h, alfa));
      size.push(`${largh.toFixed(3)}% 100%`);
      pos.push(`${(largh >= 100 ? 0 : (sinistra / (100 - largh)) * 100).toFixed(3)}% 0`);
    }
  };
  // le barre si sovrappongono di un soffio: accostate e basta, gli arrotondamenti lasciavano un filo di fondo fra due barre
  fila(w + 0.3, 0.15, 1);
  if (alone > 0) fila(w * 1.9, w * 0.45, (alone / 100) * 0.6);
  return {
    backgroundColor: v('gradini_base'),
    backgroundImage: img.join(', '),
    backgroundSize: size.join(', '),
    backgroundPosition: pos.join(', '),
    backgroundRepeat: 'no-repeat',
  };
}
