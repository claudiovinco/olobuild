<template>
  <div style="padding: 16px; text-align: center;">
    <span
      class="mb-inline-flex mb-items-center mb-gap-2 mb-rounded mb-font-medium mb-text-sm mb-cursor-default"
      :class="btnClass"
      style="padding: 8px 20px;"
    >
      <span v-if="settings.button_icon" class="dashicons" :class="'dashicons-' + settings.button_icon" style="font-size:16px;width:16px;height:16px;"></span>
      {{ settings.button_text || 'Apri' }}
    </span>
    <div class="mb-mt-2 mb-text-xs mb-text-gray-500">
      Click → Modal {{ settings.mode === 'template' ? '(template)' : '(contenuto)' }}
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const btnClass = computed(() => {
  const s = props.settings.button_style || 'default';
  const map = {
    default: 'mb-bg-gray-600 mb-text-white',
    primary: 'mb-bg-blue-600 mb-text-white',
    secondary: 'mb-bg-gray-500 mb-text-white',
    danger: 'mb-bg-red-600 mb-text-white',
    text: 'mb-bg-transparent mb-text-gray-300 mb-underline',
    link: 'mb-bg-transparent mb-text-blue-400 mb-underline',
  };
  return map[s] || map.default;
});
</script>
