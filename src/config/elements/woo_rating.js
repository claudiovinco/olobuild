
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
export default {
  type: 'woo_rating',
  name: 'Valutazione Prodotto',
  icon: 'dashicons-star-filled',
  category: 'woocommerce',
  placeholder: 'Valutazione stelle prodotto WooCommerce',
  defaults: {
    show_count: true,
    show_average: true,
    star_color: '',
    empty_star_color: '',
    text_color: '',
    star_size: '20',
    text_size: '14',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },
  fields: [
    { key: 'show_count', label: 'Mostra numero recensioni', type: 'toggle' },
    { key: 'show_average', label: 'Mostra media', type: 'toggle' },

    { type: 'separator', label: 'Dimensioni' },
    { key: 'star_size', label: 'Dimensione stelle (px)', type: 'range', min: 12, max: 48, step: 2 },
    { key: 'text_size', label: 'Dimensione testo (px)', type: 'range', min: 10, max: 24, step: 1 },

    { type: 'separator', label: 'Colori' },
    { key: 'star_color', label: 'Colore stelle piene', type: 'color' },
    { key: 'empty_star_color', label: 'Colore stelle vuote', type: 'color' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },
    ...borderFields(),
  ],
};
