import { shadowField } from './_shared.js';

export default {
  type: 'totop',
  name: 'Torna su',
  icon: 'dashicons-arrow-up-alt',
  category: 'navigation',
  defaults: {
    alignment: 'right',
    style: 'default',
    smooth: true,
    shadow: 'none',
  },
  fields: [
    { key: 'alignment', label: 'Allineamento', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'center', label: 'Centro' },
      { value: 'right', label: 'Destra' },
    ]},
    { key: 'style', label: 'Stile', type: 'select', options: [
      { value: 'default', label: 'Predefinito' },
      { value: 'primary', label: 'Primary' },
    ]},
    { key: 'smooth', label: 'Scorrimento fluido', type: 'toggle' },
    ...shadowField,
  ],
};
