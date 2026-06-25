<template>
  <div class="mb-space-y-2">
    <!-- Rotate -->
    <div class="mb-flex mb-items-center mb-gap-2">
      <label class="mb-text-[10px] mb-text-gray-500 mb-w-16">{{ t('Rotazione') }}</label>
      <NumberScrubber class="mb-flex-1" :modelValue="val.rotate" :min="-180" :max="180" :step="1"
        :defaultValue="0" emitAs="number" unit="°" :sliderOnFocus="false" :ariaLabel="t('Rotazione')"
        @update:modelValue="update('rotate', $event)" />
    </div>
    <!-- Scale -->
    <div class="mb-flex mb-items-center mb-gap-2">
      <label class="mb-text-[10px] mb-text-gray-500 mb-w-16">{{ t('Scala') }}</label>
      <NumberScrubber class="mb-flex-1" :modelValue="Math.round(val.scale * 100)" :min="10" :max="300" :step="5"
        :defaultValue="100" emitAs="number" unit="%" :sliderOnFocus="false" :ariaLabel="t('Scala')"
        @update:modelValue="update('scale', $event / 100)" />
    </div>
    <!-- Translate X/Y -->
    <div class="mb-flex mb-gap-2">
      <div class="mb-flex-1 mb-flex mb-items-center mb-gap-1.5 olo-tf-cell">
        <label class="mb-text-[10px] mb-text-gray-500">X</label>
        <NumberScrubber class="mb-flex-1 olo-tf-fill" :modelValue="val.translateX" :min="-500" :max="500" :step="1"
          :defaultValue="0" emitAs="number" unit="px" :ariaLabel="t('Spostamento orizzontale (px)')"
          @update:modelValue="update('translateX', $event)" />
      </div>
      <div class="mb-flex-1 mb-flex mb-items-center mb-gap-1.5 olo-tf-cell">
        <label class="mb-text-[10px] mb-text-gray-500">Y</label>
        <NumberScrubber class="mb-flex-1 olo-tf-fill" :modelValue="val.translateY" :min="-500" :max="500" :step="1"
          :defaultValue="0" emitAs="number" unit="px" :ariaLabel="t('Spostamento verticale (px)')"
          @update:modelValue="update('translateY', $event)" />
      </div>
    </div>
    <!-- Skew X/Y -->
    <div class="mb-flex mb-gap-2">
      <div class="mb-flex-1 mb-flex mb-items-center mb-gap-1.5 olo-tf-cell">
        <label class="mb-text-[10px] mb-text-gray-500 mb-w-10">{{ t('SkewX') }}</label>
        <NumberScrubber class="mb-flex-1 olo-tf-fill" :modelValue="val.skewX" :min="-45" :max="45" :step="1"
          :defaultValue="0" emitAs="number" unit="°" :ariaLabel="t('SkewX')"
          @update:modelValue="update('skewX', $event)" />
      </div>
      <div class="mb-flex-1 mb-flex mb-items-center mb-gap-1.5 olo-tf-cell">
        <label class="mb-text-[10px] mb-text-gray-500 mb-w-10">{{ t('SkewY') }}</label>
        <NumberScrubber class="mb-flex-1 olo-tf-fill" :modelValue="val.skewY" :min="-45" :max="45" :step="1"
          :defaultValue="0" emitAs="number" unit="°" :ariaLabel="t('SkewY')"
          @update:modelValue="update('skewY', $event)" />
      </div>
    </div>
    <!-- Origin -->
    <div class="mb-flex mb-items-center mb-gap-2">
      <label class="mb-text-[10px] mb-text-gray-500 mb-w-16">{{ t('Origine') }}</label>
      <FieldSelect
        ui="dropdown"
        size="compact"
        class="mb-flex-1 mb-min-w-0"
        :model-value="val.origin"
        :options="ORIGIN_OPTIONS"
        @update:model-value="update('origin', $event)"
      />
    </div>
  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed } from 'vue';
import FieldSelect from './FieldSelect.vue';
import NumberScrubber from './NumberScrubber.vue';

// Label RAW: FieldSelect applica t() internamente
const ORIGIN_OPTIONS = [
  { value: 'center', label: 'Centro' },
  { value: 'top left', label: 'Alto SX' },
  { value: 'top center', label: 'Alto Centro' },
  { value: 'top right', label: 'Alto DX' },
  { value: 'center left', label: 'Centro SX' },
  { value: 'center right', label: 'Centro DX' },
  { value: 'bottom left', label: 'Basso SX' },
  { value: 'bottom center', label: 'Basso Centro' },
  { value: 'bottom right', label: 'Basso DX' },
];

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

<style scoped>
/* Sposta/Skew: la valbox del NumberScrubber riempie la cella della coppia. */
.olo-tf-cell .olo-tf-fill { min-width: 0; }
.olo-tf-cell .olo-tf-fill :deep(.olo-ns-box) { width: 100%; }
</style>
