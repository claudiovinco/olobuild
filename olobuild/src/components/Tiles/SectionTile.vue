<template>
  <div class="olo-section-tile">
    <div class="olo-section-header">
      <span class="olo-section-label">Section</span>
      <span v-if="settings.style && settings.style !== 'default'" class="olo-section-badge">{{ settings.style }}</span>
    </div>
    <!-- Section children (rows) are rendered by the recursive canvas, not here -->
  </div>
</template>

<script setup>
defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});
</script>

<style scoped>
.olo-section-tile {
  min-height: 20px;
}
.olo-section-header {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 4px 8px;
  font-size: 10px;
  color: #6B7280;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.olo-section-badge {
  background: rgba(99, 102, 241, 0.15);
  color: var(--olo-color-primary, #6366F1);
  padding: 1px 6px;
  border-radius: 3px;
  font-size: 9px;
}
</style>
