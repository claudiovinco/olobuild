<template>
  <div class="mb-p-2" style="min-height: 60px;">
    <!-- Filter bar preview -->
    <div v-if="s.show_filter" class="mb-flex mb-gap-2 mb-mb-3" :style="filterAlignStyle">
      <span
        v-for="tag in uniqueTags"
        :key="tag"
        :class="s.filter_style === 'minimal'
          ? 'mb-px-1 mb-py-0.5 mb-text-xs mb-text-gray-300 mb-uppercase mb-tracking-wide mb-border-b mb-border-gray-500'
          : 'mb-px-2 mb-py-0.5 mb-text-xs mb-rounded mb-bg-gray-600 mb-text-gray-300'"
      >{{ tag }}</span>
    </div>
    <!-- Grid -->
    <div
      class="mb-grid"
      :style="gridStyle"
    >
      <div
        v-for="(item, i) in items"
        :key="item.id || i"
        :class="s.card_style === 'minimal'
          ? 'mb-overflow-hidden'
          : 'mb-border mb-border-gray-600 mb-rounded mb-overflow-hidden'"
      >
        <div v-if="item.image" class="mb-overflow-hidden" :class="s.card_style === 'minimal' ? 'mb-rounded' : ''">
          <img :src="item.image" alt="" class="mb-w-full mb-block" style="height:120px; object-fit:cover;" />
        </div>
        <div v-else-if="hasAnyImage" class="mb-h-16 mb-bg-gray-700 mb-flex mb-items-center mb-justify-center mb-text-gray-500 mb-text-xs" :class="s.card_style === 'minimal' ? 'mb-rounded' : ''">IMG</div>
        <div :class="s.card_style === 'minimal' ? 'mb-pt-1' : 'mb-p-2'">
          <div class="mb-text-sm mb-font-medium mb-text-gray-200" :data-olo-editable="'items.' + i + '.title'">{{ item.title }}</div>
          <div class="mb-text-xs mb-text-gray-400 mb-mt-1" :data-olo-editable="'items.' + i + '.content'">{{ item.content }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  columns: '3',
  gap: 'default',
  show_filter: false,
  filter_style: 'pills',
  filter_align: 'left',
  masonry: false,
  card_style: 'default',
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const items = computed(() => {
  const raw = s.value.items;
  if (Array.isArray(raw) && raw.length) return raw;
  return [
    { title: 'Item 1', content: 'Description...', image: '', tag: 'all' },
    { title: 'Item 2', content: 'Description...', image: '', tag: 'all' },
    { title: 'Item 3', content: 'Description...', image: '', tag: 'all' },
  ];
});

const hasAnyImage = computed(() => items.value.some(i => i.image));

const uniqueTags = computed(() => {
  const tags = new Set();
  tags.add('All');
  items.value.forEach(item => {
    if (item.tag && item.tag !== 'all') tags.add(item.tag);
  });
  return [...tags];
});

const filterAlignStyle = computed(() => {
  const a = s.value.filter_align || 'left';
  if (a === 'center') return { justifyContent: 'center' };
  if (a === 'right') return { justifyContent: 'flex-end' };
  return {};
});

const gapMap = { collapse: '0px', small: '8px', default: '16px', medium: '24px', large: '40px' };

const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${parseInt(s.value.columns) || 3}, 1fr)`,
  gap: gapMap[s.value.gap] || '16px',
}));
</script>
