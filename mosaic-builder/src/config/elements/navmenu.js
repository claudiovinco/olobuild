export default {
  type: 'navmenu',
  name: 'Menu Nav',
  icon: 'dashicons-menu',
  category: 'header',
  defaults: {
    menu_id: 0,
    style: 'navbar',
    alignment: 'left',
    mobile_toggle: true,
    mobile_style: 'offcanvas',
    // Stile
    text_color: '',
    hover_color: '',
    active_color: '',
    font_size: 15,
    font_weight: 'normal',
    text_transform: 'none',
    letter_spacing: 0,
    gap: 'medium',
    // Dropdown
    dropdown_bg: '',
    dropdown_color: '',
    // Header
    header_mode: 'overlay',
    // Sticky Header
    sticky: false,
    sticky_show_on_up: false,
    sticky_bg: '',
    sticky_shadow: true,
    // Mega Menu
    mega_menu: 'none',
    mega_columns: 3,
    // Voci pulsante
    button_items: 'none',
    button_style: 'primary',
    button_size: 'small',
  },
  fields: [
    // --- Menu ---
    { key: 'menu_id', label: 'Menu', type: 'select',
      optionsSource: 'wpMenus' },
    { key: 'style', label: 'Stile', type: 'select', options: [
      { value: 'navbar', label: 'Navbar orizzontale' },
      { value: 'subnav', label: 'Link Subnav' },
    ]},
    { key: 'alignment', label: 'Allineamento', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'center', label: 'Centro' },
      { value: 'right', label: 'Destra' },
    ]},
    // --- Tipografia ---
    { key: '_separator_typo', label: 'Tipografia', type: 'separator' },
    { key: 'text_color', label: 'Colore link', type: 'color' },
    { key: 'hover_color', label: 'Colore hover', type: 'color' },
    { key: 'active_color', label: 'Colore attivo/corrente', type: 'color' },
    { key: 'font_size', label: 'Dimensione font (px)', type: 'range', min: 11, max: 24, step: 1 },
    { key: 'font_weight', label: 'Peso font', type: 'select', options: [
      { value: 'normal', label: 'Normale' },
      { value: '500', label: 'Medio' },
      { value: '600', label: 'Semi-grassetto' },
      { value: 'bold', label: 'Grassetto' },
    ]},
    { key: 'text_transform', label: 'Trasformazione testo', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'uppercase', label: 'Maiuscolo' },
      { value: 'capitalize', label: 'Iniziale maiuscola' },
    ]},
    { key: 'letter_spacing', label: 'Spaziatura lettere (px)', type: 'range', min: 0, max: 5, step: 0.5 },
    { key: 'gap', label: 'Gap elementi', type: 'select', options: [
      { value: 'small', label: 'Piccolo' },
      { value: 'medium', label: 'Medio' },
      { value: 'large', label: 'Grande' },
    ]},
    // --- Dropdown ---
    { key: '_separator_dropdown', label: 'Dropdown', type: 'separator' },
    { key: 'dropdown_bg', label: 'Sfondo dropdown', type: 'color' },
    { key: 'dropdown_color', label: 'Colore testo dropdown', type: 'color' },
    // --- Header ---
    { key: '_separator_header', label: 'Header', type: 'separator' },
    { key: 'header_mode', label: 'Modalità header', type: 'select', options: [
      { value: 'overlay', label: 'Sovrapposto (sopra il contenuto)' },
      { value: 'classic', label: 'Classico (spinge il contenuto)' },
    ]},
    { key: 'sticky', label: 'Abilita sticky', type: 'toggle' },
    { key: 'sticky_show_on_up', label: 'Mostra allo scroll in su', type: 'toggle',
      show: s => !!s.sticky },
    { key: 'sticky_bg', label: 'Sfondo sticky', type: 'color',
      show: s => !!s.sticky },
    { key: 'sticky_shadow', label: 'Ombra sticky', type: 'toggle',
      show: s => !!s.sticky },
    // --- Mega Menu ---
    { key: '_separator_mega', label: 'Mega Menu', type: 'separator' },
    { key: 'mega_menu', label: 'Mega Menu', type: 'select', options: [
      { value: 'none', label: 'Disabilitato' },
      { value: 'auto', label: 'Auto (tutti i parent)' },
      { value: 'class', label: 'Classe CSS (mega-menu)' },
    ]},
    { key: 'mega_columns', label: 'Colonne', type: 'select',
      show: s => s.mega_menu && s.mega_menu !== 'none',
      options: [
        { value: 2, label: '2 colonne' },
        { value: 3, label: '3 colonne' },
        { value: 4, label: '4 colonne' },
      ]},
    // --- Voci pulsante ---
    { key: '_separator_buttons', label: 'Voci pulsante', type: 'separator' },
    { key: 'button_items', label: 'Voci pulsante', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'last', label: 'Ultima voce' },
      { value: 'last-2', label: 'Ultime 2 voci' },
      { value: 'css-class', label: 'Classe CSS (olo-btn)' },
    ]},
    { key: 'button_style', label: 'Stile pulsante', type: 'select',
      show: s => s.button_items && s.button_items !== 'none',
      options: [
        { value: 'default', label: 'Predefinito' },
        { value: 'primary', label: 'Primary' },
        { value: 'secondary', label: 'Secondary' },
      ]},
    { key: 'button_size', label: 'Dimensione pulsante', type: 'select',
      show: s => s.button_items && s.button_items !== 'none',
      options: [
        { value: 'small', label: 'Piccolo' },
        { value: 'default', label: 'Predefinito' },
      ]},
    // --- Mobile ---
    { key: '_separator_mobile', label: 'Mobile', type: 'separator' },
    { key: 'mobile_toggle', label: 'Hamburger mobile', type: 'toggle' },
    { key: 'mobile_style', label: 'Stile mobile', type: 'select', options: [
      { value: 'offcanvas', label: 'Pannello offcanvas' },
      { value: 'dropdown', label: 'Dropdown' },
    ]},
  ],
};
