import { shadowField } from './_shared.js';

export default {
  type: 'switcher',
  name: 'Switcher',
  icon: 'dashicons-welcome-widgets-menus',
  category: 'interactive',
  defaults: {
    items: [
      { id: 'sw-1', title: 'Scheda 1', content: 'Contenuto della prima scheda.' },
      { id: 'sw-2', title: 'Scheda 2', content: 'Contenuto della seconda scheda.' },
    ],
    nav_style: 'tab',
    animation: '',
    vertical: false,
    shadow: 'none',
  },
  fields: [
    { key: 'items', label: 'Elementi', type: 'content-items', supportsDynamic: true,
      itemFields: [
        { key: 'title', label: 'Titolo', type: 'text' },
        { key: 'content', label: 'Contenuto', type: 'textarea' },
      ],
      newItemDefaults: { title: 'Nuova scheda', content: 'Contenuto della scheda.' },
      itemLabel: 'Scheda',
    },
    { key: 'nav_style', label: 'Stile navigazione', type: 'select', options: [
      { value: 'tab', label: 'Tab' },
      { value: 'tab-underline', label: 'Tab Underline' },
      { value: 'subnav', label: 'Subnav' },
      { value: 'subnav-pill', label: 'Subnav Pill' },
    ]},
    { key: 'animation', label: 'Animazione', type: 'select', options: [
      { value: '', label: 'Nessuna' },
      { value: 'fade', label: 'Fade' },
      { value: 'slide-left', label: 'Slide sinistra' },
      { value: 'slide-right', label: 'Slide destra' },
      { value: 'slide-top', label: 'Slide alto' },
      { value: 'slide-bottom', label: 'Slide basso' },
    ]},
    { key: 'vertical', label: 'Verticale', type: 'toggle' },
    ...shadowField,
  ],
};
