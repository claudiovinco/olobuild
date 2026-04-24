<template>
  <div>
    <h3 style="font-size:16px;font-weight:700;color:var(--olo-color-text, #374151);margin:0 0 10px">{{ t('Orari di apertura') }}</h3>
    <div style="display:flex;flex-direction:column;gap:4px">
      <div v-for="day in days" :key="day.name" style="display:flex;justify-content:space-between;font-size:13px;padding:4px 0;border-bottom:1px solid #f3f4f6">
        <span :style="{color:day.closed?'#9ca3af':'#374151',fontWeight:'600'}">{{ day.name }}</span>
        <span :style="{color:day.closed?'#ef4444':'#6b7280'}">{{ day.hours }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
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
