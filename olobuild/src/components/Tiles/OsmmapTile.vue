<template>
  <div
    class="olo-osmmap-preview"
    :style="{
      height: s.height + 'px',
      borderRadius: s.border_radius + 'px',
      backgroundColor: '#E5E7EB',
      backgroundImage: 'linear-gradient(45deg, #D1D5DB 25%, transparent 25%), linear-gradient(-45deg, #D1D5DB 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #D1D5DB 75%), linear-gradient(-45deg, transparent 75%, #D1D5DB 75%)',
      backgroundSize: '20px 20px',
      backgroundPosition: '0 0, 0 10px, 10px -10px, -10px 0',
      display: 'flex',
      flexDirection: 'column',
      alignItems: 'center',
      justifyContent: 'center',
      gap: '8px',
      overflow: 'hidden',
      position: 'relative',
      border: '1px solid #D1D5DB',
    }"
  >
    <!-- Pin icon -->
    <span
      v-html="pinSvg"
      :style="{ color: s.marker_color || '#e74c3c' }"
    ></span>

    <!-- Map info -->
    <div style="text-align: center; color: #374151; font-size: 13px; font-weight: 500;">
      OpenStreetMap
    </div>
    <div style="text-align: center; color: #6B7280; font-size: 11px; font-family: monospace;">
      {{ s.latitude }}, {{ s.longitude }} &middot; zoom {{ s.zoom }}
    </div>

    <!-- Layer label -->
    <div
      style="position: absolute; bottom: 8px; right: 8px; background: rgba(0,0,0,0.6); color: #fff; font-size: 10px; padding: 2px 6px; border-radius: 3px;"
    >
      {{ layerLabel }}
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

const defaults = {
  latitude: '45.4642',
  longitude: '9.1900',
  zoom: '13',
  height: '400',
  marker: true,
  marker_popup: 'La nostra sede',
  scroll_wheel_zoom: false,
  dragging: true,
  tile_layer: 'standard',
  border_radius: '0',
  marker_color: '#e74c3c',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const pinSvg = computed(() => {
  return `<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>`;
});

const layerLabels = {
  standard: 'Standard',
  toner: 'Toner',
  watercolor: 'Acquerello',
  dark: 'Scuro',
};

const layerLabel = computed(() => layerLabels[s.value.tile_layer] || 'Standard');
</script>
