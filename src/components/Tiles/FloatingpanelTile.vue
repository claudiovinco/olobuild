<template>
  <div class="olo-floatingpanel-preview" :style="previewStyle">
    <div class="ofp-header">
      <span class="ofp-icon">📌</span>
      <span class="ofp-name">{{ t('Pannello flottante') }}</span>
      <span class="ofp-pos">{{ placementIcon }} {{ placementLabel }}</span>
      <span v-if="settings.trigger_mode === 'button'" class="ofp-trigger">{{ t('con trigger') }}</span>
    </div>
    <div class="ofp-meta">
      <span class="ofp-pill">{{ positionLabel }}</span>
      <span v-if="childCount > 0" class="ofp-count">{{ childCount }} {{ childCount === 1 ? t('elemento') : t('elementi') }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useTilesStore } from '../../stores/tiles';
import { t } from '@/i18n';

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

const placementIcon = computed(() => placementMap[props.settings.placement]?.icon || '↘');
const placementLabel = computed(() => placementMap[props.settings.placement]?.label || 'Basso destra');

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
  return style;
});
</script>

<style scoped>
.olo-floatingpanel-preview {
  padding: 10px 14px;
  border: 2px dashed #94a3b8;
  border-radius: 6px;
  background: #f8fafc;
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.ofp-header {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}
.ofp-icon { font-size: 16px; }
.ofp-name {
  font-size: 13px;
  font-weight: 600;
  color: #334155;
}
.ofp-pos {
  font-size: 11px;
  color: #64748b;
  margin-left: 4px;
}
.ofp-trigger {
  font-size: 10px;
  color: var(--olo-color-primary, #e1474f);
  background: color-mix(in srgb, var(--olo-color-primary, #e1474f) 12%, #fff);
  padding: 2px 8px;
  border-radius: 10px;
  font-weight: 600;
}

.ofp-meta {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 10px;
}
.ofp-pill {
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #94a3b8;
  font-weight: 600;
}
.ofp-count {
  color: #059669;
  font-weight: 600;
}
</style>
