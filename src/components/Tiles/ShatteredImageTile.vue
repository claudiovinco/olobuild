<template>
  <div style="padding:4px">
    <div v-if="!s.image_url" style="display:flex;align-items:center;justify-content:center;height:200px;background:#f3f4f6;border-radius:8px;color:#9ca3af;font-size:13px">
      {{ t('Seleziona un\'immagine') }}
    </div>
    <template v-else>
      <component v-if="s.kenburns" :is="'style'" v-text="dynamicKeyframes"></component>
      <div :style="containerStyle">
      <!-- Outer: maschera statica (clip-path fermo) -->
      <div
        v-for="(frag, i) in fragments"
        :key="i"
        :style="maskStyle(frag)"
      >
        <!-- Inner: immagine animata (Ken Burns si muove dentro la maschera) -->
        <div :style="imageStyle(i)"></div>
        <div v-if="s.overlay" :style="overlayStyle"></div>
      </div>
      <!-- Scroll effects badge (builder only) -->
      <div v-if="s.scroll_parallax || s.scroll_reveal" style="position:absolute;bottom:6px;right:8px;display:flex;gap:4px;z-index:2">
        <span v-if="s.scroll_parallax" style="background:rgba(99,102,241,.85);color:#fff;font-size:10px;padding:2px 6px;border-radius:4px;line-height:1.3">{{ t('Parallax') }}</span>
        <span v-if="s.scroll_reveal" style="background:rgba(99,102,241,.85);color:#fff;font-size:10px;padding:2px 6px;border-radius:4px;line-height:1.3">{{ t('Reveal') }}</span>
      </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
import { getShadowValue } from '@/composables/useShadowMap';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => props.settings);

/* ─── Preset mask definitions ─── */
/* Each fragment is an array of [x%, y%] vertices.
   Gap is applied by shrinking each polygon inward. */

/* Tutti i preset tassellano perfettamente il rettangolo [0,0]-[100,100].
   Ogni bordo interno è condiviso da esattamente 2 poligoni adiacenti,
   quindi con gap=0 non ci sono mai aree scoperte. */
