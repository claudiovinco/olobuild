<template>
  <div class="mb-flex mb-items-center mb-gap-2">
    <input
      type="range"
      :value="modelValue"
      @input="onInput($event)"
      :min="min"
      :max="max"
      :step="step"
      class="field-range mb-flex-1"
    />
    <input
      type="number"
      :value="modelValue"
      @change="onInput($event)"
      :step="step"
      class="mb-w-14 mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-1.5 mb-py-0.5 mb-text-xs mb-text-gray-900 mb-text-center"
    />
  </div>
</template>

<script setup>
const props = defineProps({
  modelValue: { type: [String, Number], default: 0 },
  min: { type: Number, default: 0 },
  max: { type: Number, default: 100 },
  step: { type: Number, default: 1 },
});
const emit = defineEmits(['update:modelValue']);

function onInput(e) {
  emit('update:modelValue', e.target.value);
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
</style>
