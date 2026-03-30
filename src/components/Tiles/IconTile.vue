<template>
  <div class="mb-flex mb-items-center mb-justify-center mb-py-6" :style="{ minHeight: '60px' }">
    <span :style="wrapperStyle" :class="animClass">
      <span
        v-if="iconSvg"
        class="olo-icon-preview"
        :style="{ width: s.size + 'px', height: s.size + 'px', color: s.color || '#9CA3AF' }"
        v-html="iconSvg"
      ></span>
      <span
        v-else
        :style="{ fontSize: s.size + 'px', color: s.color || '#9CA3AF', lineHeight: 1, display: 'inline-flex', alignItems: 'center', justifyContent: 'center' }"
      >&#9733;</span>
    </span>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  icon: 'star',
  size: 40,
  color: '',
  view: 'default',
  bg_color: '#6366F1',
  bg_shape: 'circle',
  padding: '20',
  hover_animation: 'none',
  rotation: '0',
  link_url: '',
  link_target: '_self',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const iconSvg = computed(() => iconsSvg[s.value.icon] || '');

const wrapperStyle = computed(() => {
  const view = s.value.view || 'default';
  const shape = s.value.bg_shape || 'circle';
  const pad = parseInt(s.value.padding) || 20;
  const rotation = parseInt(s.value.rotation) || 0;
  const st = {
    display: 'inline-flex',
    alignItems: 'center',
    justifyContent: 'center',
    transition: 'transform 0.3s ease',
  };
  if (rotation !== 0) st.transform = `rotate(${rotation}deg)`;
  if (view === 'stacked') {
    st.background = s.value.bg_color || '#6366F1';
    st.padding = pad + 'px';
    st.borderRadius = shape === 'circle' ? '50%' : shape === 'rounded' ? '12px' : '0';
  } else if (view === 'framed') {
    st.border = '2px solid ' + (s.value.bg_color || '#6366F1');
    st.padding = pad + 'px';
    st.borderRadius = shape === 'circle' ? '50%' : shape === 'rounded' ? '12px' : '0';
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