const PRESETS = {
  /* 5 frammenti speculari — 2 sx, centro grande, 2 dx */
  shards: [
    [[0,0],[22,0],[18,45],[0,40]],
    [[0,40],[18,45],[20,100],[0,100]],
    [[22,0],[78,0],[82,45],[80,100],[20,100],[18,45]],
    [[78,0],[100,0],[100,40],[82,45]],
    [[82,45],[100,40],[100,100],[80,100]],
  ],
  /* 8 frammenti — impatto al centro (50,48), crepe radiali in tutte le direzioni */
  radial_center: [
    [[50,48],[20,0],[75,0]],
    [[50,48],[75,0],[100,0],[100,30]],
    [[50,48],[100,30],[100,72]],
    [[50,48],[100,72],[100,100],[70,100]],
    [[50,48],[70,100],[25,100]],
    [[50,48],[25,100],[0,100],[0,68]],
    [[50,48],[0,68],[0,25]],
    [[50,48],[0,25],[0,0],[20,0]],
  ],
  /* 6 frammenti — impatto a sinistra (8,48), crepe radiali verso destra */
  shards_left: [
    [[8,48],[0,12],[0,0],[40,0]],
    [[8,48],[40,0],[100,0],[100,35]],
    [[8,48],[100,35],[100,65]],
    [[8,48],[100,65],[100,100],[45,100]],
    [[8,48],[45,100],[0,100],[0,82]],
    [[8,48],[0,82],[0,12]],
  ],
  /* 6 frammenti — impatto a destra (92,48), specchio di shards_left */
  shards_right: [
    [[92,48],[100,12],[100,0],[60,0]],
    [[92,48],[60,0],[0,0],[0,35]],
    [[92,48],[0,35],[0,65]],
    [[92,48],[0,65],[0,100],[55,100]],
    [[92,48],[55,100],[100,100],[100,82]],
    [[92,48],[100,82],[100,12]],
  ],
  /* 6 frammenti — impatto dall'alto (50,8), crepe verso il basso */
  shards_top: [
    [[50,8],[88,0],[100,0],[100,40]],
    [[50,8],[100,40],[100,100],[75,100]],
    [[50,8],[75,100],[25,100]],
    [[50,8],[25,100],[0,100],[0,40]],
    [[50,8],[0,40],[0,0],[12,0]],
    [[50,8],[12,0],[88,0]],
  ],
  /* 6 frammenti — impatto dal basso (50,92), crepe verso l'alto */
  shards_bottom: [
    [[50,92],[88,100],[100,100],[100,60]],
    [[50,92],[100,60],[100,0],[75,0]],
    [[50,92],[75,0],[25,0]],
    [[50,92],[25,0],[0,0],[0,60]],
    [[50,92],[0,60],[0,100],[12,100]],
    [[50,92],[12,100],[88,100]],
  ],
  /* ─── Colonne ─── */
  columns: [
    [[0,0],[33.33,0],[33.33,100],[0,100]],
    [[33.33,0],[66.67,0],[66.67,100],[33.33,100]],
    [[66.67,0],[100,0],[100,100],[66.67,100]],
  ],
  columns_4: [
    [[0,0],[25,0],[25,100],[0,100]],
    [[25,0],[50,0],[50,100],[25,100]],
    [[50,0],[75,0],[75,100],[50,100]],
    [[75,0],[100,0],[100,100],[75,100]],
  ],
  columns_5: [
    [[0,0],[20,0],[20,100],[0,100]],
    [[20,0],[40,0],[40,100],[20,100]],
    [[40,0],[60,0],[60,100],[40,100]],
    [[60,0],[80,0],[80,100],[60,100]],
    [[80,0],[100,0],[100,100],[80,100]],
  ],
  columns_6: [
    [[0,0],[16.67,0],[16.67,100],[0,100]],
    [[16.67,0],[33.33,0],[33.33,100],[16.67,100]],
    [[33.33,0],[50,0],[50,100],[33.33,100]],
    [[50,0],[66.67,0],[66.67,100],[50,100]],
    [[66.67,0],[83.33,0],[83.33,100],[66.67,100]],
    [[83.33,0],[100,0],[100,100],[83.33,100]],
  ],
  /* ─── Mosaico ─── */
  mosaic: [
    [[0,0],[33.33,0],[33.33,55],[0,55]],
    [[33.33,0],[66.67,0],[66.67,55],[33.33,55]],
    [[66.67,0],[100,0],[100,55],[66.67,55]],
    [[0,55],[50,55],[50,100],[0,100]],
    [[50,55],[100,55],[100,100],[50,100]],
  ],
  mosaic_4: [
    [[0,0],[50,0],[50,50],[0,50]],
    [[50,0],[100,0],[100,50],[50,50]],
    [[0,50],[50,50],[50,100],[0,100]],
    [[50,50],[100,50],[100,100],[50,100]],
  ],
  mosaic_6: [
    [[0,0],[33.33,0],[33.33,50],[0,50]],
    [[33.33,0],[66.67,0],[66.67,50],[33.33,50]],
    [[66.67,0],[100,0],[100,50],[66.67,50]],
    [[0,50],[33.33,50],[33.33,100],[0,100]],
    [[33.33,50],[66.67,50],[66.67,100],[33.33,100]],
    [[66.67,50],[100,50],[100,100],[66.67,100]],
  ],
  /* ─── Diagonali ─── */
  diagonal: [
    [[0,0],[40,0],[15,100],[0,100]],
    [[40,0],[75,0],[50,100],[15,100]],
    [[75,0],[100,0],[100,100],[50,100]],
  ],
  diagonal_4: [
    [[0,0],[35,0],[15,100],[0,100]],
    [[35,0],[60,0],[40,100],[15,100]],
    [[60,0],[85,0],[65,100],[40,100]],
    [[85,0],[100,0],[100,100],[65,100]],
  ],
  diagonal_5: [
    [[0,0],[30,0],[10,100],[0,100]],
    [[30,0],[50,0],[30,100],[10,100]],
    [[50,0],[70,0],[50,100],[30,100]],
    [[70,0],[90,0],[70,100],[50,100]],
    [[90,0],[100,0],[100,100],[70,100]],
  ],
  diagonal_reverse: [
    [[0,0],[25,0],[50,100],[0,100]],
    [[25,0],[60,0],[85,100],[50,100]],
    [[60,0],[100,0],[100,100],[85,100]],
  ],
  /* ─── Esagoni ─── */
  honeycomb: [
    [[0,0],[50,0],[60,25],[50,50],[0,40]],
    [[50,0],[100,0],[100,40],[50,50],[60,25]],
    [[0,40],[50,50],[40,75],[50,100],[0,100]],
    [[50,50],[100,40],[100,100],[50,100],[40,75]],
  ],
  honeycomb_6: [
    [[0,0],[35,0],[30,55],[0,45]],
    [[35,0],[70,0],[70,45],[30,55]],
    [[70,0],[100,0],[100,60],[70,45]],
    [[0,45],[30,55],[35,100],[0,100]],
    [[30,55],[70,45],[65,100],[35,100]],
    [[70,45],[100,60],[100,100],[65,100]],
  ],
  honeycomb_8: [
    [[0,0],[25,0],[25,55],[0,45]],
    [[25,0],[50,0],[50,42],[25,55]],
    [[50,0],[75,0],[75,58],[50,42]],
    [[75,0],[100,0],[100,48],[75,58]],
    [[0,45],[25,55],[25,100],[0,100]],
    [[25,55],[50,42],[50,100],[25,100]],
    [[50,42],[75,58],[75,100],[50,100]],
    [[75,58],[100,48],[100,100],[75,100]],
  ],
};

