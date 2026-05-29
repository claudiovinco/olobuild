<template>
  <div>
    <h3 :style="{ fontSize:'16px', fontWeight:'700', color: TOKENS.text, margin:'0 0 10px' }">{{ t('Regole di prenotazione') }}</h3>
    <div :style="{ display:'flex', flexDirection:'column', gap:'6px', fontSize:'13px', color: TOKENS.text }">
      <div v-for="rule in rules" :key="rule.label" style="display:flex;align-items:center;gap:6px">
        <span class="olo-rule-ic" :style="{ width:'15px', height:'15px', color: rule.color, flexShrink:0, display:'inline-flex' }" v-html="iconSvg(rule.icon)"></span>
        <span>{{ rule.label }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import iconsSvg from '../ProSlider/uikitIconsSvg.js';
import { TOKENS } from '@/composables/oloTileDefaults';
defineProps({ settings: { type: Object, default: () => ({}) } });
const iconSvg = (name) => iconsSvg[name] || '';
const rules = [
  { label: t('Anticipo minimo: 24 ore'), icon: 'warning', color: TOKENS.warning.fg },
  { label: t('Anticipo massimo: 30 giorni'), icon: 'warning', color: TOKENS.warning.fg },
  { label: t('Max prenotazioni attive: 3'), icon: 'warning', color: TOKENS.warning.fg },
  { label: t('Durata slot: 60 minuti'), icon: 'clock', color: TOKENS.textSoft },
];
</script>

<style scoped>
.olo-rule-ic :deep(svg) { width: 100%; height: 100%; fill: currentColor; stroke: currentColor; }
</style>
