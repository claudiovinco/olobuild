<template>
  <div>
    <div class="olo-woo-multistep-preview">
      <!-- Progress bar -->
      <div :style="progressBarStyle">
        <div v-for="(label, i) in steps" :key="i" :style="stepStyle(i)">
          <div :style="stepCircleStyle(i)">{{ i + 1 }}</div>
          <span :style="stepLabelStyle(i)">{{ label }}</span>
        </div>
      </div>
      <!-- Placeholder form -->
      <div :style="cardStyle">
        <h3 :style="{ margin: '0 0 16px', fontSize: '18px', fontWeight: 700, color: resolveColor(s.text_color, TOKENS.text) }">{{ steps[0] || 'Dati' }}</h3>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
          <input type="text" class="olo-woo-ms-input" :placeholder="t('Nome')" :style="inputStyle" readonly />
          <input type="text" class="olo-woo-ms-input" :placeholder="t('Cognome')" :style="inputStyle" readonly />
        </div>
        <input type="text" class="olo-woo-ms-input" :placeholder="t('Email')" :style="{ ...inputStyle, marginBottom: '12px' }" readonly />
        <input type="text" class="olo-woo-ms-input" :placeholder="t('Telefono')" :style="inputStyle" readonly />
      </div>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const s = computed(() => props.settings || {});
const steps = computed(() => (s.value.step_labels || 'Dati,Spedizione,Pagamento,Conferma').split(',').map(l => l.trim()));
const accent = computed(() => resolveColor(s.value.accent_color, TOKENS.primary));
const radius = computed(() => ((v => isNaN(v) ? 12 : v)(parseInt(s.value.card_radius))) + 'px');

const progressBarStyle = computed(() => ({
  display: 'flex',
  justifyContent: 'center',
  gap: '24px',
  marginBottom: '24px',
  padding: '16px',
  background: resolveColor(s.value.step_bg, TOKENS.surfaceAlt),
  borderRadius: radius.value,
}));
const stepStyle = (i) => ({ display: 'flex', alignItems: 'center', gap: '8px' });
const stepCircleStyle = (i) => ({
  width: '28px', height: '28px', borderRadius: '50%',
  display: 'flex', alignItems: 'center', justifyContent: 'center',
  fontSize: '13px', fontWeight: 700,
  background: i === 0 ? accent.value : 'transparent',
  color: i === 0 ? TOKENS.onPrimary : resolveColor(s.value.text_color, TOKENS.textFaint),
  border: i === 0 ? 'none' : '2px solid ' + TOKENS.border,
});
const stepLabelStyle = (i) => ({
  fontSize: '13px', fontWeight: i === 0 ? 600 : 400,
  color: i === 0 ? resolveColor(s.value.active_color, accent.value) : resolveColor(s.value.text_color, TOKENS.textFaint),
});
const cardStyle = computed(() => ({
  padding: '24px',
  border: '1px solid ' + TOKENS.border,
  borderRadius: radius.value,
}));
const inputStyle = {
  width: '100%', padding: '10px 14px', border: '1px solid ' + TOKENS.border,
  borderRadius: '6px', fontSize: '14px', boxSizing: 'border-box',
};
</script>

<style scoped>
/* a11y: input checkout con focus-visible sul primario brand */
.olo-woo-ms-input:focus-visible {
  outline: none;
  border-color: var(--olo-color-primary, #e1474f) !important;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 25%, transparent);
}
</style>
