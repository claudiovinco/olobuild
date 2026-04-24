<template>
  <div>
    <div v-if="!wooActive" class="olo-woo-notice">
      <span class="olo-woo-notice-icon">{{ t('&#x1F6D2;') }}</span>
      <span>{{ t('WooCommerce richiesto') }}</span>
    </div>
    <div v-else :style="wrapStyle">
      <div v-if="s.show_success" :style="successStyle">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <span>{{ t('Prodotto aggiunto al carrello con successo.') }}</span>
      </div>
      <div v-if="s.show_error" :style="errorStyle">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        <span>{{ t('Si e verificato un errore durante l\'elaborazione.') }}</span>
      </div>
      <div v-if="s.show_info" :style="infoStyle">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
        <span>{{ t('Il codice coupon e stato applicato.') }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  show_success: true,
  show_error: true,
  show_info: true,
  border_radius: '8',
  font_size: '14',
};
const s = computed(() => ({ ...defaults, ...props.settings }));
const wooActive = computed(() => true);

const radius = computed(() => ((v => isNaN(v) ? 8 : v)(parseInt(s.value.border_radius))) + 'px');
const fontSize = computed(() => (parseInt(s.value.font_size) || 14) + 'px');

const wrapStyle = computed(() => ({
  display: 'flex',
  flexDirection: 'column',
  gap: '12px',
}));

const baseNotice = computed(() => ({
  display: 'flex',
  alignItems: 'center',
  gap: '10px',
  padding: '12px 18px',
  borderRadius: radius.value,
  fontSize: fontSize.value,
  fontWeight: 500,
}));

const successStyle = computed(() => ({
  ...baseNotice.value,
  background: '#F0FDF4',
  border: '1px solid #86EFAC',
  color: '#166534',
}));

const errorStyle = computed(() => ({
  ...baseNotice.value,
  background: '#FEF2F2',
  border: '1px solid #FCA5A5',
  color: '#991B1B',
}));

const infoStyle = computed(() => ({
  ...baseNotice.value,
  background: '#EFF6FF',
  border: '1px solid #93C5FD',
  color: '#1E40AF',
}));
</script>

<style scoped>
.olo-woo-notice { display:flex;align-items:center;gap:8px;padding:16px 20px;background:#FEF3C7;border:1px solid #F59E0B;border-radius:8px;color:#92400E;font-size:14px;font-weight:500 }
.olo-woo-notice-icon { font-size:20px }
</style>
