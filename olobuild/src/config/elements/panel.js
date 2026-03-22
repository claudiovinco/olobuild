import { shadowField } from './_shared.js';

export default {
  type: 'panel',
  name: 'Pannello',
  icon: 'dashicons-id-alt',
  category: 'interactive',
  defaults: {
    style: 'default',
    title: 'Titolo pannello',
    meta: 'Scritto da Autore',
    content: 'Il contenuto del pannello va qui. Aggiungi testo, immagini o qualsiasi altro contenuto.',
    image: '',
    hover_image: '',
    hover_video: '',
    link_url: '',
    link_target: '_self',
    title_element: 'h3',
    shadow: 'none',
    border_radius: '0',
  },
  fields: [
    { key: 'style', label: 'Stile', type: 'select', options: [
      { value: 'default', label: 'Predefinito' },
      { value: 'primary', label: 'Primary' },
      { value: 'secondary', label: 'Secondary' },
      { value: 'hover', label: 'Hover' },
    ]},
    { key: 'image', label: 'Immagine', type: 'image' },
    { key: 'border_radius', label: 'Border Radius immagine', type: 'border-radius' },
    { key: 'hover_image', label: 'Immagine hover', type: 'image' },
    { key: 'hover_video', label: 'Video hover', type: 'media' },
    { key: 'title', label: 'Titolo', type: 'text' },
    { key: 'meta', label: 'Meta', type: 'text' },
    { key: 'content', label: 'Contenuto', type: 'textarea' },
    { key: 'link_url', label: 'URL link', type: 'text' },
    { key: 'link_target', label: 'Apri in', type: 'select', options: [
      { value: '_self', label: 'Stessa finestra' },
      { value: '_blank', label: 'Nuova finestra' },
    ]},
    { key: 'title_element', label: 'Elemento titolo', type: 'select', options: [
      { value: 'h2', label: 'H2' },
      { value: 'h3', label: 'H3' },
      { value: 'h4', label: 'H4' },
      { value: 'div', label: 'DIV' },
    ]},
    ...shadowField,
  ],
};
