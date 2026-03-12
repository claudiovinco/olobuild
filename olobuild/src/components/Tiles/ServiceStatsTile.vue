<template>
  <!-- Grid layout -->
  <div v-if="s.layout === 'grid'" :style="gridStyle">
    <div v-for="stat in stats" :key="stat.label" :style="itemStyle">
      <div :style="{ marginBottom:'6px', display:'flex', justifyContent: justifyAlign }">
        <svg :width="iconSz" :height="iconSz" viewBox="0 0 24 24" fill="none" v-html="stat.svg"></svg>
      </div>
      <div :style="numStyle">{{ stat.value }}</div>
      <div :style="lblStyle">{{ stat.label }}</div>
    </div>
  </div>
  <!-- Inline layout -->
  <div v-else :style="inlineStyle">
    <div v-for="(stat, idx) in stats" :key="stat.label" :style="[itemStyle, inlineItemStyle(idx)]">
      <span style="display:flex;align-items:center">
        <svg :width="iconSz" :height="iconSz" viewBox="0 0 24 24" fill="none" v-html="stat.svg"></svg>
      </span>
      <span :style="numStyle">{{ stat.value }}</span>
      <span :style="lblStyle">{{ stat.label }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  layout: 'grid', columns: '4', gap: '16', align: 'center',
  icon_size: '24', number_size: '22', label_size: '11',
  icon_color: '', number_color: '', label_color: '',
  show_dividers: false, divider_color: '#E5E7EB',
  item_bg: '', item_border_radius: '8', item_padding: '0', item_border_color: '',
  bg_color: '', border_radius: '0', padding: '0',
};
const s = computed(() => ({ ...defaults, ...props.settings }));
const iconSz = computed(() => Math.min(parseInt(s.value.icon_size) || 24, 32));
const c = computed(() => s.value.icon_color || '#6366F1');

const justifyAlign = computed(() =>
  s.value.align === 'center' ? 'center' : s.value.align === 'right' ? 'flex-end' : 'flex-start'
);

const stats = computed(() => [
  { value: '3', label: 'Camere', svg: `<path d="M3 18V12C3 10.3431 4.34315 9 6 9H18C19.6569 9 21 10.3431 21 12V18" stroke="${c.value}" stroke-width="1.8" stroke-linecap="round"/><path d="M3 18V20M21 18V20" stroke="${c.value}" stroke-width="1.8" stroke-linecap="round"/><path d="M7 9V7C7 5.89543 7.89543 5 9 5H15C16.1046 5 17 5.89543 17 7V9" stroke="${c.value}" stroke-width="1.8" stroke-linecap="round"/><circle cx="8.5" cy="7.5" r="1.5" fill="${c.value}" opacity="0.5"/>` },
  { value: '2', label: 'Bagni', svg: `<path d="M4 12H20V15C20 17.2091 18.2091 19 16 19H8C5.79086 19 4 17.2091 4 15V12Z" stroke="${c.value}" stroke-width="1.8"/><path d="M4 12V5C4 4.44772 4.44772 4 5 4H7C7.55228 4 8 4.44772 8 5V12" stroke="${c.value}" stroke-width="1.8" stroke-linecap="round"/><path d="M7 19V21M17 19V21" stroke="${c.value}" stroke-width="1.8" stroke-linecap="round"/>` },
  { value: '6', label: 'Ospiti', svg: `<circle cx="9" cy="7" r="3" stroke="${c.value}" stroke-width="1.8"/><path d="M3 20C3 16.6863 5.68629 14 9 14C12.3137 14 15 16.6863 15 20" stroke="${c.value}" stroke-width="1.8" stroke-linecap="round"/><circle cx="17" cy="8" r="2" stroke="${c.value}" stroke-width="1.8" opacity="0.6"/><path d="M17 14C19.2091 14 21 15.7909 21 18" stroke="${c.value}" stroke-width="1.8" stroke-linecap="round" opacity="0.6"/>` },
  { value: '85 m²', label: 'Superficie', svg: `<rect x="4" y="4" width="16" height="16" rx="2" stroke="${c.value}" stroke-width="1.8"/><path d="M4 9H8M4 14H8" stroke="${c.value}" stroke-width="1.2" stroke-linecap="round" opacity="0.35"/><path d="M9 4V8M14 4V8" stroke="${c.value}" stroke-width="1.2" stroke-linecap="round" opacity="0.35"/><path d="M10 12L14 12M12 10V14" stroke="${c.value}" stroke-width="1.5" stroke-linecap="round" opacity="0.5"/>` },
]);

const wrapBase = computed(() => {
  const st = {};
  if (s.value.bg_color) st.background = s.value.bg_color;
  if (parseInt(s.value.border_radius)) st.borderRadius = s.value.border_radius + 'px';
  if (parseInt(s.value.padding)) st.padding = s.value.padding + 'px';
  return st;
});

const gridStyle = computed(() => ({
  ...wrapBase.value,
  display: 'grid',
  gridTemplateColumns: `repeat(${parseInt(s.value.columns) || 4}, 1fr)`,
  gap: s.value.gap + 'px',
  textAlign: s.value.align,
}));

const inlineStyle = computed(() => ({
  ...wrapBase.value,
  display: 'flex',
  flexWrap: 'wrap',
  gap: s.value.gap + 'px',
  alignItems: 'center',
  justifyContent: justifyAlign.value,
}));

const itemStyle = computed(() => {
  const st = {};
  if (s.value.item_bg) st.background = s.value.item_bg;
  if (parseInt(s.value.item_border_radius)) st.borderRadius = s.value.item_border_radius + 'px';
  if (parseInt(s.value.item_padding)) st.padding = s.value.item_padding + 'px';
  if (s.value.item_border_color) st.border = '1px solid ' + s.value.item_border_color;
  return st;
});

function inlineItemStyle(idx) {
  if (!s.value.show_dividers || idx === 0) return {};
  return { paddingLeft: s.value.gap + 'px', borderLeft: '1px solid ' + s.value.divider_color };
}

const numStyle = computed(() => ({
  fontSize: Math.min(parseInt(s.value.number_size) || 22, 28) + 'px',
  fontWeight: '700', color: s.value.number_color || 'var(--olo-color-text, #374151)', lineHeight: '1.2',
}));

const lblStyle = computed(() => ({
  fontSize: Math.min(parseInt(s.value.label_size) || 11, 14) + 'px',
  textTransform: 'uppercase', color: s.value.label_color || 'var(--olo-color-text-muted, #9CA3AF)',
  letterSpacing: '0.5px', fontWeight: '600', marginTop: '2px',
}));
</script>
