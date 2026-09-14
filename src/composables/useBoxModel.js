/**
 * useBoxModel — composable di normalizzazione box-model (lato RENDER).
 *
 * Adattamento allo stack reale del prototipo `regoletiles1/prototype/useBoxModel.js`.
 *
 * PROBLEMA che risolve
 * --------------------
 * Quasi ogni *Tile.vue re-implementa lo stesso parsing:
 *   - border_radius può essere numero (6) OPPURE oggetto ({tl,tr,br,bl})
 *   - tile_padding può essere oggetto ({top,right,bottom,left}) con fallback
 *     legacy padding_x / padding_y
 * Questo composable LEGGE i formati esistenti (il contratto dati NON cambia) e
 * restituisce stringhe CSS pronte, reattive.
 *
 * Riuso: per il radius delega a `radiusToCss` di useRadius.js (già condiviso).
 */
import { computed, unref } from 'vue';
import { radiusToCss } from './useRadius';

const int = (v, fb = 0) => {
  const n = parseInt(v, 10);
  return Number.isFinite(n) ? n : fb;
};

/** radius: number | {tl,tr,br,bl} → "Npx" oppure "TLpx TRpx BRpx BLpx" */
export function toRadiusCss(val, fallback = 0) {
  return radiusToCss(val, { fallback: `${int(fallback, 0)}px` });
}

/**
 * spacing: {top,right,bottom,left} | number → "Tpx Rpx Bpx Lpx"
 * legacy: { x, y } per retrocompat (usato solo se val è undefined/null/'').
 */
export function toSpacingCss(val, { legacy = null, fallback = [0, 0, 0, 0] } = {}) {
  return sidesCss(toSpacingSides(val, { legacy, fallback }));
}

/**
 * spacing → { top, right, bottom, left } numerici. Gemello JS di
 * `Olobuild_Tile_Utils::spacing_sides()`. Da usare quando il renderer deve fare
 * aritmetica su un lato e non gli basta la shorthand di toSpacingCss().
 *
 * legacy accetta { y, x } e/o { all }: le vecchie chiavi piatte.
 */
export function toSpacingSides(val, { legacy = null, fallback = [0, 0, 0, 0] } = {}) {
  const [ft, fr, fb, fl] = fallback;
  // Oggetto senza alcun lato = non impostato.
  if (val && typeof val === 'object' && !['top', 'right', 'bottom', 'left'].some((k) => k in val)) val = null;
  if (val && typeof val === 'object') {
    return { top: int(val.top, ft), right: int(val.right, fr), bottom: int(val.bottom, fb), left: int(val.left, fl) };
  }
  if (val !== undefined && val !== null && val !== '') {
    const n = int(val);
    return { top: n, right: n, bottom: n, left: n };
  }
  const has = (v) => v !== undefined && v !== null && v !== '';
  const all = legacy && has(legacy.all) ? int(legacy.all) : null;
  const y = legacy && has(legacy.y) ? int(legacy.y) : all;
  const x = legacy && has(legacy.x) ? int(legacy.x) : all;
  return {
    top: y === null || y === undefined ? ft : y,
    right: x === null || x === undefined ? fr : x,
    bottom: y === null || y === undefined ? fb : y,
    left: x === null || x === undefined ? fl : x,
  };
}

/** Shorthand CSS dai 4 lati di toSpacingSides(). */
export function sidesCss(s) {
  return `${int(s?.top)}px ${int(s?.right)}px ${int(s?.bottom)}px ${int(s?.left)}px`;
}

/**
 * Lunghezza CSS che accetta sia il numero nudo sia la stringa con unità.
 * Gemello JS di `Olobuild_Tile_Utils::css_len()`: rende il renderer indifferente
 * al controllo che ha scritto il valore (FieldUnit salva '0.2em', il pannello
 * Tipografia salva 0.2).
 */
export function cssLen(val, unit = 'px', fallback = '') {
  if (val === undefined || val === null || val === '' || typeof val === 'object') return fallback;
  const v = String(val).trim();
  if (v === '') return fallback;
  if (Number.isNaN(Number(v))) return v;
  return v + (/^[a-z%]{1,4}$/i.test(unit) ? unit : 'px');
}

const BORDER_STYLES = ['solid', 'dashed', 'dotted', 'double', 'groove', 'ridge', 'inset', 'outset', 'none', 'hidden'];

