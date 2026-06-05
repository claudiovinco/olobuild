/**
 * Mesh / "Aurora" background — N blob radiali sfumati su un colore base.
 *
 * Gemello di glowCSS.js / patternCSS.js. Va usato nello STESSO modo:
 *   • BackgroundControls.vue  → preview del controllo
 *   • useBackgroundStyle.js   → resa sul canvas del builder
 *   • class-css-builder.php    → copia equivalente build_mesh_css() per il frontend
 *
 * Resa: NON usa filter:blur() — la morbidezza è data dal falloff dei radial-gradient
 * (colore → trasparente con stop ampio). Colori token-first (ruoli cliente): se il
 * valore è var(--olo-color-*) l'alfa si applica con color-mix(); se è hex/rgba si
 * converte in rgba(), come in glowCSS.colorToCss.
 *
 * Controllo dati (additivo, retrocompatibile):
 *   mesh_colors  array di colori (palette dei blob). Fallback: [mesh_c1, mesh_c2, mesh_c3].
 *   mesh_base    colore di fondo.
 *   mesh_count   1–6, quanti blob (ciclano la palette). Default = n° colori.
 *   mesh_softness 0–100, morbidezza (stop del falloff). Default 70.
 *   mesh_intensity 0–100, opacità dei blob. Default 100.
 *   mesh_preset  disposizione dei blob (spread|boreale|corners|center|top). Default spread.
 *   mesh_animate bool · mesh_speed secondi (drift lento via @keyframes olo-mesh-drift).
 *
 * I template legacy (solo mesh_c1/c2/c3 + base) restano validi: stesse 3 posizioni
 * "spread", solo falloff leggermente più morbido.
 */

export const meshPresets = [
  { value: 'spread',  label: 'Diffusa' },
  { value: 'boreale', label: 'Boreale' },
  { value: 'corners', label: 'Agli angoli' },
  { value: 'center',  label: 'Centrale' },
  { value: 'top',     label: 'Dall’alto' },
];

// Posizioni [x%, y%] dei blob per disposizione. Le prime 3 di "spread" coincidono
// con la resa storica (20/25, 80/30, 50/90) → nessuna regressione per i legacy.
const MESH_POSITIONS = {
  spread:  [ [20, 25], [80, 30], [50, 90], [85, 78], [15, 75], [50, 12] ],
  boreale: [ [22, 94], [50, 88], [78, 96], [35, 78], [66, 82], [50, 66] ],
  corners: [ [10, 12], [90, 14], [12, 88], [88, 86], [50, 50], [50, 8] ],
  center:  [ [50, 46], [34, 56], [66, 56], [50, 30], [42, 42], [58, 42] ],
  top:     [ [20, 8], [50, 3], [80, 8], [35, 22], [66, 18], [50, 32] ],
};

/** Converte hex|rgba|var(--token) + alfa (0-1) in un colore CSS valido.
 *  Speculare a glow_color_to_css() (PHP) e glowColorToCss() (glowCSS.js). */
function meshColorToCss(input, alpha) {
  const s = (input || 'var(--olo-color-primary)').trim();
  const a = Math.max(0, Math.min(1, alpha));
  if (s.startsWith('var(') || s.startsWith('color-mix(')) {
    const pct = Math.round(a * 100);
    return `color-mix(in srgb, ${s} ${pct}%, transparent)`;
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
  return `rgba(${isNaN(r) ? 0 : r}, ${isNaN(g) ? 0 : g}, ${isNaN(b) ? 0 : b}, ${a.toFixed(3)})`;
}

/** Palette colori effettiva: mesh_colors[] oppure i legacy mesh_c1/c2/c3, con fallback ai ruoli. */
export function getMeshColors(bg = {}) {
  if (Array.isArray(bg.mesh_colors)) {
    const list = bg.mesh_colors.filter((c) => c !== '' && c != null);
    if (list.length) return list;
  }
  const legacy = [bg.mesh_c1, bg.mesh_c2, bg.mesh_c3].filter((c) => c !== '' && c != null);
  if (legacy.length) return legacy;
  return ['var(--olo-color-primary)', 'var(--olo-color-secondary)', 'var(--olo-color-accent)'];
}

/**
 * Ritorna { backgroundColor, backgroundImage, backgroundRepeat, backgroundSize[, animation] }
 * per lo sfondo "Aurora". Stessa firma-spirito di getGlowCSS/getPatternCSS.
 */
export function getMeshCSS(bg = {}) {
  const colors = getMeshColors(bg);
  const base = bg.mesh_base || 'var(--olo-color-background, #0b0a0d)';
  const positions = MESH_POSITIONS[bg.mesh_preset] || MESH_POSITIONS.spread;
  const count = Math.max(1, Math.min(6, parseInt(bg.mesh_count ?? colors.length) || colors.length));
  const softness = Math.max(0, Math.min(100, bg.mesh_softness ?? 70));
  const intensity = Math.max(0, Math.min(100, bg.mesh_intensity ?? 100)) / 100;
  const stop = Math.round(40 + softness * 0.5);   // 40..90
  const midpos = Math.round(stop * 0.45);

  const layers = [];
  for (let i = 0; i < count; i++) {
    const [x, y] = positions[i % positions.length];
    const col = colors[i % colors.length];
    const core = meshColorToCss(col, intensity);
    const mid = meshColorToCss(col, intensity * 0.5);
    const fade = meshColorToCss(col, 0);
    layers.push(`radial-gradient(circle at ${x}% ${y}%, ${core} 0%, ${mid} ${midpos}%, ${fade} ${stop}%)`);
  }

  const out = {
    backgroundColor: base,
    backgroundImage: layers.join(', '),
    backgroundRepeat: 'no-repeat',
  };
  if (bg.mesh_animate) {
    const speed = Math.max(4, Math.min(60, parseInt(bg.mesh_speed ?? 18) || 18));
    out.backgroundSize = '160% 160%';
    out.animation = `olo-mesh-drift ${speed}s ease-in-out infinite alternate`;
  } else {
    out.backgroundSize = 'cover';
  }
  return out;
}
