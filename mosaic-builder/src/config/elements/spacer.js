export default {
  type: 'spacer',
  name: 'Spaziatore',
  icon: 'dashicons-arrows-alt',
  category: 'layout',
  defaults: {
    height: '60',
    show_divider: false,
    divider_color: '#374151',
    divider_width: '100',
    divider_thickness: '1',
  },
  fields: [
    { key: 'height', label: 'Altezza (px)', type: 'range', min: 10, max: 600, step: 5 },
    { key: 'show_divider', label: 'Mostra divisore', type: 'toggle' },
    { key: 'divider_color', label: 'Colore divisore', type: 'color' },
    { key: 'divider_width', label: 'Larghezza divisore (%)', type: 'range', min: 10, max: 100, step: 5 },
    { key: 'divider_thickness', label: 'Spessore divisore (px)', type: 'range', min: 1, max: 10, step: 1 },
  ],
};
