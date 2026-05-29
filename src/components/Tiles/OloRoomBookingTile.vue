<template>
  <div :style="cardStyle">
    <div :style="{ fontSize:'13px', color: TOKENS.textSoft, marginBottom:'4px' }">{{ t('A partire da') }}</div>
    <div :style="{ fontSize:'28px', fontWeight:'700', color: TOKENS.primary }">&euro; 25,00<span :style="{ fontSize:'14px', fontWeight:'400', color: TOKENS.textSoft }"> {{ t('/ ora') }}</span></div>
    <div :style="{ fontSize:'13px', color: TOKENS.textSoft, margin:'6px 0 14px' }">{{ t('Orari: 08:00 - 18:00') }}</div>
    <button type="button" class="olo-book-btn" :style="{ background: TOKENS.primary, color: TOKENS.onPrimary, padding:'10px 24px', borderRadius:'8px', fontSize:'14px', fontWeight:'600', border:'none', cursor:'pointer' }">{{ t('Prenota questa sala') }}</button>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { TOKENS } from '@/composables/oloTileDefaults';
defineProps({ settings: { type: Object, default: () => ({}) } });
// Card neutra (surface-alt + bordo) con accenti brand. Era azzurro #f0f9ff/#bae6fd off-brand.
const cardStyle = {
  background: TOKENS.surfaceAlt, border: '1px solid ' + TOKENS.border,
  borderRadius: '12px', padding: '20px', textAlign: 'center',
};
</script>

<style scoped>
.olo-book-btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
</style>
