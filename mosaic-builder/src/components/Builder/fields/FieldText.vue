<template>
  <input
    type="text"
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
    @keydown.enter="$emit('confirm', $event.target.value)"
    @blur="$emit('confirm', $event.target.value)"
    class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900"
  />
</template>

<script setup>
defineProps({
  modelValue: { type: [String, Number], default: '' },
});
defineEmits(['update:modelValue', 'confirm']);
</script>
