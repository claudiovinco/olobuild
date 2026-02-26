<template>
  <div>
    <h3 style="font-size:16px;font-weight:700;color:#1f2937;margin:0 0 10px">Regole di prenotazione</h3>
    <div style="display:flex;flex-direction:column;gap:6px;font-size:13px;color:#374151">
      <div style="display:flex;align-items:center;gap:6px"><span style="color:#f59e0b">&#9888;</span> Anticipo minimo: 24 ore</div>
      <div style="display:flex;align-items:center;gap:6px"><span style="color:#f59e0b">&#9888;</span> Anticipo massimo: 30 giorni</div>
      <div style="display:flex;align-items:center;gap:6px"><span style="color:#f59e0b">&#9888;</span> Max prenotazioni attive: 3</div>
      <div style="display:flex;align-items:center;gap:6px"><span style="color:#6b7280">&#9201;</span> Durata slot: 60 minuti</div>
    </div>
  </div>
</template>

<script setup>
defineProps({ settings: { type: Object, default: () => ({}) } });
</script>
