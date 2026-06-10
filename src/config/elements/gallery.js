import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Gallery — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → immagini, filter_bar, random_order, show_caption (visibilità didascalie)
 *   styleFields[] → preset, bg, typo, layout, griglia, gap, height, fit, radius, mobile_cols, fx kenburns/hover/visual, "+N" indicator, lightbox anim, shadow, border
 *   AVANZATE      → meta tecnico
 */
export default {
  type: 'gallery',
  name: t('Galleria'),
  icon: 'dashicons-format-gallery',
  category: 'media',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    images: [],
    layout: 'grid',
    filter_bar: false,
    random_order: false,
    columns: '3',
    max_columns: '6',
    rows: '0',
    gap: '8',
    img_height: '200px',
    object_fit: 'cover',
    thumb_radius: '8',
    lightbox_animation: 'slide',
    show_caption: false,
    fx_kenburns: false,
    fx_kenburns_speed: '20',
    fx_kenburns_scale: '1.12',
    fx_hover_zoom: true,
    fx_hover_zoom_scale: '1.08',
    fx_hover_tilt: false,
    fx_hover_tilt_angle: '8',
    fx_vignette: false,
    fx_vignette_strength: '40',
    fx_grain: false,
    fx_grain_opacity: '6',
    fx_tint: false,
    fx_tint_color: '',
    fx_tint_opacity: '10',
    fx_tint_blend: 'multiply',
    more_bg: 'rgba(0,0,0,0.55)',
    more_color: '',
    more_size: '28',
    mobile_columns: '2',
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'images', label: t('Immagini'), type: 'gallery' },
    { key: 'filter_bar', label: t('Barra filtro'), type: 'toggle' },
    { key: 'random_order', label: t('Ordine casuale'), type: 'toggle' },
    { key: 'show_caption', label: t('Mostra didascalie'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-grid',     label: t('Modern Grid') },
      { value: 'mosaic-art',      label: t('Mosaic Art') },
      { value: 'cinema-strip',    label: t('Cinema Strip') },
      { value: 'photo-wall',      label: t('Photo Wall (B&N)') },
      { value: 'polaroid-album',  label: t('Polaroid Album') },
      { value: 'glass-tiles',     label: t('Glass Tiles') },
      { value: 'neon-frame',      label: t('Neon Frame') },
      { value: 'brutalist-grid',  label: t('Brutalist Grid') },
      { value: 'soft-pastels',    label: t('Soft Pastels') },
      { value: 'sticker-fun',     label: t('Sticker Fun') },
      { value: 'vhs-retro',       label: t('VHS Retro') },
      { value: 'tilt-3d',         label: t('3D Tilt') },
      { value: 'custom',          label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Layout') },
    { key: 'layout', label: t('Layout'), type: 'select', options: [
      { value: 'grid', label: t('Griglia') },
      { value: 'masonry', label: t('Masonry') },
      { value: 'justified', label: t('Giustificato') },
    ]},
    { key: 'columns', label: t('Colonne'), type: 'range', min: 2, max: 12, step: 1 },
    { key: 'rows', label: t('Righe visibili (0 = tutte)'), type: 'range', min: 0, max: 5, step: 1 },
    { key: 'gap', label: t('Gap (px)'), type: 'range', min: 0, max: 32, step: 2 },
    { key: 'img_height', label: t('Altezza immagine'), type: 'unit', units: ['px'], min: 0, step: 10 },
    { key: 'object_fit', label: t('Adattamento'), type: 'select', options: [
      { value: 'cover', label: t('Riempi') },
      { value: 'contain', label: t('Contieni') },
      { value: 'fill', label: t('Riempi (deforma)') },
    ]},
    withHover({ key: 'thumb_radius', label: t('Raggio bordi (px)'), type: 'border-radius' }),
    { key: 'mobile_columns', label: t('Colonne mobile'), type: 'range', min: 1, max: 4, step: 1 },

    { type: 'separator', label: t('Effetti automatici') },
    { key: 'fx_kenburns', label: t('Ken Burns (zoom cinematico)'), type: 'toggle' },
    { key: 'fx_kenburns_speed', label: t('Durata ciclo (sec)'), type: 'range', min: 10, max: 40, step: 1,
      condition: { field: 'fx_kenburns', value: true } },
    { key: 'fx_kenburns_scale', label: t('Intensità zoom'), type: 'range', min: 1.05, max: 1.25, step: 0.01,
      condition: { field: 'fx_kenburns', value: true } },

    { type: 'separator', label: t('Effetti hover') },
    { key: 'fx_hover_zoom', label: t('Zoom al passaggio'), type: 'toggle' },
    { key: 'fx_hover_zoom_scale', label: t('Intensità zoom'), type: 'range', min: 1.02, max: 1.15, step: 0.01,
      condition: { field: 'fx_hover_zoom', value: true } },
    { key: 'fx_hover_tilt', label: t('Tilt 3D al passaggio'), type: 'toggle' },
    { key: 'fx_hover_tilt_angle', label: t('Angolo rotazione (deg)'), type: 'range', min: 3, max: 15, step: 1,
      condition: { field: 'fx_hover_tilt', value: true } },

    { type: 'separator', label: t('Filtri visivi') },
    { key: 'fx_vignette', label: t('Vignettatura (bordi scuri)'), type: 'toggle' },
    { key: 'fx_vignette_strength', label: t('Intensità vignettatura'), type: 'range', min: 15, max: 60, step: 5,
      condition: { field: 'fx_vignette', value: true } },
    { key: 'fx_grain', label: t('Grana pellicola'), type: 'toggle' },
    { key: 'fx_grain_opacity', label: t('Intensità grana (%)'), type: 'range', min: 3, max: 20, step: 1,
      condition: { field: 'fx_grain', value: true } },
    { key: 'fx_tint', label: t('Tinta colore'), type: 'toggle' },
    { key: 'fx_tint_color', label: t('Colore tinta'), type: 'color',
      condition: { field: 'fx_tint', value: true } },
    { key: 'fx_tint_opacity', label: t('Intensità tinta (%)'), type: 'range', min: 5, max: 50, step: 5,
      condition: { field: 'fx_tint', value: true } },
    { key: 'fx_tint_blend', label: t('Blend mode'), type: 'select', options: [
      { value: 'multiply', label: t('Moltiplica') },
      { value: 'overlay', label: t('Sovrapposizione') },
      { value: 'color', label: t('Colore') },
      { value: 'soft-light', label: t('Luce soffusa') },
      { value: 'hard-light', label: t('Luce forte') },
    ], condition: { field: 'fx_tint', value: true } },

    { type: 'separator', label: 'Indicatore "+N"' },
    { key: 'more_bg', label: t('Sfondo overlay'), type: 'color' },
    { key: 'more_color', label: t('Colore testo'), type: 'color' },
    { key: 'more_size', label: t('Dimensione testo (px)'), type: 'range', min: 16, max: 48, step: 2 },

    { type: 'separator', label: t('Lightbox') },
    { key: 'lightbox_animation', label: t('Animazione lightbox'), type: 'select', options: [
      { value: 'slide', label: t('Scorrimento') },
      { value: 'fade', label: t('Dissolvenza') },
      { value: 'scale', label: t('Scala') },
    ]},

    ...shadowField,
    ...borderFields(),
  ],
};
