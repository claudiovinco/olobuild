<template>
  <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:16px">
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
      <div style="flex:1;min-width:200px;background:#fff;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;color:#9ca3af">{{ t('Cerca una sala...') }}</div>
      <div style="background:#fff;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;color:#6b7280;min-width:120px">{{ t('Tipo sala &#9662;') }}</div>
      <div style="background:#fff;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;color:#6b7280;min-width:120px">{{ t('Zona &#9662;') }}</div>
      <div style="background:#fff;border:1px solid #d1d5db;border-radius:8px;padding:8px 12px;font-size:13px;color:#6b7280;min-width:100px">{{ t('Capienza &#9662;') }}</div>
      <div style="background:#1e87f0;color:#fff;padding:8px 20px;border-radius:8px;font-size:13px;font-weight:600">{{ t('Cerca') }}</div>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
defineProps({ settings: { type: Object, default: () => ({}) } });
</script>