/* ─── Circle preset definitions ─── */
/* Each circle: { cx, cy, r } where cx/cy are % of width/height, r is visual radius as % of height */
const CIRCLE_DEFS = {
  circles_3: [
    { cx: 20, cy: 50, r: 28 },
    { cx: 50, cy: 50, r: 28 },
    { cx: 80, cy: 50, r: 28 },
  ],
  circles_4: [
    { cx: 28, cy: 33, r: 24 },
    { cx: 72, cy: 33, r: 24 },
    { cx: 28, cy: 67, r: 24 },
    { cx: 72, cy: 67, r: 24 },
  ],
  circles_5: [
    { cx: 20, cy: 33, r: 22 },
    { cx: 50, cy: 33, r: 22 },
    { cx: 80, cy: 33, r: 22 },
    { cx: 35, cy: 70, r: 22 },
    { cx: 65, cy: 70, r: 22 },
  ],
  circles_6: [
    { cx: 20, cy: 32, r: 21 },
    { cx: 50, cy: 32, r: 21 },
    { cx: 80, cy: 32, r: 21 },
    { cx: 20, cy: 68, r: 21 },
    { cx: 50, cy: 68, r: 21 },
    { cx: 80, cy: 68, r: 21 },
  ],
  circles_7: [
    { cx: 50, cy: 50, r: 24 },
    { cx: 18, cy: 28, r: 18 },
    { cx: 82, cy: 28, r: 18 },
    { cx: 10, cy: 62, r: 16 },
    { cx: 90, cy: 62, r: 16 },
    { cx: 30, cy: 82, r: 17 },
    { cx: 70, cy: 82, r: 17 },
  ],
  circles_scattered: [
    { cx: 14, cy: 22, r: 18 },
    { cx: 46, cy: 14, r: 14 },
    { cx: 78, cy: 20, r: 20 },
    { cx: 28, cy: 52, r: 22 },
    { cx: 62, cy: 48, r: 16 },
    { cx: 88, cy: 55, r: 18 },
    { cx: 18, cy: 82, r: 16 },
    { cx: 52, cy: 80, r: 20 },
    { cx: 84, cy: 84, r: 14 },
  ],
};

/**
 * Generate polygon vertices approximating a circle.
 * r is expressed as % of container height. We compensate for aspect ratio
 * so the circle appears visually round.
 */
