<template>
  <div class="mb-p-8">
    <img
      v-if="settings.image"
      :src="settings.image"
      alt=""
      class="mb-w-full mb-h-auto mb-rounded-lg mb-mb-4"
    />
    <h2 class="mb-text-xl mb-font-bold mb-text-gray-100 mb-mb-2" v-html="settings.heading || 'Titolo Sezione'"></h2>
    <div class="mb-text-gray-400 mb-leading-relaxed" v-html="settings.text || 'Il contenuto va qui...'"></div>
  </div>
</template>

<script setup>
defineProps({
  settings: { type: Object, default: () => ({}) },
});
</script>
