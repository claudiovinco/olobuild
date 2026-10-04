/**
 * Geometrie — anelli concentrici (uno può essere in evidenza) e una trama di puntini o
 * di griglia che sfuma verso un punto, su un colore di base.
 *
 * Gemello di glowCSS.js / crtCSS.js. Stesso uso:
 *   • BackgroundControls.vue  → mattonella e anteprima del controllo
 *   • useBackgroundStyle.js   → resa nel builder
 *   • class-css-builder.php    → copia equivalente build_geo_css() (stesse stringhe)
 *
 * Nato per gli sfondi che i temi portavano come SVG statici (il blu con anelli e
 * puntini di olotutor.com): lì colore, anelli e trama erano disegnati nel file e
 * nessun controllo li cambiava. Qui ogni parte ha il suo e segue i colori del tema.
 *
 * Tutto CSS (nessun layer DOM). Ordine dei layer, dall'alto: anello in evidenza, anelli,
 * velo della dissolvenza (il colore di base che copre la trama lontano dal fuoco), trama;
 * sotto, il colore di base.
 *
 * Chiavi: geo_base, geo_rings (0-8), geo_rings_x/y (%), geo_rings_r (px, il più
 * piccolo), geo_rings_step (px), geo_rings_width (px), geo_rings_color, geo_rings_opacity
 * (%), geo_accent (0 = nessuno, n = l'n-esimo anello), geo_accent_color,
 * geo_accent_opacity (%), geo_texture (none|dots|grid), geo_tex_gap (px), geo_tex_size
 * (px: diametro dei puntini, spessore delle linee), geo_tex_color, geo_tex_opacity (%),
 * geo_fade (bool), geo_fade_x/y (%), geo_fade_r (%).
 */

export const GEO_DEFAULTS = {
  geo_base: 'var(--olo-color-dark, #0f172a)',
  geo_rings: 3,
  geo_rings_x: 90,
  geo_rings_y: 18,
  geo_rings_r: 120,
  geo_rings_step: 110,
  geo_rings_width: 1,
  geo_rings_color: '#ffffff',
  geo_rings_opacity: 6,
  geo_accent: 2,
  geo_accent_color: 'var(--olo-color-primary)',
  geo_accent_opacity: 18,
  geo_texture: 'dots',
  geo_tex_gap: 30,
  geo_tex_size: 3,
  geo_tex_color: '#ffffff',
  geo_tex_opacity: 12,
  geo_fade: true,
  geo_fade_x: 16,
  geo_fade_y: 88,
  geo_fade_r: 55,
};

export const geoTextures = [
  { value: 'none', label: 'Nessuna' },
  { value: 'dots', label: 'Puntini' },
  { value: 'grid', label: 'Griglia' },
];

const clamp = (v, lo, hi) => Math.max(lo, Math.min(hi, v));
const num = (v, d, lo, hi) => { const n = parseFloat(v); return clamp(Number.isNaN(n) ? d : n, lo, hi); };
const r1 = (v) => Math.round(v * 10) / 10;
const val = (bg, k) => (bg[k] === undefined || bg[k] === null || bg[k] === '' ? GEO_DEFAULTS[k] : bg[k]);

/** hex|rgba|var(--token) + alfa (0-1) → colore CSS. Speculare a glow_color_to_css() (PHP). */
function geoColor(input, alpha) {
  const s = String(input || '#ffffff').trim();
  const a = clamp(alpha, 0, 1);
  if (s.startsWith('var(') || s.startsWith('color-mix(')) {
    // Un token senza riserva ripiega sul primario, come con_riserva() del PHP.
    const t = s.match(/^var\(\s*(--[A-Za-z0-9_-]+)\s*\)$/);
    const c = t ? `var(${t[1]}, var(--olo-color-primary))` : s;
    return `color-mix(in srgb, ${c} ${Math.round(a * 100)}%, transparent)`;
  }
  const m = s.match(/rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*(?:,\s*([\d.]+))?\s*\)/);
  if (m) {
    const ca = m[4] != null ? parseFloat(m[4]) : 1;
    return `rgba(${+m[1] || 0}, ${+m[2] || 0}, ${+m[3] || 0}, ${(ca * a).toFixed(3)})`;
  }
  const h = s.replace('#', '');
  let r, g, b;
  if (h.length === 3) { r = parseInt(h[0] + h[0], 16); g = parseInt(h[1] + h[1], 16); b = parseInt(h[2] + h[2], 16); }
  else { r = parseInt(h.substring(0, 2), 16); g = parseInt(h.substring(2, 4), 16); b = parseInt(h.substring(4, 6), 16); }
  return `rgba(${r || 0}, ${g || 0}, ${b || 0}, ${a.toFixed(3)})`;
}

