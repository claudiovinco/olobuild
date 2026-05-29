<template>
  <div :class="['olo-collapse-section', macro ? 'olo-collapse-section--macro' : '']">
    <button
      @click="open = !open"
      :aria-expanded="open"
      class="collapse-head mb-flex mb-items-center mb-justify-between mb-w-full mb-rounded-md mb-transition-colors mb-px-3 mb-py-2 mb-text-[11px] mb-font-bold mb-uppercase mb-tracking-wider"
      :class="open ? 'collapse-head--open' : ''"
    >
      <span class="mb-flex-1">{{ title }}</span>
      <slot name="header-right" />
      <svg
        :class="['mb-transition-transform mb-duration-200', open ? 'mb-rotate-180' : '']"
        width="12" height="12" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
        style="color: rgba(255,255,255,0.6)"
      >
        <path d="M3 4.5L6 7.5L9 4.5" />
      </svg>
    </button>
    <div v-show="open" :class="['mb-pt-3 mb-pb-1 mb-space-y-3', macro ? 'olo-collapse-body--macro' : '']">
      <slot />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const props = defineProps({
  title: { type: String, required: true },
  defaultOpen: { type: Boolean, default: false },
  // macro: true = accordion principale (top-level); false (default) = sub-accordion
  macro: { type: Boolean, default: false },
});

const open = ref(props.defaultOpen);
</script>

<style scoped>
/* Stile flat uniforme per tutti gli accordion (minimale, no gradient) */
.collapse-head {
  background: rgba(0, 0, 0, 0.32);
  color: rgba(255, 255, 255, 0.85);
}
.collapse-head:hover {
  background: rgba(0, 0, 0, 0.42);
  color: #fff;
}
.collapse-head--open {
  background: rgba(0, 0, 0, 0.5);
  color: #fff;
}

/* Macro accordion: stesso stile di header, solo separator visivo via spaziatura.
 * NESSUN gradient, NESSUN bordo accent — gerarchia data solo dal body indentato. */
.olo-collapse-section--macro {
  margin-top: 0.5rem;
}
.olo-collapse-section--macro:first-child {
  margin-top: 0;
}
/* Sub-accordion dentro una macro: indentati con border-left sottile per gerarchia */
.olo-collapse-body--macro {
  padding-left: 0.5rem;
  border-left: 1px solid rgba(255, 255, 255, 0.08);
  margin-left: 0.25rem;
}
</style>
