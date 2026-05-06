import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared';
export default {
  type: 'viewscounter',
  name: 'Contatore Visite',
  icon: 'dashicons-visibility',
  category: 'dynamic',
  defaults: {
    show_icon: true,
    icon_position: 'before',
    label: 'visualizzazioni',
    show_label: true,
    text_color: '',
    icon_color: '',
    font_size: '14',
    font_weight: '400',
    layout: 'inline',
    icon_size: '16',
    number_format: true,
    ...textEffectsDefaults,
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },
  fields: [
    // ── Contenuto ──
    { key: 'show_icon', label: 'Mostra icona', type: 'toggle' },
    { key: 'icon_position', label: 'Posizione icona', type: 'select', options: [
      { value: 'before', label: 'Prima del numero' },
      { value: 'after', label: 'Dopo il numero' },
    ], condition: { field: 'show_icon', value: true } },
    { key: 'icon_size', label: 'Dimensione icona (px)', type: 'range', min: 10, max: 40, step: 1,
      condition: { field: 'show_icon', value: true } },
    { key: 'label', label: 'Etichetta', type: 'text' },
    { key: 'show_label', label: 'Mostra etichetta', type: 'toggle' },
    { key: 'number_format', label: 'Formato con separatore migliaia', type: 'toggle' },

    // ── Layout ──
    { type: 'separator', label: 'Layout' },
    { key: 'layout', label: 'Layout', type: 'select', options: [
      { value: 'inline', label: 'In linea' },
      { value: 'block', label: 'A blocchi' },
    ]},

    // ── Tipografia ──
    { type: 'separator', label: 'Tipografia' },
    { key: 'font_size', label: 'Dimensione testo (px)', type: 'range', min: 10, max: 40, step: 1 },
    { key: 'font_weight', label: 'Peso testo', type: 'select', options: [
      { value: '400', label: 'Normale' },
      { value: '500', label: 'Medio' },
      { value: '600', label: 'Semi-grassetto' },
      { value: '700', label: 'Grassetto' },
    ]},

    // ── Colori ──
    { type: 'separator', label: 'Colori' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },
    { key: 'icon_color', label: 'Colore icona', type: 'color',
      condition: { field: 'show_icon', value: true } },
    ...textEffectsFields([ { value: 'label', label: 'Solo Etichetta' } ]),
    ...borderFields(),
  ],
};
