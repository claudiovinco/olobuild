<template>
  <div>
    <h3 :style="{ fontSize:'18px', fontWeight:'700', color: TOKENS.text, margin:'0 0 8px' }">{{ t('Descrizione') }}</h3>
    <div :style="{ fontSize:'14px', color: TOKENS.textSoft, lineHeight:'1.7' }">
      {{ t('Sala conferenze moderna dotata di tutte le attrezzature necessarie per riunioni, presentazioni e workshop. Ampia illuminazione naturale, sistema audio integrato e connettività Wi-Fi ad alta velocità. Disponibile per enti pubblici e privati con prenotazione anticipata.') }}
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { TOKENS } from '@/composables/oloTileDefaults';
defineProps({ settings: { type: Object, default: () => ({}) } });
</script>
