<template>
  <!-- Più livelli scelti insieme (1.4.1428): quello che si fa a un gruppo di livelli in un colpo solo. Il meglio degli
       altri editor: allinea e distribuisci (tutti), entrate a cascata (Slider Revolution), la transizione di una scelta
       come quella fra due varianti (Framer), etichette colorate e «messi da parte» (After Effects), gruppo (Smart Slider). -->
  <div class="mps-mp">
    <div class="mps-mp-testa">
      <span class="mps-mp-titolo">{{ t('%d livelli scelti').replace('%d', layers.length) }}</span>
      <button type="button" class="mps-mp-link" @click="$emit('annulla')">{{ t('Annulla la scelta') }}</button>
    </div>
    <p class="mps-mp-nomi" :title="nomi">{{ nomi }}</p>
    <p class="mps-mp-aiuto">{{ t('Trascinane uno sulla tela per spostarli tutti; con le frecce della tastiera si spostano di poco (con Maiusc di più).') }}</p>

    <section class="mps-mp-sez">
      <p class="mps-mp-sotto">{{ t('Posizione') }}</p>
      <!-- 1.4.1475: anche al centro e a destra, al centro e in basso (prima solo a sinistra e in alto) -->
      <div class="mps-mp-fila mps-mp-fila--3" role="group" :aria-label="t('Allinea in orizzontale')">
        <button type="button" class="mps-mp-btn" @click="$emit('allinea', 'sinistra')" :title="t('Allinea a sinistra')"><span class="mps-mp-ico" aria-hidden="true" v-html="ico('align-start-vertical')"></span>{{ t('A sinistra') }}</button>
        <button type="button" class="mps-mp-btn" @click="$emit('allinea', 'centro-o')" :title="t('Allinea al centro in orizzontale')"><span class="mps-mp-ico" aria-hidden="true" v-html="ico('align-center-vertical')"></span>{{ t('Al centro') }}</button>
        <button type="button" class="mps-mp-btn" @click="$emit('allinea', 'destra')" :title="t('Allinea a destra')"><span class="mps-mp-ico" aria-hidden="true" v-html="ico('align-end-vertical')"></span>{{ t('A destra') }}</button>
      </div>
      <div class="mps-mp-fila mps-mp-fila--3" role="group" :aria-label="t('Allinea in verticale')">
        <button type="button" class="mps-mp-btn" @click="$emit('allinea', 'alto')" :title="t('Allinea in alto')"><span class="mps-mp-ico" aria-hidden="true" v-html="ico('align-start-horizontal')"></span>{{ t('In alto') }}</button>
        <button type="button" class="mps-mp-btn" @click="$emit('allinea', 'centro-v')" :title="t('Allinea al centro in verticale')"><span class="mps-mp-ico" aria-hidden="true" v-html="ico('align-center-horizontal')"></span>{{ t('Al centro') }}</button>
        <button type="button" class="mps-mp-btn" @click="$emit('allinea', 'basso')" :title="t('Allinea in basso')"><span class="mps-mp-ico" aria-hidden="true" v-html="ico('align-end-horizontal')"></span>{{ t('In basso') }}</button>
      </div>
      <div class="mps-mp-fila">
        <button type="button" class="mps-mp-btn" :disabled="layers.length < 3" @click="$emit('distribuisci', 'x')" :title="t('Stessa distanza in orizzontale (almeno tre livelli)')"><span class="mps-mp-ico" aria-hidden="true" v-html="ico('align-horizontal-space-between')"></span>{{ t('Distanza uguale ↔') }}</button>
        <button type="button" class="mps-mp-btn" :disabled="layers.length < 3" @click="$emit('distribuisci', 'y')" :title="t('Stessa distanza in verticale (almeno tre livelli)')"><span class="mps-mp-ico" aria-hidden="true" v-html="ico('align-vertical-space-between')"></span>{{ t('Distanza uguale ↕') }}</button>
      </div>
    </section>

    <section class="mps-mp-sez">
      <p class="mps-mp-sotto">{{ t('Entrata, a cascata') }}</p>
      <div class="mps-mp-fila">
        <FieldSelect ui="dropdown" size="compact" theme="dark" class="mb-flex-1" :model-value="entrata" :options="OPTS_ENTRATA" @update:model-value="entrata = $event" />
      </div>
      <div class="mps-mp-fila mps-mp-fila--num">
        <label class="mps-mp-et">{{ t('Comincia dopo') }}</label>
        <NumberScrubber theme="dark" :modelValue="base" :min="0" :max="5000" :step="50" :defaultValue="200" emitAs="number" unit="ms" :ariaLabel="t('Comincia dopo')" @update:modelValue="base = $event" />
        <label class="mps-mp-et">{{ t('uno ogni') }}</label>
        <NumberScrubber theme="dark" :modelValue="cascata" :min="0" :max="2000" :step="20" :defaultValue="120" emitAs="number" unit="ms" :ariaLabel="t('uno ogni')" @update:modelValue="cascata = $event" />
      </div>
      <button type="button" class="mps-mp-btn mps-mp-btn--pieno" @click="$emit('entrata', { animIn: entrata, base, cascata })">{{ t('Applica dall’alto in basso') }}</button>
    </section>

    <section v-if="conStato" class="mps-mp-sez">
      <p class="mps-mp-sotto mb-flex mb-items-center mb-gap-1">{{ t('Come compaiono nella loro scena') }}<InfoTip :testo="t('Per i livelli che si vedono solo in uno stato (le scelte): l’effetto con cui compaiono quando lo stato cambia, la durata e la cascata. Come la transizione fra due varianti.')" :titolo="t('Come compaiono nella loro scena')" /></p>
      <div class="mps-mp-fila">
        <FieldSelect ui="dropdown" size="compact" theme="dark" class="mb-flex-1" :model-value="effetto" :options="OPTS_EFFETTO" @update:model-value="effetto = $event" />
      </div>
      <div class="mps-mp-fila mps-mp-fila--num">
        <label class="mps-mp-et">{{ t('Durata') }}</label>
        <NumberScrubber theme="dark" :modelValue="durata" :min="0" :max="4000" :step="50" :defaultValue="450" emitAs="number" unit="ms" :ariaLabel="t('Durata')" @update:modelValue="durata = $event" />
        <label class="mps-mp-et">{{ t('uno ogni') }}</label>
        <NumberScrubber theme="dark" :modelValue="cascataStato" :min="0" :max="2000" :step="20" :defaultValue="80" emitAs="number" unit="ms" :ariaLabel="t('uno ogni')" @update:modelValue="cascataStato = $event" />
      </div>
      <button type="button" class="mps-mp-btn mps-mp-btn--pieno" @click="$emit('transizione', { effetto, durata, cascata: cascataStato })">{{ t('Applica alle scene') }}</button>
    </section>

    <section class="mps-mp-sez">
      <p class="mps-mp-sotto">{{ t('Ordine') }}</p>
      <div class="mps-mp-fila">
        <input v-model="nomeGruppo" class="mps-input mb-flex-1" :placeholder="t('Nome del gruppo')" list="mps-nomi-gruppi-multi" @keydown.enter.prevent="nomeGruppo.trim() && $emit('gruppo', nomeGruppo.trim())" />
        <datalist id="mps-nomi-gruppi-multi"><option v-for="n in nomiGruppi" :key="n" :value="n" /></datalist>
        <button type="button" class="mps-mp-btn" :disabled="!nomeGruppo.trim()" @click="$emit('gruppo', nomeGruppo.trim())"><span class="mps-mp-ico" aria-hidden="true" v-html="ico('folder-input')"></span>{{ t('Metti nel gruppo') }}</button>
      </div>
      <div class="mps-mp-fila mps-mp-etichette" role="group" :aria-label="t('Etichetta colorata')">
        <span class="mps-mp-et">{{ t('Etichetta') }}</span>
        <button v-for="e in ETICHETTE" :key="e.k" type="button" class="mps-mp-colore" :style="{ '--c': e.c }" :class="{ 'is-on': etichettaComune === e.k }"
                @click="$emit('etichetta', etichettaComune === e.k ? '' : e.k)" :title="e.nome" :aria-label="e.nome" :aria-pressed="etichettaComune === e.k ? 'true' : 'false'"></button>
      </div>
      <div class="mps-mp-fila">
        <button type="button" class="mps-mp-btn" @click="$emit('da-parte', !tuttiDaParte)" :title="t('Come i livelli «shy» di After Effects: spariscono dall’elenco dei livelli ma restano nella slide')">
          <span class="mps-mp-ico" aria-hidden="true" v-html="ico('archive')"></span>{{ tuttiDaParte ? t('Rimetti nell’elenco') : t('Metti da parte') }}
        </button>
      </div>
    </section>

    <section class="mps-mp-sez">
      <div class="mps-mp-fila">
        <button type="button" class="mps-mp-btn" @click="$emit('duplica')"><span class="mps-mp-ico" aria-hidden="true" v-html="ico('copy')"></span>{{ t('Duplica') }}</button>
        <button v-if="!confermaVia" type="button" class="mps-mp-btn mps-mp-via" @click="confermaVia = true"><span class="mps-mp-ico" aria-hidden="true" v-html="ico('trash-2')"></span>{{ t('Elimina') }}</button>
        <template v-else>
          <button type="button" class="mps-mp-btn mps-mp-via is-conferma" @click="confermaVia = false; $emit('elimina')">{{ t('Elimina %d livelli').replace('%d', layers.length) }}</button>
          <button type="button" class="mps-mp-btn" @click="confermaVia = false">{{ t('No') }}</button>
        </template>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { t } from '@/i18n';
