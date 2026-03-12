<template>
  <div :style="wrapStyle">
    <h3 style="font-size:16px;font-weight:700;color:var(--olo-color-text, #374151);margin:0 0 10px">Informazioni sala</h3>
    <div :style="gridStyle">
      <div v-for="item in items" :key="item.label" style="display:flex;flex-direction:column;gap:2px">
        <span style="font-size:11px;color:#9ca3af;font-weight:600;text-transform:uppercase">{{ item.label }}</span>
        <span style="font-size:14px;color:var(--olo-color-text, #374151);font-weight:500">{{ item.value }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { style: 'card' };
const s = computed(() => ({ ...defaults, ...props.settings }));
const items = [
  { label: 'Capienza', value: '50 persone' },
  { label: 'Superficie', value: '85 mq' },
  { label: 'Piano', value: 'Piano terra' },
  { label: 'Tipo', value: 'Sala conferenze' },
  { label: 'Zona', value: 'Centro storico' },
  { label: 'Tariffa', value: '\u20AC 25/h' },
];
const wrapStyle = computed(() => s.value.style === 'card' ? { background: '#f9fafb', border: '1px solid #e5e7eb', borderRadius: '10px', padding: '16px' } : {});
const gridStyle = computed(() => ({ display: 'grid', gridTemplateColumns: 'repeat(3,1fr)', gap: '12px 20px' }));
</script>
