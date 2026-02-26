<template>
  <div style="padding:4px">
    <div v-if="!images.length" style="display:flex;align-items:center;justify-content:center;height:200px;background:#f3f4f6;border-radius:8px;color:#9ca3af;font-size:13px">
      Aggiungi immagini alla Pro Gallery
    </div>
    <template v-else>
      <!-- Grid -->
      <div v-if="layout === 'grid'" :style="gridStyle">
        <div v-for="(img, i) in visibleImages" :key="i" :style="itemStyle(i)">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" :style="imgStyle" />
          <div v-if="isLastVisible(i)" :style="moreOverlayStyle">+{{ extraCount }}</div>
        </div>
      </div>

      <!-- Masonry -->
      <div v-else-if="layout === 'masonry'" :style="masonryStyle">
        <div v-for="(img, i) in visibleImages" :key="i" :style="masonryItemStyle(i)">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" style="width:100%;display:block;object-fit:cover" :style="{ borderRadius: radius + 'px' }" />
          <div v-if="isLastVisible(i)" :style="moreOverlayStyle">+{{ extraCount }}</div>
        </div>
      </div>

      <!-- Scattered -->
      <div v-else-if="layout === 'scattered'" :style="scatteredContainerStyle">
        <div v-for="(img, i) in visibleImages" :key="i" :style="scatteredItemStyle(i)">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" :style="imgStyle" />
        </div>
      </div>

      <!-- Collage -->
      <div v-else-if="layout === 'collage'" :style="collageStyle">
        <div v-for="(img, i) in visibleImages" :key="i" :style="collageItemStyle(i)">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" :style="imgStyle" />
          <div v-if="isLastVisible(i)" :style="moreOverlayStyle">+{{ extraCount }}</div>
        </div>
      </div>

      <!-- Filmstrip -->
      <div v-else-if="layout === 'filmstrip'" :style="filmstripStyle">
        <div v-for="(img, i) in visibleImages" :key="i" :style="filmstripItemStyle">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" :style="imgStyle" />
        </div>
      </div>

      <!-- Mosaic -->
      <div v-else-if="layout === 'mosaic'" :style="mosaicStyle">
        <div v-for="(img, i) in visibleImages" :key="i" :style="mosaicItemStyle(i)">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" :style="imgStyle" />
          <div v-if="isLastVisible(i)" :style="moreOverlayStyle">+{{ extraCount }}</div>
        </div>
      </div>

      <!-- Honeycomb -->
      <div v-else-if="layout === 'honeycomb'" :style="honeycombStyle">
        <div v-for="(img, i) in visibleImages" :key="i" :style="honeycombItemStyle">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" style="width:100%;height:100%;object-fit:cover" />
        </div>
      </div>

      <!-- Hex Grid (tessellating) -->
      <div v-else-if="layout === 'hexgrid'" :style="hexGridContainerStyle">
        <div v-for="(img, i) in visibleImages" :key="i" :style="hexGridItemStyle(i)">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" style="width:100%;height:100%;object-fit:cover" />
          <div v-if="isLastVisible(i)" :style="moreOverlayStyle">+{{ extraCount }}</div>
        </div>
      </div>

      <!-- Puzzle -->
      <div v-else-if="layout === 'puzzle'" :style="puzzleContainerStyle">
        <div v-for="(img, i) in visibleImages" :key="i" :style="puzzleItemStyle(i)">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" :style="puzzleImgStyle" />
          <div v-if="isLastVisible(i)" :style="moreOverlayStyle">+{{ extraCount }}</div>
        </div>
      </div>

      <!-- Diagonal -->
      <div v-else-if="layout === 'diagonal'" :style="gridStyle">
        <div v-for="(img, i) in visibleImages" :key="i" :style="diagonalItemStyle(i)">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" :style="imgStyle" />
          <div v-if="isLastVisible(i)" :style="moreOverlayStyle">+{{ extraCount }}</div>
        </div>
      </div>

      <!-- Effect badges (builder only) -->
      <div v-if="hasBadges" style="display:flex;gap:4px;margin-top:4px;flex-wrap:wrap">
        <span v-if="s.entrance && s.entrance !== 'none'" :style="badgeStyle">Entrance: {{ s.entrance }}</span>
        <span v-if="s.continuous && s.continuous !== 'none'" :style="badgeStyle">Anim: {{ s.continuous }}</span>
        <span v-if="s.hover_effect && s.hover_effect !== 'none'" :style="badgeStyle">Hover: {{ s.hover_effect }}</span>
        <span v-if="s.filter && s.filter !== 'none'" :style="badgeStyle">Filtro: {{ s.filter }}</span>
        <span v-if="s.frame && s.frame !== 'none'" :style="badgeStyle">Cornice: {{ s.frame }}</span>
      </div>
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => props.settings);

