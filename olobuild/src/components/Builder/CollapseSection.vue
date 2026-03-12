<template>
  <div class="olo-collapse-section">
    <button
      @click="open = !open"
      :aria-expanded="open"
      class="mb-flex mb-items-center mb-justify-between mb-w-full mb-px-3 mb-py-2 mb-text-[11px] mb-font-bold mb-uppercase mb-tracking-wider mb-rounded-md mb-transition-colors"
      :class="open
        ? 'mb-bg-gray-600/60 mb-text-gray-200'
        : 'mb-bg-gray-700/40 mb-text-gray-400 hover:mb-bg-gray-700/70 hover:mb-text-gray-300'"
    >
      <span>{{ title }}</span>
      <svg
        :class="['mb-transition-transform mb-duration-200 mb-text-gray-500', open ? 'mb-rotate-180' : '']"
        width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
      >
        <path d="M3 4.5L6 7.5L9 4.5" />
      </svg>
    </button>
    <div v-show="open" class="mb-pt-3 mb-pb-1 mb-space-y-3">
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
