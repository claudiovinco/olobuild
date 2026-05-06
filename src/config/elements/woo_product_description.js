
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
export default {
  type: 'woo_product_description',
  name: 'Descrizione Prodotto',
  icon: 'dashicons-text-page',
  category: 'woocommerce',
  placeholder: 'Descrizione prodotto WooCommerce',
  defaults: {
    content_type: 'full',
    text_color: '',
    font_size: '16',
    line_height: '1.6',
    text_align: 'left',
    max_lines: '0',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },
  fields: [
    { key: 'content_type', label: 'Tipo contenuto', type: 'select', options: [
      { value: 'full', label: 'Descrizione completa' },
      { value: 'short', label: 'Descrizione breve' },
    ]},

    { type: 'separator', label: 'Stile' },
    { key: 'font_size', label: 'Dimensione (px)', type: 'range', min: 12, max: 32, step: 1 },
    { key: 'line_height', label: 'Altezza riga', type: 'select', options: [
      { value: '1.2', label: '1.2' },
      { value: '1.4', label: '1.4' },
      { value: '1.5', label: '1.5' },
      { value: '1.6', label: '1.6' },
      { value: '1.8', label: '1.8' },
      { value: '2', label: '2' },
    ]},
    { key: 'text_align', label: 'Allineamento', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'center', label: 'Centro' },
      { value: 'right', label: 'Destra' },
      { value: 'justify', label: 'Giustificato' },
    ]},
    { key: 'max_lines', label: 'Righe massime (0 = tutte)', type: 'range', min: 0, max: 20, step: 1 },

    { type: 'separator', label: 'Colori' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },
    ...borderFields(),
  ],
};
