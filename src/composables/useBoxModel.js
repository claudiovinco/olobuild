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
  const [ft, fr, fb, fl] = fallback;
  if (val && typeof val === 'object') {
    return `${int(val.top, ft)}px ${int(val.right, fr)}px ${int(val.bottom, fb)}px ${int(val.left, fl)}px`;
  }
  if (val !== undefined && val !== null && val !== '') {
    const n = int(val);
    return `${n}px ${n}px ${n}px ${n}px`;
  }
  if (legacy && (legacy.x !== undefined || legacy.y !== undefined)) {
    const y = int(legacy.y, ft);
    const x = int(legacy.x, fr);
    return `${y}px ${x}px ${y}px ${x}px`;
  }
  return `${ft}px ${fr}px ${fb}px ${fl}px`;
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
