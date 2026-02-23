import { borderFields, borderDefaults, shadowField } from './_shared.js';

export default {
  type: 'subnav',
  name: 'Subnav',
  icon: 'dashicons-ellipsis',
  category: 'content',
  defaults: {
    items: [
      { id: 'sn-1', title: 'Elemento 1', content: '#' },
      { id: 'sn-2', title: 'Elemento 2', content: '#' },
      { id: 'sn-3', title: 'Elemento 3', content: '#' },
    ],
    style: 'default',
    divider: false,
    shadow: 'none',
    ...borderDefaults,
  },
  fields: [
    { key: 'items', label: 'Elementi', type: 'content-items', supportsDynamic: true,
      itemFields: [
        { key: 'title', label: 'Etichetta', type: 'text' },
        { key: 'content', label: 'URL', type: 'text', placeholder: 'https://...' },
      ],
      newItemDefaults: { title: 'Nuovo elemento', content: '#' },
    },
    { key: 'style', label: 'Stile', type: 'select', options: [
      { value: 'default', label: 'Predefinito' },
      { value: 'pill', label: 'Pill' },
    ]},
    { key: 'divider', label: 'Separatore', type: 'toggle' },
    shadowField,
    ...borderFields,
  ],
};
