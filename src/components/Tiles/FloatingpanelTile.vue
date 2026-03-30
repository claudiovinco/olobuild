<template>
  <div class="olo-floatingpanel-preview" :style="previewStyle">
    <!-- Position badge -->
    <div class="ofp-badge">
      <span class="ofp-badge-icon">{{ placementIcon }}</span>
      <span class="ofp-badge-label">{{ placementLabel }}</span>
    </div>

    <!-- Trigger mode indicator -->
    <div v-if="settings.trigger_mode === 'button'" class="ofp-trigger-badge">
      🔘 Con trigger
    </div>

    <!-- Children info -->
    <div class="ofp-children-info">
      <span v-if="childCount > 0" class="ofp-children-count">{{ childCount }} {{ childCount === 1 ? 'elemento' : 'elementi' }}</span>
      <span v-else class="ofp-children-empty">Trascina tile qui ↓</span>
    </div>

    <!-- Position type -->
    <div class="ofp-pos-type">{{ positionLabel }}</div>
  </div>
</template>

<script setup>
import { computed, inject } from 'vue';
import { useTilesStore } from '../../stores/tiles';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

const tilesStore = useTilesStore();

const childCount = computed(() => {
  if (!props.tileId) return 0;
  const tile = tilesStore.findNodeById(tilesStore.canvasTiles, props.tileId);
  if (tile && Array.isArray(tile.children)) return tile.children.length;
  return 0;
});

const placementMap = {
  'top-left':      { icon: '↖', label: 'Alto sinistra' },
  'top-center':    { icon: '↑', label: 'Alto centro' },
  'top-right':     { icon: '↗', label: 'Alto destra' },
  'center-left':   { icon: '←', label: 'Centro sinistra' },
  'center':        { icon: '●', label: 'Centro' },
  'center-right':  { icon: '→', label: 'Centro destra' },
  'bottom-left':   { icon: '↙', label: 'Basso sinistra' },
  'bottom-center': { icon: '↓', label: 'Basso centro' },
  'bottom-right':  { icon: '↘', label: 'Basso destra' },
  'custom':        { icon: '✎', label: 'Personalizzato' },
};

const placementIcon = computed(() => {
  return placementMap[props.settings.placement]?.icon || '↘';
});
const placementLabel = computed(() => {
  return placementMap[props.settings.placement]?.label || 'Basso destra';
});

const positionLabel = computed(() => {
  const map = { fixed: 'Fisso', absolute: 'Assoluto', sticky: 'Appiccicoso' };
  return map[props.settings.position] || 'Fisso';
});

const previewStyle = computed(() => {
  const s = props.settings;
  const style = {};
  if (s.bg_color) style.background = s.bg_color;
  if (parseInt(s.border_radius)) style.borderRadius = parseInt(s.border_radius) + 'px';
  if (parseInt(s.border_width) > 0) {
    style.border = `${parseInt(s.border_width)}px solid ${s.border_color || '#e0e0e0'}`;
  }
  if (s.shadow && s.shadow !== 'false') {
    style.boxShadow = `0 ${parseInt(s.shadow_y) || 4}px ${parseInt(s.shadow_blur) || 20}px ${s.shadow_color || 'rgba(0,0,0,0.15)'}`;
  }
  return style;
});
</script>

<style scoped>
.olo-floatingpanel-preview {
  padding: 16px;
  min-height: 80px;
  border: 2px dashed #94a3b8;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  position: relative;
  background: #ffffff;
}

.ofp-badge {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 14px;
  font-weight: 600;
  color: #334155;
}
.ofp-badge-icon {
  font-size: 18px;
}

.ofp-trigger-badge {
  font-size: 11px;
  color: #7c3aed;
  background: #ede9fe;
  padding: 2px 8px;
  border-radius: 10px;
}

.ofp-children-info {
  font-size: 12px;
  color: #64748b;
}
.ofp-children-empty {
  color: #94a3b8;
  font-style: italic;
}
.ofp-children-count {
  color: #059669;
  font-weight: 500;
}

.ofp-pos-type {
  font-size: 10px;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
</style>
