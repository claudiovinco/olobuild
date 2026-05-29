import { t } from '@/i18n';

const shapeOptions = [
  { value: 'none', label: t('Nessuna') },
  { value: 'wave', label: t('Onda') },
  { value: 'wave-rough', label: t('Onda irregolare') },
  { value: 'tilt', label: t('Diagonale') },
  { value: 'triangle', label: t('Triangolo') },
  { value: 'curve', label: t('Curva') },
  { value: 'mountains', label: t('Montagne') },
  { value: 'drops', label: t('Gocce') },
  { value: 'zigzag', label: t('Zigzag') },
  { value: 'clouds', label: t('Nuvole') },
  { value: 'brush', label: t('Pennellata') },
];

const fillOptions = [
  { value: 'color', label: t('Colore') },
  { value: 'image', label: t('Immagine') },
  { value: 'video', label: t('Video') },
];

function shapeFields(prefix, label, condField) {
  return [
    { type: 'separator', label },
    { key: prefix, label: t('Forma'), type: 'select', options: shapeOptions },
    { key: prefix + '_height', label: t('Altezza (px)'), type: 'range', min: 20, max: 300, step: 5,
      condition: { field: prefix, operator: '!=', value: 'none' } },
    { key: prefix + '_fill', label: t('Riempimento'), type: 'select', options: fillOptions,
      condition: { field: prefix, operator: '!=', value: 'none' } },
    { key: prefix + '_color', label: t('Colore'), type: 'color',
      condition: { field: prefix + '_fill', value: 'color' } },
    { key: prefix + '_opacity', label: t('Opacità (%)'), type: 'range', min: 10, max: 100, step: 5,
      condition: { field: prefix + '_fill', value: 'color' } },
    { key: prefix + '_fill_image', label: t('Immagine'), type: 'image',
      condition: { field: prefix + '_fill', value: 'image' } },
    { key: prefix + '_fill_video', label: t('Video (mp4)'), type: 'media',
      condition: { field: prefix + '_fill', value: 'video' } },
    { key: prefix + '_flip', label: t('Specchia orizzontale'), type: 'toggle',
      condition: { field: prefix, operator: '!=', value: 'none' } },
    { key: prefix + '_invert', label: t('Inverti direzione'), type: 'toggle',
      condition: { field: prefix, operator: '!=', value: 'none' } },
    { key: prefix + '_scale_x', label: t('Scala larghezza (%)'), type: 'range', min: 50, max: 300, step: 5,
      condition: { field: prefix, operator: '!=', value: 'none' } },
    { key: prefix + '_layer2', label: t('Secondo livello'), type: 'toggle',
      condition: { field: prefix, operator: '!=', value: 'none' } },
    { key: prefix + '_layer2_color', label: t('Colore 2° livello'), type: 'color',
      condition: { field: prefix + '_layer2', value: true } },
    { key: prefix + '_layer2_opacity', label: t('Opacità 2° livello (%)'), type: 'range', min: 5, max: 100, step: 5,
      condition: { field: prefix + '_layer2', value: true } },
  ];
}

/**
 * Tile Spacer — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → SVG personalizzato (unico vero "dato" inseribile)
 *   styleFields[] → altezza, forme decorative top/bottom, sfondo, layout, divisore
 *   AVANZATE      → meta tecnico
 */
export default {
  type: 'spacer',
  name: t('Spaziatore'),
  icon: 'dashicons-arrows-alt',
  category: 'essential',
  defaults: {
    height: '60',

    shape_top: 'none',
    shape_top_height: '80',
    shape_top_color: '',
    shape_top_opacity: '100',
    shape_top_fill: 'color',
    shape_top_fill_image: '',
    shape_top_fill_video: '',
    shape_top_flip: false,
    shape_top_invert: false,
    shape_top_scale_x: '100',
    shape_top_layer2: false,
    shape_top_layer2_color: '',
    shape_top_layer2_opacity: '30',

    shape_bottom: 'none',
    shape_bottom_height: '80',
    shape_bottom_color: '',
    shape_bottom_opacity: '100',
    shape_bottom_fill: 'color',
    shape_bottom_fill_image: '',
    shape_bottom_fill_video: '',
    shape_bottom_flip: false,
    shape_bottom_invert: false,
    shape_bottom_scale_x: '100',
    shape_bottom_layer2: false,
    shape_bottom_layer2_color: '',
    shape_bottom_layer2_opacity: '30',

    bg_color: '',
    bg_gradient: false,
    bg_gradient_from: '',
    bg_gradient_to: '',
    bg_gradient_angle: '180',

    full_bleed: false,
    overlap_top: '0',
    overlap_bottom: '0',

    custom_svg: '',

    show_divider: false,
    divider_style: 'solid',
    divider_color: '',
    divider_width: '100',
    divider_thickness: '1',
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'custom_svg', label: t('Codice SVG'), type: 'textarea',
      description: t('SVG decorativo personalizzato che sostituisce le forme top/bottom.') },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Dimensione') },
    { key: 'height', label: t('Altezza (px)'), type: 'range', min: 0, max: 300, step: 5 },

    ...shapeFields('shape_top', 'Forma sopra'),
    ...shapeFields('shape_bottom', 'Forma sotto'),

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg_color', label: t('Colore sfondo'), type: 'color' },
    { key: 'bg_gradient', label: t('Sfumatura'), type: 'toggle' },
    { key: 'bg_gradient_from', label: t('Colore da'), type: 'color',
      condition: { field: 'bg_gradient', value: true } },
    { key: 'bg_gradient_to', label: t('Colore a'), type: 'color',
      condition: { field: 'bg_gradient', value: true } },
    { key: 'bg_gradient_angle', label: t('Angolo sfumatura'), type: 'range', min: 0, max: 360, step: 15,
      condition: { field: 'bg_gradient', value: true } },

    { type: 'separator', label: t('Layout') },
    { key: 'full_bleed', label: t('Larghezza piena sito (100vw)'), type: 'toggle' },
    { key: 'overlap_top', label: t('Sovrapposizione sopra (px)'), type: 'range', min: 0, max: 200, step: 5 },
    { key: 'overlap_bottom', label: t('Sovrapposizione sotto (px)'), type: 'range', min: 0, max: 200, step: 5 },

    { type: 'separator', label: t('Linea divisore') },
    { key: 'show_divider', label: t('Mostra linea'), type: 'toggle' },
    { key: 'divider_style', label: t('Stile linea'), type: 'select', options: [
      { value: 'solid', label: t('Continua') },
      { value: 'dashed', label: t('Tratteggiata') },
      { value: 'dotted', label: t('Puntinata') },
      { value: 'double', label: t('Doppia') },
    ], condition: { field: 'show_divider', value: true } },
    { key: 'divider_color', label: t('Colore linea'), type: 'color',
      condition: { field: 'show_divider', value: true } },
    { key: 'divider_width', label: t('Larghezza linea (%)'), type: 'range', min: 10, max: 100, step: 5,
      condition: { field: 'show_divider', value: true } },
    { key: 'divider_thickness', label: t('Spessore linea (px)'), type: 'range', min: 1, max: 10, step: 1,
      condition: { field: 'show_divider', value: true } },
  ],
};
