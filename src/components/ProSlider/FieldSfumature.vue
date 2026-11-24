<template>
  <!-- Le sfumature di una forma (1.4.1431): una o più, una sopra l'altra, lineari o radiali, anche ripetute (righe, pois,
       trame). Nei temi erano CSS su misura (Cementina, Ciak, A Cipolla, Trentagradi, Capitonné): ora ogni colore, ogni
       tappa e l'angolo si cambiano da qui. -->
  <div class="mps-sf">
    <div v-for="(g, i) in lista" :key="i" class="mps-sf-g">
      <div class="mps-sf-testa">
        <span class="mps-sf-n">{{ t('Sfumatura %d').replace('%d', i + 1) }}</span>
        <span class="mps-sf-anteprima" :style="{ background: sfumaturaCss(g) || 'transparent' }" aria-hidden="true"></span>
        <button v-if="i > 0" type="button" class="mps-sf-btn" @click="sposta(i, -1)" :title="t('Più in alto')" :aria-label="t('Più in alto')">↑</button>
        <button type="button" class="mps-sf-btn mps-sf-via" @click="togli(i)" :title="t('Togli la sfumatura')" :aria-label="t('Togli la sfumatura')">&times;</button>
      </div>
      <div class="mps-sf-fila">
        <FieldSelect ui="dropdown" size="compact" theme="dark" class="mb-flex-1" :model-value="g.tipo || 'lineare'" :options="TIPI" @update:model-value="cambia(i, 'tipo', $event)" />
        <label class="mps-sf-casella"><input type="checkbox" :checked="!!g.ripeti" @change="cambia(i, 'ripeti', $event.target.checked)" />{{ t('Ripetuta') }}</label>
      </div>
      <div v-if="(g.tipo || 'lineare') === 'lineare'" class="mps-sf-fila">
        <label class="mps-sf-et">{{ t('Angolo') }}</label>
        <NumberScrubber theme="dark" class="mb-flex-1" :modelValue="g.angolo ?? 180" :min="-360" :max="360" :step="1" :defaultValue="180" emitAs="number" unit="°" :sliderOnFocus="false" :ariaLabel="t('Angolo')" @update:modelValue="cambia(i, 'angolo', $event)" />
      </div>
      <template v-else>
        <div class="mps-sf-fila">
          <FieldSelect ui="dropdown" size="compact" theme="dark" class="mb-flex-1" :model-value="g.forma || ''" :options="FORME" @update:model-value="cambia(i, 'forma', $event)" />
          <input :value="g.posizione || ''" @change="cambia(i, 'posizione', $event.target.value.trim())" class="mps-input mb-flex-1" :placeholder="t('Centro: 50% 50%')" :aria-label="t('Centro della sfumatura')" />
        </div>
        <div class="mps-sf-fila">
          <label class="mps-sf-et">{{ t('Grandezza') }}</label>
          <input :value="g.dimensione || ''" @change="cambia(i, 'dimensione', $event.target.value.trim())" class="mps-input mb-flex-1" list="mps-sf-dim" :placeholder="t('di serie: fino all’angolo più lontano')" :aria-label="t('Grandezza')" />
        </div>
      </template>
      <div v-for="(tp, j) in (g.tappe || [])" :key="j" class="mps-sf-tappa">
        <FieldColor :modelValue="tp.colore || ''" @update:modelValue="cambiaTappa(i, j, 'colore', $event)" />
        <div class="mps-sf-fila">
          <label class="mps-sf-et">{{ t('da') }}</label>
          <input :value="tp.da || ''" @change="cambiaTappa(i, j, 'da', $event.target.value.trim())" class="mps-input mps-sf-pos" placeholder="0%" :aria-label="t('Da (percentuale o px)')" />
          <label class="mps-sf-et">{{ t('a') }}</label>
          <input :value="tp.a || ''" @change="cambiaTappa(i, j, 'a', $event.target.value.trim())" class="mps-input mps-sf-pos" placeholder="—" :aria-label="t('A (percentuale o px)')" />
          <button v-if="(g.tappe || []).length > 2" type="button" class="mps-sf-btn mps-sf-via" @click="togliTappa(i, j)" :title="t('Togli il colore')" :aria-label="t('Togli il colore')">&times;</button>
        </div>
      </div>
      <button type="button" class="mps-sf-aggiungi" @click="aggiungiTappa(i)">+ {{ t('Colore') }}</button>
    </div>
    <datalist id="mps-sf-dim"><option value="closest-side" /><option value="farthest-corner" /><option value="50% 50%" /></datalist>
    <button type="button" class="mps-sf-aggiungi" @click="aggiungi">+ {{ lista.length ? t('Un’altra sfumatura sopra') : t('Sfumatura') }}</button>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import FieldSelect from '../Builder/fields/FieldSelect.vue';
