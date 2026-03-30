<template>
  <div>
    <h3 style="font-size:18px;font-weight:700;color:var(--olo-color-text, #374151);margin:0 0 8px">Descrizione</h3>
    <div style="font-size:14px;color:#4b5563;line-height:1.7">
      Sala conferenze moderna dotata di tutte le attrezzature necessarie per riunioni, presentazioni e workshop.
      Ampia illuminazione naturale, sistema audio integrato e connettività Wi-Fi ad alta velocità.
      Disponibile per enti pubblici e privati con prenotazione anticipata.
    </div>
  </div>
</template>

<script setup>
defineProps({ settings: { type: Object, default: () => ({}) } });
</script>
