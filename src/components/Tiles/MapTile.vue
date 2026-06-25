<template>
  <div class="mb-rounded-lg mb-overflow-hidden mb-bg-gray-800" style="min-height: 80px">
    <!-- Single address mode: Leaflet interactive -->
    <template v-if="settings.mode === 'single' || (!settings.mode)">
      <div
        :style="{
          height: (parseInt(settings.height) || 400) + 'px',
          borderRadius: (parseInt(settings.border_radius) || 0) + 'px',
          overflow: 'hidden',
          position: 'relative',
          border: '1px solid var(--olo-color-border, #d1d5db)',
        }"
      >
        <iframe
          ref="iframeRef"
          :srcdoc="mapHtml"
          :style="{
            width: '100%',
            height: '100%',
            border: 'none',
            pointerEvents: editMode ? 'auto' : 'none',
          }"
          sandbox="allow-scripts allow-same-origin"
        ></iframe>

        <!-- Edit mode toggle -->
        <button
          class="olo-map-edit-btn"
          :style="{
            position: 'absolute',
            top: '8px',
            left: '8px',
            background: editMode ? '#dc2626' : 'rgba(0,0,0,0.6)',
            color: '#fff',
            fontSize: '11px',
            padding: '4px 8px',
            borderRadius: '4px',
            border: 'none',
            cursor: 'pointer',
            zIndex: 2,
            display: 'flex',
            alignItems: 'center',
            gap: '4px',
            transition: 'background 0.2s',
          }"
          @click.stop="toggleEdit"
          :title="editMode ? t('Esci dalla modalità posizionamento') : t('Clicca o trascina il marker sulla mappa')"
        >
          <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
          {{ editMode ? t('Fine') : t('Posiziona') }}
        </button>

        <!-- Layer label overlay -->
        <div
          style="position: absolute; bottom: 8px; right: 8px; background: rgba(0,0,0,0.6); color: #fff; font-size: 10px; padding: 2px 6px; border-radius: 3px; pointer-events: none; z-index: 1;"
        >
          {{ layerLabel }}
        </div>

        <!-- Coordinates display when editing -->
        <div
          v-if="editMode"
          style="position: absolute; bottom: 8px; left: 8px; background: rgba(0,0,0,0.7); color: #10b981; font-size: 10px; padding: 3px 8px; border-radius: 3px; pointer-events: none; z-index: 1; font-family: monospace;"
        >
          {{ s.latitude }}, {{ s.longitude }}
        </div>
      </div>
    </template>

    <!-- Dynamic service mode: informative placeholder -->
    <div
      v-else-if="settings.mode === 'dynamic_service'"
      class="mb-flex mb-flex-col mb-items-center mb-justify-center mb-text-gray-400 mb-gap-2 mb-p-4"
      :style="{ height: (settings.height || 400) + 'px' }"
    >
      <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
      <span class="mb-text-sm mb-font-semibold mb-text-gray-300">{{ t('Mappa Servizio Dinamico') }}</span>
      <div class="mb-text-xs mb-text-center mb-text-gray-500">
        <div>{{ t('Legge GPS dal servizio corrente') }}</div>
        <div>Zoom: <span class="mb-text-gray-300">{{ settings.zoom || 13 }}</span></div>
      </div>
      <span class="mb-text-[10px] mb-text-gray-600 mb-mt-1">{{ t('Mappa renderizzata nel frontend con lat/lng dal post meta') }}</span>
    </div>

    <!-- Services mode: informative placeholder -->
    <div
      v-else-if="settings.mode === 'services'"
      class="mb-flex mb-flex-col mb-items-center mb-justify-center mb-text-gray-400 mb-gap-2 mb-p-4"
      :style="{ height: (settings.height || 400) + 'px' }"
    >
      <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="m3 20 5.5-9 4 6 3-4.5L21 20H3z"/><circle cx="8" cy="6" r="2"/></svg>
      <span class="mb-text-sm mb-font-semibold mb-text-gray-300">{{ t('Mappa Servizi (Olo Booking)') }}</span>
      <div class="mb-text-xs mb-text-center mb-space-y-1 mb-text-gray-500">
        <div>{{ t('Modalit\u00e0') }}: <span class="mb-text-gray-300">{{ settings.svc_booking_mode || 'tutti' }}</span></div>
        <div>
          {{ t('Filtri') }}:
          <span class="mb-text-gray-300">
            {{ [
              settings.svc_show_altitude_filter !== false ? t('Altitudine') : '',
              settings.svc_show_locality_filter !== false ? t('Localit\u00e0') : '',
              settings.svc_show_guests_filter !== false ? t('Ospiti') : '',
              settings.svc_show_price_filter !== false ? t('Prezzo') : '',
              settings.svc_show_bedrooms_filter !== false ? t('Camere') : '',
              settings.svc_show_amenities_filter !== false ? 'Amenities' : '',
            ].filter(Boolean).join(', ') || t('Nessuno') }}
          </span>
        </div>
        <div>Cluster: <span class="mb-text-gray-300">{{ settings.svc_cluster !== false ? t('S\u00ec') : 'No' }}</span> &middot; {{ t('Stile') }}: <span class="mb-text-gray-300">{{ settings.svc_tile_layer || 'positron' }}</span></div>
      </div>
      <span class="mb-text-[10px] mb-text-gray-600 mb-mt-1">{{ t('Mappa interattiva con marker servizi e filtri combinati') }}</span>
    </div>

    <!-- Locations mode: informative placeholder -->
    <div
      v-else
      class="mb-flex mb-flex-col mb-items-center mb-justify-center mb-text-gray-400 mb-gap-2 mb-p-4"
      :style="{ height: (settings.height || 400) + 'px' }"
    >
      <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
      <span class="mb-text-sm mb-font-semibold mb-text-gray-300">Leaflet Locations Map</span>
      <div class="mb-text-xs mb-text-center mb-space-y-1 mb-text-gray-500">
        <div>Post Type: <span class="mb-text-gray-300">{{ settings.loc_post_type || 'location' }}</span></div>
        <div>ACF Field: <span class="mb-text-gray-300">{{ settings.loc_osm_field || 'location_map' }}</span></div>
        <div v-if="settings.loc_taxonomy">Taxonomy: <span class="mb-text-gray-300">{{ settings.loc_taxonomy }}</span></div>
        <div>
          Cluster: <span class="mb-text-gray-300">{{ settings.loc_cluster !== false ? t('S\u00EC') : 'No' }}</span>
          &middot; {{ t('Filtri') }}: <span class="mb-text-gray-300">{{ settings.loc_show_filters ? t('S\u00EC') : 'No' }}</span>
        </div>
      </div>
      <span class="mb-text-[10px] mb-text-gray-600 mb-mt-1">{{ t('Mappa interattiva renderizzata nel frontend') }}</span>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { useTilesStore } from '@/stores/tiles';
