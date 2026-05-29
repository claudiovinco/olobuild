<template>
  <div>
    <h3 :style="{ fontSize:'16px', fontWeight:'700', color: TOKENS.text, margin:'0 0 10px' }">{{ t('Orari di apertura') }}</h3>
    <div style="display:flex;flex-direction:column;gap:4px">
      <div v-for="day in days" :key="day.name" :style="{ display:'flex', justifyContent:'space-between', fontSize:'13px', padding:'4px 0', borderBottom:'1px solid ' + TOKENS.border }">
        <span :style="{ color: day.closed ? TOKENS.textFaint : TOKENS.text, fontWeight:'600' }">{{ day.name }}</span>
        <span :style="{ color: day.closed ? TOKENS.error.fg : TOKENS.textSoft }">{{ day.hours }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { TOKENS } from '@/composables/oloTileDefaults';
defineProps({ settings: { type: Object, default: () => ({}) } });
const days = [
  { name: 'Lunedì', hours: '08:00 - 18:00', closed: false },
  { name: 'Martedì', hours: '08:00 - 18:00', closed: false },
  { name: 'Mercoledì', hours: '08:00 - 18:00', closed: false },
  { name: 'Giovedì', hours: '08:00 - 18:00', closed: false },
  { name: 'Venerdì', hours: '08:00 - 18:00', closed: false },
  { name: 'Sabato', hours: '09:00 - 13:00', closed: false },
  { name: 'Domenica', hours: 'Chiuso', closed: true },
];
</script>
