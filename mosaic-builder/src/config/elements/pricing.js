export default {
  type: 'pricing',
  name: 'Listino prezzi',
  icon: 'dashicons-money-alt',
  category: 'content',
  defaults: {
    // Contenuto
    plan_name: 'Piano Pro',
    price: '29',
    currency: '€',
    currency_size: '14',
    period: '/mese',
    features: 'Progetti illimitati\n10 GB di spazio\nSupporto prioritario\nDominio personalizzato',

    // Badge
    is_popular: false,
    badge_text: 'Popolare',
    badge_style: 'pill',
    badge_radius: '20',
    badge_top: '-12',
    badge_bg_color: '',
    badge_text_color: '#FFFFFF',

    // Prezzo forma
    price_shape: 'none',
    price_shape_color: '#374151',
    price_shape_glow: false,
    price_shape_glow_color: '#6366F1',
    price_shape_glow_intensity: '15',
    price_shape_border_width: '0',
    price_shape_border_color: '#6366F1',

    // Funzionalità
    check_style: 'checkmark',

    // Colori
    bg_color: '#1F2937',
    accent_color: '#6366F1',
    text_color: '#F3F4F6',

    // Sfondo
    bg_type: 'color',
    bg_image: '',
    bg_video: '',
    overlay: false,
    overlay_color: '#000000',
    overlay_opacity: '50',

    // Pulsante
    cta_text: 'Inizia ora',
    cta_url: '#',
    cta_target: '_self',
    cta_bg_color: '',
    cta_text_color: '#FFFFFF',
    cta_radius: '8',
    cta_border_width: '0',
    cta_border_color: '#FFFFFF',
    cta_hover_effect: 'lift',
    cta_width: '100',
    cta_hover_bg_color: '',
    cta_hover_text_color: '',

    // Tile
    border_radius: '12',
    border_width: '0',
    border_color: '#374151',
  },
  fields: [
    // ── Contenuto ──
    { key: 'plan_name', label: 'Nome piano', type: 'editor', mode: 'inline' },
    { key: 'price', label: 'Prezzo', type: 'text' },
    { key: 'currency', label: 'Valuta', type: 'select', options: [
      { value: '€', label: '€ Euro' },
      { value: '$', label: '$ Dollaro' },
      { value: '£', label: '£ Sterlina' },
      { value: '', label: 'Nessuna' },
    ]},
    { key: 'currency_size', label: 'Dimensione valuta (px)', type: 'range', min: 10, max: 40, step: 1 },
    { key: 'period', label: 'Periodo', type: 'editor', mode: 'inline' },
    { key: 'features', label: 'Funzionalità (una per riga)', type: 'textarea' },

    // ── Funzionalità ──
    { type: 'separator', label: 'Stile funzionalità' },
    { key: 'check_style', label: 'Icona spunta', type: 'select', options: [
      { value: 'checkmark', label: '✓ Spunta' },
      { value: 'circle-check', label: '● Cerchio pieno' },
      { value: 'dot', label: '• Punto' },
      { value: 'star', label: '★ Stella' },
      { value: 'arrow', label: '→ Freccia' },
      { value: 'none', label: 'Nessuna' },
    ]},

    // ── Badge ──
    { type: 'separator', label: 'Badge' },
    { key: 'is_popular', label: 'Mostra badge', type: 'toggle' },
    { key: 'badge_text', label: 'Testo badge', type: 'text',
      condition: { field: 'is_popular', value: true } },
    { key: 'badge_style', label: 'Stile badge', type: 'select', options: [
      { value: 'pill', label: 'Pillola' },
      { value: 'classic', label: 'Classico' },
      { value: 'minimal', label: 'Minimale' },
    ], condition: { field: 'is_popular', value: true } },
    { key: 'badge_top', label: 'Posizione verticale badge (px)', type: 'range', min: -20, max: 40, step: 1,
      condition: { field: 'is_popular', value: true } },
    { key: 'badge_radius', label: 'Raggio bordo badge (px)', type: 'range', min: 0, max: 30, step: 1,
      condition: { field: 'is_popular', value: true } },
    { key: 'badge_bg_color', label: 'Sfondo badge', type: 'color',
      condition: { field: 'is_popular', value: true } },
    { key: 'badge_text_color', label: 'Testo badge', type: 'color',
      condition: { field: 'is_popular', value: true } },

    // ── Prezzo forma ──
    { type: 'separator', label: 'Forma prezzo' },
    { key: 'price_shape', label: 'Forma', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'circle', label: 'Cerchio' },
      { value: 'rounded', label: 'Quadrato arrotondato' },
    ]},
    { key: 'price_shape_color', label: 'Colore forma', type: 'color',
      condition: { field: 'price_shape', operator: '!=', value: 'none' } },
    { key: 'price_shape_glow', label: 'Luce interna', type: 'toggle',
      condition: { field: 'price_shape', operator: '!=', value: 'none' } },
    { key: 'price_shape_glow_color', label: 'Colore luce', type: 'color',
      condition: { field: 'price_shape_glow', value: true } },
    { key: 'price_shape_glow_intensity', label: 'Intensità luce (px)', type: 'range', min: 5, max: 40, step: 5,
      condition: { field: 'price_shape_glow', value: true } },
    { key: 'price_shape_border_width', label: 'Bordo forma (px)', type: 'range', min: 0, max: 5, step: 1,
      condition: { field: 'price_shape', operator: '!=', value: 'none' } },
    { key: 'price_shape_border_color', label: 'Colore bordo forma', type: 'color',
      condition: { field: 'price_shape_border_width', operator: '>', value: '0' } },

    // ── Colori ──
    { type: 'separator', label: 'Colori' },
    { key: 'bg_color', label: 'Colore sfondo', type: 'color' },
    { key: 'accent_color', label: 'Colore accento', type: 'color' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },

    // ── Sfondo ──
    { type: 'separator', label: 'Sfondo' },
    { key: 'bg_type', label: 'Tipo sfondo', type: 'select', options: [
      { value: 'color', label: 'Colore' },
      { value: 'image', label: 'Immagine' },
      { value: 'video', label: 'Video' },
    ]},
    { key: 'bg_image', label: 'Immagine sfondo', type: 'image',
      condition: { field: 'bg_type', value: 'image' } },
    { key: 'bg_video', label: 'Video sfondo (mp4)', type: 'media',
      condition: { field: 'bg_type', value: 'video' } },
    { key: 'overlay', label: 'Overlay', type: 'toggle',
      condition: { field: 'bg_type', operator: '!=', value: 'color' } },
    { key: 'overlay_color', label: 'Colore overlay', type: 'color',
      condition: { field: 'overlay', value: true } },
    { key: 'overlay_opacity', label: 'Opacità overlay (%)', type: 'range', min: 10, max: 100, step: 5,
      condition: { field: 'overlay', value: true } },

    // ── Pulsante ──
    { type: 'separator', label: 'Pulsante' },
    { key: 'cta_text', label: 'Testo pulsante', type: 'editor', mode: 'inline' },
    { key: 'cta_url', label: 'URL pulsante', type: 'text' },
    { key: 'cta_target', label: 'Apri in', type: 'select', options: [
      { value: '_self', label: 'Stessa finestra' },
      { value: '_blank', label: 'Nuova scheda' },
    ]},
    { key: 'cta_bg_color', label: 'Sfondo pulsante', type: 'color' },
    { key: 'cta_text_color', label: 'Testo pulsante', type: 'color' },
    { key: 'cta_width', label: 'Larghezza pulsante (%)', type: 'range', min: 30, max: 100, step: 5 },
    { key: 'cta_radius', label: 'Raggio bordo (px)', type: 'range', min: 0, max: 30, step: 1 },
    { key: 'cta_border_width', label: 'Bordo pulsante (px)', type: 'range', min: 0, max: 5, step: 1 },
    { key: 'cta_border_color', label: 'Colore bordo pulsante', type: 'color',
      condition: { field: 'cta_border_width', operator: '>', value: '0' } },
    { key: 'cta_hover_effect', label: 'Animazione hover', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'lift', label: 'Solleva' },
      { value: 'grow', label: 'Ingrandisci' },
      { value: 'glow', label: 'Bagliore' },
      { value: 'pulse', label: 'Pulsazione' },
      { value: 'shine', label: 'Luccichio' },
    ]},
    { key: 'cta_hover_bg_color', label: 'Sfondo hover', type: 'color' },
    { key: 'cta_hover_text_color', label: 'Testo hover', type: 'color' },

    // ── Tile ──
    { type: 'separator', label: 'Aspetto tile' },
    { key: 'border_radius', label: 'Arrotondamento (px)', type: 'range', min: 0, max: 30, step: 1 },
    { key: 'border_width', label: 'Bordo tile (px)', type: 'range', min: 0, max: 5, step: 1 },
    { key: 'border_color', label: 'Colore bordo tile', type: 'color',
      condition: { field: 'border_width', operator: '>', value: '0' } },
  ],
};
