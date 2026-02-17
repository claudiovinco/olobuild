<template>
  <div
    class="mb-rounded-xl mb-overflow-hidden mb-text-center"
    :style="{ background: settings.bg_color || '#1F2937', color: settings.text_color || '#F3F4F6', minHeight: '80px' }"
  >
    <img
      v-if="settings.photo"
      :src="settings.photo"
      :alt="settings.name"
      class="mb-w-full mb-h-[250px] mb-object-cover"
    />
    <div v-else class="mb-w-full mb-h-[250px] mb-bg-gray-700 mb-flex mb-items-center mb-justify-center mb-text-6xl">
      &#x1F464;
    </div>
    <div class="mb-p-6">
      <h3 class="mb-text-xl mb-font-semibold mb-mb-1" v-html="settings.name || 'Name'"></h3>
      <div class="mb-text-sm mb-opacity-70 mb-mb-3" v-html="settings.role || 'Role'"></div>
      <div v-if="settings.bio" class="mb-text-sm mb-opacity-80 mb-leading-relaxed" v-html="settings.bio"></div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  settings: { type: Object, default: () => ({}) },
});
</script>
