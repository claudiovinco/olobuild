<template>
  <!-- Più ombre insieme, anche interne (1.4.1431): un anello luminoso più un'ombra lontana, una lastra di vetro con la
       luce dentro (Vetrino, Combinazione, Scheggia, Due mani). Prima erano CSS su misura. -->
  <div class="mps-om">
    <div v-for="(o, i) in lista" :key="i" class="mps-om-o">
      <div class="mps-om-testa">
        <span class="mps-om-n">{{ t('Ombra %d').replace('%d', i + 1) }}</span>
        <label class="mps-om-casella"><input type="checkbox" :checked="!!o.inset" @change="cambia(i, 'inset', $event.target.checked)" />{{ t('Interna') }}</label>
        <button type="button" class="mps-om-btn" @click="togli(i)" :title="t('Togli l’ombra')" :aria-label="t('Togli l’ombra')">&times;</button>
      </div>
      <div class="mps-om-griglia">
        <div><label class="mps-om-et">X</label><NumberScrubber theme="dark" :modelValue="o.x ?? 0" :min="-200" :max="200" :step="1" :defaultValue="0" emitAs="number" unit="px" :sliderOnFocus="true" ariaLabel="X" @update:modelValue="cambia(i, 'x', $event)" /></div>
        <div><label class="mps-om-et">Y</label><NumberScrubber theme="dark" :modelValue="o.y ?? 0" :min="-200" :max="200" :step="1" :defaultValue="0" emitAs="number" unit="px" :sliderOnFocus="true" ariaLabel="Y" @update:modelValue="cambia(i, 'y', $event)" /></div>
        <div><label class="mps-om-et">{{ t('Sfocatura') }}</label><NumberScrubber theme="dark" :modelValue="o.blur ?? 0" :min="0" :max="300" :step="1" :defaultValue="0" emitAs="number" unit="px" :sliderOnFocus="true" :ariaLabel="t('Sfocatura')" @update:modelValue="cambia(i, 'blur', $event)" /></div>
        <div><label class="mps-om-et">{{ t('Estensione') }}</label><NumberScrubber theme="dark" :modelValue="o.spread ?? 0" :min="-100" :max="100" :step="1" :defaultValue="0" emitAs="number" unit="px" :sliderOnFocus="true" :ariaLabel="t('Estensione')" @update:modelValue="cambia(i, 'spread', $event)" /></div>
      </div>
      <FieldColor :modelValue="o.color || 'rgba(0,0,0,.35)'" @update:modelValue="cambia(i, 'color', $event)" />
    </div>
    <button type="button" class="mps-om-aggiungi" @click="aggiungi">+ {{ lista.length ? t('Un’altra ombra') : t('Ombre (anche più d’una, anche interne)') }}</button>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import FieldColor from '../Builder/fields/FieldColor.vue';
import NumberScrubber from '../Builder/fields/NumberScrubber.vue';

const props = defineProps({ modelValue: { type: Array, default: () => [] } });
const emit = defineEmits(['update:modelValue']);
const lista = computed(() => (Array.isArray(props.modelValue) ? props.modelValue : []));
const copia = () => JSON.parse(JSON.stringify(lista.value));
function cambia(i, k, v) { const l = copia(); l[i][k] = v; emit('update:modelValue', l); }
function aggiungi() { emit('update:modelValue', [...copia(), { x: 0, y: 12, blur: 30, spread: 0, color: 'rgba(0,0,0,.35)', inset: false }]); }
function togli(i) { const l = copia(); l.splice(i, 1); emit('update:modelValue', l); }
</script>

<style scoped>
.mps-om { display: flex; flex-direction: column; gap: 6px; }
.mps-om-o { border: 1px solid #374151; border-radius: 5px; padding: 6px; display: flex; flex-direction: column; gap: 5px; }
.mps-om-testa { display: flex; align-items: center; gap: 8px; }
.mps-om-n { flex: 1; font-size: 10.5px; font-weight: 600; color: #d1d5db; }
.mps-om-casella { display: inline-flex; align-items: center; gap: 4px; font-size: 10.5px; color: #9ca3af; cursor: pointer; }
.mps-om-griglia { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 4px 8px; }
.mps-om-griglia > div { min-width: 0; }
.mps-om-et { display: block; font-size: 10.5px; color: #9ca3af; margin-bottom: 2px; }
.mps-om-btn { font: inherit; width: 20px; height: 20px; padding: 0; border: 0; border-radius: 4px; background: transparent; color: #9ca3af; cursor: pointer; }
.mps-om-btn:hover { color: #f87171; background: #1f2937; }
.mps-om-aggiungi { font: inherit; font-size: 10.5px; padding: 4px 6px; border: 1px dashed #4b5563; border-radius: 4px; background: transparent; color: #d1d5db; cursor: pointer; align-self: flex-start; }
.mps-om-aggiungi:hover { border-color: var(--olo-color-primary, #e1474f); }
.mps-om-btn:focus-visible, .mps-om-aggiungi:focus-visible { outline: 2px solid var(--olo-color-primary, #e1474f); outline-offset: 1px; }
</style>
