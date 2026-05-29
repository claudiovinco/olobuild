<template>
  <div class="olo-breadcrumbs">
    <nav class="mb-flex mb-items-center mb-gap-2 mb-text-sm" :style="{ color: linkResolved }">
      <template v-if="s.show_home !== false">
        <a href="#" class="olo-bc-link mb-no-underline hover:mb-underline" :style="{ color: 'var(--olo-color-primary, #e1474f)' }" data-olo-editable="home_label">{{ s.home_label }}</a>
        <span :style="{ color: 'var(--olo-color-text-faint, #94a3b8)' }" data-olo-editable="separator">{{ s.separator }}</span>
      </template>
      <a href="#" class="olo-bc-link mb-no-underline hover:mb-underline" :style="{ color: 'var(--olo-color-primary, #e1474f)' }">{{ t('Categoria') }}</a>
      <span :style="{ color: 'var(--olo-color-text-faint, #94a3b8)' }">{{ s.separator }}</span>
      <template v-if="s.show_current !== false">
        <span :style="{ color: 'var(--olo-color-text, #374151)' }">{{ t('Pagina corrente') }}</span>
      </template>
    </nav>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';

const defaults = {
  separator: '/',
  home_label: 'Home',
  show_home: true,
  show_current: true,
  shadow: 'none',
  border_width: '0',
  border_color: '#e5e7eb',
  border_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
};

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const s = computed(() => ({ ...defaults, ...props.settings }));
// Neutro nudo (mb-text-gray-400) → token testo soft
const linkResolved = 'var(--olo-color-text-soft, #6b7280)';
</script>

<style scoped>
.olo-breadcrumbs {
  padding: 8px 0;
  min-height: 32px;
}
/* a11y tastiera: anello di focus visibile sui link del breadcrumb */
.olo-bc-link:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
  border-radius: 3px;
}
</style>
