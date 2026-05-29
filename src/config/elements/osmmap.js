
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
  category: 'content',
  defaults: {
    preset: 'custom',
    typography_preset: '',
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

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    // ── Posizione ──
    { key: 'address', label: t('Cerca indirizzo'), type: 'geocode', targetLat: 'latitude', targetLng: 'longitude', targetZoom: 'zoom' },
    { key: 'latitude', label: t('Latitudine'), type: 'text' },
    { key: 'longitude', label: t('Longitudine'), type: 'text' },
    { key: 'zoom', label: t('Zoom'), type: 'range', min: 1, max: 19, step: 1 },

    // ── Marker ──
    { type: 'separator', label: t('Marker') },
    { key: 'marker', label: t('Mostra marker'), type: 'toggle' },
    { key: 'marker_popup', label: t('Testo popup marker'), type: 'text',
      condition: { field: 'marker', value: true } },
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
    ], condition: { field: 'marker', value: true } },
    { key: 'marker_image', label: t('Immagine marker'), type: 'image',
      condition: { field: 'marker_type', value: 'image' } },

    // ── Interazione ──
    { type: 'separator', label: t('Interazione') },
    { key: 'scroll_wheel_zoom', label: t('Zoom con rotella mouse'), type: 'toggle' },
    { key: 'dragging', label: t('Trascinamento mappa'), type: 'toggle' },
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

    // ── Marker (stile) ──
    { type: 'separator', label: t('Stile marker') },
    { key: 'marker_color', label: t('Colore marker'), type: 'color',
      condition: { field: 'marker', value: true } },
    { key: 'marker_size', label: t('Dimensione marker (px)'), type: 'range', min: 20, max: 64, step: 2,
      condition: { field: 'marker', value: true } },

    // ── Aspetto ──
    { type: 'separator', label: t('Aspetto') },
    { key: 'height', label: t('Altezza (px)'), type: 'range', min: 150, max: 800, step: 10 },
    withHover({ key: 'border_radius', label: t('Arrotondamento (px)'), type: 'border-radius' }),
    { key: 'tile_layer', label: t('Stile mappa'), type: 'select', options: [
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
    ...borderFields(),
  ],
};
