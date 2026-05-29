/**
 * Helper unificato per convertire un border-radius (oggetto 4-angoli, numero o
 * stringa) in CSS valido. Estratto da CarouselTile / LightboxTile / NewstickerTile
 * / PortfolioTile / PostMetaTile / QueryloopTile / SitemapTile che avevano 5
 * dialect leggermente diversi.
 *
 * @param  {*}      value           Input: `{tl,tr,br,bl}` | number | numeric string | null/undefined
 * @param  {Object} [opts]
 * @param  {string} [opts.fallback]        CSS ritornato se value non utilizzabile (default `'0'`)
 * @param  {string} [opts.zero]            CSS ritornato se è un oggetto con tutti zero (default: '' = no collapse, usa il pattern Apx Bpx Cpx Dpx)
 * @param  {boolean}[opts.acceptPrimitive] Se true (default) un numero scalare diventa `${N}px`.
 *                                          Se false, ogni primitivo (non oggetto) viene trattato come fallback.
 * @returns {string|null} stringa CSS, oppure null se fallback è esplicitamente null
 */
export function radiusToCss(value, opts = {}) {
  const fallback        = opts.fallback === undefined ? '0' : opts.fallback;
  const zero            = opts.zero === undefined ? '' : opts.zero;
  const acceptPrimitive = opts.acceptPrimitive !== false;

  if (value && typeof value === 'object') {
    const tl = parseInt(value.tl) || 0;
    const tr = parseInt(value.tr) || 0;
    const br = parseInt(value.br) || 0;
    const bl = parseInt(value.bl) || 0;
    if (!tl && !tr && !br && !bl && zero !== '') return zero;
    return `${tl}px ${tr}px ${br}px ${bl}px`;
  }

  if (!acceptPrimitive) return fallback;

  const v = parseInt(value);
  if (isNaN(v)) return fallback;
  return `${v}px`;
}
