<template>
  <div class="mb-border-b mb-border-gray-700">
    <button
      @click="open = !open"
      class="mb-flex mb-items-center mb-justify-between mb-w-full mb-py-2.5 mb-text-xs mb-font-semibold mb-text-gray-300 hover:mb-text-white mb-transition-colors"
    >
      <span>{{ title }}</span>
      <span
        :class="['mb-transition-transform mb-duration-200', open ? 'mb-rotate-180' : '']"
        class="mb-text-gray-500"
      >&#9660;</span>
    </button>
    <div v-show="open" class="mb-pb-3">
      <slot />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  title: { type: String, required: true },
  defaultOpen: { type: Boolean, default: false },
});

const open = ref(props.defaultOpen);
</script>
