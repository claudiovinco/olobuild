export default {
  type: 'megamenu',
  name: 'Mega Menu',
  icon: 'dashicons-menu-alt3',
  category: 'header',
  defaults: {
    // Menu
    menu_id: 0,

    // Navbar
    layout: 'left',
    nav_bg: '',
    nav_height: '0',
    text_color: '',
    hover_color: '',
    active_color: '',
    font_size: '15',
    font_weight: 'normal',
    text_transform: 'none',
    letter_spacing: '0',
    item_gap: '15',

    // Mega Panel
    mega_mode: 'auto',
    panel_width: 'container',
    panel_columns: '4',
    panel_bg: '#FFFFFF',
    panel_shadow: 'md',
    panel_radius: '8',
    panel_padding: '32',
    panel_border_top: '3',
    panel_border_color: '',
    panel_animation: 'fade',
    show_dividers: false,

    // Panel Typography
    heading_color: '',
    heading_size: '14',
    heading_weight: '600',
    heading_transform: 'uppercase',
    link_color: '',
    link_hover_color: '',
    link_size: '14',
    link_spacing: '8',
    show_descriptions: false,
    desc_color: '',

    // Buttons (CTA items)
    button_mode: 'none',
    btn_bg: '',
    btn_color: '#FFFFFF',
    btn_radius: '6',
    btn_hover_bg: '',

    // Mobile
    mobile_breakpoint: '1024',
    mobile_side: 'left',
    hamburger_color: '',
    mobile_bg: '#1e1e2e',
    mobile_text_color: '#FFFFFF',
    mobile_heading_color: '',
    mobile_accent_color: '',
    mobile_logo: '',
    mobile_logo_height: '36',

    // Header
    header_mode: 'overlay',
    sticky: false,
    sticky_show_on_up: false,
    sticky_bg: '',
    sticky_shadow: true,
  },
  fields: [
    // -- Menu --
    { key: 'menu_id', label: 'Menu WordPress', type: 'select', optionsSource: 'wpMenus' },

    // -- Navbar --
    { type: 'separator', label: 'Navbar' },
    { key: 'layout', label: 'Allineamento', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'center', label: 'Centro' },
      { value: 'right', label: 'Destra' },
    ]},
    { key: 'nav_bg', label: 'Sfondo navbar', type: 'color' },
    { key: 'nav_height', label: 'Altezza min (px)', type: 'range', min: 40, max: 120, step: 2 },
    { key: 'text_color', label: 'Colore testo', type: 'color' },
    { key: 'hover_color', label: 'Colore hover', type: 'color' },
    { key: 'active_color', label: 'Colore attivo', type: 'color' },
    { key: 'font_size', label: 'Dimensione font (px)', type: 'range', min: 12, max: 22, step: 1 },
    { key: 'font_weight', label: 'Peso font', type: 'select', options: [
      { value: 'normal', label: 'Normale' },
      { value: '500', label: 'Medio' },
      { value: '600', label: 'Semi-grassetto' },
      { value: 'bold', label: 'Grassetto' },
    ]},
    { key: 'text_transform', label: 'Trasformazione', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'uppercase', label: 'Maiuscolo' },
      { value: 'capitalize', label: 'Capitalizza' },
    ]},
    { key: 'letter_spacing', label: 'Spaziatura lettere (px)', type: 'range', min: 0, max: 5, step: 0.5 },
    { key: 'item_gap', label: 'Gap tra voci (px)', type: 'range', min: 0, max: 40, step: 1 },

    // -- Mega Panel --
    { type: 'separator', label: 'Mega Panel' },
    { key: 'mega_mode', label: 'Attivazione mega', type: 'select', options: [
      { value: 'auto', label: 'Automatico (tutte con figli)' },
      { value: 'css-class', label: 'Solo classe mega-menu' },
    ]},
    { key: 'panel_width', label: 'Larghezza panel', type: 'select', options: [
      { value: 'container', label: 'Contenitore' },
      { value: 'full', label: 'Full-width' },
    ]},
    { key: 'panel_columns', label: 'Colonne', type: 'range', min: 2, max: 6, step: 1 },
    { key: 'panel_bg', label: 'Sfondo panel', type: 'color' },
    { key: 'panel_shadow', label: 'Ombra', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'sm', label: 'Leggera' },
      { value: 'md', label: 'Media' },
      { value: 'lg', label: 'Grande' },
    ]},
    { key: 'panel_radius', label: 'Arrotondamento (px)', type: 'range', min: 0, max: 20, step: 1 },
    { key: 'panel_padding', label: 'Padding (px)', type: 'range', min: 16, max: 60, step: 2 },
    { key: 'panel_border_top', label: 'Linea accento top (px)', type: 'range', min: 0, max: 5, step: 1 },
    { key: 'panel_border_color', label: 'Colore linea accento', type: 'color' },
    { key: 'panel_animation', label: 'Animazione', type: 'select', options: [
      { value: 'fade', label: 'Dissolvenza' },
      { value: 'slide-down', label: 'Scorrimento' },
    ]},
    { key: 'show_dividers', label: 'Divisori tra colonne', type: 'toggle' },

    // -- Tipografia Panel --
    { type: 'separator', label: 'Tipografia panel' },
    { key: 'heading_color', label: 'Colore intestazioni', type: 'color' },
    { key: 'heading_size', label: 'Dim. intestazioni (px)', type: 'range', min: 12, max: 20, step: 1 },
    { key: 'heading_weight', label: 'Peso intestazioni', type: 'select', options: [
      { value: '500', label: 'Medio' },
      { value: '600', label: 'Semi-grassetto' },
      { value: '700', label: 'Grassetto' },
    ]},
    { key: 'heading_transform', label: 'Trasformazione intestaz.', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'uppercase', label: 'Maiuscolo' },
    ]},
    { key: 'link_color', label: 'Colore link', type: 'color' },
    { key: 'link_hover_color', label: 'Colore link hover', type: 'color' },
    { key: 'link_size', label: 'Dim. link (px)', type: 'range', min: 12, max: 18, step: 1 },
    { key: 'link_spacing', label: 'Gap verticale link (px)', type: 'range', min: 4, max: 16, step: 1 },
    { key: 'show_descriptions', label: 'Mostra descrizioni', type: 'toggle' },
    { key: 'desc_color', label: 'Colore descrizioni', type: 'color',
      condition: { field: 'show_descriptions', value: true } },

    // -- Pulsanti CTA --
    { type: 'separator', label: 'Pulsanti CTA' },
    { key: 'button_mode', label: 'Modalita pulsanti', type: 'select', options: [
      { value: 'none', label: 'Nessuno' },
      { value: 'last', label: 'Ultimo' },
      { value: 'last-2', label: 'Ultimi 2' },
      { value: 'css-class', label: 'Classe CSS olo-btn' },
    ]},
    { key: 'btn_bg', label: 'Sfondo pulsante', type: 'color',
      condition: { field: 'button_mode', operator: '!=', value: 'none' } },
    { key: 'btn_color', label: 'Colore testo pulsante', type: 'color',
      condition: { field: 'button_mode', operator: '!=', value: 'none' } },
    { key: 'btn_radius', label: 'Arrotondamento pulsante (px)', type: 'range', min: 0, max: 30, step: 1,
      condition: { field: 'button_mode', operator: '!=', value: 'none' } },
    { key: 'btn_hover_bg', label: 'Sfondo pulsante hover', type: 'color',
      condition: { field: 'button_mode', operator: '!=', value: 'none' } },

    // -- Mobile --
    { type: 'separator', label: 'Mobile' },
    { key: 'mobile_breakpoint', label: 'Breakpoint mobile', type: 'select', options: [
      { value: '768', label: '768px (Tablet)' },
      { value: '1024', label: '1024px (Desktop)' },
    ]},
    { key: 'mobile_side', label: 'Lato off-canvas', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'right', label: 'Destra' },
    ]},
    { key: 'hamburger_color', label: 'Colore hamburger', type: 'color' },
    { key: 'mobile_bg', label: 'Sfondo off-canvas', type: 'color' },
    { key: 'mobile_text_color', label: 'Colore testo mobile', type: 'color' },
    { key: 'mobile_heading_color', label: 'Colore intestazioni mobile', type: 'color' },
    { key: 'mobile_accent_color', label: 'Colore accento mobile', type: 'color' },
    { key: 'mobile_logo', label: 'Logo off-canvas', type: 'image' },
    { key: 'mobile_logo_height', label: 'Altezza logo (px)', type: 'range', min: 20, max: 60, step: 2 },

    // -- Header --
    { type: 'separator', label: 'Header' },
    { key: 'header_mode', label: 'Modalita header', type: 'select', options: [
      { value: 'overlay', label: 'Overlay (trasparente)' },
      { value: 'classic', label: 'Classico' },
    ]},
    { key: 'sticky', label: 'Header sticky', type: 'toggle' },
    { key: 'sticky_show_on_up', label: 'Mostra su scroll up', type: 'toggle',
      condition: { field: 'sticky', value: true } },
    { key: 'sticky_bg', label: 'Sfondo sticky', type: 'color',
      condition: { field: 'sticky', value: true } },
    { key: 'sticky_shadow', label: 'Ombra sticky', type: 'toggle',
      condition: { field: 'sticky', value: true } },
  ],
};
