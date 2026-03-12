<template>
  <div class="mps-preview" :style="{ height: sliderHeight + 'px' }">
    <!-- Global background (behind slide bg) -->
    <div v-if="isTransparent" class="mps-preview-bg" :style="globalBgStyle"></div>
    <!-- Background of first slide -->
    <div class="mps-preview-bg" :style="bgStyle"></div>
    <!-- Overlay -->
    <div v-if="overlayOpacity > 0 && !isTransparent" class="mps-preview-overlay" :style="{ background: overlayColor, opacity: overlayOpacity }"></div>

    <!-- Layer previews (first slide only) -->
    <div
      v-for="layer in layers"
      :key="layer.id"
      class="mps-preview-layer"
      :style="layerStyle(layer)"
    >
      <component
        v-if="layer.type === 'text'"
        :is="layer.tag || 'h2'"
        :style="textStyle(layer)"
        class="mps-preview-text"
      >{{ layer.content }}</component>
      <span v-else-if="layer.type === 'button'" :style="buttonStyle(layer)">{{ layer.content || 'Button' }}</span>
      <img v-else-if="layer.type === 'image' && layer.imageSrc" :src="layer.imageSrc" class="mps-preview-img" draggable="false" />
      <span v-else-if="layer.type === 'icon'" class="mps-preview-icon" :style="{ width: layer.fontSize + 'px', height: layer.fontSize + 'px', color: layer.color || '#fff' }" v-html="getIconSvg(layer.iconName)"></span>
      <div v-else-if="layer.type === 'shape'" class="mps-preview-shape" :style="{ background: layer.bgColor || '#3b82f6', borderRadius: (layer.borderRadius || 0) + 'px' }"></div>
    </div>

    <!-- Badge -->
    <div class="mps-preview-badge">
      ProSlider &middot; {{ slideCount }} slide{{ slideCount !== 1 ? 's' : '' }}
    </div>

    <!-- Nav preview -->
    <div v-if="s.showArrows !== false" class="mps-preview-arrows">
      <span class="mps-preview-arrow">&lsaquo;</span>
      <span class="mps-preview-arrow">&rsaquo;</span>
    </div>
    <div v-if="s.showDots !== false && slideCount > 1" class="mps-preview-dots">
      <span v-for="i in slideCount" :key="i" :class="['mps-preview-dot', i === 1 ? 'mps-preview-dot-active' : '']"></span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  height: 600,
  showArrows: true,
  showDots: true,
  slides: [],
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const slides = computed(() => {
  const sl = s.value.slides;
  return Array.isArray(sl) && sl.length ? sl : [];
});

const slideCount = computed(() => slides.value.length);
const firstSlide = computed(() => slides.value[0] || null);
const layers = computed(() => firstSlide.value?.layers || []);
const sliderHeight = computed(() => parseInt(s.value.height) || 600);

const isTransparent = computed(() => (firstSlide.value?.background?.type) === 'transparent');

const bgStyle = computed(() => {
  const bg = firstSlide.value?.background || {};
  if (bg.type === 'transparent') {
    return { background: 'transparent' };
  }
  if (bg.type === 'image' && bg.image) {
    return { backgroundImage: `url(${bg.image})`, backgroundSize: 'cover', backgroundPosition: 'center' };
  }
  if (bg.type === 'gradient') {
    return { background: `linear-gradient(${bg.gradientAngle || 180}deg, ${bg.gradientFrom || '#1e293b'}, ${bg.gradientTo || '#0f172a'})` };
  }
  return { background: bg.color || '#1e293b' };
});

const globalBgStyle = computed(() => {
  const gb = s.value.globalBackground;
  if (!gb) return { background: '#1e293b' };
  if (gb.type === 'image' && gb.image) {
    return { backgroundImage: `url(${gb.image})`, backgroundSize: 'cover', backgroundPosition: 'center' };
  }
  if (gb.type === 'gradient') {
    return { background: `linear-gradient(${gb.gradientAngle || 180}deg, ${gb.gradientFrom || '#1e293b'}, ${gb.gradientTo || '#0f172a'})` };
  }
  return { background: gb.color || '#1e293b' };
});

const overlayColor = computed(() => firstSlide.value?.background?.overlay || '#000');
const overlayOpacity = computed(() => firstSlide.value?.background?.overlayOpacity ?? 0.3);

function layerStyle(l) {
  const st = {
    position: 'absolute',
    left: l.x + '%',
    top: l.y + '%',
    width: l.width === 'auto' ? 'auto' : l.width + '%',
    zIndex: 2,
  };
  if (l.opacity !== undefined && l.opacity !== null && l.opacity !== 1) {
    st.opacity = parseFloat(l.opacity);
  }
  return st;
}

function getIconSvg(name) {
  return iconsSvg[name] || iconsSvg['star'] || '';
}

function textStyle(l) {
  return {
    fontSize: l.fontSize + 'px',
    fontWeight: l.fontWeight || '700',
    color: l.color || '#fff',
    textAlign: l.textAlign || 'left',
    margin: 0,
    lineHeight: 1.2,
  };
}

function buttonStyle(l) {
  return {
    fontSize: l.fontSize + 'px',
    fontWeight: l.fontWeight || '600',
    color: l.color || '#fff',
    background: l.bgColor || '#2563eb',
    borderRadius: (l.borderRadius || 6) + 'px',
    padding: (l.padding || 12) + 'px ' + ((l.padding || 12) * 2) + 'px',
    display: 'inline-block',
  };
}
</script>

<style scoped>
.mps-preview {
  position: relative;
  overflow: hidden;
  border-radius: 4px;
}
.mps-preview-bg {
  position: absolute;
  inset: 0;
}
.mps-preview-overlay {
  position: absolute;
  inset: 0;
  z-index: 1;
}
.mps-preview-layer {
  pointer-events: none;
}
.mps-preview-text {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.mps-preview-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.mps-preview-shape {
  width: 100%;
  height: 100%;
  min-height: 20px;
}
.mps-preview-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.mps-preview-icon :deep(svg) {
  width: 100%;
  height: 100%;
  fill: currentColor;
  stroke: currentColor;
}
.mps-preview-badge {
  position: absolute;
  top: 8px;
  right: 8px;
  background: rgba(0,0,0,0.6);
  color: #e5e7eb;
  font-size: 10px;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: 4px;
  z-index: 10;
}
.mps-preview-arrows {
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  display: flex;
  justify-content: space-between;
  padding: 0 12px;
  transform: translateY(-50%);
  z-index: 5;
  pointer-events: none;
}
.mps-preview-arrow {
  background: rgba(0,0,0,0.4);
  color: #fff;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
}
.mps-preview-dots {
  position: absolute;
  bottom: 10px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 6px;
  z-index: 5;
}
.mps-preview-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  border: 1.5px solid rgba(255,255,255,0.7);
  background: transparent;
}
.mps-preview-dot-active {
  background: #fff;
  border-color: #fff;
}
</style>
