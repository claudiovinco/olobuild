<template>
  <div class="orz-media" :class="'orz-media--' + tipo">
    <ul v-if="voci.length" class="orz-media-voci">
      <li v-for="(m, i) in voci" :key="m.id" class="orz-media-voce">
        <img v-if="tipo !== 'allegati' && m.mini" :src="m.mini" alt="" class="orz-media-img" />
        <span v-else class="orz-media-file">
          <Icona nome="file" />
          <span class="orz-media-nome">{{ m.titolo }}</span>
          <em>{{ [m.est, m.peso].filter(Boolean).join(' · ') }}</em>
        </span>
        <span class="orz-media-azioni">
          <button v-if="i > 0" type="button" class="orz-btn-icona" :title="t('Sposta prima')" :aria-label="t('Sposta prima') + ': ' + m.titolo" :disabled="spento" @click="sposta(i)"><Icona nome="su" :dim="14" /></button>
          <button type="button" class="orz-btn-icona" :title="t('Togli')" :aria-label="t('Togli') + ': ' + m.titolo" :disabled="spento" @click="togli(i)"><Icona nome="x" :dim="14" /></button>
        </span>
      </li>
    </ul>
    <button type="button" class="orz-btn orz-btn--piccolo" :disabled="spento" @click="apri">
      <Icona :nome="tipo === 'allegati' ? 'file' : 'foto'" />
      {{ etichettaPulsante }}
    </button>
  </div>
</template>

<script setup>
// Foto, galleria e allegati di un campo: ID della libreria separati da virgola, nell'ordine scelto.
import { computed } from 'vue';
import { t } from '@/i18n';
import Icona from './Icona.vue';
import { scegli } from './media.js';

const props = defineProps({
  modelValue: { type: String, default: '' },
  tipo: { type: String, required: true },
  media: { type: Object, required: true },
  spento: { type: Boolean, default: false },
});
const emit = defineEmits(['update:modelValue', 'media']);

const ids = computed(() => String(props.modelValue || '').split(',').map((x) => parseInt(x, 10)).filter(Boolean));
const voci = computed(() => ids.value.map((id) => props.media[id] || { id, titolo: '#' + id, mini: '', est: '', peso: '' }));

const etichettaPulsante = computed(() => {
  if (props.tipo === 'allegati') return t('Aggiungi allegati');
  if (props.tipo === 'galleria') return t('Aggiungi foto');
  return ids.value.length ? t('Cambia la foto') : t('Scegli la foto');
});

function scrivi(lista) {
  emit('update:modelValue', lista.join(','));
}
function apri() {
  scegli(props.tipo, (scelti) => {
    emit('media', scelti);
    if (props.tipo === 'immagine') {
      scrivi(scelti.slice(0, 1).map((m) => m.id));
      return;
    }
    const lista = ids.value.slice();
    scelti.forEach((m) => {
      if (!lista.includes(m.id)) lista.push(m.id);
    });
    scrivi(lista);
  });
}
function togli(i) {
  const lista = ids.value.slice();
  lista.splice(i, 1);
  scrivi(lista);
}
function sposta(i) {
  const lista = ids.value.slice();
  [lista[i - 1], lista[i]] = [lista[i], lista[i - 1]];
  scrivi(lista);
}
</script>