import FieldColor from '../Builder/fields/FieldColor.vue';
import NumberScrubber from '../Builder/fields/NumberScrubber.vue';
import { sfumaturaCss } from './livelloUtils.js';

const props = defineProps({ modelValue: { type: Array, default: () => [] } });
const emit = defineEmits(['update:modelValue']);
const lista = computed(() => (Array.isArray(props.modelValue) ? props.modelValue : []));
const TIPI = [{ value: 'lineare', label: t('Lineare') }, { value: 'radiale', label: t('Radiale (dal centro)') }];
const FORME = [{ value: '', label: t('Forma di serie') }, { value: 'circle', label: t('Cerchio') }, { value: 'ellipse', label: t('Ellisse') }];
const copia = () => JSON.parse(JSON.stringify(lista.value));
function manda(l) { emit('update:modelValue', l); }
function cambia(i, k, v) {
  const l = copia();
  if (v === '' || v === null || v === undefined) delete l[i][k]; else l[i][k] = v;
  manda(l);
}
function cambiaTappa(i, j, k, v) {
  const l = copia();
  if (v === '' && k !== 'colore') delete l[i].tappe[j][k]; else l[i].tappe[j][k] = v;
  manda(l);
}
function aggiungiTappa(i) {
  const l = copia();
  const ult = l[i].tappe?.[l[i].tappe.length - 1];
  (l[i].tappe = l[i].tappe || []).push({ colore: ult?.colore || 'var(--olo-color-primary)', da: '100%' });
  manda(l);
}
function togliTappa(i, j) { const l = copia(); l[i].tappe.splice(j, 1); manda(l); }
function aggiungi() {
  manda([...copia(), { tipo: 'lineare', angolo: 180, tappe: [{ colore: 'var(--olo-color-primary)', da: '0%' }, { colore: 'transparent', da: '100%' }] }]);
}
function togli(i) { const l = copia(); l.splice(i, 1); manda(l); }
function sposta(i, d) { const l = copia(); const [x] = l.splice(i, 1); l.splice(i + d, 0, x); manda(l); }
</script>

<style scoped>
.mps-sf { display: flex; flex-direction: column; gap: 6px; }
.mps-sf-g { border: 1px solid #374151; border-radius: 5px; padding: 6px; display: flex; flex-direction: column; gap: 5px; }
.mps-sf-testa { display: flex; align-items: center; gap: 6px; }
.mps-sf-n { font-size: 10.5px; font-weight: 600; color: #d1d5db; }
.mps-sf-anteprima { flex: 1; height: 14px; border-radius: 3px; border: 1px solid #374151; background-color: #0b1220; }
.mps-sf-fila { display: flex; align-items: center; gap: 6px; }
.mps-sf-et { font-size: 10.5px; color: #9ca3af; white-space: nowrap; }
.mps-sf-casella { display: inline-flex; align-items: center; gap: 4px; font-size: 10.5px; color: #9ca3af; white-space: nowrap; cursor: pointer; }
.mps-sf-tappa { display: flex; flex-direction: column; gap: 3px; padding-left: 6px; border-left: 2px solid #374151; }
.mps-sf-fila > * { min-width: 0; }
.mps-sf-pos { flex: 1 1 0; min-width: 0; }
.mps-input { width: 100%; min-width: 0; background: #fff; border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 3px 6px; font-size: 0.75rem; color: #111827; }
.mps-input:focus { outline: none; border-color: var(--olo-color-primary, #e1474f); }
.mps-sf-btn { font: inherit; width: 20px; height: 20px; flex: none; padding: 0; border: 0; border-radius: 4px; background: transparent; color: #9ca3af; cursor: pointer; }
.mps-sf-btn:hover { background: #1f2937; color: #fff; }
.mps-sf-via:hover { color: #f87171; }
.mps-sf-aggiungi { font: inherit; font-size: 10.5px; padding: 4px 6px; border: 1px dashed #4b5563; border-radius: 4px; background: transparent; color: #d1d5db; cursor: pointer; align-self: flex-start; }
.mps-sf-aggiungi:hover { border-color: var(--olo-color-primary, #e1474f); }
.mps-sf-btn:focus-visible, .mps-sf-aggiungi:focus-visible { outline: 2px solid var(--olo-color-primary, #e1474f); outline-offset: 1px; }
</style>
