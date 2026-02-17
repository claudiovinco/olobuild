<template>
  <div class="olo-breadcrumbs">
    <nav class="mb-flex mb-items-center mb-gap-2 mb-text-sm mb-text-gray-400">
      <template v-if="settings.show_home !== false">
        <a href="#" class="mb-text-blue-400 mb-no-underline hover:mb-underline">{{ settings.home_label || 'Home' }}</a>
        <span class="mb-text-gray-600">{{ settings.separator || '/' }}</span>
      </template>
      <a href="#" class="mb-text-blue-400 mb-no-underline hover:mb-underline">Category</a>
      <span class="mb-text-gray-600">{{ settings.separator || '/' }}</span>
      <template v-if="settings.show_current !== false">
        <span class="mb-text-gray-300">Current Page</span>
      </template>
    </nav>
  </div>
</template>

<script setup>
const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});
</script>

<style scoped>
.olo-breadcrumbs {
  padding: 8px 0;
  min-height: 32px;
}
</style>
