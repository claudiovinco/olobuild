<template>
  <div style="position:relative">
  <div :style="gridStyle">
    <div v-for="i in visibleCount" :key="i" :style="thumbStyle">
      <!-- Placeholder image -->
      <div :style="imgStyle(i)"></div>
      <!-- Tint overlay -->
      <div v-if="s.fx_tint" :style="tintStyle"></div>
      <!-- Vignette -->
      <div v-if="s.fx_vignette" :style="vignetteStyle"></div>
      <!-- "+N" on last item -->
      <div v-if="i === visibleCount && extraCount > 0" :style="moreStyle">
        +{{ extraCount }}
      </div>
    </div>
  </div>
  <!-- Effect indicators -->
  <div v-if="activeEffects.length" style="display:flex;gap:3px;margin-top:4px;justify-content:flex-end">
    <span v-for="fx in activeEffects" :key="fx" style="background:rgba(0,0,0,0.5);color:#a5f3fc;padding:1px 5px;border-radius:8px;font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:0.3px">{{ fx }}</span>
  </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  columns: '3', rows: '2', gap: '8', thumb_height: '200', thumb_radius: '12',
  fx_kenburns: false, fx_hover_zoom: true, fx_hover_tilt: false,
  fx_vignette: false, fx_vignette_strength: '40',
  fx_grain: false, fx_tint: false, fx_tint_color: '#1E3A5F', fx_tint_opacity: '10', fx_tint_blend: 'multiply',
  more_bg: 'rgba(0,0,0,0.55)', more_color: '#FFFFFF', more_size: '28',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const cols = computed(() => Math.max(2, Math.min(6, parseInt(s.value.columns) || 3)));
const rows = computed(() => Math.max(1, Math.min(5, parseInt(s.value.rows) || 2)));
const visibleCount = computed(() => cols.value * rows.value);
const totalPreview = computed(() => visibleCount.value + 4);
const extraCount = computed(() => totalPreview.value - visibleCount.value);

const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${cols.value}, 1fr)`,
  gap: s.value.gap + 'px',
}));

const thumbStyle = computed(() => ({
  position: 'relative',
  height: Math.min(parseInt(s.value.thumb_height) || 200, 140) + 'px',
  borderRadius: s.value.thumb_radius + 'px',
  overflow: 'hidden',
}));

const hues = [200, 180, 220, 160, 240, 190, 210, 170, 230, 150, 205, 185];
function imgStyle(i) {
  const h = hues[(i - 1) % hues.length];
  const anim = s.value.fx_kenburns ? `olo-sgal-kb-preview 8s ease-in-out infinite ${(i * 2.5) % 8}s` : 'none';
  return {
    position: 'absolute', inset: '0',
    background: `linear-gradient(135deg, hsl(${h},40%,65%), hsl(${h + 20},35%,50%))`,
    animation: anim,
  };
}

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
    borderRadius: s.value.thumb_radius + 'px',
  };
});

const moreStyle = computed(() => ({
  position: 'absolute', inset: '0', zIndex: '5',
  display: 'flex', alignItems: 'center', justifyContent: 'center',
  background: s.value.more_bg, color: s.value.more_color,
  fontSize: Math.min(parseInt(s.value.more_size) || 28, 32) + 'px',
  fontWeight: '700', letterSpacing: '-0.5px',
  borderRadius: s.value.thumb_radius + 'px',
}));

const activeEffects = computed(() => {
  const list = [];
  if (s.value.fx_kenburns) list.push('KB');
  if (s.value.fx_hover_zoom) list.push('Zoom');
  if (s.value.fx_hover_tilt) list.push('Tilt');
  if (s.value.fx_vignette) list.push('Vig');
  if (s.value.fx_grain) list.push('Grain');
  if (s.value.fx_tint) list.push('Tint');
  return list;
});
</script>

<style scoped>
@keyframes olo-sgal-kb-preview {
  0%   { transform: scale(1); }
  50%  { transform: scale(1.08) translate(-1%, -0.5%); }
  100% { transform: scale(1); }
}
</style>
