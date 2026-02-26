<template>
  <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;text-align:center">
    <div v-for="stat in stats" :key="stat.label" style="padding:16px;background:#f9fafb;border-radius:10px;border:1px solid #e5e7eb">
      <div style="font-size:28px;font-weight:700;color:#1e87f0">{{ stat.value }}</div>
      <div style="font-size:13px;color:#6b7280;margin-top:4px">{{ stat.label }}</div>
    </div>
  </div>
</template>

<script setup>
defineProps({ settings: { type: Object, default: () => ({}) } });
const stats = [
  { value: '12', label: 'Sale disponibili' },
  { value: '3', label: 'Zone coperte' },
  { value: '1.250', label: 'Posti totali' },
  { value: '847', label: 'Prenotazioni' },
];
</script>
