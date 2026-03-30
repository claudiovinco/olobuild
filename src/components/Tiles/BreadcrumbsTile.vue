<template>
  <div class="olo-breadcrumbs">
    <nav class="mb-flex mb-items-center mb-gap-2 mb-text-sm mb-text-gray-400">
      <template v-if="s.show_home !== false">
        <a href="#" class="mb-text-blue-400 mb-no-underline hover:mb-underline" data-olo-editable="home_label">{{ s.home_label }}</a>
        <span class="mb-text-gray-600" data-olo-editable="separator">{{ s.separator }}</span>
      </template>
      <a href="#" class="mb-text-blue-400 mb-no-underline hover:mb-underline">Category</a>
      <span class="mb-text-gray-600">{{ s.separator }}</span>
      <template v-if="s.show_current !== false">
        <span class="mb-text-gray-300">Current Page</span>
      </template>
    </nav>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const defaults = {
  separator: '/',
  home_label: 'Home',
  show_home: true,
  show_current: true,
  shadow: 'none',
  border_width: '0',
  border_color: '#e5e7eb',
  border_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
};

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({ ...defaults, ...props.settings }));
</script>

<style scoped>
.olo-breadcrumbs {
  padding: 8px 0;
  min-height: 32px;
}
</style>
