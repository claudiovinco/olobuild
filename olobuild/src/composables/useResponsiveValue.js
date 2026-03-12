/**
 * useResponsiveValue — Risolve valori responsivi con cascade tra breakpoint.
 *
 * Cascade order (max-width, come CSS reale):
 *   mobile         → mobile_landscape → tablet → tablet_landscape → desktop
 *   mobile_landscape → tablet → tablet_landscape → desktop
 *   tablet         → tablet_landscape → desktop
 *   tablet_landscape → desktop
 *   widescreen     → desktop (min-width, nessun cascade verso il basso)
 *   desktop        → valore base
 */

import { useBuilderStore } from '@/stores/builder';

/**
 * Ordine di fallback per ogni breakpoint.
 * Se il viewMode è "mobile" e non c'è un valore _mobile, cerca _mobile_landscape,
 * poi _tablet, poi _tablet_landscape, poi il valore desktop (base).
 */
const CASCADE = {
  desktop:          [],
  widescreen:       ['_widescreen'],
  tablet_landscape: ['_tablet_landscape'],
  tablet:           ['_tablet', '_tablet_landscape'],
  mobile_landscape: ['_mobile_landscape', '_tablet', '_tablet_landscape'],
  mobile:           ['_mobile', '_mobile_landscape', '_tablet', '_tablet_landscape'],
};

/**
 * Risolve il valore di una proprietà per il breakpoint corrente, con cascade.
 *
 * @param {Object} obj - L'oggetto da cui leggere (tile.style o tile.settings)
 * @param {string} key - La chiave base (es. 'font_size', 'margin_top')
 * @param {string} [mode] - Override del viewMode (se omesso usa lo store)
 * @returns {*} Il valore risolto, o undefined se nessun valore trovato
 */
export function resolveResponsive(obj, key, mode) {
  if (!obj) return undefined;
  if (!mode) {
    const store = useBuilderStore();
    mode = store.viewMode;
  }

  // Desktop: valore base, nessun suffisso
  if (mode === 'desktop') return obj[key];

  // Cerca nella catena di fallback
  const chain = CASCADE[mode] || [];
  for (const suffix of chain) {
    const val = obj[key + suffix];
    if (val !== undefined && val !== null && val !== '') return val;
  }

  // Fallback al valore desktop (base)
  return obj[key];
}

/**
 * Versione "or default": come resolveResponsive ma con valore di fallback.
 */
export function rv(obj, key, fallback, mode) {
  const val = resolveResponsive(obj, key, mode);
  return (val !== undefined && val !== null && val !== '') ? val : fallback;
}

/**
 * Controlla se il valore corrente per un dato breakpoint è diverso dal desktop.
 * Utile per mostrare indicatori visivi di override attivo.
 */
export function hasResponsiveOverride(obj, key, mode) {
  if (!obj || !mode || mode === 'desktop') return false;
  const chain = CASCADE[mode] || [];
  for (const suffix of chain) {
    const val = obj[key + suffix];
    if (val !== undefined && val !== null && val !== '') return true;
  }
  return false;
}
