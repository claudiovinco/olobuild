<template>
  <div
    class="mb-relative mb-rounded mb-min-h-[40px] mb-p-3"
    style="border: 1px dashed #4B5563; background: transparent;"
  >
    <span
      class="mb-absolute mb-top-0 mb-left-2 mb-text-[10px] mb-text-gray-600 mb-bg-gray-900 mb-px-1"
      style="transform: translateY(-50%);"
    >{{ t('FRAGMENT') }}</span>
    <div class="mb-text-center mb-text-xs mb-text-gray-600 mb-py-2">
      {{ t('Invisible container') }}
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
defineProps({
  settings: { type: Object, default: () => ({}) },
});
</script>
