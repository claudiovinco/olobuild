<template>
  <div
    class="olo-osmmap-preview"
    :style="{
      height: (parseInt(s.height) || 400) + 'px',
      borderRadius: (parseInt(s.border_radius) || 0) + 'px',
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
      class="olo-osm-edit-btn"
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
      :title="editMode ? 'Esci dalla modalità posizionamento' : 'Clicca o trascina il marker sulla mappa'"
    >
      <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
      {{ editMode ? 'Fine' : 'Posiziona' }}
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

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { useTilesStore } from '@/stores/tiles';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
  tileId: { type: String, default: '' },
});

const tilesStore = useTilesStore();
const iframeRef = ref(null);
const editMode = ref(false);

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

// Listen for postMessage from iframe
function onMessage(e) {
  if (!e.data || e.data.type !== 'olo-osm-update') return;
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
  const lat = parseFloat(s.value.latitude) || 45.4642;
  const lng = parseFloat(s.value.longitude) || 9.1900;
  const zoom = parseInt(s.value.zoom) || 13;
  const tileUrl = tileUrls[s.value.tile_layer] || tileUrls.standard;
  const showMarker = s.value.marker !== false && s.value.marker !== 'false';
  const markerColor = s.value.marker_color || '#e74c3c';
  const popupText = s.value.marker_popup || '';

  return `<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
html,body{margin:0;padding:0;height:100%;overflow:hidden}
#map{width:100%;height:100%;cursor:crosshair}
.olo-pin{background:none!important;border:none!important}
.leaflet-control-attribution{font-size:9px!important}
</style>
</head>
<body>
<div id="map"></div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"><\/script>
<script>
var map=L.map('map',{scrollWheelZoom:true,dragging:true,zoomControl:true,attributionControl:true}).setView([${lat},${lng}],${zoom});
L.tileLayer('${tileUrl}',{attribution:'\\u00a9 OpenStreetMap',maxZoom:19}).addTo(map);

var pinSvg='<svg xmlns="http://www.w3.org/2000/svg" width="28" height="40" viewBox="0 0 24 36"><path d="M12 0C5.4 0 0 5.4 0 12c0 9 12 24 12 24s12-15 12-24C24 5.4 18.6 0 12 0zm0 16c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4z" fill="${markerColor}"/></svg>';
var icon=L.divIcon({html:pinSvg,iconSize:[28,40],iconAnchor:[14,40],popupAnchor:[0,-40],className:'olo-pin'});

var marker=${showMarker ? 'true' : 'false'} ? L.marker([${lat},${lng}],{icon:icon,draggable:true}).addTo(map) : null;
${showMarker && popupText ? `if(marker) marker.bindPopup('${popupText.replace(/'/g, "\\'")}');` : ''}

function sendPos(lat,lng){
  parent.postMessage({type:'olo-osm-update',lat:lat,lng:lng},'*');
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
.olo-osm-edit-btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px color-mix(in srgb, var(--olo-color-primary, #e1474f) 30%, transparent);
}
</style>
