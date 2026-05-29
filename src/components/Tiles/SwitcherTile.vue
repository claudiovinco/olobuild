<template>
  <div style="min-height: 80px;">
    <!-- Navigation -->
    <div :class="containerClass">
      <div :class="navWrapClass">
        <div class="mb-flex" :class="s.vertical ? 'mb-flex-col mb-gap-1' : ''" :style="navRowStyle">
          <button
            v-for="(item, i) in items"
            :key="i"
            type="button"
            class="olo-switcher-tab"
            @click.stop="activeIndex = i"
            :class="[
              'mb-px-4 mb-py-2 mb-text-sm mb-bg-transparent mb-transition-colors',
              i === activeIndex ? 'mb-font-semibold' : 'mb-font-normal'
            ]"
            :style="tabStyle(i)"
            :data-olo-editable="'items.' + i + '.title'"
          >{{ item.title }}</button>
        </div>
        <div class="mb-text-xs mb-mt-1 mb-px-1" :style="{ color: TOKENS.textFaint }">{{ navLabel }}</div>
      </div>

      <!-- Content -->
      <div :class="contentWrapClass">
        <div v-if="items[activeIndex]" class="mb-p-4 mb-leading-relaxed mb-text-sm" :style="{ color: TOKENS.textSoft, whiteSpace: 'pre-wrap' }" :data-olo-editable="'items.' + activeIndex + '.content'" data-olo-multiline>{{ items[activeIndex].content }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { TOKENS } from '@/composables/oloTileDefaults';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const activeIndex = ref(0);

const defaults = {
  nav_style: 'tab',
  animation: '',
  vertical: false,
};
const s = computed(() => ({ ...defaults, ...props.settings }));

const items = computed(() => {
  const raw = s.value.items;
  if (Array.isArray(raw) && raw.length) return raw;
  return [
    { title: 'Tab 1', content: 'Tab content 1.' },
    { title: 'Tab 2', content: 'Tab content 2.' },
  ];
});

const navLabel = computed(() => {
  const style = s.value.nav_style || 'tab';
  const labels = { tab: 'Tab', subnav: 'Subnav', 'subnav-pill': 'Subnav Pill' };
  return labels[style] || 'Tab';
});

const containerClass = computed(() => s.value.vertical ? 'mb-flex mb-gap-4' : '');
const navWrapClass = computed(() => s.value.vertical ? 'mb-w-1/4 mb-flex-shrink-0' : '');
const contentWrapClass = computed(() => s.value.vertical ? 'mb-flex-1' : '');

// Riga nav (orizzontale): bordo inferiore token-first invece del grigio nudo
const navRowStyle = computed(() => s.value.vertical
  ? {}
  : { borderBottom: `2px solid ${TOKENS.border}` });

function tabStyle(i) {
  const active = i === activeIndex.value;
  // TOKEN-FIRST: indicatore → primary (era #e1474f indaco off-brand); testo via token
  const indicator = `var(--olo-color-primary, #e1474f)`;
  const base = {
    color: active ? TOKENS.text : TOKENS.textSoft,
  };
  if (s.value.vertical) {
    return {
      ...base,
      borderLeft: active ? `2px solid ${indicator}` : '2px solid transparent',
      borderBottom: 'none',
      textAlign: 'left',
      width: '100%',
    };
  }
  return {
    ...base,
    borderBottom: active ? `2px solid ${indicator}` : '2px solid transparent',
    marginBottom: '-2px',
  };
}
</script>

<style scoped>
.olo-switcher-tab:hover { color: var(--olo-color-text, #1f2937); }
/* a11y: anello di focus visibile da tastiera sul tab */
.olo-switcher-tab:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
  border-radius: 4px;
}
</style>
