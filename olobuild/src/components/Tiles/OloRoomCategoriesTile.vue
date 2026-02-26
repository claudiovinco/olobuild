<template>
  <div>
    <h3 style="font-size:18px;font-weight:700;color:#1f2937;margin:0 0 12px">Tipi di struttura</h3>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px">
      <div v-for="cat in categories" :key="cat.name" :style="{background:cat.bg,borderRadius:'10px',padding:'16px',textAlign:'center',color:'#fff'}">
        <div style="font-size:24px;margin-bottom:4px">{{ cat.icon }}</div>
        <div style="font-size:14px;font-weight:600">{{ cat.name }}</div>
        <div style="font-size:12px;opacity:.8;margin-top:2px">{{ cat.count }} strutture</div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({ settings: { type: Object, default: () => ({}) } });
const categories = [
  { name: 'Sale conferenze', count: 5, icon: '&#127970;', bg: '#3b82f6' },
  { name: 'Auditorium', count: 2, icon: '&#127917;', bg: '#8b5cf6' },
  { name: 'Palestre', count: 3, icon: '&#127947;', bg: '#10b981' },
  { name: 'Piazze', count: 1, icon: '&#127961;', bg: '#f59e0b' },
];
</script>
