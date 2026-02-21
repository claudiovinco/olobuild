<template>
  <div v-if="images.length > 0" :style="gridStyle">
    <div v-for="(img, idx) in visibleImages" :key="idx" :style="thumbStyle">
      <img
        :src="typeof img === 'string' ? img : img.url"
        :alt="typeof img === 'string' ? '' : (img.alt || '')"
        :style="imgInnerStyle"
      />
      <!-- Tint -->
      <div v-if="s.fx_tint" :style="tintStyle"></div>
      <!-- Vignette -->
      <div v-if="s.fx_vignette" :style="vignetteStyle"></div>
      <!-- "+N" on last visible -->
      <div v-if="idx === visibleImages.length - 1 && extraCount > 0" :style="moreStyle">
        +{{ extraCount }}
      </div>
    </div>
  </div>
  <div
    v-else
    class="mb-flex mb-flex-col mb-items-center mb-justify-center mb-py-12 mb-text-gray-500 mb-bg-gray-800 mb-rounded-lg"
  >
    <span class="mb-text-4xl mb-mb-2">&#x1F5BC;&#x1F5BC;</span>
    <span class="mb-text-sm">Aggiungi immagini alla galleria</span>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  columns: '3', rows: '0', gap: '8', img_height: '200px', object_fit: 'cover', thumb_radius: '8',
  fx_hover_zoom: true, fx_hover_tilt: false,
  fx_vignette: false, fx_vignette_strength: '40',
  fx_tint: false, fx_tint_color: '#1E3A5F', fx_tint_opacity: '10', fx_tint_blend: 'multiply',
  more_bg: 'rgba(0,0,0,0.55)', more_color: '#FFFFFF', more_size: '28',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const images = computed(() => Array.isArray(props.settings.images) ? props.settings.images : []);

const cols = computed(() => Math.max(2, Math.min(6, parseInt(s.value.columns) || 3)));
const rows = computed(() => Math.max(0, Math.min(5, parseInt(s.value.rows) || 0)));
const maxVisible = computed(() => rows.value > 0 ? cols.value * rows.value : images.value.length);
const visibleImages = computed(() => images.value.slice(0, maxVisible.value));
const extraCount = computed(() => Math.max(0, images.value.length - maxVisible.value));

const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${cols.value}, 1fr)`,
  gap: s.value.gap + 'px',
}));

const thumbStyle = computed(() => ({
  position: 'relative',
  height: s.value.img_height || '200px',
  borderRadius: (s.value.thumb_radius || '8') + 'px',
  overflow: 'hidden',
}));

const imgInnerStyle = computed(() => ({
  width: '100%', height: '100%',
  objectFit: s.value.object_fit || 'cover',
  display: 'block',
}));

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
</script>
