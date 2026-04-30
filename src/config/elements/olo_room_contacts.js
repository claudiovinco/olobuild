import { textEffectsFields, textEffectsDefaults } from './_shared';
export default {
  type: 'olo_room_contacts',
  name: 'Sala - Contatti',
  icon: 'dashicons-phone',
  category: 'olo-space',
  defaults: {
    style: 'card',
    title: 'Contatti',
    ...textEffectsDefaults,
  },
  fields: [
    { type: 'separator', label: 'Contenuto' },
    { key: 'title', label: 'Titolo sezione', type: 'text' },

    { type: 'separator', label: 'Aspetto' },
    { key: 'style', label: 'Stile', type: 'select', options: [
      { value: 'card', label: 'Card con sfondo' },
      { value: 'flat', label: 'Piatto (senza sfondo)' },
    ]},
    ...textEffectsFields([ { value: 'title', label: 'Solo Titolo' } ]),
  ],
};
