<template>
  <div class="olo-tagcloud" :style="containerStyle">
    <a
      v-for="(tag, i) in sampleTags"
      :key="i"
      href="#"
      class="olo-tagcloud-tag"
      :style="tagStyle(tag)"
      @click.prevent
      @mouseenter="hoveredIndex = i"
      @mouseleave="hoveredIndex = -1"
    >
      {{ tag.name }}
      <span v-if="s.show_count" class="olo-tagcloud-count" style="opacity:.6;">({{ tag.count }})</span>
    </a>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { resolveColor, TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

const defaults = {
  taxonomy: 'post_tag',
  custom_taxonomy: '',
  min_font: '12',
  max_font: '28',
  max_tags: '30',
  orderby: 'name',
  order: 'ASC',
  show_count: false,
  separator: ' ',
  layout: 'cloud',
  columns: '3',
  text_color: '',
  hover_color: '',
  background_color: '',
  hover_background: '',
  border_radius: '16',
  padding: '6 14',
  gap: '8',
  font_weight: '500',
  link_underline: false,
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const hoveredIndex = ref(-1);

const sampleTags = [
  { name: 'WordPress', count: 24 },
  { name: 'Design', count: 18 },
  { name: 'PHP', count: 15 },
  { name: 'JavaScript', count: 31 },
  { name: 'CSS', count: 12 },
  { name: 'Vue.js', count: 28 },
  { name: 'Tutorial', count: 9 },
  { name: 'Sviluppo', count: 22 },
  { name: 'UI/UX', count: 7 },
  { name: 'Performance', count: 14 },
  { name: 'SEO', count: 11 },
  { name: 'Responsive', count: 16 },
];

const minCount = computed(() => Math.min(...sampleTags.map(t => t.count)));
const maxCount = computed(() => Math.max(...sampleTags.map(t => t.count)));

function getFontSize(count) {
  const minF = parseInt(s.value.min_font) || 12;
  const maxF = parseInt(s.value.max_font) || 28;
  const range = maxCount.value - minCount.value;
  if (range === 0) return (minF + maxF) / 2;
  return minF + ((count - minCount.value) / range) * (maxF - minF);
}

const parsePadding = computed(() => {
  const parts = (s.value.padding || '6 14').trim().split(/\s+/);
  if (parts.length === 1) return parts[0] + 'px';
  return parts.map(p => p + 'px').join(' ');
});

const containerStyle = computed(() => {
  const gap = (parseInt(s.value.gap) || 8) + 'px';

  if (s.value.layout === 'list') {
    return {
      display: 'flex',
      flexDirection: 'column',
      gap,
    };
  }
  if (s.value.layout === 'grid') {
    return {
      display: 'grid',
      gridTemplateColumns: `repeat(${parseInt(s.value.columns) || 3}, 1fr)`,
      gap,
    };
  }
  // cloud
  return {
    display: 'flex',
    flexWrap: 'wrap',
    gap,
    alignItems: 'center',
  };
});

function tagStyle(tag) {
  const idx = sampleTags.indexOf(tag);
  const isHovered = hoveredIndex.value === idx;
  return {
    fontSize: getFontSize(tag.count) + 'px',
    fontWeight: s.value.font_weight || '500',
    color: isHovered ? resolveColor(s.value.hover_color, TOKENS.onPrimary) : resolveColor(s.value.text_color, TOKENS.text),
    backgroundColor: isHovered ? resolveColor(s.value.hover_background, TOKENS.primary) : resolveColor(s.value.background_color, TOKENS.surfaceAlt),
    borderRadius: ((v => isNaN(v) ? 16 : v)(parseInt(s.value.border_radius))) + 'px',
    padding: parsePadding.value,
    display: 'inline-flex',
    alignItems: 'center',
    gap: '4px',
    textDecoration: s.value.link_underline ? 'underline' : 'none',
    transition: 'all .2s ease',
    cursor: 'pointer',
    lineHeight: '1.4',
  };
}
</script>

<style scoped>
.olo-tagcloud-tag:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
</style>
