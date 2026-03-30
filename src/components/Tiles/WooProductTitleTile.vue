<template>
  <div>
    <div v-if="!wooActive" class="olo-woo-notice">
      <span class="olo-woo-notice-icon">&#x1F6D2;</span>
      <span>WooCommerce richiesto</span>
    </div>
    <component
      v-else
      :is="s.tag"
      :style="titleStyle"
    >Titolo del prodotto</component>
  </div>
</template>

<script setup>
import { computed } from 'vue';

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
  link_color_hover: 'var(--olo-color-primary, #6366F1)',
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
