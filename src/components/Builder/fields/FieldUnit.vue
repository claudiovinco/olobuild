<template>
  <div class="fu-wrap">
    <!-- Valore non parsabile (es. 'auto', 'calc(...)'): input testo raw di fallback -->
    <input
      v-if="rawMode"
      type="text"
      :value="modelValue"
      :placeholder="placeholder"
      class="fu-num fu-raw"
      @input="$emit('update:modelValue', $event.target.value)"
    />
    <template v-else>
      <input
        type="number"
        :value="numPart"
        :min="min"
        :max="max"
        :step="step ?? 'any'"
        :placeholder="placeholder"
        class="fu-num"
        @input="onNum($event.target.value)"
      />
      <FieldSelect
        ui="dropdown"
        size="compact"
        class="fu-unit"
        :model-value="unitPart"
        :options="unitOptions"
        @update:model-value="onUnit($event)"
      />
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import FieldSelect from './FieldSelect.vue';

/**
 * FieldUnit — dimensione CSS con unità ('200px', '0.2em', '50%', '120ms').
 * Salva la STESSA stringa CSS del vecchio campo testo: numero+unità composti,
 * '' se vuoto (semantica "auto" preservata). Valori non parsabili (auto,
 * calc(...), keyword) restano editabili in modalità testo raw — nessun dato
 * legacy viene perso o riscritto.
 */
const props = defineProps({
  modelValue: { type: [String, Number], default: '' },
  units: { type: Array, default: () => ['px', 'em', 'rem', '%', 'vh', 'vw'] },
  min: { type: Number, default: undefined },
  max: { type: Number, default: undefined },
  step: { type: [Number, String], default: undefined },
  placeholder: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);

const RE = /^(-?[\d.]+)\s*([a-z%]*)$/i;

const parsed = computed(() => {
  const v = String(props.modelValue ?? '').trim();
  if (v === '') return { num: '', unit: props.units[0] || 'px', raw: false };
  const m = v.match(RE);
  if (!m) return { num: '', unit: props.units[0] || 'px', raw: true };
  // Numero puro senza unità (es. '14' o 14): trattato come prima unità della lista
  const unit = m[2] || (props.units[0] || 'px');
  if (m[2] && !props.units.includes(m[2])) return { num: '', unit: props.units[0] || 'px', raw: true };
  return { num: m[1], unit, raw: false };
});

const rawMode = computed(() => parsed.value.raw);
const numPart = computed(() => parsed.value.num);
const unitPart = computed(() => parsed.value.unit);

// Opzioni per il dropdown unità (label tecniche: px/em/rem/%… restano as-is)
const unitOptions = computed(() => props.units.map((u) => ({ value: u, label: u })));

function onNum(n) {
  const v = String(n).trim();
  emit('update:modelValue', v === '' ? '' : v + unitPart.value);
}
function onUnit(u) {
  const n = numPart.value;
  emit('update:modelValue', n === '' ? '' : n + u);
}
</script>

<style scoped>
.fu-wrap {
  display: flex;
  gap: 4px;
}
.fu-num {
  flex: 1 1 auto;
  min-width: 0;
  background: #fff;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  padding: 5px 8px;
  font-size: 13px;
  color: #1f2937;
}
.fu-raw {
  font-family: ui-monospace, Menlo, Consolas, monospace;
  font-size: 12px;
}
/* FieldSelect unità: larghezza fissa nel flex row (flex-basis vince sul
   width:100% del root .fsel), trigger allineato all'altezza dell'input numero */
.fu-wrap .fu-unit {
  flex: 0 0 58px;
  min-width: 0;
}
.fu-unit :deep(.fsel-trigger) {
  height: 100%;
}
.fu-num:focus-visible {
  outline: 2px solid var(--olo-ui-accent, #e8622a);
  outline-offset: -1px;
}
</style>
