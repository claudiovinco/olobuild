
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
export default {
  type: 'woo_breadcrumbs',
  name: 'Breadcrumbs WooCommerce',
  icon: 'dashicons-admin-links',
  category: 'woocommerce',
  placeholder: 'Breadcrumbs navigazione WooCommerce',
  defaults: {
    separator: '/',
    text_color: '',
    link_color: '',
    font_size: '14',
    alignment: 'left',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },
  fields: [
    { key: 'separator', label: 'Separatore', type: 'select', options: [
      { value: '/', label: '/' },
      { value: '>', label: '>' },
      { value: '-', label: '-' },
      { value: '>>', label: '>>' },
    ]},
    { key: 'alignment', label: 'Allineamento', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'center', label: 'Centro' },
      { value: 'right', label: 'Destra' },
    ]},

    { type: 'separator', label: 'Stile' },
    { key: 'font_size', label: 'Dimensione (px)', type: 'range', min: 10, max: 24, step: 1 },
    { key: 'text_color', label: 'Colore testo', type: 'color' },
    { key: 'link_color', label: 'Colore link', type: 'color' },
    ...borderFields(),
  ],
};
