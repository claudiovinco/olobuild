<template>
  <div
    class="olo-textmask-preview mb-relative mb-overflow-hidden mb-rounded-lg"
    :style="containerStyle"
  >
    <!-- Video background -->
    <video
      v-if="s.video_url"
      class="mb-absolute mb-inset-0 mb-w-full mb-h-full"
      style="object-fit: cover;"
      :style="{ opacity: (parseInt(s.video_opacity) || 100) / 100 }"
      :src="s.video_url"
      :poster="s.video_poster || undefined"
      muted autoplay loop playsinline
    ></video>
    <div
      v-else
      class="mb-absolute mb-inset-0"
      style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);"
    ></div>

    <!-- Mask layer (text_reveals_video mode) -->
    <div
      v-if="s.mask_mode === 'text_reveals_video'"
      class="mb-absolute mb-inset-0 mb-flex mb-items-center mb-justify-center"
      :style="maskLayerStyle"
    >
      <div :style="textStyleReveal" v-html="displayText" data-olo-editable="text"></div>
    </div>

    <!-- Other modes -->
    <div
      v-else
      class="mb-absolute mb-inset-0 mb-flex mb-items-center mb-justify-center"
      :style="{ padding: `${parseInt(s.padding_y)||0}px ${parseInt(s.padding_x)||0}px`, alignItems: vaAlign }"
    >
      <div :style="textStyleOther" v-html="displayText" data-olo-editable="text"></div>
    </div>

    <!-- Mode indicator -->
    <div class="mb-absolute mb-bottom-2 mb-right-2 mb-text-[9px] mb-text-white/50 mb-bg-black/40 mb-px-2 mb-py-0.5 mb-rounded">
      {{ modeLabel }}
      <span v-if="s.scroll_animate" class="mb-ml-1">{{ t('+ scroll') }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  text: 'WELCOME\nTO THE WORLD',
  multiline: true,
  font_size: '120',
  font_weight: '900',
  font_family: '',
  text_transform: 'uppercase',
  letter_spacing: '5',
  line_height: '1',
  text_align: 'center',
  video_url: '',
  video_poster: '',
  video_opacity: '100',
  min_height: '100vh',
  padding_y: '0',
  padding_x: '0',
  vertical_align: 'center',
  bg_color: '#000000',
  mask_mode: 'text_reveals_video',
  blend_mode: 'normal',
  text_fill: '#ffffff',
  scroll_animate: true,
  scroll_start: '0',
  scroll_end: '100',
  scroll_scale: true,
  scroll_opacity: true,
  scroll_blur: false,
  overlay_color: '',
  overlay_opacity: '0',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const displayText = computed(() => {
  const raw = s.value.text || 'WELCOME';
  // Escape HTML, then convert newlines to <br>
  const escaped = raw.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  return s.value.multiline ? escaped.replace(/\n/g, '<br>') : escaped;
});

const vaAlign = computed(() => {
  const va = s.value.vertical_align;
  return va === 'top' ? 'flex-start' : va === 'bottom' ? 'flex-end' : 'center';
});

const containerStyle = computed(() => {
  const h = s.value.min_height || '100vh';
  return {
    minHeight: h.includes('vh') ? '400px' : h,
    background: s.value.bg_color || '#000',
  };
});

const baseTextStyle = computed(() => {
  const fs = Math.min(parseInt(s.value.font_size) || 120, 200);
  return {
    fontSize: `${fs}px`,
    fontWeight: s.value.font_weight || '900',
    fontFamily: s.value.font_family || 'inherit',
    textTransform: s.value.text_transform || 'uppercase',
    letterSpacing: `${parseInt(s.value.letter_spacing) || 0}px`,
    lineHeight: s.value.line_height || '1',
    textAlign: s.value.text_align || 'center',
    width: '100%',
    whiteSpace: s.value.multiline ? 'normal' : 'nowrap',
    userSelect: 'none',
    position: 'relative',
  };
});

// Detect dark/light bg
const isDark = computed(() => {
  const hex = (s.value.bg_color || '#000').replace('#', '');
  const full = hex.length === 3
    ? hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2]
    : hex;
  const rp = parseInt(full.substr(0, 2), 16); const r = !isNaN(rp) ? rp : 0;
  const gp = parseInt(full.substr(2, 2), 16); const g = !isNaN(gp) ? gp : 0;
  const bp = parseInt(full.substr(4, 2), 16); const b = !isNaN(bp) ? bp : 0;
  return (0.299 * r + 0.587 * g + 0.114 * b) < 128;
});

// text_reveals_video: dark bg → multiply/screen, light bg → screen/multiply
const maskLayerStyle = computed(() => ({
  background: s.value.bg_color || '#000',
  isolation: 'isolate',
  mixBlendMode: isDark.value ? 'multiply' : 'screen',
  padding: `${parseInt(s.value.padding_y)||0}px ${parseInt(s.value.padding_x)||0}px`,
  alignItems: vaAlign.value,
  zIndex: 2,
}));

const textStyleReveal = computed(() => ({
  ...baseTextStyle.value,
  color: isDark.value ? '#fff' : '#000',
  mixBlendMode: isDark.value ? 'screen' : 'multiply',
}));

// Other modes
const textStyleOther = computed(() => {
  const st = { ...baseTextStyle.value, zIndex: 2 };
  if (s.value.mask_mode === 'video_behind_text') {
    st.color = 'transparent';
    st.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
    st.WebkitBackgroundClip = 'text';
    st.backgroundClip = 'text';
  } else {
    st.color = s.value.text_fill || '#ffffff';
    st.mixBlendMode = s.value.blend_mode || 'normal';
  }
  return st;
});

const modeLabel = computed(() => {
  const labels = {
    text_reveals_video: 'Knockout',
    video_behind_text: 'Clip',
    blend: 'Blend',
  };
  return labels[s.value.mask_mode] || 'Mask';
});
</script>
