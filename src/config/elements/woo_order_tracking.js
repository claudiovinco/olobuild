import { textEffectsFields, textEffectsDefaults } from './_shared';
export default {
  type: 'woo_order_tracking',
  name: 'Tracciamento Ordine',
  icon: 'dashicons-search',
  category: 'woocommerce',
  placeholder: 'Modulo tracciamento ordine WooCommerce',
  defaults: {
    title: 'Traccia il tuo ordine',
    title_tag: 'h2',
    accent_color: '',
    text_color: '',
    button_color: '',
    button_bg: '',
    form_style: 'modern',
    ...textEffectsDefaults,
  },
  fields: [
    { key: 'title', label: 'Titolo', type: 'text' },
    { key: 'title_tag', label: 'Tag titolo', type: 'select', options: [
      { value: 'h2', label: 'H2' },
      { value: 'h3', label: 'H3' },
      { value: 'h4', label: 'H4' },
      { value: 'h5', label: 'H5' },
    ]},
    { key: 'form_style', label: 'Stile form', type: 'select', options: [
      { value: 'modern', label: 'Moderno' },
      { value: 'classic', label: 'Classico' },
    ]},

    { type: 'separator', label: 'Colori' },
    { key: 'accent_color', label: 'Colore accento', type: 'color' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },
    { key: 'button_bg', label: 'Sfondo pulsante', type: 'color' },
    { key: 'button_color', label: 'Colore pulsante', type: 'color' },
    ...textEffectsFields([ { value: 'title', label: 'Solo Titolo' } ]),
  ],
};
