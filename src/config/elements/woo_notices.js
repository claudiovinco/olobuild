
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
export default {
  type: 'woo_notices',
  name: 'Notifiche WooCommerce',
  icon: 'dashicons-info',
  category: 'woocommerce',
  placeholder: 'Notifiche successo, errore e info WooCommerce',
  defaults: {
    show_success: true,
    show_error: true,
    show_info: true,
    border_radius: '8',
    font_size: '14',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },
  fields: [
    { key: 'show_success', label: 'Mostra successo', type: 'toggle' },
    { key: 'show_error', label: 'Mostra errore', type: 'toggle' },
    { key: 'show_info', label: 'Mostra info', type: 'toggle' },

    { type: 'separator', label: 'Stile' },
    { key: 'border_radius', label: 'Arrotondamento (px)', type: 'border-radius' },
    { key: 'border_radius_hover', label: 'Raggio bordo (hover)', type: 'border-radius' },
    { key: 'font_size', label: 'Dimensione testo (px)', type: 'range', min: 10, max: 24, step: 1 },
    ...borderFields(),
  ],
};
