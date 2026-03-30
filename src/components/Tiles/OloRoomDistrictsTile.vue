<template>
  <div>
    <h3 style="font-size:18px;font-weight:700;color:var(--olo-color-text, #374151);margin:0 0 12px">Zone</h3>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px">
      <div v-for="zone in zones" :key="zone.name" style="border:1px solid #e5e7eb;border-radius:10px;padding:14px;display:flex;align-items:center;gap:10px">
        <div :style="{width:'36px',height:'36px',borderRadius:'50%',background:zone.bg,display:'flex',alignItems:'center',justifyContent:'center',fontSize:'16px',flexShrink:'0'}">{{ zone.icon }}</div>
        <div>
          <div style="font-size:14px;font-weight:600;color:var(--olo-color-text, #374151)">{{ zone.name }}</div>
          <div style="font-size:12px;color:#6b7280">{{ zone.count }} sale</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({ settings: { type: Object, default: () => ({}) } });
const zones = [
  { name: 'Centro storico', count: 4, icon: '&#127963;', bg: '#dbeafe' },
  { name: 'Brione', count: 2, icon: '&#127956;', bg: '#d1fae5' },
  { name: 'Fucine', count: 1, icon: '&#127981;', bg: '#fef3c7' },
];
</script>
