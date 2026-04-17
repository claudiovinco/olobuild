<template>
  <div
    class="mb-py-4"
    :style="{ display: 'flex', justifyContent: alignMap[rv(settings, 'alignment', s.alignment, builderStore.viewMode)] || 'center' }"
  >
    <span
      :style="btnStyle"
      class="mb-relative"
    >
      <span style="display:inline-flex;align-items:center;" :style="{ flexDirection: s.icon_position === 'after' ? 'row-reverse' : 'row', gap: (parseInt(s.icon_spacing) || 8) + 'px' }">
        <span v-if="iconSvg" class="olo-btn-icon" :style="{ width: '1em', height: '1em', display: 'inline-flex', alignItems: 'center', justifyContent: 'center' }" v-html="iconSvg"></span>
        <span data-olo-editable="text">{{ s.text || 'Clicca qui' }}</span>
      </span>
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
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
import { useBuilderStore } from '@/stores/builder';
import { rv } from '@/composables/useResponsiveValue';
import { getShadowValue } from '@/composables/useShadowMap';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const builderStore = useBuilderStore();

const s = computed(() => ({
  text: 'Clicca qui',
  url: '#',
  alignment: 'center',
  full_width: false,
  bg_color: '#6366F1',
  text_color: '#FFFFFF',
  border_radius: '6',
  tile_padding: { top: 14, right: 32, bottom: 14, left: 32 },
  font_size: '16',
  font_weight: '600',
  letter_spacing: '0',
  text_transform: 'none',
  icon: '',
  icon_position: 'before',
  icon_spacing: '8',
  border_width: '0',
  border_color: '#6366F1',
  shadow: 'none',
  hover_image: '',
  hover_video: '',
  ...props.settings,
}));

const iconSvg = computed(() => iconsSvg[s.value.icon] || '');

const alignMap = {
  left: 'flex-start',
  center: 'center',
  right: 'flex-end',
};

const btnStyle = computed(() => {
  const mode = builderStore.viewMode;
  const bw = parseInt(s.value.border_width) || 0;
  const ls = parseFloat(s.value.letter_spacing) || 0;
  const rad = s.value.border_radius;
  let borderRadius;
  if (typeof rad === 'object' && rad !== null) {
    borderRadius = `${rad.tl || 0}px ${rad.tr || 0}px ${rad.br || 0}px ${rad.bl || 0}px`;
  } else {
    borderRadius = `${rad || 6}px`;
  }

  const fontSize = rv(props.settings, 'font_size', s.value.font_size, mode);

  // tile_padding (standard spacing object) with backward compat for legacy padding_x/padding_y
  const tp = s.value.tile_padding;
  let padTop = 14, padRight = 32, padBottom = 14, padLeft = 32;
  if (typeof tp === 'object' && tp !== null) {
    padTop = parseInt(tp.top) || 14;
    padRight = parseInt(tp.right) || 32;
    padBottom = parseInt(tp.bottom) || 14;
    padLeft = parseInt(tp.left) || 32;
  } else if (s.value.padding_y !== undefined || s.value.padding_x !== undefined) {
    // Legacy: padding_x / padding_y
    padTop = padBottom = parseInt(s.value.padding_y) || 14;
    padRight = padLeft = parseInt(s.value.padding_x) || 32;
  }

  const style = {
    display: 'inline-block',
    width: s.value.full_width ? '100%' : 'auto',
    textAlign: 'center',
    padding: `${padTop}px ${padRight}px ${padBottom}px ${padLeft}px`,
    backgroundColor: s.value.bg_color || '#6366F1',
    color: s.value.text_color || '#FFFFFF',
    borderRadius,
    fontSize: `${fontSize || 16}px`,
    fontWeight: s.value.font_weight || '600',
    cursor: 'pointer',
    textTransform: s.value.text_transform !== 'none' ? s.value.text_transform : undefined,
  };

  if (ls > 0) style.letterSpacing = ls + 'px';
  if (bw > 0) style.border = `${bw}px solid ${s.value.border_color || '#6366F1'}`;

  const sh = getShadowValue(s.value);
  if (sh !== 'none') style.boxShadow = sh;

  return style;
});
</script>

<style scoped>
.olo-btn-icon :deep(svg) {
  width: 100%;
  height: 100%;
  fill: currentColor;
  stroke: currentColor;
}
</style>
