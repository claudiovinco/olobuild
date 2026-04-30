import { textEffectsFields, textEffectsDefaults } from './_shared';
export default {
  type: 'menuanchor',
  name: 'Ancora Menu',
  icon: 'dashicons-admin-links',
  category: 'navigation',
  defaults: {
    anchor_id: '',
    offset: '0',
    label: '',
    ...textEffectsDefaults,
  },
  fields: [
    { key: 'anchor_id', label: 'ID ancora (senza #)', type: 'text' },
    { key: 'offset', label: 'Offset per header fisso (px)', type: 'range', min: 0, max: 200, step: 5 },

    { type: 'separator', label: 'Builder' },
    { key: 'label', label: 'Etichetta (solo builder)', type: 'text' },
    ...textEffectsFields([ { value: 'label', label: 'Solo Etichetta' } ]),
  ],
};
