<template>
  <div>
    <h3 :style="{ fontSize:'16px', fontWeight:'700', color: TOKENS.text, margin:'0 0 8px' }">{{ t('Posizione') }}</h3>
    <div :style="{ fontSize:'14px', color: TOKENS.text, lineHeight:'1.6' }">
      <div>{{ t('Via Tartarotti 7') }}</div>
      <div>{{ t('38068 Rovereto (TN)') }}</div>
    </div>
    <div style="margin-top:8px">
      <span :style="{ fontSize:'13px', color: TOKENS.primary, fontWeight:'500', cursor:'pointer' }">{{ t('Indicazioni stradali &rarr;') }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { TOKENS } from '@/composables/oloTileDefaults';
defineProps({ settings: { type: Object, default: () => ({}) } });
</script>
