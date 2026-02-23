import { borderFields, borderDefaults } from './_shared.js';

const shapeOptions = [
  { value: 'none', label: 'Nessuna' },
  { value: 'wave', label: 'Onda' },
  { value: 'wave-rough', label: 'Onda irregolare' },
  { value: 'tilt', label: 'Diagonale' },
  { value: 'triangle', label: 'Triangolo' },
  { value: 'curve', label: 'Curva' },
  { value: 'mountains', label: 'Montagne' },
  { value: 'drops', label: 'Gocce' },
  { value: 'zigzag', label: 'Zigzag' },
  { value: 'clouds', label: 'Nuvole' },
  { value: 'brush', label: 'Pennellata' },
];

const fillOptions = [
  { value: 'color', label: 'Colore' },
  { value: 'image', label: 'Immagine' },
  { value: 'video', label: 'Video' },
];

function shapeFields(prefix, label, condField) {
  return [
    { type: 'separator', label },
    { key: prefix, label: 'Forma', type: 'select', options: shapeOptions },
    { key: prefix + '_height', label: 'Altezza (px)', type: 'range', min: 20, max: 300, step: 5,
      condition: { field: prefix, operator: '!=', value: 'none' } },
    { key: prefix + '_fill', label: 'Riempimento', type: 'select', options: fillOptions,
      condition: { field: prefix, operator: '!=', value: 'none' } },
    { key: prefix + '_color', label: 'Colore', type: 'color',
      condition: { field: prefix + '_fill', value: 'color' } },
    { key: prefix + '_opacity', label: 'Opacita\u0300 (%)', type: 'range', min: 10, max: 100, step: 5,
      condition: { field: prefix + '_fill', value: 'color' } },
    { key: prefix + '_fill_image', label: 'Immagine', type: 'image',
      condition: { field: prefix + '_fill', value: 'image' } },
    { key: prefix + '_fill_video', label: 'Video (mp4)', type: 'media',
      condition: { field: prefix + '_fill', value: 'video' } },
    { key: prefix + '_flip', label: 'Specchia orizzontale', type: 'toggle',
      condition: { field: prefix, operator: '!=', value: 'none' } },
    { key: prefix + '_invert', label: 'Inverti direzione', type: 'toggle',
      condition: { field: prefix, operator: '!=', value: 'none' } },
    { key: prefix + '_scale_x', label: 'Scala larghezza (%)', type: 'range', min: 50, max: 300, step: 5,
      condition: { field: prefix, operator: '!=', value: 'none' } },
    { key: prefix + '_layer2', label: 'Secondo livello', type: 'toggle',
      condition: { field: prefix, operator: '!=', value: 'none' } },
    { key: prefix + '_layer2_color', label: 'Colore 2\u00b0 livello', type: 'color',
      condition: { field: prefix + '_layer2', value: true } },
    { key: prefix + '_layer2_opacity', label: 'Opacita\u0300 2\u00b0 livello (%)', type: 'range', min: 5, max: 100, step: 5,
      condition: { field: prefix + '_layer2', value: true } },
  ];
}

export default {
  type: 'spacer',
  name: 'Spaziatore',
  icon: 'dashicons-arrows-alt',
  category: 'layout',
  defaults: {
    height: '60',

    // Top shape
    shape_top: 'none',
    shape_top_height: '80',
    shape_top_color: '#ffffff',
    shape_top_opacity: '100',
    shape_top_fill: 'color',
    shape_top_fill_image: '',
    shape_top_fill_video: '',
    shape_top_flip: false,
    shape_top_invert: false,
    shape_top_scale_x: '100',
    shape_top_layer2: false,
    shape_top_layer2_color: '#000000',
    shape_top_layer2_opacity: '30',

    // Bottom shape
    shape_bottom: 'none',
    shape_bottom_height: '80',
    shape_bottom_color: '#ffffff',
    shape_bottom_opacity: '100',
    shape_bottom_fill: 'color',
    shape_bottom_fill_image: '',
    shape_bottom_fill_video: '',
    shape_bottom_flip: false,
    shape_bottom_invert: false,
    shape_bottom_scale_x: '100',
    shape_bottom_layer2: false,
    shape_bottom_layer2_color: '#000000',
    shape_bottom_layer2_opacity: '30',

    // Background
    bg_color: '',
    bg_gradient: false,
    bg_gradient_from: '#6366F1',
    bg_gradient_to: '#8B5CF6',
    bg_gradient_angle: '180',

    // Layout
    full_bleed: false,
    overlap_top: '0',
    overlap_bottom: '0',

    // Custom SVG
    custom_svg: '',

    // Classic divider line
    show_divider: false,
    divider_style: 'solid',
    divider_color: '#374151',
    divider_width: '100',
    divider_thickness: '1',
    ...borderDefaults,
  },
  fields: [
    { key: 'height', label: 'Altezza (px)', type: 'range', min: 0, max: 300, step: 5 },

    ...shapeFields('shape_top', 'Forma sopra'),
    ...shapeFields('shape_bottom', 'Forma sotto'),

    { type: 'separator', label: 'Sfondo' },

    { key: 'bg_color', label: 'Colore sfondo', type: 'color' },
    { key: 'bg_gradient', label: 'Sfumatura', type: 'toggle' },
    { key: 'bg_gradient_from', label: 'Colore da', type: 'color',
      condition: { field: 'bg_gradient', value: true } },
    { key: 'bg_gradient_to', label: 'Colore a', type: 'color',
      condition: { field: 'bg_gradient', value: true } },
    { key: 'bg_gradient_angle', label: 'Angolo sfumatura', type: 'range', min: 0, max: 360, step: 15,
      condition: { field: 'bg_gradient', value: true } },

    { type: 'separator', label: 'Layout' },

    { key: 'full_bleed', label: 'Larghezza piena sito (100vw)', type: 'toggle' },
    { key: 'overlap_top', label: 'Sovrapposizione sopra (px)', type: 'range', min: 0, max: 200, step: 5 },
    { key: 'overlap_bottom', label: 'Sovrapposizione sotto (px)', type: 'range', min: 0, max: 200, step: 5 },

    { type: 'separator', label: 'SVG personalizzato' },

    { key: 'custom_svg', label: 'Codice SVG', type: 'textarea' },

    { type: 'separator', label: 'Linea divisore' },

    { key: 'show_divider', label: 'Mostra linea', type: 'toggle' },
    { key: 'divider_style', label: 'Stile linea', type: 'select', options: [
      { value: 'solid', label: 'Continua' },
      { value: 'dashed', label: 'Tratteggiata' },
      { value: 'dotted', label: 'Puntinata' },
      { value: 'double', label: 'Doppia' },
    ], condition: { field: 'show_divider', value: true } },
    { key: 'divider_color', label: 'Colore linea', type: 'color',
      condition: { field: 'show_divider', value: true } },
    { key: 'divider_width', label: 'Larghezza linea (%)', type: 'range', min: 10, max: 100, step: 5,
      condition: { field: 'show_divider', value: true } },
    { key: 'divider_thickness', label: 'Spessore linea (px)', type: 'range', min: 1, max: 10, step: 1,
      condition: { field: 'show_divider', value: true } },
    ...borderFields,
  ],
};
