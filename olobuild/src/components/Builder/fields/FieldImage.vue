<template>
  <div class="mb-space-y-2">
    <div v-if="modelValue" class="mb-relative mb-group">
      <img
        :src="modelValue"
        alt=""
        class="mb-w-full mb-h-24 mb-object-cover mb-rounded-md mb-border mb-border-gray-600"
      />
      <button
        @click="$emit('update:modelValue', '')"
        class="mb-absolute mb-top-1 mb-right-1 mb-bg-red-600 mb-text-white mb-rounded-full mb-w-5 mb-h-5 mb-text-xs mb-flex mb-items-center mb-justify-center mb-opacity-0 group-hover:mb-opacity-100 mb-transition-opacity"
        title="Rimuovi immagine"
      >&times;</button>
    </div>
    <button
      @click="pickImage"
      class="mb-w-full mb-py-1.5 mb-px-3 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-text-xs mb-text-gray-300 hover:mb-bg-gray-600 mb-transition-colors"
    >
      {{ modelValue ? 'Cambia immagine' : 'Seleziona immagine' }}
    </button>
  </div>
</template>

<script setup>
import { useMediaPicker } from '@/composables/useMediaPicker';

const props = defineProps({
  modelValue: { type: String, default: '' },
});
const emit = defineEmits(['update:modelValue']);

const { openSingleImage } = useMediaPicker();

function pickImage() {
  openSingleImage(({ url }) => {
    emit('update:modelValue', url);
  });
}
</script>
