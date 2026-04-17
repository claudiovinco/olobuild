<template>
  <div class="mb-font-sans">
    <div :style="gridStyle">
      <div v-for="stat in stats" :key="stat.label" :style="statStyle">
        <div :style="{ fontSize: s.number_size+'px', fontWeight:'800', color: accent, marginBottom:'4px' }">{{ stat.value }}</div>
        <div :style="{ fontSize:'13px', fontWeight:'600', color:'#6B7280', textTransform:'uppercase', letterSpacing:'0.5px' }">{{ stat.label }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { columns: 3, gap: 24, number_size: 36, accent_color: '', bg: '#F9FAFB', border_radius: 12, padding: 28 };
const s = computed(() => ({ ...defaults, ...props.settings }));
const accent = computed(() => s.value.accent_color || 'var(--olo-color-primary, #6366F1)');
const stats = [
  { value: '24', label: 'In Vendita' },
  { value: '8', label: 'In Affitto' },
  { value: '12', label: 'Venduti' },
];
const gridStyle = computed(() => ({ display:'grid', gridTemplateColumns:'repeat('+s.value.columns+',1fr)', gap: s.value.gap+'px' }));
const statStyle = computed(() => ({ background: s.value.bg, borderRadius: s.value.border_radius+'px', padding: s.value.padding+'px', textAlign:'center' }));
</script>
