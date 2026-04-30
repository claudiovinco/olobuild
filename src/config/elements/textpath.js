import { textEffectsFields, textEffectsDefaults } from './_shared';
export default {
  type: 'textpath',
  name: 'Testo su Tracciato',
  icon: 'dashicons-editor-textcolor',
  category: 'text',
  defaults: {
    text: 'Testo che segue un tracciato curvo',
    path_preset: 'arc',
    custom_path: '',
    font_size: '24',
    text_color: '',
    letter_spacing: '2',
    animation: 'none',
    animation_speed: '10',
    ...textEffectsDefaults,
  },
  fields: [
    { key: 'text', label: 'Testo', type: 'text' },

    { type: 'separator', label: 'Tracciato' },
    { key: 'path_preset', label: 'Forma tracciato', type: 'select', options: [
      { value: 'arc', label: 'Arco' },
      { value: 'wave', label: 'Onda' },
      { value: 'circle', label: 'Cerchio' },
      { value: 'spiral', label: 'Spirale' },
      { value: 'custom', label: 'Personalizzato' },
    ]},
    { key: 'custom_path', label: 'Percorso SVG (d)', type: 'text', placeholder: 'M 0 50 Q 150 0 300 50',
      condition: { field: 'path_preset', value: 'custom' } },

    { type: 'separator', label: 'Tipografia' },
    { key: 'font_size', label: 'Dimensione testo (px)', type: 'range', min: 12, max: 72 },
    { key: 'text_color', label: 'Colore testo', type: 'color' },
    { key: 'letter_spacing', label: 'Spaziatura lettere (px)', type: 'range', min: 0, max: 20 },

    { type: 'separator', label: 'Animazione' },
    { key: 'animation', label: 'Animazione', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'scroll', label: 'Scorrimento una volta' },
      { value: 'continuous', label: 'Scorrimento continuo' },
    ]},
    { key: 'animation_speed', label: 'Velocita animazione (sec)', type: 'range', min: 1, max: 20,
      condition: { field: 'animation', value: ['scroll', 'continuous'] } },
    ...textEffectsFields([ { value: 'text', label: 'Solo Testo' } ]),
  ],
};
