export default {
  type: 'calendar',
  name: 'Calendario Eventi',
  icon: 'dashicons-calendar-alt',
  category: 'booking',
  defaults: {
    // Vista
    default_view: 'dayGridMonth',
    calendar_height: 'auto',
    show_toolbar: true,
    locale: 'it',

    // Viste visibili
    show_today_btn: true,
    show_month_view: true,
    show_week_view: true,
    show_day_view: true,
    show_list_view: true,

    // Filtro categorie
    show_category_filter: true,
    filter_style: 'pills',
    filter_position: 'above',
    filter_gap: '8',
    filter_margin_bottom: '16',

    // Toolbar container
    toolbar_bg: '',
    toolbar_border_color: '',
    toolbar_border_width: '0',
    toolbar_padding_x: '4',
    toolbar_padding_y: '10',
    toolbar_margin_bottom: '0',
    toolbar_radius: '0',

    // Toolbar titolo
    toolbar_title_size: '20',
    toolbar_title_weight: '600',
    toolbar_title_color: '',
    toolbar_title_transform: 'capitalize',

    // Toolbar pulsanti
    toolbar_btn_bg: '',
    toolbar_btn_color: '',
    toolbar_btn_hover_bg: '',
    toolbar_btn_active_bg: '',
    toolbar_btn_active_color: '',
    toolbar_btn_radius: '8',
    toolbar_btn_padding_x: '14',
    toolbar_btn_padding_y: '6',
    toolbar_btn_font_size: '13',
    toolbar_btn_font_weight: '500',
    toolbar_btn_border_width: '0',
    toolbar_btn_border_color: '',
    toolbar_btn_text_transform: 'capitalize',
    toolbar_btn_shadow: false,

    // Stile calendario
    header_bg: '',
    header_color: '',
    today_bg: '',
    day_number_size: '13',
    border_color: '',

    // Stile eventi
    event_radius: '4',
    event_font_size: '12',

    // Modale
    modal_show_image: true,
    modal_show_excerpt: true,
    modal_show_link: true,
  },
  fields: [
    // ── Vista ──
    { type: 'separator', label: 'Vista' },
    { key: 'default_view', label: 'Vista iniziale', type: 'select', options: [
      { value: 'dayGridMonth', label: 'Mese (griglia)' },
      { value: 'timeGridWeek', label: 'Settimana' },
      { value: 'timeGridDay', label: 'Giorno' },
      { value: 'listMonth', label: 'Lista mensile' },
    ]},
    { key: 'calendar_height', label: 'Altezza', type: 'select', options: [
      { value: 'auto', label: 'Automatica' },
      { value: '500', label: '500px' },
      { value: '600', label: '600px' },
      { value: '700', label: '700px' },
      { value: '800', label: '800px' },
    ]},
    { key: 'locale', label: 'Lingua', type: 'select', options: [
      { value: 'it', label: 'Italiano' },
      { value: 'en', label: 'English' },
      { value: 'fr', label: 'Français' },
      { value: 'de', label: 'Deutsch' },
      { value: 'es', label: 'Español' },
    ]},

    // ── Toolbar ──
    { type: 'separator', label: 'Barra navigazione' },
    { key: 'show_toolbar', label: 'Mostra toolbar', type: 'toggle' },
    { key: 'show_today_btn', label: 'Pulsante Oggi', type: 'toggle',
      condition: { field: 'show_toolbar', op: 'eq', value: true } },
    { key: 'show_month_view', label: 'Vista Mese', type: 'toggle',
      condition: { field: 'show_toolbar', op: 'eq', value: true } },
    { key: 'show_week_view', label: 'Vista Settimana', type: 'toggle',
      condition: { field: 'show_toolbar', op: 'eq', value: true } },
    { key: 'show_day_view', label: 'Vista Giorno', type: 'toggle',
      condition: { field: 'show_toolbar', op: 'eq', value: true } },
    { key: 'show_list_view', label: 'Vista Lista', type: 'toggle',
      condition: { field: 'show_toolbar', op: 'eq', value: true } },

    // ── Toolbar contenitore ──
    { type: 'separator', label: 'Stile toolbar' },
    { key: 'toolbar_bg', label: 'Sfondo toolbar', type: 'color' },
    { key: 'toolbar_border_color', label: 'Colore bordo inferiore', type: 'color' },
    { key: 'toolbar_border_width', label: 'Spessore bordo (px)', type: 'range', min: 0, max: 4 },
    { key: 'toolbar_padding_x', label: 'Padding orizzontale (px)', type: 'spacing', max: 32 },
    { key: 'toolbar_padding_y', label: 'Padding verticale (px)', type: 'spacing', max: 24 },
    { key: 'toolbar_margin_bottom', label: 'Margine inferiore (px)', type: 'spacing', max: 32 },
    { key: 'toolbar_radius', label: 'Raggio bordo toolbar (px)', type: 'border-radius' },

    // ── Toolbar titolo ──
    { type: 'separator', label: 'Titolo toolbar' },
    { key: 'toolbar_title_size', label: 'Dimensione (px)', type: 'range', min: 14, max: 32 },
    { key: 'toolbar_title_weight', label: 'Peso', type: 'select', options: [
      { value: '400', label: 'Normal' },
      { value: '500', label: 'Medium' },
      { value: '600', label: 'Semibold' },
      { value: '700', label: 'Bold' },
    ]},
    { key: 'toolbar_title_color', label: 'Colore', type: 'color' },
    { key: 'toolbar_title_transform', label: 'Trasformazione', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'capitalize', label: 'Iniziale maiuscola' },
      { value: 'uppercase', label: 'MAIUSCOLO' },
    ]},

    // ── Toolbar pulsanti ──
    { type: 'separator', label: 'Pulsanti toolbar' },
    { key: 'toolbar_btn_bg', label: 'Sfondo', type: 'color' },
    { key: 'toolbar_btn_color', label: 'Colore testo', type: 'color' },
    { key: 'toolbar_btn_hover_bg', label: 'Sfondo hover', type: 'color' },
    { key: 'toolbar_btn_active_bg', label: 'Sfondo attivo', type: 'color' },
    { key: 'toolbar_btn_active_color', label: 'Colore testo attivo', type: 'color' },
    { key: 'toolbar_btn_radius', label: 'Raggio bordo (px)', type: 'border-radius' },
    { key: 'toolbar_btn_padding_x', label: 'Padding orizzontale (px)', type: 'spacing', max: 24 },
    { key: 'toolbar_btn_padding_y', label: 'Padding verticale (px)', type: 'spacing', max: 14 },
    { key: 'toolbar_btn_font_size', label: 'Dimensione testo (px)', type: 'range', min: 11, max: 16 },
    { key: 'toolbar_btn_font_weight', label: 'Peso testo', type: 'select', options: [
      { value: '400', label: 'Normal' },
      { value: '500', label: 'Medium' },
      { value: '600', label: 'Semibold' },
      { value: '700', label: 'Bold' },
    ]},
    { key: 'toolbar_btn_text_transform', label: 'Trasformazione testo', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'capitalize', label: 'Iniziale maiuscola' },
      { value: 'uppercase', label: 'MAIUSCOLO' },
      { value: 'lowercase', label: 'minuscolo' },
    ]},
    { key: 'toolbar_btn_border_width', label: 'Bordo (px)', type: 'range', min: 0, max: 3 },
    { key: 'toolbar_btn_border_color', label: 'Colore bordo', type: 'color',
      condition: { field: 'toolbar_btn_border_width', operator: '>', value: 0 } },
    { key: 'toolbar_btn_shadow', label: 'Ombra pulsanti', type: 'toggle' },

    // ── Filtro categorie ──
    { type: 'separator', label: 'Filtro categorie' },
    { key: 'show_category_filter', label: 'Mostra filtro categorie', type: 'toggle' },
    { key: 'filter_style', label: 'Stile filtro', type: 'select', options: [
      { value: 'pills', label: 'Pillole' },
      { value: 'checkboxes', label: 'Caselle di controllo' },
      { value: 'minimal', label: 'Minimale (solo testo)' },
    ],
      condition: { field: 'show_category_filter', op: 'eq', value: true } },
    { key: 'filter_position', label: 'Posizione', type: 'select', options: [
      { value: 'above', label: 'Sopra il calendario' },
      { value: 'in-toolbar', label: 'Nella toolbar' },
    ],
      condition: { field: 'show_category_filter', op: 'eq', value: true } },
    { key: 'filter_gap', label: 'Gap tra filtri (px)', type: 'range', min: 4, max: 16,
      condition: { field: 'show_category_filter', op: 'eq', value: true } },
    { key: 'filter_margin_bottom', label: 'Margine inferiore (px)', type: 'spacing', max: 32,
      condition: { field: 'show_category_filter', op: 'eq', value: true } },

    // ── Stile calendario ──
    { type: 'separator', label: 'Stile calendario' },
    { key: 'header_bg', label: 'Sfondo intestazione giorni', type: 'color' },
    { key: 'header_color', label: 'Colore testo intestazione', type: 'color' },
    { key: 'today_bg', label: 'Sfondo cella oggi', type: 'color' },
    { key: 'day_number_size', label: 'Dimensione numero giorno (px)', type: 'range', min: 10, max: 20 },
    { key: 'border_color', label: 'Colore bordi griglia', type: 'color' },

    // ── Stile eventi ──
    { type: 'separator', label: 'Stile eventi' },
    { key: 'event_radius', label: 'Raggio bordo eventi (px)', type: 'border-radius' },
    { key: 'event_font_size', label: 'Dimensione testo eventi (px)', type: 'range', min: 10, max: 16 },

    // ── Modale ──
    { type: 'separator', label: 'Modale dettagli' },
    { key: 'modal_show_image', label: 'Mostra immagine', type: 'toggle' },
    { key: 'modal_show_excerpt', label: 'Mostra descrizione', type: 'toggle' },
    { key: 'modal_show_link', label: 'Mostra link dettagli', type: 'toggle' },
  ],
};
