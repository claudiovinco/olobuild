<template>
  <div :style="{ padding: '10px', background: TOKENS.surfaceAlt, borderRadius: '8px', minHeight: '60px', display: 'flex', alignItems: 'center', justifyContent: 'center' }">
    <button class="olo-woo-qv-btn" :style="btnStyle">
      <svg style="width:16px;height:16px;margin-right:6px;flex-shrink:0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
        <circle cx="12" cy="12" r="3"/>
      </svg>
      <span data-olo-editable="button_text">{{ s.button_text || t('Anteprima rapida') }}</span>
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  button_text: t('Anteprima rapida'),
  button_bg: '',            // '' ⇒ TOKENS.primary (CTA = brand)
  button_color: '',         // '' ⇒ TOKENS.onPrimary
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const btnStyle = computed(() => ({
  display: 'inline-flex',
  alignItems: 'center',
  padding: '10px 20px',
  background: resolveColor(s.value.button_bg, TOKENS.primary),
  color: resolveColor(s.value.button_color, TOKENS.onPrimary),
  border: 'none',
  borderRadius: '6px',
  fontSize: '13px',
  fontWeight: 600,
  cursor: 'pointer',
}));
</script>

<style scoped>
.olo-woo-qv-btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
</style>
