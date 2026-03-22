export default {
  type: 'servicecheckin',
  name: 'Check-in / Check-out',
  icon: 'dashicons-clock',
  category: 'booking',
  defaults: {
    meta_prefix: '_olo_service_',
    bg_color: '',
    text_color: '',
    label_color: '',
    border_color: '',
    border_radius: '12',
    tile_padding: { top: 16, right: 16, bottom: 16, left: 16 },
    font_size: '14',
  },
  fields: [
    { type: 'separator', label: 'Sorgente dati' },
    { key: 'meta_prefix', label: 'Tipo servizio', type: 'select', optionsSource: 'metaPrefixes' },

    { type: 'separator', label: 'Stile' },
    { key: 'bg_color', label: 'Sfondo', type: 'color' },
    { key: 'text_color', label: 'Testo', type: 'color' },
    { key: 'label_color', label: 'Etichette', type: 'color' },
    { key: 'border_color', label: 'Bordo', type: 'color' },
    { key: 'border_radius', label: 'Raggio bordi (px)', type: 'border-radius' },
    { key: 'tile_padding', label: 'Padding (px)', type: 'spacing', max: 32 },
    { key: 'font_size', label: 'Dimensione testo (px)', type: 'range', min: 12, max: 18, step: 1 },
  ],
};
