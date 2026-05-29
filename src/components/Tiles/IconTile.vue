<template>
  <div class="mb-flex mb-items-center mb-justify-center mb-py-6" :style="{ minHeight: '60px' }">
    <span :style="wrapperStyle" :class="animClass">
      <span
        v-if="iconSvg"
        class="olo-icon-preview"
        :style="{ width: s.size + 'px', height: s.size + 'px', color: iconColor }"
        v-html="iconSvg"
      ></span>
      <span
        v-else
        :style="{ fontSize: s.size + 'px', color: iconColor, lineHeight: 1, display: 'inline-flex', alignItems: 'center', justifyContent: 'center' }"
      >&#9733;</span>
    </span>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
import { useBoxModel } from '@/composables/useBoxModel';
import { resolveColor, TOKENS, RADIUS, buildDefaults } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

// Fonte UNICA dei default (allineata a icon.js); colori token-first
const s = computed(() => ({ ...buildDefaults('icon'), ...props.settings }));

// Box-model: tile_padding (oggetto) con retrocompat per la vecchia chiave 'padding'
const { paddingCss } = useBoxModel(s, {
  paddingKey: 'tile_padding', paddingFallback: [16, 16, 16, 16],
  paddingLegacy: ['padding', 'padding'],
});

const iconSvg = computed(() => iconsSvg[s.value.icon] || '');

// Colore icona token-first: su sfondo pieno (stacked) contrasta col primario,
// altrimenti usa il primario brand (era fallback grigio #9CA3AF).
const iconColor = computed(() =>
  resolveColor(s.value.color, (s.value.view === 'stacked') ? TOKENS.onPrimary : TOKENS.primary),
);

const wrapperStyle = computed(() => {
  const view = s.value.view || 'default';
  const shape = s.value.bg_shape || 'circle';
  const rotation = parseInt(s.value.rotation) || 0;
  // raggio dalla scala condivisa RADIUS (rounded → lg)
  const radius = shape === 'circle' ? '50%' : shape === 'rounded' ? RADIUS.lg + 'px' : '0';
  const accent = resolveColor(s.value.bg_color, TOKENS.primary);
  const st = {
    display: 'inline-flex',
    alignItems: 'center',
    justifyContent: 'center',
    transition: 'transform 0.3s ease',
  };
  if (rotation !== 0) st.transform = `rotate(${rotation}deg)`;
  if (view === 'stacked') {
    st.background = accent;
    st.padding = paddingCss.value;
    st.borderRadius = radius;
  } else if (view === 'framed') {
    st.border = '2px solid ' + accent;
    st.padding = paddingCss.value;
    st.borderRadius = radius;
  }
  return st;
});

const animClass = computed(() => {
  const anim = s.value.hover_animation || 'none';
  return anim !== 'none' ? 'olo-icon-' + anim : '';
});
</script>

<style scoped>
.olo-icon-preview {
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.olo-icon-preview :deep(svg) {
  width: 100%;
  height: 100%;
  fill: currentColor;
  stroke: currentColor;
}
.olo-icon-grow:hover { transform: scale(1.2) !important; }
.olo-icon-shake:hover { animation: olo-shake 0.5s ease; }
.olo-icon-bounce:hover { animation: olo-bounce 0.6s ease; }
.olo-icon-spin:hover { animation: olo-spin 0.8s ease; }
.olo-icon-pulse:hover { animation: olo-pulse 1s ease infinite; }
@keyframes olo-shake { 0%,100%{transform:translateX(0)} 25%{transform:translateX(-4px)} 75%{transform:translateX(4px)} }
@keyframes olo-bounce { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
@keyframes olo-spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
@keyframes olo-pulse { 0%,100%{transform:scale(1)} 50%{transform:scale(1.15)} }
</style>
