<template>
  <select
    :value="modelValue"
    @change="$emit('update:modelValue', $event.target.value)"
    class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
  >
    <option v-for="opt in options" :key="opt.value" :value="opt.value">
      {{ t(opt.label) }}
    </option>
  </select>
</template>

<script setup>
import { t } from '@/i18n';
defineProps({
  modelValue: { type: [String, Number], default: '' },
  options: { type: Array, default: () => [] },
});
defineEmits(['update:modelValue']);
</script>
