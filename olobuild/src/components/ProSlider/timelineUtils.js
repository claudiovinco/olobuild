/**
 * Timeline Keyframe Utilities
 * Funzioni pure per interpolazione, easing e generazione CSS @keyframes.
 */

// --- Easing functions (per preview editor) ---
// Approssimazioni delle curve CSS standard via cubic-bezier
const BEZIER_CURVES = {
  'linear':      [0, 0, 1, 1],
  'ease':        [0.25, 0.1, 0.25, 1],
  'ease-in':     [0.42, 0, 1, 1],
  'ease-out':    [0, 0, 0.58, 1],
  'ease-in-out': [0.42, 0, 0.58, 1],
  // Power (GSAP-style)
  'power1.in':    [0.55, 0.085, 0.68, 0.53],
  'power1.out':   [0.25, 0.46, 0.45, 0.94],
  'power1.inOut': [0.455, 0.03, 0.515, 0.955],
  'power2.in':    [0.55, 0.055, 0.675, 0.19],
  'power2.out':   [0.215, 0.61, 0.355, 1],
  'power2.inOut': [0.645, 0.045, 0.355, 1],
  'power3.in':    [0.895, 0.03, 0.685, 0.22],
  'power3.out':   [0.165, 0.84, 0.44, 1],
  'power3.inOut': [0.77, 0, 0.175, 1],
  'power4.in':    [0.755, 0.05, 0.855, 0.06],
  'power4.out':   [0.23, 1, 0.32, 1],
  'power4.inOut': [0.86, 0, 0.07, 1],
  // Sine
  'sine.in':      [0.47, 0, 0.745, 0.715],
  'sine.out':     [0.39, 0.575, 0.565, 1],
  'sine.inOut':   [0.445, 0.05, 0.55, 0.95],
  // Expo
  'expo.in':      [0.95, 0.05, 0.795, 0.035],
  'expo.out':     [0.19, 1, 0.22, 1],
  'expo.inOut':   [1, 0, 0, 1],
  // Circ
  'circ.in':      [0.6, 0.04, 0.98, 0.335],
  'circ.out':     [0.075, 0.82, 0.165, 1],
  'circ.inOut':   [0.785, 0.135, 0.15, 0.86],
  // Back (overshoot)
  'back.in':      [0.6, -0.28, 0.735, 0.045],
  'back.out':     [0.175, 0.885, 0.32, 1.275],
  'back.inOut':   [0.68, -0.55, 0.265, 1.55],
};

// Easing speciali che non si possono esprimere come cubic-bezier
function elasticOut(t) {
  if (t <= 0) return 0;
  if (t >= 1) return 1;
  return Math.pow(2, -10 * t) * Math.sin((t - 0.075) * (2 * Math.PI) / 0.3) + 1;
}

function bounceOut(t) {
  if (t < 1 / 2.75) return 7.5625 * t * t;
  if (t < 2 / 2.75) { t -= 1.5 / 2.75; return 7.5625 * t * t + 0.75; }
  if (t < 2.5 / 2.75) { t -= 2.25 / 2.75; return 7.5625 * t * t + 0.9375; }
  t -= 2.625 / 2.75;
  return 7.5625 * t * t + 0.984375;
}

const SPECIAL_EASING = {
  'elastic.out': elasticOut,
  'bounce.out': bounceOut,
};

/**
 * Evaluate cubic-bezier at parameter t (Newton-Raphson).
 * Returns the y value for a given x progress (0-1).
 */
function cubicBezier(x1, y1, x2, y2, t) {
  // Quick paths
  if (t <= 0) return 0;
  if (t >= 1) return 1;

  // Newton-Raphson to solve for parameter u where bezierX(u) = t
  let u = t;
  for (let i = 0; i < 8; i++) {
    const bx = 3 * (1 - u) * (1 - u) * u * x1 + 3 * (1 - u) * u * u * x2 + u * u * u;
    const dx = 3 * (1 - u) * (1 - u) * x1 + 6 * (1 - u) * u * (x2 - x1) + 3 * u * u * (1 - x2);
    if (Math.abs(dx) < 1e-6) break;
    u -= (bx - t) / dx;
    u = Math.max(0, Math.min(1, u));
  }
  // Evaluate bezierY at u
  return 3 * (1 - u) * (1 - u) * u * y1 + 3 * (1 - u) * u * u * y2 + u * u * u;
}

/**
 * Apply easing function by name. Returns eased progress (0-1).
 */
