<template>
  <div>
    <label class="mb-block mb-text-xs mb-font-medium mb-text-gray-400 mb-mb-1">
      {{ t(field.label) }}
    </label>

    <!-- Range -->
    <div v-if="field.type === 'range'" class="mb-flex mb-items-center mb-gap-2">
      <input
        type="range"
        :value="value"
        @input="onUpdate(Number($event.target.value))"
        :min="field.min ?? 0"
        :max="field.max ?? 100"
        :step="field.step ?? 1"
        class="mb-flex-1"
      />
      <span class="mb-text-xs mb-text-gray-400 mb-w-12 mb-text-right">{{ value }}{{ field.unit || '' }}</span>
    </div>

    <!-- Select -->
    <select
      v-else-if="field.type === 'select'"
      :value="value"
      @change="onUpdate($event.target.value)"
      class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
    >
      <option v-for="opt in (field.options || [])" :key="opt.value" :value="opt.value">{{ t(opt.label) }}</option>
    </select>

    <!-- Text fallback -->
    <input
      v-else
      type="text"
      :value="value"
      @input="onUpdate($event.target.value)"
      class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';

/**
 * StyleNestedField — gestisce field con chiave "path" annidata via "."
 *  es. field.key = 'transition.duration' → legge tile.style.transition.duration
 *
 * Emette update di tipo 'transition' (per ora) — il path completo è inferito dal segmento iniziale.
 * Se il primo segmento è 'transition', emette { type: 'transition', key: 'duration', value }.
 * Altri prefix (futuri): mappare in StyleFieldsRenderer.
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

function onUpdate(val) {
  if (root.value === 'transition') {
    emit('update', { type: 'transition', key: sub.value, value: val });
  } else {
    // Generic: emit nested path
    emit('update', { type: 'nested', path: props.field.key, value: val });
  }
}
</script>
