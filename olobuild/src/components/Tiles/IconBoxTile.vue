<template>
  <div
    class="mb-py-8 mb-px-6"
    :style="containerStyle"
  >
    <!-- Icon -->
    <div v-if="s.icon_emoji" :class="{ 'mb-mb-4': !isHorizontal }" :style="isHorizontal ? { flexShrink: 0 } : {}">
      <div :style="iconWrapperStyle">
        <span
          v-if="iconSvg"
          class="iconbox-icon-preview"
          :style="{ width: '1em', height: '1em' }"
          v-html="iconSvg"
        ></span>
        <template v-else>{{ s.icon_emoji }}</template>
      </div>
    </div>
    <!-- Content -->
    <div :style="isHorizontal ? { flex: 1 } : {}">
      <h3 class="mb-text-xl mb-font-semibold mb-mb-2" :style="{ fontSize: s.title_font_size + 'px', fontWeight: s.title_font_weight }" data-olo-editable="title">{{ s.title }}</h3>
      <div class="mb-opacity-80 mb-leading-relaxed mb-mb-4" style="white-space:pre-wrap" data-olo-editable="description" data-olo-multiline>{{ s.description }}</div>
      <span v-if="s.link_url" class="mb-font-medium" :style="{ color: s.link_color }" data-olo-editable="link_text">
        {{ s.link_text }} &rarr;
      </span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
import { useBuilderStore } from '@/stores/builder';
import { rv } from '@/composables/useResponsiveValue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const builderStore = useBuilderStore();

const defaults = {
  icon_emoji: '\uD83D\uDE80',
  title: 'Titolo funzionalit\u00e0',
  description: 'Una breve descrizione.',
  link_url: '',
  link_text: 'Scopri di pi\u00f9',
  alignment: 'center',
  text_color: 'var(--olo-color-text, #374151)',
  icon_size: '3',
  icon_position: 'top',
  icon_bg_color: '',
  icon_bg_shape: 'circle',
  icon_color: '',
  title_font_size: '20',
  title_font_weight: '600',
  link_color: 'var(--olo-color-primary, #6366F1)',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const iconSvg = computed(() => iconsSvg[s.value.icon_emoji] || '');

const isHorizontal = computed(() => s.value.icon_position === 'left' || s.value.icon_position === 'right');
const containerStyle = computed(() => {
  const align = rv(props.settings, 'alignment', s.value.alignment, builderStore.viewMode);
  const st = { textAlign: align, color: s.value.text_color, minHeight: '80px' };
  if (isHorizontal.value) {
    st.display = 'flex';
    st.flexDirection = s.value.icon_position === 'right' ? 'row-reverse' : 'row';
    st.alignItems = 'flex-start';
    st.gap = '16px';
    st.textAlign = 'left';
  }
  return st;
});

const iconWrapperStyle = computed(() => {
  const st = { fontSize: s.value.icon_size + 'em', lineHeight: 1 };
  const bgClr = s.value.icon_bg_color;
  if (bgClr) {
    const shape = s.value.icon_bg_shape || 'circle';
    st.background = bgClr;
    st.padding = '16px';
    st.display = 'inline-flex';
    st.alignItems = 'center';
    st.justifyContent = 'center';
    st.borderRadius = shape === 'circle' ? '50%' : shape === 'rounded' ? '12px' : '4px';
  }
  if (s.value.icon_color) st.color = s.value.icon_color;
  return st;
});
</script>

<style scoped>
.iconbox-icon-preview {
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.iconbox-icon-preview :deep(svg) {
  width: 100%;
  height: 100%;
  fill: currentColor;
  stroke: currentColor;
}
</style>
