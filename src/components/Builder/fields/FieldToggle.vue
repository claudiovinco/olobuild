<template>
  <button
    @click="$emit('update:modelValue', !acceso)"
    :class="[
      'mb-relative mb-w-10 mb-h-5 mb-rounded-full mb-transition-colors',
      acceso ? 'mb-bg-primary-600' : 'mb-bg-gray-600'
    ]"
  >
    <span
      :class="[
        'mb-absolute mb-top-0.5 mb-w-4 mb-h-4 mb-rounded-full mb-bg-white mb-transition-transform',
        acceso ? 'mb-left-5' : 'mb-left-0.5'
      ]"
    ></span>
  </button>
</template>

<script setup>
import { computed } from 'vue';

// Il valore di un campo non ancora salvato arriva come '' (stringa vuota): con il solo tipo Boolean Vue lo
// converte in true e l'interruttore si mostrava ACCESO mentre il renderer lo legge spento (18 nov 2026: «Attiva
// scroll fisso» acceso in ogni colonna nuova, e la sua «Distanza dal bordo» nascosta). Acceso solo se vero davvero.
const props = defineProps({
  modelValue: { type: [Boolean, String, Number], default: false },
});
defineEmits(['update:modelValue']);

const acceso = computed(() => {
  const v = props.modelValue;
  return v === true || v === 1 || v === '1' || v === 'true' || v === 'yes' || v === 'on';
});
</script>
