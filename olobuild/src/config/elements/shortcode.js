import { shadowField } from './_shared.js';

export default {
  type: 'shortcode',
  name: 'Shortcode',
  icon: 'dashicons-shortcode',
  category: 'dynamic',
  defaults: {
    shortcode_text: '[gallery]',
    parse_shortcodes: true,
    shadow: 'none',
  },
  fields: [
    { key: 'shortcode_text', label: 'Shortcode', type: 'textarea' },
    { key: 'parse_shortcodes', label: 'Esegui shortcode', type: 'toggle' },
    ...shadowField,
  ],
};
