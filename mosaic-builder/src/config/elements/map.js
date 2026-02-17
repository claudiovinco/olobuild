export default {
  type: 'map',
  name: 'Mappa',
  icon: 'dashicons-location',
  category: 'media',
  defaults: {
    mode: 'single',
    // Single mode
    address: 'Roma, Italia',
    zoom: '13',
    // Locations mode
    loc_post_type: 'location',
    loc_osm_field: 'location_map',
    loc_taxonomy: '',
    loc_show_filters: false,
    loc_filter_style: 'pills',
    loc_cluster: true,
    loc_fit_bounds: true,
    loc_default_zoom: '13',
    loc_default_center: '41.9028, 12.4964',
    loc_popup_show_image: true,
    loc_popup_show_excerpt: true,
    loc_popup_show_link: true,
    loc_max_locations: '100',
    loc_tile_layer: 'osm',
    // Shared
    height: '400',
  },
  fields: [
    {
      key: 'mode',
      label: 'Modalità',
      type: 'select',
      options: [
        { value: 'single', label: 'Indirizzo singolo' },
        { value: 'locations', label: 'Sedi (CPT)' },
      ],
    },

    // ── Campi modalità singola ──
    { key: 'address', label: 'Indirizzo', type: 'text', condition: { field: 'mode', value: 'single' } },
    { key: 'zoom', label: 'Zoom', type: 'range', min: 1, max: 19, step: 1, condition: { field: 'mode', value: 'single' } },

    // ── Separatore ──
    { type: 'separator', condition: { field: 'mode', value: 'locations' } },

    // ── Campi modalità sedi ──
    { key: 'loc_post_type', label: 'Post Type (slug)', type: 'text', condition: { field: 'mode', value: 'locations' } },
    { key: 'loc_osm_field', label: 'Nome campo ACF OSM', type: 'text', condition: { field: 'mode', value: 'locations' } },
    { key: 'loc_taxonomy', label: 'Tassonomia (slug)', type: 'text', condition: { field: 'mode', value: 'locations' } },
    { key: 'loc_max_locations', label: 'Max sedi', type: 'range', min: 1, max: 500, step: 1, condition: { field: 'mode', value: 'locations' } },

    { type: 'separator', condition: { field: 'mode', value: 'locations' } },

    { key: 'loc_tile_layer', label: 'Stile mappa', type: 'select', options: [
      { value: 'osm', label: 'OpenStreetMap' },
      { value: 'positron', label: 'CartoDB Positron (Chiaro)' },
      { value: 'dark', label: 'CartoDB Dark Matter' },
    ], condition: { field: 'mode', value: 'locations' } },
    { key: 'loc_default_center', label: 'Centro predefinito (lat, lng)', type: 'text', condition: { field: 'mode', value: 'locations' } },
    { key: 'loc_default_zoom', label: 'Zoom predefinito', type: 'range', min: 1, max: 19, step: 1, condition: { field: 'mode', value: 'locations' } },
    { key: 'loc_fit_bounds', label: 'Adatta ai marker', type: 'toggle', condition: { field: 'mode', value: 'locations' } },
    { key: 'loc_cluster', label: 'Raggruppa marker', type: 'toggle', condition: { field: 'mode', value: 'locations' } },

    { type: 'separator', condition: { field: 'mode', value: 'locations' } },

    { key: 'loc_show_filters', label: 'Mostra filtri tassonomia', type: 'toggle', condition: { field: 'mode', value: 'locations' } },
    { key: 'loc_filter_style', label: 'Stile filtri', type: 'select', options: [
      { value: 'pills', label: 'Pillole (pulsanti)' },
      { value: 'dropdown', label: 'Menu a tendina' },
    ], condition: { field: 'mode', value: 'locations' } },

    { type: 'separator', condition: { field: 'mode', value: 'locations' } },

    { key: 'loc_popup_show_image', label: 'Popup: mostra immagine', type: 'toggle', condition: { field: 'mode', value: 'locations' } },
    { key: 'loc_popup_show_excerpt', label: 'Popup: mostra riassunto', type: 'toggle', condition: { field: 'mode', value: 'locations' } },
    { key: 'loc_popup_show_link', label: 'Popup: mostra link', type: 'toggle', condition: { field: 'mode', value: 'locations' } },

    // ── Condivisi ──
    { type: 'separator' },
    { key: 'height', label: 'Altezza (px)', type: 'range', min: 150, max: 800, step: 10 },
  ],
};
