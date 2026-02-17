export default {
  type: 'popover',
  name: 'Popover',
  icon: 'dashicons-location-alt',
  category: 'content',
  defaults: {
    image: '',
    markers: [
      { id: 'mk-1', x: 25, y: 30, title: 'Punto 1', content: 'Descrizione...' },
      { id: 'mk-2', x: 70, y: 60, title: 'Punto 2', content: 'Descrizione...' },
    ],
    image_alt: '',
    marker_color: '#6366F1',
  },
  fields: [
    { key: 'image', label: 'Immagine', type: 'image' },
    { key: 'markers', label: 'Marcatori', type: 'content-items',
      itemFields: [
        { key: 'x', label: 'Posizione X (%)', type: 'range', min: 0, max: 100, step: 1 },
        { key: 'y', label: 'Posizione Y (%)', type: 'range', min: 0, max: 100, step: 1 },
        { key: 'title', label: 'Titolo', type: 'text' },
        { key: 'content', label: 'Contenuto', type: 'textarea' },
      ],
      newItemDefaults: { x: 50, y: 50, title: 'Nuovo punto', content: 'Descrizione...' },
      itemLabel: 'Marcatore',
    },
    { key: 'image_alt', label: 'Testo alternativo immagine', type: 'text' },
    { key: 'marker_color', label: 'Colore marcatore', type: 'color' },
  ],
};
