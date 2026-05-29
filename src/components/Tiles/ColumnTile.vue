<template>
  <div class="olo-column-tile">
    <!-- Column children (elements) are rendered by the recursive canvas, not here -->
    <div v-if="!hasChildren" class="olo-column-empty">
      <span class="olo-column-plus">+</span>
      <span>{{ t('Drop element here') }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useTilesStore } from '@/stores/tiles';
import { t } from '@/i18n';

const defaults = {
  width_default: '',
  width_small: '',
  width_medium: '',
  width_large: '',
};

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

const s = computed(() => ({ ...defaults, ...props.settings }));

const tilesStore = useTilesStore();

const hasChildren = computed(() => {
  const tile = tilesStore.getTileById(props.tileId);
  return tile && Array.isArray(tile.children) && tile.children.length > 0;
});
</script>

<style scoped>
.olo-column-tile {
  min-height: 60px;
}
.olo-column-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 80px;
  color: var(--olo-color-text-soft, #6B7280);
  font-size: 11px;
  gap: 2px;
}
.olo-column-plus {
  font-size: 18px;
  line-height: 1;
}
</style>
