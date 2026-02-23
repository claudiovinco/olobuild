import { borderFields, borderDefaults, shadowField } from './_shared.js';

export default {
  type: 'nav',
  name: 'Nav',
  icon: 'dashicons-menu',
  category: 'content',
  defaults: {
    items: [
      { id: 'n-1', title: 'Home', content: '#', tag: '' },
      { id: 'n-2', title: 'Chi siamo', content: '#', tag: '' },
      { id: 'n-3', title: 'Contatti', content: '#', tag: '' },
    ],
    style: 'default',
    show_icons: false,
    shadow: 'none',
    ...borderDefaults,
  },
  fields: [
    { key: 'items', label: 'Elementi', type: 'content-items', supportsDynamic: true,
      itemFields: [
        { key: 'title', label: 'Etichetta', type: 'text' },
        { key: 'content', label: 'URL', type: 'text', placeholder: 'https://...' },
        { key: 'tag', label: 'Icona', type: 'icon' },
      ],
      newItemDefaults: { title: 'Nuovo link', content: '#', tag: '' },
      itemLabel: 'Link',
    },
    { key: 'style', label: 'Stile', type: 'select', options: [
      { value: 'default', label: 'Predefinito' },
      { value: 'primary', label: 'Primary' },
      { value: 'secondary', label: 'Secondary' },
    ]},
    { key: 'show_icons', label: 'Mostra icone', type: 'toggle' },
    shadowField,
    ...borderFields,
  ],
};