import InfoTip from '../Builder/InfoTip.vue';
import FieldSelect from '../Builder/fields/FieldSelect.vue';
import NumberScrubber from '../Builder/fields/NumberScrubber.vue';
import lucideIcons from './lucideIconsSvg.js';
import { nomeLivello, gruppiLivelli, nomeGruppo as nomeDelGruppo, ETICHETTE } from './livelloUtils.js';

const props = defineProps({
  layers: { type: Array, default: () => [] },
  slideLayers: { type: Array, default: () => [] },
});
defineEmits(['annulla', 'allinea', 'distribuisci', 'entrata', 'transizione', 'gruppo', 'etichetta', 'da-parte', 'duplica', 'elimina']);

const ico = (k) => lucideIcons[k] || '';
const nomi = computed(() => props.layers.map((l) => nomeLivello(l)).join(' · '));
const conStato = computed(() => props.layers.some((l) => l.stato?.nome));
const tuttiDaParte = computed(() => props.layers.length > 0 && props.layers.every((l) => l.daParte));
const etichettaComune = computed(() => {
  const e = props.layers[0]?.etichetta || '';
  return props.layers.every((l) => (l.etichetta || '') === e) ? e : null;
});
const nomiGruppi = computed(() => [...new Set(gruppiLivelli(props.slideLayers).map((g) => nomeDelGruppo(g)))]);

