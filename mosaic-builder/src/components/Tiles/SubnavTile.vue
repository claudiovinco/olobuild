<template>
  <div class="mb-p-3" style="min-height: 40px;">
    <div class="mb-flex mb-flex-wrap mb-items-center mb-gap-1">
      <template v-for="(item, i) in items" :key="item.id || i">
        <span
          v-if="i > 0 && s.divider"
          class="mb-text-gray-600 mb-mx-1"
        >|</span>
        <span
          :class="[
            'mb-text-sm mb-transition-colors',
            s.style === 'pill'
              ? 'mb-px-3 mb-py-1 mb-rounded-full mb-bg-gray-700 mb-text-gray-300'
              : 'mb-px-2 mb-py-1 mb-text-gray-300 hover:mb-text-gray-100'
          ]"
        >{{ item.title }}</span>
      </template>
    </div>
    <div class="mb-text-xs mb-text-gray-500 mb-mt-2 mb-px-2">
      Style: {{ s.style || 'default' }}{{ s.divider ? ' + divider' : '' }}
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
    { title: 'Item 1', content: '#' },
    { title: 'Item 2', content: '#' },
    { title: 'Item 3', content: '#' },
  ];
});
</script>
