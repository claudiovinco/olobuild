<template>
  <div class="olo-overlayslider" :class="'olo-os--preset-' + (s.preset || 'cinematic-overlay')" style="position:relative;overflow:hidden;">
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
          <component :is="titleTag" class="mb-font-bold mb-text-white mb-m-0" :style="titleFontStyle" :data-olo-editable="'slides.' + i + '.title'">{{ slide.title }}</component>
          <div v-if="slide.subtitle" class="mb-text-xs mb-text-gray-200 mb-mt-1" :data-olo-editable="'slides.' + i + '.subtitle'">{{ slide.subtitle }}</div>
          <div v-if="slide.link" class="mb-text-[10px] mb-text-blue-300 mb-mt-1 mb-opacity-70">&#128279; {{ slide.link }}</div>
        </div>
      </div>
    </div>

    <!-- Arrows -->
    <template v-if="s.show_arrows !== false && slides.length > 1">
      <button class="olo-os-arrow olo-os-prev" @click="prev" :aria-label="t('Precedente')">&#10094;</button>
      <button class="olo-os-arrow olo-os-next" @click="next" :aria-label="t('Successivo')">&#10095;</button>
    </template>

    <!-- Dots -->
    <div v-if="s.show_dots !== false && slides.length > 1" class="olo-os-dots">
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
import { t } from '@/i18n';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  columns: '1',
  gap: 'default',
  image_ratio: 'auto',
  image_height: '400',
  image_fit: 'cover',
  height: '400',
  overlay_position: 'bottom',
  overlay_horizontal: 'left',
  overlay_padding: 'medium',
  title_size: 'h3',
  hover_effect: 'none',
  hover_overlay: 'always',
  show_arrows: true,
  show_dots: true,
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const current = ref(0);

const slides = computed(() => {
  const raw = s.value.slides;
  if (Array.isArray(raw) && raw.length) return raw;
  return [
    { id: 'os-1', image: '', title: 'Slide 1', subtitle: '' },
    { id: 'os-2', image: '', title: 'Slide 2', subtitle: '' },
  ];
});

const slideHeight = computed(() => parseInt(s.value.image_height || s.value.height) || 400);

const useRatio = computed(() => s.value.image_ratio && s.value.image_ratio !== 'auto');

const trackStyle = computed(() => {
  const style = {
    display: 'flex',
    transition: 'transform 0.5s ease',
    transform: `translateX(-${current.value * 100}%)`,
  };
  if (!useRatio.value) {
    style.height = slideHeight.value + 'px';
  }
  return style;
});

const slideFrameStyle = computed(() => {
  const style = { width: '100%', position: 'relative', overflow: 'hidden' };
  if (useRatio.value) {
    style.aspectRatio = (s.value.image_ratio || '16/9').replace('/', ' / ');
  } else {
    style.height = '100%';
  }
  return style;
});

const objectFit = computed(() => s.value.image_fit || 'cover');

const overlayClasses = computed(() => {
  const pos = s.value.overlay_position || 'bottom';
  const align = s.value.overlay_horizontal || 'left';
  const classes = ['olo-os-overlay--' + pos];
  if (align === 'center') classes.push('olo-os-align-center');
  if (align === 'right') classes.push('olo-os-align-right');
  return classes;
});

const overlayPadStyle = computed(() => {
  const pad = s.value.overlay_padding || 'medium';
  if (pad === 'small') return { padding: '8px 12px' };
  if (pad === 'large') return { padding: '24px 32px' };
  return { padding: '12px 16px' };
});

const titleTag = computed(() => {
  const tag = s.value.title_size || 'h3';
  return ['h1', 'h2', 'h3', 'h4'].includes(tag) ? tag : 'h3';
});

const titleFontStyle = computed(() => {
  const tag = s.value.title_size || 'h3';
  const sizes = { h1: '28px', h2: '22px', h3: '16px', h4: '14px' };
  return { fontSize: sizes[tag] || '16px', lineHeight: 1.3 };
});

const hoverImageClass = computed(() => {
  const fx = s.value.hover_effect || 'none';
  return fx !== 'none' ? 'olo-os-hover-' + fx : '';
});

const hoverOverlayClass = computed(() => {
  const ov = s.value.hover_overlay || 'always';
  return ov !== 'always' ? 'olo-os-ov-' + ov : '';
});

