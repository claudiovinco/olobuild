import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';

export default {
  type: 'postnavigation',
  name: 'Navigazione articolo',
  icon: 'dashicons-arrow-left-alt',
  category: 'navigation',
  defaults: {
    show_thumbnail: true,
    show_label: true,
    prev_label: 'Precedente',
    next_label: 'Successivo',
    show_title: true,
    title_length: '30',
    layout: 'side-by-side',
    gap: '20',
    thumbnail_size: '60',
    text_color: '',
    link_color: '',
    hover_color: '',
    background_color: '',
    border_radius: '8',
    tile_padding: { top: 16, right: 16, bottom: 16, left: 16 },
    same_taxonomy: false,
    taxonomy: 'category',
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },
  fields: [
    // ── Contenuto ──
    { key: 'show_thumbnail', label: 'Mostra miniatura', type: 'toggle' },
    { key: 'show_label', label: 'Mostra etichetta', type: 'toggle' },
    { key: 'prev_label', label: 'Etichetta precedente', type: 'text' },
    { key: 'next_label', label: 'Etichetta successivo', type: 'text' },
    { key: 'show_title', label: 'Mostra titolo articolo', type: 'toggle' },
    { key: 'title_length', label: 'Lunghezza titolo (caratteri)', type: 'range', min: 10, max: 100, step: 5 },

    // ── Layout ──
    { type: 'separator', label: 'Layout' },
    { key: 'layout', label: 'Layout', type: 'select', options: [
      { value: 'side-by-side', label: 'Affiancato' },
      { value: 'stacked', label: 'Sovrapposto' },
    ]},
    { key: 'gap', label: 'Gap (px)', type: 'range', min: 0, max: 40, step: 4 },
    { key: 'thumbnail_size', label: 'Dimensione miniatura (px)', type: 'range', min: 30, max: 120, step: 5,
      condition: { field: 'show_thumbnail', value: true } },
    { key: 'tile_padding', label: 'Padding (px)', type: 'spacing', max: 40 },
    { key: 'border_radius', label: 'Arrotondamento card (px)', type: 'border-radius' },
    { key: 'border_radius_hover', label: 'Raggio bordo (hover)', type: 'border-radius' },

    // ── Colori ──
    { type: 'separator', label: 'Colori' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },
    { key: 'link_color', label: 'Colore link', type: 'color' },
    { key: 'hover_color', label: 'Colore hover', type: 'color' },
    { key: 'background_color', label: 'Sfondo card', type: 'color' },

    // ── Tassonomia ──
    { type: 'separator', label: 'Tassonomia' },
    { key: 'same_taxonomy', label: 'Stesso termine tassonomia', type: 'toggle' },
    { key: 'taxonomy', label: 'Tassonomia', type: 'text',
      condition: { field: 'same_taxonomy', value: true } },

    ...shadowField,
    ...borderFields(),
  ],
};
