import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared';
import { shadowField } from './_shared.js';

export default {
  type: 'popover',
  name: 'Popover',
  icon: 'dashicons-location-alt',
  category: 'interactive',
  defaults: {
    image: '',
    markers: [
      { id: 'mk-1', x: 25, y: 30, title: 'Punto 1', content: 'Descrizione...', image: '' },
      { id: 'mk-2', x: 70, y: 60, title: 'Punto 2', content: 'Descrizione...', image: '' },
    ],
    image_alt: '',
    image_height: '0',
    marker_color: '',
    popup_bg: '',
    popup_color: '',
    popup_radius: '8',
    popup_img_height: '120',
    popup_hover_effect: 'none',
    popup_hover_color: '',
    shadow: 'none',
    ...textEffectsDefaults,
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },
  fields: [
    { key: 'image', label: 'Immagine', type: 'image' },
    { key: 'markers', label: 'Marcatori', type: 'content-items',
      itemFields: [
        { key: 'x', label: 'Posizione X (%)', type: 'range', min: 0, max: 100, step: 1 },
        { key: 'y', label: 'Posizione Y (%)', type: 'range', min: 0, max: 100, step: 1 },
        { key: 'title', label: 'Titolo', type: 'text' },
        { key: 'content', label: 'Contenuto', type: 'textarea' },
        { key: 'image', label: 'Immagine popup', type: 'image' },
      ],
      newItemDefaults: { x: 50, y: 50, title: 'Nuovo punto', content: 'Descrizione...', image: '' },
      itemLabel: 'Marcatore',
    },
    { key: 'image_alt', label: 'Testo alternativo immagine', type: 'text' },
    { key: 'image_height', label: 'Altezza immagine (px, 0 = auto)', type: 'range', min: 0, max: 700, step: 10 },

    { type: 'separator', label: 'Marcatori e popup' },

    { key: 'marker_color', label: 'Colore marcatore', type: 'color' },
    { key: 'popup_bg', label: 'Sfondo popup', type: 'color' },
    { key: 'popup_color', label: 'Colore testo popup', type: 'color' },
    { key: 'popup_radius', label: 'Arrotondamento popup (px)', type: 'border-radius' },
    { key: 'popup_radius_hover', label: 'Raggio bordo (hover)', type: 'border-radius' },

    { type: 'separator', label: 'Immagine popup' },

    { key: 'popup_img_height', label: 'Altezza immagine popup (px)', type: 'range', min: 60, max: 300, step: 10 },
    { key: 'popup_hover_effect', label: 'Effetto hover immagine', type: 'select', options: [
      { value: 'none', label: 'Nessuno' },
      { value: 'zoom', label: 'Zoom' },
      { value: 'zoom-rotate', label: 'Zoom + rotazione' },
      { value: 'brightness', label: 'Luminosità' },
      { value: 'desaturate', label: 'Desatura → colore' },
      { value: 'blur-in', label: 'Sfocatura → nitido' },
      { value: 'color-overlay', label: 'Colore additivo' },
    ]},
    { key: 'popup_hover_color', label: 'Colore overlay hover', type: 'color',
      condition: { field: 'popup_hover_effect', value: 'color-overlay' } },
    ...shadowField,
    ...textEffectsFields([
      { value: 'title', label: 'Solo Titolo' },
      { value: 'content', label: 'Solo Contenuto' },
      { value: 'all', label: 'Tutti gli elementi testuali' },
    ]),
    ...borderFields(),
  ],
};
