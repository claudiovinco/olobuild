<template>
  <div class="orz-video">
    <!-- Un video della libreria: l'anteprima che si riproduce e il file, da togliere o cambiare -->
    <template v-if="file">
      <video v-if="file.url" class="orz-video-anteprima" :src="file.url" controls preload="metadata"></video>
      <span class="orz-media-file">
        <Icona nome="video" />
        <span class="orz-media-nome">{{ file.titolo }}</span>
        <em>{{ [file.est, file.peso].filter(Boolean).join(' · ') }}</em>
        <button type="button" class="orz-btn-icona" :title="t('Togli')" :aria-label="t('Togli') + ': ' + file.titolo" :disabled="spento" @click="manda('')"><Icona nome="x" :dim="14" /></button>
      </span>
    </template>
    <!-- Un link: YouTube, Vimeo o un file su un altro sito -->
    <template v-else>
      <input :id="id" type="url" :value="modelValue" :disabled="spento" placeholder="https://www.youtube.com/watch?v=…" @input="manda($event.target.value.trim())" />
      <span v-if="piattaforma" class="orz-video-piattaforma" :class="{ 'is-ignota': !riconosciuto }">
        <img v-if="miniatura" :src="miniatura" alt="" class="orz-video-mini" />
        <Icona v-else nome="video" />
        {{ piattaforma }}
      </span>
    </template>
    <button type="button" class="orz-btn orz-btn--piccolo" :disabled="spento" @click="apri">
      <Icona nome="video" />
      {{ file ? t('Cambia il video') : t('Scegli dalla libreria') }}
    </button>
  </div>
</template>

<script setup>
// Un campo Video: il numero di un video della libreria oppure un link (YouTube, Vimeo, un file .mp4). Sul sito la tile
// Video lo mostra col collegamento dinamico «Campi dei contenuti» (Olobuild_Tipi_Contenuto::indirizzo_video).
import { computed } from 'vue';
import { t } from '@/i18n';
import Icona from './Icona.vue';
import { scegli } from './media.js';

const props = defineProps({
  id: { type: String, default: '' },
  modelValue: { type: String, default: '' },
  media: { type: Object, required: true },
  spento: { type: Boolean, default: false },
});
const emit = defineEmits(['update:modelValue', 'media']);

const file = computed(() => {
  const v = String(props.modelValue || '');
  if (!/^\d+$/.test(v)) return null;
  return props.media[v] || { id: Number(v), titolo: '#' + v, url: '', est: '', peso: '' };
});

const youtube = computed(() => (String(props.modelValue || '').match(/(?:youtube\.com\/(?:watch\?v=|shorts\/|embed\/)|youtu\.be\/)([\w-]{6,})/i) || [])[1] || '');
const riconosciuto = computed(() => /youtube\.com|youtu\.be|vimeo\.com|\.(mp4|m4v|mov|webm|ogv|ogg)(\?|$)/i.test(props.modelValue || ''));
const piattaforma = computed(() => {
  const v = String(props.modelValue || '');
  if (!v) return '';
  if (youtube.value || /youtube\.com|youtu\.be/i.test(v)) return 'YouTube';
  if (/vimeo\.com/i.test(v)) return 'Vimeo';
  if (riconosciuto.value) return t('File video');
  return t('Link non riconosciuto');
});
const miniatura = computed(() => (youtube.value ? 'https://i.ytimg.com/vi/' + youtube.value + '/mqdefault.jpg' : ''));

function manda(v) {
  emit('update:modelValue', v == null ? '' : String(v));
}
function apri() {
  scegli('video', (scelti) => {
    if (!scelti.length) return;
    emit('media', scelti);
    manda(scelti[0].id);
  });
}
</script>
