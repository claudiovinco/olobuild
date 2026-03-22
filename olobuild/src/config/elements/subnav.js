import { shadowField } from './_shared.js';

export default {
  type: 'subnav',
  name: 'Subnav',
  icon: 'dashicons-ellipsis',
  category: 'navigation',
  defaults: {
    source: 'manual',
    items: [
      { id: 'sn-1', title: 'Elemento 1', content: '#' },
      { id: 'sn-2', title: 'Elemento 2', content: '#' },
      { id: 'sn-3', title: 'Elemento 3', content: '#' },
    ],
    menu_id: 0,
    menu_depth: 'top',
    parent_item: '0',
    style: 'default',
    divider: false,
    alignment: 'left',
    gap: '8',
    font_size: '14',
    font_weight: '400',
    text_transform: 'none',
    link_color: '',
    hover_color: '',
    active_color: '',
    active_style: 'none',
    bg_color: '',
    hover_bg: '',
    active_bg: '',
    border_radius: '4',
    tile_padding: { top: 8, right: 12, bottom: 8, left: 12 },
    highlight_current: true,
    shadow: 'none',
  },
  fields: [
    // --- SORGENTE ---
    { key: 'source', label: 'Sorgente', type: 'select', options: [
      { value: 'manual', label: 'Manuale' },
      { value: 'wp_menu', label: 'Menu WordPress' },
    ]},

    // Manuale
    { key: 'items', label: 'Elementi', type: 'content-items', supportsDynamic: true,
      itemFields: [
        { key: 'title', label: 'Etichetta', type: 'text' },
        { key: 'content', label: 'URL', type: 'text', placeholder: 'https://...' },
      ],
      newItemDefaults: { title: 'Nuovo elemento', content: '#' },
      show: s => s.source === 'manual',
    },

    // Menu WP
    { key: 'menu_id', label: 'Menu WordPress', type: 'select', optionsSource: 'wpMenus',
      show: s => s.source === 'wp_menu' },
    { key: 'menu_depth', label: 'Livello da mostrare', type: 'select', options: [
      { value: 'top', label: 'Voci principali (1 livello)' },
      { value: 'children', label: 'Figli di una voce specifica' },
      { value: 'auto', label: 'Figli della pagina corrente' },
    ], show: s => s.source === 'wp_menu' },
    { key: 'parent_item', label: 'Voce genitore', type: 'select',
      optionsSource: 'wpMenuItems', optionsDependOn: 'menu_id',
      show: s => s.source === 'wp_menu' && s.menu_depth === 'children' },

    // --- LAYOUT ---
    { key: '_section_layout', label: 'LAYOUT', type: 'separator' },
    { key: 'style', label: 'Stile', type: 'select', options: [
      { value: 'default', label: 'Predefinito' },
      { value: 'pill', label: 'Pill' },
      { value: 'underline', label: 'Sottolineato' },
      { value: 'boxed', label: 'Riquadro' },
    ]},
    { key: 'alignment', label: 'Allineamento', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'center', label: 'Centro' },
      { value: 'right', label: 'Destra' },
      { value: 'stretch', label: 'Distribuito' },
    ]},
    { key: 'divider', label: 'Separatore', type: 'toggle' },
    { key: 'gap', label: 'Gap (px)', type: 'range', min: 0, max: 32, step: 2 },
    { key: 'tile_padding', label: 'Padding (px)', type: 'spacing', max: 24 },
    { key: 'border_radius', label: 'Raggio bordo (px)', type: 'border-radius' },

    // --- TIPOGRAFIA ---
    { key: '_section_typo', label: 'TIPOGRAFIA', type: 'separator' },
    { key: 'font_size', label: 'Dimensione (px)', type: 'range', min: 11, max: 20, step: 1 },
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
      { value: 'capitalize', label: 'Capitalizzato' },
    ]},

    // --- COLORI ---
    { key: '_section_colors', label: 'COLORI', type: 'separator' },
    { key: 'link_color', label: 'Colore link', type: 'color' },
    { key: 'hover_color', label: 'Colore hover', type: 'color' },
    { key: 'active_color', label: 'Colore attivo', type: 'color' },
    { key: 'bg_color', label: 'Sfondo', type: 'color' },
    { key: 'hover_bg', label: 'Sfondo hover', type: 'color' },
    { key: 'active_bg', label: 'Sfondo attivo', type: 'color' },
    { key: 'active_style', label: 'Indicatore attivo', type: 'select', options: [
      { value: 'none', label: 'Nessuno' },
      { value: 'underline', label: 'Sottolineatura' },
      { value: 'background', label: 'Sfondo' },
      { value: 'bold', label: 'Grassetto' },
    ]},
    { key: 'highlight_current', label: 'Evidenzia pagina corrente', type: 'toggle' },

    ...shadowField,
  ],
};
