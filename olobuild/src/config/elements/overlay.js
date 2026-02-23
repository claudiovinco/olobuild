import { borderFields, borderDefaults, shadowField } from './_shared.js';

export default {
  type: 'overlay',
  name: 'Overlay',
  icon: 'dashicons-format-image',
  category: 'media',
  defaults: {
    image_url: '',
    title: 'Titolo progetto',
    description: 'Descrizione.',
    link_url: '',
    link_target: '_self',
    overlay_color: '#000000',
    text_color: '#FFFFFF',
    hover_effect: 'fade',
    overlay_opacity: '70',
    height: '300',
    shadow: 'none',
    ...borderDefaults,
  },
  fields: [
    { key: 'image_url', label: 'Immagine', type: 'image' },
    { key: 'title', label: 'Titolo', type: 'editor', mode: 'inline' },
    { key: 'description', label: 'Descrizione', type: 'editor', mode: 'block' },
    { key: 'link_url', label: 'URL link', type: 'text' },
    { key: 'link_target', label: 'Apri in', type: 'select', options: [
      { value: '_self', label: 'Stessa finestra' },
      { value: '_blank', label: 'Nuova scheda' },
    ]},
    { key: 'overlay_color', label: 'Colore overlay', type: 'color' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },
    { key: 'hover_effect', label: 'Effetto hover', type: 'select', options: [
      { value: 'fade', label: 'Fade' },
      { value: 'slide-up', label: 'Slide Up' },
      { value: 'zoom', label: 'Zoom' },
    ]},
    { key: 'overlay_opacity', label: 'Opacità overlay (%)', type: 'range', min: 0, max: 100, step: 5 },
    { key: 'height', label: 'Altezza (px)', type: 'range', min: 10, max: 600, step: 5 },
    shadowField,
    ...borderFields,
  ],
};