function makeCirclePoly(cx, cy, rPctH, cw, ch, segments) {
  const rx = rPctH * (ch / cw); // compensate for wider containers
  const ry = rPctH;
  const pts = [];
  for (let i = 0; i < segments; i++) {
    const angle = (2 * Math.PI * i) / segments;
    pts.push([cx + rx * Math.cos(angle), cy + ry * Math.sin(angle)]);
  }
  return pts;
}

/**
 * Deterministic pseudo-random for a given seed (fragment index).
 * Returns a value in [0, 1).
 */
function seededRandom(seed) {
  const x = Math.sin(seed * 127.1 + 311.7) * 43758.5453123;
  return x - Math.floor(x);
}

/**
 * Apply gap to a polygon by shrinking each vertex toward the polygon's centroid.
 */
function applyGap(polygon, gapPx, containerW, containerH) {
  if (gapPx <= 0) return polygon;
  // centroid
  const cx = polygon.reduce((s, p) => s + p[0], 0) / polygon.length;
  const cy = polygon.reduce((s, p) => s + p[1], 0) / polygon.length;
  // Convert gap from px to % (approximate)
  const gapXPct = (gapPx / containerW) * 100;
  const gapYPct = (gapPx / containerH) * 100;
  return polygon.map(([x, y]) => {
    const dx = x - cx;
    const dy = y - cy;
    const dist = Math.sqrt(dx * dx + dy * dy);
    if (dist === 0) return [x, y];
    const shrink = Math.min(gapXPct, gapYPct) * 0.5;
    const factor = Math.max(0, 1 - shrink / dist);
    return [cx + dx * factor, cy + dy * factor];
  });
}

function polyToClipPath(polygon) {
  return 'polygon(' + polygon.map(p => p[0].toFixed(2) + '% ' + p[1].toFixed(2) + '%').join(', ') + ')';
}

const fragments = computed(() => {
  const presetKey = s.value.preset || 'shards';
  const gap = parseInt(s.value.gap) || 0;
  const cw = 600;
  const ch = parseInt(s.value.height) || 400;

  // Circle presets: generate polygon approximations
  const circleDef = CIRCLE_DEFS[presetKey];
  if (circleDef) {
    return circleDef.map(c => {
      const poly = makeCirclePoly(c.cx, c.cy, c.r, cw, ch, 36);
      return { clipPath: polyToClipPath(applyGap(poly, gap, cw, ch)) };
    });
  }

  // Polygon presets
  const preset = PRESETS[presetKey] || PRESETS.shards;
  return preset.map(poly => ({
    clipPath: polyToClipPath(applyGap(poly, gap, cw, ch)),
  }));
});

/* ─── Ken Burns style presets ───
   Ogni stile definisce 6 varianti da assegnare ciclicamente ai frammenti. */
