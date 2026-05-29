<template>
  <div>
    <h3 :style="{ fontSize:'16px', fontWeight:'700', color: TOKENS.text, margin:'0 0 12px' }">{{ t('Disponibilità — Febbraio 2026') }}</h3>
    <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;text-align:center">
      <span v-for="d in [t('Lun'),t('Mar'),t('Mer'),t('Gio'),t('Ven'),t('Sab'),t('Dom')]" :key="d" :style="{ fontSize:'11px', fontWeight:'600', color: TOKENS.textFaint, padding:'4px 0' }">{{ d }}</span>
      <span v-for="i in 5" :key="'e'+i" style="padding:6px"></span>
      <span v-for="day in calDays" :key="day.n" :style="dayStyle(day)">{{ day.n }}</span>
    </div>
    <div :style="{ display:'flex', gap:'16px', marginTop:'10px', fontSize:'11px', color: TOKENS.textSoft }">
      <span><span :style="legendDot(TOKENS.success.fg)"></span>{{ t('Libero') }}</span>
      <span><span :style="legendDot(TOKENS.warning.fg)"></span>{{ t('Parziale') }}</span>
      <span><span :style="legendDot(TOKENS.error.fg)"></span>{{ t('Occupato') }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { TOKENS } from '@/composables/oloTileDefaults';
defineProps({ settings: { type: Object, default: () => ({}) } });
const calDays = [];
for (let i = 1; i <= 28; i++) {
  const status = i % 7 === 0 ? 'closed' : (i % 5 === 0 ? 'busy' : (i % 3 === 0 ? 'partial' : 'free'));
  calDays.push({ n: i, status });
}
// Stati semantici: libero → success, parziale → warning, occupato → error, chiuso → neutro.
const dayBg = {
  free: TOKENS.success.bg, partial: TOKENS.warning.bg, busy: TOKENS.error.bg, closed: TOKENS.surfaceAlt,
};
const dayFg = {
  free: TOKENS.success.fg, partial: TOKENS.warning.fg, busy: TOKENS.error.fg, closed: TOKENS.textFaint,
};
function dayStyle(day) {
  return { padding: '6px 2px', borderRadius: '6px', fontSize: '12px', fontWeight: '600', background: dayBg[day.status], color: dayFg[day.status] };
}
function legendDot(color) {
  return { display: 'inline-block', width: '10px', height: '10px', borderRadius: '50%', background: color, marginRight: '4px' };
}
</script>
