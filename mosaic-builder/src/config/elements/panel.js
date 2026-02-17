export default {
  type: 'panel',
  name: 'Pannello',
  icon: 'dashicons-id-alt',
  category: 'content',
  defaults: {
    style: 'default',
    title: 'Titolo pannello',
    meta: 'Scritto da Autore',
    content: 'Il contenuto del pannello va qui. Aggiungi testo, immagini o qualsiasi altro contenuto.',
    image: '',
    link_url: '',
    link_target: '_self',
    title_element: 'h3',
  },
  fields: [
    { key: 'style', label: 'Stile', type: 'select', options: [
      { value: 'default', label: 'Predefinito' },
      { value: 'primary', label: 'Primary' },
      { value: 'secondary', label: 'Secondary' },
      { value: 'hover', label: 'Hover' },
    ]},
    { key: 'image', label: 'Immagine', type: 'image' },
    { key: 'title', label: 'Titolo', type: 'editor', mode: 'inline' },
    { key: 'meta', label: 'Meta', type: 'text' },
    { key: 'content', label: 'Contenuto', type: 'editor', mode: 'block' },
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
  ],
};
