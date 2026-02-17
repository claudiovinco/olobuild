<template>
  <div
    class="mb-text-center mb-py-8 mb-px-4"
    :style="{ color: settings.text_color || '#F3F4F6', minHeight: '80px' }"
  >
    <div v-if="settings.icon_emoji" class="mb-text-4xl mb-mb-2">{{ settings.icon_emoji }}</div>
    <div class="mb-text-5xl mb-font-extrabold mb-leading-tight">
      {{ settings.prefix || '' }}{{ settings.number || '0' }}{{ settings.suffix || '' }}
    </div>
    <div v-if="settings.label" class="mb-text-base mb-opacity-70 mb-mt-2" v-html="settings.label"></div>
  </div>
</template>

<script setup>
defineProps({
  settings: { type: Object, default: () => ({}) },
});
</script>