/** Le tappe di un anello di raggio R e spessore w (bordi sfumati di mezzo pixel). */
function anello(R, w, col) {
  return `transparent ${r1(R - w - 0.5)}px, ${col} ${r1(R - w)}px ${R}px, transparent ${r1(R + 0.5)}px`;
}

/** Ritorna le proprietà background-* (longhand) dello sfondo Geometrie. */
export function getGeoCSS(bg = {}) {
  const base = val(bg, 'geo_base');
  const n    = Math.round(num(val(bg, 'geo_rings'), 3, 0, 8));
  const x    = num(val(bg, 'geo_rings_x'), 90, -50, 150);
  const y    = num(val(bg, 'geo_rings_y'), 18, -50, 150);
  const w    = Math.round(num(val(bg, 'geo_rings_width'), 1, 1, 12));
  const r    = Math.max(w + 1, Math.round(num(val(bg, 'geo_rings_r'), 120, 4, 2000)));
  const step = Math.round(num(val(bg, 'geo_rings_step'), 110, 4, 1000));
  const ringCol = geoColor(val(bg, 'geo_rings_color'), num(val(bg, 'geo_rings_opacity'), 6, 0, 100) / 100);
  const acc  = Math.round(num(val(bg, 'geo_accent'), 2, 0, 8));
  const tex  = ['dots', 'grid'].includes(val(bg, 'geo_texture')) ? val(bg, 'geo_texture') : 'none';

  const imgs = [], reps = [], sizes = [], poss = [];
  const strato = (img, rep = 'no-repeat', size = '100% 100%') => { imgs.push(img); reps.push(rep); sizes.push(size); poss.push('0 0'); };

  if (n > 0 && acc > 0 && acc <= n) {
    const col = geoColor(val(bg, 'geo_accent_color'), num(val(bg, 'geo_accent_opacity'), 18, 0, 100) / 100);
    strato(`radial-gradient(circle at ${x}% ${y}%, ${anello(r + (acc - 1) * step, w, col)})`);
  }
  if (n > 0) {
    const tappe = ['transparent 0'];
    for (let i = 0; i < n; i++) tappe.push(anello(r + i * step, w, ringCol));
    strato(`radial-gradient(circle at ${x}% ${y}%, ${tappe.join(', ')})`);
  }
  if (tex !== 'none') {
    const gap  = Math.round(num(val(bg, 'geo_tex_gap'), 30, 4, 400));
    const size = num(val(bg, 'geo_tex_size'), 3, 1, 40);
    const col  = geoColor(val(bg, 'geo_tex_color'), num(val(bg, 'geo_tex_opacity'), 12, 0, 100) / 100);
    if (val(bg, 'geo_fade') !== false) {
      const fx = num(val(bg, 'geo_fade_x'), 16, -50, 150), fy = num(val(bg, 'geo_fade_y'), 88, -50, 150);
      const fr = num(val(bg, 'geo_fade_r'), 55, 5, 200);
      // Ellisse in % di larghezza e altezza (come un radialGradient SVG sul riquadro).
      strato(`radial-gradient(${fr}% ${fr}% at ${fx}% ${fy}%, transparent 0, ${base} 100%)`);
    }
    if (tex === 'dots') {
      strato(`radial-gradient(circle, ${col} ${r1(size / 2)}px, transparent ${r1(size / 2 + 0.6)}px)`, 'repeat', `${gap}px ${gap}px`);
    } else {
      const t = Math.round(size) > 0 ? Math.round(size) : 1;
      strato(`linear-gradient(${col} ${t}px, transparent ${t}px)`, 'repeat', `${gap}px ${gap}px`);
      strato(`linear-gradient(90deg, ${col} ${t}px, transparent ${t}px)`, 'repeat', `${gap}px ${gap}px`);
    }
  }

  const out = { backgroundColor: base };
  if (imgs.length) {
    out.backgroundImage = imgs.join(', ');
    out.backgroundRepeat = reps.join(', ');
    out.backgroundSize = sizes.join(', ');
    out.backgroundPosition = poss.join(', ');
  }
  return out;
}
