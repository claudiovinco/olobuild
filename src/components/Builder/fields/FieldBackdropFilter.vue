<template>
  <div class="mb-space-y-2">
    <div class="mb-flex mb-items-center mb-gap-2">
      <label class="mb-text-[10px] mb-text-gray-400 mb-w-16">Blur</label>
      <input
        type="range"
        :value="value.blur"
        @input="onUpdate('blur', parseInt($event.target.value) || 0)"
        min="0" max="30"
        class="mb-flex-1"
      />
      <span class="mb-text-xs mb-text-gray-400 mb-w-10 mb-text-right">{{ value.blur }}px</span>
    </div>
    <div class="mb-flex mb-items-center mb-gap-2">
      <label class="mb-text-[10px] mb-text-gray-400 mb-w-16">Luminosità</label>
      <input
        type="range"
        :value="value.brightness"
        @input="onUpdate('brightness', parseInt($event.target.value) || 100)"
        min="0" max="200" step="5"
        class="mb-flex-1"
      />
      <span class="mb-text-xs mb-text-gray-400 mb-w-10 mb-text-right">{{ value.brightness }}%</span>
    </div>
    <div class="mb-flex mb-items-center mb-gap-2">
      <label class="mb-text-[10px] mb-text-gray-400 mb-w-16">Saturazione</label>
      <input
        type="range"
        :value="value.saturate"
        @input="onUpdate('saturate', parseInt($event.target.value) || 100)"
        min="0" max="200" step="5"
        class="mb-flex-1"
      />
      <span class="mb-text-xs mb-text-gray-400 mb-w-10 mb-text-right">{{ value.saturate }}%</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

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
