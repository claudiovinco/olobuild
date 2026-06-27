/**
 * Glow / "Bagliori" background — aloni radiali sfocati + grana film opzionale.
 *
 * NUOVO tipo di sfondo per OLObuild, gemello di patternCSS.js.
 * Va usato nello STESSO modo:
 *   • BackgroundControls.vue  → preview del controllo
 *   • useBackgroundStyle.js   → resa sul canvas del builder (buildBgStyle / bgInlineStyle)
 *   • class-frontend-renderer.php → copia equivalente build_glow_css() per il frontend
 *
 * Resa: NON usa filter:blur() — la sfocatura è data dai radial-gradient morbidi
 * (colore → trasparente con falloff ampio), così è performante e stampabile.
 *
 * Colore: rispetta i ruoli globali del cliente. Se il valore è un token
 * (var(--olo-color-primary)) l'alfa si applica con color-mix(); se è hex/rgba
 * si converte in rgba() come fa patternCSS.colorToRgba().
 *
 * ⚠️ Mappatura ruoli: il colore di DEFAULT dei bagliori è il PRIMARIO del cliente
 * (var(--olo-color-primary), seed #e1474f). NON è l'arancio #e8622a del chrome:
 * questo è CONTENUTO di pagina, non UI dell'inspector (regola CHROME vs CONTENUTO).
 */

/** Preset di posizione degli aloni: ogni voce è una lista di hotspot { x%, y%, scale }.
 *  `scale` moltiplica glow_size per dare profondità (aloni davanti più grandi). */
export const glowPresets = [
  { value: 'spread',     label: 'Diffuso' },
  { value: 'top',        label: 'Dall’alto' },
  { value: 'top-left',   label: 'Angolo alto sx' },
  { value: 'top-right',  label: 'Angolo alto dx' },
  { value: 'center',     label: 'Centro' },
  { value: 'corners',    label: 'Agli angoli' },
  { value: 'aurora',     label: 'Aurora (in basso)' },
];

const HOTSPOTS = {
  spread:    [ { x: 12, y: 6, s: 1.15 }, { x: 88, y: 30, s: 0.9 } ],
  top:       [ { x: 50, y: -8, s: 1.25 } ],
  'top-left':[ { x: 8,  y: 4,  s: 1.2 } ],
  'top-right':[ { x: 92, y: 6, s: 1.2 } ],
  center:    [ { x: 50, y: 42, s: 1.1 } ],
  corners:   [ { x: 4, y: 4, s: 0.95 }, { x: 96, y: 96, s: 0.95 } ],
  aurora:    [ { x: 30, y: 108, s: 1.3 }, { x: 74, y: 116, s: 1.0 } ],
};

/** Converte hex|rgba|var(--token) + alfa (0-1) in un colore CSS valido. */
function glowColorToCss(input, alpha) {
  const s = (input || '#e1474f').trim();
  // Token globale del cliente → usa color-mix per applicare l'alfa
  if (s.startsWith('var(') || s.startsWith('color-mix(')) {
    const pct = Math.round(Math.max(0, Math.min(1, alpha)) * 100);
    return `color-mix(in srgb, ${s} ${pct}%, transparent)`;
  }
  // rgba()/rgb()
  const m = s.match(/rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*(?:,\s*([\d.]+))?\s*\)/);
  if (m) {
    const r = +m[1] || 0, g = +m[2] || 0, b = +m[3] || 0;
    const a = m[4] != null ? parseFloat(m[4]) : 1;
    return `rgba(${r}, ${g}, ${b}, ${(a * alpha).toFixed(3)})`;
  }
  // hex (#rgb | #rrggbb)
  const h = s.replace('#', '');
  let r, g, b;
  if (h.length === 3) { r = parseInt(h[0]+h[0],16); g = parseInt(h[1]+h[1],16); b = parseInt(h[2]+h[2],16); }
  else { r = parseInt(h.substring(0,2),16); g = parseInt(h.substring(2,4),16); b = parseInt(h.substring(4,6),16); }
  return `rgba(${isNaN(r)?0:r}, ${isNaN(g)?0:g}, ${isNaN(b)?0:b}, ${alpha.toFixed(3)})`;
}

/** Grana film come layer SVG data-URI (overlay, bassa opacità). */
function grainLayer(opacity = 0.06) {
  const svg =
    `<svg xmlns='http://www.w3.org/2000/svg' width='140' height='140'>` +
    `<filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.8' numOctaves='2' seed='4'/>` +
    `<feColorMatrix values='0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 ${opacity * 16} -${opacity * 6}'/></filter>` +
    `<rect width='100%' height='100%' filter='url(%23n)'/></svg>`;
  return `url("data:image/svg+xml,${encodeURIComponent(svg)}")`;
}

/**
 * Ritorna { backgroundColor, backgroundImage } per lo sfondo "glow".
 * Stessa firma-spirito di getPatternCSS: solo proprietà background-*.
 *
 * @param {object} g  l'oggetto bg (chiavi glow_*)
 *   glow_base      colore di base (hex|rgba|token)            default '#0b0d12'
 *   glow_color     colore alone primario (hex|rgba|token)     default 'var(--olo-color-primary)'
 *   glow_color2    colore alone secondario (vuoto = =primario) default ''
 *   glow_preset    chiave glowPresets                          default 'spread'
 *   glow_intensity 0-100 (alfa max degli aloni)               default 55
 *   glow_size      30-120 (% raggio alone)                    default 70
 *   glow_grain     bool (grana film)                          default true
 */
export function getGlowCSS(g = {}) {
  const base      = g.glow_base || '#0b0d12';
  const c1        = g.glow_color || 'var(--olo-color-primary)';
  const c2        = g.glow_color2 || c1;
  const preset    = g.glow_preset || 'spread';
  const intensity = (g.glow_intensity ?? 55) / 100;       // 0-1
  const sizePct   = (g.glow_size ?? 70);                  // % falloff
  const hotspots  = HOTSPOTS[preset] || HOTSPOTS.spread;

  const layers = hotspots.map((h, i) => {
    const color = i % 2 === 0 ? c1 : c2;
    const stop  = Math.max(20, Math.min(110, Math.round(sizePct * h.s)));
    // alone in primo piano leggermente più intenso
    const a = Math.min(1, intensity * (i === 0 ? 1 : 0.8));
    // tre stop: nucleo brillante → plateau → trasparente, così su fondo scuro il
    // bagliore "accende" invece di spegnersi in un singolo fade.
    const core   = glowColorToCss(color, a);
    const mid    = glowColorToCss(color, a * 0.45);
    const fade   = glowColorToCss(color, 0);
    const midPos = Math.round(stop * 0.42);
    return `radial-gradient(circle at ${h.x}% ${h.y}%, ${core} 0%, ${mid} ${midPos}%, ${fade} ${stop}%)`;
  });

  if (g.glow_grain !== false) layers.push(grainLayer(0.06));

  return {
    backgroundColor: base,
    backgroundImage: layers.join(', '),
    // gli aloni sono ancorati al box; nessun repeat
    backgroundRepeat: 'no-repeat',
    backgroundSize: 'cover',
  };
}
