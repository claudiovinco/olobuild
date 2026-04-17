<template>
  <div class="mb-font-sans">
    <h3 :style="{ fontSize:'17px', fontWeight:'700', color:'var(--olo-color-text, #374151)', margin:'0 0 14px', fontStyle:'italic' }">Descrizione</h3>
    <div :style="textStyle">
      <p :style="{ margin:'0 0 12px' }">Splendido trilocale di nuova costruzione situato nel cuore del centro storico di Trento, a due passi da Piazza Duomo. L'appartamento si sviluppa su un unico livello di 95 m&sup2; commerciali e gode di una luminosit&agrave; eccezionale grazie alla tripla esposizione.</p>
      <p :style="{ margin:'0 0 12px' }">La zona giorno open space comprende un ampio soggiorno con accesso diretto al terrazzo panoramico di 12 m&sup2;, ideale per cene all'aperto con vista sulle montagne. La cucina moderna &egrave; dotata di elettrodomestici di ultima generazione.</p>
      <p :style="{ margin:'0' }">Completano la propriet&agrave; due camere da letto, un bagno con doccia walk-in, cantina e due posti auto nel garage condominiale. Classe energetica A2, riscaldamento a pavimento, predisposizione aria condizionata.</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ settings: { type: Object, default: () => ({}) } });
const defaults = { font_size: 14, text_color: '#374151', line_height: 1.7 };
const s = computed(() => ({ ...defaults, ...props.settings }));
const textStyle = computed(() => ({ fontSize: s.value.font_size+'px', color: s.value.text_color, lineHeight: s.value.line_height }));
</script>
