export default {
  type: 'sitelogo',
  name: 'Logo sito',
  icon: 'dashicons-admin-home',
  category: 'header',
  defaults: {
    source: 'auto',
    custom_image: '',
    max_height: 50,
    link_home: true,
    show_tagline: false,
  },
  fields: [
    { key: 'source', label: 'Origine', type: 'select', options: [
      { value: 'auto', label: 'Logo / Titolo WP' },
      { value: 'custom_image', label: 'Immagine personalizzata' },
    ]},
    { key: 'custom_image', label: 'Immagine logo', type: 'image',
      condition: { field: 'source', value: 'custom_image' } },
    { key: 'max_height', label: 'Altezza massima (px)', type: 'range', min: 20, max: 120 },
    { key: 'link_home', label: 'Link alla homepage', type: 'toggle' },
    { key: 'show_tagline', label: 'Mostra motto', type: 'toggle' },
  ],
};
