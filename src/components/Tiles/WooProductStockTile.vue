<template>
  <div>
    <div v-if="!wooActive" class="olo-woo-notice">
      <span class="olo-woo-notice-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor"><circle cx="7.3" cy="17.3" r="1.4" fill="currentColor" stroke="none"/><circle cx="13.3" cy="17.3" r="1.4" fill="currentColor" stroke="none"/><polyline points="0 2 3.2 4 5.3 12.5 16 12.5 18 6.5 8 6.5"/></svg></span>
      <span>{{ t('WooCommerce richiesto') }}</span>
    </div>
    <div v-else :style="wrapStyle">
      <span v-if="s.show_icon" :style="dotStyle"></span>
      <span>Disponibile{{ s.show_quantity ? ' (24)' : '' }}</span>
    </div>
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
  show_quantity: true,
  show_icon: true,
  in_stock_color: '',       // '' ⇒ TOKENS.success
  out_of_stock_color: '',   // '' ⇒ TOKENS.error
  low_stock_color: '',      // '' ⇒ TOKENS.warning
  low_stock_threshold: '5',
  font_size: '14',
  font_weight: '500',
  text_align: 'left',
  icon_size: '10',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const wooActive = computed(() => true);

// Anteprima builder: stato "disponibile" ⇒ semantico success (token-first).
const stockColor = computed(() => resolveColor(s.value.in_stock_color, TOKENS.success.fg));

const wrapStyle = computed(() => ({
  display: 'flex',
  alignItems: 'center',
  gap: '8px',
  fontSize: parseInt(s.value.font_size) + 'px',
  fontWeight: s.value.font_weight || '500',
  color: stockColor.value,
  justifyContent: s.value.text_align === 'center' ? 'center' : s.value.text_align === 'right' ? 'flex-end' : 'flex-start',
}));

const dotStyle = computed(() => ({
  width: parseInt(s.value.icon_size) + 'px',
  height: parseInt(s.value.icon_size) + 'px',
  borderRadius: '50%',
  background: stockColor.value,
  flexShrink: '0',
}));
</script>

<style scoped>
.olo-woo-notice {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 16px 20px;
  background: color-mix(in srgb, var(--olo-color-warning, #b45309) 12%, #fff);
  border: 1px solid var(--olo-color-warning, #b45309);
  border-radius: 8px;
  color: var(--olo-color-warning, #b45309);
  font-size: 14px;
  font-weight: 500;
}
.olo-woo-notice-icon {
  width: 20px;
  height: 20px;
  display: inline-flex;
  flex-shrink: 0;
}
.olo-woo-notice-icon :deep(svg) {
  width: 100%;
  height: 100%;
}
</style>
