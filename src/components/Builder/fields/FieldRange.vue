<template>
  <div class="mb-flex mb-items-center mb-gap-2">
    <input
      type="range"
      :value="modelValue"
      @input="onInput($event)"
      @dblclick="onReset"
      :min="min"
      :max="max"
      :step="step"
      class="field-range mb-flex-1"
      :title="resetHint"
    />
    <input
      type="number"
      :value="modelValue"
      @change="onInput($event)"
      @input="onInput($event)"
      @dblclick="onReset"
      @wheel="handleNumberWheel"
      :step="step"
      :min="min"
      :max="max"
      class="field-range-num mb-w-16 mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-1.5 mb-py-0.5 mb-text-xs mb-text-gray-900 mb-text-center"
      :title="resetHint"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import { handleNumberWheel } from '@/utils/numberInputWheel';

const props = defineProps({
  modelValue: { type: [String, Number], default: 0 },
  min: { type: Number, default: 0 },
  max: { type: Number, default: 100 },
  step: { type: Number, default: 1 },
  defaultValue: { type: [String, Number, null], default: null },
});
const emit = defineEmits(['update:modelValue']);

// Resolve the reset target: respect the explicit defaultValue verbatim
// (including '' which means "no value / inherit / unfiltered").
// Only fall back to min when no defaultValue was provided at all.
const resetTarget = computed(() => {
  if (props.defaultValue !== null && props.defaultValue !== undefined) {
    return props.defaultValue;
  }
  return props.min ?? 0;
});

const resetHint = computed(() => t('Doppio click per reimpostare al valore predefinito'));

function onInput(e) {
  emit('update:modelValue', e.target.value);
}

function onReset() {
  emit('update:modelValue', resetTarget.value);
}
</script>

<style scoped>
.field-range {
  -webkit-appearance: none;
  appearance: none;
  height: 4px;
  background: #374151;
  border-radius: 2px;
  outline: none;
}
.field-range::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 14px;
  height: 14px;
  border-radius: 50%;
  background: var(--olo-color-primary, #6366f1);
  cursor: pointer;
  border: 2px solid var(--olo-color-primary, #6366F1);
}
.field-range::-moz-range-thumb {
  width: 14px;
  height: 14px;
  border-radius: 50%;
  background: var(--olo-color-primary, #6366f1);
  cursor: pointer;
  border: 2px solid var(--olo-color-primary, #6366F1);
}
.field-range::-moz-range-track {
  height: 4px;
  background: #374151;
  border-radius: 2px;
}

/* Hide native number spin-buttons (liberano spazio per 3 cifre). */
.field-range-num::-webkit-outer-spin-button,
.field-range-num::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}
.field-range-num {
  -moz-appearance: textfield;
}
</style>
