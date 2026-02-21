<template>
  <div class="mb-overflow-x-auto mb-p-2">
    <table v-if="rows.length" class="mb-w-full" style="border-collapse:collapse;" :style="tableStyle">
      <thead>
        <tr :style="{ background: settings.header_bg, color: settings.header_text_color }">
          <th
            v-for="(cell, ci) in header"
            :key="ci"
            class="mb-text-left mb-font-semibold mb-text-sm"
            :style="cellStyle"
          >{{ cell }}</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(row, ri) in bodyRows"
          :key="ri"
          :style="rowStyle(ri)"
          @mouseover="settings.hover_effect ? ($event.currentTarget.style.background = 'rgba(99,102,241,0.1)') : null"
          @mouseleave="settings.hover_effect ? ($event.currentTarget.style.background = stripeColor(ri)) : null"
        >
          <td
            v-for="(cell, ci) in row"
            :key="ci"
            class="mb-text-sm"
            :style="cellStyle"
          >{{ cell }}</td>
        </tr>
      </tbody>
    </table>
    <div v-else class="mb-text-center mb-text-gray-500 mb-py-4">Nessun dato nella tabella</div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const rows = computed(() => {
  const text = props.settings.table_data || '';
  return text.split('\n').map(l => l.trim()).filter(Boolean).map(l => l.split('|').map(c => c.trim()));
});

const header = computed(() => rows.value[0] || []);
const bodyRows = computed(() => rows.value.slice(1));

const tableStyle = computed(() => ({
  color: props.settings.text_color,
  border: props.settings.bordered ? `1px solid ${props.settings.border_color}` : 'none',
}));

const cellStyle = computed(() => ({
  padding: '10px 16px',
  border: props.settings.bordered ? `1px solid ${props.settings.border_color}` : 'none',
}));

function stripeColor(index) {
  return props.settings.striped && index % 2 === 1 ? 'rgba(255,255,255,0.03)' : 'transparent';
}

function rowStyle(index) {
  return { background: stripeColor(index) };
}
</script>
