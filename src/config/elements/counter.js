import { textEffectsFields, textEffectsDefaults, shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';

export default {
  type: 'counter',
  name: 'Contatore',
  icon: 'dashicons-performance',
  category: 'marketing',
  defaults: {
    number: '1250',
    label: 'Clienti soddisfatti',
    prefix: '',
    suffix: '+',
    icon_emoji: 'bolt',
    icon_size: '40',

    // Tipografia
    text_color: '',
    number_font_size: '48',
    number_font_weight: '700',
    label_color: '',
    label_font_size: '14',
    label_font_weight: '400',

    // Sfondo
    bg_type: 'color',
    bg_color: '',
    bg_image: '',
    bg_video: '',
    overlay: false,
    overlay_color: '#000000',
    overlay_opacity: '50',

    // Tile
    tile_padding: { top: 32, right: 32, bottom: 32, left: 32 },
    border_radius: '0',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
    shadow: 'none',
    ...textEffectsDefaults,
  },
  fields: [
    // ── Contenuto ──
    { key: 'number', label: 'Numero', type: 'text' },
    { key: 'label', label: 'Etichetta', type: 'text' },
    { key: 'prefix', label: 'Prefisso', type: 'text' },
    { key: 'suffix', label: 'Suffisso', type: 'text' },
    { key: 'icon_emoji', label: 'Icona / Emoji', type: 'icon' },
    { key: 'icon_size', label: 'Dimensione icona (px)', type: 'range', min: 16, max: 80, step: 2 },

    // ── Tipografia ──
    { type: 'separator', label: 'Tipografia' },
    { key: 'number_font_size', label: 'Dim. numero (px)', type: 'range', min: 20, max: 120, step: 2 },
    { key: 'number_font_weight', label: 'Peso numero', type: 'select', options: [
      { value: '400', label: 'Normale' },
      { value: '500', label: 'Medio' },
      { value: '600', label: 'Semi-grassetto' },
      { value: '700', label: 'Grassetto' },
      { value: '800', label: 'Extra-grassetto' },
      { value: '900', label: 'Nero' },
    ]},
    { key: 'label_font_size', label: 'Dim. etichetta (px)', type: 'range', min: 10, max: 32, step: 1 },
    { key: 'label_font_weight', label: 'Peso etichetta', type: 'select', options: [
      { value: '400', label: 'Normale' },
      { value: '500', label: 'Medio' },
      { value: '600', label: 'Semi-grassetto' },
      { value: '700', label: 'Grassetto' },
    ]},

    // ── Colori ──
    { type: 'separator', label: 'Colori' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },
    { key: 'label_color', label: 'Colore etichetta', type: 'color' },

    // ── Sfondo ──
    { type: 'separator', label: 'Sfondo' },
    { key: 'bg_type', label: 'Tipo sfondo', type: 'select', options: [
      { value: 'color', label: 'Colore' },
      { value: 'image', label: 'Immagine' },
      { value: 'video', label: 'Video' },
    ]},
    { key: 'bg_color', label: 'Colore sfondo', type: 'color',
      condition: { field: 'bg_type', value: 'color' } },
    { key: 'bg_image', label: 'Immagine sfondo', type: 'image',
      condition: { field: 'bg_type', value: 'image' } },
    { key: 'bg_video', label: 'Video sfondo (mp4)', type: 'media',
      condition: { field: 'bg_type', value: 'video' } },
    { key: 'overlay', label: 'Overlay', type: 'toggle',
      condition: { field: 'bg_type', operator: '!=', value: 'color' } },
    { key: 'overlay_color', label: 'Colore overlay', type: 'color',
      condition: { field: 'overlay', value: true } },
    { key: 'overlay_opacity', label: 'Opacità overlay (%)', type: 'range', min: 10, max: 100, step: 5,
      condition: { field: 'overlay', value: true } },

    // ── Tile ──
    { type: 'separator', label: 'Aspetto tile' },
    { key: 'tile_padding', label: 'Padding (px)', type: 'spacing', max: 80 },
    { key: 'border_radius', label: 'Arrotondamento (px)', type: 'border-radius' },
    { key: 'border_radius_hover', label: 'Raggio bordo (hover)', type: 'border-radius' },
    ...shadowField,
    ...borderFields(),
    ...textEffectsFields([ { value: 'label', label: 'Solo Etichetta' } ]),
  ],
};
