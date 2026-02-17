<template>
  <button
    @click="$emit('update:modelValue', !modelValue)"
    :class="[
      'mb-relative mb-w-10 mb-h-5 mb-rounded-full mb-transition-colors',
      modelValue ? 'mb-bg-primary-600' : 'mb-bg-gray-600'
    ]"
  >
    <span
      :class="[
        'mb-absolute mb-top-0.5 mb-w-4 mb-h-4 mb-rounded-full mb-bg-white mb-transition-transform',
        modelValue ? 'mb-left-5' : 'mb-left-0.5'
      ]"
    ></span>
  </button>
</template>

<script setup>
defineProps({
  modelValue: { type: Boolean, default: false },
});
defineEmits(['update:modelValue']);
</script>
