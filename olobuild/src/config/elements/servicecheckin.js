export default {
  type: 'servicecheckin',
  name: 'Check-in / Check-out',
  icon: 'dashicons-clock',
  category: 'booking',
  defaults: {
    meta_prefix: '_olo_service_',
    bg_color: '#F9FAFB',
    text_color: '#374151',
    label_color: '#1F2937',
    border_color: '#E5E7EB',
    border_radius: '12',
    padding: '16',
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
    { key: 'border_radius', label: 'Raggio bordi (px)', type: 'range', min: 0, max: 24, step: 2 },
    { key: 'padding', label: 'Padding (px)', type: 'range', min: 8, max: 32, step: 4 },
    { key: 'font_size', label: 'Dimensione testo (px)', type: 'range', min: 12, max: 18, step: 1 },
  ],
};
