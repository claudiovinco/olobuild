export default {
  type: 'switcherpanel',
  name: 'Switcher Panel',
  icon: 'dashicons-images-alt',
  category: 'interactive',
  defaults: {
    items: [
      { id: 'sp-1', nav_label: 'About Us', title: 'About Us', text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', button_text: 'READ MORE', button_url: '#', image: '' },
      { id: 'sp-2', nav_label: 'Bar & Cocktails', title: 'Bar & Cocktails', text: 'Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.', button_text: 'READ MORE', button_url: '#', image: '' },
      { id: 'sp-3', nav_label: 'Restaurant', title: 'Restaurant', text: 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.', button_text: 'READ MORE', button_url: '#', image: '' },
    ],
    hero_image: '',
    hero_height: '400',
    image_position: 'right',
    nav_style: 'minimal',
    animation: 'fade',
    content_padding: '40',
    title_tag: 'h3',
    button_style: 'default',
  },
  fields: [
    // ── Items ──
    {
      key: 'items',
      label: 'Pannelli',
      type: 'content-items',
      itemFields: [
        { key: 'nav_label', label: 'Etichetta navigazione', type: 'text' },
        { key: 'title', label: 'Titolo', type: 'text' },
        { key: 'text', label: 'Testo', type: 'textarea' },
        { key: 'button_text', label: 'Testo pulsante', type: 'text' },
        { key: 'button_url', label: 'URL pulsante', type: 'text' },
        { key: 'image', label: 'Immagine', type: 'image' },
        { key: 'hover_image', label: 'Immagine hover', type: 'image' },
        { key: 'hover_video', label: 'Video hover', type: 'media' },
      ],
      newItemDefaults: { nav_label: 'Nuovo', title: 'Nuovo pannello', text: 'Contenuto del pannello.', button_text: 'READ MORE', button_url: '#', image: '', hover_image: '', hover_video: '' },
      itemLabel: 'Pannello',
    },

    { type: 'separator', label: 'Hero' },

    // ── Hero ──
    { key: 'hero_image', label: 'Immagine teaser', type: 'image' },
    { key: 'hero_height', label: 'Altezza hero (px)', type: 'range', min: 200, max: 700, step: 10 },

    { type: 'separator', label: 'Layout' },

    // ── Layout ──
    { key: 'image_position', label: 'Posizione immagine', type: 'select', options: [
      { value: 'right', label: 'Destra' },
      { value: 'left', label: 'Sinistra' },
    ]},
    { key: 'nav_style', label: 'Stile navigazione', type: 'select', options: [
      { value: 'minimal', label: 'Minimale (sottolineatura)' },
      { value: 'tab', label: 'Tab' },
      { value: 'pills', label: 'Pills' },
    ]},
    { key: 'animation', label: 'Animazione', type: 'select', options: [
      { value: '', label: 'Nessuna' },
      { value: 'fade', label: 'Dissolvenza' },
      { value: 'slide-left', label: 'Scorrimento sinistra' },
      { value: 'slide-right', label: 'Scorrimento destra' },
      { value: 'slide-top', label: 'Scorrimento alto' },
      { value: 'slide-bottom', label: 'Scorrimento basso' },
    ]},
    { key: 'content_padding', label: 'Padding contenuto (px)', type: 'range', min: 10, max: 80, step: 5 },
    { key: 'title_tag', label: 'Tag titolo', type: 'select', options: [
      { value: 'h2', label: 'H2' },
      { value: 'h3', label: 'H3' },
      { value: 'h4', label: 'H4' },
    ]},
    { key: 'button_style', label: 'Stile pulsante', type: 'select', options: [
      { value: 'default', label: 'Predefinito' },
      { value: 'primary', label: 'Primary' },
      { value: 'secondary', label: 'Secondary' },
      { value: 'text', label: 'Solo testo' },
    ]},
  ],
};
