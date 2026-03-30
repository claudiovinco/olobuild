<template>
  <div style="display:flex;align-items:center;gap:12px;padding:8px 0">
    <div style="display:flex;align-items:center;gap:6px">
      <span style="width:10px;height:10px;border-radius:50%;background:#22c55e;display:inline-block"></span>
      <span style="font-size:14px;font-weight:600;color:#166534">Aperto ora</span>
    </div>
    <span style="color:#d1d5db">|</span>
    <span style="font-size:13px;color:#6b7280">Prossimo slot: 14:00 - 15:00</span>
    <span style="color:#d1d5db">|</span>
    <span style="font-size:13px;color:#6b7280">3 slot disponibili oggi</span>
  </div>
</template>

<script setup>
defineProps({ settings: { type: Object, default: () => ({}) } });
</script>