const KB_STYLES = {
  mixed: [
    { from: 'scale(1.20) translate(-4%,0)',   to: 'scale(1.25) translate(4%,0)' },
    { from: 'scale(1.22) translate(0,-4%)',   to: 'scale(1.18) translate(0,4%)' },
    { from: 'scale(1.18) translate(3%,3%)',   to: 'scale(1.25) translate(-3%,-3%)' },
    { from: 'scale(1.25) translate(-3%,3%)',  to: 'scale(1.18) translate(3%,-3%)' },
    { from: 'scale(1.15) translate(5%,-1%)',  to: 'scale(1.28) translate(-3%,2%)' },
    { from: 'scale(1.28) translate(-2%,-3%)', to: 'scale(1.15) translate(2%,5%)' },
  ],
  horizontal: [
    { from: 'scale(1.20) translate(-5%,0)',   to: 'scale(1.25) translate(5%,0)' },
    { from: 'scale(1.25) translate(4%,0)',    to: 'scale(1.20) translate(-4%,0)' },
    { from: 'scale(1.18) translate(-6%,0)',   to: 'scale(1.22) translate(3%,0)' },
    { from: 'scale(1.22) translate(3%,0)',    to: 'scale(1.18) translate(-5%,0)' },
    { from: 'scale(1.20) translate(-3%,0)',   to: 'scale(1.28) translate(6%,0)' },
    { from: 'scale(1.28) translate(5%,0)',    to: 'scale(1.20) translate(-4%,0)' },
  ],
  vertical: [
    { from: 'scale(1.20) translate(0,-5%)',   to: 'scale(1.25) translate(0,5%)' },
    { from: 'scale(1.25) translate(0,4%)',    to: 'scale(1.20) translate(0,-4%)' },
    { from: 'scale(1.18) translate(0,-6%)',   to: 'scale(1.22) translate(0,3%)' },
    { from: 'scale(1.22) translate(0,3%)',    to: 'scale(1.18) translate(0,-5%)' },
    { from: 'scale(1.20) translate(0,-3%)',   to: 'scale(1.28) translate(0,6%)' },
    { from: 'scale(1.28) translate(0,5%)',    to: 'scale(1.20) translate(0,-4%)' },
  ],
  diagonal: [
    { from: 'scale(1.20) translate(-4%,-4%)', to: 'scale(1.25) translate(4%,4%)' },
    { from: 'scale(1.25) translate(3%,3%)',   to: 'scale(1.20) translate(-3%,-3%)' },
    { from: 'scale(1.18) translate(-5%,-3%)', to: 'scale(1.22) translate(3%,5%)' },
    { from: 'scale(1.22) translate(4%,2%)',   to: 'scale(1.18) translate(-4%,-4%)' },
    { from: 'scale(1.20) translate(-3%,-5%)', to: 'scale(1.28) translate(5%,3%)' },
    { from: 'scale(1.28) translate(3%,4%)',   to: 'scale(1.20) translate(-5%,-3%)' },
  ],
  radial: [
    { from: 'scale(1.15) translate(0,0)',     to: 'scale(1.30) translate(0,0)' },
    { from: 'scale(1.30) translate(-2%,-2%)', to: 'scale(1.15) translate(2%,2%)' },
    { from: 'scale(1.30) translate(2%,-2%)',  to: 'scale(1.15) translate(-2%,2%)' },
    { from: 'scale(1.30) translate(-2%,2%)',  to: 'scale(1.15) translate(2%,-2%)' },
    { from: 'scale(1.30) translate(2%,2%)',   to: 'scale(1.15) translate(-2%,-2%)' },
    { from: 'scale(1.15) translate(0,0)',     to: 'scale(1.35) translate(0,0)' },
  ],
  rotation: [
    { from: 'scale(1.18) rotate(-2deg) translate(1%,1%)',    to: 'scale(1.25) rotate(2deg) translate(-1%,-1%)' },
    { from: 'scale(1.22) rotate(1.5deg) translate(-1%,0)',   to: 'scale(1.18) rotate(-2.5deg) translate(1%,0)' },
    { from: 'scale(1.20) rotate(-1deg) translate(0,1.5%)',   to: 'scale(1.25) rotate(3deg) translate(0,-1.5%)' },
    { from: 'scale(1.25) rotate(2.5deg) translate(1%,-1%)',  to: 'scale(1.18) rotate(-1.5deg) translate(-1%,1%)' },
    { from: 'scale(1.18) rotate(-3deg) translate(-1%,-1%)',  to: 'scale(1.22) rotate(1deg) translate(1%,1%)' },
    { from: 'scale(1.22) rotate(1deg) translate(0.5%,1.5%)', to: 'scale(1.20) rotate(-2deg) translate(-0.5%,-1.5%)' },
  ],
  zoom: [
    { from: 'scale(1.12) translate(0,0)', to: 'scale(1.28) translate(0,0)' },
    { from: 'scale(1.25) translate(0,0)', to: 'scale(1.10) translate(0,0)' },
    { from: 'scale(1.10) translate(0,0)', to: 'scale(1.30) translate(0,0)' },
    { from: 'scale(1.28) translate(0,0)', to: 'scale(1.12) translate(0,0)' },
    { from: 'scale(1.15) translate(0,0)', to: 'scale(1.32) translate(0,0)' },
    { from: 'scale(1.30) translate(0,0)', to: 'scale(1.15) translate(0,0)' },
  ],
  chaotic: [
    { from: 'scale(1.15) translate(-6%,2%)',  to: 'scale(1.30) translate(4%,-4%)' },
    { from: 'scale(1.28) translate(3%,-6%)',  to: 'scale(1.15) translate(-5%,3%)' },
    { from: 'scale(1.20) translate(5%,5%)',   to: 'scale(1.30) translate(-6%,-2%)' },
    { from: 'scale(1.30) translate(-4%,-5%)', to: 'scale(1.18) translate(6%,4%)' },
    { from: 'scale(1.18) translate(-2%,6%)',  to: 'scale(1.32) translate(5%,-5%)' },
    { from: 'scale(1.32) translate(6%,-3%)',  to: 'scale(1.15) translate(-4%,6%)' },
  ],
};

