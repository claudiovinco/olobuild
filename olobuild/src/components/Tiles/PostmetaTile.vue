<template>
  <div class="olo-postmeta" :style="wrapStyle">
    <template v-for="(item, idx) in visibleItems" :key="item.key">
      <span v-if="idx > 0 && s.layout === 'inline'" class="olo-postmeta-sep" :style="{ color: s.text_color }">{{ s.separator }}</span>
      <span class="olo-postmeta-item" :style="{ color: item.isLink ? s.link_color : s.text_color }">
        <svg v-if="s.icon_style === 'before'" :style="{ color: s.icon_color }" class="olo-postmeta-icon" v-html="item.icon" viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></svg>
        <span v-html="item.label"></span>
      </span>
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

const defaults = {
  show_date: true,
  show_author: true,
  show_categories: true,
  show_tags: false,
  show_comments_count: false,
  show_reading_time: false,
  date_format: 'd/m/Y',
  layout: 'inline',
  separator: ' · ',
  icon_style: 'none',
  text_color: '#9CA3AF',
  link_color: '#6366F1',
  icon_color: '#6B7280',
  font_size: '14',
  author_link: true,
  category_link: true,
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const icons = {
  date: '<line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/>',
  author: '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
  categories: '<path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>',
  tags: '<path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/>',
  comments: '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
  reading_time: '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
};

const visibleItems = computed(() => {
  const items = [];
  if (s.value.show_date) {
    items.push({ key: 'date', label: '5 Marzo 2026', icon: icons.date, isLink: false });
  }
  if (s.value.show_author) {
    items.push({ key: 'author', label: 'Claudio', icon: icons.author, isLink: !!s.value.author_link });
  }
  if (s.value.show_categories) {
    items.push({ key: 'categories', label: 'WordPress, Design', icon: icons.categories, isLink: !!s.value.category_link });
  }
  if (s.value.show_tags) {
    items.push({ key: 'tags', label: 'tutorial, guida', icon: icons.tags, isLink: false });
  }
  if (s.value.show_comments_count) {
    items.push({ key: 'comments', label: '3 commenti', icon: icons.comments, isLink: false });
  }
  if (s.value.show_reading_time) {
    items.push({ key: 'reading_time', label: '4 min di lettura', icon: icons.reading_time, isLink: false });
  }
  return items;
});

const wrapStyle = computed(() => {
  const st = {
    fontSize: (parseInt(s.value.font_size) || 14) + 'px',
    lineHeight: '1.6',
  };
  if (s.value.layout === 'stacked') {
    st.display = 'flex';
    st.flexDirection = 'column';
    st.gap = '4px';
  } else {
    st.display = 'flex';
    st.flexWrap = 'wrap';
    st.alignItems = 'center';
    st.gap = '0';
  }
  return st;
});
</script>

<style scoped>
.olo-postmeta-item {
  display: inline-flex;
  align-items: center;
  gap: 5px;
}
.olo-postmeta-icon {
  flex-shrink: 0;
}
.olo-postmeta-sep {
  display: inline-block;
}
</style>
