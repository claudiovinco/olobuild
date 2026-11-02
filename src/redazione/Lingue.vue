<template>
  <nav class="orz-lingue" :aria-label="t('Lingue')">
    <span class="orz-lingue-nome">
      <Icona nome="lingue" :dim="15" /> {{ t('Lingue') }}
      <InfoTip :titolo="t('Lingue')" :testo="t('Ogni lingua ha la sua traduzione: titolo, riassunto, testo e campi di testo. Date, foto, video, link, scelte, categorie e immagine in evidenza sono quelli dell\'originale: si cambiano lì e valgono per tutte le lingue. Una traduzione compare sul sito quando la pubblichi; finché non c\'è, in quella lingua si vede l\'originale.')" />
    </span>
    <button
      type="button"
      class="orz-lingua"
      :class="{ on: !lingua }"
      :aria-current="!lingua ? 'page' : undefined"
      :disabled="!!aprendo"
      @click="apriOriginale"
    >
      <span class="orz-lingua-nome">{{ lingue.principale.nome }}</span>
      <span class="orz-lingua-stato">{{ t('originale') }}</span>
    </button>
    <button
      v-for="l in voci"
      :key="l.codice"
      type="button"
      class="orz-lingua"
      :class="[{ on: l.codice === lingua }, 'orz-lingua--' + (l.id ? l.stato : 'manca')]"
      :aria-current="l.codice === lingua ? 'page' : undefined"
      :disabled="!!aprendo || (!l.id && !puoCreare)"
      @click="apri(l)"
    >
      <span class="orz-lingua-nome">{{ l.nome }}</span>
      <span class="orz-lingua-stato">{{ aprendo === l.codice ? t('Preparo…') : (l.id ? nomeStato(l.stato) : t('da tradurre')) }}</span>
    </button>
  </nav>
</template>

<script setup>
/**
 * La barra delle lingue della scheda (OLOlang, Olobuild_Redazione_Lingue): l'originale e una voce per lingua, con lo
 * stato della traduzione. Una lingua senza traduzione la crea (in bozza, col testo dell'originale da tradurre) e la apre.
 */
import { ref, computed } from 'vue';
import { t } from '@/i18n';
import InfoTip from '@/components/Builder/InfoTip.vue';
import Icona from './Icona.vue';
import { chiama } from './api.js';
import { nomeStato } from './stati.js';

const props = defineProps({
  lingue: { type: Object, required: true },
  // La lingua della scheda aperta ('' = l'originale).
  lingua: { type: String, default: '' },
  // L'id dell'originale e le sue traduzioni [ { codice, nome, id, stato } ].
  originale: { type: Number, required: true },
  traduzioni: { type: Array, default: () => [] },
  puoCreare: { type: Boolean, default: true },
});
const emit = defineEmits(['apri', 'errore']);

const aprendo = ref('');

const voci = computed(() => props.lingue.altre.map((l) => {
  const tr = props.traduzioni.find((x) => x.codice === l.codice);
  return { ...l, id: tr ? tr.id : 0, stato: tr ? tr.stato : '' };
}));

function apriOriginale() {
  if (props.lingua) emit('apri', props.originale);
}

async function apri(l) {
  if (l.codice === props.lingua) return;
  if (l.id) {
    emit('apri', l.id);
    return;
  }
  aprendo.value = l.codice;
  try {
    const r = await chiama('redazione/' + props.originale + '/traduzione', { metodo: 'POST', corpo: { lingua: l.codice } });
    emit('apri', r.id);
  } catch (e) {
    emit('errore', e.message);
  } finally {
    aprendo.value = '';
  }
}
</script>
