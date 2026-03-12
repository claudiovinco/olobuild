import { shadowField } from './_shared.js';

export default {
  type: 'woo_product_filter',
  name: 'WC Filtro Prodotti',
  icon: 'dashicons-filter',
  category: 'woocommerce',
  placeholder: 'Filtri prodotti WooCommerce',
  defaults: {
    show_price_range: true,
    show_categories: true,
    show_attributes: true,
    show_stock: true,
    filter_style: 'sidebar',
    price_range_min: '0',
    price_range_max: '500',
    price_step: '10',
    show_count: true,
    collapsible: true,
    apply_button: true,
    button_text: 'Applica filtri',
    button_bg: '',
    button_color: '',
    label_color: '',
    active_color: '',
    shadow: 'none',
  },
  fields: [
    { type: 'separator', label: 'Filtri visibili' },
    { key: 'show_price_range', label: 'Filtro prezzo', type: 'toggle' },
    { key: 'show_categories', label: 'Filtro categorie', type: 'toggle' },
    { key: 'show_attributes', label: 'Filtro attributi', type: 'toggle' },
    { key: 'show_stock', label: 'Filtro disponibilita', type: 'toggle' },
    { key: 'show_count', label: 'Mostra conteggio prodotti', type: 'toggle' },

    { type: 'separator', label: 'Stile' },
    { key: 'filter_style', label: 'Stile filtro', type: 'select', options: [
      { value: 'sidebar', label: 'Sidebar' },
      { value: 'horizontal', label: 'Orizzontale' },
      { value: 'dropdown', label: 'Dropdown' },
    ]},
    { key: 'collapsible', label: 'Sezioni richiudibili', type: 'toggle' },

    { type: 'separator', label: 'Range prezzo' },
    { key: 'price_range_min', label: 'Prezzo minimo', type: 'range', min: 0, max: 1000, step: 10 },
    { key: 'price_range_max', label: 'Prezzo massimo', type: 'range', min: 10, max: 10000, step: 10 },
    { key: 'price_step', label: 'Step prezzo', type: 'range', min: 1, max: 100, step: 1 },

    { type: 'separator', label: 'Pulsante' },
    { key: 'apply_button', label: 'Mostra pulsante applica', type: 'toggle' },
    { key: 'button_text', label: 'Testo pulsante', type: 'text' },

    { type: 'separator', label: 'Colori' },
    { key: 'label_color', label: 'Colore etichette', type: 'color' },
    { key: 'active_color', label: 'Colore filtro attivo', type: 'color' },
    { key: 'button_color', label: 'Colore testo pulsante', type: 'color' },
    { key: 'button_bg', label: 'Sfondo pulsante', type: 'color' },

    ...shadowField,
  ],
};
