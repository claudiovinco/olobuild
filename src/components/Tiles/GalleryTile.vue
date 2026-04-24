<template>
  <div v-if="images.length > 0">
    <!-- Layout badge -->
    <div v-if="s.layout !== 'grid'" style="display:inline-block;margin-bottom:6px;padding:2px 8px;background:rgba(99,102,241,0.25);border-radius:3px;font-size:9px;color:#a5b4fc;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">
      {{ s.layout === 'masonry' ? 'Masonry' : 'Giustificato' }}
    </div>
    <!-- Filter bar preview -->
    <div v-if="s.filter_bar" style="display:flex;gap:8px;margin-bottom:8px;padding:4px 0;">
      <span style="font-size:9px;padding:2px 8px;background:rgba(255,255,255,0.15);border-radius:3px;color:#e5e7eb;font-weight:600;">{{ t('Tutti') }}</span>
      <span style="font-size:9px;padding:2px 8px;color:#9ca3af;">{{ t('Cat. 1') }}</span>
      <span style="font-size:9px;padding:2px 8px;color:#9ca3af;">{{ t('Cat. 2') }}</span>
    </div>
    <!-- Grid layout -->
    <div v-if="s.layout === 'grid'" :style="gridStyle">
      <div
        v-for="(img, idx) in visibleImages"
        :key="idx"
        :style="thumbStyle(idx)"
        :class="thumbClasses"
        @mouseenter="onTiltEnter" @mouseleave="onTiltLeave" @mousemove="onTiltMove"
      >
        <img
          :src="typeof img === 'string' ? img : img.url"
          :alt="typeof img === 'string' ? '' : (img.alt || '')"
          :style="imgInnerStyle"
          :class="{ 'olo-gallery-kb': s.fx_kenburns }"
        />
        <div v-if="s.fx_tint" :style="tintStyle"></div>
        <div v-if="s.fx_vignette" :style="vignetteStyle"></div>
        <div v-if="s.fx_grain" class="olo-gallery-grain" :style="{ opacity: parseInt(s.fx_grain_opacity || 15) / 100 }"></div>
        <div v-if="idx === visibleImages.length - 1 && extraCount > 0" :style="moreStyle">
          +{{ extraCount }}
        </div>
      </div>
    </div>
    <!-- Masonry layout -->
    <div v-else-if="s.layout === 'masonry'" :style="masonryContainerStyle">
      <div
        v-for="(img, idx) in visibleImages"
        :key="idx"
        :style="thumbStyle(idx)"
        :class="thumbClasses"
        @mouseenter="onTiltEnter" @mouseleave="onTiltLeave" @mousemove="onTiltMove"
      >
        <img
          :src="typeof img === 'string' ? img : img.url"
          :alt="typeof img === 'string' ? '' : (img.alt || '')"
          :style="imgInnerStyle"
          :class="{ 'olo-gallery-kb': s.fx_kenburns }"
        />
        <div v-if="s.fx_tint" :style="tintStyle"></div>
        <div v-if="s.fx_vignette" :style="vignetteStyle"></div>
        <div v-if="s.fx_grain" class="olo-gallery-grain" :style="{ opacity: parseInt(s.fx_grain_opacity || 15) / 100 }"></div>
        <div v-if="idx === visibleImages.length - 1 && extraCount > 0" :style="moreStyle">
          +{{ extraCount }}
        </div>
      </div>
    </div>
    <!-- Justified layout -->
    <div v-else :style="justifiedContainerStyle">
      <div
        v-for="(img, idx) in visibleImages"
        :key="idx"
        :style="justifiedThumbStyle"
        :class="thumbClasses"
        @mouseenter="onTiltEnter" @mouseleave="onTiltLeave" @mousemove="onTiltMove"
      >
        <img
          :src="typeof img === 'string' ? img : img.url"
          :alt="typeof img === 'string' ? '' : (img.alt || '')"
          :style="imgInnerStyle"
          :class="{ 'olo-gallery-kb': s.fx_kenburns }"
        />
        <div v-if="s.fx_tint" :style="tintStyle"></div>
        <div v-if="s.fx_vignette" :style="vignetteStyle"></div>
        <div v-if="s.fx_grain" class="olo-gallery-grain" :style="{ opacity: parseInt(s.fx_grain_opacity || 15) / 100 }"></div>
        <div v-if="idx === visibleImages.length - 1 && extraCount > 0" :style="moreStyle">
          +{{ extraCount }}
        </div>
      </div>
    </div>
  </div>
  <div
    v-else
    class="mb-flex mb-flex-col mb-items-center mb-justify-center mb-py-12 mb-text-gray-500 mb-bg-gray-800 mb-rounded-lg"
  >
    <span class="mb-text-4xl mb-mb-2">{{ t('&#x1F5BC;&#x1F5BC;') }}</span>
    <span class="mb-text-sm">{{ t('Aggiungi immagini alla galleria') }}</span>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  layout: 'grid', filter_bar: false, random_order: false,
  columns: '3', rows: '0', gap: '8', img_height: '200px', object_fit: 'cover', thumb_radius: '8',
  fx_hover_zoom: true, fx_hover_zoom_scale: '1.08',
  fx_hover_tilt: false,
  fx_kenburns: false, fx_kenburns_speed: '20', fx_kenburns_scale: '1.15',
  fx_vignette: false, fx_vignette_strength: '40',
  fx_grain: false, fx_grain_opacity: '15',
  fx_tint: false, fx_tint_color: '#1E3A5F', fx_tint_opacity: '10', fx_tint_blend: 'multiply',
  more_bg: 'rgba(0,0,0,0.55)', more_color: '#FFFFFF', more_size: '28',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const images = computed(() => Array.isArray(props.settings.images) ? props.settings.images : []);

