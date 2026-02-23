import { borderFields, borderDefaults, shadowField } from './_shared.js';

export default {
  type: 'social',
  name: 'Link social',
  icon: 'dashicons-share',
  category: 'content',
  defaults: {
    links: 'facebook|https://facebook.com\ntwitter|https://twitter.com',
    size: '32',
    alignment: 'center',
    gap: '12',
    shadow: 'none',
    ...borderDefaults,
  },
  fields: [
    { key: 'links', label: 'Link (piattaforma|url per riga)', type: 'textarea' },
    { key: 'size', label: 'Dim. icona (px)', type: 'range', min: 20, max: 60, step: 4 },
    { key: 'alignment', label: 'Allineamento', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'center', label: 'Centro' },
      { value: 'right', label: 'Destra' },
    ]},
    { key: 'gap', label: 'Gap (px)', type: 'range', min: 0, max: 48, step: 4 },
    shadowField,
    ...borderFields,
  ],
};
