<template>
  <div>
    <div style="display:flex;gap:12px;margin-bottom:16px">
      <span :style="{ background: TOKENS.primary, color: TOKENS.onPrimary, padding:'6px 16px', borderRadius:'20px', fontSize:'13px', fontWeight:'600' }">{{ t('Le mie prenotazioni') }}</span>
      <span :style="{ background: TOKENS.surfaceAlt, color: TOKENS.textSoft, padding:'6px 16px', borderRadius:'20px', fontSize:'13px' }">{{ t('Preferiti') }}</span>
    </div>
    <div :style="{ border:'1px solid ' + TOKENS.border, borderRadius:'10px', overflow:'hidden' }">
      <div v-for="(b,i) in bookings" :key="i" :style="{ display:'flex', justifyContent:'space-between', alignItems:'center', padding:'10px 14px', borderBottom: i<2 ? '1px solid ' + TOKENS.border : 'none' }">
        <div>
          <div :style="{ fontSize:'13px', fontWeight:'600', color: TOKENS.text }">{{ b.room }}</div>
          <div :style="{ fontSize:'11px', color: TOKENS.textSoft }">{{ b.date }}</div>
        </div>
        <span :style="{ fontSize:'11px', fontWeight:'600', padding:'3px 10px', borderRadius:'12px', background: b.bg, color: b.color }">{{ b.status }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { TOKENS } from '@/composables/oloTileDefaults';
defineProps({ settings: { type: Object, default: () => ({}) } });
// Stati prenotazione semantici: Approvata → success, In attesa → warning.
const bookings = [
  { room: 'Sala Giunta', date: '25 Feb 2026, 09:00-11:00', status: 'Approvata', bg: TOKENS.success.bg, color: TOKENS.success.fg },
  { room: 'Auditorium Melotti', date: '28 Feb 2026, 14:00-17:00', status: 'In attesa', bg: TOKENS.warning.bg, color: TOKENS.warning.fg },
  { room: 'Sala Tartarotti', date: '3 Mar 2026, 10:00-12:00', status: 'Approvata', bg: TOKENS.success.bg, color: TOKENS.success.fg },
];
</script>