export function applyEasing(progress, easingName) {
  // Easing speciali (non cubic-bezier)
  if (SPECIAL_EASING[easingName]) {
    return SPECIAL_EASING[easingName](progress);
  }
  const curve = BEZIER_CURVES[easingName] || BEZIER_CURVES['ease'];
  return cubicBezier(curve[0], curve[1], curve[2], curve[3], progress);
}

/**
 * Mappa nome easing → valore CSS.
 * Per cubic-bezier restituisce il valore CSS, per speciali restituisce il nome più vicino.
 */
export function easingToCSS(easingName) {
  // Standard CSS
  if (['linear', 'ease', 'ease-in', 'ease-out', 'ease-in-out'].includes(easingName)) {
    return easingName;
  }
  // Cubic-bezier
  if (BEZIER_CURVES[easingName]) {
    const c = BEZIER_CURVES[easingName];
    return `cubic-bezier(${c[0]}, ${c[1]}, ${c[2]}, ${c[3]})`;
  }
  // Speciali: approssimazione
  if (easingName === 'elastic.out') return 'cubic-bezier(0.175, 0.885, 0.32, 1.275)';
  if (easingName === 'bounce.out') return 'cubic-bezier(0.34, 1.56, 0.64, 1)';
  return 'ease';
}

/** Lista easing raggruppata per la UI */
export const EASING_GROUPS = [
  { label: 'CSS Standard', options: [
    { value: 'linear', label: 'Linear' },
    { value: 'ease', label: 'Ease' },
    { value: 'ease-in', label: 'Ease In' },
    { value: 'ease-out', label: 'Ease Out' },
    { value: 'ease-in-out', label: 'Ease In-Out' },
  ]},
  { label: 'Power', options: [
    { value: 'power1.in', label: 'Power1 In' },
    { value: 'power1.out', label: 'Power1 Out' },
    { value: 'power1.inOut', label: 'Power1 InOut' },
    { value: 'power2.in', label: 'Power2 In' },
    { value: 'power2.out', label: 'Power2 Out' },
    { value: 'power2.inOut', label: 'Power2 InOut' },
    { value: 'power3.in', label: 'Power3 In' },
    { value: 'power3.out', label: 'Power3 Out' },
    { value: 'power3.inOut', label: 'Power3 InOut' },
    { value: 'power4.in', label: 'Power4 In' },
    { value: 'power4.out', label: 'Power4 Out' },
    { value: 'power4.inOut', label: 'Power4 InOut' },
  ]},
  { label: 'Sine', options: [
    { value: 'sine.in', label: 'Sine In' },
    { value: 'sine.out', label: 'Sine Out' },
    { value: 'sine.inOut', label: 'Sine InOut' },
  ]},
  { label: 'Expo', options: [
    { value: 'expo.in', label: 'Expo In' },
    { value: 'expo.out', label: 'Expo Out' },
    { value: 'expo.inOut', label: 'Expo InOut' },
  ]},
  { label: 'Circ', options: [
    { value: 'circ.in', label: 'Circ In' },
    { value: 'circ.out', label: 'Circ Out' },
    { value: 'circ.inOut', label: 'Circ InOut' },
  ]},
  { label: 'Back', options: [
    { value: 'back.in', label: 'Back In' },
    { value: 'back.out', label: 'Back Out' },
    { value: 'back.inOut', label: 'Back InOut' },
  ]},
  { label: 'Speciali', options: [
    { value: 'elastic.out', label: 'Elastic Out' },
    { value: 'bounce.out', label: 'Bounce Out' },
  ]},
];

