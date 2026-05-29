<template>
  <div
    class="olo-iconbox-tile"
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
      <h3 class="mb-text-xl mb-font-semibold mb-mb-2" :style="{ fontSize: s.title_font_size + 'px', fontWeight: s.title_font_weight, color: s.title_color || undefined }" data-olo-editable="title">{{ s.title }}</h3>
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
  icon_emoji: 'star',
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
  link_color: 'var(--olo-color-primary, #e1474f)',
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
    st.gap = (s.value.icon_gap || 16) + 'px';
    st.textAlign = 'left';
  }
  // Tile background (mutually exclusive via bg_type)
  const bgType = s.value.bg_type || 'none';
  if (bgType === 'color' && s.value.bg_color) {
    st.backgroundColor = s.value.bg_color;
  } else if (bgType === 'gradient' && s.value.bg_gradient) {
    const g = s.value.bg_gradient;
    if (g && g.stops && g.stops.length) {
      const stops = g.stops.map(s => s.color + ' ' + s.position + '%').join(', ');
      st.background = (g.type === 'radial' ? 'radial-gradient(circle, ' : 'linear-gradient(' + (g.angle || 180) + 'deg, ') + stops + ')';
    }
  } else if (bgType === 'image' && s.value.bg_image) {
    st.backgroundImage = 'url(' + s.value.bg_image + ')';
    st.backgroundSize = s.value.bg_image_size || 'cover';
    st.backgroundPosition = s.value.bg_image_position || 'center center';
    st.backgroundRepeat = 'no-repeat';
  }
  // Tile padding
  const tp = s.value.tile_padding;
  if (tp && typeof tp === 'object') {
    st.padding = (tp.top || 24) + 'px ' + (tp.right || 24) + 'px ' + (tp.bottom || 24) + 'px ' + (tp.left || 24) + 'px';
  }
  // Border
  const bw = parseInt(s.value.border_width) || 0;
  if (bw > 0) {
    st.border = bw + 'px ' + (s.value.border_style || 'solid') + ' ' + (s.value.border_color || 'var(--olo-color-border, #e5e7eb)');
  }
  // Border radius
  const br = s.value.border_radius;
  if (br && typeof br === 'object') {
    st.borderRadius = (br.tl || 0) + 'px ' + (br.tr || 0) + 'px ' + (br.br || 0) + 'px ' + (br.bl || 0) + 'px';
  } else if (parseInt(br) > 0) {
    st.borderRadius = parseInt(br) + 'px';
  }
  // Shadow
  const sh = s.value.shadow;
  if (sh && sh !== 'none') {
    const shadowMap = { sm: '0 1px 3px rgba(0,0,0,0.1)', md: '0 4px 10px rgba(0,0,0,0.12)', lg: '0 8px 25px rgba(0,0,0,0.15)', xl: '0 12px 40px rgba(0,0,0,0.2)' };
    if (sh === 'custom') {
      const h = s.value.shadow_h || 0, v = s.value.shadow_v || 4, bl = s.value.shadow_blur || 10, sp = s.value.shadow_spread || 0;
      const col = s.value.shadow_color || 'rgba(0,0,0,0.15)';
      const ins = s.value.shadow_inset ? 'inset ' : '';
      st.boxShadow = ins + h + 'px ' + v + 'px ' + bl + 'px ' + sp + 'px ' + col;
    } else if (shadowMap[sh]) {
      st.boxShadow = shadowMap[sh];
    }
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
