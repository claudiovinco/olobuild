<template>
  <div :style="gridStyle">
    <div v-for="price in prices" :key="price.label" :style="cardStyle">
      <div :style="{ fontSize: s.amount_size+'px', fontWeight:'700', color: accent }">{{ price.amount }}</div>
      <div :style="{ fontSize: s.label_size+'px', color: s.label_color, marginTop:'4px' }">{{ price.label }}</div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });

const defaults = {
  columns: '3', gap: '16', card_radius: '12', card_border: '#E5E7EB', card_padding: '20',
  accent_color: '', amount_size: '26', label_size: '14', label_color: '#6B7280', hover_border: true,
};
const s = computed(() => ({ ...defaults, ...props.settings }));
const accent = computed(() => s.value.accent_color || 'var(--olo-color-primary, #6366F1)');

const prices = [
  { amount: '\u20AC 75', label: '/ notte' },
  { amount: '\u20AC 95', label: '/ notte weekend' },
  { amount: '\u20AC 450', label: '/ settimana' },
];

const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${parseInt(s.value.columns) || 3}, 1fr)`,
  gap: s.value.gap + 'px',
}));

const cardStyle = computed(() => ({
  border: '2px solid ' + s.value.card_border,
  borderRadius: s.value.card_radius + 'px',
  padding: s.value.card_padding + 'px 16px',
  textAlign: 'center',
}));
</script>
