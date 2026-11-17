<template>
  <textarea
    :value="mostrato"
    @input="$emit('update:modelValue', $event.target.value)"
    rows="3"
    class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900 mb-resize-y"
  />
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  modelValue: { type: String, default: '' },
  // Elenco salvato con un separatore storico (es. '|'): si mostra una voce per riga. Il renderer accetta
  // sia il separatore sia l'a capo, quindi si salva così come lo si scrive, senza migrare i dati (16 nov 2026).
  righeDa: { type: String, default: '' },
});
defineEmits(['update:modelValue']);

const mostrato = computed(() => {
  const v = props.modelValue == null ? '' : String(props.modelValue);
  if (!props.righeDa || !v.includes(props.righeDa)) return v;
  return v.split(props.righeDa).map((r) => r.trim()).join(String.fromCharCode(10));
});
</script>
