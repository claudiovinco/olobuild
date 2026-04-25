<template>
  <div class="mb-space-y-2">
    <!-- Preview -->
    <div v-if="modelValue" class="mb-relative mb-group">
      <img
        v-if="!isVideo && !isPdf"
        :src="modelValue"
        alt=""
        class="mb-w-full mb-h-24 mb-object-cover mb-rounded-md mb-border mb-border-gray-600"
      />
      <div
        v-else-if="isPdf"
        class="mb-w-full mb-h-24 mb-rounded-md mb-border mb-border-gray-600 mb-bg-gray-800 mb-flex mb-flex-col mb-items-center mb-justify-center mb-text-gray-400"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="mb-w-8 mb-h-8 mb-text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
        </svg>
        <span class="mb-mt-1 mb-text-xs mb-truncate mb-max-w-[180px]">{{ fileName }}</span>
      </div>
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
        :title="t('Rimuovi media')"
      >{{ t('&times;') }}</button>
    </div>

    <!-- Primary picker (auto-filtered by detected type) -->
    <button
      @click="pickMedia(detectedType)"
      class="mb-w-full mb-py-1.5 mb-px-3 mb-bg-primary-600 mb-border mb-border-primary-600 mb-rounded-md mb-text-xs mb-text-white hover:mb-bg-primary-700 mb-transition-colors"
    >
      {{ modelValue ? primaryLabel.change : primaryLabel.select }}
    </button>

    <!-- Other types (when detected type isn't 'all') -->
    <div v-if="otherTabs.length" class="mb-grid mb-gap-1" :style="`grid-template-columns: repeat(${otherTabs.length}, 1fr)`">
      <button v-for="opt in otherTabs" :key="opt.type"
        @click="pickMedia(opt.type)"
        class="mb-py-1 mb-px-1 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded-md mb-text-[10px] mb-text-gray-400 hover:mb-bg-gray-600 mb-transition-colors mb-flex mb-items-center mb-justify-center mb-gap-1"
        :title="opt.title">
        <span class="mb-text-sm mb-leading-none">{{ opt.icon }}</span>
        <span>{{ opt.label }}</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
import { useToast } from '../../../composables/useToast.js';

const props = defineProps({
  modelValue: { type: String, default: '' },
  // Auto-filter: 'image' | 'video' | 'audio' | 'application/pdf' | 'all'
  // (passed by InspectorField, derived from the field's key)
  accept: { type: String, default: 'all' },
});
const emit = defineEmits(['update:modelValue', 'update:attachmentId']);

const VIDEO_EXTS = /\.(mp4|webm|ogg)(\?.*)?$/i;
const PDF_EXT = /\.pdf(\?.*)?$/i;

const isVideo = computed(() => VIDEO_EXTS.test(props.modelValue));
const isPdf = computed(() => PDF_EXT.test(props.modelValue));

const fileName = computed(() => {
  if (!props.modelValue) return '';
  return props.modelValue.split('/').pop().split('?')[0];
});

const allTabs = [
  { type: 'image', icon: '🖼️', label: t('Immagine'), title: t('Filtra solo immagini'), select: t('Seleziona immagine'), change: t('Cambia immagine') },
  { type: 'video', icon: '🎬', label: t('Video'),    title: t('Filtra solo video'),    select: t('Seleziona video'),    change: t('Cambia video') },
  { type: 'audio', icon: '🎵', label: t('Audio'),    title: t('Filtra solo audio'),    select: t('Seleziona audio'),    change: t('Cambia audio') },
  { type: 'application/pdf', icon: '📄', label: t('PDF'), title: t('Filtra solo PDF'), select: t('Seleziona PDF'), change: t('Cambia PDF') },
  { type: 'all',   icon: '📁', label: t('Tutto'),    title: t('Tutti i media (nessun filtro)'), select: t('Seleziona media'), change: t('Cambia media') },
];

// Detected type: comes from prop (set by InspectorField from field.accept or field.key heuristic)
const detectedType = computed(() => {
  const a = (props.accept || 'all').toLowerCase();
  return allTabs.some(t => t.type === a) ? a : 'all';
});

const primaryLabel = computed(() => {
  return allTabs.find(t => t.type === detectedType.value) || allTabs[allTabs.length - 1];
});

// Show the OTHER tabs (so user can override): if detectedType is specific, hide it from secondary list
const otherTabs = computed(() => {
  if (detectedType.value === 'all') return [];  // primary is "all" already, no extra tabs needed
  return allTabs.filter(t => t.type !== detectedType.value);
});

function pickMedia(type = 'all') {
  const toast = useToast();
  if (!window.wp || !window.wp.media) {
    toast.error(t('Libreria Media di WordPress non disponibile.'));
    return;
  }
  const tab = allTabs.find(t => t.type === type) || allTabs[allTabs.length - 1];
  const frameOpts = {
    title: tab.select,
    button: { text: t('Usa questo media') },
    multiple: false,
  };
  if (type !== 'all') frameOpts.library = { type };  // wp.media accepts mime ('application/pdf') or type ('image','video','audio')
  const frame = wp.media(frameOpts);
  frame.on('select', () => {
    const attachment = frame.state().get('selection').first().toJSON();
    emit('update:modelValue', attachment.url);
    emit('update:attachmentId', attachment.id);
  });
  frame.open();
}
</script>
