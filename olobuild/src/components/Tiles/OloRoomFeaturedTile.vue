<template>
  <div>
    <h3 style="font-size:18px;font-weight:700;color:var(--olo-color-text, #374151);margin:0 0 12px">Sale in evidenza</h3>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px">
      <div v-for="room in rooms" :key="room.name" style="border:1px solid #e5e7eb;border-radius:10px;overflow:hidden">
        <div :style="{height:'110px',background:room.bg,position:'relative'}">
          <span style="position:absolute;top:8px;right:8px;background:rgba(0,0,0,.5);color:#fbbf24;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:700">&#9733; In evidenza</span>
        </div>
        <div style="padding:10px">
          <div style="font-size:14px;font-weight:600;color:var(--olo-color-text, #374151)">{{ room.name }}</div>
          <div style="font-size:12px;color:#6b7280;margin-top:2px">{{ room.info }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({ settings: { type: Object, default: () => ({}) } });
const rooms = [
  { name: 'Auditorium Melotti', info: '200 posti · Rovereto', bg: 'linear-gradient(135deg,#7c3aed,#5b21b6)' },
  { name: 'Salone delle Feste', info: '120 posti · Palazzo Fedrigotti', bg: 'linear-gradient(135deg,#0891b2,#155e75)' },
  { name: 'Aula Magna', info: '80 posti · Centro Civico', bg: 'linear-gradient(135deg,#059669,#065f46)' },
];
</script>
