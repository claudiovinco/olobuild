<template>
  <div class="cfg-input cfg-number" :class="sizeClass">
    <input
      ref="inputEl"
      type="number"
      :min="min" :max="max" :step="step"
      :value="modelValue"
      :disabled="disabled"
      @input="onInput"
      @blur="onBlur"
      @keydown.up.prevent="nudge(1)"
      @keydown.down.prevent="nudge(-1)"
    />
    <span v-if="suffix" class="suffix">{{ suffix }}</span>
    <span class="cnum-steppers">
      <button type="button" tabindex="-1" :disabled="disabled" @click="nudge(1)" :aria-label="'+' + step">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg>
      </button>
      <button type="button" tabindex="-1" :disabled="disabled" @click="nudge(-1)" :aria-label="'-' + step">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
      </button>
    </span>
  </div>
</template>

<script setup>
// Input numerico della pagina cfg: spinner nativi nascosti (CSS globale),
// stepper custom coerenti col design system, frecce tastiera supportate.
import { ref, computed } from 'vue';

const props = defineProps({
  modelValue: { type: Number, default: 0 },
  min: { type: Number, default: undefined },
  max: { type: Number, default: undefined },
  step: { type: Number, default: 1 },
  suffix: { type: String, default: '' },
  disabled: { type: Boolean, default: false },
  size: { type: String, default: 'xs' }, // default compatto: un numero non ha bisogno di 500px
});
const emit = defineEmits(['update:modelValue']);

const inputEl = ref(null);
const sizeClass = computed(() => (props.size ? `cfg-w-${props.size}` : ''));

function clamp(n) {
  if (props.min !== undefined && n < props.min) n = props.min;
  if (props.max !== undefined && n > props.max) n = props.max;
  return n;
}

// Evita 0.30000000000000004 con step decimali
function roundToStep(n) {
  const dec = (String(props.step).split('.')[1] || '').length;
  return parseFloat(n.toFixed(dec));
}

function onInput(e) {
  const raw = e.target.value;
  if (raw === '' || raw === '-') return; // lascia digitare, sistemiamo al blur
  const n = parseFloat(raw);
  if (!Number.isNaN(n)) emit('update:modelValue', clamp(n));
}

function onBlur(e) {
  const n = parseFloat(e.target.value);
  const v = Number.isNaN(n) ? clamp(props.min ?? 0) : clamp(n);
  emit('update:modelValue', v);
  if (inputEl.value) inputEl.value.value = String(v);
}

function nudge(dir) {
  if (props.disabled) return;
  const v = clamp(roundToStep((props.modelValue || 0) + dir * props.step));
  emit('update:modelValue', v);
}
</script>

<style scoped>
.cfg-number { padding-right: 0; gap: 6px; }
.cfg-number input { font-variant-numeric: tabular-nums; }
.cfg-number .suffix { margin-left: 0; padding-left: 0; }
.cnum-steppers {
  display: flex; flex-direction: column;
  align-self: stretch;
  border-left: 1px solid var(--c-line-soft);
  flex-shrink: 0;
}
.cnum-steppers button {
  flex: 1;
  display: grid; place-items: center;
  width: 22px;
  border: 0; padding: 0;
  background: transparent;
  color: var(--c-text-faint);
  cursor: pointer;
}
.cnum-steppers button:hover:not(:disabled) { background: var(--c-bg); color: var(--c-navy); }
.cnum-steppers button:disabled { cursor: not-allowed; opacity: .5; }
.cnum-steppers button svg { width: 11px; height: 11px; }
.cnum-steppers button:first-child { border-radius: 0 7px 0 0; }
.cnum-steppers button:last-child { border-radius: 0 0 7px 0; border-top: 1px solid var(--c-line-soft); }
</style>
