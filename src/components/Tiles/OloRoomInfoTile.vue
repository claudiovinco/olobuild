<template>
  <div :style="wrapStyle">
    <h3 :style="{ fontSize:'16px', fontWeight:'700', color: TOKENS.text, margin:'0 0 10px' }">{{ t('Informazioni sala') }}</h3>
    <div :style="gridStyle">
      <div v-for="item in items" :key="item.label" style="display:flex;flex-direction:column;gap:2px">
        <span :style="{ fontSize:'11px', color: TOKENS.textFaint, fontWeight:'600', textTransform:'uppercase' }">{{ item.label }}</span>
        <span :style="{ fontSize:'14px', color: TOKENS.text, fontWeight:'500' }">{{ item.value }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { TOKENS } from '@/composables/oloTileDefaults';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { style: 'card' };
const s = computed(() => ({ ...defaults, ...props.settings }));
// Dati di esempio per la preview builder: i valori reali sono dinamici (meta del
// CPT "room" di OLObooking), qui mostriamo un mock localizzato.
const items = [
  { label: t('Capienza'), value: t('50 persone') },
  { label: t('Superficie'), value: '85 mq' },
  { label: t('Piano'), value: t('Piano terra') },
  { label: t('Tipo'), value: t('Sala conferenze') },
  { label: t('Zona'), value: t('Centro storico') },
  { label: t('Tariffa'), value: '€ 25/h' },
];
const wrapStyle = computed(() => s.value.style === 'card' ? { background: TOKENS.surfaceAlt, border: '1px solid ' + TOKENS.border, borderRadius: '10px', padding: '16px' } : {});
const gridStyle = computed(() => ({ display: 'grid', gridTemplateColumns: 'repeat(3,1fr)', gap: '12px 20px' }));
</script>
