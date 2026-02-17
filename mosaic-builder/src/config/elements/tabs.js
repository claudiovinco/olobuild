export default {
  type: 'tabs',
  name: 'Schede',
  icon: 'dashicons-category',
  category: 'content',
  defaults: {
    tabs_data: 'Scheda 1\nContenuto della prima scheda.\n---\nScheda 2\nContenuto della seconda scheda.',
    accent_color: '#6366F1',
    text_color: '#F3F4F6',
  },
  fields: [
    { key: 'tabs_data', label: 'Dati schede', type: 'textarea' },
    { key: 'accent_color', label: 'Colore accento', type: 'color' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },
  ],
};