// --- Proprietà animabili con defaults ---
export const ANIMATABLE_PROPS = {
  x:         { label: 'X (%)',         min: -100, max: 200,  step: 0.1,  default: 0 },
  y:         { label: 'Y (%)',         min: -100, max: 200,  step: 0.1,  default: 0 },
  opacity:   { label: 'Opacità',      min: 0,    max: 100,  step: 1,    default: 100 },
  scale:     { label: 'Scala',         min: 0,    max: 5,    step: 0.01, default: 1 },
  rotation:  { label: 'Rotazione Z°', min: -360, max: 360,  step: 1,    default: 0 },
  rotationX: { label: 'Rotazione X°', min: -180, max: 180,  step: 1,    default: 0 },
  rotationY: { label: 'Rotazione Y°', min: -180, max: 180,  step: 1,    default: 0 },
  skewX:     { label: 'Skew X°',      min: -60,  max: 60,   step: 1,    default: 0 },
  skewY:     { label: 'Skew Y°',      min: -60,  max: 60,   step: 1,    default: 0 },
  blur:      { label: 'Blur (px)',     min: 0,    max: 50,   step: 0.5,  default: 0 },
  originX:   { label: 'Origin X (%)', min: 0,    max: 100,  step: 1,    default: 50 },
  originY:   { label: 'Origin Y (%)', min: 0,    max: 100,  step: 1,    default: 50 },
  // Mask (clip-path inset reveal)
  maskTop:    { label: 'Mask Top (%)',    min: 0, max: 100, step: 1, default: 0 },
  maskRight:  { label: 'Mask Right (%)',  min: 0, max: 100, step: 1, default: 0 },
  maskBottom: { label: 'Mask Bottom (%)', min: 0, max: 100, step: 1, default: 0 },
  maskLeft:   { label: 'Mask Left (%)',   min: 0, max: 100, step: 1, default: 0 },
  // Brightness/Grayscale animabili
  brightness: { label: 'Luminosità (%)', min: 0, max: 200, step: 5, default: 100 },
  grayscale:  { label: 'Scala grigi (%)', min: 0, max: 100, step: 5, default: 0 },
};

/**
 * Crea un ID unico per un keyframe.
 */
export function makeKfId() {
  return 'kf-' + Date.now().toString(36) + Math.random().toString(36).slice(2, 6);
}

/**
 * Crea un keyframe con valori default o forniti.
 */
export function defaultKeyframe(timeMs = 0, propsOverride = {}) {
  const props = {};
  for (const key in ANIMATABLE_PROPS) {
    props[key] = propsOverride[key] ?? ANIMATABLE_PROPS[key].default;
  }
  return {
    id: makeKfId(),
    time: timeMs,
    easing: 'ease',
    props,
  };
}

/**
 * Crea una timeline default per un layer, catturando la posizione/opacità corrente.
 */
export function defaultTimeline(layer, duration = 3000) {
  const startProps = {
    x: layer.x ?? 10,
    y: layer.y ?? 30,
    opacity: layer.opacity ?? 100,
    scale: 1,
    rotation: 0,
    rotationX: 0,
    rotationY: 0,
    skewX: 0,
    skewY: 0,
    blur: 0,
    originX: 50,
    originY: 50,
    maskTop: 0,
    maskRight: 0,
    maskBottom: 0,
    maskLeft: 0,
    brightness: 100,
    grayscale: 0,
  };
  return {
    duration,
    delay: 0,
    loop: false,
    endWithSlide: true,  // WAIT: layer OUT aspetta fine slide (come Slider Revolution)
    tloop: null,         // Timeline loop: { from: kfId, to: kfId, repeat: -1, yoyo: false }
    keyframes: [
      defaultKeyframe(0, startProps),
      defaultKeyframe(duration, { ...startProps }),
    ],
  };
}

/**
 * Trova i due keyframe che circondano un dato tempo.
 * Restituisce { before, after, progress } dove progress è 0-1 tra i due.
 */
export function findSurroundingKeyframes(keyframes, timeMs) {
  if (!keyframes || keyframes.length === 0) return null;

  // Ordina per tempo
  const sorted = [...keyframes].sort((a, b) => a.time - b.time);

  // Prima del primo keyframe
  if (timeMs <= sorted[0].time) {
    return { before: sorted[0], after: sorted[0], progress: 0 };
  }

  // Dopo l'ultimo keyframe
  if (timeMs >= sorted[sorted.length - 1].time) {
    const last = sorted[sorted.length - 1];
    return { before: last, after: last, progress: 0 };
  }

  // Trova la coppia
  for (let i = 0; i < sorted.length - 1; i++) {
    if (timeMs >= sorted[i].time && timeMs <= sorted[i + 1].time) {
      const span = sorted[i + 1].time - sorted[i].time;
      const progress = span > 0 ? (timeMs - sorted[i].time) / span : 0;
      return { before: sorted[i], after: sorted[i + 1], progress };
    }
  }

  return { before: sorted[0], after: sorted[0], progress: 0 };
}

/**
 * Interpola le proprietà di un layer ad un dato tempo nella timeline.
 * Restituisce un oggetto { x, y, opacity, scale, rotation, blur }.
 */
