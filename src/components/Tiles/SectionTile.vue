<template>
  <div class="olo-section-tile">
    <div class="olo-section-header">
      <span class="olo-section-label">{{ t('Section') }}</span>
      <span v-if="s.style && s.style !== 'default'" class="olo-section-badge">{{ s.style }}</span>
      <span v-if="s.scroll_snap" class="olo-section-badge" style="background:color-mix(in srgb, var(--olo-color-success, #15803d) 15%, transparent);color:var(--olo-color-success, #15803d)">snap</span>
    </div>
    <!-- Section children (rows) are rendered by the recursive canvas, not here -->
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';

const defaults = {
  style: 'default',
  width: 'default',
  padding: 'default',
  sticky_effect: 'none',
  sticky_top: '',
  scroll_snap: false,
  snap_dots: false,
  snap_dot_color: '#ffffff',
  snap_dot_active_color: '',
  snap_dot_position: 'right',
};

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

const s = computed(() => ({ ...defaults, ...props.settings }));
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
  color: var(--olo-color-text-soft, #6B7280);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.olo-section-badge {
  background: color-mix(in srgb, var(--olo-color-primary, #e1474f) 15%, transparent);
  color: var(--olo-color-primary, #e1474f);
  padding: 1px 6px;
  border-radius: 3px;
  font-size: 9px;
}
</style>
