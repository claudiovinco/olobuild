export default {
  type: 'divider',
  name: 'Divisore',
  icon: 'dashicons-minus',
  category: 'essential',
  defaults: {
    style: 'solid',
    width: '100',
    thickness: '1',
    color: '',
    alignment: 'center',
    text: '',
    text_color: '',
    icon_emoji: '',
  },
  fields: [
    { key: 'style', label: 'Stile linea', type: 'select', options: [
      { value: 'solid', label: 'Continua' },
      { value: 'dashed', label: 'Tratteggiata' },
      { value: 'dotted', label: 'Puntinata' },
      { value: 'double', label: 'Doppia' },
      { value: 'gradient', label: 'Gradiente' },
    ]},
    { key: 'width', label: 'Larghezza (%)', type: 'range', min: 10, max: 100, step: 5 },
    { key: 'thickness', label: 'Spessore (px)', type: 'range', min: 1, max: 10, step: 1 },
    { key: 'color', label: 'Colore linea', type: 'color' },
    { key: 'alignment', label: 'Allineamento', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'center', label: 'Centro' },
      { value: 'right', label: 'Destra' },
    ]},

    { type: 'separator', label: 'Contenuto centrale' },

    { key: 'text', label: 'Testo centrale', type: 'text' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },
    { key: 'icon_emoji', label: 'Emoji / icona centrale', type: 'text' },
  ],
};
