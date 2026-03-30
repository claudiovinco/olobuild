<template>
  <div class="olo-svsearch-preview" :class="'olo-svsearch-preview--' + (settings.layout || 'horizontal')" :style="wrapStyle">
    <div class="olo-svsearch-preview-fields">
      <div v-if="hasFilter('valley')" class="olo-svsearch-preview-field">
        <span class="olo-svsearch-preview-label">Località</span>
        <span class="olo-svsearch-preview-input">Tutte le località</span>
      </div>
      <div v-if="hasFilter('checkin')" class="olo-svsearch-preview-field">
        <span class="olo-svsearch-preview-label">Check-in</span>
        <span class="olo-svsearch-preview-input">gg/mm/aaaa</span>
      </div>
      <div v-if="hasFilter('checkin')" class="olo-svsearch-preview-field">
        <span class="olo-svsearch-preview-label">Check-out</span>
        <span class="olo-svsearch-preview-input">gg/mm/aaaa</span>
      </div>
      <div v-if="hasFilter('guests')" class="olo-svsearch-preview-field">
        <span class="olo-svsearch-preview-label">Ospiti</span>
        <span class="olo-svsearch-preview-input">Qualsiasi</span>
      </div>
      <div v-if="hasFilter('bedrooms')" class="olo-svsearch-preview-field">
        <span class="olo-svsearch-preview-label">Camere</span>
        <span class="olo-svsearch-preview-input">Qualsiasi</span>
      </div>
      <div v-if="hasFilter('altitude')" class="olo-svsearch-preview-field">
        <span class="olo-svsearch-preview-label">Altitudine</span>
        <span class="olo-svsearch-preview-input">Qualsiasi</span>
      </div>
      <div v-if="hasFilter('type')" class="olo-svsearch-preview-field">
        <span class="olo-svsearch-preview-label">Tipologia</span>
        <span class="olo-svsearch-preview-input">Tutte</span>
      </div>
      <div v-if="hasFilter('amenities')" class="olo-svsearch-preview-field olo-svsearch-preview-field--wide">
        <span class="olo-svsearch-preview-label">Servizi</span>
        <div class="olo-svsearch-preview-pills">
          <span v-for="a in amenitiesList" :key="a" class="olo-svsearch-preview-pill">{{ a }}</span>
        </div>
      </div>
    </div>
    <div class="olo-svsearch-preview-btn" :style="btnStyle">
      {{ settings.button_text || 'Cerca' }}
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useBuilderStore } from '@/stores/builder';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const builderStore = useBuilderStore();

const visibleFilters = computed(() => {
  const raw = props.settings.visible_filters || '';
  return raw.split(',').map(s => s.trim()).filter(Boolean);
});

function hasFilter(name) {
  return visibleFilters.value.includes(name);
}

const amenitiesList = computed(() => {
  const raw = props.settings.amenities_list || '';
  return raw.split(',').map(s => s.trim()).filter(Boolean).slice(0, 8);
});

const wrapStyle = computed(() => {
  const s = props.settings;
  const style = {
    borderRadius: (s.border_radius || 8) + 'px',
    background: s.filter_bg || '#ffffff',
    border: '1px solid ' + (s.filter_border || '#e5e7eb'),
  };
  // In clean mode, apply overlap styles like the frontend
  if (builderStore.cleanMode && s.overlap) {
    const offset = Math.max(20, Math.min(200, parseInt(s.overlap_offset) || 60));
    const maxW = Math.max(600, Math.min(1400, parseInt(s.overlap_max_width) || 1100));
    style.position = 'relative';
    style.zIndex = 10;
    style.marginTop = `-${offset}px`;
    style.maxWidth = `${maxW}px`;
    style.marginLeft = 'auto';
    style.marginRight = 'auto';
    if (s.overlap_shadow !== false) {
      style.boxShadow = '0 8px 30px rgba(0,0,0,0.15)';
    }
  }
  return style;
});

const btnStyle = computed(() => {
  const s = props.settings;
  return {
    background: s.button_bg || 'var(--olo-color-primary, #6366F1)',
    color: s.button_color || '#ffffff',
  };
});
</script>

<style scoped>
.olo-svsearch-preview {
  padding: 16px;
  display: flex;
  align-items: flex-end;
  gap: 12px;
}
.olo-svsearch-preview--vertical {
  flex-direction: column;
  align-items: stretch;
}
.olo-svsearch-preview-fields {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  flex: 1;
}
.olo-svsearch-preview--vertical .olo-svsearch-preview-fields {
  flex-direction: column;
}
.olo-svsearch-preview-field {
  display: flex;
  flex-direction: column;
  gap: 3px;
  min-width: 110px;
  flex: 1;
}
.olo-svsearch-preview-field--wide {
  flex-basis: 100%;
}
.olo-svsearch-preview-label {
  font-size: 0.7em;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #888;
  font-weight: 600;
}
.olo-svsearch-preview-input {
  font-size: 0.82em;
  padding: 5px 8px;
  background: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 4px;
  color: #666;
}
.olo-svsearch-preview-pills {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
}
.olo-svsearch-preview-pill {
  font-size: 0.72em;
  padding: 2px 8px;
  background: #f3f4f6;
  border: 1px solid #e5e7eb;
  border-radius: 999px;
  color: #666;
}
.olo-svsearch-preview-btn {
  padding: 8px 24px;
  border-radius: 6px;
  font-size: 0.85em;
  font-weight: 600;
  text-align: center;
  white-space: nowrap;
  cursor: default;
}
</style>
