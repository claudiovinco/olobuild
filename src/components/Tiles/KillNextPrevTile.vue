<template>
  <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#fef3c7;border:1px dashed #d97706;border-radius:8px;font-size:13px;color:#92400e">
    <span class="dashicons dashicons-hidden" style="font-size:18px"></span>
    <span><strong>{{ t('Kill Next/Prev') }}</strong> — nasconde la navigazione WP</span>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});
</script>