const entrata = ref(props.layers[0]?.animIn || 'fadeInUp');
const base = ref(200);
const cascata = ref(120);
const effetto = ref(props.layers.find((l) => l.stato?.nome)?.stato?.effetto || 'su');
const durata = ref(450);
const cascataStato = ref(80);
const nomeGruppo = ref('');
const confermaVia = ref(false);

const OPTS_ENTRATA = [
  { value: 'fadeIn', label: t('Dissolvenza') },
  { value: 'fadeInUp', label: t('Sale dal basso') },
  { value: 'fadeInDown', label: t('Scende dall’alto') },
  { value: 'fadeInLeft', label: t('Da sinistra') },
  { value: 'fadeInRight', label: t('Da destra') },
  { value: 'zoomIn', label: t('Si ingrandisce') },
  { value: 'maskFromBottom', label: t('Si scopre dal basso') },
  { value: 'none', label: t('Nessuna: già al suo posto') },
];
const OPTS_EFFETTO = [
  { value: 'fade', label: t('Dissolvenza') },
  { value: 'su', label: t('Sale dal basso') },
  { value: 'giu', label: t('Scende dall’alto') },
  { value: 'sinistra', label: t('Da sinistra') },
  { value: 'destra', label: t('Da destra') },
  { value: 'zoom', label: t('Si ingrandisce') },
  { value: 'nessuno', label: t('Subito, senza effetto') },
];
</script>

