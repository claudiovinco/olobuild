<template>
  <div class="mb-py-4 mb-px-4">
    <div :style="listStyle">
      <div v-for="(item, i) in items" :key="item.id || i" :style="itemStyle">
        <span :style="iconStyle(item)" class="olo-il-icon">
          <span v-if="iconSvg(item.icon)" v-html="iconSvg(item.icon)" :style="{ width: iconSize + 'px', height: iconSize + 'px', display: 'inline-flex' }"></span>
          <span v-else style="line-height:1;">{{ item.icon || '✓' }}</span>
        </span>
        <span :style="{ color: resolveColor(s.text_color, TOKENS.text), fontSize: (parseInt(s.text_size) || 16) + 'px', lineHeight: '1.4' }" :data-olo-editable="'items.' + i + '.text'">{{ item.text || 'Voce' }}</span>
        <span v-if="item.link" class="olo-il-link-badge" :title="t('Link')">{{ t('&#x1F517;') }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
import { t } from '@/i18n';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  items: [
    { id: 'il-1', icon: 'check', text: 'Prima voce della lista', color: '' },
    { id: 'il-2', icon: 'check', text: 'Seconda voce della lista', color: '' },
    { id: 'il-3', icon: 'check', text: 'Terza voce della lista', color: '' },
  ],
  icon_color: '',
  icon_size: '20',
  text_color: '',
  text_size: '16',
  gap: '12',
  icon_shape: 'none',
  icon_bg_color: '',
  divider: false,
  divider_color: '',
  layout: 'vertical',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const items = computed(() => {
  const raw = s.value.items;
  return Array.isArray(raw) ? raw : [];
});

const iconSize = computed(() => parseInt(s.value.icon_size) || 20);
const gap = computed(() => parseInt(s.value.gap) || 12);

const listStyle = computed(() => ({
  display: 'flex',
  flexDirection: s.value.layout === 'horizontal' ? 'row' : 'column',
  flexWrap: s.value.layout === 'horizontal' ? 'wrap' : 'nowrap',
  gap: gap.value + 'px',
}));

const itemStyle = computed(() => {
  const st = { display: 'flex', alignItems: 'center', gap: '10px' };
  if (s.value.divider && s.value.layout === 'vertical') {
    st.paddingBottom = gap.value + 'px';
    st.borderBottom = '1px solid ' + resolveColor(s.value.divider_color, TOKENS.border);
  }
  return st;
});

function iconStyle(item) {
  const clr = resolveColor(item.color || s.value.icon_color, TOKENS.success.fg);
  const shape = s.value.icon_shape || 'none';
  const st = {
    color: clr,
    flexShrink: 0,
    display: 'inline-flex',
    alignItems: 'center',
    justifyContent: 'center',
  };
  if (shape !== 'none') {
    st.background = s.value.icon_bg_color || 'color-mix(in srgb, var(--olo-color-success, #10B981) 15%, transparent)';
    st.width = (iconSize.value + 16) + 'px';
    st.height = (iconSize.value + 16) + 'px';
    st.borderRadius = shape === 'circle' ? '50%' : shape === 'rounded' ? '8px' : '4px';
  }
  return st;
}

function iconSvg(name) {
  return iconsSvg[name] || '';
}
</script>

<style scoped>
.olo-il-link-badge {
  flex-shrink: 0;
  font-size: 12px;
  opacity: 0.5;
  margin-left: auto;
}
.olo-il-icon :deep(svg) {
  width: 100%;
  height: 100%;
  fill: currentColor;
  stroke: currentColor;
}
</style>
