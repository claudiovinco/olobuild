<template>
  <div class="mb-font-sans">
    <h3 v-if="s.show_title" :style="{ margin:'0 0 16px', fontSize: s.title_size+'px', fontWeight:'700', color: s.title_color || 'inherit' }">{{ s.title_text || 'Caratteristiche' }}</h3>
    <div :style="gridStyle">
      <div v-for="spec in specs" :key="spec.label" :style="itemStyle">
        <svg xmlns="http://www.w3.org/2000/svg" :width="s.icon_size || 22" :height="s.icon_size || 22" viewBox="0 0 24 24" fill="none" :stroke="s.icon_color || 'var(--olo-color-primary, #e1474f)'" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" v-html="spec.icon"></svg>
        <span :style="{ fontSize: (s.value_size || 18)+'px', fontWeight:'700', color: s.value_color || 'var(--olo-color-text, #1f2937)', lineHeight:'1.2' }">{{ spec.value }}</span>
        <span :style="{ fontSize: (s.label_size || 12)+'px', color: s.label_color || 'var(--olo-color-text-muted, #6b7280)', textTransform:'uppercase', letterSpacing:'0.3px', fontWeight:'500', marginTop:'2px' }">{{ spec.label }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { columns: 4, gap: 12, style: 'cards', show_title: true, title_text: 'Caratteristiche', title_size: 18, title_color: '', card_bg: '', card_border: '', card_radius: 10, icon_color: '', icon_size: 22, value_color: '', value_size: 18, label_color: '', label_size: 12 };
const s = computed(() => ({ ...defaults, ...props.settings }));

const specs = [
  { value: '95 m\u00B2', label: 'Superficie', icon: '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>' },
  { value: '4', label: 'Locali', icon: '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>' },
  { value: '2', label: 'Camere', icon: '<path d="M2 4v16"/><path d="M2 8h18a2 2 0 0 1 2 2v10"/><path d="M2 17h20"/><path d="M6 8v9"/>' },
  { value: '1', label: 'Bagni', icon: '<path d="M4 12h16a1 1 0 0 1 1 1v3a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4v-3a1 1 0 0 1 1-1z"/><path d="M6 12V5a2 2 0 0 1 2-2h3v2.25"/>' },
];

const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${s.value.columns}, 1fr)`,
  gap: s.value.gap + 'px',
}));

const cardBg = computed(() => s.value.card_bg || 'var(--olo-color-surface-alt, #f6f7f9)');
const cardBorder = computed(() => s.value.card_border || 'var(--olo-color-border, #e5e7eb)');
const itemStyle = computed(() => {
  const base = { display: 'flex', flexDirection: 'column', alignItems: 'center', textAlign: 'center' };
  if (s.value.style === 'cards') {
    return { ...base, background: cardBg.value, border: '1px solid ' + cardBorder.value, borderRadius: s.value.card_radius + 'px', padding: '16px 12px' };
  }
  if (s.value.style === 'pills') {
    return { ...base, flexDirection: 'row', gap: '10px', textAlign: 'left', background: cardBg.value, borderRadius: '100px', padding: '12px 16px' };
  }
  return { ...base, flexDirection: 'row', gap: '12px', textAlign: 'left', padding: '8px 0', borderBottom: '1px solid ' + cardBorder.value };
});
</script>
