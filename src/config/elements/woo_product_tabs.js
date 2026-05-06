
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
export default {
  type: 'woo_product_tabs',
  name: 'Tab Prodotto',
  icon: 'dashicons-index-card',
  category: 'woocommerce',
  placeholder: 'Tab prodotto WooCommerce (descrizione, info, recensioni)',
  defaults: {
    show_description: true,
    show_additional: true,
    show_reviews: true,
    tab_style: 'underline',
    active_color: '',
    text_color: '',
    border_color: '',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },
  fields: [
    { type: 'separator', label: 'Tab visibili' },
    { key: 'show_description', label: 'Mostra Descrizione', type: 'toggle' },
    { key: 'show_additional', label: 'Mostra Info aggiuntive', type: 'toggle' },
    { key: 'show_reviews', label: 'Mostra Recensioni', type: 'toggle' },

    { type: 'separator', label: 'Stile' },
    { key: 'tab_style', label: 'Stile tab', type: 'select', options: [
      { value: 'underline', label: 'Sottolineatura' },
      { value: 'pills', label: 'Pillole' },
      { value: 'boxed', label: 'Riquadri' },
    ]},

    { type: 'separator', label: 'Colori' },
    { key: 'active_color', label: 'Colore tab attiva', type: 'color' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },
    { key: 'border_color', label: 'Colore bordo', type: 'color' },
    ...borderFields(),
  ],
};
