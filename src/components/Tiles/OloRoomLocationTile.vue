<template>
  <div>
    <h3 style="font-size:16px;font-weight:700;color:var(--olo-color-text, #374151);margin:0 0 8px">Posizione</h3>
    <div style="font-size:14px;color:#374151;line-height:1.6">
      <div>Via Tartarotti 7</div>
      <div>38068 Rovereto (TN)</div>
    </div>
    <div style="margin-top:8px">
      <span style="font-size:13px;color:#1e87f0;font-weight:500;cursor:pointer">Indicazioni stradali &rarr;</span>
    </div>
  </div>
</template>

<script setup>
defineProps({ settings: { type: Object, default: () => ({}) } });
</script>
