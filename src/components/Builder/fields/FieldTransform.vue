<template>
  <div class="mb-space-y-2">
    <!-- Rotate -->
    <div class="mb-flex mb-items-center mb-gap-2">
      <label class="mb-text-[10px] mb-text-gray-500 mb-w-16">{{ t('Rotazione') }}</label>
      <input type="range" :value="val.rotate" @input="update('rotate', $event.target.value)" min="-180" max="180"
        class="mb-flex-1 mb-h-1.5" />
      <span class="mb-text-[10px] mb-text-gray-400 mb-w-10 mb-text-right">{{ val.rotate }}°</span>
    </div>
    <!-- Scale -->
    <div class="mb-flex mb-items-center mb-gap-2">
      <label class="mb-text-[10px] mb-text-gray-500 mb-w-16">{{ t('Scala') }}</label>
      <input type="range" :value="val.scale * 100" @input="update('scale', $event.target.value / 100)" min="10" max="300" step="5"
        class="mb-flex-1 mb-h-1.5" />
      <span class="mb-text-[10px] mb-text-gray-400 mb-w-10 mb-text-right">{{ Math.round(val.scale * 100) }}%</span>
    </div>
    <!-- Translate X/Y -->
    <div class="mb-flex mb-gap-2">
      <div class="mb-flex-1 mb-flex mb-items-center mb-gap-1">
        <label class="mb-text-[10px] mb-text-gray-500">X</label>
        <input type="number" :value="val.translateX" @input="update('translateX', $event.target.value)"
          min="-500" max="500" :aria-label="t('Spostamento orizzontale (px)')"
          class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded mb-px-2 mb-py-1 mb-text-xs mb-text-gray-200" />
      </div>
      <div class="mb-flex-1 mb-flex mb-items-center mb-gap-1">
        <label class="mb-text-[10px] mb-text-gray-500">Y</label>
        <input type="number" :value="val.translateY" @input="update('translateY', $event.target.value)"
          min="-500" max="500" :aria-label="t('Spostamento verticale (px)')"
          class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded mb-px-2 mb-py-1 mb-text-xs mb-text-gray-200" />
      </div>
    </div>
    <!-- Skew X/Y -->
    <div class="mb-flex mb-gap-2">
      <div class="mb-flex-1 mb-flex mb-items-center mb-gap-1">
        <label class="mb-text-[10px] mb-text-gray-500 mb-w-10">{{ t('SkewX') }}</label>
        <input type="number" :value="val.skewX" @input="update('skewX', $event.target.value)" min="-45" max="45"
          class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded mb-px-2 mb-py-1 mb-text-xs mb-text-gray-200" />
      </div>
      <div class="mb-flex-1 mb-flex mb-items-center mb-gap-1">
        <label class="mb-text-[10px] mb-text-gray-500 mb-w-10">{{ t('SkewY') }}</label>
        <input type="number" :value="val.skewY" @input="update('skewY', $event.target.value)" min="-45" max="45"
          class="mb-w-full mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded mb-px-2 mb-py-1 mb-text-xs mb-text-gray-200" />
      </div>
    </div>
    <!-- Origin -->
    <div class="mb-flex mb-items-center mb-gap-2">
      <label class="mb-text-[10px] mb-text-gray-500 mb-w-16">{{ t('Origine') }}</label>
      <select :value="val.origin" @change="update('origin', $event.target.value)"
        class="mb-flex-1 mb-bg-gray-700 mb-border mb-border-gray-600 mb-rounded mb-px-2 mb-py-1 mb-text-xs mb-text-gray-200">
        <option value="center">{{ t('Centro') }}</option>
        <option value="top left">{{ t('Alto SX') }}</option>
        <option value="top center">{{ t('Alto Centro') }}</option>
        <option value="top right">{{ t('Alto DX') }}</option>
        <option value="center left">{{ t('Centro SX') }}</option>
        <option value="center right">{{ t('Centro DX') }}</option>
        <option value="bottom left">{{ t('Basso SX') }}</option>
        <option value="bottom center">{{ t('Basso Centro') }}</option>
        <option value="bottom right">{{ t('Basso DX') }}</option>
      </select>
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';

const props = defineProps({
  modelValue: { type: Object, default: () => ({ rotate: 0, scale: 1, translateX: 0, translateY: 0, skewX: 0, skewY: 0, origin: 'center' }) }
});
const emit = defineEmits(['update:modelValue']);

const val = computed(() => ({
  rotate: props.modelValue?.rotate ?? 0,
  scale: props.modelValue?.scale ?? 1,
  translateX: props.modelValue?.translateX ?? 0,
  translateY: props.modelValue?.translateY ?? 0,
  skewX: props.modelValue?.skewX ?? 0,
  skewY: props.modelValue?.skewY ?? 0,
  origin: props.modelValue?.origin ?? 'center',
}));

function update(key, value) {
  const numKeys = ['rotate', 'translateX', 'translateY', 'skewX', 'skewY'];
  const newVal = { ...val.value, [key]: numKeys.includes(key) ? parseInt(value) || 0 : key === 'scale' ? parseFloat(value) || 1 : value };
  emit('update:modelValue', newVal);
}
</script>
