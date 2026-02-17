<template>
  <div style="min-height: 80px;">
    <!-- Navigation -->
    <div :class="containerClass">
      <div :class="navWrapClass">
        <div class="mb-flex" :class="s.vertical ? 'mb-flex-col mb-gap-1' : 'mb-border-b-2 mb-border-gray-600'">
          <button
            v-for="(item, i) in items"
            :key="i"
            @click.stop="activeIndex = i"
            :class="[
              'mb-px-4 mb-py-2 mb-text-sm mb-bg-transparent mb-transition-colors',
              i === activeIndex
                ? 'mb-font-semibold mb-text-gray-100'
                : 'mb-font-normal mb-text-gray-400 hover:mb-text-gray-200'
            ]"
            :style="tabStyle(i)"
          >{{ item.title }}</button>
        </div>
        <div class="mb-text-xs mb-text-gray-500 mb-mt-1 mb-px-1">{{ navLabel }}</div>
      </div>

      <!-- Content -->
      <div :class="contentWrapClass">
        <div v-if="items[activeIndex]" class="mb-p-4 mb-leading-relaxed mb-text-sm mb-text-gray-300" v-html="items[activeIndex].content"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const activeIndex = ref(0);

const s = computed(() => props.settings);

const items = computed(() => {
  const raw = s.value.items;
  if (Array.isArray(raw) && raw.length) return raw;
  return [
    { title: 'Tab 1', content: 'Tab content 1.' },
    { title: 'Tab 2', content: 'Tab content 2.' },
  ];
});

const navLabel = computed(() => {
  const style = s.value.nav_style || 'tab';
  const labels = { tab: 'Tab', subnav: 'Subnav', 'subnav-pill': 'Subnav Pill' };
  return labels[style] || 'Tab';
});

const containerClass = computed(() => s.value.vertical ? 'mb-flex mb-gap-4' : '');
const navWrapClass = computed(() => s.value.vertical ? 'mb-w-1/4 mb-flex-shrink-0' : '');
const contentWrapClass = computed(() => s.value.vertical ? 'mb-flex-1' : '');

function tabStyle(i) {
  const active = i === activeIndex.value;
  if (s.value.vertical) {
    return {
      borderLeft: active ? '2px solid #6366F1' : '2px solid transparent',
      borderBottom: 'none',
      textAlign: 'left',
      width: '100%',
    };
  }
  return {
    borderBottom: active ? '2px solid #6366F1' : '2px solid transparent',
    marginBottom: '-2px',
  };
}
</script>
