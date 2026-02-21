<template>
  <div class="mb-p-8">
    <div :class="wrapClass" :style="wrapStyle">
      <!-- Image -->
      <div v-if="s.image" :class="imgColClass" :style="imgColStyle">
        <div class="mb-relative mb-overflow-hidden" :style="imgWrapStyle">
          <img
            :src="s.image"
            alt=""
            class="mb-w-full mb-block"
            :style="imgStyle"
          />
          <!-- hover indicator -->
          <div
            v-if="s.hover_image || s.hover_video"
            class="mb-absolute mb-top-1 mb-right-1 mb-bg-black/60 mb-text-white mb-text-xs mb-px-1.5 mb-py-0.5 mb-rounded"
          >
            {{ s.hover_video ? '▶ hover' : '⇄ hover' }}
          </div>
        </div>
      </div>

      <!-- Text -->
      <div :class="textColClass">
        <h2 class="mb-text-xl mb-font-bold mb-text-gray-100 mb-mb-2" v-html="s.heading || 'Titolo Sezione'"></h2>
        <div class="mb-text-gray-400 mb-leading-relaxed" v-html="s.text || 'Il contenuto va qui...'"></div>
        <div v-if="s.link_url" class="mb-mt-2 mb-text-blue-400 mb-text-sm mb-truncate">🔗 {{ s.link_url }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({
  heading: '',
  text: '',
  image: '',
  image_position: 'top',
  image_width: '40',
  image_height: 'auto',
  image_fit: 'cover',
  image_radius: '0',
  image_border_width: '0',
  image_border_color: '#e5e7eb',
  image_shadow: 'none',
  image_gap: '16',
  hover_effect: 'none',
  hover_image: '',
  hover_video: '',
  link_url: '',
  link_target: '_self',
  ...props.settings,
}));

const isHorizontal = computed(() => s.value.image_position === 'left' || s.value.image_position === 'right');
const isReversed = computed(() => s.value.image_position === 'bottom' || s.value.image_position === 'right');

const wrapClass = computed(() => {
  return isHorizontal.value
    ? 'mb-flex mb-flex-row mb-items-start'
    : 'mb-flex mb-flex-col';
});

const wrapStyle = computed(() => {
  if (isReversed.value) return { flexDirection: isHorizontal.value ? 'row-reverse' : 'column-reverse' };
  return {};
});

const imgColClass = computed(() => {
  return isHorizontal.value ? 'mb-flex-shrink-0' : '';
});

const imgColStyle = computed(() => {
  const gap = parseInt(s.value.image_gap) || 0;
  const style = {};
  if (isHorizontal.value) {
    style.width = s.value.image_width + '%';
    style[isReversed.value ? 'marginLeft' : 'marginRight'] = gap + 'px';
  } else {
    style[isReversed.value ? 'marginTop' : 'marginBottom'] = gap + 'px';
  }
  return style;
});

const textColClass = computed(() => {
  return isHorizontal.value ? 'mb-flex-1 mb-min-w-0' : '';
});

const shadowMap = {
  none: 'none',
  sm: '0 1px 2px rgba(0,0,0,.05)',
  md: '0 4px 6px rgba(0,0,0,.1)',
  lg: '0 10px 15px rgba(0,0,0,.1)',
  xl: '0 20px 25px rgba(0,0,0,.1)',
};

const imgWrapStyle = computed(() => {
  const r = parseInt(s.value.image_radius) || 0;
  const bw = parseInt(s.value.image_border_width) || 0;
  const style = { borderRadius: r + 'px' };
  if (bw > 0) {
    style.border = bw + 'px solid ' + (s.value.image_border_color || '#e5e7eb');
  }
  const sh = shadowMap[s.value.image_shadow] || 'none';
  if (sh !== 'none') style.boxShadow = sh;
  return style;
});

const imgStyle = computed(() => {
  const h = s.value.image_height;
  const style = {
    objectFit: s.value.image_fit || 'cover',
  };
  if (h && h !== 'auto') {
    style.height = /^\d+$/.test(h) ? h + 'px' : h;
  }
  return style;
});
</script>
