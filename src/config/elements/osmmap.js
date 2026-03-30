export default {
  type: 'osmmap',
  name: 'Mappa',
  icon: 'dashicons-location-alt',
  category: 'content',
  defaults: {
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
    marker_color: '',
    address: '',
  },
  fields: [
    // ── Posizione ──
    { key: 'address', label: 'Cerca indirizzo', type: 'geocode', targetLat: 'latitude', targetLng: 'longitude', targetZoom: 'zoom' },
    { key: 'latitude', label: 'Latitudine', type: 'text' },
    { key: 'longitude', label: 'Longitudine', type: 'text' },
    { key: 'zoom', label: 'Zoom', type: 'range', min: 1, max: 19, step: 1 },

    // ── Marker ──
    { type: 'separator', label: 'Marker' },
    { key: 'marker', label: 'Mostra marker', type: 'toggle' },
    { key: 'marker_popup', label: 'Testo popup marker', type: 'text',
      condition: { field: 'marker', value: true } },
    { key: 'marker_color', label: 'Colore marker', type: 'color',
      condition: { field: 'marker', value: true } },

    // ── Interazione ──
    { type: 'separator', label: 'Interazione' },
    { key: 'scroll_wheel_zoom', label: 'Zoom con rotella mouse', type: 'toggle' },
    { key: 'dragging', label: 'Trascinamento mappa', type: 'toggle' },

    // ── Aspetto ──
    { type: 'separator', label: 'Aspetto' },
    { key: 'height', label: 'Altezza (px)', type: 'range', min: 150, max: 800, step: 10 },
    { key: 'border_radius', label: 'Arrotondamento (px)', type: 'border-radius' },
    { key: 'tile_layer', label: 'Stile mappa', type: 'select', options: [
      { value: 'standard', label: 'Standard' },
      { value: 'hot', label: 'Humanitarian' },
      { value: 'positron', label: 'Positron (chiaro)' },
      { value: 'voyager', label: 'Voyager (colorato)' },
      { value: 'dark', label: 'Dark Matter' },
      { value: 'satellite', label: 'Satellite' },
      { value: 'topo', label: 'Topografica' },
      { value: 'esri_street', label: 'Esri Street' },
      { value: 'gray', label: 'Grigio minimal' },
      { value: 'opentopomap', label: 'OpenTopoMap' },
    ]},
  ],
};
