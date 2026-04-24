<template>
  <div>
    <h3 style="font-size:18px;font-weight:700;color:var(--olo-color-text, #374151);margin:0 0 12px">{{ t('Sale nella stessa zona') }}</h3>
    <div :style="gridStyle">
      <div v-for="room in rooms" :key="room.name" style="border:1px solid #e5e7eb;border-radius:10px;overflow:hidden">
        <div :style="{height:'100px',background:room.bg}"></div>
        <div style="padding:10px">
          <div style="font-size:14px;font-weight:600;color:var(--olo-color-text, #374151)">{{ room.name }}</div>
          <div style="font-size:12px;color:#6b7280;margin-top:2px">{{ room.info }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { count: 3, source: 'zone' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const rooms = [
  { name: 'Sala Tartarotti', info: '40 posti · Palazzo Annona', bg: 'linear-gradient(135deg,#6366f1,#4f46e5)' },
  { name: 'Auditorium Melotti', info: '200 posti · Via Laurenti', bg: 'linear-gradient(135deg,#0891b2,#0e7490)' },
  { name: 'Sala Rosmini', info: '30 posti · Biblioteca Civica', bg: 'linear-gradient(135deg,#059669,#047857)' },
];
const gridStyle = computed(() => ({ display: 'grid', gridTemplateColumns: `repeat(${parseInt(s.value.count)||3}, 1fr)`, gap: '16px' }));
</script>
