<template>
  <div>
    <h3 :style="{ fontSize:'18px', fontWeight:'700', color: TOKENS.text, margin:'0 0 12px' }">{{ t('Disponibilità e Prenotazione') }}</h3>
    <div :style="{ border:'1px solid ' + TOKENS.border, borderRadius:'10px', overflow:'hidden' }">
      <div :style="{ display:'flex', justifyContent:'space-between', alignItems:'center', padding:'10px 14px', background: TOKENS.surfaceAlt, borderBottom:'1px solid ' + TOKENS.border }">
        <span :style="{ fontSize:'11px', color: TOKENS.textSoft, cursor:'pointer' }">{{ t('&lsaquo; Prec') }}</span>
        <span :style="{ fontSize:'14px', fontWeight:'600', color: TOKENS.text }">{{ t('Febbraio 2026') }}</span>
        <span :style="{ fontSize:'11px', color: TOKENS.textSoft, cursor:'pointer' }">{{ t('Succ &rsaquo;') }}</span>
      </div>
      <div style="display:grid;grid-template-columns:repeat(7,1fr);text-align:center">
        <span v-for="d in ['Lun','Mar','Mer','Gio','Ven','Sab','Dom']" :key="d" :style="{ fontSize:'10px', color: TOKENS.textFaint, padding:'6px 0', borderBottom:'1px solid ' + TOKENS.border }">{{ d }}</span>
        <span v-for="i in 5" :key="'e'+i" style="padding:8px;font-size:11px"></span>
        <span v-for="n in 28" :key="n" :style="cellStyle(n)">{{ n }}</span>
      </div>
    </div>
    <div :style="{ marginTop:'8px', fontSize:'11px', color: TOKENS.textFaint, textAlign:'center' }">{{ t('Clicca su un giorno per prenotare uno slot') }}</div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { TOKENS } from '@/composables/oloTileDefaults';
defineProps({ settings: { type: Object, default: () => ({}) } });
// Selezione giorno = accento brand (tinta soft del primario).
const selBg = 'color-mix(in srgb, var(--olo-color-primary, #e1474f) 14%, #fff)';
function cellStyle(n) {
  const base = { padding: '8px 2px', fontSize: '11px', fontWeight: '500', cursor: 'pointer', borderBottom: '1px solid ' + TOKENS.border };
  if (n % 7 === 0) return { ...base, color: TOKENS.textFaint, background: TOKENS.surfaceAlt };
  if (n === 15 || n === 16) return { ...base, background: selBg, color: TOKENS.primary, fontWeight: '700', borderRadius: '4px' };
  return { ...base, color: TOKENS.text };
}
</script>
