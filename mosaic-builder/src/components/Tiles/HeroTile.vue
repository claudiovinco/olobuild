<template>
  <div
    class="mb-text-center mb-py-16 mb-px-8 mb-rounded-lg"
    :style="{
      backgroundColor: settings.background_color || '#6366F1',
      color: settings.text_color || '#FFFFFF',
    }"
  >
    <h1 class="mb-text-3xl mb-font-bold mb-mb-3" v-html="settings.title || 'Hero Title'"></h1>
    <div class="mb-text-lg mb-opacity-90 mb-mb-6" v-html="settings.subtitle || 'Subtitle goes here'"></div>
    <span
      v-if="settings.cta_text"
      class="mb-inline-block mb-px-6 mb-py-2.5 mb-rounded-md mb-font-semibold mb-text-sm"
      :style="{
        backgroundColor: settings.cta_bg_color || '#FFFFFF',
        color: settings.cta_text_color || settings.background_color || '#6366F1',
      }"
    >
      {{ settings.cta_text }}
    </span>
  </div>
</template>

<script setup>
defineProps({
  settings: { type: Object, default: () => ({}) },
});
</script>
