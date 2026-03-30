<template>
  <div :style="wrapStyle">
    <img v-if="s.logo_url" :src="s.logo_url" alt="Club logo"
         :style="{ width: logoW + 'px', height: 'auto', flexShrink: 0 }" />
    <div style="display:flex;flex-direction:column;gap:2px">
      <span v-if="s.show_category" :style="catStyle">Family</span>
      <span v-if="s.show_group" :style="groupStyle">Trentino Marketing</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  layout: 'horizontal', logo_url: '', logo_width: '80',
  show_group: false, show_category: true,
  text_size: '14', text_color: '#374151', text_weight: '600',
  group_size: '12', group_color: '#9CA3AF',
  gap: '12', align: 'left',
  bg_color: '', border_color: '#E5E7EB', border_radius: '10', padding: '12',
};
const s = computed(() => ({ ...defaults, ...props.settings }));
const logoW = computed(() => Math.min(parseInt(s.value.logo_width) || 80, 200));

const alignMap = { left: 'flex-start', center: 'center', right: 'flex-end' };
const wrapStyle = computed(() => {
  const v = s.value;
  const st = {
    display: 'flex',
    flexDirection: v.layout === 'horizontal' ? 'row' : 'column',
    alignItems: v.layout === 'horizontal' ? 'center' : (alignMap[v.align] || 'flex-start'),
    gap: (parseInt(v.gap) || 12) + 'px',
  };
  if (v.bg_color) st.background = v.bg_color;
  if (v.border_color) st.border = '1px solid ' + v.border_color;
  if (parseInt(v.border_radius)) st.borderRadius = v.border_radius + 'px';
  if (parseInt(v.padding)) st.padding = v.padding + 'px';
  return st;
});

const catStyle = computed(() => ({
  fontSize: Math.min(parseInt(s.value.text_size) || 14, 22) + 'px',
  fontWeight: s.value.text_weight,
  color: s.value.text_color,
}));

const groupStyle = computed(() => ({
  fontSize: Math.min(parseInt(s.value.group_size) || 12, 18) + 'px',
  color: s.value.group_color,
}));
</script>
