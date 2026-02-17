export default {
  type: 'image',
  name: 'Immagine',
  icon: 'dashicons-format-image',
  category: 'media',
  defaults: {
    image_url: '',
    alt_text: '',
    caption: '',
    link_url: '',
    link_target: '_self',
    object_fit: 'cover',
    height: '300px',
  },
  fields: [
    { key: 'image_url', label: 'Immagine', type: 'image' },
    { key: 'alt_text', label: 'Testo alternativo', type: 'text' },
    { key: 'caption', label: 'Didascalia', type: 'editor', mode: 'inline' },
    { key: 'link_url', label: 'URL link', type: 'text' },
    { key: 'link_target', label: 'Apri in', type: 'select', options: [
      { value: '_self', label: 'Stessa finestra' },
      { value: '_blank', label: 'Nuova scheda' },
    ]},
    { key: 'object_fit', label: 'Adattamento', type: 'select', options: [
      { value: 'cover', label: 'Riempi' },
      { value: 'contain', label: 'Contieni' },
      { value: 'fill', label: 'Riempi (deforma)' },
    ]},
    { key: 'height', label: 'Altezza', type: 'text' },
  ],
};
