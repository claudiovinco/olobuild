<template>
  <div style="padding:4px">
    <div v-if="!images.length" style="display:flex;align-items:center;justify-content:center;height:200px;background:var(--olo-color-surface-alt, #f6f7f9);border-radius:8px;color:var(--olo-color-text-soft, #6b7280);font-size:13px">
      {{ t('Aggiungi immagini alla Pro Gallery') }}
    </div>
    <template v-else>
      <!-- Grid -->
      <div v-if="layout === 'grid'" :style="gridStyle">
        <div v-for="(img, i) in visibleImages" :key="img.id || img.url || i" :style="itemStyle(i)">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" :style="imgStyle" />
          <div v-if="isVideoItem(visibleImages[i])" :style="playBadgeStyle"></div>
          <div v-if="isLastVisible(i)" :style="moreOverlayStyle">+{{ extraCount }}</div>
        </div>
      </div>

      <!-- Justified -->
      <div v-else-if="layout === 'justified'" :style="justifiedStyle">
        <div v-for="(img, i) in visibleImages" :key="img.id || img.url || i" :style="justifiedItemStyle">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" :style="imgStyle" />
          <div v-if="isVideoItem(visibleImages[i])" :style="playBadgeStyle"></div>
          <div v-if="isLastVisible(i)" :style="moreOverlayStyle">+{{ extraCount }}</div>
        </div>
      </div>

      <!-- Masonry -->
      <div v-else-if="layout === 'masonry'" :style="masonryStyle">
        <div v-for="(img, i) in visibleImages" :key="img.id || img.url || i" :style="masonryItemStyle(i)">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" style="width:100%;display:block;object-fit:cover" :style="{ borderRadius: radius + 'px', objectPosition: objPos }" />
          <div v-if="isVideoItem(visibleImages[i])" :style="playBadgeStyle"></div>
          <div v-if="isLastVisible(i)" :style="moreOverlayStyle">+{{ extraCount }}</div>
        </div>
      </div>

      <!-- Scattered -->
      <div v-else-if="layout === 'scattered'" :style="scatteredContainerStyle">
        <div v-for="(img, i) in visibleImages" :key="img.id || img.url || i" :style="scatteredItemStyle(i)">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" :style="imgStyle" />
          <div v-if="isVideoItem(visibleImages[i])" :style="playBadgeStyle"></div>
        </div>
      </div>

      <!-- Parallax -->
      <div v-else-if="layout === 'parallax'" :style="parallaxContainerStyle">
        <div v-for="(img, i) in visibleImages" :key="img.id || img.url || i" :style="parallaxItemStyle(i)">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" :style="imgStyleAuto" />
          <div v-if="isVideoItem(visibleImages[i])" :style="playBadgeStyle"></div>
        </div>
      </div>

      <!-- Drift (multi-directional parallax) -->
      <div v-else-if="layout === 'drift'" :style="driftContainerStyle">
        <div v-for="(img, i) in visibleImages" :key="img.id || img.url || i" :style="driftItemStyle(i)">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" :style="imgStyleAuto" />
          <div v-if="isVideoItem(visibleImages[i])" :style="playBadgeStyle"></div>
        </div>
      </div>

      <!-- Cascade (stacked cards) -->
      <div v-else-if="layout === 'cascade'" :style="cascadeContainerStyle">
        <div v-for="(img, i) in visibleImages" :key="img.id || img.url || i" :style="cascadeItemStyle(i)">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" :style="imgStyle" />
          <div v-if="isVideoItem(visibleImages[i])" :style="playBadgeStyle"></div>
        </div>
      </div>

      <!-- Metro (mixed sizes) -->
      <div v-else-if="layout === 'metro'" :style="metroStyle">
        <div v-for="(img, i) in visibleImages" :key="img.id || img.url || i" :style="metroItemStyle(i)">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" :style="imgStyle" />
          <div v-if="isVideoItem(visibleImages[i])" :style="playBadgeStyle"></div>
          <div v-if="isLastVisible(i)" :style="moreOverlayStyle">+{{ extraCount }}</div>
        </div>
      </div>

      <!-- Collage -->
      <div v-else-if="layout === 'collage'" :style="collageStyle">
        <div v-for="(img, i) in visibleImages" :key="img.id || img.url || i" :style="collageItemStyle(i)">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" :style="imgStyle" />
          <div v-if="isVideoItem(visibleImages[i])" :style="playBadgeStyle"></div>
          <div v-if="isLastVisible(i)" :style="moreOverlayStyle">+{{ extraCount }}</div>
        </div>
      </div>

      <!-- Coverflow 3D preview -->
      <div v-else-if="layout === 'strip_coverflow'" :style="filmstripWrapStyle">
        <div :style="filmstripStyle">
          <div v-for="(img, i) in visibleImages" :key="img.id || img.url || i" :style="filmstripItemStyle(i)">
            <img :src="imgUrl(img)" :alt="imgAlt(img)" :style="imgStyle" />
            <div v-if="isVideoItem(visibleImages[i])" :style="playBadgeStyle"></div>
          </div>
        </div>
        <div :style="filmArrowStyle('left')">&#8249;</div>
        <div :style="filmArrowStyle('right')">&#8250;</div>
        <!-- Dots / Lines -->
        <div v-if="filmDotsStyle === 'dots' || filmDotsStyle === 'lines'" :style="filmDotsContainerStyle">
          <span v-for="(img, i) in visibleImages" :key="'d'+i"
            :style="filmDotStyle(i)"></span>
        </div>
        <!-- Progress bar -->
        <div v-else-if="filmDotsStyle === 'progress'" :style="filmProgressWrapStyle">
          <div :style="filmProgressTrackStyle">
            <div :style="filmProgressFillStyle"></div>
          </div>
        </div>
        <!-- Fraction -->
        <div v-else-if="filmDotsStyle === 'fraction'" :style="filmFractionStyle">
          {{ filmFractionText }}
        </div>
      </div>

      <!-- Strip (nastro orizzontale, drag-to-scroll) -->
      <div v-else-if="layout === 'strip'" :style="stripWrapStyle">
        <div v-for="(img, i) in visibleImages" :key="img.id || img.url || i" :style="stripItemStyle(i)">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" :style="imgStyle" />
          <div v-if="isVideoItem(visibleImages[i])" :style="playBadgeStyle"></div>
        </div>
      </div>

      <!-- Strip Collage (altezze variabili) -->
      <div v-else-if="layout === 'strip_collage'" :style="stripWrapStyle">
        <div v-for="(img, i) in visibleImages" :key="img.id || img.url || i" :style="stripCollageItemStyle(i)">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" :style="imgStyle" />
          <div v-if="isVideoItem(visibleImages[i])" :style="playBadgeStyle"></div>
        </div>
      </div>

      <!-- Strip Multi-riga -->
      <div v-else-if="layout === 'strip_multi'" :style="stripMultiStyle">
        <div v-for="(img, i) in visibleImages" :key="img.id || img.url || i" :style="stripMultiItemStyle">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" :style="imgStyle" />
          <div v-if="isVideoItem(visibleImages[i])" :style="playBadgeStyle"></div>
        </div>
      </div>

      <!-- Strip Marquee (auto-scroll) -->
      <div v-else-if="layout === 'strip_marquee'" :style="stripWrapStyle">
        <div v-for="(img, i) in visibleImages" :key="img.id || img.url || i" :style="stripItemStyle(i)">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" :style="imgStyle" />
          <div v-if="isVideoItem(visibleImages[i])" :style="playBadgeStyle"></div>
        </div>
      </div>

      <!-- Strip Split (due righe, direzioni opposte) -->
      <div v-else-if="layout === 'strip_split'" style="display:flex;flex-direction:column;gap:8px">
        <div :style="stripWrapStyle">
          <div v-for="(img, i) in evenImages" :key="'a'+i" :style="stripItemStyle(i*2)">
            <img :src="imgUrl(img)" :alt="imgAlt(img)" :style="imgStyle" />
            <div v-if="isVideoItem(evenImages[i])" :style="playBadgeStyle"></div>
          </div>
        </div>
        <div :style="stripWrapStyle">
          <div v-for="(img, i) in oddImages" :key="'b'+i" :style="stripItemStyle(i*2+1)">
            <img :src="imgUrl(img)" :alt="imgAlt(img)" :style="imgStyle" />
            <div v-if="isVideoItem(oddImages[i])" :style="playBadgeStyle"></div>
          </div>
        </div>
      </div>

      <!-- Mosaic -->
      <div v-else-if="layout === 'mosaic'" :style="mosaicStyle">
        <div v-for="(img, i) in visibleImages" :key="img.id || img.url || i" :style="mosaicItemStyle(i)">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" :style="imgStyle" />
          <div v-if="isVideoItem(visibleImages[i])" :style="playBadgeStyle"></div>
          <div v-if="isLastVisible(i)" :style="moreOverlayStyle">+{{ extraCount }}</div>
        </div>
      </div>

      <!-- Honeycomb -->
      <div v-else-if="layout === 'honeycomb'" :style="honeycombStyle">
        <div v-for="(img, i) in visibleImages" :key="img.id || img.url || i" :style="honeycombItemStyle">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" style="width:100%;height:100%;object-fit:cover" :style="{ objectPosition: objPos }" />
          <div v-if="isVideoItem(visibleImages[i])" :style="playBadgeStyle"></div>
        </div>
      </div>

      <!-- Hex Grid (tessellating) -->
      <div v-else-if="layout === 'hexgrid'" :style="hexGridContainerStyle">
        <div v-for="(img, i) in visibleImages" :key="img.id || img.url || i" :style="hexGridItemStyle(i)">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" style="width:100%;height:100%;object-fit:cover" :style="{ objectPosition: objPos }" />
          <div v-if="isVideoItem(visibleImages[i])" :style="playBadgeStyle"></div>
          <div v-if="isLastVisible(i)" :style="moreOverlayStyle">+{{ extraCount }}</div>
        </div>
      </div>

      <!-- Puzzle -->
      <div v-else-if="layout === 'puzzle'" :style="puzzleContainerStyle">
        <div v-for="(img, i) in visibleImages" :key="img.id || img.url || i" :style="puzzleItemStyle(i)">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" :style="puzzleImgStyle" />
          <div v-if="isVideoItem(visibleImages[i])" :style="playBadgeStyle"></div>
          <div v-if="isLastVisible(i)" :style="moreOverlayStyle">+{{ extraCount }}</div>
        </div>
      </div>

      <!-- Diagonal -->
      <div v-else-if="layout === 'diagonal'" :style="gridStyle">
        <div v-for="(img, i) in visibleImages" :key="img.id || img.url || i" :style="diagonalItemStyle(i)">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" :style="imgStyle" />
          <div v-if="isVideoItem(visibleImages[i])" :style="playBadgeStyle"></div>
          <div v-if="isLastVisible(i)" :style="moreOverlayStyle">+{{ extraCount }}</div>
        </div>
      </div>

      <!-- Expand (spotlight) -->
      <div v-else-if="layout === 'expand'" :style="expandStyle">
        <div v-for="(img, i) in visibleImages" :key="img.id || img.url || i" :style="expandItemStyle">
          <img :src="imgUrl(img)" :alt="imgAlt(img)" :style="expandImgStyle" />
          <div v-if="isVideoItem(visibleImages[i])" :style="playBadgeStyle"></div>
          <div v-if="isLastVisible(i)" :style="moreOverlayStyle">+{{ extraCount }}</div>
        </div>
      </div>

      <!-- Effect badges (builder only) -->
      <div v-if="hasBadges" style="display:flex;gap:4px;margin-top:4px;flex-wrap:wrap">
        <span v-if="s.entrance && s.entrance !== 'none'" :style="badgeStyle">Entrance: {{ s.entrance }}</span>
        <span v-if="continuousLabel" :style="badgeStyle">Anim: {{ continuousLabel }}</span>
        <span v-if="s.hover_effect && s.hover_effect !== 'none'" :style="badgeStyle">Hover: {{ s.hover_effect }}</span>
        <span v-if="s.filter && s.filter !== 'none'" :style="badgeStyle">Filtro: {{ s.filter }}</span>
        <span v-if="s.frame && s.frame !== 'none'" :style="badgeStyle">Cornice: {{ s.frame }}</span>
      </div>
    </template>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  images: [], layout: 'grid', layout_family: 'classic', puzzle_style: 'classic',
  columns: '3', gap: '8', img_height: '250px', object_fit: 'cover', object_position: 'center center', thumb_radius: '8',
  rows: '0', mobile_columns: '2', expand_ratio: '4', expand_shrink: '0.5', expand_speed: '500',
  parallax_height: '1500', parallax_intensity: '50',
  drift_height: '1200', drift_intensity: '60', drift_rotation: '12',
  cascade_spread: '60', cascade_overlap: '40', cascade_rotation: '8',
  metro_cell_height: '200',
  filmstrip_item_width: '280', filmstrip_center_zoom: '1.15', filmstrip_side_tilt: '35',
  filmstrip_speed: '4', filmstrip_dots: 'dots',
  strip_height: '280', strip_item_width: '300', strip_rows: '2', strip_speed: '30',
  strip_pause_hover: true, strip_direction: 'left', strip_fade_edges: true,
  entrance: 'none', entrance_stagger: '120', entrance_duration: '600',
  hover_effect: 'zoom', hover_zoom_scale: '1.08', hover_tilt_angle: '10',
  hover_magnetic_strength: '24', hover_glow_color: '', hover_glow_spread: '20',
  hover_caption: 'none', hover_caption_bg: 'rgba(0,0,0,0.6)', hover_caption_color: '#ffffff',
  continuous: '', continuous_speed: '20',
  filter: 'none', duotone_dark: '#1a1a2e', duotone_light: '#e94560', duotone_intensity: '80',
  frame: 'none', frame_color: '#ffffff', frame_inset_padding: '10',
  anim_border: 'none', anim_border_color: '#ffffff', anim_border_thickness: '2',
  lightbox: true, lightbox_animation: 'slide', lightbox_thumbs: 'none',
  more_bg: 'rgba(0,0,0,0.55)', more_color: '#ffffff', more_size: '28',
  shadow: 'none', video_preview: 'poster',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const layout = computed(() => {
  const l = s.value.layout || 'grid';
  return l === 'filmstrip' ? 'strip_coverflow' : l;
});
const cols = computed(() => Math.max(2, Math.min(6, parseInt(s.value.columns) || 3)));
const gap = computed(() => parseInt(s.value.gap) || 8);
const radius = computed(() => (v => isNaN(v) ? 8 : v)(parseInt(s.value.thumb_radius)));
const imgHeight = computed(() => s.value.img_height || '250px');
const objectFit = computed(() => s.value.object_fit || 'cover');
// Punto focale GLOBALE applicato a OGNI immagine ('' → 'center center' = resa attuale).
const objPos = computed(() => s.value.object_position || 'center center');

