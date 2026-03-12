<template>
  <div class="olo-paymentbuttons" :style="{ textAlign: s.alignment || 'center' }">
    <!-- Stripe button -->
    <button
      v-if="s.provider === 'stripe' || s.provider === 'both'"
      class="olo-pay-btn olo-pay-btn--stripe"
      :style="btnStyle"
    >
      <svg v-if="s.icon_position !== 'none'" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
      <span data-olo-editable="button_text">{{ s.button_text || 'Paga con Stripe' }}</span>
    </button>
    <!-- PayPal button -->
    <button
      v-if="s.provider === 'paypal' || s.provider === 'both'"
      class="olo-pay-btn olo-pay-btn--paypal"
      :style="paypalStyle"
    >
      <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.94 3.72a.766.766 0 0 1 .757-.66h7.063c2.344 0 4.07.584 5.067 1.717.442.502.742 1.07.905 1.697.176.672.195 1.47.053 2.438l-.014.09v.776l.555.318a3.8 3.8 0 0 1 1.133.99c.442.571.72 1.27.824 2.082.107.833.035 1.825-.213 2.945-.287 1.296-.755 2.42-1.39 3.343-.585.849-1.314 1.547-2.167 2.078-.815.507-1.771.87-2.84 1.079-.738.144-1.79.192-2.785.192H11.62a1.24 1.24 0 0 0-1.223 1.046l-.059.315-.974 6.17-.046.22a.164.164 0 0 1-.162.138z"/></svg>
      <span data-olo-editable="button_text">{{ s.provider === 'both' ? 'PayPal' : (s.button_text || 'Paga con PayPal') }}</span>
    </button>
    <!-- Amount display -->
    <div v-if="s.amount" class="olo-pay-amount" style="margin-top: 8px; font-size: 13px; opacity: 0.7;">
      {{ currencySymbol }}{{ s.amount }} {{ s.currency }}
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => props.settings || {});
const currencySymbol = computed(() => ({ EUR: '€', USD: '$', GBP: '£' }[s.value.currency] || ''));
const btnStyle = computed(() => ({
  backgroundColor: s.value.bg_color || 'var(--olo-color-primary, #6366F1)',
  color: s.value.text_color || '#ffffff',
  borderRadius: (s.value.border_radius || 8) + 'px',
  fontSize: (s.value.font_size || 16) + 'px',
  width: s.value.full_width ? '100%' : 'auto',
  padding: '12px 32px',
  border: 'none',
  cursor: 'pointer',
  display: 'inline-flex',
  alignItems: 'center',
  gap: '8px',
  fontWeight: '600',
}));
const paypalStyle = computed(() => ({
  ...btnStyle.value,
  backgroundColor: '#0070ba',
  marginLeft: s.value.provider === 'both' ? '12px' : '0',
}));
</script>
