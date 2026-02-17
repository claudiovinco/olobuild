<template>
  <div class="olo-overlayslider" style="position:relative;overflow:hidden;">
    <!-- Track -->
    <div
      class="olo-os-track"
      :style="trackStyle"
    >
      <div
        v-for="(slide, i) in slides"
        :key="slide.id || i"
        :class="['olo-os-slide', hoverImageClass]"
        :style="slideStyle(slide)"
      >
        <!-- Overlay -->
        <div class="olo-os-overlay" :class="[...overlayClasses, hoverOverlayClass]" :style="overlayPadStyle">
          <component :is="titleTag" class="mb-font-bold mb-text-white mb-m-0" :style="titleFontStyle">{{ slide.title }}</component>
          <div v-if="slide.subtitle" class="mb-text-xs mb-text-gray-200 mb-mt-1">{{ slide.subtitle }}</div>
          <div v-if="slide.link" class="mb-text-[10px] mb-text-blue-300 mb-mt-1 mb-opacity-70">&#128279; {{ slide.link }}</div>
        </div>
      </div>
    </div>

    <!-- Arrows -->
    <template v-if="settings.show_arrows !== false && slides.length > 1">
      <button class="olo-os-arrow olo-os-prev" @click="prev" aria-label="Precedente">&#10094;</button>
      <button class="olo-os-arrow olo-os-next" @click="next" aria-label="Successivo">&#10095;</button>
    </template>

    <!-- Dots -->
    <div v-if="settings.show_dots !== false && slides.length > 1" class="olo-os-dots">
      <button
        v-for="(_, i) in slides"
        :key="i"
        class="olo-os-dot"
        :class="{ 'olo-os-dot--active': i === current }"
        @click="goTo(i)"
        :aria-label="'Vai a slide ' + (i + 1)"
      ></button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const current = ref(0);

const slides = computed(() => {
  const raw = props.settings.slides;
  if (Array.isArray(raw) && raw.length) return raw;
  return [
    { id: 'os-1', image: '', title: 'Slide 1', subtitle: '' },
    { id: 'os-2', image: '', title: 'Slide 2', subtitle: '' },
  ];
});

const slideHeight = computed(() => parseInt(props.settings.height) || 400);

const trackStyle = computed(() => ({
  display: 'flex',
  height: slideHeight.value + 'px',
  transition: 'transform 0.5s ease',
  transform: `translateX(-${current.value * 100}%)`,
}));

const overlayClasses = computed(() => {
  const pos = props.settings.overlay_position || 'bottom';
  const align = props.settings.overlay_horizontal || 'left';
  const classes = ['olo-os-overlay--' + pos];
  if (align === 'center') classes.push('olo-os-align-center');
  if (align === 'right') classes.push('olo-os-align-right');
  return classes;
});

const overlayPadStyle = computed(() => {
  const pad = props.settings.overlay_padding || 'medium';
  if (pad === 'small') return { padding: '8px 12px' };
  if (pad === 'large') return { padding: '24px 32px' };
  return { padding: '12px 16px' };
});

const titleTag = computed(() => {
  const tag = props.settings.title_size || 'h3';
  return ['h1', 'h2', 'h3', 'h4'].includes(tag) ? tag : 'h3';
});

const titleFontStyle = computed(() => {
  const tag = props.settings.title_size || 'h3';
  const sizes = { h1: '28px', h2: '22px', h3: '16px', h4: '14px' };
  return { fontSize: sizes[tag] || '16px', lineHeight: 1.3 };
});

const hoverImageClass = computed(() => {
  const fx = props.settings.hover_effect || 'none';
  return fx !== 'none' ? 'mos-os-hover-' + fx : '';
});

const hoverOverlayClass = computed(() => {
  const ov = props.settings.hover_overlay || 'always';
  return ov !== 'always' ? 'mos-os-ov-' + ov : '';
});

function slideStyle(slide) {
  const bg = slide.image ? `url(${slide.image}) center/cover no-repeat` : '#374151';
  return {
    minWidth: '100%',
    height: '100%',
    position: 'relative',
    background: bg,
  };
}

