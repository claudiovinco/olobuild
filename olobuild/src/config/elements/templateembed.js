import { shadowField } from './_shared.js';

export default {
  type: 'templateembed',
  name: 'Includi template',
  icon: 'dashicons-layout',
  category: 'layout',
  defaults: {
    template_id: 0,
    shadow: 'none',
  },
  fields: [
    { key: 'template_id', label: 'Template', type: 'select', optionsSource: 'templates' },
    ...shadowField,
  ],
};
