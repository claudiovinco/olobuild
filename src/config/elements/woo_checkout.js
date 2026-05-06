
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
export default {
  type: 'woo_checkout',
  name: 'Checkout',
  icon: 'dashicons-clipboard',
  category: 'woocommerce',
  placeholder: 'Pagina checkout WooCommerce',
  defaults: {
    layout: 'two_columns',
    show_order_notes: true,
    accent_color: '',
    text_color: '',
    form_style: 'modern',
    heading_color: '',
    border_color: '',
    button_color: '',
    button_bg: '',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },
  fields: [
    { type: 'separator', label: 'Layout' },
    { key: 'layout', label: 'Layout', type: 'select', options: [
      { value: 'one_column', label: 'Una colonna' },
      { value: 'two_columns', label: 'Due colonne' },
    ]},
    { key: 'form_style', label: 'Stile form', type: 'select', options: [
      { value: 'modern', label: 'Moderno' },
      { value: 'classic', label: 'Classico' },
    ]},

    { type: 'separator', label: 'Opzioni' },
    { key: 'show_order_notes', label: 'Mostra note ordine', type: 'toggle' },

    { type: 'separator', label: 'Colori' },
    { key: 'accent_color', label: 'Colore accento', type: 'color' },
    { key: 'heading_color', label: 'Colore intestazioni', type: 'color' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },
    { key: 'border_color', label: 'Colore bordi', type: 'color' },
    { key: 'button_bg', label: 'Sfondo pulsante', type: 'color' },
    { key: 'button_color', label: 'Colore testo pulsante', type: 'color' },
    ...borderFields(),
  ],
};
