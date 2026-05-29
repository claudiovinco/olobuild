<template>
  <div class="mb-flex mb-gap-2">
    <div class="mb-flex-1">
      <label class="mb-text-[10px] mb-text-gray-400">H</label>
      <input
        type="number"
        :value="value.h"
        @input="onUpdate('h', parseInt($event.target.value) || 0)"
        class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-1 mb-text-xs mb-text-gray-900"
      />
    </div>
    <div class="mb-flex-1">
      <label class="mb-text-[10px] mb-text-gray-400">V</label>
      <input
        type="number"
        :value="value.v"
        @input="onUpdate('v', parseInt($event.target.value) || 0)"
        class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-1 mb-text-xs mb-text-gray-900"
      />
    </div>
    <div class="mb-flex-1">
      <label class="mb-text-[10px] mb-text-gray-400">Blur</label>
      <input
        type="number"
        :value="value.blur"
        @input="onUpdate('blur', Math.max(0, parseInt($event.target.value) || 0))"
        min="0"
        class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded mb-px-2 mb-py-1 mb-text-xs mb-text-gray-900"
      />
    </div>
  </div>
  <div class="mb-mt-1.5">
    <label class="mb-text-[10px] mb-text-gray-400 mb-block mb-mb-1">Colore</label>
    <FieldColor
      :modelValue="value.color || '#000000'"
      @update:modelValue="onUpdate('color', $event)"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import FieldColor from './FieldColor.vue';

/**
 * FieldTextShadow — UI per la 4-tupla (h, v, blur, color) di text-shadow.
 *
 * modelValue: oggetto { h, v, blur, color } oppure null/{} per stato non impostato.
 * Le chiavi sono leggere — l'integrazione con il backend usa
 * tile.style.text_shadow_h / _v / _blur / _color come 4 chiavi piatte.
 * Il caller si occupa di mappare l'oggetto a 4 chiavi piatte (o usare un wrapper).
 */
const props = defineProps({
  modelValue: { type: [Object, null], default: () => ({}) },
});
const emit = defineEmits(['update:modelValue']);

const value = computed(() => ({
  h:     props.modelValue?.h     ?? 0,
  v:     props.modelValue?.v     ?? 0,
  blur:  props.modelValue?.blur  ?? 0,
  color: props.modelValue?.color ?? '',
}));

function onUpdate(key, val) {
  emit('update:modelValue', { ...value.value, [key]: val });
}
</script>
