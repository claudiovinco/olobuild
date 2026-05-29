<template>
  <div style="background:#f0f9ff;border:2px solid #bae6fd;border-radius:12px;padding:20px;text-align:center">
    <div style="font-size:13px;color:#0369a1;margin-bottom:4px">{{ t('A partire da') }}</div>
    <div style="font-size:28px;font-weight:700;color:#0c4a6e">&euro; 25,00<span style="font-size:14px;font-weight:400;color:#6b7280"> {{ t('/ ora') }}</span></div>
    <div style="font-size:13px;color:#6b7280;margin:6px 0 14px">{{ t('Orari: 08:00 - 18:00') }}</div>
    <div style="background:#1e87f0;color:#fff;padding:10px 24px;border-radius:8px;font-size:14px;font-weight:600;display:inline-block">{{ t('Prenota questa sala') }}</div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
defineProps({ settings: { type: Object, default: () => ({}) } });
</script>
