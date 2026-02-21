<template>
  <div class="mb-relative mb-overflow-hidden mb-rounded-lg">
    <img
      v-if="settings.image_url"
      :src="settings.image_url"
      :alt="settings.alt_text || ''"
      class="mb-w-full mb-block mb-rounded-lg"
      :style="{
        height: settings.height || '300px',
        objectFit: settings.object_fit || 'cover',
      }"
    />
    <div
      v-else
      class="mb-flex mb-flex-col mb-items-center mb-justify-center mb-bg-gray-800 mb-rounded-lg mb-text-gray-500"
      :style="{ height: settings.height || '300px' }"
    >
      <span class="mb-text-4xl mb-mb-2">&#x1F5BC;</span>
      <span class="mb-text-sm">Click to add image</span>
    </div>
    <div
      v-if="settings.caption"
      class="mb-text-sm mb-text-gray-400 mb-text-center mb-mt-2"
      v-html="settings.caption"
    ></div>
  </div>
</template>

<script setup>
defineProps({
  settings: { type: Object, default: () => ({}) },
});
</script>