function goTo(index) {
  current.value = ((index % slides.value.length) + slides.value.length) % slides.value.length;
}

function next() { goTo(current.value + 1); }
function prev() { goTo(current.value - 1); }
</script>

<style scoped>
.olo-overlayslider {
  min-height: 120px;
  border-radius: 6px;
  overflow: hidden;
}

.olo-os-overlay {
  position: absolute;
  left: 0;
  right: 0;
  padding: 12px 16px;
  background: rgba(0, 0, 0, 0.6);
}

.olo-os-overlay--bottom {
  bottom: 0;
}

.olo-os-overlay--top {
  top: 0;
}

.olo-os-overlay--center {
  top: 50%;
  transform: translateY(-50%);
}

.olo-os-overlay--cover {
  inset: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
}

/* Compound positions */
.olo-os-overlay--bottom-left { bottom: 0; left: 0; right: auto; }
.olo-os-overlay--bottom-center { bottom: 0; left: 50%; right: auto; transform: translateX(-50%); text-align: center; }
.olo-os-overlay--bottom-right { bottom: 0; left: auto; right: 0; text-align: right; }
.olo-os-overlay--top-left { top: 0; left: 0; right: auto; }
.olo-os-overlay--top-center { top: 0; left: 50%; right: auto; transform: translateX(-50%); text-align: center; }
.olo-os-overlay--top-right { top: 0; left: auto; right: 0; text-align: right; }
.olo-os-overlay--center-left { top: 50%; left: 0; right: auto; transform: translateY(-50%); }
.olo-os-overlay--center-right { top: 50%; left: auto; right: 0; transform: translateY(-50%); text-align: right; }

/* Text alignment overrides */
.olo-os-align-center { text-align: center; }
.olo-os-align-right { text-align: right; }

/* Hover image effects */
.olo-os-slide { transition: transform 0.5s ease, filter 0.5s ease; }
.mos-os-hover-zoom:hover { transform: scale(1.08); }
.mos-os-hover-zoom-rotate:hover { transform: scale(1.08) rotate(2deg); }
.mos-os-hover-brightness { filter: brightness(0.7); }
.mos-os-hover-brightness:hover { filter: brightness(1); }
.mos-os-hover-desaturate { filter: grayscale(100%); }
.mos-os-hover-desaturate:hover { filter: grayscale(0%); }
.mos-os-hover-blur-in { filter: blur(3px); }
.mos-os-hover-blur-in:hover { filter: blur(0); }

/* Hover overlay effects */
.mos-os-ov-fade { opacity: 0; transition: opacity 0.3s ease; }
.olo-os-slide:hover .mos-os-ov-fade { opacity: 1; }
.mos-os-ov-slide-bottom { transform: translateY(100%); transition: transform 0.3s ease; }
.olo-os-slide:hover .mos-os-ov-slide-bottom { transform: translateY(0); }
.mos-os-ov-slide-top { transform: translateY(-100%); transition: transform 0.3s ease; }
.olo-os-slide:hover .mos-os-ov-slide-top { transform: translateY(0); }
.mos-os-ov-slide-left { transform: translateX(-100%); transition: transform 0.3s ease; }
.olo-os-slide:hover .mos-os-ov-slide-left { transform: translateX(0); }
.mos-os-ov-slide-right { transform: translateX(100%); transition: transform 0.3s ease; }
.olo-os-slide:hover .mos-os-ov-slide-right { transform: translateX(0); }

.olo-os-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(0,0,0,0.5);
  color: #fff;
  border: none;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  cursor: pointer;
  font-size: 16px;
  z-index: 2;
  transition: background 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.olo-os-arrow:hover {
  background: rgba(0,0,0,0.75);
}

.olo-os-prev { left: 8px; }
.olo-os-next { right: 8px; }

.olo-os-dots {
  position: absolute;
  bottom: 10px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 6px;
  z-index: 2;
}

.olo-os-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  border: 2px solid #fff;
  background: transparent;
  cursor: pointer;
  padding: 0;
  transition: background 0.2s;
}

.olo-os-dot--active {
  background: #fff;
}
</style>
