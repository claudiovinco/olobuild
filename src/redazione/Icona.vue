<template>
  <svg
    class="orz-icona"
    :width="dim"
    :height="dim"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="2"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
  >
    <path v-for="(d, i) in tracce" :key="i" :d="d" />
  </svg>
</template>

<script setup>
// Le icone della Redazione: tratti semplici da 24 px, il colore del testo (mai emoji).
import { computed } from 'vue';

const props = defineProps({
  nome: { type: String, required: true },
  dim: { type: [Number, String], default: 16 },
});

const SET = {
  piu: ['M12 5v14', 'M5 12h14'],
  cerca: ['M11 4a7 7 0 1 0 0 14a7 7 0 1 0 0-14z', 'M20 20l-4-4'],
  indietro: ['M15 18l-6-6l6-6'],
  lucchetto: ['M6 11h12v10H6z', 'M8 11V7a4 4 0 0 1 8 0v4'],
  esterno: ['M14 4h6v6', 'M20 4L11 13', 'M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5'],
  cestino: ['M4 7h16', 'M10 11v6', 'M14 11v6', 'M6 7l1 13h10l1-13', 'M9 7V4h6v3'],
  occhio: ['M2 12s3.5-7 10-7s10 7 10 7s-3.5 7-10 7S2 12 2 12z', 'M12 9a3 3 0 1 0 0 6a3 3 0 1 0 0-6z'],
  foto: ['M4 5h16v14H4z', 'M4 16l5-5l4 4l3-3l4 4', 'M15 9h.01'],
  file: ['M14 3H7a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V7z', 'M14 3v4h4'],
  video: ['M3 6h12v12H3z', 'M15 10l6-3v10l-6-3'],
  x: ['M6 6l12 12', 'M18 6L6 18'],
  su: ['M12 19V5', 'M6 11l6-6l6 6'],
  spunta: ['M5 12l5 5l9-10'],
  calendario: ['M4 6h16v14H4z', 'M4 10h16', 'M8 3v4', 'M16 3v4'],
  grassetto: ['M7 5h6a3.5 3.5 0 0 1 0 7H7z', 'M7 12h7a3.5 3.5 0 0 1 0 7H7z'],
  corsivo: ['M10 5h8', 'M6 19h8', 'M14 5l-4 14'],
  link: ['M10 14a4 4 0 0 0 5.7 0l3-3a4 4 0 0 0-5.7-5.7l-1 1', 'M14 10a4 4 0 0 0-5.7 0l-3 3a4 4 0 0 0 5.7 5.7l1-1'],
  elenco: ['M9 6h11', 'M9 12h11', 'M9 18h11', 'M4.5 6h.01', 'M4.5 12h.01', 'M4.5 18h.01'],
  numerato: ['M10 6h10', 'M10 12h10', 'M10 18h10', 'M4 5l1.5-1v5', 'M4 14.5a1.5 1.5 0 0 1 3 0c0 1-3 2.5-3 3.5h3'],
  citazione: ['M9 7H5v5h4v-1c0 2.5-1 4-3 5', 'M19 7h-4v5h4v-1c0 2.5-1 4-3 5'],
  annulla: ['M9 14L4 9l5-5', 'M4 9h11a5 5 0 0 1 0 10h-3'],
  ripristina: ['M15 14l5-5l-5-5', 'M20 9H9a5 5 0 0 0 0 10h3'],
  ritaglio: ['M6 2v14a2 2 0 0 0 2 2h14', 'M18 22V8a2 2 0 0 0-2-2H2'],
  documento: ['M6 3h9l4 4v14H6z', 'M9 12h7', 'M9 16h7'],
  orologio: ['M12 3a9 9 0 1 0 0 18a9 9 0 1 0 0-18z', 'M12 7v5l3 2'],
  menu: ['M5 12h.01', 'M12 12h.01', 'M19 12h.01'],
  telefono: ['M8 3h8a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z', 'M11 18h2'],
  tablet: ['M6 3h12a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z', 'M11 18h2'],
  computer: ['M3 5h18v11H3z', 'M8 20h8', 'M12 16v4'],
  aggiorna: ['M20 11a8 8 0 0 0-14.3-4.9L4 8', 'M4 4v4h4', 'M4 13a8 8 0 0 0 14.3 4.9L20 16', 'M20 20v-4h-4'],
};

const tracce = computed(() => SET[props.nome] || []);
</script>
