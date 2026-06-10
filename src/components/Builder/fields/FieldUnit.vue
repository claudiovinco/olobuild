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
      <select :value="unitPart" class="fu-unit" @change="onUnit($event.target.value)">
        <option v-for="u in units" :key="u" :value="u">{{ u }}</option>
      </select>
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue';

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
.fu-unit {
  flex: 0 0 auto;
  width: 58px;
  background: #f9fafb;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  padding: 5px 4px;
  font-size: 12px;
  color: #374151;
}
.fu-num:focus-visible,
.fu-unit:focus-visible {
  outline: 2px solid var(--olo-ui-accent, #e8622a);
  outline-offset: -1px;
}
</style>
