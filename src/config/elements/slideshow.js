import { textEffectsFields, textEffectsDefaults, filterFields, filterDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared';
import { shadowField } from './_shared.js';

export default {
  type: 'slideshow',
  name: 'Slideshow',
  icon: 'dashicons-slides',
  category: 'media',
  defaults: {
    slides: [
      { id: 's-1', image: '', title: 'Prima slide', subtitle: 'Prima slide', link: '' },
    ],
    autoplay: true,
    autoplay_speed: '5000',
    show_arrows: true,
    show_dots: true,
    slide_height: '400',
    overlay_color: '#000000',
    text_color: '',
    transition: 'slide',
    shadow: 'none',
    ...textEffectsDefaults,
    ...filterDefaults,
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },
  fields: [
    { type: 'separator', label: 'Slide' },
    { key: 'slides', label: 'Slide', type: 'content-items', supportsDynamic: true,
      itemFields: [
        { key: 'image', label: 'Immagine', type: 'image' },
        { key: 'title', label: 'Titolo', type: 'text' },
        { key: 'subtitle', label: 'Sottotitolo', type: 'text' },
        { key: 'link', label: 'URL link', type: 'text', placeholder: 'https://...' },
      ],
      newItemDefaults: { image: '', title: 'Nuova slide', subtitle: '', link: '' },
      itemLabel: 'Slide',
    },

    { type: 'separator', label: 'Riproduzione' },
    { key: 'autoplay', label: 'Riproduzione automatica', type: 'toggle' },
    { key: 'autoplay_speed', label: 'Velocità riproduzione (ms)', type: 'range', min: 2000, max: 10000, step: 500 },
    { key: 'transition', label: 'Transizione', type: 'select', options: [
      { value: 'slide', label: 'Slide' },
      { value: 'fade', label: 'Fade' },
    ]},

    { type: 'separator', label: 'Controlli' },
    { key: 'show_arrows', label: 'Mostra frecce', type: 'toggle' },
    { key: 'show_dots', label: 'Mostra punti', type: 'toggle' },

    { type: 'separator', label: 'Aspetto' },
    { key: 'slide_height', label: 'Altezza slide (px)', type: 'range', min: 200, max: 800, step: 25 },
    { key: 'overlay_color', label: 'Colore overlay', type: 'color' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },
    ...shadowField,
    ...filterFields,
    ...textEffectsFields([
      { value: 'title', label: 'Solo Titolo' },
      { value: 'subtitle', label: 'Solo Sottotitolo' },
      { value: 'all', label: 'Tutti gli elementi testuali' },
    ]),
    ...borderFields(),
  ],
};