function slideStyle(slide) {
  const fit = objectFit.value;
  const bg = slide.image ? `url(${slide.image}) center/${fit === 'cover' ? 'cover' : (fit === 'contain' ? 'contain' : '100% 100%')} no-repeat` : '#374151';
  const style = {
    minWidth: '100%',
    position: 'relative',
    background: bg,
  };
  if (useRatio.value) {
    style.aspectRatio = (s.value.image_ratio || '16/9').replace('/', ' / ');
  } else {
    style.height = '100%';
  }
  return style;
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
.olo-os-hover-zoom:hover { transform: scale(1.08); }
.olo-os-hover-zoom-rotate:hover { transform: scale(1.08) rotate(2deg); }
.olo-os-hover-brightness { filter: brightness(0.7); }
.olo-os-hover-brightness:hover { filter: brightness(1); }
.olo-os-hover-desaturate { filter: grayscale(100%); }
.olo-os-hover-desaturate:hover { filter: grayscale(0%); }
.olo-os-hover-blur-in { filter: blur(3px); }
.olo-os-hover-blur-in:hover { filter: blur(0); }

/* Hover overlay effects */
.olo-os-ov-fade { opacity: 0; transition: opacity 0.3s ease; }
.olo-os-slide:hover .olo-os-ov-fade { opacity: 1; }
.olo-os-ov-slide-bottom { transform: translateY(100%); transition: transform 0.3s ease; }
.olo-os-slide:hover .olo-os-ov-slide-bottom { transform: translateY(0); }
.olo-os-ov-slide-top { transform: translateY(-100%); transition: transform 0.3s ease; }
.olo-os-slide:hover .olo-os-ov-slide-top { transform: translateY(0); }
.olo-os-ov-slide-left { transform: translateX(-100%); transition: transform 0.3s ease; }
.olo-os-slide:hover .olo-os-ov-slide-left { transform: translateX(0); }
.olo-os-ov-slide-right { transform: translateX(100%); transition: transform 0.3s ease; }
.olo-os-slide:hover .olo-os-ov-slide-right { transform: translateX(0); }

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

/* ───── Preset visual hints in builder ───── */

/* Liquid Glass */
.olo-os--preset-liquid-glass .olo-os-overlay {
  backdrop-filter: blur(12px) saturate(180%);
  -webkit-backdrop-filter: blur(12px) saturate(180%);
  background: rgba(255,255,255,0.20) !important;
  border: 1px solid rgba(255,255,255,0.4);
  border-radius: 12px;
  margin: 12px;
}

/* Neon Cyber */
.olo-os--preset-neon-cyber .olo-os-slide {
  border: 2px solid #ff6a2a;
  box-shadow: 0 0 14px rgba(255,106,42,0.35);
}
.olo-os--preset-neon-cyber .olo-os-overlay {
  background: linear-gradient(180deg, transparent 0%, rgba(10,15,28,0.85) 100%) !important;
}
.olo-os--preset-neon-cyber .olo-os-overlay :deep(*) {
  color: #ff6a2a !important;
  text-shadow: 0 0 6px rgba(255,106,42,0.5);
  text-transform: uppercase;
  letter-spacing: 0.06em;
}

/* Brutalist */
.olo-os--preset-brutalist-block .olo-os-slide {
  border: 3px solid #000;
  box-shadow: 6px 6px 0 0 #000;
}
.olo-os--preset-brutalist-block .olo-os-overlay {
  background: #fff !important;
  color: #000 !important;
  border-top: 3px solid #000;
}
.olo-os--preset-brutalist-block .olo-os-overlay :deep(*) {
  color: #000 !important;
  font-weight: 900 !important;
  text-transform: uppercase;
}

/* Magnetic */
.olo-os--preset-magnetic-liquid .olo-os-slide {
  border-radius: 22px;
  box-shadow: 0 14px 32px rgba(232,98,42,0.18);
}
.olo-os--preset-magnetic-liquid .olo-os-overlay {
  background: linear-gradient(135deg, rgba(0,0,0,0.30) 0%, rgba(232,98,42,0.55) 100%) !important;
  border-radius: 22px;
  margin: 12px;
}

/* Sticker */
.olo-os--preset-sticker .olo-os-slide {
  border: 3px dashed rgba(232,98,42,0.55);
  background: #fff;
  padding: 6px;
  border-radius: 6px;
  box-shadow: 0 10px 24px rgba(0,0,0,0.18);
  transform: rotate(-1deg);
}

/* Retro Terminal */
.olo-os--preset-retro-terminal .olo-os-slide {
  border: 1px solid rgba(0,255,140,0.45);
  background-image: repeating-linear-gradient(0deg, transparent 0, transparent 2px, rgba(0,255,140,0.06) 2px, rgba(0,255,140,0.06) 3px);
}
.olo-os--preset-retro-terminal .olo-os-overlay {
  background: rgba(12,12,12,0.85) !important;
  font-family: ui-monospace, SFMono-Regular, Menlo, monospace !important;
}
.olo-os--preset-retro-terminal .olo-os-overlay :deep(*) {
  color: #00ff8c !important;
  text-shadow: 0 0 6px rgba(0,255,140,0.5);
  font-family: inherit !important;
  text-transform: uppercase;
}

/* 3D Tilt */
.olo-os--preset-3d-tilt .olo-os-slide {
  box-shadow: 0 20px 40px rgba(0,0,0,0.20);
  transform: perspective(900px) rotateX(2deg);
  transform-origin: center top;
}
</style>
