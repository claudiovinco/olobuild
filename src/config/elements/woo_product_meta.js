export default {
  type: 'woo_product_meta',
  name: 'Meta Prodotto',
  icon: 'dashicons-info-outline',
  category: 'woocommerce',
  placeholder: 'SKU, categorie e tag del prodotto WooCommerce',
  defaults: {
    show_sku: true,
    show_categories: true,
    show_tags: true,
    layout: 'stacked',
    separator: '|',
    text_color: '',
    label_color: '',
    link_color: '',
    font_size: '14',
    label_weight: '600',
  },
  fields: [
    { key: 'show_sku', label: 'Mostra SKU', type: 'toggle' },
    { key: 'show_categories', label: 'Mostra categorie', type: 'toggle' },
    { key: 'show_tags', label: 'Mostra tag', type: 'toggle' },

    { type: 'separator', label: 'Layout' },
    { key: 'layout', label: 'Disposizione', type: 'select', options: [
      { value: 'stacked', label: 'In colonna' },
      { value: 'inline', label: 'In riga' },
    ]},
    { key: 'separator', label: 'Separatore (inline)', type: 'text', placeholder: '|' },

    { type: 'separator', label: 'Stile' },
    { key: 'font_size', label: 'Dimensione (px)', type: 'range', min: 11, max: 24, step: 1 },
    { key: 'label_weight', label: 'Peso etichetta', type: 'select', options: [
      { value: '400', label: 'Normale' },
      { value: '500', label: 'Medium' },
      { value: '600', label: 'Semi-bold' },
      { value: '700', label: 'Bold' },
    ]},

    { type: 'separator', label: 'Colori' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },
    { key: 'label_color', label: 'Colore etichette', type: 'color' },
    { key: 'link_color', label: 'Colore link', type: 'color' },
  ],
};
