<template>
  <div class="mb-p-3" style="min-height: 40px;">
    <!-- WP Menu source notice -->
    <div v-if="s.source === 'wp_menu'" class="mb-text-[10px] mb-text-gray-500 mb-mb-2 mb-flex mb-items-center mb-gap-1">
      <svg width="12" height="12" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="10" cy="10" r="8"/><line x1="10" y1="9" x2="10" y2="14"/><circle cx="10" cy="6.5" r="0.5" fill="currentColor"/></svg>
      <span v-if="selectedMenu">Menu: {{ selectedMenu.name }} ({{ s.menu_depth === 'top' ? 'livello 1' : s.menu_depth === 'auto' ? 'auto' : 'figli ID ' + s.parent_item }})</span>
      <span v-else>{{ t('Seleziona un menu nell\'Inspector') }}</span>
    </div>

    <!-- Items preview -->
    <div :class="containerClasses" :style="containerStyle">
      <template v-for="(item, i) in displayItems" :key="item.id || i">
        <span
          v-if="i > 0 && s.divider"
          class="mb-text-gray-600"
          :style="{ alignSelf: 'stretch', width: '1px', background: '#4b5563' }"
        ></span>
        <span
          :style="itemStyle(i)"
          @mouseenter="hoverIdx = i"
          @mouseleave="hoverIdx = -1"
          :data-olo-editable="'items.' + i + '.title'"
        >{{ item.title || item.label || '—' }}</span>
      </template>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed, ref } from 'vue';

const oloData = window.oloData || {};
const wpMenus = oloData.wpMenus || [];

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  source: 'manual',
  style: 'default',
  divider: false,
  alignment: 'left',
  gap: '8',
  font_size: '14',
  font_weight: '400',
  text_transform: 'none',
  link_color: '',
  hover_color: '',
  active_color: '',
  active_style: 'none',
  bg_color: '',
  hover_bg: '',
  active_bg: '',
  border_radius: '4',
  padding_x: '12',
  padding_y: '6',
  menu_id: 0,
  menu_depth: 'top',
  parent_item: '0',
};
const s = computed(() => ({ ...defaults, ...props.settings }));
const hoverIdx = ref(-1);

const selectedMenu = computed(() => {
  const id = parseInt(s.value.menu_id);
  return wpMenus.find(m => m.id === id) || null;
});

const displayItems = computed(() => {
  if (s.value.source === 'wp_menu') {
    const menu = selectedMenu.value;
    if (!menu) return [{ title: 'Voce 1' }, { title: 'Voce 2' }, { title: 'Voce 3' }];
    // In builder we show placeholder items from menu name
    const items = (menu.items || []).slice(0, 8);
    if (items.length) return items.map(it => ({ title: it.title || it.label }));
    return [{ title: menu.name + ' (voci)' }];
  }
  const raw = s.value.items;
  if (Array.isArray(raw) && raw.length) return raw;
  return [{ title: 'Elemento 1' }, { title: 'Elemento 2' }, { title: 'Elemento 3' }];
});

const linkColor = computed(() => s.value.link_color || '#9ca3af');
const hoverColor = computed(() => s.value.hover_color || '#e5e7eb');
const activeColor = computed(() => s.value.active_color || '#6366f1');

const containerClasses = computed(() => ['mb-flex', 'mb-flex-wrap', 'mb-items-center']);

const containerStyle = computed(() => {
  const align = s.value.alignment;
  return {
    gap: `${parseInt(s.value.gap) || 8}px`,
    justifyContent: align === 'center' ? 'center' : align === 'right' ? 'flex-end' : align === 'stretch' ? 'space-around' : 'flex-start',
  };
});

function itemStyle(i) {
  const isActive = i === 0;
  const isHover = hoverIdx.value === i;
  const px = parseInt(s.value.padding_x) || 12;
  const py = parseInt(s.value.padding_y) || 6;
  const radius = (v => isNaN(v) ? 4 : v)(parseInt(s.value.border_radius));
  const style_type = s.value.style;

  const st = {
    fontSize: `${parseInt(s.value.font_size) || 14}px`,
    fontWeight: s.value.font_weight || '400',
    textTransform: s.value.text_transform || 'none',
    padding: `${py}px ${px}px`,
    borderRadius: `${radius}px`,
    color: isActive ? activeColor.value : isHover ? hoverColor.value : linkColor.value,
    background: s.value.bg_color || 'transparent',
    cursor: 'pointer',
    transition: 'all 0.2s ease',
    borderBottom: 'none',
    textDecoration: 'none',
    border: 'none',
  };

  // Active
  if (isActive) {
    if (s.value.active_style === 'underline') st.borderBottom = `2px solid ${activeColor.value}`;
    if (s.value.active_style === 'background') st.background = s.value.active_bg || `${activeColor.value}15`;
    if (s.value.active_style === 'bold') st.fontWeight = '700';
    if (s.value.active_bg && s.value.active_style !== 'background') st.background = s.value.active_bg;
  }

  // Hover
  if (isHover && !isActive) {
    if (s.value.hover_bg) st.background = s.value.hover_bg;
  }

  // Style presets
  if (style_type === 'pill') {
    st.borderRadius = '999px';
    if (isActive) st.background = s.value.active_bg || `${activeColor.value}20`;
  } else if (style_type === 'underline') {
    st.borderRadius = '0';
    st.borderBottom = isActive ? `2px solid ${activeColor.value}` : '2px solid transparent';
  } else if (style_type === 'boxed') {
    st.border = `1px solid ${isActive ? activeColor.value : 'rgba(255,255,255,0.15)'}`;
    if (isActive) st.background = s.value.active_bg || `${activeColor.value}10`;
  }

  return st;
}
</script>
