<template>
  <div :style="wrapStyle">
    <span v-if="s.prefix" :style="{ color: s.price_color, fontSize: (parseInt(s.font_size) * 0.65) + 'px', marginRight: '6px' }" data-olo-editable="prefix">{{ s.prefix }}</span>
    <span v-if="s.show_regular" :style="regularStyle">49,00</span>
    <span v-if="s.show_sale" :style="saleStyle">39,00</span>
    <span v-if="s.show_suffix" :style="{ color: s.price_color, fontSize: (parseInt(s.font_size) * 0.55) + 'px', marginLeft: '4px', opacity: 0.7 }" data-olo-editable="suffix">{{ s.suffix || '+ IVA' }}</span>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  show_regular: true,
  show_sale: true,
  show_suffix: false,
  price_color: 'var(--olo-color-text, #374151)',
  sale_color: '#EF4444',
  regular_color: '#9CA3AF',
  font_size: '24',
  font_weight: '700',
  text_align: 'left',
  prefix: '',
  suffix: '',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const wrapStyle = computed(() => ({
  display: 'flex',
  alignItems: 'baseline',
  gap: '8px',
  textAlign: s.value.text_align,
  justifyContent: s.value.text_align === 'center' ? 'center' : s.value.text_align === 'right' ? 'flex-end' : 'flex-start',
  padding: '8px 0',
}));

const regularStyle = computed(() => ({
  textDecoration: s.value.show_sale ? 'line-through' : 'none',
  color: s.value.show_sale ? (s.value.regular_color || '#9CA3AF') : (s.value.price_color || 'var(--olo-color-text, #374151)'),
  fontSize: s.value.show_sale ? (parseInt(s.value.font_size) * 0.75) + 'px' : s.value.font_size + 'px',
  fontWeight: s.value.show_sale ? '400' : s.value.font_weight,
}));

const saleStyle = computed(() => ({
  color: s.value.sale_color || '#EF4444',
  fontSize: s.value.font_size + 'px',
  fontWeight: s.value.font_weight || '700',
}));
</script>
