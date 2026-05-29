<template>
  <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:color-mix(in srgb, var(--olo-color-warning, #b45309) 14%, #fff);border:1px dashed var(--olo-color-warning, #b45309);border-radius:8px;font-size:13px;color:var(--olo-color-warning, #b45309)">
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
