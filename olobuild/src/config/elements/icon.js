import { borderFields, borderDefaults, shadowField } from './_shared.js';

export default {
  type: 'icon',
  name: 'Icona',
  icon: 'dashicons-star-filled',
  category: 'content',
  defaults: {
    icon: 'star',
    size: 40,
    color: '',
    link_url: '',
    link_target: '_self',
    shadow: 'none',
    ...borderDefaults,
  },
  fields: [
    { key: 'icon', label: 'Nome icona', type: 'icon' },
    { key: 'size', label: 'Dimensione (px)', type: 'range', min: 16, max: 120, step: 4 },
    { key: 'color', label: 'Colore', type: 'color' },
    { key: 'link_url', label: 'URL link', type: 'text' },
    { key: 'link_target', label: 'Apri in', type: 'select', options: [
      { value: '_self', label: 'Stessa finestra' },
      { value: '_blank', label: 'Nuova finestra' },
    ]},
    shadowField,
    ...borderFields,
  ],
};
