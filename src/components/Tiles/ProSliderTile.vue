<template>
  <div ref="rootEl" class="mps-preview" :class="{ 'mps-clean': isClean }" :style="containerStyle">
    <!-- Global background (behind slide bg) -->
    <div v-if="hasGlobalBg" class="mps-preview-bg" :style="globalBgStyle">
      <img v-if="globalBg.type === 'image' && globalBg.image" :src="globalBg.image" class="mps-bg-media" draggable="false" />
      <video v-else-if="globalBg.type === 'video' && globalBg.video" :src="globalBg.video" class="mps-bg-media" muted autoplay loop playsinline></video>
    </div>

    <!-- ═══ Slide track: all slides stacked, only active visible ═══ -->
    <div
      v-for="(slide, si) in slides" :key="slide.id || si"
      :class="['mps-slide', { 'mps-slide--active': si === currentIdx, 'mps-slide--prev': si === prevIdx }]"
      :style="slideTransitionStyle(si)"
    >
      <!-- Slide background -->
      <div class="mps-slide-bg" :style="slideBgColorStyle(slide)">
        <img
          v-if="slideBgType(slide) === 'image' && slideBgImage(slide)"
          :src="slideBgImage(slide)"
          :class="['mps-bg-media', slideBgKenBurnsClass(slide, si)]"
          :style="slideBgKenBurnsStyle(slide)"
          draggable="false"
        />
        <video
          v-else-if="slideBgType(slide) === 'video' && slideBgVideo(slide) && !isSlideBgYouTube(slide)"
          :src="slideBgVideo(slide)"
          class="mps-bg-media"
          muted autoplay loop playsinline
        ></video>
        <iframe
          v-else-if="slideBgType(slide) === 'video' && slideBgVideo(slide) && isSlideBgYouTube(slide)"
          :src="slideYouTubeUrl(slide)"
          class="mps-bg-media mps-bg-youtube"
          allow="autoplay; encrypted-media"
          allowfullscreen
        ></iframe>
        <div v-else-if="slideBgType(slide) === 'gradient'" class="mps-bg-fill" :style="slideGradientStyle(slide)"></div>
      </div>

      <!-- Slide overlay -->
      <div v-if="slideOverlayOpacity(slide) > 0 && slideBgType(slide) !== 'transparent'" class="mps-preview-overlay" :style="{ background: slideOverlayColor(slide), opacity: slideOverlayOpacity(slide) }"></div>

      <!-- Slide layers -->
      <div
        v-for="layer in (slide.layers || [])" :key="layer.id"
        :class="['mps-layer', { 'mps-layer--animate': si === currentIdx }]"
        :style="fullLayerStyle(layer)"
      >
        <component v-if="layer.type === 'text'" :is="layer.tag || 'h2'" :style="fullTextStyle(layer)" class="mps-layer-text" v-html="layer.content || ''"></component>
        <span v-else-if="layer.type === 'button'" :style="fullButtonStyle(layer)">{{ layer.content || 'Button' }}</span>
        <img v-else-if="layer.type === 'image' && layer.imageSrc" :src="layer.imageSrc" class="mps-layer-img" :style="imgLayerStyle(layer)" draggable="false" />
        <span v-else-if="layer.type === 'icon'" class="mps-layer-icon" :style="iconStyle(layer)" v-html="getIconSvg(layer.iconName)"></span>
        <div v-else-if="layer.type === 'shape'" class="mps-layer-shape" :style="shapeInnerStyle(layer)"></div>
        <video
          v-else-if="layer.type === 'video' && layer.videoSrc && !isLayerYouTube(layer)"
          :src="layer.videoSrc"
          class="mps-layer-img"
          muted autoplay loop playsinline
          style="object-fit:cover;"
        ></video>
      </div>
    </div>

    <!-- Global layers (back) -->
    <div v-if="backGlobalLayers.length" class="mps-global-layers mps-global-back">
      <div v-for="gl in backGlobalLayers" :key="gl.id" class="mps-layer" :style="fullLayerStyle(gl)">
        <component v-if="gl.type === 'text'" :is="gl.tag || 'h2'" :style="fullTextStyle(gl)" class="mps-layer-text" v-html="gl.content || ''"></component>
        <span v-else-if="gl.type === 'button'" :style="fullButtonStyle(gl)">{{ gl.content || 'Button' }}</span>
        <img v-else-if="gl.type === 'image' && gl.imageSrc" :src="gl.imageSrc" class="mps-layer-img" :style="imgLayerStyle(gl)" draggable="false" />
        <span v-else-if="gl.type === 'icon'" class="mps-layer-icon" :style="iconStyle(gl)" v-html="getIconSvg(gl.iconName)"></span>
        <div v-else-if="gl.type === 'shape'" class="mps-layer-shape" :style="shapeInnerStyle(gl)"></div>
      </div>
    </div>

    <!-- Global layers (front) -->
    <div v-if="frontGlobalLayers.length" class="mps-global-layers mps-global-front">
      <div v-for="gl in frontGlobalLayers" :key="gl.id" class="mps-layer" :style="fullLayerStyle(gl)">
        <component v-if="gl.type === 'text'" :is="gl.tag || 'h2'" :style="fullTextStyle(gl)" class="mps-layer-text" v-html="gl.content || ''"></component>
        <span v-else-if="gl.type === 'button'" :style="fullButtonStyle(gl)">{{ gl.content || 'Button' }}</span>
        <img v-else-if="gl.type === 'image' && gl.imageSrc" :src="gl.imageSrc" class="mps-layer-img" :style="imgLayerStyle(gl)" draggable="false" />
        <span v-else-if="gl.type === 'icon'" class="mps-layer-icon" :style="iconStyle(gl)" v-html="getIconSvg(gl.iconName)"></span>
        <div v-else-if="gl.type === 'shape'" class="mps-layer-shape" :style="shapeInnerStyle(gl)"></div>
      </div>
    </div>

    <!-- Badge (hidden in clean mode) -->
    <div v-if="!isClean" class="mps-preview-badge">
      ProSlider &middot; {{ slideCount }} slide{{ slideCount !== 1 ? 's' : '' }}
      <span v-if="slideCount > 1"> &middot; {{ currentIdx + 1 }}/{{ slideCount }}</span>
    </div>

    <!-- Arrows (functional) -->
    <div v-if="s.showArrows !== false && slideCount > 1" class="mps-nav-arrows">
      <button :class="['mps-arrow', 'mps-arrow-prev', arrowClass]" @click.stop="goPrev">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
      </button>
      <button :class="['mps-arrow', 'mps-arrow-next', arrowClass]" @click.stop="goNext">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 6 15 12 9 18"/></svg>
      </button>
    </div>

    <!-- Dots (functional) -->
    <div v-if="s.showDots !== false && slideCount > 1" :class="['mps-nav-dots', dotClass]">
      <span
        v-for="i in slideCount" :key="i"
        :class="['mps-dot', i - 1 === currentIdx ? 'mps-dot--active' : '']"
        @click.stop="goTo(i - 1)"
      >
        <template v-if="dotStyleVal === 'numbers'">{{ i }}</template>
      </span>
    </div>

    <!-- Progress bar (animated) -->
    <div v-if="s.showProgressBar && slideCount > 1" class="mps-progress" :style="{ height: (s.progressBarHeight || 3) + 'px' }">
      <div class="mps-progress-fill" :style="progressFillStyle"></div>
    </div>

    <!-- Thumbnail preview (functional) -->
    <div v-if="s.showThumbs && slideCount > 1" :class="['mps-thumbs', 'mps-thumbs--' + (s.thumbPosition || 'bottom')]">
      <div
        v-for="(sl, si) in slides" :key="si"
        :class="['mps-thumb', si === currentIdx ? 'mps-thumb--active' : '']"
        @click.stop="goTo(si)"
      >
        <img v-if="sl.background?.type === 'image' && sl.background?.image" :src="sl.background.image" draggable="false" />
        <span v-else class="mps-thumb-num">{{ si + 1 }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch, onMounted, onBeforeUnmount } from 'vue';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
