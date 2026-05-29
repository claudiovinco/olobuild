<template>
  <div style="display:flex;align-items:center;gap:12px;padding:8px 0">
    <div style="display:flex;align-items:center;gap:6px">
      <span :style="{ width:'10px', height:'10px', borderRadius:'50%', background: TOKENS.success.fg, display:'inline-block' }"></span>
      <span :style="{ fontSize:'14px', fontWeight:'600', color: TOKENS.success.fg }">{{ t('Aperto ora') }}</span>
    </div>
    <span :style="{ color: TOKENS.border }">|</span>
    <span :style="{ fontSize:'13px', color: TOKENS.textSoft }">{{ t('Prossimo slot: 14:00 - 15:00') }}</span>
    <span :style="{ color: TOKENS.border }">|</span>
    <span :style="{ fontSize:'13px', color: TOKENS.textSoft }">{{ t('3 slot disponibili oggi') }}</span>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { TOKENS } from '@/composables/oloTileDefaults';
defineProps({ settings: { type: Object, default: () => ({}) } });
</script>
