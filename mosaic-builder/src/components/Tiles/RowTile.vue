<template>
  <div class="olo-row-tile">
    <div class="olo-row-tile-label">
      <span>Row</span>
      <span class="olo-row-tile-layout">{{ settings.layout || '50-50' }}</span>
    </div>
  </div>
</template>

<script setup>
defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});
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
  color: #6B7280;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.olo-row-tile-layout {
  background: rgba(99, 102, 241, 0.15);
  color: var(--olo-color-primary, #6366F1);
  padding: 1px 6px;
  border-radius: 3px;
  font-size: 9px;
}
</style>