const layout = computed(() => s.value.layout || 'grid');
const cols = computed(() => Math.max(2, Math.min(6, parseInt(s.value.columns) || 3)));
const gap = computed(() => parseInt(s.value.gap) || 8);
const radius = computed(() => parseInt(s.value.thumb_radius) || 8);
const imgHeight = computed(() => s.value.img_height || '250px');
const objectFit = computed(() => s.value.object_fit || 'cover');

const images = computed(() => {
  const imgs = s.value.images;
  return Array.isArray(imgs) ? imgs.filter(i => imgUrl(i)) : [];
});

const maxVisible = computed(() => {
  const rows = parseInt(s.value.rows) || 0;
  if (rows <= 0) return images.value.length;
  return cols.value * rows;
});

const visibleImages = computed(() => images.value.slice(0, maxVisible.value));
const extraCount = computed(() => Math.max(0, images.value.length - maxVisible.value));

function isLastVisible(i) {
  return extraCount.value > 0 && i === visibleImages.value.length - 1;
}

function imgUrl(img) {
  return typeof img === 'string' ? img : (img?.url || '');
}
function imgAlt(img) {
  return typeof img === 'string' ? '' : (img?.alt || '');
}

function seededRandom(seed) {
  const x = Math.sin(seed * 127.1 + 311.7) * 43758.5453123;
  return x - Math.floor(x);
}

const imgStyle = computed(() => ({
  width: '100%',
  height: '100%',
  objectFit: objectFit.value,
  display: 'block',
  borderRadius: radius.value + 'px',
}));

const filterStyle = computed(() => {
  const f = s.value.filter;
  if (!f || f === 'none') return {};
  const map = {
    grayscale: 'grayscale(100%)',
    sepia: 'sepia(80%)',
    'high-contrast': 'contrast(140%) saturate(120%)',
    warm: 'sepia(25%) saturate(130%) hue-rotate(-10deg)',
    cool: 'saturate(80%) hue-rotate(20deg) brightness(105%)',
    vintage: 'sepia(40%) contrast(90%) brightness(95%) saturate(80%)',
    duotone: 'grayscale(100%) contrast(110%)',
  };
  return { filter: map[f] || '' };
});

// ─── Grid ───
const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${cols.value}, 1fr)`,
  gap: gap.value + 'px',
}));

function itemStyle() {
  return {
    position: 'relative',
    overflow: 'hidden',
    borderRadius: radius.value + 'px',
    height: imgHeight.value,
    ...filterStyle.value,
  };
}

// ─── Masonry ───
const masonryStyle = computed(() => ({
  columnCount: cols.value,
  columnGap: gap.value + 'px',
}));

function masonryItemStyle(i) {
  const heights = [180, 240, 200, 280, 220, 260, 190, 250];
  const h = heights[i % heights.length];
  return {
    marginBottom: gap.value + 'px',
    overflow: 'hidden',
    position: 'relative',
    breakInside: 'avoid',
    height: h + 'px',
    ...filterStyle.value,
  };
}

// ─── Scattered ───
const scatteredContainerStyle = computed(() => ({
  position: 'relative',
  height: '400px',
  overflow: 'hidden',
}));

function scatteredItemStyle(i) {
  const total = visibleImages.value.length;
  const colsNum = Math.ceil(Math.sqrt(total));
  const rowsNum = Math.ceil(total / colsNum);
  const col = i % colsNum;
  const row = Math.floor(i / colsNum);
  const cellW = 100 / colsNum;
  const cellH = 100 / rowsNum;
  const rot = (seededRandom(i) - 0.5) * 12;
  const offX = (seededRandom(i + 7) - 0.5) * 10;
  const offY = (seededRandom(i + 13) - 0.5) * 10;
  return {
    position: 'absolute',
    left: (col * cellW + offX) + '%',
    top: (row * cellH + offY) + '%',
    width: (cellW * 0.85) + '%',
    height: (cellH * 0.85) + '%',
    transform: `rotate(${rot.toFixed(1)}deg)`,
    overflow: 'hidden',
    borderRadius: radius.value + 'px',
    boxShadow: '0 4px 12px rgba(0,0,0,.15)',
    ...filterStyle.value,
  };
}

// ─── Collage ───
const collageStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${cols.value}, 1fr)`,
  gridAutoRows: imgHeight.value,
  gap: gap.value + 'px',
}));

