
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
export default {
  type: 'woo_cart',
  name: 'Carrello',
  icon: 'dashicons-cart',
  category: 'woocommerce',
  placeholder: 'Pagina carrello WooCommerce',
  defaults: {
    show_thumbnail: true,
    show_coupon: true,
    show_totals: true,
    button_color: '',
    button_bg: '',
    text_color: '',
    heading_color: '',
    border_color: '',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },
  fields: [
    { type: 'separator', label: 'Elementi visibili' },
    { key: 'show_thumbnail', label: 'Mostra miniatura prodotto', type: 'toggle' },
    { key: 'show_coupon', label: 'Mostra campo coupon', type: 'toggle' },
    { key: 'show_totals', label: 'Mostra totali carrello', type: 'toggle' },

    { type: 'separator', label: 'Colori' },
    { key: 'heading_color', label: 'Colore intestazioni', type: 'color' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },
    { key: 'border_color', label: 'Colore bordi', type: 'color' },
    { key: 'button_bg', label: 'Sfondo pulsante', type: 'color' },
    { key: 'button_color', label: 'Colore testo pulsante', type: 'color' },
    ...borderFields(),
  ],
};
