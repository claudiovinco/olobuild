<template>
  <div v-if="pieno" class="orz-originale">
    <span class="orz-originale-nome">{{ nome }}</span>
    <div v-if="html" class="orz-originale-testo" v-html="testo"></div>
    <p v-else class="orz-originale-testo">{{ testo }}</p>
  </div>
</template>

<script setup>
// Il testo dell'originale sopra quello da tradurre, nella scheda di una traduzione (Olobuild_Redazione_Lingue).
// L'HTML è il testo del sito, già ripulito da wp_kses_post() quando è stato salvato.
import { computed } from 'vue';

const props = defineProps({
  nome: { type: String, required: true },
  testo: { type: String, default: '' },
  html: { type: Boolean, default: false },
});

const pieno = computed(() => String(props.testo || '').replace(/<[^>]*>/g, '').trim() !== '');
</script>