const cols = computed(() => Math.max(2, Math.min(12, parseInt(s.value.columns) || 3)));
const rows = computed(() => Math.max(0, Math.min(5, parseInt(s.value.rows) || 0)));
const maxVisible = computed(() => rows.value > 0 ? cols.value * rows.value : images.value.length);
const visibleImages = computed(() => images.value.slice(0, maxVisible.value));
const extraCount = computed(() => Math.max(0, images.value.length - maxVisible.value));

const masonryHeight = (index) => {
  if (s.value.layout !== 'masonry') return s.value.img_height || '200px';
  return (index % 3 === 0 ? '240' : index % 3 === 1 ? '180' : '220') + 'px';
};

const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${cols.value}, 1fr)`,
  gap: s.value.gap + 'px',
}));

const masonryContainerStyle = computed(() => ({
  columnCount: cols.value,
  columnGap: s.value.gap + 'px',
}));

const justifiedContainerStyle = computed(() => ({
  display: 'flex',
  flexWrap: 'wrap',
  gap: s.value.gap + 'px',
}));

const thumbStyle = (index) => {
  const base = {
    position: 'relative',
    borderRadius: (s.value.thumb_radius || '8') + 'px',
    overflow: 'hidden',
    '--olo-gallery-zoom-scale': String(zoomScale.value),
  };
  if (s.value.layout === 'masonry') {
    base.height = masonryHeight(index);
    base.marginBottom = s.value.gap + 'px';
    base.breakInside = 'avoid';
  } else {
    base.height = s.value.img_height || '200px';
  }
  return base;
};

const justifiedThumbStyle = computed(() => ({
  position: 'relative',
  height: s.value.img_height || '200px',
  borderRadius: (s.value.thumb_radius || '8') + 'px',
  overflow: 'hidden',
  flexGrow: 1,
  minWidth: '120px',
  '--olo-gallery-zoom-scale': String(zoomScale.value),
}));

const imgInnerStyle = computed(() => {
  const st = {
    width: '100%', height: '100%',
    objectFit: s.value.object_fit || 'cover',
    display: 'block',
    transition: 'transform 0.4s ease',
  };
  if (s.value.fx_kenburns) {
    st.animationName = 'oloKenBurns';
    st.animationDuration = (parseInt(s.value.fx_kenburns_speed) || 20) + 's';
    st.animationTimingFunction = 'ease-in-out';
    st.animationIterationCount = 'infinite';
    st.animationDirection = 'alternate';
  }
  return st;
});

const tintStyle = computed(() => ({
  position: 'absolute', inset: '0', zIndex: '1', pointerEvents: 'none',
  background: s.value.fx_tint_color,
  opacity: parseInt(s.value.fx_tint_opacity || 10) / 100,
  mixBlendMode: s.value.fx_tint_blend || 'multiply',
}));

const vignetteStyle = computed(() => {
  const str = parseInt(s.value.fx_vignette_strength || 40);
  return {
    position: 'absolute', inset: '0', zIndex: '2', pointerEvents: 'none',
    boxShadow: `inset 0 0 ${str}px ${str / 2}px rgba(0,0,0,0.35)`,
    borderRadius: (s.value.thumb_radius || '8') + 'px',
  };
});

const moreStyle = computed(() => ({
  position: 'absolute', inset: '0', zIndex: '5',
  display: 'flex', alignItems: 'center', justifyContent: 'center',
  background: s.value.more_bg, color: s.value.more_color,
  fontSize: Math.min(parseInt(s.value.more_size) || 28, 32) + 'px',
  fontWeight: '700', letterSpacing: '-0.5px',
  borderRadius: (s.value.thumb_radius || '8') + 'px',
}));

const thumbClasses = computed(() => ({
  'olo-gallery-hover-zoom': s.value.fx_hover_zoom,
  'olo-gallery-tilt': s.value.fx_hover_tilt,
}));

const zoomScale = computed(() => parseFloat(s.value.fx_hover_zoom_scale) || 1.08);

// 3D Tilt handlers
function onTiltEnter(e) {
  if (!s.value.fx_hover_tilt) return;
  e.currentTarget.style.transition = 'transform 0.15s ease';
}
function onTiltLeave(e) {
  if (!s.value.fx_hover_tilt) return;
  e.currentTarget.style.transition = 'transform 0.4s ease';
  e.currentTarget.style.transform = 'perspective(600px) rotateX(0deg) rotateY(0deg)';
}
function onTiltMove(e) {
  if (!s.value.fx_hover_tilt) return;
  const el = e.currentTarget;
  const rect = el.getBoundingClientRect();
  const x = (e.clientX - rect.left) / rect.width - 0.5;
  const y = (e.clientY - rect.top) / rect.height - 0.5;
  const rotY = x * 20;
  const rotX = -y * 20;
  el.style.transform = `perspective(600px) rotateX(${rotX}deg) rotateY(${rotY}deg)`;
}
</script>

<style>
@keyframes oloKenBurns {
  0% { transform: scale(1) translate(0, 0); }
  100% { transform: scale(1.15) translate(-2%, -1%); }
}
.olo-gallery-kb {
  will-change: transform;
}
.olo-gallery-hover-zoom {
  transition: transform 0.4s ease, box-shadow 0.4s ease;
}
.olo-gallery-hover-zoom:hover {
  transform: scale(var(--olo-gallery-zoom-scale, 1.08));
  box-shadow: 0 8px 25px rgba(0,0,0,0.3);
  z-index: 2;
}
.olo-gallery-hover-zoom:hover img {
  transform: scale(1) !important;
}
.olo-gallery-tilt {
  transform-style: preserve-3d;
  will-change: transform;
}
.olo-gallery-grain {
  position: absolute;
  inset: 0;
  z-index: 3;
  pointer-events: none;
  background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
  background-size: 128px 128px;
  mix-blend-mode: overlay;
}
</style>
