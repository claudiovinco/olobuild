export default {
  type: 'woo_product_stock',
  name: 'Stock Prodotto',
  icon: 'dashicons-archive',
  category: 'woocommerce',
  placeholder: 'Stato disponibilita prodotto WooCommerce',
  defaults: {
    show_quantity: true,
    show_icon: true,
    in_stock_color: '',
    out_of_stock_color: '',
    low_stock_color: '',
    low_stock_threshold: '5',
    font_size: '14',
    font_weight: '500',
    text_align: 'left',
    icon_size: '10',
  },
  fields: [
    { key: 'show_quantity', label: 'Mostra quantita', type: 'toggle' },
    { key: 'show_icon', label: 'Mostra indicatore', type: 'toggle' },
    { key: 'low_stock_threshold', label: 'Soglia scorte basse', type: 'range', min: 1, max: 50, step: 1 },

    { type: 'separator', label: 'Stile' },
    { key: 'font_size', label: 'Dimensione (px)', type: 'range', min: 11, max: 24, step: 1 },
    { key: 'font_weight', label: 'Peso font', type: 'select', options: [
      { value: '400', label: 'Normale' },
      { value: '500', label: 'Medium' },
      { value: '600', label: 'Semi-bold' },
      { value: '700', label: 'Bold' },
    ]},
    { key: 'text_align', label: 'Allineamento', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'center', label: 'Centro' },
      { value: 'right', label: 'Destra' },
    ]},
    { key: 'icon_size', label: 'Dimensione indicatore (px)', type: 'range', min: 6, max: 16, step: 2 },

    { type: 'separator', label: 'Colori' },
    { key: 'in_stock_color', label: 'Colore disponibile', type: 'color' },
    { key: 'out_of_stock_color', label: 'Colore non disponibile', type: 'color' },
    { key: 'low_stock_color', label: 'Colore scorte basse', type: 'color' },
  ],
};