import { t } from '@/i18n';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

const tilesStore = useTilesStore();
const iframeRef = ref(null);
const editMode = ref(false);

const defaults = {
  latitude: '41.9028',
  longitude: '12.4964',
  zoom: '13',
  height: '400',
  marker: true,
  marker_popup: '',
  marker_color: '#e74c3c',
  tile_layer: 'standard',
  border_radius: '0',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const tileUrls = {
  standard:    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
  hot:         'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png',
  positron:    'https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png',
  voyager:     'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',
  dark:        'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',
  satellite:   'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
  topo:        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}',
  esri_street: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}',
  gray:        'https://server.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer/tile/{z}/{y}/{x}',
  opentopomap: 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
};

const layerLabels = {
  standard:    'Standard',
  hot:         'Humanitarian',
  positron:    'Positron',
  voyager:     'Voyager',
  dark:        'Dark Matter',
  satellite:   'Satellite',
  topo:        'Topografica',
  esri_street: 'Esri Street',
  gray:        'Grigio minimal',
  opentopomap: 'OpenTopoMap',
};

const layerLabel = computed(() => layerLabels[s.value.tile_layer] || 'Standard');

function toggleEdit() {
  editMode.value = !editMode.value;
}

function onMessage(e) {
  if (!e.data || e.data.type !== 'olo-map-update') return;
  if (!props.tileId) return;
  tilesStore.updateTile(props.tileId, {
    latitude: parseFloat(e.data.lat).toFixed(6),
    longitude: parseFloat(e.data.lng).toFixed(6),
  });
}

onMounted(() => {
  window.addEventListener('message', onMessage);
});

onUnmounted(() => {
  window.removeEventListener('message', onMessage);
});

const mapHtml = computed(() => {
  const lat = parseFloat(s.value.latitude) || 41.9028;
  const lng = parseFloat(s.value.longitude) || 12.4964;
  const zoom = parseInt(s.value.zoom) || 13;
  const tileUrl = tileUrls[s.value.tile_layer] || tileUrls.standard;
  const showMarker = s.value.marker !== false && s.value.marker !== 'false';
  const markerColor = s.value.marker_color || '#e74c3c';
  const popupText = s.value.marker_popup || '';
  const vbase = (window.oloData?.pluginUrl || '/wp-content/plugins/olobuild/') + 'assets/vendor/leaflet/';

  return `<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="${vbase}leaflet.css"/>
<style>
html,body{margin:0;padding:0;height:100%;overflow:hidden}
#map{width:100%;height:100%;cursor:crosshair}
.olo-pin{background:none!important;border:none!important}
.leaflet-control-attribution{font-size:9px!important}
</style>
</head>
<body>
<div id="map"></div>
<script src="${vbase}leaflet.js"><\/script>
<script>
var map=L.map('map',{scrollWheelZoom:true,dragging:true,zoomControl:true,attributionControl:true}).setView([${lat},${lng}],${zoom});
L.tileLayer('${tileUrl}',{attribution:'\\u00a9 OpenStreetMap',maxZoom:19}).addTo(map);

var pinSvg='<svg xmlns="http://www.w3.org/2000/svg" width="28" height="40" viewBox="0 0 24 36"><path d="M12 0C5.4 0 0 5.4 0 12c0 9 12 24 12 24s12-15 12-24C24 5.4 18.6 0 12 0zm0 16c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4z" fill="${markerColor}"/></svg>';
var icon=L.divIcon({html:pinSvg,iconSize:[28,40],iconAnchor:[14,40],popupAnchor:[0,-40],className:'olo-pin'});

var marker=${showMarker ? 'true' : 'false'} ? L.marker([${lat},${lng}],{icon:icon,draggable:true}).addTo(map) : null;
${showMarker && popupText ? `if(marker) marker.bindPopup('${popupText.replace(/'/g, "\\'")}');` : ''}

function sendPos(lat,lng){
  parent.postMessage({type:'olo-map-update',lat:lat,lng:lng},'*');
}

if(marker){
  marker.on('dragend',function(e){
    var p=e.target.getLatLng();
    sendPos(p.lat,p.lng);
  });
}

map.on('click',function(e){
  if(marker){
    marker.setLatLng(e.latlng);
  } else {
    marker=L.marker(e.latlng,{icon:icon,draggable:true}).addTo(map);
    marker.on('dragend',function(ev){
      var p=ev.target.getLatLng();
      sendPos(p.lat,p.lng);
    });
  }
  sendPos(e.latlng.lat,e.latlng.lng);
});
<\/script>
</body>
</html>`;
});
</script>

<style scoped>
.olo-map-edit-btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
</style>
