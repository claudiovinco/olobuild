import { textEffectsFields, textEffectsDefaults } from './_shared';
import { shadowField } from './_shared.js';

export default {
  type: 'nav',
  name: 'Nav',
  icon: 'dashicons-menu',
  category: 'navigation',
  defaults: {
    items: [
      { id: 'n-1', title: 'Home', content: '#', tag: 'home' },
      { id: 'n-2', title: 'Chi siamo', content: '#', tag: 'users' },
      { id: 'n-3', title: 'Servizi', content: '#', tag: 'settings' },
      { id: 'n-4', title: 'Contatti', content: '#', tag: 'mail' },
    ],
    style: 'default',
    direction: 'vertical',
    alignment: 'left',
    show_icons: true,
    icon_position: 'left',
    icon_size: '16',
    font_size: '14',
    font_weight: '400',
    text_transform: 'none',
    letter_spacing: '0',
    link_color: '',
    link_hover_color: '',
    active_color: '',
    icon_color: '',
    separator: false,
    separator_color: '',
    gap: '4',
    tile_padding: { top: 8, right: 12, bottom: 8, left: 12 },
    border_radius: '6',
    active_style: 'left-border',
    active_bg: '',
    hover_bg: '',
    hover_effect: 'none',
    open_in_new_tab: false,
    nofollow: false,
    shadow: 'none',
    ...textEffectsDefaults,
  },
  fields: [
    // --- CONTENUTO ---
    { key: 'items', label: 'Elementi', type: 'content-items', supportsDynamic: true,
      itemFields: [
        { key: 'title', label: 'Etichetta', type: 'text' },
        { key: 'content', label: 'URL', type: 'text', placeholder: 'https://...' },
        { key: 'tag', label: 'Icona', type: 'icon' },
      ],
      newItemDefaults: { title: 'Nuovo link', content: '#', tag: '' },
      itemLabel: 'Link',
    },

    // --- LAYOUT ---
    { key: '_section_layout', label: 'LAYOUT', type: 'separator' },
    { key: 'direction', label: 'Direzione', type: 'select', options: [
      { value: 'vertical', label: 'Verticale' },
      { value: 'horizontal', label: 'Orizzontale' },
    ]},
    { key: 'alignment', label: 'Allineamento', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'center', label: 'Centro' },
      { value: 'right', label: 'Destra' },
      { value: 'stretch', label: 'Espandi' },
    ]},
    { key: 'gap', label: 'Gap tra elementi (px)', type: 'range', min: 0, max: 24, step: 2 },
    { key: 'tile_padding', label: 'Padding (px)', type: 'spacing', max: 32 },

    // --- STILE ---
    { key: '_section_style', label: 'STILE', type: 'separator' },
    { key: 'style', label: 'Stile predefinito', type: 'select', options: [
      { value: 'default', label: 'Predefinito' },
      { value: 'pill', label: 'Pill' },
      { value: 'underline', label: 'Sottolineato' },
      { value: 'boxed', label: 'Riquadro' },
      { value: 'minimal', label: 'Minimale' },
    ]},
    { key: 'active_style', label: 'Indicatore attivo', type: 'select', options: [
      { value: 'none', label: 'Nessuno' },
      { value: 'left-border', label: 'Bordo sinistro' },
      { value: 'bottom-border', label: 'Bordo inferiore' },
      { value: 'background', label: 'Sfondo' },
      { value: 'bold', label: 'Grassetto' },
      { value: 'dot', label: 'Punto' },
    ]},
    { key: 'border_radius', label: 'Raggio bordo (px)', type: 'border-radius' },
    { key: 'border_radius_hover', label: 'Raggio bordo (hover)', type: 'border-radius' },
    { key: 'hover_effect', label: 'Effetto hover', type: 'select', options: [
      { value: 'none', label: 'Nessuno' },
      { value: 'slide-bg', label: 'Sfondo slide' },
      { value: 'underline', label: 'Sottolineatura' },
      { value: 'lift', label: 'Solleva' },
    ]},

    // --- TIPOGRAFIA ---
    { key: '_section_typo', label: 'TIPOGRAFIA', type: 'separator' },
    { key: 'font_size', label: 'Dimensione testo (px)', type: 'range', min: 11, max: 24, step: 1 },
    { key: 'font_weight', label: 'Peso font', type: 'select', options: [
      { value: '300', label: 'Light' },
      { value: '400', label: 'Regular' },
      { value: '500', label: 'Medium' },
      { value: '600', label: 'Semi Bold' },
      { value: '700', label: 'Bold' },
    ]},
    { key: 'text_transform', label: 'Trasformazione', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'uppercase', label: 'Maiuscolo' },
      { value: 'lowercase', label: 'Minuscolo' },
      { value: 'capitalize', label: 'Capitalizzato' },
    ]},
    { key: 'letter_spacing', label: 'Letter spacing (px)', type: 'range', min: 0, max: 5, step: 0.5 },

    // --- ICONE ---
    { key: '_section_icons', label: 'ICONE', type: 'separator' },
    { key: 'show_icons', label: 'Mostra icone', type: 'toggle' },
    { key: 'icon_position', label: 'Posizione icona', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'right', label: 'Destra' },
    ], show: s => s.show_icons },
    { key: 'icon_size', label: 'Dim. icona (px)', type: 'range', min: 12, max: 28, step: 2, show: s => s.show_icons },
    { key: 'icon_color', label: 'Colore icona', type: 'color', show: s => s.show_icons },

    // --- COLORI ---
    { key: '_section_colors', label: 'COLORI', type: 'separator' },
    { key: 'link_color', label: 'Colore link', type: 'color' },
    { key: 'link_hover_color', label: 'Colore hover', type: 'color' },
    { key: 'active_color', label: 'Colore attivo', type: 'color' },
    { key: 'active_bg', label: 'Sfondo attivo', type: 'color' },
    { key: 'hover_bg', label: 'Sfondo hover', type: 'color' },
    { key: 'separator', label: 'Mostra separatori', type: 'toggle' },
    { key: 'separator_color', label: 'Colore separatore', type: 'color', show: s => s.separator },

    // --- LINK ---
    { key: '_section_link', label: 'LINK', type: 'separator' },
    { key: 'open_in_new_tab', label: 'Apri in nuova scheda', type: 'toggle' },
    { key: 'nofollow', label: 'rel="nofollow"', type: 'toggle' },

    ...shadowField,
    ...textEffectsFields([
      { value: 'title', label: 'Solo Titolo' },
      { value: 'content', label: 'Solo Contenuto' },
      { value: 'all', label: 'Tutti gli elementi testuali' },
    ]),
  ],
};
