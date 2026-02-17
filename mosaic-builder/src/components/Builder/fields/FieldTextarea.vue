<template>
  <textarea
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
    rows="3"
    class="mb-w-full mb-bg-white mb-border mb-border-gray-300 mb-rounded-md mb-px-2 mb-py-1.5 mb-text-sm mb-text-gray-900 mb-resize-y"
  />
</template>

<script setup>
defineProps({
  modelValue: { type: String, default: '' },
});
defineEmits(['update:modelValue']);
</script>
