<template>
  <!-- Il ritaglio a punti (1.4.1431): il livello si vede solo dentro la figura fatta da questi punti (in percentuale del
       livello). Forme pronte e punti da spostare uno per uno: il fascio di luce di Sirena, la lama a metà di Truciolo. -->
  <div class="mps-ri">
    <div class="mps-ri-fila">
      <FieldSelect ui="dropdown" size="compact" theme="dark" class="mb-flex-1" :model-value="''" :options="PRONTE" @update:model-value="pronta" />
    </div>
    <div v-for="(p, i) in punti" :key="i" class="mps-ri-punto">
      <span class="mps-ri-n">{{ i + 1 }}</span>
      <input :value="p[0]" @change="cambia(i, 0, $event.target.value.trim())" class="mps-input mps-ri-v" aria-label="X" />
      <input :value="p[1]" @change="cambia(i, 1, $event.target.value.trim())" class="mps-input mps-ri-v" aria-label="Y" />
      <button v-if="punti.length > 3" type="button" class="mps-ri-btn" @click="togli(i)" :title="t('Togli il punto')" :aria-label="t('Togli il punto')">&times;</button>
    </div>
    <div class="mps-ri-fila">
      <button v-if="punti.length" type="button" class="mps-ri-aggiungi" @click="aggiungi">+ {{ t('Punto') }}</button>
      <button v-if="punti.length" type="button" class="mps-ri-aggiungi" @click="$emit('update:modelValue', [])">{{ t('Nessun ritaglio') }}</button>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import FieldSelect from '../Builder/fields/FieldSelect.vue';

const props = defineProps({ modelValue: { type: Array, default: () => [] } });
const emit = defineEmits(['update:modelValue']);
const punti = computed(() => (Array.isArray(props.modelValue) ? props.modelValue : []));
const FORME = {
  triangolo: [['50%', '0'], ['100%', '100%'], ['0', '100%']],
  rombo: [['50%', '0'], ['100%', '50%'], ['50%', '100%'], ['0', '50%']],
  fascio: [['0', '42%'], ['100%', '0'], ['100%', '100%'], ['0', '58%']],
  sinistra: [['0', '0'], ['50%', '0'], ['50%', '100%'], ['0', '100%']],
  destra: [['50%', '0'], ['100%', '0'], ['100%', '100%'], ['50%', '100%']],
  freccia: [['0', '20%'], ['60%', '20%'], ['60%', '0'], ['100%', '50%'], ['60%', '100%'], ['60%', '80%'], ['0', '80%']],
  esagono: [['25%', '0'], ['75%', '0'], ['100%', '50%'], ['75%', '100%'], ['25%', '100%'], ['0', '50%']],
};
const PRONTE = [
  { value: '', label: t('Forme pronte…') },
  { value: 'triangolo', label: t('Triangolo') }, { value: 'rombo', label: t('Rombo') }, { value: 'fascio', label: t('Fascio di luce') },
  { value: 'sinistra', label: t('Metà sinistra') }, { value: 'destra', label: t('Metà destra') }, { value: 'freccia', label: t('Freccia') },
  { value: 'esagono', label: t('Esagono') },
];
const copia = () => JSON.parse(JSON.stringify(punti.value));
function pronta(k) { if (FORME[k]) emit('update:modelValue', JSON.parse(JSON.stringify(FORME[k]))); }
function cambia(i, j, v) { const l = copia(); l[i][j] = v || '0'; emit('update:modelValue', l); }
function aggiungi() { const l = copia(); l.push(['50%', '50%']); emit('update:modelValue', l); }
function togli(i) { const l = copia(); l.splice(i, 1); emit('update:modelValue', l); }
</script>

<style scoped>
.mps-ri { display: flex; flex-direction: column; gap: 4px; }
.mps-ri-fila { display: flex; gap: 6px; }
.mps-ri-punto { display: flex; align-items: center; gap: 4px; }
.mps-ri-n { width: 14px; font-size: 10px; color: #6b7280; text-align: right; }
.mps-ri-v { flex: 1 1 0; min-width: 0; }
.mps-input { width: 100%; min-width: 0; background: #fff; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 3px 6px; font-size: 0.75rem; color: #111827; }
.mps-input:focus { outline: none; border-color: var(--olo-color-primary, #e1474f); }
.mps-ri-btn { font: inherit; width: 20px; height: 20px; padding: 0; border: 0; border-radius: 4px; background: transparent; color: #9ca3af; cursor: pointer; }
.mps-ri-btn:hover { color: #f87171; background: #1f2937; }
.mps-ri-aggiungi { font: inherit; font-size: 10.5px; padding: 3px 6px; border: 1px dashed #4b5563; border-radius: 4px; background: transparent; color: #d1d5db; cursor: pointer; }
.mps-ri-btn:focus-visible, .mps-ri-aggiungi:focus-visible { outline: 2px solid var(--olo-color-primary, #e1474f); outline-offset: 1px; }
</style>