const images = computed(() => {
  const imgs = s.value.images;
  if (!Array.isArray(imgs)) return [];
  return imgs.filter(i => {
    if (i && i.type === 'video') return !!(i.url || i.embed || i.poster);
    return !!imgUrl(i);
  });
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
  if (img && img.type === 'video') return img.poster || '';
  return typeof img === 'string' ? img : (img?.url || '');
}
function imgAlt(img) {
  return typeof img === 'string' ? '' : (img?.alt || '');
}
function isVideoItem(img) {
  return img?.type === 'video';
}

function seededRandom(seed) {
  const x = Math.sin(seed * 127.1 + 311.7) * 43758.5453123;
  return x - Math.floor(x);
}

const imgStyle = computed(() => ({
  width: '100%',
  height: '100%',
  objectFit: objectFit.value,
  objectPosition: objPos.value,
  display: 'block',
  borderRadius: radius.value + 'px',
}));

// Per layout con item senza altezza esplicita (parallax, drift, scattered)
const imgStyleAuto = computed(() => ({
  width: '100%',
  height: 'auto',
  objectPosition: objPos.value,
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

// ─── Justified ───
const justifiedStyle = computed(() => ({
  display: 'flex',
  flexWrap: 'wrap',
  gap: gap.value + 'px',
}));

const justifiedItemStyle = computed(() => ({
  position: 'relative',
  overflow: 'hidden',
  borderRadius: radius.value + 'px',
  height: imgHeight.value,
  flexGrow: 1,
  minWidth: '120px',
  ...filterStyle.value,
}));

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

// ─── Parallax (builder preview — static depth) ───
const parallaxContainerStyle = computed(() => ({
  position: 'relative',
  height: '500px',
  overflow: 'hidden',
}));

function parallaxItemStyle(i) {
  const total = visibleImages.value.length;
  const colsNum = Math.min(4, Math.ceil(Math.sqrt(total)));
  const rowsNum = Math.ceil(total / colsNum);
  const col = i % colsNum;
  const row = Math.floor(i / colsNum);
  const cellW = 100 / colsNum;
  const cellH = 100 / rowsNum;
  const depth = seededRandom(i + 3);
  const size = 0.50 + depth * 0.45;
  const offX = (seededRandom(i + 7) - 0.5) * 15;
  const offY = (seededRandom(i + 13) - 0.5) * 15;
  const rot = (seededRandom(i + 21) - 0.5) * 8;
  const shadowBlur = Math.round(4 + depth * 20);
  const shadowAlpha = (0.1 + depth * 0.2).toFixed(2);
  const op = (0.7 + depth * 0.3).toFixed(2);
  const zi = Math.round(depth * 20);
  return {
    position: 'absolute',
    left: (col * cellW + offX) + '%',
    top: (row * cellH + offY) + '%',
    width: (cellW * size) + '%',
    transform: `rotate(${rot.toFixed(1)}deg)`,
    overflow: 'hidden',
    borderRadius: radius.value + 'px',
    boxShadow: `0 ${Math.round(shadowBlur/2)}px ${shadowBlur}px rgba(0,0,0,${shadowAlpha})`,
    opacity: op,
    zIndex: zi,
    ...filterStyle.value,
  };
}

// ─── Drift (builder preview — static, similar to parallax but with X offset) ───
const driftContainerStyle = computed(() => ({
  position: 'relative',
  height: '500px',
  overflow: 'hidden',
}));

function driftItemStyle(i) {
  const total = visibleImages.value.length;
  const colsNum = Math.min(4, Math.ceil(Math.sqrt(total)));
  const rowsNum = Math.ceil(total / colsNum);
  const col = i % colsNum;
  const row = Math.floor(i / colsNum);
  const cellW = 100 / colsNum;
  const cellH = 100 / rowsNum;
  const depth = seededRandom(i + 5);
  const size = 0.45 + depth * 0.50;
  const offX = (seededRandom(i + 11) - 0.5) * 18;
  const offY = (seededRandom(i + 17) - 0.5) * 18;
  const rot = (seededRandom(i + 23) - 0.5) * 10;
  const shadowBlur = Math.round(4 + depth * 18);
  const shadowAlpha = (0.1 + depth * 0.18).toFixed(2);
  const op = (0.75 + depth * 0.25).toFixed(2);
  const zi = Math.round(depth * 20);
  return {
    position: 'absolute',
    left: (col * cellW + offX) + '%',
    top: (row * cellH + offY) + '%',
    width: (cellW * size) + '%',
    transform: `rotate(${rot.toFixed(1)}deg)`,
    overflow: 'hidden',
    borderRadius: radius.value + 'px',
    boxShadow: `0 ${Math.round(shadowBlur/2)}px ${shadowBlur}px rgba(0,0,0,${shadowAlpha})`,
    opacity: op,
    zIndex: zi,
    ...filterStyle.value,
  };
}

// ─── Cascade (builder preview — stacked cards) ───
const cascadeContainerStyle = computed(() => {
  const total = visibleImages.value.length;
  const h = Math.max(400, total * 80 + 300);
  return {
    position: 'relative',
    height: h + 'px',
    overflow: 'hidden',
    perspective: '1200px',
  };
});

function cascadeItemStyle(i) {
  const total = visibleImages.value.length;
  const overlap = parseInt(s.value.cascade_overlap) || 40;
  const rotMax = parseInt(s.value.cascade_rotation) || 8;
  const stackOffset = i * (overlap / total);
  const rot = (seededRandom(i + 9) - 0.5) * rotMax * 2;
  const shiftX = (seededRandom(i + 15) - 0.5) * 30;
  const w = Math.max(35, 60 - (total > 6 ? i * 2 : 0));
  const h = Math.max(200, 350 - (total > 6 ? i * 15 : 0));
  const left = 50 - w / 2 + shiftX;
  const zi = total - i;
  return {
    position: 'absolute',
    left: left + '%',
    top: stackOffset + '%',
    width: w + '%',
    height: h + 'px',
    zIndex: zi,
    transform: `rotate(${rot.toFixed(1)}deg)`,
    overflow: 'hidden',
    borderRadius: radius.value + 'px',
    boxShadow: '0 8px 30px rgba(0,0,0,.2)',
    ...filterStyle.value,
  };
}

// ─── Metro (builder preview — mixed tile sizes) ───
const metroStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${cols.value}, 1fr)`,
  gridAutoRows: (parseInt(s.value.metro_cell_height) || 200) + 'px',
  gridAutoFlow: 'dense',
  gap: gap.value + 'px',
}));

function metroItemStyle(i) {
  const mod = i % 7;
  const style = {
    position: 'relative',
    overflow: 'hidden',
    borderRadius: radius.value + 'px',
    ...filterStyle.value,
  };
  if (mod === 0) {
    style.gridColumn = 'span 2';
    style.gridRow = 'span 2';
  } else if (mod === 3) {
    style.gridColumn = 'span 2';
  } else if (mod === 5) {
    style.gridRow = 'span 2';
  }
  return style;
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

// ─── Filmstrip (Coverflow) ───
const filmWidth = computed(() => Math.max(180, Math.min(450, parseInt(s.value.filmstrip_item_width) || 280)));
const filmZoom = computed(() => Math.max(1.0, Math.min(1.5, parseFloat(s.value.filmstrip_center_zoom) || 1.15)));
const filmTilt = computed(() => Math.max(0, Math.min(25, parseInt(s.value.filmstrip_side_tilt) || 8)));

const filmstripWrapStyle = computed(() => ({
  position: 'relative',
  overflow: 'hidden',
}));

const filmstripStyle = computed(() => ({
  display: 'flex',
  gap: gap.value + 'px',
  overflowX: 'hidden',
  padding: '20px 0',
  justifyContent: 'center',
}));

function filmstripItemStyle(i) {
  const total = visibleImages.value.length;
  const mid = Math.floor(total / 2);
  const dist = i - mid;
  const absD = Math.abs(dist);
  const maxD = Math.max(1, Math.floor(total / 2));
  const ratio = Math.min(absD / maxD, 1);
  const sc = filmZoom.value - (filmZoom.value - 1) * ratio;
  const ry = (dist / maxD) * filmTilt.value;
  const op = 1 - ratio * 0.25;
  const zi = 100 - Math.round(ratio * 50);
  return {
    flex: '0 0 auto',
    width: filmWidth.value + 'px',
    height: imgHeight.value,
    overflow: 'hidden',
    borderRadius: radius.value + 'px',
    transform: `perspective(800px) scale(${sc.toFixed(3)}) rotateY(${ry.toFixed(1)}deg)`,
    opacity: op.toFixed(2),
    zIndex: zi,
    transition: 'transform .35s ease',
    ...filterStyle.value,
  };
}

function filmArrowStyle(side) {
  return {
    position: 'absolute',
    top: '50%',
    transform: 'translateY(-50%)',
    [side === 'left' ? 'left' : 'right']: '4px',
    width: '28px',
    height: '28px',
    borderRadius: '50%',
    background: 'rgba(0,0,0,.35)',
    color: '#fff',
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    fontSize: '16px',
    lineHeight: '1',
    cursor: 'default',
    opacity: '0.6',
    pointerEvents: 'none',
  };
}

const filmDotsStyle = computed(() => s.value.filmstrip_dots || 'dots');
const filmDotsColor = computed(() => s.value.filmstrip_dots_color || '');

const filmDotsContainerStyle = computed(() => {
  const isLines = filmDotsStyle.value === 'lines';
  return {
    display: 'flex',
    justifyContent: 'center',
    gap: isLines ? '3px' : '4px',
    padding: '4px 0',
    alignItems: isLines ? 'center' : undefined,
  };
});

function filmDotStyle(i) {
  const total = visibleImages.value.length;
  const mid = Math.floor(total / 2);
  const isActive = i === mid;
  const isLines = filmDotsStyle.value === 'lines';
  const activeClr = filmDotsColor.value || 'rgba(0,0,0,.7)';
  const inactClr = filmDotsColor.value || 'rgba(0,0,0,.2)';
  if (isLines) return {
    width: isActive ? '18px' : '10px',
    height: '2px',
    borderRadius: '1px',
    background: isActive ? activeClr : inactClr,
    opacity: isActive ? 1 : (filmDotsColor.value ? 0.35 : 1),
    transition: 'width .2s ease',
  };
  return {
    width: '6px',
    height: '6px',
    borderRadius: '50%',
    background: isActive ? activeClr : inactClr,
    opacity: isActive ? 1 : (filmDotsColor.value ? 0.35 : 1),
    transform: isActive ? 'scale(1.3)' : 'scale(1)',
  };
}

// Progress bar preview
const filmProgressWrapStyle = computed(() => ({
  padding: '6px 0',
  width: '60%',
  maxWidth: '200px',
  margin: '0 auto',
}));
const filmProgressTrackStyle = computed(() => ({
  height: '3px',
  borderRadius: '2px',
  background: filmDotsColor.value || 'rgba(0,0,0,.15)',
  opacity: filmDotsColor.value ? 0.25 : 1,
  overflow: 'hidden',
  position: 'relative',
}));
const filmProgressFillStyle = computed(() => {
  const total = visibleImages.value.length;
  const mid = Math.floor(total / 2);
  const pct = total > 1 ? (mid / (total - 1)) * 100 : 100;
  return {
    height: '100%',
    borderRadius: '2px',
    background: filmDotsColor.value || 'rgba(0,0,0,.55)',
    width: pct + '%',
  };
});

// Fraction preview
const filmFractionStyle = computed(() => ({
  textAlign: 'center',
  padding: '4px 0',
  fontSize: '11px',
  fontWeight: '600',
  color: filmDotsColor.value || 'rgba(0,0,0,.5)',
  fontVariantNumeric: 'tabular-nums',
  letterSpacing: '0.05em',
}));
const filmFractionText = computed(() => {
  const total = visibleImages.value.length;
  const mid = Math.floor(total / 2) + 1;
  return mid + ' / ' + total;
});

// ─── Strip (nastro) ───
const stripHeight = computed(() => Math.max(150, Math.min(500, parseInt(s.value.strip_height) || 280)));
const stripItemW = computed(() => Math.max(150, Math.min(500, parseInt(s.value.strip_item_width) || 300)));
const stripRows = computed(() => Math.max(2, Math.min(3, parseInt(s.value.strip_rows) || 2)));
const stripFade = computed(() => !!s.value.strip_fade_edges);

const stripWrapStyle = computed(() => {
  const base = {
    display: 'flex',
    gap: gap.value + 'px',
    overflow: 'hidden',
    alignItems: 'center',
  };
  if (stripFade.value) {
    base.maskImage = 'linear-gradient(to right, transparent, black 6%, black 94%, transparent)';
    base.webkitMaskImage = base.maskImage;
  }
  return base;
});

function stripItemStyle() {
  return {
    flex: '0 0 auto',
    width: stripItemW.value + 'px',
    height: stripHeight.value + 'px',
    overflow: 'hidden',
    borderRadius: radius.value + 'px',
    ...filterStyle.value,
  };
}

function stripCollageItemStyle(i) {
  const base = stripHeight.value;
  const variation = 50;
  const rand = seededRandom(i + 42);
  const h = Math.round(base - variation + rand * variation * 2);
  return {
    flex: '0 0 auto',
    width: stripItemW.value + 'px',
    height: h + 'px',
    overflow: 'hidden',
    borderRadius: radius.value + 'px',
    ...filterStyle.value,
  };
}

const stripMultiStyle = computed(() => {
  const base = {
    display: 'grid',
    gridTemplateRows: `repeat(${stripRows.value}, 1fr)`,
    gridAutoFlow: 'column',
    gridAutoColumns: stripItemW.value + 'px',
    gap: gap.value + 'px',
    overflow: 'hidden',
    height: stripHeight.value + 'px',
  };
  if (stripFade.value) {
    base.maskImage = 'linear-gradient(to right, transparent, black 6%, black 94%, transparent)';
    base.webkitMaskImage = base.maskImage;
  }
  return base;
});

const stripMultiItemStyle = computed(() => ({
  overflow: 'hidden',
  borderRadius: radius.value + 'px',
  ...filterStyle.value,
}));

const evenImages = computed(() => visibleImages.value.filter((_, i) => i % 2 === 0));
const oddImages = computed(() => visibleImages.value.filter((_, i) => i % 2 !== 0));

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
  objectPosition: objPos.value,
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

// ─── Expand (spotlight) — builder preview: griglia statica, effetto solo frontend ───
const expandStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${cols.value}, 1fr)`,
  gap: gap.value + 'px',
}));

