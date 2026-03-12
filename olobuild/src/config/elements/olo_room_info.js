export default {
  type: 'olo_room_info',
  name: 'Sala - Informazioni',
  icon: 'dashicons-info-outline',
  category: 'olo-space',
  defaults: {
    style: 'card',
  },
  fields: [
    { type: 'separator', label: 'Aspetto' },
    { key: 'style', label: 'Stile', type: 'select', options: [
      { value: 'card', label: 'Card con sfondo' },
      { value: 'flat', label: 'Piatto (senza sfondo)' },
    ]},
  ],
};