const activeVariants = computed(() => KB_STYLES[s.value.kenburns_style] || KB_STYLES.mixed);

const containerStyle = computed(() => {
  const radius = parseInt(s.value.border_radius_outer) || 0;
  const bw = parseInt(s.value.border_width) || 0;
  const style = {
    position: 'relative',
    width: '100%',
    height: s.value.height || '400px',
    overflow: 'hidden',
    borderRadius: radius + 'px',
    background: s.value.gap_color || 'transparent',
    boxShadow: getShadowValue(s.value),
  };
  if (bw > 0) {
    style.border = bw + 'px solid ' + (s.value.border_color || '#e5e7eb');
  }
  return style;
});

/* Outer div: maschera statica con clip-path, non si muove */
function maskStyle(frag) {
  return {
    position: 'absolute',
    inset: '0',
    clipPath: frag.clipPath,
    overflow: 'hidden',
  };
}

/**
 * Compute zoom factor for a given fragment index.
 */
function getFragmentZoom(index) {
  if (!s.value.zoom_variation) return null;
  const min = parseInt(s.value.zoom_min) || 100;
  const max = parseInt(s.value.zoom_max) || 180;
  if (min >= max) return min;
  const n = fragments.value.length;
  if (s.value.zoom_random) {
    return min + (max - min) * seededRandom(index);
  }
  // Sequential gradient: min → max across fragments
  return n <= 1 ? min : min + (max - min) * (index / (n - 1));
}

/* Inner div: immagine con Ken Burns, si muove dentro la maschera ferma */
function imageStyle(index) {
  const duration = parseInt(s.value.kenburns_duration) || 20;
  const kbEnabled = s.value.kenburns;
  const zoom = getFragmentZoom(index);

  const base = {
    position: 'absolute',
    inset: '0',
    backgroundImage: `url(${s.value.image_url})`,
    backgroundSize: zoom ? (zoom + '%') : 'cover',
    backgroundPosition: s.value.image_position || 'center center',
  };

  if (kbEnabled) {
    const vars = activeVariants.value;
    const delay = Math.min(2, (duration / fragments.value.length) * 0.3) * index;
    base.animation = `olo-kb-${index % vars.length} ${duration}s ${delay.toFixed(1)}s ease-in-out infinite alternate both`;
    base.willChange = 'transform';
  }

  return base;
}

const overlayStyle = computed(() => {
  const color = s.value.overlay_color || '#000000';
  const opacity = (parseInt(s.value.overlay_opacity) || 30) / 100;
  return {
    position: 'absolute',
    inset: '0',
    background: color,
    opacity: opacity,
    pointerEvents: 'none',
  };
});

/* Genera @keyframes CSS dinamicamente in base allo stile selezionato */
const dynamicKeyframes = computed(() => {
  const vars = activeVariants.value;
  let css = '';
  vars.forEach((v, i) => {
    css += `@keyframes olo-kb-${i}{from{transform:${v.from}}to{transform:${v.to}}}`;
  });
  css += `@media(prefers-reduced-motion:reduce){`;
  vars.forEach((_, i) => {
    css += `@keyframes olo-kb-${i}{from,to{transform:none}}`;
  });
  css += `}`;
  return css;
});
</script>

<style scoped>
</style>