export function interpolateAtTime(timeline, timeMs) {
  if (!timeline || !timeline.keyframes || timeline.keyframes.length === 0) {
    return null;
  }

  const result = findSurroundingKeyframes(timeline.keyframes, timeMs);
  if (!result) return null;

  const { before, after, progress } = result;

  // Se stesso keyframe, restituisci direttamente
  if (before === after || progress === 0) {
    return { ...before.props };
  }

  // Applica easing (l'easing del keyframe "after" controlla la curva verso di esso)
  const easedProgress = applyEasing(progress, after.easing || 'ease');

  // Interpola linearmente ogni proprietà
  const interpolated = {};
  for (const key in ANIMATABLE_PROPS) {
    const a = before.props[key] ?? ANIMATABLE_PROPS[key].default;
    const b = after.props[key] ?? ANIMATABLE_PROPS[key].default;
    interpolated[key] = a + (b - a) * easedProgress;
  }

  return interpolated;
}

/**
 * Genera una stringa CSS @keyframes da una timeline.
 * Il nome sarà mpsKf_{layerId}.
 * Usa transform: translate() per la posizione (x, y come % del container via calc).
 */
export function generateKeyframeCSS(layerId, timeline, canvasW = 1200, canvasH = 600) {
  if (!timeline || !timeline.keyframes || timeline.keyframes.length < 2) {
    return '';
  }

  const sorted = [...timeline.keyframes].sort((a, b) => a.time - b.time);
  const dur = timeline.duration || 3000;
  const name = 'mpsKf_' + layerId.replace(/[^a-zA-Z0-9_-]/g, '');

  let css = `@keyframes ${name} {\n`;

  for (const kf of sorted) {
    const pct = Math.round((kf.time / dur) * 10000) / 100;
    const p = kf.props;
    const x = p.x ?? 0;
    const y = p.y ?? 0;
    const s = p.scale ?? 1;
    const r = p.rotation ?? 0;
    const rx = p.rotationX ?? 0;
    const ry = p.rotationY ?? 0;
    const sx = p.skewX ?? 0;
    const sy = p.skewY ?? 0;
    const o = (p.opacity ?? 100) / 100;
    const b = p.blur ?? 0;
    const ox = p.originX ?? 50;
    const oy = p.originY ?? 50;
    const mt = p.maskTop ?? 0;
    const mr = p.maskRight ?? 0;
    const mb = p.maskBottom ?? 0;
    const ml = p.maskLeft ?? 0;
    const bri = p.brightness ?? 100;
    const gra = p.grayscale ?? 0;

    const translateX = `calc(var(--mps-cw, ${canvasW}px) * ${x / 100})`;
    const translateY = `calc(var(--mps-ch, ${canvasH}px) * ${y / 100})`;

    // Build transform with perspective if 3D rotations are used
    let transform = '';
    if (rx !== 0 || ry !== 0) {
      transform += 'perspective(800px) ';
    }
    transform += `translate(${translateX}, ${translateY}) scale(${s}) rotate(${r}deg)`;
    if (rx !== 0) transform += ` rotateX(${rx}deg)`;
    if (ry !== 0) transform += ` rotateY(${ry}deg)`;
    if (sx !== 0) transform += ` skewX(${sx}deg)`;
    if (sy !== 0) transform += ` skewY(${sy}deg)`;

    // Build filter string
    const filters = [];
    if (b > 0) filters.push(`blur(${b}px)`);
    if (bri !== 100) filters.push(`brightness(${bri}%)`);
    if (gra > 0) filters.push(`grayscale(${gra}%)`);

    css += `  ${pct}% {\n`;
    css += `    transform: ${transform};\n`;
    css += `    transform-origin: ${ox}% ${oy}%;\n`;
    css += `    opacity: ${o};\n`;
    css += filters.length > 0 ? `    filter: ${filters.join(' ')};\n` : `    filter: none;\n`;
    // Clip-path mask (inset reveal)
    if (mt > 0 || mr > 0 || mb > 0 || ml > 0) {
      css += `    clip-path: inset(${mt}% ${mr}% ${mb}% ${ml}%);\n`;
    } else {
      css += `    clip-path: none;\n`;
    }
    if (kf.easing && kf.easing !== 'ease') {
      css += `    animation-timing-function: ${easingToCSS(kf.easing)};\n`;
    }
    css += `  }\n`;
  }

  css += '}\n';
  return css;
}

/**
 * Formatta millisecondi in stringa "SS.s" (es. 1200 → "01.2").
 */
export function formatTime(ms) {
  const s = Math.floor(ms / 1000);
  const d = Math.floor((ms % 1000) / 100);
  return String(s).padStart(2, '0') + '.' + d;
}
