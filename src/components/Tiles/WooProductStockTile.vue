<template>
  <div>
    <div v-if="!wooActive" class="olo-woo-notice">
      <span class="olo-woo-notice-icon">&#x1F6D2;</span>
      <span>WooCommerce richiesto</span>
    </div>
    <div v-else :style="wrapStyle">
      <span v-if="s.show_icon" :style="dotStyle"></span>
      <span>Disponibile{{ s.show_quantity ? ' (24)' : '' }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  show_quantity: true,
  show_icon: true,
  in_stock_color: '#059669',
  out_of_stock_color: '#DC2626',
  low_stock_color: '#D97706',
  low_stock_threshold: '5',
  font_size: '14',
  font_weight: '500',
  text_align: 'left',
  icon_size: '10',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const wooActive = computed(() => true);

const stockColor = computed(() => s.value.in_stock_color || '#059669');

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
  background: #FEF3C7;
  border: 1px solid #F59E0B;
  border-radius: 8px;
  color: #92400E;
  font-size: 14px;
  font-weight: 500;
}
.olo-woo-notice-icon {
  font-size: 20px;
}
</style>
