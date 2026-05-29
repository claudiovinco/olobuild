
import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Map (Pro) — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → mode/sorgente dati (single/dynamic/locations/services), coordinate/indirizzo,
 *                   marker toggle/tipo/popup, toggle filtri/popup, sorgente dati CPT, ranges filtri,
 *                   toggle interazione (fit_bounds/cluster/fullscreen/schema/ricerca), view_mode/sort/paging
 *   styleFields[] → preset, typography_preset, dimensioni, tile_layer, colori/dim marker,
 *                   stile popup (bg/colore/larghezza/radius), card style (border-radius, max-height),
 *                   layout split-view (filter_width/position/columns), shadow + bordo
 *   defaults      → identico (nessuna modifica)
 *
 * Note di scelta:
 *   - I toggle "show_*" per i filtri e i popup → fields[] (controllano contenuto/comportamento)
 *   - I "_ranges" filtri → fields[] (dati di configurazione)
 *   - `view_mode`, `grid_columns`, `sort_default`, `results_per_page` → fields[] (comportamento elenco)
 *   - `marker_shape` (tipo) → fields[] | `marker_color`/`marker_size` → styleFields[]
 *   - `filter_position`, `filter_columns`, `filter_width` (layout split-view) → styleFields[]
 *   - `emit_schema` (SEO) → fields[]
 *   - `btn_text` → fields[]; `btn_bg`/`btn_color` → styleFields[]
 *   - `svc_popup_btn_text` → fields[]; gli altri `svc_popup_*` di stile → styleFields[]
 */
