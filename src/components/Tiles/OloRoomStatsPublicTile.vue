<template>
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;text-align:center">
    <div v-for="stat in stats" :key="stat.label" :style="{ padding:'16px', background: TOKENS.surfaceAlt, borderRadius:'10px', border:'1px solid ' + TOKENS.border }">
      <div :style="{ fontSize:'28px', fontWeight:'700', color: TOKENS.primary }">{{ stat.value }}</div>
      <div :style="{ fontSize:'13px', color: TOKENS.textSoft, marginTop:'4px' }">{{ stat.label }}</div>
    </div>
  </div>
</template>

<script setup>
import { TOKENS } from '@/composables/oloTileDefaults';
defineProps({ settings: { type: Object, default: () => ({}) } });
const stats = [
  { value: '12', label: 'Sale disponibili' },
  { value: '3', label: 'Zone coperte' },
  { value: '1.250', label: 'Posti totali' },
  { value: '847', label: 'Prenotazioni' },
];
</script>
