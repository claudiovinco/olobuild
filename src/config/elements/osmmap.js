
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile OSM Map — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → indirizzo/coordinate, marker (toggle/tipo/popup/immagine), interazione (zoom/dragging)
 *   styleFields[] → preset, typography_preset, dimensioni, border-radius, tile_layer, colore/dim marker, bordo
 *   defaults      → identico (nessuna modifica)
 */
export default {
  type: 'osmmap',
  name: t('Mappa'),
  icon: 'dashicons-location-alt',
  category: 'media',
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
    marker_type: 'pin',
    marker_image: '',
    marker_size: '36',
    address: '',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // Come nasce dalla palette: la sede in piazza del Duomo a Milano con lo zoom di quartiere, marker
  // a goccia nel colore del sito con l'indirizzo nel popup, angoli arrotondati. Stile «Standard»
  // (OpenStreetMap). Positron, Voyager e Dark Matter non sono più i raster CARTO (oggi chiedono una
  // chiave): il renderer li disegna con servizi liberi equivalenti (class-osmmap-tile.php).
  partenza: {
    address: 'Piazza del Duomo, Milano',
    zoom: '15',
    marker_popup: t('La nostra sede · Piazza del Duomo, Milano'),
    marker_type: 'drop',
    marker_color: 'var(--olo-color-primary, #e1474f)',
    marker_size: '40',
    border_radius: '16',
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    // ── Posizione ──
    { key: 'address', label: t('Cerca indirizzo'), type: 'geocode', targetLat: 'latitude', targetLng: 'longitude', targetZoom: 'zoom' },
    { key: 'latitude', label: t('Latitudine'), type: 'text' },
    { key: 'longitude', label: t('Longitudine'), type: 'text' },
    { key: 'zoom', label: t('Zoom'), type: 'range', min: 1, max: 19, step: 1,
      description: t('1 = il mondo intero, 19 = il singolo edificio. 15 mostra il quartiere.') },

    // ── Segnaposto ──
    { type: 'separator', label: t('Segnaposto') },
    { key: 'marker', label: t('Mostra segnaposto'), type: 'toggle' },
    { key: 'marker_popup', label: t('Testo del fumetto'), type: 'text',
      description: t('Compare cliccando il segnaposto. Vuoto = nessun fumetto.'),
      condition: { field: 'marker', value: true } },
    { key: 'marker_type', label: t('Forma'), type: 'select', options: [
      { value: 'pin', label: t('Spillo') },
      { value: 'drop', label: t('Goccia') },
      { value: 'circle', label: t('Cerchio') },
      { value: 'square', label: t('Quadrato') },
      { value: 'diamond', label: t('Rombo') },
      { value: 'star', label: t('Stella') },
      { value: 'flag', label: t('Bandiera') },
      { value: 'flag-wave', label: t('Bandiera al vento') },
      { value: 'heart', label: t('Cuore') },
      { value: 'image', label: t('Immagine') },
    ], condition: { field: 'marker', value: true } },
    { key: 'marker_image', label: t('Immagine segnaposto'), type: 'image',
      condition: [ { field: 'marker', value: true }, { field: 'marker_type', value: 'image' } ] },

    // ── Interazione ──
    { type: 'separator', label: t('Interazione') },
    { key: 'scroll_wheel_zoom', label: t('Zoom con la rotella'), type: 'toggle',
      description: t('Spento, la rotella fa scorrere la pagina invece di ingrandire la mappa: di solito è meglio.') },
    { key: 'dragging', label: t('Si sposta trascinando'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  // Zone standard (16 nov 2026): Aspetto · Segnaposto · Forma. Tolti «Stile» e «Stile tipografico»: nessuno stile
  // registrato e nessun testo da impaginare, il menu mostrava «—» e non cambiava niente.
  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'tile_layer', label: t('Stile mappa'), type: 'select', options: [
      { value: 'standard', label: t('Standard') },
      { value: 'positron', label: t('Chiara') },
      { value: 'gray', label: t('Grigia') },
      { value: 'voyager', label: t('Tenue') },
      { value: 'dark', label: t('Scura') },
      { value: 'satellite', label: t('Satellite') },
      { value: 'topo', label: t('Topografica') },
      { value: 'opentopomap', label: t('Rilievi') },
      { value: 'esri_street', label: t('Stradale') },
      { value: 'hot', label: t('Umanitaria') },
    ]},

    { type: 'separator', label: t('Segnaposto'), condition: { field: 'marker', value: true } },
    { key: 'marker_color', label: t('Colore segnaposto'), type: 'color',
      condition: [ { field: 'marker', value: true }, { field: 'marker_type', op: 'neq', value: 'image' } ] },
    { key: 'marker_size', label: t('Dimensione segnaposto'), type: 'range', unit: 'px', min: 20, max: 64, step: 2,
      condition: { field: 'marker', value: true } },

    { type: 'separator', label: t('Forma') },
    { key: 'height', label: t('Altezza'), type: 'range', unit: 'px', min: 150, max: 800, step: 10 },
    withHover({ key: 'border_radius', label: t('Raggio'), type: 'border-radius' }),
    ...borderFields(),
  ],
};
