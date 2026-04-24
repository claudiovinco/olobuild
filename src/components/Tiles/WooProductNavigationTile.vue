<template>
  <div>
    <div v-if="!wooActive" class="olo-woo-notice">
      <span class="olo-woo-notice-icon">{{ t('&#x1F6D2;') }}</span>
      <span>{{ t('WooCommerce richiesto') }}</span>
    </div>
    <div v-else :style="wrapStyle">
      <!-- Previous -->
      <div :style="itemStyle('prev')">
        <span :style="arrowStyle">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        </span>
        <div v-if="s.show_thumbnail" :style="thumbStyle"></div>
        <div :style="textWrap('left')">
          <span v-if="s.show_label" :style="labelStyle" data-olo-editable="label_prev">{{ s.label_prev }}</span>
          <span :style="nameStyle">{{ t('Scarpe Running Pro') }}</span>
        </div>
      </div>

      <!-- Separator -->
      <div :style="sepStyle"></div>

      <!-- Next -->
      <div :style="itemStyle('next')">
        <div :style="textWrap('right')">
          <span v-if="s.show_label" :style="labelStyle" data-olo-editable="label_next">{{ s.label_next }}</span>
          <span :style="nameStyle">{{ t('Zaino Trekking Ultra') }}</span>
        </div>
        <div v-if="s.show_thumbnail" :style="thumbStyleNext"></div>
        <span :style="arrowStyle">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        </span>
      </div>
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
  show_thumbnail: true,
  show_label: true,
  label_prev: 'Prodotto precedente',
  label_next: 'Prodotto successivo',
  text_color: '#374151',
  hover_color: 'var(--olo-color-primary, #6366F1)',
  separator_style: 'line',
};
const s = computed(() => ({ ...defaults, ...props.settings }));
const wooActive = computed(() => true);

const wrapStyle = computed(() => ({
  display: 'flex',
  alignItems: 'stretch',
  justifyContent: 'space-between',
}));

const itemStyle = (dir) => ({
  display: 'flex',
  alignItems: 'center',
  gap: '12px',
  padding: '16px 0',
  color: s.value.text_color || '#374151',
  flex: 1,
  cursor: 'pointer',
  justifyContent: dir === 'next' ? 'flex-end' : 'flex-start',
  textAlign: dir === 'next' ? 'right' : 'left',
});

const thumbBase = {
  width: '56px',
  height: '56px',
  borderRadius: '8px',
  flexShrink: 0,
};

const thumbStyle = computed(() => ({
  ...thumbBase,
  background: 'linear-gradient(135deg, #E5E7EB 0%, #D1D5DB 100%)',
}));

const thumbStyleNext = computed(() => ({
  ...thumbBase,
  background: 'linear-gradient(135deg, #DBEAFE 0%, #BFDBFE 100%)',
}));

const textWrap = (align) => ({
  display: 'flex',
  flexDirection: 'column',
  gap: '2px',
  textAlign: align,
});

const labelStyle = computed(() => ({
  fontSize: '12px',
  textTransform: 'uppercase',
  letterSpacing: '0.05em',
  opacity: 0.6,
  color: s.value.text_color || '#374151',
}));

const nameStyle = computed(() => ({
  fontSize: '15px',
  fontWeight: 600,
  lineHeight: 1.3,
  color: s.value.text_color || '#374151',
}));

const arrowStyle = computed(() => ({
  flexShrink: 0,
  opacity: 0.4,
  color: s.value.text_color || '#374151',
}));

const sepStyle = computed(() => {
  const style = s.value.separator_style || 'line';
  const base = { alignSelf: 'stretch', margin: '0 20px' };
  if (style === 'line') {
    base.borderLeft = '1px solid #E5E7EB';
  } else if (style === 'dotted') {
    base.borderLeft = '1px dotted #D1D5DB';
  }
  return base;
});
</script>

<style scoped>
.olo-woo-notice { display:flex;align-items:center;gap:8px;padding:16px 20px;background:#FEF3C7;border:1px solid #F59E0B;border-radius:8px;color:#92400E;font-size:14px;font-weight:500 }
.olo-woo-notice-icon { font-size:20px }
</style>
