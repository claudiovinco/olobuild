<template>
  <div>
    <div v-if="!wooActive" class="olo-woo-notice">
      <span class="olo-woo-notice-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor"><circle cx="7.3" cy="17.3" r="1.4" fill="currentColor" stroke="none"/><circle cx="13.3" cy="17.3" r="1.4" fill="currentColor" stroke="none"/><polyline points="0 2 3.2 4 5.3 12.5 16 12.5 18 6.5 8 6.5"/></svg></span>
      <span>{{ t('WooCommerce richiesto') }}</span>
    </div>
    <component
      v-else
      :is="s.tag"
      :style="titleStyle"
    >{{ t('Titolo del prodotto') }}</component>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  tag: 'h1',
  text_align: 'left',
  color: 'var(--olo-color-text, #374151)',
  font_size: '32',
  font_weight: '700',
  line_height: '1.2',
  link_to_product: false,
  link_color_hover: 'var(--olo-color-primary, #e1474f)',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const wooActive = computed(() => true);

const titleStyle = computed(() => ({
  margin: '0',
  padding: '0',
  textAlign: s.value.text_align,
  color: s.value.color || 'var(--olo-color-text, #374151)',
  fontSize: parseInt(s.value.font_size) + 'px',
  fontWeight: s.value.font_weight || '700',
  lineHeight: s.value.line_height || '1.2',
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
