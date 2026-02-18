<template>
  <div class="mb-relative mb-overflow-hidden" :style="wrapStyle">
    <!-- Bg image -->
    <div v-if="s.bg_type === 'image' && s.bg_image" class="mb-absolute mb-inset-0"
      :style="{ backgroundImage: `url(${s.bg_image})`, backgroundSize: 'cover', backgroundPosition: 'center' }"></div>
    <!-- Bg video badge -->
    <div v-if="s.bg_type === 'video'" class="mb-absolute mb-inset-0 mb-flex mb-items-center mb-justify-center mb-bg-gray-900">
      <div class="mb-text-gray-500 mb-text-xs">&#9654; Video</div>
    </div>
    <!-- Overlay -->
    <div v-if="showOverlay" class="mb-absolute mb-inset-0" :style="overlayStyle"></div>

    <!-- Content -->
    <div class="mb-relative mb-text-center" style="z-index:2">
      <div v-if="s.icon_emoji" :style="iconStyle">
        <span v-if="iconSvg" class="olo-cnt-icon-svg" v-html="iconSvg"></span>
        <template v-else>{{ s.icon_emoji }}</template>
      </div>
      <div :style="numberStyle">
        {{ s.prefix }}{{ s.number || '0' }}{{ s.suffix }}
      </div>
      <div v-if="s.label" :style="labelStyle" v-html="s.label"></div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({
  number: '1250', label: 'Clienti soddisfatti', prefix: '', suffix: '+',
  icon_emoji: '🏆', icon_size: '40',
  text_color: '#F3F4F6', number_font_size: '48', number_font_weight: '700',
  label_color: '', label_font_size: '14', label_font_weight: '400',
  bg_type: 'color', bg_color: '', bg_image: '', bg_video: '',
  overlay: false, overlay_color: '#000000', overlay_opacity: '50',
  padding: '32', border_radius: '0', border_width: '0', border_color: '#374151',
  ...props.settings,
}));

const iconSvg = computed(() => iconsSvg[s.value.icon_emoji] || '');

const showOverlay = computed(() => {
  if (s.value.bg_type === 'color') return false;
  const v = s.value.overlay;
  return v && v !== 'false' && v !== '0' && v !== '';
});

const wrapStyle = computed(() => {
  const pad = parseInt(s.value.padding) || 0;
  const bw = parseInt(s.value.border_width) || 0;
  const st = {
    padding: pad + 'px',
    borderRadius: (parseInt(s.value.border_radius) || 0) + 'px',
    color: s.value.text_color || '#F3F4F6',
    minHeight: '80px',
  };
  if (s.value.bg_type === 'color' && s.value.bg_color) {
    st.background = s.value.bg_color;
  }
  if (bw > 0) st.border = `${bw}px solid ${s.value.border_color || '#374151'}`;
  return st;
});

const overlayStyle = computed(() => ({
  backgroundColor: s.value.overlay_color || '#000000',
  opacity: (parseInt(s.value.overlay_opacity) || 50) / 100,
  zIndex: 1,
}));

const iconStyle = computed(() => ({
  fontSize: (parseInt(s.value.icon_size) || 40) + 'px',
  lineHeight: '1.2',
  marginBottom: '8px',
}));

const numberStyle = computed(() => ({
  fontSize: (parseInt(s.value.number_font_size) || 48) + 'px',
  fontWeight: s.value.number_font_weight || '700',
  lineHeight: '1.1',
}));

const labelStyle = computed(() => {
  const fg = s.value.text_color || '#F3F4F6';
  const lc = s.value.label_color || '';
  return {
    fontSize: (parseInt(s.value.label_font_size) || 14) + 'px',
    fontWeight: s.value.label_font_weight || '400',
    marginTop: '8px',
    color: lc || fg,
    opacity: lc ? 1 : 0.7,
  };
});
</script>

<style scoped>
.olo-cnt-icon-svg {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 1em;
  height: 1em;
}
.olo-cnt-icon-svg :deep(svg) {
  width: 100%;
  height: 100%;
  fill: currentColor;
  stroke: currentColor;
}
</style>
