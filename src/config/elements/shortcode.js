import { shadowField } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Shortcode — split CONTENUTO/STILE.
 *   fields[]      → shortcode_text, parse_shortcodes
 *   styleFields[] → shadow
 */
export default {
  type: 'shortcode',
  name: t('Shortcode'),
  icon: 'dashicons-shortcode',
  category: 'dynamic',
  defaults: {
    shortcode_text: '[gallery]',
    parse_shortcodes: true,
    shadow: 'none',
  },

  fields: [
    { key: 'shortcode_text', label: t('Shortcode'), type: 'textarea',
      description: 'Esempio: [gallery ids="1,2,3"] o [contact-form-7 id="42"]' },
    { key: 'parse_shortcodes', label: t('Esegui shortcode'), type: 'toggle' },
  ],

  styleFields: [
    ...shadowField,
  ],
};
