export default {
  type: 'scrollprogress',
  name: 'Barra Scroll',
  icon: 'dashicons-ellipsis',
  category: 'interactive',
  defaults: {
    position: 'top',
    bar_color: '',
    bar_bg: '',
    bar_height: '4',
    show_percentage: false,
    percentage_color: '',
    z_index: '9999',
  },
  fields: [
    { key: 'position', label: 'Posizione', type: 'select', options: [
      { value: 'top',    label: 'In alto' },
      { value: 'bottom', label: 'In basso' },
    ]},
    { key: 'bar_color', label: 'Colore barra', type: 'color' },
    { key: 'bar_bg', label: 'Colore sfondo', type: 'color' },
    { key: 'bar_height', label: 'Altezza barra (px)', type: 'range', min: 2, max: 12, step: 1 },
    { key: 'show_percentage', label: 'Mostra percentuale', type: 'toggle' },
    { key: 'percentage_color', label: 'Colore percentuale', type: 'color',
      condition: { field: 'show_percentage', operator: '==', value: true } },
    { key: 'z_index', label: 'Z-index', type: 'range', min: 100, max: 10000, step: 100 },
  ],
};
