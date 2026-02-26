<template>
  <div>
    <div style="display:flex;gap:12px;margin-bottom:16px">
      <span style="background:#1e87f0;color:#fff;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:600">Le mie prenotazioni</span>
      <span style="background:#f3f4f6;color:#6b7280;padding:6px 16px;border-radius:20px;font-size:13px">Preferiti</span>
    </div>
    <div style="border:1px solid #e5e7eb;border-radius:10px;overflow:hidden">
      <div v-for="(b,i) in bookings" :key="i" :style="{display:'flex',justifyContent:'space-between',alignItems:'center',padding:'10px 14px',borderBottom:i<2?'1px solid #f3f4f6':'none'}">
        <div>
          <div style="font-size:13px;font-weight:600;color:#1f2937">{{ b.room }}</div>
          <div style="font-size:11px;color:#6b7280">{{ b.date }}</div>
        </div>
        <span :style="{fontSize:'11px',fontWeight:'600',padding:'3px 10px',borderRadius:'12px',background:b.bg,color:b.color}">{{ b.status }}</span>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({ settings: { type: Object, default: () => ({}) } });
const bookings = [
  { room: 'Sala Giunta', date: '25 Feb 2026, 09:00-11:00', status: 'Approvata', bg: '#dcfce7', color: '#166534' },
  { room: 'Auditorium Melotti', date: '28 Feb 2026, 14:00-17:00', status: 'In attesa', bg: '#fef3c7', color: '#92400e' },
  { room: 'Sala Tartarotti', date: '3 Mar 2026, 10:00-12:00', status: 'Approvata', bg: '#dcfce7', color: '#166534' },
];
</script>
