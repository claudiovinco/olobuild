<template>
  <div class="mb-space-y-2">
    <!-- Preview -->
    <div v-if="modelValue" class="mb-relative mb-group">
      <img
        v-if="!isVideo"
        :src="modelValue"
        alt=""
        class="mb-w-full mb-h-24 mb-object-cover mb-rounded-md mb-border mb-border-gray-600"
      />
      <div
        v-else
        class="mb-w-full mb-h-24 mb-rounded-md mb-border mb-border-gray-600 mb-bg-gray-800 mb-flex mb-items-center mb-justify-center mb-text-gray-400"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="mb-w-8 mb-h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
        </svg>
        <span class="mb-ml-2 mb-text-xs mb-truncate mb-max-w-[120px]">{{ fileName }}</span>
      </div>
      <button
        @click="$emit('update:modelValue', ''); $emit('update:attachmentId', 0)"
        class="mb-absolute mb-top-1 mb-right-1 mb-bg-red-600 mb-text-white mb-rounded-full mb-w-5 mb-h-5 mb-text-xs mb-flex mb-items-center mb-justify-center mb-opacity-0 group-hover:mb-opacity-100 mb-transition-opacity"
        title="Rimuovi media"
      >&times;</button>
    </div>

    <!-- Single picker button -->
    <button
      @click="pickMedia"
      class="mb-w-full mb-py-1.5 mb-px-3 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-text-xs mb-text-gray-300 hover:mb-bg-gray-600 mb-transition-colors"
    >
      {{ modelValue ? 'Cambia media' : 'Seleziona immagine / video' }}
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  modelValue: { type: String, default: '' },
});
const emit = defineEmits(['update:modelValue', 'update:attachmentId']);

const VIDEO_EXTS = /\.(mp4|webm|ogg)(\?.*)?$/i;

const isVideo = computed(() => VIDEO_EXTS.test(props.modelValue));

const fileName = computed(() => {
  if (!props.modelValue) return '';
  return props.modelValue.split('/').pop().split('?')[0];
});

function pickMedia() {
  if (!window.wp || !window.wp.media) {
    alert('Libreria Media di WordPress non disponibile.');
    return;
  }
  const frame = wp.media({
    title: 'Seleziona immagine o video',
    button: { text: 'Usa questo media' },
    multiple: false,
  });
  frame.on('select', () => {
    const attachment = frame.state().get('selection').first().toJSON();
    emit('update:modelValue', attachment.url);
    emit('update:attachmentId', attachment.id);
  });
  frame.open();
}
</script>
