<template>
  <div :style="wrapStyle">
    <h3 :style="{ fontSize:'16px', fontWeight:'700', color: TOKENS.text, margin:'0 0 12px' }">{{ t('Tariffa') }}</h3>
    <div style="text-align:center;padding:10px 0">
      <div :style="{ fontSize:'32px', fontWeight:'700', color: TOKENS.primary }">{{ t('&euro; 25,00') }}</div>
      <div :style="{ fontSize:'14px', color: TOKENS.textSoft, marginTop:'2px' }">{{ t('/ ora') }}</div>
    </div>
    <div :style="{ fontSize:'12px', color: TOKENS.textFaint, marginTop:'8px', paddingTop:'8px', borderTop:'1px solid ' + TOKENS.border }">
      {{ t('Tariffa ridotta per enti pubblici. IVA inclusa.') }}
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
const wrapStyle = computed(() => s.value.style === 'card' ? { background: TOKENS.surfaceAlt, border: '1px solid ' + TOKENS.border, borderRadius: '10px', padding: '16px' } : {});
</script>
