import { shadowField } from './_shared.js';

// Shortcode tile è un wrapper per shortcode WordPress di terze parti.
// D1 (text effects) N/A: il contenuto è codice WP arbitrario, non testo Olobuild.
// D2 (image presets) N/A: nessuna immagine propria.
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
    { type: 'separator', label: 'Shortcode' },
    { key: 'shortcode_text', label: 'Shortcode', type: 'textarea',
      description: 'Esempio: [gallery ids="1,2,3"] o [contact-form-7 id="42"]' },
    { key: 'parse_shortcodes', label: 'Esegui shortcode', type: 'toggle' },

    { type: 'separator', label: 'Aspetto' },
    ...shadowField,
  ],
};
