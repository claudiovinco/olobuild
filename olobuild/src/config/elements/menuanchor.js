export default {
  type: 'menuanchor',
  name: 'Ancora Menu',
  icon: 'dashicons-admin-links',
  category: 'layout',
  defaults: {
    anchor_id: '',
    offset: '0',
    label: '',
  },
  fields: [
    { key: 'anchor_id', label: 'ID ancora (senza #)', type: 'text' },
    { key: 'offset', label: 'Offset per header fisso (px)', type: 'range', min: 0, max: 200, step: 5 },

    { type: 'separator', label: 'Builder' },
    { key: 'label', label: 'Etichetta (solo builder)', type: 'text' },
  ],
};
