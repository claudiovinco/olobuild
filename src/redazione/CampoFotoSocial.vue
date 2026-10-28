<template>
  <div class="orz-social">
    <div class="orz-social-sorgente">
      <img v-if="sorgente && media[sorgente] && media[sorgente].mini" :src="media[sorgente].mini" alt="" />
      <span v-else class="orz-social-vuoto"><Icona nome="foto" :dim="20" /></span>
      <button type="button" class="orz-btn orz-btn--piccolo" :disabled="spento" @click="scegliFoto">
        {{ sorgente ? t('Cambia la foto') : t('Scegli la foto') }}
      </button>
    </div>
    <ul class="orz-social-formati">
      <li v-for="f in formatiCampo" :key="f.chiave" class="orz-social-formato">
        <span class="orz-social-cornice" :style="{ aspectRatio: f.w + ' / ' + f.h }">
          <img v-if="urlFormato(f.chiave)" :src="urlFormato(f.chiave)" alt="" />
        </span>
        <span class="orz-social-nome">{{ f.nome }}</span>
        <span class="orz-social-stato">{{ dati[f.chiave] ? t('Inquadrata') : t('Centro della foto') }}</span>
        <button type="button" class="orz-btn orz-btn--piccolo orz-btn--quieto" :disabled="spento || !sorgente" @click="inquadraFormato(f)">
          <Icona nome="ritaglio" :dim="14" /> {{ t('Inquadra') }}
        </button>
      </li>
    </ul>
  </div>
</template>

<script setup>
/**
 * «Foto per i social»: una foto e un ritaglio per formato (1,91:1, 4:5, 1:1, 9:16), salvati come JSON
 * { sorgente: id, '191': id, … } come nella scheda di WordPress (Olobuild_Tipi_Contenuto::pulisci()).
 */
import { computed } from 'vue';
import { t } from '@/i18n';
import Icona from './Icona.vue';
import { scegli, inquadra } from './media.js';

const props = defineProps({
  modelValue: { type: String, default: '' },
  formati: { type: Array, default: () => [] },
  media: { type: Object, required: true },
  spento: { type: Boolean, default: false },
});
const emit = defineEmits(['update:modelValue', 'media']);

const FORMATI = (window.oloData && window.oloData.redazione && window.oloData.redazione.formati) || {};

const dati = computed(() => {
  try {
    const d = JSON.parse(props.modelValue || '{}');
    return d && typeof d === 'object' ? d : {};
  } catch (e) {
    return {};
  }
});
const sorgente = computed(() => parseInt(dati.value.sorgente, 10) || 0);
const formatiCampo = computed(() => props.formati
  .filter((k) => FORMATI[k])
  .map((k) => ({ chiave: String(k), ...FORMATI[k] })));

function urlFormato(k) {
  const id = parseInt(dati.value[k], 10) || sorgente.value;
  const m = id && props.media[id];
  return m ? (m.mini || m.url) : '';
}

function scegliFoto() {
  scegli('immagine', (scelti) => {
    const m = scelti[0];
    if (!m) return;
    emit('media', [m]);
    // Una foto nuova: i ritagli della vecchia non valgono più.
    emit('update:modelValue', JSON.stringify({ sorgente: m.id }));
  });
}

function inquadraFormato(f) {
  inquadra(sorgente.value, f, (img) => {
    emit('media', [{ id: img.id, titolo: '', url: img.url, mini: img.url, est: '', peso: '' }]);
    emit('update:modelValue', JSON.stringify({ ...dati.value, [f.chiave]: img.id }));
  });
}
</script>
