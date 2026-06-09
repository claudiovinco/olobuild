/**
 * colorHarmony.js — motore di teoria del colore per la Palette di Olobuild.
 *
 * Conversioni hex/rgb/hsl, contrasto WCAG e generazione di palette armoniche
 * (complementare, analoga, triade, split, tetrade, monocromatica, due-colori)
 * a partire da uno o due colori seed. Le funzioni di assegnazione mappano i
 * colori generati sui ruoli reali di olo_styles.colors (primary/secondary/link
 * + i rispettivi _contrast), senza toccare gli stati semantici
 * (success/warning/danger), che restano fissi.
 */

// ─────────────────────────────────────────────────────────────
// Conversioni
// ─────────────────────────────────────────────────────────────
export function hexToRgb(hex) {
  let h = String(hex || '').replace('#', '').trim();
  if (h.length === 3) h = h.split('').map((c) => c + c).join('');
  if (h.length !== 6 || /[^0-9a-fA-F]/.test(h)) return { r: 0, g: 0, b: 0 };
  const n = parseInt(h, 16);
  return { r: (n >> 16) & 255, g: (n >> 8) & 255, b: n & 255 };
}

export function rgbToHex(r, g, b) {
  const c = (v) => Math.max(0, Math.min(255, Math.round(v))).toString(16).padStart(2, '0');
  return ('#' + c(r) + c(g) + c(b)).toUpperCase();
}

export function hexToHsl(hex) {
  const { r, g, b } = hexToRgb(hex);
  const rn = r / 255, gn = g / 255, bn = b / 255;
  const max = Math.max(rn, gn, bn), min = Math.min(rn, gn, bn);
  let h = 0, s = 0;
  const l = (max + min) / 2;
  const d = max - min;
  if (d !== 0) {
    s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
    switch (max) {
      case rn: h = (gn - bn) / d + (gn < bn ? 6 : 0); break;
      case gn: h = (bn - rn) / d + 2; break;
      default: h = (rn - gn) / d + 4; break;
    }
    h *= 60;
  }
  return { h: Math.round(h), s: Math.round(s * 100), l: Math.round(l * 100) };
}

export function hslToHex(h, s, l) {
  h = ((h % 360) + 360) % 360;
  s = Math.max(0, Math.min(100, s)) / 100;
  l = Math.max(0, Math.min(100, l)) / 100;
  const c = (1 - Math.abs(2 * l - 1)) * s;
  const x = c * (1 - Math.abs(((h / 60) % 2) - 1));
  const m = l - c / 2;
  let r = 0, g = 0, b = 0;
  if (h < 60) { r = c; g = x; }
  else if (h < 120) { r = x; g = c; }
  else if (h < 180) { g = c; b = x; }
  else if (h < 240) { g = x; b = c; }
  else if (h < 300) { r = x; b = c; }
  else { r = c; b = x; }
  return rgbToHex((r + m) * 255, (g + m) * 255, (b + m) * 255);
}

export function isValidHex(hex) {
  const h = String(hex || '').replace('#', '').trim();
  return (h.length === 3 || h.length === 6) && !/[^0-9a-fA-F]/.test(h);
}

// ─────────────────────────────────────────────────────────────
// Helper su tinta/saturazione/luminosità
// ─────────────────────────────────────────────────────────────
export function rotateHue(hex, deg) {
  const { h, s, l } = hexToHsl(hex);
  return hslToHex(h + deg, s, l);
}

export function adjust(hex, { ds = 0, dl = 0 } = {}) {
  const { h, s, l } = hexToHsl(hex);
  return hslToHex(h, s + ds, l + dl);
}

// ─────────────────────────────────────────────────────────────
// Contrasto WCAG
// ─────────────────────────────────────────────────────────────
function luminance(hex) {
  const { r, g, b } = hexToRgb(hex);
  const ch = [r, g, b].map((v) => {
    const sv = v / 255;
    return sv <= 0.03928 ? sv / 12.92 : Math.pow((sv + 0.055) / 1.055, 2.4);
  });
  return 0.2126 * ch[0] + 0.7152 * ch[1] + 0.0722 * ch[2];
}

