<template>
  <!--
    FieldDimension — input dimensionale compatto: numero + selettore unità.
    Emette una stringa CSS ("1200px", "50%", "80vh") oppure '' (= auto/non impostato).
    Il valore salvato resta una stringa come prima (chiavi tile_width / tile_max_width /
    tile_min_height invariate): cambia solo la UI di editing.
  -->
  <div class="olo-dim" :class="{ 'is-focused': false }">
    <input
      type="text"
      inputmode="decimal"
      class="olo-dim-num"
      :value="numPart"
      :placeholder="placeholder"
      @input="onInput($event.target.value)"
      @blur="onInput($event.target.value)"
      :aria-label="ariaLabel || placeholder"
    />
    <div class="olo-dim-unitwrap">
      <select
        class="olo-dim-unit"
        :value="unitPart"
        @change="onUnit($event.target.value)"
        :aria-label="t('Unità')"
      >
        <option v-for="u in units" :key="u" :value="u">{{ u }}</option>
      </select>
      <svg class="olo-dim-chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';

const props = defineProps({
  modelValue: { type: String, default: '' },
  // unità disponibili nel dropdown
  units: { type: Array, default: () => ['px', '%', 'vh', 'vw', 'em', 'rem'] },
  defaultUnit: { type: String, default: 'px' },
  // testo grigio quando il campo è vuoto (es. "auto", "nessuna")
  placeholder: { type: String, default: 'auto' },
  ariaLabel: { type: String, default: '' },
});
const emit = defineEmits(['update:modelValue']);

// Parsing tollerante: "1200px" → {num:'1200',unit:'px'}; "50%" → {50,%};
// vuoto / "auto" / "none" / "calc(...)" → {num:'', unit:defaultUnit} (input vuoto, placeholder).
const parsed = computed(() => {
  const v = String(props.modelValue ?? '').trim();
  const m = v.match(/^(-?\d*\.?\d+)\s*(px|%|vh|vw|em|rem|fr|ch)?$/i);
  if (m) {
    const unit = (m[2] || props.defaultUnit).toLowerCase();
    return { num: m[1], unit: props.units.includes(unit) ? unit : props.defaultUnit };
  }
  return { num: '', unit: props.defaultUnit };
});
const numPart = computed(() => parsed.value.num);
const unitPart = computed(() => parsed.value.unit);

function emitVal(num, unit) {
  const n = String(num).trim();
  // vuoto = auto/non impostato (il PHP tratta tile_* vuoto come default)
  emit('update:modelValue', n === '' ? '' : n + unit);
}
function onInput(raw) {
  // accetta solo cifre/punto/segno: le keyword (auto) si esprimono lasciando vuoto
  const clean = String(raw).replace(/[^0-9.\-]/g, '');
  emitVal(clean, unitPart.value);
}
function onUnit(u) {
  if (numPart.value === '') return; // niente unità da applicare se non c'è un numero
  emitVal(numPart.value, u);
}
</script>

<style scoped>
.olo-dim {
  display: flex;
  align-items: stretch;
  height: 38px;
  border: 1px solid #e5e7eb;
  border-radius: 9px;
  background: #fff;
  overflow: hidden;
  transition: border-color 0.15s, box-shadow 0.15s;
}
.olo-dim:focus-within {
  border-color: var(--olo-ui-accent, #e8622a);
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-ui-accent, #e8622a) 18%, transparent);
}
.olo-dim-num {
  flex: 1;
  min-width: 0;
  border: none;
  outline: none;
  background: transparent;
  padding: 0 12px;
  font-family: 'SF Mono', Monaco, 'Cascadia Code', monospace;
  font-size: 13px;
  color: #1f2937;
}
.olo-dim-num::placeholder { color: #9ca3af; font-family: inherit; }
.olo-dim-unitwrap {
  position: relative;
  flex-shrink: 0;
  border-left: 1px solid #eef0f3;
  background: #f6f7f9;
}
.olo-dim-unit {
  height: 100%;
  padding: 0 26px 0 10px;
  border: none;
  outline: none;
  background: transparent;
  font-size: 12px;
  font-weight: 600;
  color: #6b7280;
  cursor: pointer;
  appearance: none;
  -webkit-appearance: none;
}
.olo-dim-chev {
  position: absolute;
  right: 8px;
  top: 50%;
  transform: translateY(-50%);
  width: 12px;
  height: 12px;
  color: #9ca3af;
  pointer-events: none;
}
</style>
