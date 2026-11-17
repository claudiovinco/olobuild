<!-- min/max: senza, il browser accetta anni fino a sei cifre e non passa da solo dall'anno all'ora
     (scrivendo «19062027» e poi l'ora, l'anno diventava 202721 e il Conto alla rovescia mostrava «NaN»). -->
<template>
  <input
    type="datetime-local"
    min="1900-01-01T00:00"
    max="9999-12-31T23:59"
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
    class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
    style="cursor: pointer; color-scheme: light;"
  />
</template>

<script setup>
defineProps({
  modelValue: { type: String, default: '' },
});
defineEmits(['update:modelValue']);
</script>
