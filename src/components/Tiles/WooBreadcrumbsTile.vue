<template>
  <div>
    <div v-if="!wooActive" class="olo-woo-notice">
      <span class="olo-woo-notice-icon">{{ t('&#x1F6D2;') }}</span>
      <span>{{ t('WooCommerce richiesto') }}</span>
    </div>
    <div v-else :style="wrapStyle">
      <span :style="linkStyle">{{ t('Home') }}</span>
      <span :style="sepStyle">{{ sepChar }}</span>
      <span :style="linkStyle">{{ t('Shop') }}</span>
      <span :style="sepStyle">{{ sepChar }}</span>
      <span :style="linkStyle">{{ t('Categoria') }}</span>
      <span :style="sepStyle">{{ sepChar }}</span>
      <span :style="textStyle">{{ t('Prodotto esempio') }}</span>
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
  separator: '/',
  text_color: '#6B7280',
  link_color: '#6366F1',
  font_size: '14',
  alignment: 'left',
};
const s = computed(() => ({ ...defaults, ...props.settings }));
const wooActive = computed(() => true);

const sepMap = {
  '/': '/',
  '>': '>',
  '-': '-',
  '>>': '\u00BB',
};
const sepChar = computed(() => sepMap[s.value.separator] || '/');

const wrapStyle = computed(() => ({
  display: 'flex',
  alignItems: 'center',
  gap: '6px',
  flexWrap: 'wrap',
  fontSize: (parseInt(s.value.font_size) || 14) + 'px',
  padding: '8px 0',
  justifyContent: s.value.alignment === 'center' ? 'center' : s.value.alignment === 'right' ? 'flex-end' : 'flex-start',
}));

const textStyle = computed(() => ({
  color: s.value.text_color || '#6B7280',
}));

const linkStyle = computed(() => ({
  color: s.value.link_color || '#6366F1',
  textDecoration: 'none',
  cursor: 'pointer',
}));

const sepStyle = computed(() => ({
  color: s.value.text_color || '#6B7280',
  opacity: 0.5,
}));
</script>

<style scoped>
.olo-woo-notice { display:flex;align-items:center;gap:8px;padding:16px 20px;background:#FEF3C7;border:1px solid #F59E0B;border-radius:8px;color:#92400E;font-size:14px;font-weight:500 }
.olo-woo-notice-icon { font-size:20px }
</style>
