<template>
  <div :style="wrapStyle">
    <div style="font-size:22px;font-weight:700;color:#fff;margin-bottom:6px">{{ t('Prenota la tua sala') }}</div>
    <div style="font-size:14px;color:rgba(255,255,255,.85);margin-bottom:16px">{{ t('Scegli tra le nostre strutture e prenota in pochi click') }}</div>
    <div :style="{ display:'inline-block', background:'#fff', color: TOKENS.primary, padding:'10px 24px', borderRadius:'8px', fontSize:'14px', fontWeight:'600' }">{{ t('Scopri le sale') }}</div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { TOKENS } from '@/composables/oloTileDefaults';
defineProps({ settings: { type: Object, default: () => ({}) } });
// Gradiente brand: navy (secondary) → rosso (primary). Era #1e3a5f→#e1474f off-brand.
const wrapStyle = {
  background: `linear-gradient(135deg, ${TOKENS.secondary} 0%, ${TOKENS.primary} 100%)`,
  borderRadius: '12px', padding: '30px', textAlign: 'center',
};
</script>
