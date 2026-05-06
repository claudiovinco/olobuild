
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
export default {
  type: 'mobilebar',
  name: 'Mobile Bar',
  icon: 'dashicons-smartphone',
  category: 'navigation',

  defaults: {
    breakpoint:          '1024',
    logo_image:          '',
    logo_width:          '120',
    logo_link:           '',
    bar_bg:              '#1a3a5c',
    bar_height:          '56',
    bar_shadow:          true,
    tile_padding: { top: 12, right: 12, bottom: 12, left: 12 },
    hamburger_style:     'classic',
    hamburger_size:      '28',
    hamburger_color:     '#ffffff',
    menu_id:             '',
    panel_bg:            '#ffffff',
    panel_text_color:    '#222222',
    panel_active_color:  '',
    panel_font_size:     '17',
    panel_item_padding:  '16',
    panel_separator:     true,
    panel_chevron_color: '#999999',
    search_enabled:      true,
    search_icon_color:   '#ffffff',
    search_placeholder:  'Cerca...',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    // ── Generale ──
    { type: 'separator', label: 'Generale' },
    { key: 'breakpoint', label: 'Breakpoint visibilità (px)', type: 'range', min: 480, max: 1400, step: 10 },

    // ── Logo ──
    { type: 'separator', label: 'Logo' },
    { key: 'logo_image', label: 'Immagine logo', type: 'image' },
    { key: 'logo_width', label: 'Larghezza logo (px)', type: 'range', min: 40, max: 250, step: 5 },
    { key: 'logo_link', label: 'Link logo (vuoto = home)', type: 'text' },

    // ── Barra ──
    { type: 'separator', label: 'Barra' },
    { key: 'bar_bg', label: 'Sfondo barra', type: 'color' },
    { key: 'bar_height', label: 'Altezza barra (px)', type: 'range', min: 40, max: 80, step: 2 },
    { key: 'bar_shadow', label: 'Ombra barra', type: 'toggle' },
    { key: 'tile_padding', label: 'Padding (px)', type: 'spacing', max: 30 },

    // ── Hamburger ──
    { type: 'separator', label: 'Hamburger' },
    { key: 'hamburger_style', label: 'Stile', type: 'select', options: [
      { value: 'classic', label: 'Classic (3 linee → X)' },
      { value: 'squeeze', label: 'Squeeze (comprime → X)' },
      { value: 'arrow', label: 'Arrow (→ freccia)' },
      { value: 'dot-grid', label: 'Dot Grid (9 pallini)' },
      { value: 'minimal', label: 'Minimal (2 linee)' },
    ]},
    { key: 'hamburger_size', label: 'Dimensione (px)', type: 'range', min: 20, max: 44, step: 2 },
    { key: 'hamburger_color', label: 'Colore', type: 'color' },

    // ── Menu ──
    { type: 'separator', label: 'Menu' },
    { key: 'menu_id', label: 'Menu WordPress', type: 'select', optionsSource: 'wpMenus' },

    // ── Pannello Menu ──
    { type: 'separator', label: 'Pannello menu' },
    { key: 'panel_bg', label: 'Sfondo pannello', type: 'color' },
    { key: 'panel_text_color', label: 'Colore testo', type: 'color' },
    { key: 'panel_active_color', label: 'Colore voce attiva', type: 'color' },
    { key: 'panel_font_size', label: 'Dimensione testo (px)', type: 'range', min: 14, max: 24, step: 1 },
    { key: 'panel_item_padding', label: 'Padding voci (px)', type: 'spacing', max: 30 },
    { key: 'panel_separator', label: 'Separatore tra voci', type: 'toggle' },
    { key: 'panel_chevron_color', label: 'Colore chevron', type: 'color' },

    // ── Ricerca ──
    { type: 'separator', label: 'Ricerca' },
    { key: 'search_enabled', label: 'Mostra icona ricerca', type: 'toggle' },
    { key: 'search_icon_color', label: 'Colore icona ricerca', type: 'color',
      condition: { field: 'search_enabled', value: true } },
    { key: 'search_placeholder', label: 'Placeholder ricerca', type: 'text',
      condition: { field: 'search_enabled', value: true } },
    ...borderFields(),
  ],
};
