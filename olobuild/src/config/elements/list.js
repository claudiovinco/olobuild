import { shadowField } from './_shared.js';

export default {
  type: 'list',
  name: 'Lista',
  icon: 'dashicons-editor-ul',
  category: 'text',
  defaults: {
    items: 'check|Funzionalità uno\ncheck|Funzionalità due\ncheck|Funzionalità tre',
    icon_default: 'check',
    icon_color: '',
    text_color: '',
    spacing: '12',
    icon_size: '18',
    shadow: 'none',
  },
  fields: [
    { key: 'items', label: 'Elementi (icona|testo per riga)', type: 'textarea' },
    { key: 'icon_default', label: 'Icona predefinita', type: 'select', options: [
      { value: 'check', label: 'Spunta' },
      { value: 'arrow', label: 'Freccia' },
      { value: 'star', label: 'Stella' },
      { value: 'dot', label: 'Punto' },
      { value: 'number', label: 'Numero' },
    ]},
    { key: 'icon_color', label: 'Colore icona', type: 'color' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },
    { key: 'spacing', label: 'Spaziatura (px)', type: 'range', min: 4, max: 32, step: 2 },
    { key: 'icon_size', label: 'Dim. icona (px)', type: 'range', min: 14, max: 32, step: 2 },
    ...shadowField,
  ],
};
