<template>
  <div>
    <div v-if="!wooActive" class="olo-woo-notice">
      <span class="olo-woo-notice-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor"><circle cx="7.3" cy="17.3" r="1.4" fill="currentColor" stroke="none"/><circle cx="13.3" cy="17.3" r="1.4" fill="currentColor" stroke="none"/><polyline points="0 2 3.2 4 5.3 12.5 16 12.5 18 6.5 8 6.5"/></svg></span>
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
  text_color: '',
  hover_color: 'var(--olo-color-primary, #e1474f)',
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
  color: s.value.text_color || 'var(--olo-color-text, #1f2937)',
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

const thumbBg = 'linear-gradient(135deg, var(--olo-color-surface-alt, #f6f7f9) 0%, var(--olo-color-border, #e5e7eb) 100%)';

const thumbStyle = computed(() => ({
  ...thumbBase,
  background: thumbBg,
}));

const thumbStyleNext = computed(() => ({
  ...thumbBase,
  background: thumbBg,
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
  color: s.value.text_color || 'var(--olo-color-text, #1f2937)',
}));

const nameStyle = computed(() => ({
  fontSize: '15px',
  fontWeight: 600,
  lineHeight: 1.3,
  color: s.value.text_color || 'var(--olo-color-text, #1f2937)',
}));

const arrowStyle = computed(() => ({
  flexShrink: 0,
  opacity: 0.4,
  color: s.value.text_color || 'var(--olo-color-text, #1f2937)',
}));

const sepStyle = computed(() => {
  const style = s.value.separator_style || 'line';
  const base = { alignSelf: 'stretch', margin: '0 20px' };
  if (style === 'line') {
    base.borderLeft = '1px solid var(--olo-color-border, #e5e7eb)';
  } else if (style === 'dotted') {
    base.borderLeft = '1px dotted var(--olo-color-text-faint, #94a3b8)';
  }
  return base;
});
</script>

<style scoped>
.olo-woo-notice { display:flex;align-items:center;gap:8px;padding:16px 20px;background:color-mix(in srgb, var(--olo-color-warning, #b45309) 12%, #fff);border:1px solid var(--olo-color-warning, #b45309);border-radius:8px;color:var(--olo-color-warning, #b45309);font-size:14px;font-weight:500 }
.olo-woo-notice-icon { width:20px;height:20px;display:inline-flex;flex-shrink:0 } .olo-woo-notice-icon :deep(svg) { width:100%;height:100% }
</style>
