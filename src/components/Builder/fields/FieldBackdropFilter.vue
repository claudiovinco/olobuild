<template>
  <div class="mb-space-y-2">
    <div class="mb-flex mb-items-center mb-gap-2">
      <label class="mb-text-[10px] mb-text-gray-400 mb-w-16">Blur</label>
      <NumberScrubber class="mb-flex-1" :modelValue="value.blur" :min="0" :max="30" :step="1"
        :defaultValue="0" emitAs="number" unit="px" :sliderOnFocus="false" :ariaLabel="t('Sfocatura')"
        @update:modelValue="onUpdate('blur', $event)" />
    </div>
    <div class="mb-flex mb-items-center mb-gap-2">
      <label class="mb-text-[10px] mb-text-gray-400 mb-w-16">{{ t('Luminosità') }}</label>
      <NumberScrubber class="mb-flex-1" :modelValue="value.brightness" :min="0" :max="200" :step="5"
        :defaultValue="100" emitAs="number" unit="%" :sliderOnFocus="false" :ariaLabel="t('Luminosità')"
        @update:modelValue="onUpdate('brightness', $event)" />
    </div>
    <div class="mb-flex mb-items-center mb-gap-2">
      <label class="mb-text-[10px] mb-text-gray-400 mb-w-16">{{ t('Saturazione') }}</label>
      <NumberScrubber class="mb-flex-1" :modelValue="value.saturate" :min="0" :max="200" :step="5"
        :defaultValue="100" emitAs="number" unit="%" :sliderOnFocus="false" :ariaLabel="t('Saturazione')"
        @update:modelValue="onUpdate('saturate', $event)" />
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';
import NumberScrubber from './NumberScrubber.vue';

/**
 * FieldBackdropFilter — 3 range (blur, brightness, saturate) per
 * effetto glassmorphism del wrapper.
 *
 * modelValue: oggetto { blur, brightness, saturate }.
 * Backend chiavi: tile.style.backdrop_blur / _brightness / _saturate.
 */
const props = defineProps({
  modelValue: { type: [Object, null], default: () => ({}) },
});
const emit = defineEmits(['update:modelValue']);

const value = computed(() => ({
  blur:       props.modelValue?.blur       ?? 0,
  brightness: props.modelValue?.brightness ?? 100,
  saturate:   props.modelValue?.saturate   ?? 100,
}));

function onUpdate(key, val) {
  emit('update:modelValue', { ...value.value, [key]: val });
}
</script>