export default {
  type: 'map',
  name: t('Mappa Pro'),
  icon: 'dashicons-location',
  category: 'media',
  defaults: {
    preset: 'custom',
    typography_preset: '',
    mode: 'single',
    // Single mode
    address: '',
    latitude: '41.9028',
    longitude: '12.4964',
    zoom: '13',
    tile_layer: 'standard',
    marker: true,
    marker_popup: '',
    marker_color: '#e74c3c',
    marker_type: 'pin',
    marker_image: '',
    marker_size: '36',
    border_radius: '0',
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
    svc_popup_show_specs: true,
    svc_popup_show_amenities: false,
    svc_popup_show_gallery: false,
    svc_popup_show_valley: true,
    svc_popup_max_width: '280',
    svc_popup_img_height: '180',
    svc_popup_btn_text: 'Scopri e Prenota',
    svc_popup_btn_color: '',
    svc_popup_bg: '',
    svc_popup_color: '',
    svc_popup_radius: '8',
    // Shared split-view layout (locations + services)
    map_position: 'left',
    map_width: '',
    filter_columns: '2',
    filter_position: '',
    filter_width: '45',
    fullscreen_btn: true,
    view_mode: 'list',
    grid_columns: '2',
    sort_default: 'default',
    results_per_page: '10',
    card_max_height: '0',
    card_border_radius: '8',
    show_location_search: true,
    show_radius: false,
    radius_default: '5',
    marker_shape: 'pin',
    emit_schema: true,
    btn_text: 'Ricerca',
    btn_bg: '#2563EB',
    btn_color: '#FFFFFF',
    // Shared
    height: '400',
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    {
      key: 'mode',
      label: t('Modalità'),
      type: 'select',
      options: [
        { value: 'single', label: t('Indirizzo singolo') },
        { value: 'dynamic_service', label: t('Servizio corrente (dinamico)') },
        { value: 'locations', label: t('Sedi (CPT)') },
        { value: 'services', label: t('Servizi / Baite (Olo Booking)') },
      ],
    },

    // ── Campi modalità singola ──
    { key: 'address', label: t('Cerca indirizzo'), type: 'geocode', targetLat: 'latitude', targetLng: 'longitude', targetZoom: 'zoom', condition: { field: 'mode', value: 'single' } },
    { key: 'latitude', label: t('Latitudine'), type: 'text', condition: { field: 'mode', value: 'single' } },
    { key: 'longitude', label: t('Longitudine'), type: 'text', condition: { field: 'mode', value: 'single' } },
    { key: 'zoom', label: t('Zoom'), type: 'range', min: 1, max: 19, step: 1, condition: { field: 'mode', value: 'single' } },

    { type: 'separator', label: t('Marker'), condition: { field: 'mode', value: 'single' } },
    { key: 'marker', label: t('Mostra marker'), type: 'toggle', condition: { field: 'mode', value: 'single' } },
    { key: 'marker_popup', label: t('Testo popup marker'), type: 'text', condition: { field: 'mode', value: 'single' } },
    { key: 'marker_type', label: t('Tipo marker'), type: 'select', options: [
      { value: 'pin', label: t('Pin classico') },
      { value: 'drop', label: t('Goccia') },
      { value: 'circle', label: t('Cerchio') },
      { value: 'square', label: t('Quadrato') },
      { value: 'diamond', label: t('Diamante') },
      { value: 'star', label: t('Stella') },
      { value: 'flag', label: t('Bandiera') },
      { value: 'flag-wave', label: t('Bandiera animata') },
      { value: 'heart', label: t('Cuore') },
      { value: 'image', label: t('Immagine personalizzata') },
    ], condition: { field: 'mode', value: 'single' } },
    { key: 'marker_image', label: t('Immagine marker'), type: 'image', condition: { field: 'marker_type', value: 'image' } },

    // ── Campi modalità dynamic_service ──
    { key: 'zoom', label: t('Zoom'), type: 'range', min: 1, max: 19, step: 1, condition: { field: 'mode', value: 'dynamic_service' } },

    // ── Campi modalità sedi ──
    { type: 'separator', label: t('Sorgente dati'), condition: { field: 'mode', value: 'locations' } },

    { key: 'loc_post_type', label: t('Post Type (slug)'), type: 'text', condition: { field: 'mode', value: 'locations' } },
    { key: 'loc_osm_field', label: t('Nome campo ACF OSM'), type: 'text', condition: { field: 'mode', value: 'locations' } },
    { key: 'loc_taxonomy', label: t('Tassonomia (slug)'), type: 'text', condition: { field: 'mode', value: 'locations' } },
    { key: 'loc_max_locations', label: t('Max sedi'), type: 'range', min: 1, max: 500, step: 1, condition: { field: 'mode', value: 'locations' } },
    { key: 'loc_default_center', label: t('Centro predefinito (lat, lng)'), type: 'text', condition: { field: 'mode', value: 'locations' } },
    { key: 'loc_default_zoom', label: t('Zoom predefinito'), type: 'range', min: 1, max: 19, step: 1, condition: { field: 'mode', value: 'locations' } },
    { key: 'loc_fit_bounds', label: t('Adatta ai marker'), type: 'toggle', condition: { field: 'mode', value: 'locations' } },
    { key: 'loc_cluster', label: t('Raggruppa marker'), type: 'toggle', condition: { field: 'mode', value: 'locations' } },

    { type: 'separator', label: t('Filtri'), condition: { field: 'mode', value: 'locations' } },

    { key: 'loc_show_filters', label: t('Mostra filtri tassonomia'), type: 'toggle', condition: { field: 'mode', value: 'locations' } },
    // Nota: loc_filter_style e loc_filter_align (legacy) rimossi.
    // La disposizione dei filtri è determinata dal "Layout split-view" più in basso.

    { type: 'separator', label: t('Popup'), condition: { field: 'mode', value: 'locations' } },

    { key: 'loc_popup_show_image', label: t('Mostra immagine'), type: 'toggle', condition: { field: 'mode', value: 'locations' } },
    { key: 'loc_popup_show_excerpt', label: t('Mostra riassunto'), type: 'toggle', condition: { field: 'mode', value: 'locations' } },
    { key: 'loc_popup_show_link', label: t('Mostra link'), type: 'toggle', condition: { field: 'mode', value: 'locations' } },

    // ── Campi modalità servizi ──
    { type: 'separator', label: t('Tipo servizio'), condition: { field: 'mode', value: 'services' } },

    { key: 'svc_booking_mode', label: t('Tipo servizio'), type: 'select', options: [
      { value: 'accommodation', label: t('Alloggi (accommodation)') },
      { value: 'timeslot', label: t('Fasce orarie (timeslot)') },
      { value: '', label: t('Tutti i tipi') },
    ], condition: { field: 'mode', value: 'services' } },

    { key: 'svc_default_center', label: t('Centro predefinito (lat, lng)'), type: 'text', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_default_zoom', label: t('Zoom predefinito'), type: 'range', min: 1, max: 19, step: 1, condition: { field: 'mode', value: 'services' } },
    { key: 'svc_fit_bounds', label: t('Adatta ai marker'), type: 'toggle', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_cluster', label: t('Raggruppa marker'), type: 'toggle', condition: { field: 'mode', value: 'services' } },

    { type: 'separator', label: t('Filtri'), condition: { field: 'mode', value: 'services' } },
    // Nota: svc_filter_style e svc_filter_position (legacy) rimossi dall'UI.
    // La posizione del pannello filtri è ora controllata da "filter_position"
    // più in basso nella sezione "Layout split-view".

    { key: 'svc_show_altitude_filter', label: t('Mostra filtro altitudine'), type: 'toggle', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_altitude_ranges', label: t('Fasce altitudine (min-max,...)'), type: 'text', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_show_locality_filter', label: t('Mostra filtro località'), type: 'toggle', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_show_guests_filter', label: t('Mostra filtro ospiti'), type: 'toggle', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_guests_ranges', label: t('Fasce ospiti (min-max,...)'), type: 'text', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_show_price_filter', label: t('Mostra filtro prezzo'), type: 'toggle', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_price_ranges', label: t('Fasce prezzo (min-max,...)'), type: 'text', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_show_bedrooms_filter', label: t('Mostra filtro camere'), type: 'toggle', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_show_amenities_filter', label: t('Mostra filtro amenities'), type: 'toggle', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_amenities_list', label: t('Amenities da mostrare nel filtro'), type: 'multi_pills', condition: { field: 'mode', value: 'services' }, options: [
      { value: 'wifi', label: t('WiFi'), icon: 'wifi' },
      { value: 'fireplace', label: t('Camino'), icon: 'fire' },
      { value: 'parking', label: t('Parcheggio'), icon: 'car' },
      { value: 'kitchen', label: t('Cucina'), icon: 'cup' },
      { value: 'tv', label: t('TV') },
      { value: 'bbq', label: t('BBQ'), icon: 'fire' },
      { value: 'terrace', label: t('Terrazza') },
      { value: 'heating', label: t('Riscald.'), icon: 'bolt' },
      { value: 'sauna', label: t('Sauna'), icon: 'droplet' },
      { value: 'hottub', label: t('Idromassaggio'), icon: 'droplet' },
      { value: 'ski', label: t('Sci'), icon: 'snowflake' },
      { value: 'garden', label: t('Giardino'), icon: 'tree' },
      { value: 'pets', label: t('Animali'), icon: 'paw' },
      { value: 'washer', label: t('Lavatrice') },
      { value: 'highchair', label: t('Seggiolone') },
      { value: 'aircon', label: t('Aria Cond.'), icon: 'snowflake' },
      { value: 'dishwasher', label: t('Lavastoviglie') },
      { value: 'linens', label: t('Biancheria'), icon: 'bed' },
      { value: 'towels', label: t('Asciugamani') },
      { value: 'pool', label: t('Piscina'), icon: 'droplet' },
      { value: 'hiking', label: t('Escursioni'), icon: 'location' },
      { value: 'bikes', label: t('Biciclette') },
    ] },

    { type: 'separator', label: t('Contenuto popup'), condition: { field: 'mode', value: 'services' } },

    { key: 'svc_popup_show_image', label: t('Mostra immagine'), type: 'toggle', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_popup_show_excerpt', label: t('Mostra descrizione'), type: 'toggle', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_popup_show_price', label: t('Mostra prezzo'), type: 'toggle', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_popup_show_altitude', label: t('Mostra altitudine'), type: 'toggle', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_popup_show_specs', label: t('Mostra specs (camere/bagni/ospiti)'), type: 'toggle', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_popup_show_valley', label: t('Mostra località/vallata'), type: 'toggle', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_popup_show_amenities', label: t('Mostra amenities'), type: 'toggle', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_popup_show_gallery', label: t('Mini gallery (4 thumb)'), type: 'toggle', condition: { field: 'mode', value: 'services' } },

    { key: 'svc_popup_btn_text', label: t('Testo pulsante CTA'), type: 'text', condition: { field: 'mode', value: 'services' } },

    // ── Comportamento elenco (locations) ──
    { type: 'separator', label: t('Comportamento elenco'), condition: { field: 'mode', value: 'locations' } },
    { key: 'show_location_search', label: t('Ricerca località (Nominatim)'), type: 'toggle', condition: { field: 'mode', value: 'locations' } },
    { key: 'show_radius', label: t('Filtro raggio (km)'), type: 'toggle', condition: { field: 'mode', value: 'locations' } },
    { key: 'radius_default', label: t('Raggio predefinito (km)'), type: 'range', min: 1, max: 50, step: 1, condition: { field: 'show_radius', value: true } },
    { key: 'fullscreen_btn', label: t('Pulsante schermo intero'), type: 'toggle', condition: { field: 'mode', value: 'locations' } },
    { key: 'marker_shape', label: t('Forma marker'), type: 'select', options: [
      { value: 'pin',     label: t('Pin classico') },
      { value: 'drop',    label: t('Goccia') },
      { value: 'circle',  label: t('Cerchio') },
      { value: 'square',  label: t('Quadrato') },
      { value: 'diamond', label: t('Diamante') },
      { value: 'star',    label: t('Stella') },
      { value: 'flag',    label: t('Bandiera') },
      { value: 'heart',   label: t('Cuore') },
    ], condition: { field: 'mode', value: 'locations' } },

    { key: 'view_mode', label: t('Vista predefinita'), type: 'select', options: [
      { value: 'list', label: t('Lista') },
      { value: 'grid', label: t('Griglia') },
    ], condition: { field: 'mode', value: 'locations' } },
    { key: 'grid_columns', label: t('Colonne griglia'), type: 'range', min: 1, max: 4, step: 1, condition: { field: 'view_mode', value: 'grid' } },
    { key: 'sort_default', label: t('Ordinamento predefinito'), type: 'select', options: [
      { value: 'default',    label: t('Predefinito') },
      { value: 'title_asc',  label: t('Titolo A-Z') },
      { value: 'title_desc', label: t('Titolo Z-A') },
      { value: 'newest',     label: t('Più recenti') },
      { value: 'distance',   label: t('Distanza') },
    ], condition: { field: 'mode', value: 'locations' } },
    { key: 'results_per_page', label: t('Risultati per pagina (0 = tutti)'), type: 'range', min: 0, max: 50, step: 1, condition: { field: 'mode', value: 'locations' } },

    { key: 'btn_text',  label: t('Testo pulsante ricerca'),  type: 'text',  condition: { field: 'mode', value: 'locations' } },
    { key: 'emit_schema', label: t('Schema.org JSON-LD (SEO)'), type: 'toggle', condition: { field: 'mode', value: 'locations' } },

    // ── Comportamento elenco (services) ──
    { type: 'separator', label: t('Comportamento elenco'), condition: { field: 'mode', value: 'services' } },
    { key: 'show_location_search', label: t('Ricerca località (Nominatim)'), type: 'toggle', condition: { field: 'mode', value: 'services' } },
    { key: 'show_radius', label: t('Filtro raggio (km)'), type: 'toggle', condition: { field: 'mode', value: 'services' } },
    { key: 'radius_default', label: t('Raggio predefinito (km)'), type: 'range', min: 1, max: 50, step: 1, condition: { field: 'show_radius', value: true } },
    { key: 'fullscreen_btn', label: t('Pulsante schermo intero'), type: 'toggle', condition: { field: 'mode', value: 'services' } },
    { key: 'marker_shape', label: t('Forma marker'), type: 'select', options: [
      { value: 'pin',     label: t('Pin classico') },
      { value: 'drop',    label: t('Goccia') },
      { value: 'circle',  label: t('Cerchio') },
      { value: 'square',  label: t('Quadrato') },
      { value: 'diamond', label: t('Diamante') },
      { value: 'star',    label: t('Stella') },
      { value: 'flag',    label: t('Bandiera') },
      { value: 'heart',   label: t('Cuore') },
    ], condition: { field: 'mode', value: 'services' } },

    { key: 'view_mode', label: t('Vista predefinita'), type: 'select', options: [
      { value: 'list', label: t('Lista') },
      { value: 'grid', label: t('Griglia') },
    ], condition: { field: 'mode', value: 'services' } },
    { key: 'grid_columns', label: t('Colonne griglia'), type: 'range', min: 1, max: 4, step: 1, condition: { field: 'view_mode', value: 'grid' } },
    { key: 'sort_default', label: t('Ordinamento predefinito'), type: 'select', options: [
      { value: 'default',    label: t('Predefinito') },
      { value: 'title_asc',  label: t('Titolo A-Z') },
      { value: 'title_desc', label: t('Titolo Z-A') },
      { value: 'newest',     label: t('Più recenti') },
      { value: 'distance',   label: t('Distanza') },
    ], condition: { field: 'mode', value: 'services' } },
    { key: 'results_per_page', label: t('Risultati per pagina (0 = tutti)'), type: 'range', min: 0, max: 50, step: 1, condition: { field: 'mode', value: 'services' } },

    { key: 'btn_text',  label: t('Testo pulsante ricerca'),  type: 'text',  condition: { field: 'mode', value: 'services' } },
    { key: 'emit_schema', label: t('Schema.org JSON-LD (SEO)'), type: 'toggle', condition: { field: 'mode', value: 'services' } },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean',    label: t('Modern Clean') },
      { value: 'minimal-mono',    label: t('Minimal Mono') },
      { value: 'magazine-bold',   label: t('Magazine Bold') },
      { value: 'editorial-serif', label: t('Editorial Serif') },
      { value: 'compact-inline',  label: t('Compact Inline') },
      { value: 'glass-frosted',   label: t('Glass Frosted') },
      { value: 'neon-glow',       label: t('Neon Glow') },
      { value: 'brutalist-stamp', label: t('Brutalist Stamp') },
      { value: 'gradient-aurora', label: t('Gradient Aurora') },
      { value: 'sticker-fun',     label: t('Sticker Fun') },
      { value: 'retro-terminal',  label: t('Retro Terminal') },
      { value: 'tilt-3d',         label: t('3D Tilt') },
      { value: 'custom',          label: t('Personalizzato') },
    ] },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    // ── Stile marker (single mode) ──
    { type: 'separator', label: t('Stile marker'), condition: { field: 'mode', value: 'single' } },
    { key: 'marker_color', label: t('Colore marker'), type: 'color', condition: { field: 'mode', value: 'single' } },
    { key: 'marker_size', label: t('Dimensione marker (px)'), type: 'range', min: 20, max: 64, step: 2, condition: { field: 'mode', value: 'single' } },

    // ── Stile mappa (single mode) ──
    { type: 'separator', label: t('Stile mappa'), condition: { field: 'mode', value: 'single' } },
    { key: 'tile_layer', label: t('Stile mappa'), type: 'select', condition: { field: 'mode', value: 'single' }, options: [
      { value: 'standard', label: t('Standard') },
      { value: 'hot', label: t('Humanitarian') },
      { value: 'positron', label: t('Positron (chiaro)') },
      { value: 'voyager', label: t('Voyager (colorato)') },
      { value: 'dark', label: t('Dark Matter') },
      { value: 'satellite', label: t('Satellite') },
      { value: 'topo', label: t('Topografica') },
      { value: 'esri_street', label: t('Esri Street') },
      { value: 'gray', label: t('Grigio minimal') },
      { value: 'opentopomap', label: t('OpenTopoMap') },
    ]},

    // ── Stile mappa (locations) ──
    { type: 'separator', label: t('Stile mappa'), condition: { field: 'mode', value: 'locations' } },
    { key: 'loc_tile_layer', label: t('Stile mappa'), type: 'select', options: [
      { value: 'osm', label: t('Standard') },
      { value: 'hot', label: t('Humanitarian') },
      { value: 'positron', label: t('Positron (chiaro)') },
      { value: 'voyager', label: t('Voyager (colorato)') },
      { value: 'dark', label: t('Dark Matter') },
      { value: 'satellite', label: t('Satellite') },
      { value: 'topo', label: t('Topografica') },
      { value: 'esri_street', label: t('Esri Street') },
      { value: 'gray', label: t('Grigio minimal') },
      { value: 'opentopomap', label: t('OpenTopoMap') },
    ], condition: { field: 'mode', value: 'locations' } },
    { key: 'marker_color', label: t('Colore marker/cluster'), type: 'color', condition: { field: 'mode', value: 'locations' } },

    // ── Stile mappa (services) ──
    { type: 'separator', label: t('Stile mappa'), condition: { field: 'mode', value: 'services' } },
    { key: 'svc_tile_layer', label: t('Stile mappa'), type: 'select', options: [
      { value: 'osm', label: t('Standard') },
      { value: 'hot', label: t('Humanitarian') },
      { value: 'positron', label: t('Positron (chiaro)') },
      { value: 'voyager', label: t('Voyager (colorato)') },
      { value: 'dark', label: t('Dark Matter') },
      { value: 'satellite', label: t('Satellite') },
      { value: 'topo', label: t('Topografica') },
      { value: 'esri_street', label: t('Esri Street') },
      { value: 'gray', label: t('Grigio minimal') },
      { value: 'opentopomap', label: t('OpenTopoMap') },
    ], condition: { field: 'mode', value: 'services' } },
    { key: 'marker_color', label: t('Colore marker/cluster'), type: 'color', condition: { field: 'mode', value: 'services' } },

    // ── Stile popup (services) ──
    { type: 'separator', label: t('Stile popup'), condition: { field: 'mode', value: 'services' } },
    { key: 'svc_popup_max_width', label: t('Larghezza max (px)'), type: 'range', min: 200, max: 500, step: 10, condition: { field: 'mode', value: 'services' } },
    { key: 'svc_popup_img_height', label: t('Altezza immagine (px)'), type: 'range', min: 80, max: 300, step: 10, condition: { field: 'mode', value: 'services' } },
    { key: 'svc_popup_btn_color', label: t('Colore pulsante'), type: 'color', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_popup_bg', label: t('Sfondo'), type: 'color', condition: { field: 'mode', value: 'services' } },
    { key: 'svc_popup_color', label: t('Colore testo'), type: 'color', condition: { field: 'mode', value: 'services' } },
    withHover({ key: 'svc_popup_radius', label: t('Border radius'), type: 'border-radius', condition: { field: 'mode', value: 'services' } }),

    // ══════ LAYOUT SPLIT-VIEW (locations + services) ══════
    // Il layout split-view ha mappa su un lato e pannello risultati (filtri + lista) dall'altro.
    // Queste opzioni si applicano solo alle modalità multi-marker.

    { type: 'separator', label: t('Layout split-view'), condition: { field: 'mode', value: 'locations' } },
    { key: 'filter_width', label: t('Dimensione blocco filtri (% — la mappa prende il resto)'), type: 'range', min: 20, max: 80, step: 1, condition: { field: 'mode', value: 'locations' } },
    { key: 'filter_position', label: t('Posizione filtri'), type: 'select', options: [
      { value: 'top',    label: t('Sopra (riga in alto)') },
      { value: 'bottom', label: t('Sotto (riga in basso)') },
      { value: 'left',   label: t('Sinistra (colonna)') },
      { value: 'right',  label: t('Destra (colonna)') },
    ], condition: { field: 'mode', value: 'locations' } },
    { key: 'filter_columns', label: t('Colonne filtri'), type: 'range', min: 1, max: 4, step: 1, condition: { field: 'mode', value: 'locations' } },

    { type: 'separator', label: t('Card risultati'), condition: { field: 'mode', value: 'locations' } },
    { key: 'card_max_height', label: t('Altezza max card (px, 0 = auto)'), type: 'range', min: 0, max: 400, step: 10, condition: { field: 'mode', value: 'locations' } },
    withHover({ key: 'card_border_radius', label: t('Raggio angoli card'), type: 'border-radius', condition: { field: 'mode', value: 'locations' } }),

    { type: 'separator', label: t('Pulsante ricerca'), condition: { field: 'mode', value: 'locations' } },
    { key: 'btn_bg',    label: t('Colore sfondo'),   type: 'color', condition: { field: 'mode', value: 'locations' } },
    { key: 'btn_color', label: t('Colore testo'),    type: 'color', condition: { field: 'mode', value: 'locations' } },

    // ── Stessi controlli per modalità services ──
    { type: 'separator', label: t('Layout split-view'), condition: { field: 'mode', value: 'services' } },
    { key: 'filter_width', label: t('Dimensione blocco filtri (% — la mappa prende il resto)'), type: 'range', min: 20, max: 80, step: 1, condition: { field: 'mode', value: 'services' } },
    { key: 'filter_position', label: t('Posizione filtri'), type: 'select', options: [
      { value: 'top',    label: t('Sopra (riga in alto)') },
      { value: 'bottom', label: t('Sotto (riga in basso)') },
      { value: 'left',   label: t('Sinistra (colonna)') },
      { value: 'right',  label: t('Destra (colonna)') },
    ], condition: { field: 'mode', value: 'services' } },
    { key: 'filter_columns', label: t('Colonne filtri'), type: 'range', min: 1, max: 4, step: 1, condition: { field: 'mode', value: 'services' } },

    { type: 'separator', label: t('Card risultati'), condition: { field: 'mode', value: 'services' } },
    { key: 'card_max_height', label: t('Altezza max card (px, 0 = auto)'), type: 'range', min: 0, max: 400, step: 10, condition: { field: 'mode', value: 'services' } },
    withHover({ key: 'card_border_radius', label: t('Raggio angoli card'), type: 'border-radius', condition: { field: 'mode', value: 'services' } }),

    { type: 'separator', label: t('Pulsante ricerca'), condition: { field: 'mode', value: 'services' } },
    { key: 'btn_bg',    label: t('Colore sfondo'),   type: 'color', condition: { field: 'mode', value: 'services' } },
    { key: 'btn_color', label: t('Colore testo'),    type: 'color', condition: { field: 'mode', value: 'services' } },

    // ── Condivisi ──
    { type: 'separator', label: t('Dimensioni') },
    { key: 'height', label: t('Altezza (px)'), type: 'range', min: 150, max: 800, step: 10 },
    withHover({ key: 'border_radius', label: t('Arrotondamento (px)'), type: 'border-radius' }),
    ...shadowField,
    ...borderFields(),
  ],
};
