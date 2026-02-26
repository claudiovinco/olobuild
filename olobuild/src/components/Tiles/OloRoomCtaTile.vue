<template>
  <div :style="wrapStyle">
    <div style="font-size:22px;font-weight:700;color:#fff;margin-bottom:6px">Prenota la tua sala</div>
    <div style="font-size:14px;color:rgba(255,255,255,.85);margin-bottom:16px">Scegli tra le nostre strutture e prenota in pochi click</div>
    <div style="display:inline-block;background:#fff;color:#1e87f0;padding:10px 24px;border-radius:8px;font-size:14px;font-weight:600">Scopri le sale</div>
  </div>
</template>

<script setup>
defineProps({ settings: { type: Object, default: () => ({}) } });
const wrapStyle = { background: 'linear-gradient(135deg,#1e3a5f 0%,#1e87f0 100%)', borderRadius: '12px', padding: '30px', textAlign: 'center' };
</script>
