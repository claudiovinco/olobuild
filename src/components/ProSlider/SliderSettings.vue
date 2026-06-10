<template>
  <div class="mb-bg-gray-900 mb-border-t mb-border-gray-700 mb-px-3 mb-py-2">
    <!-- Global background (collapsible) -->
    <div v-if="showGlobalBg" class="mb-mb-2 mb-pb-2 mb-border-b mb-border-gray-700">
      <div class="mb-flex mb-items-center mb-justify-between mb-mb-2">
        <span class="mb-text-[10px] mb-text-gray-400 mb-font-semibold mb-uppercase">Sfondo globale</span>
        <button @click="showGlobalBg = false" class="mb-text-gray-500 mb-text-xs">&times;</button>
      </div>
      <div class="mb-flex mb-gap-3 mb-flex-wrap mb-items-end">
        <div class="mb-w-32">
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Tipo</label>
          <FieldSelect ui="dropdown" size="compact" theme="dark" :model-value="globalBg.type" :options="OPTS_BG_TYPE" @update:model-value="updateGlobalBg('type', $event)" />
        </div>
        <div v-if="globalBg.type === 'color'" class="mb-w-48">
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Colore</label>
          <FieldColor :modelValue="globalBg.color" @update:modelValue="updateGlobalBg('color', $event)" />
        </div>
        <div v-if="globalBg.type === 'image'" class="mb-flex mb-gap-2 mb-items-end">
          <div>
            <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Immagine</label>
            <div class="mb-flex mb-gap-1">
              <input :value="globalBg.image" @input="updateGlobalBg('image', $event.target.value)" class="mps-num-input mb-w-48" placeholder="URL" />
              <button @click="pickGlobalBgImage" class="mb-px-2 mb-bg-gray-600 mb-rounded mb-text-[10px] mb-text-gray-300 hover:mb-bg-gray-500">Sfoglia</button>
            </div>
          </div>
        </div>
        <div v-if="globalBg.type === 'video'">
          <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">URL Video</label>
          <div class="mb-flex mb-gap-1">
            <input :value="globalBg.video" @input="updateGlobalBg('video', $event.target.value)" class="mps-num-input mb-w-52" placeholder="mp4 o URL YouTube" />
            <button @click="pickGlobalBgVideo" class="mb-px-2 mb-bg-gray-600 mb-rounded mb-text-[10px] mb-text-gray-300 hover:mb-bg-gray-500">Sfoglia</button>
          </div>
        </div>
        <template v-if="globalBg.type === 'gradient'">
          <div class="mb-w-48">
            <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Da</label>
            <FieldColor :modelValue="globalBg.gradientFrom" @update:modelValue="updateGlobalBg('gradientFrom', $event)" />
          </div>
          <div class="mb-w-48">
            <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">A</label>
            <FieldColor :modelValue="globalBg.gradientTo" @update:modelValue="updateGlobalBg('gradientTo', $event)" />
          </div>
          <div>
            <label class="mb-block mb-text-[10px] mb-text-gray-400 mb-mb-0.5">Angolo</label>
            <input type="number" :value="globalBg.gradientAngle" @input="updateGlobalBg('gradientAngle', parseInt($event.target.value))" min="0" max="360" class="mps-num-input mb-w-14" />
          </div>
        </template>
      </div>
    </div>
    <div v-else>
      <button @click="showGlobalBg = true" class="mb-text-[10px] mb-text-gray-400 hover:mb-text-gray-300">
        Sfondo globale &darr;
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import FieldColor from '../Builder/fields/FieldColor.vue';
import FieldSelect from '../Builder/fields/FieldSelect.vue';

// Option array per FieldSelect (stessi value dei vecchi <option>: dati salvati invariati)
const OPTS_BG_TYPE = [
  { value: 'color', label: 'Colore' },
  { value: 'image', label: 'Immagine' },
  { value: 'video', label: 'Video' },
  { value: 'gradient', label: 'Gradiente' },
];

const props = defineProps({
  settings: { type: Object, required: true },
});

const emit = defineEmits(['update']);

const showGlobalBg = ref(false);

const globalBg = computed(() => props.settings.globalBackground || {
  type: 'color', color: '#1e293b', image: '', video: '',
  gradientFrom: '#1e293b', gradientTo: '#0f172a', gradientAngle: 180,
});

function updateGlobalBg(key, val) {
  const current = JSON.parse(JSON.stringify(globalBg.value));
  current[key] = val;
  emit('update', 'globalBackground', current);
}

function pickGlobalBgImage() {
  if (!window.wp?.media) return;
  const frame = wp.media({ title: 'Seleziona immagine sfondo globale', multiple: false, library: { type: 'image' } });
  frame.on('select', () => {
    const url = frame.state().get('selection').first().toJSON().url;
    updateGlobalBg('image', url);
  });
  frame.open();
}

function pickGlobalBgVideo() {
  if (!window.wp?.media) return;
  const frame = wp.media({ title: 'Seleziona video sfondo globale', multiple: false, library: { type: 'video' } });
  frame.on('select', () => {
    const url = frame.state().get('selection').first().toJSON().url;
    updateGlobalBg('video', url);
  });
  frame.open();
}
</script>

<style scoped>
.mps-num-input {
  background: #ffffff;
  border: 1px solid #d1d5db;
  border-radius: 0.25rem;
  padding: 2px 6px;
  font-size: 11px;
  color: #111827;
  text-align: center;
}
</style>
