<template>
  <div
    class="mb-rounded-lg mb-px-5 mb-py-4"
    :style="{ background: effectiveStyle.bg, borderLeft: '4px solid ' + effectiveStyle.border, minHeight: '60px', color: s.custom_text_color || undefined }"
  >
    <div class="mb-flex mb-items-start mb-gap-3">
      <span v-if="customIconSvg" class="olo-alert-icon mb-flex-shrink-0" :style="{ width: '20px', height: '20px', color: effectiveStyle.border }" v-html="customIconSvg"></span>
      <span v-else-if="s.show_icon !== false" class="mb-text-xl mb-flex-shrink-0">{{ effectiveStyle.icon }}</span>
      <div class="mb-flex-1">
        <div v-if="s.title" class="mb-font-semibold mb-mb-1" :style="{ color: s.custom_text_color || 'var(--olo-color-text, #374151)' }" data-olo-editable="title">{{ s.title }}</div>
        <div class="mb-text-sm mb-opacity-90 mb-leading-relaxed" :style="{ color: s.custom_text_color || 'var(--olo-color-text, #374151)', whiteSpace: 'pre-wrap' }" data-olo-editable="message" data-olo-multiline>{{ s.message || 'Alert message here.' }}</div>
      </div>
      <button v-if="s.dismissible" class="mb-flex-shrink-0 mb-text-white/60 mb-text-lg" style="background:none;border:none;cursor:pointer;line-height:1;padding:0 0 0 8px;">{{ t('&times;') }}</button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
import { t } from '@/i18n';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  alert_type: 'info',
  title: 'Attenzione!',
  message: 'Questo è un avviso informativo.',
  show_icon: true,
  custom_icon: '',
  dismissible: false,
  custom_bg_color: '',
  custom_border_color: '',
  custom_text_color: '',
  shadow: 'none',
  border_width: '0',
  border_color: '#e5e7eb',
  border_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const typeStyles = {
  info:    { bg: '#DBEAFE', border: '#3B82F6', icon: '\u2139\uFE0F' },
  success: { bg: '#DCFCE7', border: '#22C55E', icon: '\u2705' },
  warning: { bg: '#FEF9C3', border: '#EAB308', icon: '\u26A0\uFE0F' },
  error:   { bg: '#FEE2E2', border: '#EF4444', icon: '\u274C' },
};

const customIconSvg = computed(() => iconsSvg[s.value.custom_icon] || '');

const effectiveStyle = computed(() => {
  const base = typeStyles[s.value.alert_type] || typeStyles.info;
  return {
    bg: s.value.custom_bg_color || base.bg,
    border: s.value.custom_border_color || base.border,
    icon: base.icon,
  };
});
</script>

<style scoped>
.olo-alert-icon :deep(svg) {
  width: 100%;
  height: 100%;
  fill: currentColor;
  stroke: currentColor;
}
</style>
