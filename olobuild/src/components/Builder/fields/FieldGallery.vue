<template>
  <div class="mb-space-y-2">
    <div v-if="Array.isArray(modelValue) && modelValue.length" class="mb-space-y-2">
      <div
        v-for="(img, idx) in modelValue"
        :key="idx"
        class="mb-relative mb-group mb-flex mb-gap-2 mb-items-start"
      >
        <img
          :src="img.url"
          :alt="img.alt || ''"
          class="mb-w-16 mb-h-16 mb-object-cover mb-rounded mb-border mb-border-gray-600 mb-shrink-0"
        />
        <div class="mb-flex-1 mb-min-w-0">
          <input
            type="text"
            :value="img.caption || ''"
            @input="updateCaption(idx, $event.target.value)"
            placeholder="Didascalia..."
            class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-px-2 mb-py-1 mb-text-xs mb-text-gray-200 mb-placeholder-gray-500"
          />
        </div>
        <button
          @click="removeImage(idx)"
          class="mb-bg-red-600 mb-text-white mb-rounded-full mb-w-5 mb-h-5 mb-text-[10px] mb-flex mb-items-center mb-justify-center mb-opacity-0 group-hover:mb-opacity-100 mb-transition-opacity mb-shrink-0"
          title="Rimuovi"
        >&times;</button>
      </div>
    </div>
    <button
      @click="addImages"
      class="mb-w-full mb-py-1.5 mb-px-3 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-text-xs mb-text-gray-300 hover:mb-bg-gray-600 mb-transition-colors"
    >
      Aggiungi immagini
    </button>
  </div>
</template>

<script setup>
import { useMediaPicker } from '@/composables/useMediaPicker';

const props = defineProps({
  modelValue: { type: Array, default: () => [] },
});
const emit = defineEmits(['update:modelValue']);

const { openGallery } = useMediaPicker();

function addImages() {
  openGallery((newImages) => {
    const current = props.modelValue || [];
    const merged = [...current, ...newImages.map(img => ({ url: img.url, alt: img.alt, id: img.id, caption: '' }))];
    emit('update:modelValue', merged);
  });
}

function removeImage(index) {
  const updated = (props.modelValue || []).filter((_, i) => i !== index);
  emit('update:modelValue', updated);
}

function updateCaption(index, value) {
  const updated = (props.modelValue || []).map((img, i) =>
    i === index ? { ...img, caption: value } : img
  );
  emit('update:modelValue', updated);
}
</script>
