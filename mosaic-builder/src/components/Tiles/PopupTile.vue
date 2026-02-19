<template>
  <div :style="wrapStyle">
    <button type="button" :style="btnStyle">
      <span v-if="s.button_icon && iconSvg" class="olo-popup-icon" :style="iconStyle" v-html="iconSvg"></span>
      {{ s.button_text || 'Apri' }}
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({
  button_text: 'Apri',
  button_style: 'default',
  button_size: '',
  button_icon: '',
  button_fullwidth: false,
  mode: 'simple',
  ...props.settings,
}));

const iconSvg = computed(() => iconsSvg[s.value.button_icon] || '');

const styleMap = {
  default:   { bg: '#222', color: '#fff', border: 'none' },
  primary:   { bg: 'var(--olo-color-primary, #6366F1)', color: '#fff', border: 'none' },
  secondary: { bg: '#e5e7eb', color: '#222', border: 'none' },
  danger:    { bg: '#dc2626', color: '#fff', border: 'none' },
  text:      { bg: 'transparent', color: '#999', border: 'none', textDecoration: 'none' },
  link:      { bg: 'transparent', color: 'var(--olo-color-primary, #6366F1)', border: 'none', textDecoration: 'none' },
};

const sizeMap = {
  '':      { padding: '8px 24px', fontSize: '14px' },
  small:   { padding: '5px 16px', fontSize: '12px' },
  large:   { padding: '12px 32px', fontSize: '16px' },
};

const wrapStyle = computed(() => {
  if (s.value.button_fullwidth) {
    return { display: 'block' };
  }
  return { display: 'inline-block' };
});

const btnStyle = computed(() => {
  const st = styleMap[s.value.button_style] || styleMap.default;
  const sz = sizeMap[s.value.button_size] || sizeMap[''];
  const base = {
    display: 'inline-flex',
    alignItems: 'center',
    gap: '6px',
    background: st.bg,
    color: st.color,
    border: st.border,
    padding: sz.padding,
    fontSize: sz.fontSize,
    fontWeight: '500',
    lineHeight: '1.4',
    borderRadius: '2px',
    cursor: 'default',
    textTransform: 'uppercase',
    letterSpacing: '0.5px',
    fontFamily: 'inherit',
  };
  if (st.textDecoration) base.textDecoration = st.textDecoration;
  if (s.value.button_fullwidth) base.width = '100%';
  return base;
});

const iconStyle = computed(() => {
  const sz = s.value.button_size === 'small' ? '14px' : s.value.button_size === 'large' ? '18px' : '16px';
  return { display: 'inline-flex', width: sz, height: sz, flexShrink: '0' };
});
</script>

<style scoped>
.olo-popup-icon :deep(svg) {
  width: 100%;
  height: 100%;
  fill: currentColor;
  stroke: currentColor;
}
</style>
