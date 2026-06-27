/**
 * useBoxModel — composable di normalizzazione box-model (RENDER side).
 *
 * PROBLEMA che risolve
 * --------------------
 * Oggi quasi ogni *Tile.vue re-implementa lo stesso parsing:
 *   - border_radius può essere numero (6) OPPURE oggetto ({tl,tr,br,bl})
 *   - tile_padding può essere oggetto ({top,right,bottom,left}) con fallback
 *     legacy padding_x / padding_y
 *   - stesso per margin
 * Questo codice è copiato in ButtonTile, HeroTile (radiusCss), StyleBoxStack
 * (radiusPreviewCss), ecc. → divergenze e bug.
 *
 * È il gemello, lato RENDER, di ciò che FieldBox ha standardizzato lato INPUT.
 * Il contratto dati salvato NON cambia: questo composable LEGGE i formati esistenti.
 *
 * ⚠️ Riferimento per Claude Code: adattare import/percorsi e verificare i nomi
 * delle chiavi legacy rispetto allo schema reale del progetto prima dell'adozione.
 */
import { computed, unref } from 'vue';

const int = (v, fb = 0) => {
  const n = parseInt(v, 10);
  return Number.isFinite(n) ? n : fb;
};

/** radius: number | {tl,tr,br,bl} → "Npx Npx Npx Npx" */
export function toRadiusCss(val, fallback = 0) {
  if (val && typeof val === 'object') {
    return `${int(val.tl, fallback)}px ${int(val.tr, fallback)}px ${int(val.br, fallback)}px ${int(val.bl, fallback)}px`;
  }
  return `${int(val, fallback)}px`;
}

/**
 * spacing: {top,right,bottom,left} | number → "Tpx Rpx Bpx Lpx"
 * legacy: { x: padding_x, y: padding_y } per retrocompat (solo se val è undefined)
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

/** border: oggetto FieldBorder | legacy {width,style,color} → shorthand CSS o null */
export function toBorderCss(border, legacy = null) {
  if (border && typeof border === 'object' && !border.linked && border.sides) {
    // 4 lati indipendenti: il chiamante può preferire le 4 proprietà separate
    return border; // lasciare al renderer la composizione per-lato
  }
  if (legacy && int(legacy.width) > 0) {
    return `${int(legacy.width)}px ${legacy.style || 'solid'} ${legacy.color || 'currentColor'}`;
  }
  return null;
}

/**
 * useBoxModel(settingsRef) — espone i CSS pronti, reattivi.
 * Uso in un Tile:
 *   const { radiusCss, paddingCss } = useBoxModel(s, {
 *     radiusKey: 'border_radius', paddingKey: 'tile_padding',
 *     paddingFallback: [14,32,14,32], paddingLegacy: ['padding_y','padding_x'],
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
