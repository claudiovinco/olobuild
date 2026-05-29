<template>
  <div class="olo-row-tile">
    <div class="olo-row-tile-label">
      <span>{{ t('Row') }}</span>
      <span class="olo-row-tile-layout">{{ s.layout }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';

const defaults = {
  layout: '50-50',
  gap: '16',
  column_gap: 'default',
  vertical_align: 'stretch',
  stack_mobile: true,
};

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

const s = computed(() => ({ ...defaults, ...props.settings }));
</script>

<style scoped>
.olo-row-tile {
  min-height: 20px;
}
.olo-row-tile-label {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 4px 8px;
  font-size: 10px;
  color: var(--olo-color-text-soft, #6B7280);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.olo-row-tile-layout {
  background: color-mix(in srgb, var(--olo-color-primary, #e1474f) 15%, transparent);
  color: var(--olo-color-primary, #e1474f);
  padding: 1px 6px;
  border-radius: 3px;
  font-size: 9px;
}
</style>
