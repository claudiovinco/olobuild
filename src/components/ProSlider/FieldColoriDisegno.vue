<template>
  <!-- «Colori del disegno» (1.4.1433): ogni colore del file SVG si sostituisce con uno della palette. Prima i disegni dei
       temi (il cruscotto di Cric, la cassaforte di Combinazione) avevano i colori fissi dentro il file. -->
  <div class="mps-cd">
    <p v-if="!d || d.stato === 'carico'" class="mps-cd-nota">{{ t('Leggo i colori del disegno…') }}</p>
    <p v-else-if="d.stato === 'errore'" class="mps-cd-nota">{{ t('Questo disegno non si può leggere: resta coi suoi colori.') }}</p>
    <template v-else>
      <div v-for="c in visibili" :key="c.colore" class="mps-cd-riga">
        <span class="mps-cd-orig" :style="{ background: c.colore }" :title="c.colore" aria-hidden="true"></span>
        <span class="mps-cd-freccia" aria-hidden="true">→</span>
        <div class="mps-cd-campo"><FieldColor :modelValue="mappa[c.colore] || ''" @update:modelValue="cambia(c.colore, $event)" /></div>
      </div>
      <div class="mps-cd-fila">
        <button v-if="d.colori.length > MAX && !tutti" type="button" class="mps-cd-btn" @click="tutti = true">{{ t('Mostra tutti i colori (%d)').replace('%d', d.colori.length) }}</button>
        <button v-if="Object.keys(mappa).length" type="button" class="mps-cd-btn" @click="$emit('update:modelValue', '')">{{ t('Torna ai colori del file') }}</button>
      </div>
    </template>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import { t } from '@/i18n';
import FieldColor from '../Builder/fields/FieldColor.vue';
import { disegno, normHex } from './disegni.js';

const props = defineProps({ src: { type: String, default: '' }, modelValue: { type: [Object, String], default: () => ({}) } });
const emit = defineEmits(['update:modelValue']);
const MAX = 10;
const tutti = ref(false);
const d = computed(() => disegno(props.src));
const mappa = computed(() => {
  const m = {};
  if (props.modelValue && typeof props.modelValue === 'object') for (const [k, v] of Object.entries(props.modelValue)) m[normHex(k)] = v;
  return m;
});
const visibili = computed(() => (d.value?.colori || []).slice(0, tutti.value ? undefined : MAX));
function cambia(colore, valore) {
  const m = { ...mappa.value };
  if (valore) m[colore] = valore; else delete m[colore];
  emit('update:modelValue', Object.keys(m).length ? m : '');
}
</script>

<style scoped>
.mps-cd { display: flex; flex-direction: column; gap: 4px; }
.mps-cd-nota { font-size: 10.5px; color: #9ca3af; margin: 0; }
.mps-cd-riga { display: flex; align-items: flex-start; gap: 6px; }
.mps-cd-campo { flex: 1 1 0; min-width: 0; overflow: hidden; }
.mps-cd-orig { width: 18px; height: 18px; flex: none; margin-top: 7px; border-radius: 4px; border: 1px solid #4b5563; }
.mps-cd-freccia { font-size: 11px; color: #6b7280; margin-top: 8px; }
.mps-cd-fila { display: flex; flex-wrap: wrap; gap: 6px; }
.mps-cd-btn { font: inherit; font-size: 10.5px; padding: 3px 6px; border: 1px dashed #4b5563; border-radius: 4px; background: transparent; color: #d1d5db; cursor: pointer; }
.mps-cd-btn:hover { border-color: var(--olo-color-primary, #e1474f); }
.mps-cd-btn:focus-visible { outline: 2px solid var(--olo-color-primary, #e1474f); outline-offset: 1px; }
</style>