export function contrastRatio(a, b) {
  const la = luminance(a), lb = luminance(b);
  const hi = Math.max(la, lb), lo = Math.min(la, lb);
  return Math.round(((hi + 0.05) / (lo + 0.05)) * 100) / 100;
}

/** Testo leggibile (bianco o quasi-nero) sul colore dato. */
export function readableText(hex) {
  return contrastRatio(hex, '#FFFFFF') >= contrastRatio(hex, '#111827') ? '#FFFFFF' : '#111827';
}

// ─────────────────────────────────────────────────────────────
// Regole armoniche
// ─────────────────────────────────────────────────────────────
export const HARMONY_RULES = [
  { id: 'complementary', label: 'Complementare', desc: 'Tinta opposta (+180°)',          seeds: 1 },
  { id: 'analogous',     label: 'Analoga',       desc: 'Tinte adiacenti (±30°)',          seeds: 1 },
  { id: 'triadic',       label: 'Triade',        desc: 'Tre tinte equidistanti (120°)',   seeds: 1 },
  { id: 'split',         label: 'Split-compl.',  desc: 'Complementare diviso (150°/210°)', seeds: 1 },
  { id: 'tetradic',      label: 'Tetrade',       desc: 'Due coppie complementari',        seeds: 1 },
  { id: 'monochromatic', label: 'Monocromatica', desc: 'Una tinta, luminosità diverse',   seeds: 1 },
  { id: 'duotone',       label: 'Due colori',    desc: 'I tuoi due colori + accenti',     seeds: 2 },
];

/**
 * Restituisce l'array di hex (seed incluso) secondo la regola scelta.
 * @param {string} seed   hex principale
 * @param {string} rule   id regola
 * @param {string} [seed2] hex secondario (solo per 'duotone')
 */
export function harmonize(seed, rule, seed2) {
  if (!isValidHex(seed)) return [seed];
  switch (rule) {
    case 'complementary':
      return [seed, rotateHue(seed, 180)];
    case 'analogous':
      return [rotateHue(seed, -30), seed, rotateHue(seed, 30)];
    case 'triadic':
      return [seed, rotateHue(seed, 120), rotateHue(seed, 240)];
    case 'split':
      return [seed, rotateHue(seed, 150), rotateHue(seed, 210)];
    case 'tetradic':
      return [seed, rotateHue(seed, 60), rotateHue(seed, 180), rotateHue(seed, 240)];
    case 'monochromatic': {
      const { h, s } = hexToHsl(seed);
      return [hslToHex(h, s, 86), hslToHex(h, s, 66), seed, hslToHex(h, Math.min(100, s + 6), 34)];
    }
    case 'duotone':
      return isValidHex(seed2)
        ? [seed, seed2, adjust(seed, { dl: 18 }), adjust(seed2, { dl: -14 })]
        : [seed, rotateHue(seed, 180)];
    default:
      return [seed];
  }
}

/**
 * Neutri coordinati: una scala di grigi leggermente "tinti" verso la tinta del
 * colore base, per testo/sfondo/superfici/bordi che dialoghino col brand invece
 * di essere grigi neutri-puri. Saturazione tenuta bassa per restare neutrale.
 */
export function neutralsFromSeed(seed) {
  const { h } = hexToHsl(seed);
  return {
    background: hslToHex(h, 8, 98),
    muted:      hslToHex(h, 10, 95),
    border:     hslToHex(h, 12, 88),
    text_muted: hslToHex(h, 10, 50),
    text:       hslToHex(h, 14, 18),
  };
}

/**
 * Mappa i colori armonici sui ruoli reali di olo_styles.colors.
 * Tocca solo primary/secondary/link (+ _contrast auto). Gli stati restano fissi.
 */
export function paletteToRoles(seed, rule, seed2) {
  const harmony = harmonize(seed, rule, seed2);
  const primary = seed;
  let secondary;
  if (rule === 'duotone' && isValidHex(seed2)) {
    secondary = seed2;
  } else {
    // primo colore della regola diverso dal seed
    secondary = harmony.find((c) => c.toUpperCase() !== seed.toUpperCase()) || rotateHue(seed, 180);
  }
  return {
    primary,
    primary_contrast: readableText(primary),
    secondary,
    secondary_contrast: readableText(secondary),
    link: primary,
  };
}
