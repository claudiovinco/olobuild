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
    loc_filter_align: 'left',
    loc_cluster: true,
    loc_fit_bounds: true,
    loc_default_zoom: '13',
    loc_default_center: '41.9028, 12.4964',
    loc_popup_show_image: true,
    loc_popup_show_excerpt: true,
    loc_popup_show_link: true,
    loc_max_locations: '100',
    loc_tile_layer: 'osm',
    // Services mode
    svc_booking_mode: 'accommodation',
    svc_show_altitude_filter: true,
    svc_altitude_ranges: '0-1000,1000-1500,1500-2000,2000-9999',
    svc_show_locality_filter: true,
    svc_show_guests_filter: true,
    svc_guests_ranges: '1-2,3-5,6-8,9-99',
    svc_show_price_filter: true,
    svc_price_ranges: '0-100,100-150,150-200,200-9999',
    svc_show_bedrooms_filter: true,
    svc_show_amenities_filter: true,
    svc_amenities_list: 'wifi,fireplace,sauna,hottub,pets,ski,bbq,garden',
    svc_filter_style: 'default',
    svc_filter_position: 'top',
    svc_tile_layer: 'positron',
    svc_default_center: '46.07, 11.12',
    svc_default_zoom: '10',
    svc_fit_bounds: true,
    svc_cluster: true,
    svc_popup_show_image: true,
    svc_popup_show_excerpt: true,
    svc_popup_show_price: true,
    svc_popup_show_altitude: true,
    // Shared
    height: '400',
    border_radius: '8',
  },
  fields: [
    {
      key: 'mode',
      label: 'Modalità',
      type: 'select',
      options: [
        { value: 'single', label: 'Indirizzo singolo' },
        { value: 'dynamic_service', label: 'Servizio corrente (dinamico)' },
        { value: 'locations', label: 'Sedi (CPT)' },
        { value: 'services', label: 'Servizi / Baite (Olo Booking)' },
      ],
    },

    // ── Campi modalità singola ──
    { key: 'address', label: 'Indirizzo', type: 'text', condition: { field: 'mode', value: 'single' } },
    { key: 'zoom', label: 'Zoom', type: 'range', min: 1, max: 19, step: 1, condition: { field: 'mode', value: 'single' } },

    // ── Campi modalità dynamic_service ──
    { key: 'zoom', label: 'Zoom', type: 'range', min: 1, max: 19, step: 1, condition: { field: 'mode', value: 'dynamic_service' } },

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
      { value: 'minimal', label: 'Minimale (sottolineatura)' },
      { value: 'dropdown', label: 'Menu a tendina' },
    ], condition: { field: 'mode', value: 'locations' } },
    { key: 'loc_filter_align', label: 'Allineamento filtri', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'center', label: 'Centro' },
      { value: 'right', label: 'Destra' },
    ], condition: { field: 'mode', value: 'locations' } },

    { type: 'separator', condition: { field: 'mode', value: 'locations' } },

    { key: 'loc_popup_show_image', label: 'Popup: mostra immagine', type: 'toggle', condition: { field: 'mode', value: 'locations' } },
    { key: 'loc_popup_show_excerpt', label: 'Popup: mostra riassunto', type: 'toggle', condition: { field: 'mode', value: 'locations' } },
    { key: 'loc_popup_show_link', label: 'Popup: mostra link', type: 'toggle', condition: { field: 'mode', value: 'locations' } },

    // ── Campi modalità servizi ──
    { type: 'separator', condition: { field: 'mode', value: 'services' } },

    { key: 'svc_booking_mode', label: 'Tipo servizio', type: 'select', options: [
      { value: 'accommodation', label: 'Alloggi (accommodation)' },
      { value: 'timeslot', label: 'Fasce orarie (timeslot)' },
      { value: '', label: 'Tutti i tipi' },
    ], condition: { field: 'mode', value: 'services' } },

    { type: 'separator', condition: { field: 'mode', value: 'services' } },

    { key: 'svc_tile_layer', label: 'Stile mappa', type: 'select', options: [
      { value: 'osm', label: 'OpenStreetMap' },
      { value: 'positron', label: 'CartoDB Positron (Chiaro)' },
      { value: 'dark', label: 'CartoDB Dark Matter' },
    ], condition: { field: 'mode', value: 'services' } },
    { key: 'svc_default_center', label: 'Centro predefinito (lat, lng)', type: 'text', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_default_zoom', label: 'Zoom predefinito', type: 'range', min: 1, max: 19, step: 1, condition: { field: 'mode', value: 'services' } },
    { key: 'svc_fit_bounds', label: 'Adatta ai marker', type: 'toggle', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_cluster', label: 'Raggruppa marker', type: 'toggle', condition: { field: 'mode', value: 'services' } },

    { type: 'separator', condition: { field: 'mode', value: 'services' } },

    { key: 'svc_filter_style', label: 'Stile filtri', type: 'select', options: [
      { value: 'default', label: 'Default (pillole colorate)' },
      { value: 'minimal', label: 'Minimale' },
      { value: 'elegant', label: 'Elegante' },
      { value: 'modern', label: 'Moderno' },
      { value: 'compact', label: 'Compatto (dropdown)' },
    ], condition: { field: 'mode', value: 'services' } },
    { key: 'svc_filter_position', label: 'Posizione filtri', type: 'select', options: [
      { value: 'top', label: 'Sopra la mappa' },
      { value: 'bottom', label: 'Sotto la mappa' },
      { value: 'left', label: 'Sinistra' },
      { value: 'right', label: 'Destra' },
    ], condition: { field: 'mode', value: 'services' } },

    { key: 'svc_show_altitude_filter', label: 'Mostra filtro altitudine', type: 'toggle', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_altitude_ranges', label: 'Fasce altitudine (min-max,...)', type: 'text', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_show_locality_filter', label: 'Mostra filtro localit\u00e0', type: 'toggle', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_show_guests_filter', label: 'Mostra filtro ospiti', type: 'toggle', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_guests_ranges', label: 'Fasce ospiti (min-max,...)', type: 'text', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_show_price_filter', label: 'Mostra filtro prezzo', type: 'toggle', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_price_ranges', label: 'Fasce prezzo (min-max,...)', type: 'text', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_show_bedrooms_filter', label: 'Mostra filtro camere', type: 'toggle', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_show_amenities_filter', label: 'Mostra filtro amenities', type: 'toggle', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_amenities_list', label: 'Amenities da mostrare nel filtro', type: 'multi_pills', condition: { field: 'mode', value: 'services' }, options: [
      { value: 'wifi', label: 'WiFi', icon: '\uD83D\uDCF6' },
      { value: 'fireplace', label: 'Camino', icon: '\uD83D\uDD25' },
      { value: 'parking', label: 'Parcheggio', icon: '\uD83C\uDD7F' },
      { value: 'kitchen', label: 'Cucina', icon: '\uD83C\uDF73' },
      { value: 'tv', label: 'TV', icon: '\uD83D\uDCFA' },
      { value: 'bbq', label: 'BBQ', icon: '\uD83C\uDF56' },
      { value: 'terrace', label: 'Terrazza', icon: '\uD83C\uDF05' },
      { value: 'heating', label: 'Riscald.', icon: '\u2668' },
      { value: 'sauna', label: 'Sauna', icon: '\u2668' },
      { value: 'hottub', label: 'Idromassaggio', icon: '\uD83D\uDEC0' },
      { value: 'ski', label: 'Sci', icon: '\u26F7' },
      { value: 'garden', label: 'Giardino', icon: '\uD83C\uDF31' },
      { value: 'pets', label: 'Animali', icon: '\uD83D\uDC36' },
      { value: 'washer', label: 'Lavatrice', icon: '\uD83E\uDDFA' },
      { value: 'highchair', label: 'Seggiolone', icon: '\uD83D\uDC76' },
      { value: 'aircon', label: 'Aria Cond.', icon: '\u2744' },
      { value: 'dishwasher', label: 'Lavastoviglie', icon: '\uD83C\uDF7D' },
      { value: 'linens', label: 'Biancheria', icon: '\uD83D\uDECF' },
      { value: 'towels', label: 'Asciugamani', icon: '\uD83E\uDDF4' },
      { value: 'pool', label: 'Piscina', icon: '\uD83C\uDFCA' },
      { value: 'hiking', label: 'Escursioni', icon: '\uD83E\uDD7E' },
      { value: 'bikes', label: 'Biciclette', icon: '\uD83D\uDEB2' },
    ] },

    { type: 'separator', condition: { field: 'mode', value: 'services' } },

    { key: 'svc_popup_show_image', label: 'Popup: mostra immagine', type: 'toggle', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_popup_show_excerpt', label: 'Popup: mostra descrizione', type: 'toggle', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_popup_show_price', label: 'Popup: mostra prezzo', type: 'toggle', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_popup_show_altitude', label: 'Popup: mostra altitudine', type: 'toggle', condition: { field: 'mode', value: 'services' } },

    // ── Condivisi ──
    { type: 'separator' },
    { key: 'height', label: 'Altezza (px)', type: 'range', min: 150, max: 800, step: 10 },
    { key: 'border_radius', label: 'Raggio angoli (px)', type: 'range', min: 0, max: 30, step: 1 },
  ],
};
