<template>
  <div
    class="olo-alert mb-rounded-lg mb-px-5 mb-py-4"
    role="alert"
    :style="{ background: effectiveStyle.bg, borderLeft: '4px solid ' + effectiveStyle.fg, minHeight: '60px', color: textColor }"
  >
    <div class="mb-flex mb-items-start mb-gap-3">
      <span v-if="iconSvg" class="olo-alert-icon mb-flex-shrink-0" :style="{ width: '20px', height: '20px', color: effectiveStyle.fg }" v-html="iconSvg"></span>
      <div class="mb-flex-1">
        <div v-if="s.title" class="mb-font-semibold mb-mb-1" :style="{ color: textColor }" data-olo-editable="title">{{ s.title }}</div>
        <div class="mb-text-sm mb-opacity-90 mb-leading-relaxed" :style="{ color: textColor, whiteSpace: 'pre-wrap' }" data-olo-editable="message" data-olo-multiline>{{ s.message || 'Alert message here.' }}</div>
      </div>
      <button
        v-if="s.dismissible"
        type="button"
        class="olo-alert-close mb-flex-shrink-0"
        :aria-label="t('Chiudi avviso')"
        :style="{ color: effectiveStyle.fg }"
        v-html="closeSvg"
      ></button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
import { t } from '@/i18n';
import { resolveColor, TOKENS, SYSTEM, buildDefaults } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

// Fonte UNICA dei default (allineata a alert.js)
const s = computed(() => ({ ...buildDefaults('alert'), ...props.settings }));

// Token semantici (no più palette fisse #DBEAFE/#3B82F6…): fg dal ruolo globale,
// bg = tinta soft derivata via color-mix. Icone SVG (no più emoji).
const SEMANTIC = {
  info:    { fg: SYSTEM.info.fg,    bg: SYSTEM.info.bg,    icon: 'info' },
  success: { fg: SYSTEM.success.fg, bg: SYSTEM.success.bg, icon: 'check' },
  warning: { fg: SYSTEM.warning.fg, bg: SYSTEM.warning.bg, icon: 'warning' },
  error:   { fg: SYSTEM.error.fg,   bg: SYSTEM.error.bg,   icon: 'ban' },
};

const effectiveStyle = computed(() => {
  const base = SEMANTIC[s.value.alert_type] || SEMANTIC.info;
  return {
    fg: base.fg,
    bg: resolveColor(s.value.custom_bg_color, base.bg),
    iconName: base.icon,
  };
});

const textColor = computed(() => resolveColor(s.value.custom_text_color, TOKENS.text));

// Icona: custom se scelta, altrimenti l'icona SVG semantica del tipo (mai emoji)
const iconSvg = computed(() => {
  if (s.value.show_icon === false) return '';
  const name = s.value.custom_icon || effectiveStyle.value.iconName;
  return iconsSvg[name] || '';
});

const closeSvg = computed(() => iconsSvg['close'] || iconsSvg['x'] || '✕');
</script>

<style scoped>
.olo-alert-icon :deep(svg),
.olo-alert-close :deep(svg) {
  width: 100%;
  height: 100%;
  fill: currentColor;
  stroke: currentColor;
}
.olo-alert-close {
  width: 20px;
  height: 20px;
  padding: 0;
  background: none;
  border: none;
  cursor: pointer;
  line-height: 1;
  opacity: 0.7;
  border-radius: 4px;
  transition: opacity 0.15s;
}
.olo-alert-close:hover { opacity: 1; }
.olo-alert-close:focus-visible {
  outline: none;
  opacity: 1;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
</style>
