export default {
  type: 'woo_product_navigation',
  name: 'Navigazione Prodotti',
  icon: 'dashicons-leftright',
  category: 'woocommerce',
  placeholder: 'Link prodotto precedente / successivo',
  defaults: {
    show_thumbnail: true,
    show_label: true,
    label_prev: 'Prodotto precedente',
    label_next: 'Prodotto successivo',
    text_color: '',
    hover_color: '',
    separator_style: 'line',
  },
  fields: [
    { key: 'show_thumbnail', label: 'Mostra miniatura', type: 'toggle' },
    { key: 'show_label', label: 'Mostra etichetta', type: 'toggle' },
    { key: 'label_prev', label: 'Etichetta precedente', type: 'text' },
    { key: 'label_next', label: 'Etichetta successivo', type: 'text' },
    { key: 'separator_style', label: 'Separatore', type: 'select', options: [
      { value: 'line', label: 'Linea' },
      { value: 'dotted', label: 'Puntinato' },
      { value: 'none', label: 'Nessuno' },
    ]},

    { type: 'separator', label: 'Colori' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },
    { key: 'hover_color', label: 'Colore hover', type: 'color' },
  ],
};
