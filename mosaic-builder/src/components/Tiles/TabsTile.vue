<template>
  <div :style="{ color: settings.text_color || '#F3F4F6', minHeight: '80px' }">
    <div class="mb-flex mb-border-b-2 mb-border-gray-600">
      <button
        v-for="(tab, i) in tabs"
        :key="i"
        @click.stop="activeIndex = i"
        :class="[
          'mb-px-5 mb-py-3 mb-text-sm mb-border-b-2 mb--mb-[2px] mb-transition-colors mb-bg-transparent',
          i === activeIndex
            ? 'mb-font-semibold mb-opacity-100'
            : 'mb-font-normal mb-opacity-60 hover:mb-opacity-80'
        ]"
        :style="{
          borderBottomColor: i === activeIndex ? (settings.accent_color || '#6366F1') : 'transparent',
          color: 'inherit'
        }"
      >{{ tab.title }}</button>
    </div>
    <div v-if="tabs[activeIndex]" class="mb-p-5 mb-leading-relaxed">
      {{ tabs[activeIndex].content }}
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const activeIndex = ref(0);

const tabs = computed(() => {
  const text = props.settings.tabs_data || '';
  return text.split('---').map(block => {
    const lines = block.trim().split('\n').map(l => l.trim()).filter(Boolean);
    if (lines.length >= 2) return { title: lines[0], content: lines.slice(1).join('\n') };
    if (lines.length === 1) return { title: lines[0], content: '' };
    return null;
  }).filter(Boolean);
});
</script>
