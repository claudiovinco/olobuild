<template>
  <div>
    <div v-if="!wooActive" class="olo-woo-notice">
      <span class="olo-woo-notice-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor"><circle cx="7.3" cy="17.3" r="1.4" fill="currentColor" stroke="none"/><circle cx="13.3" cy="17.3" r="1.4" fill="currentColor" stroke="none"/><polyline points="0 2 3.2 4 5.3 12.5 16 12.5 18 6.5 8 6.5"/></svg></span>
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
import { SYSTEM } from '@/composables/oloTileDefaults';

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

// Notice Woo = stati semantici token-first (fg dal ruolo globale, bg tinta soft).
const successStyle = computed(() => ({
  ...baseNotice.value,
  background: SYSTEM.success.bg,
  border: '1px solid ' + SYSTEM.success.fg,
  color: SYSTEM.success.fg,
}));

const errorStyle = computed(() => ({
  ...baseNotice.value,
  background: SYSTEM.error.bg,
  border: '1px solid ' + SYSTEM.error.fg,
  color: SYSTEM.error.fg,
}));

const infoStyle = computed(() => ({
  ...baseNotice.value,
  background: SYSTEM.info.bg,
  border: '1px solid ' + SYSTEM.info.fg,
  color: SYSTEM.info.fg,
}));
</script>

<style scoped>
.olo-woo-notice { display:flex;align-items:center;gap:8px;padding:16px 20px;background:color-mix(in srgb, var(--olo-color-warning, #b45309) 12%, #fff);border:1px solid var(--olo-color-warning, #b45309);border-radius:8px;color:var(--olo-color-warning, #b45309);font-size:14px;font-weight:500 }
.olo-woo-notice-icon { width:20px;height:20px;display:inline-flex;flex-shrink:0 } .olo-woo-notice-icon :deep(svg) { width:100%;height:100% }
</style>