import { normalizeHeight, resolveHeightPx } from '@/config/elements/proslider.js';
import { useBuilderStore } from '@/stores/builder';

const builderStore = useBuilderStore();
const isClean = computed(() => builderStore.cleanMode);

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

// ── Measure container width ──
const rootEl = ref(null);
const containerWidth = ref(800);
let _ro = null;
onMounted(() => {
  if (rootEl.value) {
    containerWidth.value = rootEl.value.parentElement?.offsetWidth || rootEl.value.offsetWidth || 800;
    _ro = new ResizeObserver(entries => {
      for (const e of entries) containerWidth.value = e.contentRect.width || 800;
    });
    _ro.observe(rootEl.value.parentElement || rootEl.value);
  }
  startAutoplay();
});
onBeforeUnmount(() => {
  _ro?.disconnect();
  stopAutoplay();
});

const defaults = {
  height: { mode: 'px', value: 600 },
  showArrows: true,
  arrowStyle: 'minimal',
  showDots: true,
  dotStyle: 'circles',
  showProgressBar: false,
  progressBarColor: '',
  progressBarHeight: 3,
  showThumbs: false,
  thumbPosition: 'bottom',
  showTabs: false,
  autoplay: true,
  slides: [],
  globalLayers: [],
  globalBackground: null,
  transition: 'fade',
  transitionDuration: 700,
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const slides = computed(() => {
  const sl = s.value.slides;
  return Array.isArray(sl) && sl.length ? sl : [];
});
const slideCount = computed(() => slides.value.length);

// ── Slide navigation ──
const currentIdx = ref(0);
const prevIdx = ref(-1);
const isTransitioning = ref(false);

// Clamp currentIdx when slides change
watch(slideCount, (n) => {
  if (currentIdx.value >= n) currentIdx.value = Math.max(0, n - 1);
});

function goTo(idx) {
  if (idx === currentIdx.value || isTransitioning.value) return;
  prevIdx.value = currentIdx.value;
  currentIdx.value = idx;
  isTransitioning.value = true;
  resetAutoplay();
  setTimeout(() => { isTransitioning.value = false; prevIdx.value = -1; }, parseInt(s.value.transitionDuration) || 700);
}
function goNext() { goTo((currentIdx.value + 1) % slideCount.value); }
function goPrev() { goTo((currentIdx.value - 1 + slideCount.value) % slideCount.value); }

// ── Autoplay ──
let autoplayTimer = null;
function startAutoplay() {
  stopAutoplay();
  if (s.value.autoplay !== false && slideCount.value > 1) {
    const dur = firstSlideDuration.value;
    autoplayTimer = setInterval(goNext, dur);
  }
}
function stopAutoplay() {
  if (autoplayTimer) { clearInterval(autoplayTimer); autoplayTimer = null; }
}
function resetAutoplay() {
  stopAutoplay();
  startAutoplay();
}

const firstSlideDuration = computed(() => {
  const slide = slides.value[currentIdx.value];
  return (slide?.duration || 5000);
});

// Restart autoplay when settings change
watch(() => s.value.autoplay, () => { if (s.value.autoplay !== false) startAutoplay(); else stopAutoplay(); });
watch(slideCount, () => { resetAutoplay(); });

// ── Slide transition style ──
const transType = computed(() => s.value.transition || 'fade');
const transDur = computed(() => (parseInt(s.value.transitionDuration) || 700) + 'ms');

function slideTransitionStyle(si) {
  const isActive = si === currentIdx.value;
  const isPrev = si === prevIdx.value;
  const dur = transDur.value;
  const tt = transType.value;

  if (tt === 'slide') {
    if (isActive) return { opacity: 1, transform: 'translateX(0)', transition: `transform ${dur} ease, opacity ${dur} ease`, zIndex: 2 };
    if (isPrev) {
      const dir = currentIdx.value > prevIdx.value ? '-100%' : '100%';
      return { opacity: 0, transform: `translateX(${dir})`, transition: `transform ${dur} ease, opacity ${dur} ease`, zIndex: 1 };
    }
    return { opacity: 0, transform: 'translateX(100%)', zIndex: 0 };
  }
  // Default: fade
  if (isActive) return { opacity: 1, transition: `opacity ${dur} ease`, zIndex: 2 };
  if (isPrev) return { opacity: 0, transition: `opacity ${dur} ease`, zIndex: 1 };
  return { opacity: 0, zIndex: 0 };
}

// ── Container ──
const sliderHeight = computed(() => {
  const h = normalizeHeight(s.value.height);
  return resolveHeightPx(h, containerWidth.value) || 600;
});
const containerStyle = computed(() => ({
  height: sliderHeight.value + 'px',
  background: '#000',
}));

// ── Global background ──
const globalBg = computed(() => s.value.globalBackground);
const hasGlobalBg = computed(() => {
  const gb = globalBg.value;
  if (!gb) return false;
  return gb.type === 'image' || gb.type === 'video' || gb.type === 'gradient' || (gb.type === 'color' && gb.color);
});
const globalBgStyle = computed(() => {
  const gb = globalBg.value;
  if (!gb) return {};
  if (gb.type === 'gradient') return { background: `linear-gradient(${gb.gradientAngle || 180}deg, ${gb.gradientFrom || '#1e293b'}, ${gb.gradientTo || '#0f172a'})` };
  if (gb.type === 'color' && gb.color) return { background: gb.color };
  return {};
});

// ── Per-slide background helpers ──
function slideBgType(slide) { return slide?.background?.type || 'color'; }
function slideBgImage(slide) { return slide?.background?.image || ''; }
function slideBgVideo(slide) { return slide?.background?.video || ''; }
function slideBgColorStyle(slide) {
  const bg = slide?.background || {};
  if (['transparent', 'image', 'video', 'gradient'].includes(bg.type)) return {};
  return { background: bg.color || '#1e293b' };
}
function slideGradientStyle(slide) {
  const bg = slide?.background || {};
  return { background: `linear-gradient(${bg.gradientAngle || 180}deg, ${bg.gradientFrom || '#1e293b'}, ${bg.gradientTo || '#0f172a'})` };
}
function isSlideBgYouTube(slide) {
  return /youtube\.com|youtu\.be/i.test(slide?.background?.video || '');
}
function slideYouTubeUrl(slide) {
  const url = slide?.background?.video || '';
  const m = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
  const vid = m ? m[1] : '';
  if (!vid) return '';
  return `https://www.youtube-nocookie.com/embed/${vid}?autoplay=1&mute=1&loop=1&playlist=${vid}&controls=0&showinfo=0&rel=0&modestbranding=1&playsinline=1`;
}
function slideOverlayColor(slide) { return slide?.background?.overlay || '#000'; }
function slideOverlayOpacity(slide) { return slide?.background?.overlayOpacity ?? 0.3; }

// Ken Burns per slide
function slideBgKenBurnsClass(slide, si) {
  const bg = slide?.background || {};
  if (!bg.kenBurns || si !== currentIdx.value) return '';
  return bg.kenBurnsDirection === 'out' ? 'mps-kenburns-out' : 'mps-kenburns-in';
}
function slideBgKenBurnsStyle(slide) {
  const bg = slide?.background || {};
  if (!bg.kenBurns) return {};
  return { '--kb-scale': bg.kenBurnsScale || 1.2, '--kb-dur': ((bg.kenBurnsDuration || 8000) / 1000) + 's' };
}

function isLayerYouTube(layer) { return /youtube\.com|youtu\.be/i.test(layer.videoSrc || ''); }

// Global layers
const backGlobalLayers = computed(() => (s.value.globalLayers || []).filter(gl => gl.globalPosition === 'back'));
const frontGlobalLayers = computed(() => (s.value.globalLayers || []).filter(gl => (gl.globalPosition || 'front') !== 'back'));

// Arrow & dot styles
const arrowClass = computed(() => 'mps-arrow--' + (s.value.arrowStyle || 'minimal'));
const dotStyleVal = computed(() => s.value.dotStyle || 'circles');
const dotClass = computed(() => 'mps-dots--' + dotStyleVal.value);

// Progress bar
const progressFillStyle = computed(() => ({
  background: s.value.progressBarColor || 'var(--olo-color-primary, #e1474f)',
  width: slideCount.value > 0 ? ((currentIdx.value + 1) / slideCount.value * 100) + '%' : '0%',
  transition: 'width 0.5s ease',
}));

// ─── Layer style builders ─────────────────────────────────

function fullLayerStyle(l) {
  const st = {
    position: 'absolute',
    left: l.x + '%',
    top: l.y + '%',
    zIndex: 2,
    boxSizing: 'border-box',
  };
  if (l.type === 'image' && l.width === 'auto' && l.height === 'auto') {
    st.width = '100%'; st.height = '100%';
  } else {
    if (l.width !== undefined && l.width !== 'auto') st.width = l.width + '%';
    if (l.height !== undefined && l.height !== 'auto') st.height = l.height + '%';
  }
  if (l.opacity !== undefined && l.opacity !== null && l.opacity !== 100) {
    st.opacity = (parseFloat(l.opacity) || 100) / 100;
  }
  if (l.blendMode && l.blendMode !== 'normal') st.mixBlendMode = l.blendMode;
  if (l.borderRadiusLinked === false) {
    const tl = l.borderRadiusTL || 0, tr = l.borderRadiusTR || 0, br = l.borderRadiusBR || 0, bl = l.borderRadiusBL || 0;
    if (tl || tr || br || bl) { st.borderRadius = `${tl}px ${tr}px ${br}px ${bl}px`; st.overflow = 'hidden'; }
  } else if (l.borderRadius) {
    st.borderRadius = l.borderRadius + 'px'; st.overflow = 'hidden';
  }
  if (l.borderWidthLinked === false) {
    const bs = l.borderStyle || 'solid', bc = l.borderColor || '#fff';
    if (l.borderWidthTop) st.borderTop = `${l.borderWidthTop}px ${bs} ${bc}`;
    if (l.borderWidthRight) st.borderRight = `${l.borderWidthRight}px ${bs} ${bc}`;
    if (l.borderWidthBottom) st.borderBottom = `${l.borderWidthBottom}px ${bs} ${bc}`;
    if (l.borderWidthLeft) st.borderLeft = `${l.borderWidthLeft}px ${bs} ${bc}`;
  } else if (l.borderWidth) {
    st.border = `${l.borderWidth}px ${l.borderStyle || 'solid'} ${l.borderColor || '#fff'}`;
  }
  if (l.type === 'shape') {
    if (l.shapeGradient) {
      st.background = `linear-gradient(${l.shapeGradient.angle || 180}deg, ${l.shapeGradient.from || 'var(--olo-color-primary, #e1474f)'}, ${l.shapeGradient.to || 'var(--olo-color-accent, #f4a23b)'})`;
    } else {
      st.backgroundColor = l.bgColor || 'var(--olo-color-primary, #e1474f)';
    }
  } else if (l.type === 'button') {
    st.backgroundColor = l.bgColor || '#2563eb';
  } else if (l.bgColor) {
    st.backgroundColor = l.bgColor;
  }
  if (l.type !== 'image' && l.type !== 'video') {
    if (l.paddingLinked === false) {
      const pt = l.paddingTop || 0, pr = l.paddingRight || 0, pb = l.paddingBottom || 0, pl = l.paddingLeft || 0;
      if (pt || pr || pb || pl) st.padding = `${pt}px ${pr}px ${pb}px ${pl}px`;
    } else if (l.padding) {
      st.padding = l.type === 'button' ? `${l.padding}px ${l.padding * 2}px` : l.padding + 'px';
    }
  }
  if (l.boxShadow) {
    const sh = l.boxShadow;
    st.boxShadow = `${sh.x || 0}px ${sh.y || 4}px ${sh.blur || 10}px ${sh.spread || 0}px ${sh.color || 'rgba(0,0,0,0.3)'}`;
  }
  if (l.type === 'image' || l.type === 'video') {
    const fp = [];
    if ((l.filterBrightness ?? 100) !== 100) fp.push(`brightness(${l.filterBrightness}%)`);
    if ((l.filterContrast ?? 100) !== 100) fp.push(`contrast(${l.filterContrast}%)`);
    if ((l.filterSaturate ?? 100) !== 100) fp.push(`saturate(${l.filterSaturate}%)`);
    if ((l.filterGrayscale ?? 0) > 0) fp.push(`grayscale(${l.filterGrayscale}%)`);
    if ((l.filterHueRotate ?? 0) > 0) fp.push(`hue-rotate(${l.filterHueRotate}deg)`);
    if ((l.filterBlur ?? 0) > 0) fp.push(`blur(${l.filterBlur}px)`);
    if ((l.filterSepia ?? 0) > 0) fp.push(`sepia(${l.filterSepia}%)`);
    if ((l.filterInvert ?? 0) > 0) fp.push(`invert(${l.filterInvert}%)`);
    if (fp.length) st.filter = fp.join(' ');
  }
  const bfp = [];
  if ((l.backdropBlur ?? 0) > 0) bfp.push(`blur(${l.backdropBlur}px)`);
  if ((l.backdropBrightness ?? 100) !== 100) bfp.push(`brightness(${l.backdropBrightness}%)`);
  if ((l.backdropGrayscale ?? 0) > 0) bfp.push(`grayscale(${l.backdropGrayscale}%)`);
  if (bfp.length) {
    const bfv = bfp.join(' ');
    st.backdropFilter = bfv;
    st.webkitBackdropFilter = bfv;
  }
  return st;
}

function fullTextStyle(l) {
  const st = {
    fontSize: (l.fontSize || 24) + 'px',
    fontWeight: l.fontWeight || '700',
    color: l.color || '#fff',
    textAlign: l.textAlign || 'left',
    margin: 0,
    lineHeight: l.lineHeight || 1.2,
  };
  if (l.fontStyle && l.fontStyle !== 'normal') st.fontStyle = l.fontStyle;
  if (l.fontFamily) st.fontFamily = `'${l.fontFamily}', sans-serif`;
  if (l.letterSpacing) st.letterSpacing = l.letterSpacing + 'px';
  if (l.textTransform && l.textTransform !== 'none') st.textTransform = l.textTransform;
  if (l.textDecoration && l.textDecoration !== 'none') st.textDecoration = l.textDecoration;
  if (l.textShadow) {
    const ts = l.textShadow;
    st.textShadow = `${ts.x || 2}px ${ts.y || 2}px ${ts.blur || 4}px ${ts.color || '#000'}`;
  }
  if (l.textStrokeWidth) {
    st.webkitTextStroke = `${l.textStrokeWidth}px ${l.textStrokeColor || '#000'}`;
  }
  return st;
}

function fullButtonStyle(l) {
  const st = fullTextStyle(l);
  st.display = 'inline-block';
  st.textDecoration = 'none';
  st.cursor = 'pointer';
  st.whiteSpace = 'nowrap';
  return st;
}

function imgLayerStyle(l) {
  return { width: '100%', height: '100%', objectFit: l.objectFit || 'cover', objectPosition: l.objectPosition || 'center' };
}

function iconStyle(l) {
  return { width: (l.fontSize || 24) + 'px', height: (l.fontSize || 24) + 'px', color: l.color || '#fff', display: 'inline-flex', alignItems: 'center', justifyContent: 'center' };
}

function shapeInnerStyle(l) {
  return { width: '100%', height: '100%', minWidth: '40px', minHeight: '40px' };
}

function getIconSvg(name) {
  return iconsSvg[name] || iconsSvg['star'] || '';
}
</script>

<style scoped>
/* Container */
.mps-preview {
  position: relative;
  overflow: hidden;
  width: 100%;
}

/* ── Slides (stacked) ── */
.mps-slide {
  position: absolute;
  inset: 0;
  opacity: 0;
  z-index: 0;
  overflow: hidden;
}
.mps-slide--active {
  opacity: 1;
  z-index: 2;
}

/* Background layers */
.mps-preview-bg {
  position: absolute;
  inset: 0;
  overflow: hidden;
}
.mps-slide-bg {
  position: absolute;
  inset: 0;
  overflow: hidden;
}
.mps-bg-media {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}
.mps-bg-youtube {
  border: 0;
  pointer-events: none;
}
.mps-bg-fill {
  width: 100%;
  height: 100%;
}

/* Ken Burns */
.mps-kenburns-in {
  animation: kbIn var(--kb-dur, 8s) ease-out infinite alternate;
}
.mps-kenburns-out {
  animation: kbOut var(--kb-dur, 8s) ease-out infinite alternate;
}
@keyframes kbIn {
  from { transform: scale(1); }
  to   { transform: scale(var(--kb-scale, 1.2)); }
}
@keyframes kbOut {
  from { transform: scale(var(--kb-scale, 1.2)); }
  to   { transform: scale(1); }
}

/* Overlay */
.mps-preview-overlay {
  position: absolute;
  inset: 0;
  z-index: 1;
}

/* Global layers */
.mps-global-layers {
  position: absolute;
  inset: 0;
  pointer-events: none;
}
.mps-global-back { z-index: 0; }
.mps-global-front { z-index: 5; }

/* Layers */
.mps-layer {
  pointer-events: none;
  box-sizing: border-box;
}
.mps-layer-text {
  overflow: hidden;
  text-overflow: ellipsis;
}
.mps-layer-img {
  width: 100%;
  height: 100%;
  display: block;
}
.mps-layer-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.mps-layer-icon :deep(svg) {
  width: 100%;
  height: 100%;
  fill: currentColor;
  stroke: currentColor;
}
.mps-layer-shape {
  width: 100%;
  height: 100%;
  min-height: 20px;
}

/* Layer entrance animation */
.mps-layer--animate {
  animation: mpsLayerIn 0.6s ease both;
}
@keyframes mpsLayerIn {
  from { opacity: 0; transform: translateY(20px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* Badge */
.mps-preview-badge {
  position: absolute;
  top: 8px;
  right: 8px;
  background: rgba(0,0,0,0.5);
  color: rgba(255,255,255,0.8);
  font-size: 10px;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 4px;
  z-index: 20;
  pointer-events: none;
}
.mps-clean .mps-preview-badge { display: none; }

/* ─── Arrows ─── */
.mps-nav-arrows {
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  display: flex;
  justify-content: space-between;
  padding: 0 16px;
  transform: translateY(-50%);
  z-index: 10;
  pointer-events: none;
}
.mps-arrow {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 44px;
  height: 44px;
  border-radius: 50%;
  border: none;
  background: rgba(0,0,0,0.4);
  color: #fff;
  cursor: pointer;
  pointer-events: auto;
  transition: background 0.25s, opacity 0.25s;
  opacity: 0.7;
  padding: 0;
}
.mps-arrow:hover { background: rgba(0,0,0,0.7); opacity: 1; }
.mps-arrow:focus-visible,
.mps-dot:focus-visible,
.mps-thumb:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
.mps-arrow--rounded { background: rgba(255,255,255,0.2); backdrop-filter: blur(8px); }
.mps-arrow--rounded:hover { background: rgba(255,255,255,0.35); }
.mps-arrow--boxed { border-radius: 4px; background: rgba(0,0,0,0.6); }
.mps-arrow--boxed:hover { background: rgba(0,0,0,0.85); }
.mps-arrow--outline { background: transparent; border: 2px solid rgba(255,255,255,0.7); }
.mps-arrow--outline:hover { border-color: #fff; background: rgba(255,255,255,0.1); }

/* ─── Dots ─── */
.mps-nav-dots {
  position: absolute;
  bottom: 16px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 8px;
  z-index: 10;
}
.mps-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  border: 2px solid rgba(255,255,255,0.5);
  background: transparent;
  cursor: pointer;
  transition: background 0.25s, border-color 0.25s;
}
.mps-dot:hover { border-color: #fff; }
.mps-dot--active { background: #fff; border-color: #fff; }
.mps-dots--bars .mps-dot { width: 24px; height: 4px; border-radius: 2px; border: none; background: rgba(255,255,255,0.4); }
.mps-dots--bars .mps-dot:hover { background: rgba(255,255,255,0.7); }
.mps-dots--bars .mps-dot--active { background: #fff; }
.mps-dots--numbers .mps-dot { width: auto; height: auto; border-radius: 0; border: none; background: transparent; color: rgba(255,255,255,0.5); font-size: 13px; font-weight: 600; padding: 2px 6px; }
.mps-dots--numbers .mps-dot:hover { color: rgba(255,255,255,0.8); }
.mps-dots--numbers .mps-dot--active { color: #fff; border-bottom: 2px solid #fff; }
.mps-dots--dash .mps-dot { width: 32px; height: 3px; border-radius: 0; border: none; background: rgba(255,255,255,0.3); }
.mps-dots--dash .mps-dot:hover { background: rgba(255,255,255,0.6); }
.mps-dots--dash .mps-dot--active { background: #fff; }

/* ─── Progress Bar ─── */
.mps-progress {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  z-index: 10;
  background: rgba(255,255,255,0.15);
}
.mps-progress-fill {
  height: 100%;
}

/* ─── Thumbnails ─── */
.mps-thumbs {
  display: flex;
  gap: 6px;
  padding: 8px;
  z-index: 10;
}
.mps-thumbs--bottom,
.mps-thumbs--top {
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
  justify-content: center;
}
.mps-thumbs--bottom { bottom: 40px; }
.mps-thumbs--top { top: 8px; }
.mps-thumbs--left,
.mps-thumbs--right {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  flex-direction: column;
}
.mps-thumbs--left { left: 8px; }
.mps-thumbs--right { right: 8px; }
.mps-thumb {
  width: 60px;
  height: 40px;
  border-radius: 4px;
  overflow: hidden;
  border: 2px solid rgba(255,255,255,0.4);
  cursor: pointer;
  transition: border-color 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0,0,0,0.4);
}
.mps-thumb--active { border-color: #fff; }
.mps-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.mps-thumb-num {
  color: rgba(255,255,255,0.7);
  font-size: 12px;
  font-weight: 600;
}
</style>
