/**
 * CRT background — scanline + vignetta (+ curvatura/flicker) su un colore base.
 *
 * Gemello di glowCSS.js / meshCSS.js / patternCSS.js. Stesso uso:
 *   • BackgroundControls.vue  → preview del controllo
 *   • useBackgroundStyle.js   → resa sul canvas del builder
 *   • class-css-builder.php    → copia equivalente build_crt_css() per il frontend
 *
 * Tutto pure-CSS (nessun layer DOM): scanline = repeating-linear-gradient, vignetta
 * e curvatura = radial-gradient, flicker = animazione `background-position` (content-safe,
 * come l'animazione dei Bagliori). La curvatura è un'approssimazione "bombé" via
 * scurimento radiale agli angoli (la barrel-distortion reale richiederebbe un SVG filter).
 *
 * Chiavi (additive, retrocompatibili — default = resa storica):
 *   crt_base, crt_scanline_opacity (0-100), crt_scanline_gap (2-12 px), crt_vignette (0-100),
 *   crt_line_color (default #fff), crt_model (classic|vertical|aperture),
 *   crt_curvature (0-100), crt_flicker (bool), crt_flicker_speed (2-12 s).
 */

export const crtModels = [
  { value: 'classic',  label: 'Scanline classica' },
  { value: 'vertical', label: 'Verticale' },
  { value: 'aperture', label: 'Aperture grille' },
];

const clamp = (v, lo, hi) => Math.max(lo, Math.min(hi, v));

/** Converte hex|rgba|var(--token) + alfa (0-1) in colore CSS valido.
 *  Speculare a glow_color_to_css() (PHP) — token → color-mix, hex/rgba → rgba(). */
function crtColorToCss(input, alpha) {
  const s = (input || '#ffffff').trim();
  const a = clamp(alpha, 0, 1);
  if (s.startsWith('var(') || s.startsWith('color-mix(')) {
    return `color-mix(in srgb, ${s} ${Math.round(a * 100)}%, transparent)`;
  }
  const m = s.match(/rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*(?:,\s*([\d.]+))?\s*\)/);
  if (m) {
    const r = +m[1] || 0, g = +m[2] || 0, b = +m[3] || 0;
    const ca = m[4] != null ? parseFloat(m[4]) : 1;
    return `rgba(${r}, ${g}, ${b}, ${(ca * a).toFixed(3)})`;
  }
  const h = s.replace('#', '');
  let r, g, b;
  if (h.length === 3) { r = parseInt(h[0] + h[0], 16); g = parseInt(h[1] + h[1], 16); b = parseInt(h[2] + h[2], 16); }
  else { r = parseInt(h.substring(0, 2), 16); g = parseInt(h.substring(2, 4), 16); b = parseInt(h.substring(4, 6), 16); }
  return `rgba(${isNaN(r) ? 255 : r}, ${isNaN(g) ? 255 : g}, ${isNaN(b) ? 255 : b}, ${a.toFixed(3)})`;
}

/** Ritorna le proprietà background-* (longhand) per lo sfondo CRT.
 *  Layer (ordine): [barra flicker], scanline, [curvatura], vignetta; base = colore.
 *  Flicker = barra luminosa che spazza dall'alto al basso (background-position),
 *  content-safe e ben visibile. La curvatura è uno scurimento "bombé" agli angoli. */
export function getCrtCSS(bg = {}) {
  const base    = bg.crt_base || 'var(--olo-color-background, #0b0a0d)';
  const gap     = clamp(parseInt(bg.crt_scanline_gap ?? 3) || 3, 2, 12);
  const scanPct = clamp(parseInt(bg.crt_scanline_opacity ?? 50), 0, 100);
  const scanA   = (scanPct / 100) * 0.5;
  const lineCol = bg.crt_line_color || '#ffffff';
  const line    = crtColorToCss(lineCol, scanA);
  const model   = bg.crt_model || 'classic';
  const vigPct  = clamp(parseInt(bg.crt_vignette ?? 55), 0, 100);
  const vigA    = (vigPct / 100).toFixed(3);
  const vigStop = 70 - Math.round((vigPct / 100) * 30);
  const curv    = clamp(parseInt(bg.crt_curvature ?? 0), 0, 100);

  const imgs = [], reps = [], sizes = [], poss = [];

  // scanline
  if (model === 'vertical') {
    imgs.push(`repeating-linear-gradient(90deg, ${line} 0 1px, transparent 1px ${gap}px)`); reps.push('repeat'); sizes.push('auto'); poss.push('0 0');
  } else if (model === 'aperture') {
    imgs.push(`repeating-linear-gradient(90deg, ${line} 0 1px, transparent 1px ${gap}px)`); reps.push('repeat'); sizes.push('auto'); poss.push('0 0');
    imgs.push(`repeating-linear-gradient(0deg, ${line} 0 1px, transparent 1px ${gap}px)`); reps.push('repeat'); sizes.push('auto'); poss.push('0 0');
  } else {
    imgs.push(`repeating-linear-gradient(0deg, ${line} 0 1px, transparent 1px ${gap}px)`); reps.push('repeat'); sizes.push('auto'); poss.push('0 0');
  }

  // curvatura — scurimento curvo agli angoli (più marcato della vignetta)
  if (curv > 0) {
    const cStop = Math.max(38, 100 - Math.round(curv * 0.6));
    const cA = (0.25 + curv / 100 * 0.65).toFixed(3);
    imgs.push(`radial-gradient(100% 100% at 50% 50%, transparent ${cStop}%, rgba(0,0,0,${cA}) 100%)`); reps.push('no-repeat'); sizes.push('cover'); poss.push('center');
  }

  // vignetta
  imgs.push(`radial-gradient(120% 120% at 50% 40%, transparent ${vigStop}%, rgba(5,3,12,${vigA}))`); reps.push('no-repeat'); sizes.push('cover'); poss.push('center');

  const out = {};
  if (bg.crt_flicker) {
    // barra luminosa (layer 1) animata top→bottom dal keyframe olo-crt-flicker
    const barCol = crtColorToCss(lineCol, 0.22);
    imgs.unshift(`linear-gradient(to bottom, transparent, ${barCol}, transparent)`); reps.unshift('no-repeat'); sizes.unshift('100% 18%'); poss.unshift('0 -25%');
    const sp = clamp(parseInt(bg.crt_flicker_speed ?? 6) || 6, 2, 12);
    out.animation = `olo-crt-flicker ${sp}s linear infinite`;
  }

  out.backgroundColor = base;
  out.backgroundImage = imgs.join(', ');
  out.backgroundRepeat = reps.join(', ');
  out.backgroundSize = sizes.join(', ');
  out.backgroundPosition = poss.join(', ');
  return out;
}