const expandItemStyle = computed(() => ({
  position: 'relative',
  overflow: 'hidden',
  borderRadius: radius.value + 'px',
  height: imgHeight.value,
  ...filterStyle.value,
}));

const expandImgStyle = computed(() => ({
  width: '100%',
  height: '100%',
  objectFit: 'cover',
  objectPosition: objPos.value,
  display: 'block',
  borderRadius: radius.value + 'px',
}));

// ─── Play badge (video) ───
const playSvg = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white'%3E%3Cpolygon points='8,5 19,12 8,19'/%3E%3C/svg%3E";
const playBadgeStyle = {
  position: 'absolute',
  inset: '0',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  pointerEvents: 'none',
  zIndex: '2',
  backgroundImage: `radial-gradient(circle, rgba(0,0,0,0.4) 0%, transparent 60%), url("${playSvg}")`,
  backgroundPosition: 'center, center',
  backgroundRepeat: 'no-repeat, no-repeat',
  backgroundSize: '100% 100%, 28px 28px',
};

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
  pointerEvents: 'none',
}));

// ─── Badges ───
const continuousLabel = computed(() => {
  const c = s.value.continuous;
  if (!c || c === 'none') return '';
  // comma-separated string from multi_pills
  return c;
});

const hasBadges = computed(() =>
  (s.value.entrance && s.value.entrance !== 'none') ||
  !!continuousLabel.value ||
  (s.value.hover_effect && s.value.hover_effect !== 'none') ||
  (s.value.filter && s.value.filter !== 'none') ||
  (s.value.frame && s.value.frame !== 'none')
);

const badgeStyle = {
  background: 'color-mix(in srgb, var(--olo-color-primary, #e1474f) 85%, transparent)',
  color: '#fff',
  fontSize: '10px',
  padding: '2px 6px',
  borderRadius: '4px',
  lineHeight: '1.3',
};
</script>

<style scoped>
</style>
