<template>
  <div>
    <div v-if="!wooActive" class="olo-woo-notice">
      <span class="olo-woo-notice-icon">{{ t('&#x1F6D2;') }}</span>
      <span>{{ t('WooCommerce richiesto') }}</span>
    </div>
    <div v-else :style="wrapStyle">
      <!-- Stacked layout -->
      <template v-if="s.layout === 'stacked'">
        <span v-if="s.show_sku" :style="itemStyle">
          <span :style="labelStyle">{{ t('SKU:') }}</span>
          <span>{{ t('WC-001') }}</span>
        </span>
        <span v-if="s.show_categories" :style="itemStyle">
          <span :style="labelStyle">{{ t('Categoria:') }}</span>
          <span :style="linkStyle">{{ t('Magliette') }}</span>
        </span>
        <span v-if="s.show_tags" :style="itemStyle">
          <span :style="labelStyle">{{ t('Tag:') }}</span>
          <span :style="linkStyle">{{ t('Nuovo') }}</span>,
          <span :style="linkStyle">{{ t('Cotone') }}</span>
        </span>
      </template>
      <!-- Inline layout -->
      <template v-else>
        <span v-if="s.show_sku">
          <span :style="labelStyle">{{ t('SKU:') }}</span>
          <span>WC-001</span>
        </span>
        <span v-if="s.show_sku && (s.show_categories || s.show_tags)" :style="sepStyle">{{ s.separator }}</span>
        <span v-if="s.show_categories">
          <span :style="labelStyle">{{ t('Categoria:') }}</span>
          <span :style="linkStyle">{{ t('Magliette') }}</span>
        </span>
        <span v-if="s.show_categories && s.show_tags" :style="sepStyle">{{ s.separator }}</span>
        <span v-if="s.show_tags">
          <span :style="labelStyle">{{ t('Tag:') }}</span>
          <span :style="linkStyle">{{ t('Nuovo') }}</span>,
          <span :style="linkStyle">{{ t('Cotone') }}</span>
        </span>
      </template>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  show_sku: true,
  show_categories: true,
  show_tags: true,
  layout: 'stacked',
  separator: '|',
  text_color: '#6B7280',
  label_color: 'var(--olo-color-text, #374151)',
  link_color: 'var(--olo-color-primary, #6366F1)',
  font_size: '14',
  label_weight: '600',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const wooActive = computed(() => true);

const wrapStyle = computed(() => {
  const st = {
    fontSize: parseInt(s.value.font_size) + 'px',
    color: s.value.text_color || '#6B7280',
  };
  if (s.value.layout === 'stacked') {
    st.display = 'flex';
    st.flexDirection = 'column';
    st.gap = '8px';
  } else {
    st.display = 'flex';
    st.flexWrap = 'wrap';
    st.alignItems = 'center';
    st.gap = '6px';
  }
  return st;
});

const itemStyle = computed(() => ({
  display: 'block',
}));

const labelStyle = computed(() => ({
  color: s.value.label_color || 'var(--olo-color-text, #374151)',
  fontWeight: s.value.label_weight || '600',
  marginRight: '6px',
}));

const linkStyle = computed(() => ({
  color: s.value.link_color || 'var(--olo-color-primary, #6366F1)',
}));

const sepStyle = computed(() => ({
  color: s.value.text_color || '#6B7280',
  opacity: '0.4',
  margin: '0 4px',
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
