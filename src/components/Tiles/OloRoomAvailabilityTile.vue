<template>
  <div>
    <h3 style="font-size:16px;font-weight:700;color:var(--olo-color-text, #374151);margin:0 0 12px">{{ t('Disponibilità — Febbraio 2026') }}</h3>
    <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;text-align:center">
      <span v-for="d in [t('Lun'),t('Mar'),t('Mer'),t('Gio'),t('Ven'),t('Sab'),t('Dom')]" :key="d" style="font-size:11px;font-weight:600;color:#9ca3af;padding:4px 0">{{ d }}</span>
      <span v-for="i in 5" :key="'e'+i" style="padding:6px"></span>
      <span v-for="day in calDays" :key="day.n" :style="dayStyle(day)">{{ day.n }}</span>
    </div>
    <div style="display:flex;gap:16px;margin-top:10px;font-size:11px;color:#6b7280">
      <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#22c55e;margin-right:4px"></span>{{ t('Libero') }}</span>
      <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#f59e0b;margin-right:4px"></span>{{ t('Parziale') }}</span>
      <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#ef4444;margin-right:4px"></span>{{ t('Occupato') }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
defineProps({ settings: { type: Object, default: () => ({}) } });
const calDays = [];
for (let i = 1; i <= 28; i++) {
  const status = i % 7 === 0 ? 'closed' : (i % 5 === 0 ? 'busy' : (i % 3 === 0 ? 'partial' : 'free'));
  calDays.push({ n: i, status });
}
function dayStyle(day) {
  const colors = { free: '#dcfce7', partial: '#fef3c7', busy: '#fee2e2', closed: '#f3f4f6' };
  const textColors = { free: '#166534', partial: '#92400e', busy: '#991b1b', closed: '#9ca3af' };
  return { padding: '6px 2px', borderRadius: '6px', fontSize: '12px', fontWeight: '600', background: colors[day.status], color: textColors[day.status] };
}
</script>