/**
 * Colore di un bordo indipendentemente dal formato salvato: la chiave può
 * contenere la vecchia stringa colore o l'oggetto del controllo standard.
 * Gemello JS di `Olobuild_Tile_Utils::border_color()`.
 */
export function borderColorOf(val, fallback = '') {
  if (val && typeof val === 'object') return val.color || fallback;
  return val || fallback;
}

/** true se il bordo (in uno dei due formati) è visibile. */
export function hasBorder(val) {
  if (val && typeof val === 'object') {
    const w = Math.max(int(val.top), int(val.right), int(val.bottom), int(val.left));
    return !!val.color && w > 0;
  }
  return !!val;
}

/**
 * Bordo dal controllo standard `type:'border'` — `{top,right,bottom,left,style,color}` —
 * con ripiego sulle vecchie chiavi piatte. Gemello JS di
 * `Olobuild_Tile_Utils::border_css()`; restituisce un oggetto di stile Vue
 * (non una stringa) così si fonde con gli altri :style.
 *
 *   toBorderStyle(s.card_border, { width: s.card_border_width, color: s.card_border_color })
 */
/** true se l'oggetto bordo ha davvero qualcosa di impostato (lato > 0 o colore). */
export function borderIsSet(val) {
  if (!val || typeof val !== 'object') return val !== undefined && val !== null && val !== '';
  if (String(val.color || '').trim() !== '') return true;
  return ['top', 'right', 'bottom', 'left'].some((k) => int(val[k]) > 0);
}

export function toBorderStyle(val, legacy = {}) {
  let t, r, b, l, style, color;
  // Oggetto vuoto = non impostato: si ricade sulle chiavi piatte.
  if (val && typeof val === 'object' && !borderIsSet(val)) val = null;
  if (val && typeof val === 'object') {
    t = Math.max(0, int(val.top));
    r = Math.max(0, int(val.right));
    b = Math.max(0, int(val.bottom));
    l = Math.max(0, int(val.left));
    style = String(val.style || 'solid');
    color = String(val.color || '');
  } else {
    const w = Math.max(0, int(legacy.width));
    t = w; r = w; b = w; l = w;
    style = String(legacy.style || 'solid');
    color = String(legacy.color || '');
  }
  if (!color || (!t && !r && !b && !l)) return {};
  if (!BORDER_STYLES.includes(style)) style = 'solid';
  if (t === r && r === b && b === l) return { border: `${t}px ${style} ${color}` };
  const out = {};
  if (t) out.borderTop = `${t}px ${style} ${color}`;
  if (r) out.borderRight = `${r}px ${style} ${color}`;
  if (b) out.borderBottom = `${b}px ${style} ${color}`;
  if (l) out.borderLeft = `${l}px ${style} ${color}`;
  return out;
}

/**
 * useBoxModel(settingsRef, opts) — espone i CSS pronti, reattivi.
 *
 * Uso in un Tile:
 *   const { radiusCss, paddingCss } = useBoxModel(s, {
 *     radiusKey: 'border_radius', radiusFallback: 10,
 *     paddingKey: 'tile_padding', paddingFallback: [12, 24, 12, 24],
 *     paddingLegacy: ['padding_y', 'padding_x'],
 *   });
 */
export function useBoxModel(settings, opts = {}) {
  const get = (k) => (k ? unref(settings)?.[k] : undefined);

  const radiusCss = computed(() =>
    toRadiusCss(get(opts.radiusKey || 'border_radius'), opts.radiusFallback ?? 0),
  );

  const paddingCss = computed(() => {
    const s = unref(settings) || {};
    const legacy = opts.paddingLegacy
      ? { y: s[opts.paddingLegacy[0]], x: s[opts.paddingLegacy[1]] }
      : null;
    return toSpacingCss(get(opts.paddingKey || 'tile_padding'), {
      legacy,
      fallback: opts.paddingFallback ?? [0, 0, 0, 0],
    });
  });

  const marginCss = computed(() =>
    toSpacingCss(get(opts.marginKey || 'tile_margin'), { fallback: opts.marginFallback ?? [0, 0, 0, 0] }),
  );

  return { radiusCss, paddingCss, marginCss, toRadiusCss, toSpacingCss };
}
