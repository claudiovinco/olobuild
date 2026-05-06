
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
export default {
  type: 'woo_product_image',
  name: 'Immagine Prodotto',
  icon: 'dashicons-format-image',
  category: 'woocommerce',
  placeholder: 'Immagine prodotto WooCommerce con galleria',
  defaults: {
    show_gallery: true,
    gallery_position: 'bottom',
    lightbox: false,
    zoom_on_hover: true,
    image_ratio: '1-1',
    border_radius: '8',
    thumb_size: '64',
    thumb_gap: '8',
    thumb_border_radius: '4',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },
  fields: [
    { key: 'show_gallery', label: 'Mostra miniature galleria', type: 'toggle' },
    { key: 'gallery_position', label: 'Posizione galleria', type: 'select', options: [
      { value: 'bottom', label: 'Sotto' },
      { value: 'left', label: 'Sinistra' },
    ]},
    { key: 'lightbox', label: 'Lightbox', type: 'toggle' },
    { key: 'zoom_on_hover', label: 'Zoom al passaggio mouse', type: 'toggle' },

    { type: 'separator', label: 'Layout' },
    { key: 'image_ratio', label: 'Proporzione immagine', type: 'select', options: [
      { value: '1-1', label: '1:1 Quadrato' },
      { value: '4-3', label: '4:3' },
      { value: '3-4', label: '3:4 Verticale' },
      { value: '3-2', label: '3:2' },
      { value: '16-9', label: '16:9' },
      { value: 'auto', label: 'Automatico' },
    ]},
    { key: 'border_radius', label: 'Bordo arrotondato (px)', type: 'border-radius' },
    { key: 'border_radius_hover', label: 'Raggio bordo (hover)', type: 'border-radius' },

    { type: 'separator', label: 'Miniature' },
    { key: 'thumb_size', label: 'Dimensione miniature (px)', type: 'range', min: 40, max: 120, step: 4 },
    { key: 'thumb_gap', label: 'Gap miniature (px)', type: 'range', min: 4, max: 16, step: 2 },
    { key: 'thumb_border_radius', label: 'Bordo miniature (px)', type: 'border-radius' },
    { key: 'thumb_border_radius_hover', label: 'Raggio bordo (hover)', type: 'border-radius' },
    ...borderFields(),
  ],
};
