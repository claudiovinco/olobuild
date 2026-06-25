<template>
  <div class="olo-nf">
    <label class="olo-nf-lab">{{ t(field.label) }}</label>

    <!-- Range: NumberScrubber con slider inline (coerente con StyleEffectsStack) -->
    <div v-if="field.type === 'range'" class="olo-nf-sliderrow">
      <NumberScrubber
        class="olo-nf-ns"
        :modelValue="num"
        :min="field.min ?? 0"
        :max="field.max ?? 100"
        :step="field.step ?? 1"
        :defaultValue="field.default ?? (field.min ?? 0)"
        emitAs="number"
        :unit="field.unit || ''"
        :sliderOnFocus="false"
        :ariaLabel="t(field.label)"
        @update:modelValue="onUpdate($event)"
      />
    </div>

    <!-- Select (FieldSelect dropdown custom; t() sulle label lo applica lui) -->
    <FieldSelect
      v-else-if="field.type === 'select'"
      ui="dropdown"
      :model-value="value"
      :options="field.options || []"
      @update:model-value="onUpdate($event)"
    />

    <!-- Text fallback -->
    <input
      v-else
      type="text"
      class="olo-nf-text"
      :value="value"
      @input="onUpdate($event.target.value)"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import FieldSelect from '../fields/FieldSelect.vue';
import NumberScrubber from '../fields/NumberScrubber.vue';

/**
 * StyleNestedField — gestisce field con chiave "path" annidata via "."
 *  es. field.key = 'transition.duration' → legge tile.style.transition.duration
 *
 * Emette update di tipo 'transition' (per ora) — il path completo è inferito dal segmento iniziale.
 * Se il primo segmento è 'transition', emette { type: 'transition', key: 'duration', value }.
 * Altri prefix (futuri): mappare in StyleFieldsRenderer.
 *
 * UI: il range usa lo slider arancio CHROME + valbox con unità, coerente con StyleEffectsStack
 * (pannello "Transizione hover"). Le chiavi salvate restano INVARIATE (transition.duration/easing).
 */
const props = defineProps({
  field: { type: Object, required: true },     // { key: 'transition.duration', type, label, ... }
  tileStyle: { type: Object, required: true },
});
const emit = defineEmits(['update']);

const segments = computed(() => (props.field.key || '').split('.'));
const root = computed(() => segments.value[0]);
const sub = computed(() => segments.value.slice(1).join('.'));

const value = computed(() => {
  let cur = props.tileStyle;
  for (const s of segments.value) {
    if (cur == null) return props.field.default ?? '';
    cur = cur[s];
  }
  return cur ?? props.field.default ?? '';
});

// Valore numerico per slider/valbox (range). Fallback al default del field.
const num = computed(() => {
  const n = parseInt(value.value, 10);
  return isNaN(n) ? (props.field.default ?? 0) : n;
});

function onUpdate(val) {
  if (root.value === 'transition') {
    emit('update', { type: 'transition', key: sub.value, value: val });
  } else {
    // Generic: emit nested path
    emit('update', { type: 'nested', path: props.field.key, value: val });
  }
}
</script>

<style scoped>
.olo-nf {
  --olo-ui-accent: #e8622a;
}
.olo-nf-lab {
  display: block;
  font-size: 9px;
  font-weight: 700;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: #9ca3af;
  margin-bottom: 6px;
}

/* slider + valbox (NumberScrubber, stesso linguaggio di StyleEffectsStack) */
.olo-nf-sliderrow { display: flex; align-items: center; gap: 12px; }
.olo-nf-ns { flex: 1; min-width: 0; }

/* text fallback */
.olo-nf-text {
  width: 100%;
  height: 38px;
  padding: 0 11px;
  border: 1px solid #e5e7eb;
  border-radius: 9px;
  background: #fff;
  font-size: 14px;
  color: #1f2937;
  outline: none;
  transition: border-color 0.15s, box-shadow 0.15s;
}
.olo-nf-text:focus { border-color: var(--olo-ui-accent); box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-ui-accent) 18%, transparent); }
</style>
