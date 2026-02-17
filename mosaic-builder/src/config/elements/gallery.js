export default {
  type: 'gallery',
  name: 'Galleria',
  icon: 'dashicons-format-gallery',
  category: 'media',
  defaults: {
    images: [],
    columns: '3',
    gap: '8',
    img_height: '200px',
    object_fit: 'cover',
    lightbox_animation: 'slide',
    show_caption: false,
  },
  fields: [
    { key: 'images', label: 'Immagini', type: 'gallery' },
    { key: 'columns', label: 'Colonne', type: 'select', options: [
      { value: '2', label: '2 colonne' },
      { value: '3', label: '3 colonne' },
      { value: '4', label: '4 colonne' },
      { value: '5', label: '5 colonne' },
    ]},
    { key: 'gap', label: 'Gap (px)', type: 'range', min: 0, max: 48, step: 4 },
    { key: 'img_height', label: 'Altezza immagine', type: 'text' },
    { key: 'object_fit', label: 'Adattamento', type: 'select', options: [
      { value: 'cover', label: 'Riempi' },
      { value: 'contain', label: 'Contieni' },
      { value: 'fill', label: 'Riempi (deforma)' },
    ]},
    { type: 'separator', label: 'Lightbox' },
    { key: 'lightbox_animation', label: 'Animazione lightbox', type: 'select', options: [
      { value: 'slide', label: 'Scorrimento' },
      { value: 'fade', label: 'Dissolvenza' },
      { value: 'scale', label: 'Scala' },
    ]},
    { key: 'show_caption', label: 'Mostra didascalie', type: 'toggle' },
  ],
};