<style scoped>
.mps-mp { background: #111827; border-left: 1px solid #374151; padding: 10px; overflow-y: auto; display: flex; flex-direction: column; gap: 6px; }
.mps-mp-testa { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
.mps-mp-titolo { font-size: 12px; font-weight: 600; color: #e5e7eb; }
.mps-mp-link { font: inherit; font-size: 10.5px; color: #9ca3af; background: transparent; border: 0; cursor: pointer; padding: 0; }
.mps-mp-link:hover { color: #fff; }
.mps-mp-nomi { margin: 0; font-size: 10.5px; color: #9ca3af; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.mps-mp-aiuto { margin: 0; font-size: 10.5px; color: #6b7280; line-height: 1.45; }
.mps-mp-sez { border-top: 1px solid #1f2937; padding-top: 8px; display: flex; flex-direction: column; gap: 6px; }
.mps-mp-sotto { margin: 0; font-size: 10px; font-weight: 600; letter-spacing: .04em; text-transform: uppercase; color: #9ca3af; }
.mps-mp-fila { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
/* 1.4.1446: numeri compatti (cursore al clic) e a capo se non ci stanno: coi cursori sempre aperti la riga
   sbordava, i cursori coprivano «Comincia dopo» e il secondo valore finiva fuori dal pannello */
.mps-mp-fila--num { flex-wrap: wrap; row-gap: 4px; }
/* 1.4.1475: tre pulsanti d'allineamento per riga, stretti */
.mps-mp-fila--3 { flex-wrap: nowrap; gap: 4px; }
.mps-mp-fila--3 > .mps-mp-btn { flex: 1 1 0; min-width: 0; justify-content: center; padding: 5px 3px; font-size: 10.5px; gap: 3px; white-space: nowrap; }
.mps-mp-fila--num > * { min-width: 0; }
.mps-mp-et { font-size: 10.5px; color: #9ca3af; white-space: nowrap; }
.mps-mp-btn { font: inherit; display: inline-flex; align-items: center; gap: 5px; padding: 5px 8px; font-size: 11px; color: #e5e7eb; background: #1f2937; border: 1px solid #374151; border-radius: 5px; cursor: pointer; }
.mps-mp-btn:hover:not(:disabled) { border-color: #6b7280; }
.mps-mp-btn:disabled { opacity: .45; cursor: default; }
.mps-mp-btn--pieno { justify-content: center; background: var(--olo-color-primary, #e1474f); border-color: var(--olo-color-primary, #e1474f); color: var(--olo-color-primary-contrast, #fff); font-weight: 600; }
.mps-mp-via:hover:not(:disabled), .mps-mp-via.is-conferma { border-color: #f87171; color: #fecaca; }
.mps-mp-ico { display: inline-flex; }
.mps-mp-ico :deep(svg) { width: 13px; height: 13px; }
.mps-mp-colore { width: 18px; height: 18px; border-radius: 50%; border: 2px solid transparent; background: var(--c); cursor: pointer; padding: 0; }
.mps-mp-colore.is-on { border-color: #fff; }
.mps-mp-link:focus-visible, .mps-mp-btn:focus-visible, .mps-mp-colore:focus-visible { outline: 2px solid var(--olo-color-primary, #e1474f); outline-offset: 2px; }
/* 1.4.1447: la casella «Nome del gruppo» come le altre dell'editor (lo stile .mps-input è scoped nei componenti che lo
   usano: qui mancava e la casella usciva bianca, senza bordo né angoli). */
.mps-input { min-width: 0; background: #ffffff; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 4px 8px; font-size: 0.75rem; color: #111827; }
.mps-input:focus { outline: none; border-color: var(--olo-color-primary, #e1474f); }
</style>