function collageItemStyle(i) {
  const base = {
    position: 'relative',
    overflow: 'hidden',
    borderRadius: radius.value + 'px',
    ...filterStyle.value,
  };
  if (i === 0) {
    base.gridColumn = 'span 2';
    base.gridRow = 'span 2';
  }
  return base;
}

// ─── Filmstrip ───
const filmstripStyle = computed(() => ({
  display: 'flex',
  gap: gap.value + 'px',
  overflowX: 'auto',
  paddingBottom: '4px',
}));

const filmstripItemStyle = computed(() => ({
  flex: '0 0 auto',
  width: '250px',
  height: imgHeight.value,
  overflow: 'hidden',
  borderRadius: radius.value + 'px',
  ...filterStyle.value,
}));

// ─── Mosaic ───
const mosaicStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${cols.value}, 1fr)`,
  gridAutoRows: imgHeight.value,
  gap: gap.value + 'px',
}));

function mosaicItemStyle(i) {
  const base = {
    position: 'relative',
    overflow: 'hidden',
    borderRadius: radius.value + 'px',
    ...filterStyle.value,
  };
  // Pattern: every 5th starting from 0 is large (2x2), every 5th starting from 3 is wide (2x1)
  if (i % 5 === 0) {
    base.gridColumn = 'span 2';
    base.gridRow = 'span 2';
  } else if (i % 5 === 3) {
    base.gridColumn = 'span 2';
  }
  return base;
}

// ─── Honeycomb ───
const honeycombStyle = computed(() => ({
  display: 'flex',
  flexWrap: 'wrap',
  gap: gap.value + 'px',
  justifyContent: 'center',
}));

const honeycombItemStyle = computed(() => ({
  width: '140px',
  height: '160px',
  clipPath: 'polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%)',
  overflow: 'hidden',
  ...filterStyle.value,
}));

// ─── Hex Grid (tessellating) ───
const hexGridContainerStyle = computed(() => {
  const c = cols.value;
  const n = visibleImages.value.length;
  const totalRows = Math.ceil(n / c);
  const hexW = 100 / (c + 0.5);
  const hexH = hexW * 1.1547;
  const rowStep = hexH * 0.75;
  const containerH = rowStep * (totalRows - 1) + hexH;
  return {
    position: 'relative',
    width: '100%',
    paddingBottom: containerH + '%',
  };
});

function hexGridItemStyle(i) {
  const c = cols.value;
  const hexW = 100 / (c + 0.5);
  const hexH = hexW * 1.1547;
  const rowStep = hexH * 0.75;
  const totalRows = Math.ceil(visibleImages.value.length / c);
  const containerH = rowStep * (totalRows - 1) + hexH;
  const row = Math.floor(i / c);
  const col = i % c;
  const offsetX = row % 2 ? hexW * 0.5 : 0;
  return {
    position: 'absolute',
    left: (col * hexW + offsetX) + '%',
    top: (row * rowStep / containerH * 100) + '%',
    width: hexW + '%',
    height: (hexH / containerH * 100) + '%',
    clipPath: 'polygon(50% 0%, 100% 25%, 100% 75%, 50% 100%, 0% 75%, 0% 25%)',
    overflow: 'hidden',
    ...filterStyle.value,
  };
}

// ─── Puzzle ───
const puzzleStyle = computed(() => s.value.puzzle_style || 'classic');

const puzzleParams = computed(() => {
  const style = puzzleStyle.value;
  if (style === 'zigzag') return { pad: 14, sc: 128 };
  if (style === 'wave')   return { pad: 14, sc: 128 };
  if (style === 'castle') return { pad: 14, sc: 128 };
  if (style === 'fir')    return { pad: 20, sc: 140 };
  return { pad: 23, sc: 146 }; // classic
});

function pz(v) {
  const { pad, sc } = puzzleParams.value;
  return ((v + pad) / sc * 100).toFixed(1);
}

const puzzleContainerStyle = computed(() => {
  const c = cols.value;
  const h = parseInt(imgHeight.value) || 250;
  const totalRows = Math.ceil(visibleImages.value.length / c);
  return {
    position: 'relative',
    width: '100%',
    height: (totalRows * h) + 'px',
    overflow: 'visible',
  };
});

const puzzleImgStyle = computed(() => ({
  position: 'absolute',
  top: '0',
  left: '0',
  width: '100%',
  height: '100%',
  objectFit: 'cover',
}));

function puzzleEdges(i) {
  const c = cols.value;
  const row = Math.floor(i / c);
  const col = i % c;
  const totalRows = Math.ceil(visibleImages.value.length / c);
  const isEven = (row + col) % 2 === 0;
  return {
    top:    row === 0            ? 'flat' : (isEven ? 'blank' : 'tab'),
    right:  col === c - 1        ? 'flat' : (isEven ? 'tab' : 'blank'),
    bottom: row >= totalRows - 1 ? 'flat' : (isEven ? 'blank' : 'tab'),
    left:   col === 0            ? 'flat' : (isEven ? 'tab' : 'blank'),
  };
}

// ── Classic: knob rotondi jigsaw ──
function classicEdgePoints(edge, type) {
  const d = type === 'tab' ? 1 : -1;
  const R = 9, CY = 13, NW = 5.5, NH = 6, N = 16;
  const arc = [];
  for (let i = 0; i <= N; i++) {
    const rad = (225 - i * 270 / N) * Math.PI / 180;
    arc.push([50 + R * Math.cos(rad), CY + R * Math.sin(rad)]);
  }
  if (edge === 'top') {
    const pts = [`${pz(30)}% ${pz(0)}%`, `${pz(36)}% ${pz(0)}%`];
    pts.push(`${pz(50-NW)}% ${pz(-d*1)}%`, `${pz(50-NW)}% ${pz(-d*NH)}%`);
    arc.forEach(([ax,ay]) => pts.push(`${pz(ax)}% ${pz(-d*ay)}%`));
    pts.push(`${pz(50+NW)}% ${pz(-d*NH)}%`, `${pz(50+NW)}% ${pz(-d*1)}%`);
    pts.push(`${pz(64)}% ${pz(0)}%`, `${pz(70)}% ${pz(0)}%`);
    return pts;
  }
  if (edge === 'right') {
    const pts = [`${pz(100)}% ${pz(30)}%`, `${pz(100)}% ${pz(36)}%`];
    pts.push(`${pz(100+d*1)}% ${pz(50-NW)}%`, `${pz(100+d*NH)}% ${pz(50-NW)}%`);
    [...arc].reverse().forEach(([ax,ay]) => pts.push(`${pz(100+d*ay)}% ${pz(100-ax)}%`));
    pts.push(`${pz(100+d*NH)}% ${pz(50+NW)}%`, `${pz(100+d*1)}% ${pz(50+NW)}%`);
    pts.push(`${pz(100)}% ${pz(64)}%`, `${pz(100)}% ${pz(70)}%`);
    return pts;
  }
  if (edge === 'bottom') {
    const pts = [`${pz(70)}% ${pz(100)}%`, `${pz(64)}% ${pz(100)}%`];
    pts.push(`${pz(50+NW)}% ${pz(100+d*1)}%`, `${pz(50+NW)}% ${pz(100+d*NH)}%`);
    arc.forEach(([ax,ay]) => pts.push(`${pz(100-ax)}% ${pz(100+d*ay)}%`));
    pts.push(`${pz(50-NW)}% ${pz(100+d*NH)}%`, `${pz(50-NW)}% ${pz(100+d*1)}%`);
    pts.push(`${pz(36)}% ${pz(100)}%`, `${pz(30)}% ${pz(100)}%`);
    return pts;
  }
  if (edge === 'left') {
    const pts = [`${pz(0)}% ${pz(70)}%`, `${pz(0)}% ${pz(64)}%`];
    pts.push(`${pz(-d*1)}% ${pz(50+NW)}%`, `${pz(-d*NH)}% ${pz(50+NW)}%`);
    arc.forEach(([ax,ay]) => pts.push(`${pz(-d*ay)}% ${pz(100-ax)}%`));
    pts.push(`${pz(-d*NH)}% ${pz(50-NW)}%`, `${pz(-d*1)}% ${pz(50-NW)}%`);
    pts.push(`${pz(0)}% ${pz(36)}%`, `${pz(0)}% ${pz(30)}%`);
    return pts;
  }
  return [];
}

// ── Zigzag: 4 denti triangolari per lato ──
function zigzagEdgePoints(edge, type) {
  const d = type === 'tab' ? 1 : -1;
  const amp = 12, teeth = 4;
  const start = 25, end = 75;
  const span = end - start;
  const step = span / teeth;
  if (edge === 'top') {
    const pts = [`${pz(start)}% ${pz(0)}%`];
    for (let t = 0; t < teeth; t++) {
      const x0 = start + t * step;
      pts.push(`${pz(x0 + step/2)}% ${pz(-d * amp)}%`);
      pts.push(`${pz(x0 + step)}% ${pz(0)}%`);
    }
    return pts;
  }
  if (edge === 'right') {
    const pts = [`${pz(100)}% ${pz(start)}%`];
    for (let t = 0; t < teeth; t++) {
      const y0 = start + t * step;
      pts.push(`${pz(100 + d * amp)}% ${pz(y0 + step/2)}%`);
      pts.push(`${pz(100)}% ${pz(y0 + step)}%`);
    }
    return pts;
  }
  if (edge === 'bottom') {
    const pts = [`${pz(end)}% ${pz(100)}%`];
    for (let t = 0; t < teeth; t++) {
      const x0 = end - t * step;
      pts.push(`${pz(x0 - step/2)}% ${pz(100 + d * amp)}%`);
      pts.push(`${pz(x0 - step)}% ${pz(100)}%`);
    }
    return pts;
  }
  if (edge === 'left') {
    const pts = [`${pz(0)}% ${pz(end)}%`];
    for (let t = 0; t < teeth; t++) {
      const y0 = end - t * step;
      pts.push(`${pz(-d * amp)}% ${pz(y0 - step/2)}%`);
      pts.push(`${pz(0)}% ${pz(y0 - step)}%`);
    }
    return pts;
  }
  return [];
}

// ── Wave: 2 cicli sinusoidali ──
function waveEdgePoints(edge, type) {
  const d = type === 'tab' ? 1 : -1;
  const amp = 10, N = 32;
  const start = 20, end = 80;
  const span = end - start;
  if (edge === 'top') {
    const pts = [];
    for (let i = 0; i <= N; i++) {
      const t = i / N;
      const x = start + t * span;
      const perp = d * amp * Math.sin(t * 4 * Math.PI);
      pts.push(`${pz(x)}% ${pz(-perp)}%`);
    }
    return pts;
  }
  if (edge === 'right') {
    const pts = [];
    for (let i = 0; i <= N; i++) {
      const t = i / N;
      const y = start + t * span;
      const perp = d * amp * Math.sin(t * 4 * Math.PI);
      pts.push(`${pz(100 + perp)}% ${pz(y)}%`);
    }
    return pts;
  }
  if (edge === 'bottom') {
    const pts = [];
    for (let i = 0; i <= N; i++) {
      const t = i / N;
      const x = end - t * span;
      const perp = -d * amp * Math.sin(t * 4 * Math.PI);
      pts.push(`${pz(x)}% ${pz(100 + perp)}%`);
    }
    return pts;
  }
  if (edge === 'left') {
    const pts = [];
    for (let i = 0; i <= N; i++) {
      const t = i / N;
      const y = end - t * span;
      const perp = -d * amp * Math.sin(t * 4 * Math.PI);
      pts.push(`${pz(-perp)}% ${pz(y)}%`);
    }
    return pts;
  }
  return [];
}

// ── Castle: 3 merli rettangolari ──
function castleEdgePoints(edge, type) {
  const d = type === 'tab' ? 1 : -1;
  const h = 10, w = 8, merlons = 3;
  const start = 25, end = 75;
  const span = end - start;
  const merlonSpan = span / merlons;
  const gap = (merlonSpan - w) / 2;
  if (edge === 'top') {
    const pts = [`${pz(start)}% ${pz(0)}%`];
    for (let m = 0; m < merlons; m++) {
      const x0 = start + m * merlonSpan + gap;
      pts.push(`${pz(x0)}% ${pz(0)}%`);
      pts.push(`${pz(x0)}% ${pz(-d * h)}%`);
      pts.push(`${pz(x0 + w)}% ${pz(-d * h)}%`);
      pts.push(`${pz(x0 + w)}% ${pz(0)}%`);
    }
    pts.push(`${pz(end)}% ${pz(0)}%`);
    return pts;
  }
  if (edge === 'right') {
    const pts = [`${pz(100)}% ${pz(start)}%`];
    for (let m = 0; m < merlons; m++) {
      const y0 = start + m * merlonSpan + gap;
      pts.push(`${pz(100)}% ${pz(y0)}%`);
      pts.push(`${pz(100 + d * h)}% ${pz(y0)}%`);
      pts.push(`${pz(100 + d * h)}% ${pz(y0 + w)}%`);
      pts.push(`${pz(100)}% ${pz(y0 + w)}%`);
    }
    pts.push(`${pz(100)}% ${pz(end)}%`);
    return pts;
  }
  if (edge === 'bottom') {
    const pts = [`${pz(end)}% ${pz(100)}%`];
    for (let m = 0; m < merlons; m++) {
      const x0 = end - m * merlonSpan - gap;
      pts.push(`${pz(x0)}% ${pz(100)}%`);
      pts.push(`${pz(x0)}% ${pz(100 + d * h)}%`);
      pts.push(`${pz(x0 - w)}% ${pz(100 + d * h)}%`);
      pts.push(`${pz(x0 - w)}% ${pz(100)}%`);
    }
    pts.push(`${pz(start)}% ${pz(100)}%`);
    return pts;
  }
  if (edge === 'left') {
    const pts = [`${pz(0)}% ${pz(end)}%`];
    for (let m = 0; m < merlons; m++) {
      const y0 = end - m * merlonSpan - gap;
      pts.push(`${pz(0)}% ${pz(y0)}%`);
      pts.push(`${pz(-d * h)}% ${pz(y0)}%`);
      pts.push(`${pz(-d * h)}% ${pz(y0 - w)}%`);
      pts.push(`${pz(0)}% ${pz(y0 - w)}%`);
    }
    pts.push(`${pz(0)}% ${pz(start)}%`);
    return pts;
  }
  return [];
}

// ── Fir: 2 abeti a 3 livelli per lato ──
function firEdgePoints(edge, type) {
  const d = type === 'tab' ? 1 : -1;
  const tw = 1.5, th = 3.75;
  const t1w = 8.25, t1pw = 3, t1h = 8.25;
  const t2w = 6, t2pw = 2.25, t2h = 12.75;
  const t3w = 4.5, peakH = 18;
  // Profilo albero: [offset_lungo, offset_perp] dal centro
  const P = [
    [-tw,0],[-tw,th],[-t1w,th],[-t1pw,t1h],[-t2w,t1h],[-t2pw,t2h],[-t3w,t2h],
    [0,peakH],
    [t3w,t2h],[t2pw,t2h],[t2w,t1h],[t1pw,t1h],[t1w,th],[tw,th],[tw,0]
  ];
  if (edge === 'top') {
    const pts = [`${pz(25)}% ${pz(0)}%`];
    for (const cx of [37.5, 62.5]) {
      for (const [ao, po] of P) pts.push(`${pz(cx+ao)}% ${pz(-d*po)}%`);
    }
    pts.push(`${pz(75)}% ${pz(0)}%`);
    return pts;
  }
  if (edge === 'right') {
    const pts = [`${pz(100)}% ${pz(25)}%`];
    for (const cy of [37.5, 62.5]) {
      for (const [ao, po] of P) pts.push(`${pz(100+d*po)}% ${pz(cy+ao)}%`);
    }
    pts.push(`${pz(100)}% ${pz(75)}%`);
    return pts;
  }
  if (edge === 'bottom') {
    const pts = [`${pz(75)}% ${pz(100)}%`];
    for (const cx of [62.5, 37.5]) {
      for (const [ao, po] of P) pts.push(`${pz(cx-ao)}% ${pz(100+d*po)}%`);
    }
    pts.push(`${pz(25)}% ${pz(100)}%`);
    return pts;
  }
  if (edge === 'left') {
    const pts = [`${pz(0)}% ${pz(75)}%`];
    for (const cy of [62.5, 37.5]) {
      for (const [ao, po] of P) pts.push(`${pz(-d*po)}% ${pz(cy-ao)}%`);
    }
    pts.push(`${pz(0)}% ${pz(25)}%`);
    return pts;
  }
  return [];
}

// ── Dispatcher ──
function puzzleTabPoints(edge, type) {
  const style = puzzleStyle.value;
  if (style === 'zigzag') return zigzagEdgePoints(edge, type);
  if (style === 'wave')   return waveEdgePoints(edge, type);
  if (style === 'castle') return castleEdgePoints(edge, type);
  if (style === 'fir')    return firEdgePoints(edge, type);
  return classicEdgePoints(edge, type);
}

function puzzleClipPath(i) {
  const e = puzzleEdges(i);
  const pts = [`${pz(0)}% ${pz(0)}%`];
  if (e.top === 'flat') { pts.push(`${pz(100)}% ${pz(0)}%`); }
  else { pts.push(...puzzleTabPoints('top', e.top), `${pz(100)}% ${pz(0)}%`); }
  if (e.right === 'flat') { pts.push(`${pz(100)}% ${pz(100)}%`); }
  else { pts.push(...puzzleTabPoints('right', e.right), `${pz(100)}% ${pz(100)}%`); }
  if (e.bottom === 'flat') { pts.push(`${pz(0)}% ${pz(100)}%`); }
  else { pts.push(...puzzleTabPoints('bottom', e.bottom), `${pz(0)}% ${pz(100)}%`); }
  if (e.left !== 'flat') { pts.push(...puzzleTabPoints('left', e.left)); }
  return 'polygon(' + pts.join(',') + ')';
}

function puzzleItemStyle(i) {
  const c = cols.value;
  const h = parseInt(imgHeight.value) || 250;
  const row = Math.floor(i / c);
  const col = i % c;
  const cellW = 100 / c;
  const PZ_PAD = puzzleParams.value.pad;
  const padW = cellW * PZ_PAD / 100;
  const padH = h * PZ_PAD / 100;
  return {
    position: 'absolute',
    left: (col * cellW - padW) + '%',
    top: (row * h - padH) + 'px',
    width: (cellW + 2 * padW) + '%',
    height: (h + 2 * padH) + 'px',
    clipPath: puzzleClipPath(i),
    ...filterStyle.value,
  };
}

// ─── Diagonal ───
function diagonalItemStyle(i) {
  const skewDeg = ((i % 2 === 0) ? -3 : 3);
  return {
    position: 'relative',
    overflow: 'hidden',
    borderRadius: radius.value + 'px',
    height: imgHeight.value,
    transform: `skewY(${skewDeg}deg)`,
    ...filterStyle.value,
  };
}

// ─── "+N" overlay ───
const moreOverlayStyle = computed(() => ({
  position: 'absolute',
  inset: '0',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  background: s.value.more_bg || 'rgba(0,0,0,0.55)',
  color: s.value.more_color || '#fff',
  fontSize: (parseInt(s.value.more_size) || 28) + 'px',
  fontWeight: '700',
  borderRadius: radius.value + 'px',
  zIndex: 5,
}));

// ─── Badges ───
const hasBadges = computed(() =>
  (s.value.entrance && s.value.entrance !== 'none') ||
  (s.value.continuous && s.value.continuous !== 'none') ||
  (s.value.hover_effect && s.value.hover_effect !== 'none') ||
  (s.value.filter && s.value.filter !== 'none') ||
  (s.value.frame && s.value.frame !== 'none')
);

const badgeStyle = {
  background: 'rgba(99,102,241,.85)',
  color: '#fff',
  fontSize: '10px',
  padding: '2px 6px',
  borderRadius: '4px',
  lineHeight: '1.3',
};
</script>

<style scoped>
</style>
