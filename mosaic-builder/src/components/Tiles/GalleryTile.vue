<template>
  <div
    v-if="images.length > 0"
    class="mb-rounded-lg mb-overflow-hidden"
    :style="{
      display: 'grid',
      gridTemplateColumns: `repeat(${settings.columns || 3}, 1fr)`,
      gap: `${settings.gap || 8}px`,
    }"
  >
    <img
      v-for="(img, index) in images"
      :key="index"
      :src="typeof img === 'string' ? img : img.url"
      :alt="typeof img === 'string' ? '' : (img.alt || '')"
      class="mb-w-full mb-block mb-rounded"
      :style="{
        height: settings.img_height || '200px',
        objectFit: settings.object_fit || 'cover',
      }"
    />
  </div>
  <div
    v-else
    class="mb-flex mb-flex-col mb-items-center mb-justify-center mb-py-12 mb-text-gray-500 mb-bg-gray-800 mb-rounded-lg"
  >
    <span class="mb-text-4xl mb-mb-2">&#x1F5BC;&#x1F5BC;</span>
    <span class="mb-text-sm">Aggiungi immagini alla galleria</span>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const images = computed(() => {
  return Array.isArray(props.settings.images) ? props.settings.images : [];
});
</script>
