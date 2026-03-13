<template>
  <div class="olo-collapse-section">
    <button
      @click="open = !open"
      :aria-expanded="open"
      class="collapse-head mb-flex mb-items-center mb-justify-between mb-w-full mb-px-3 mb-py-2 mb-text-[11px] mb-font-bold mb-uppercase mb-tracking-wider mb-rounded-md mb-transition-colors"
      :class="open ? 'collapse-head--open' : ''"
    >
      <span>{{ title }}</span>
      <svg
        :class="['mb-transition-transform mb-duration-200', open ? 'mb-rotate-180' : '']"
        width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
        style="color: rgba(255,255,255,0.6)"
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

<style scoped>
.collapse-head {
  background: rgba(0, 0, 0, 0.35);
  color: rgba(255, 255, 255, 0.85);
  backdrop-filter: blur(4px);
}
.collapse-head:hover {
  background: rgba(0, 0, 0, 0.42);
  color: #fff;
}
.collapse-head--open {
  background: rgba(0, 0, 0, 0.45);
  color: #fff;
}
</style>
