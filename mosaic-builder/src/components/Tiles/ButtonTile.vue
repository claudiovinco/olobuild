<template>
  <div
    class="mb-py-4"
    :style="{ display: 'flex', justifyContent: alignMap[s.alignment] || 'center' }"
  >
    <span
      :style="btnStyle"
      class="mb-relative"
    >
      {{ s.text || 'Clicca qui' }}
      <!-- hover indicator -->
      <span
        v-if="s.hover_image || s.hover_video"
        class="mb-absolute mb--top-2 mb--right-2 mb-bg-black/70 mb-text-white mb-rounded-full mb-w-5 mb-h-5 mb-flex mb-items-center mb-justify-center"
        style="font-size: 10px; line-height: 1;"
      >{{ s.hover_video ? '▶' : '⇄' }}</span>
    </span>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({
  text: 'Clicca qui',
  url: '#',
  alignment: 'center',
  full_width: false,
  bg_color: '#6366F1',
  text_color: '#FFFFFF',
  border_radius: '6',
  padding_x: '32',
  padding_y: '14',
  font_size: '16',
  font_weight: '600',
  letter_spacing: '0',
  text_transform: 'none',
  border_width: '0',
  border_color: '#6366F1',
  shadow: 'none',
  hover_image: '',
  hover_video: '',
  ...props.settings,
}));

const shadowMap = {
  none: 'none',
  sm: '0 1px 2px rgba(0,0,0,.08)',
  md: '0 4px 6px rgba(0,0,0,.12)',
  lg: '0 10px 15px rgba(0,0,0,.12)',
  xl: '0 20px 25px rgba(0,0,0,.15)',
};

const alignMap = {
  left: 'flex-start',
  center: 'center',
  right: 'flex-end',
};

const btnStyle = computed(() => {
  const bw = parseInt(s.value.border_width) || 0;
  const ls = parseFloat(s.value.letter_spacing) || 0;
  const rad = s.value.border_radius;
  let borderRadius;
  if (typeof rad === 'object' && rad !== null) {
    borderRadius = `${rad.tl || 0}px ${rad.tr || 0}px ${rad.br || 0}px ${rad.bl || 0}px`;
  } else {
    borderRadius = `${rad || 6}px`;
  }

  const style = {
    display: 'inline-block',
    width: s.value.full_width ? '100%' : 'auto',
    textAlign: 'center',
    padding: `${s.value.padding_y || 14}px ${s.value.padding_x || 32}px`,
    backgroundColor: s.value.bg_color || '#6366F1',
    color: s.value.text_color || '#FFFFFF',
    borderRadius,
    fontSize: `${s.value.font_size || 16}px`,
    fontWeight: s.value.font_weight || '600',
    cursor: 'pointer',
    textTransform: s.value.text_transform !== 'none' ? s.value.text_transform : undefined,
  };

  if (ls > 0) style.letterSpacing = ls + 'px';
  if (bw > 0) style.border = `${bw}px solid ${s.value.border_color || '#6366F1'}`;

  const sh = shadowMap[s.value.shadow] || 'none';
  if (sh !== 'none') style.boxShadow = sh;

  return style;
});
</script>
