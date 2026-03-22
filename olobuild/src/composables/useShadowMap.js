/**
 * Shadow mapping condiviso — usato da GridCell.vue e ovunque serva
 * convertire una chiave shadow in un valore CSS box-shadow.
 *
 * Supporta: preset (none|sm|md|lg|xl) e custom (h/v/blur/spread/color/inset).
 */
export const SHADOW_MAP = {
  none: 'none',
  sm: '0 1px 2px 0 rgba(0,0,0,0.05)',
  md: '0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1)',
  lg: '0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1)',
  xl: '0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1)',
};

/**
 * Ritorna valore CSS box-shadow da chiave preset.
 * Backward-compat: accetta una stringa (none|sm|md|lg|xl).
 */
export function getShadow(key) {
  return SHADOW_MAP[key] || 'none';
}

/**
 * Ritorna valore CSS box-shadow leggendo lo style di un tile.
 * Supporta sia preset (sm/md/lg/xl) che custom (shadow_h/v/blur/spread/color/inset).
 *
 * @param {Object} style — oggetto style del tile (s.value o equivalente)
 * @param {string} [prefix='shadow'] — prefisso dei campi (es. 'hover_shadow' per hover)
 * @returns {string} valore CSS box-shadow oppure 'none'
 */
export function getShadowValue(style, prefix = 'shadow') {
  const key = style?.[prefix];
  if (!key || key === 'none') return 'none';

  // Preset
  if (SHADOW_MAP[key]) return SHADOW_MAP[key];

  // Custom
  if (key === 'custom') {
    const h      = parseInt(style[`${prefix}_h`], 10) || 0;
    const v      = parseInt(style[`${prefix}_v`], 10) || 0;
    const blur   = parseInt(style[`${prefix}_blur`], 10) || 0;
    const spread = parseInt(style[`${prefix}_spread`], 10) || 0;
    const color  = style[`${prefix}_color`] || 'rgba(0,0,0,0.15)';
    const inset  = style[`${prefix}_inset`] ? 'inset ' : '';
    return `${inset}${h}px ${v}px ${blur}px ${spread}px ${color}`;
  }

  return 'none';
}

/**
 * Mappa preset → filter: drop-shadow() (per elementi con mask/clip-path).
 * drop-shadow NON supporta spread e inset, quindi i valori vengono omessi.
 * Ombre multiple diventano drop-shadow() concatenati.
 */
const DROP_SHADOW_MAP = {
  none: 'none',
  sm: 'drop-shadow(0 1px 2px rgba(0,0,0,0.05))',
  md: 'drop-shadow(0 4px 6px rgba(0,0,0,0.1)) drop-shadow(0 2px 4px rgba(0,0,0,0.1))',
  lg: 'drop-shadow(0 10px 15px rgba(0,0,0,0.1)) drop-shadow(0 4px 6px rgba(0,0,0,0.1))',
  xl: 'drop-shadow(0 20px 25px rgba(0,0,0,0.1)) drop-shadow(0 8px 10px rgba(0,0,0,0.1))',
};

/**
 * Ritorna valore CSS filter: drop-shadow() leggendo lo style di un tile.
 * Usare al posto di getShadowValue quando l'elemento ha una mask/clip-path,
 * perché drop-shadow segue la forma visibile (non il rettangolo).
 *
 * Nota: inset e spread vengono ignorati (non supportati da drop-shadow).
 *
 * @param {Object} style — oggetto style del tile
 * @param {string} [prefix='shadow'] — prefisso dei campi
 * @returns {string} valore CSS drop-shadow per filter, oppure 'none'
 */
export function getDropShadowValue(style, prefix = 'shadow') {
  const key = style?.[prefix];
  if (!key || key === 'none') return 'none';

  // Preset
  if (DROP_SHADOW_MAP[key]) return DROP_SHADOW_MAP[key];

  // Custom (inset e spread ignorati)
  if (key === 'custom') {
    const h     = parseInt(style[`${prefix}_h`], 10) || 0;
    const v     = parseInt(style[`${prefix}_v`], 10) || 0;
    const blur  = parseInt(style[`${prefix}_blur`], 10) || 0;
    const color = style[`${prefix}_color`] || 'rgba(0,0,0,0.15)';
    return `drop-shadow(${h}px ${v}px ${blur}px ${color})`;
  }

  return 'none';
}
