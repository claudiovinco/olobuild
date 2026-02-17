<template>
  <div class="mb-p-3" style="min-height: 60px;">
    <ul class="mb-space-y-1 mb-list-none mb-p-0 mb-m-0">
      <li
        v-for="(item, i) in items"
        :key="item.id || i"
        class="mb-flex mb-items-center mb-gap-2 mb-px-3 mb-py-2 mb-rounded mb-text-sm mb-text-gray-200 hover:mb-bg-gray-700 mb-transition-colors"
        :class="{ 'mb-border-l-2 mb-border-indigo-500': i === 0 }"
      >
        <span v-if="s.show_icons && item.tag" class="mb-text-gray-400 mb-text-xs">[{{ item.tag }}]</span>
        <span class="mb-flex-1">{{ item.title }}</span>
        <span class="mb-text-xs mb-text-gray-500">{{ item.content }}</span>
      </li>
    </ul>
    <div class="mb-text-xs mb-text-gray-500 mb-mt-2 mb-px-3">
      Style: {{ s.style || 'default' }}
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => props.settings);

const items = computed(() => {
  const raw = s.value.items;
  if (Array.isArray(raw) && raw.length) return raw;
  return [
    { title: 'Home', content: '#', tag: '' },
    { title: 'About', content: '#', tag: '' },
    { title: 'Contact', content: '#', tag: '' },
  ];
});
</script>
