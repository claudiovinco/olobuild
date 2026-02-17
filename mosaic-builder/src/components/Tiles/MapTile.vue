<template>
  <div class="mb-rounded-lg mb-overflow-hidden mb-bg-gray-800" style="min-height: 80px">
    <!-- Single address mode: iframe -->
    <template v-if="settings.mode !== 'locations'">
      <iframe
        v-if="mapSrc"
        :src="mapSrc"
        :style="{ width: '100%', height: (settings.height || 400) + 'px', border: 0 }"
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
      ></iframe>
      <div
        v-else
        class="mb-flex mb-flex-col mb-items-center mb-justify-center mb-text-gray-500"
        :style="{ height: (settings.height || 400) + 'px' }"
      >
        <span class="mb-text-4xl mb-mb-2">&#x1F5FA;</span>
        <span class="mb-text-sm">Inserisci un indirizzo o coordinate</span>
      </div>
    </template>

    <!-- Locations mode: informative placeholder -->
    <div
      v-else
      class="mb-flex mb-flex-col mb-items-center mb-justify-center mb-text-gray-400 mb-gap-2 mb-p-4"
      :style="{ height: (settings.height || 400) + 'px' }"
    >
      <span class="mb-text-5xl">&#x1F4CD;</span>
      <span class="mb-text-sm mb-font-semibold mb-text-gray-300">Leaflet Locations Map</span>
      <div class="mb-text-xs mb-text-center mb-space-y-1 mb-text-gray-500">
        <div>Post Type: <span class="mb-text-gray-300">{{ settings.loc_post_type || 'location' }}</span></div>
        <div>ACF Field: <span class="mb-text-gray-300">{{ settings.loc_osm_field || 'location_map' }}</span></div>
        <div v-if="settings.loc_taxonomy">Taxonomy: <span class="mb-text-gray-300">{{ settings.loc_taxonomy }}</span></div>
        <div>
          Cluster: <span class="mb-text-gray-300">{{ settings.loc_cluster !== false ? 'S\u00EC' : 'No' }}</span>
          &middot; Filtri: <span class="mb-text-gray-300">{{ settings.loc_show_filters ? 'S\u00EC' : 'No' }}</span>
        </div>
      </div>
      <span class="mb-text-[10px] mb-text-gray-600 mb-mt-1">Mappa interattiva renderizzata nel frontend</span>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const mapSrc = computed(() => {
  const address = props.settings.address || '';
  if (!address) return '';

  const zoom = parseInt(props.settings.zoom) || 13;

  // Try coordinates
  const coordMatch = address.match(/^(-?\d+\.?\d*)\s*,\s*(-?\d+\.?\d*)$/);
  if (coordMatch) {
    const lat = parseFloat(coordMatch[1]);
    const lng = parseFloat(coordMatch[2]);
    return `https://www.openstreetmap.org/export/embed.html?bbox=${lng - 0.02},${lat - 0.01},${lng + 0.02},${lat + 0.01}&layer=mapnik&marker=${lat},${lng}`;
  }

  // Fallback: default coords (Rome)
  const lat = 41.9028;
  const lng = 12.4964;
  const delta = 0.05 / Math.max(1, zoom / 10);
  return `https://www.openstreetmap.org/export/embed.html?bbox=${lng - delta},${lat - delta},${lng + delta},${lat + delta}&layer=mapnik`;
});
</script>
