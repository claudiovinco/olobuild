export default {
  type: 'woo_sale_badge',
  name: 'Badge Offerta',
  icon: 'dashicons-awards',
  category: 'woocommerce',
  placeholder: 'Badge sconto personalizzabile per prodotto',
  defaults: {
    badge_text: 'auto',
    custom_text: 'Offerta!',
    badge_bg: '',
    badge_color: '',
    badge_shape: 'pill',
    position: 'top-left',
    font_size: '14',
    font_weight: '700',
  },
  fields: [
    { key: 'badge_text', label: 'Testo badge', type: 'select', options: [
      { value: 'auto', label: 'Automatico (%)' },
      { value: '%', label: 'Solo percentuale' },
      { value: 'custom', label: 'Testo personalizzato' },
    ]},
    { key: 'custom_text', label: 'Testo personalizzato', type: 'text', condition: { field: 'badge_text', value: 'custom' } },
    { key: 'badge_shape', label: 'Forma', type: 'select', options: [
      { value: 'circle', label: 'Cerchio' },
      { value: 'pill', label: 'Pillola' },
      { value: 'rectangle', label: 'Rettangolo' },
    ]},
    { key: 'position', label: 'Posizione', type: 'select', options: [
      { value: 'top-left', label: 'Alto sinistra' },
      { value: 'top-right', label: 'Alto destra' },
      { value: 'bottom-left', label: 'Basso sinistra' },
      { value: 'bottom-right', label: 'Basso destra' },
    ]},

    { type: 'separator', label: 'Stile' },
    { key: 'font_size', label: 'Dimensione testo (px)', type: 'range', min: 10, max: 32, step: 1 },
    { key: 'font_weight', label: 'Peso font', type: 'select', options: [
      { value: '400', label: 'Normale' },
      { value: '600', label: 'Semi-bold' },
      { value: '700', label: 'Bold' },
      { value: '800', label: 'Extra-bold' },
    ]},

    { type: 'separator', label: 'Colori' },
    { key: 'badge_bg', label: 'Sfondo badge', type: 'color' },
    { key: 'badge_color', label: 'Colore testo badge', type: 'color' },
  ],
};
