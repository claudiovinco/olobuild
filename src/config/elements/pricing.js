import { shadowField } from './_shared.js';

export default {
  type: 'pricing',
  name: 'Listino prezzi',
  icon: 'dashicons-money-alt',
  category: 'marketing',
  defaults: {
    // Contenuto
    plan_name: 'Piano Pro',
    price: '29',
    currency: '€',
    currency_size: '14',
    currency_position: 'before',
    period: '/mese',

    // Toggle mensile/annuale
    enable_toggle: false,
    toggle_label_1: 'Mensile',
    toggle_label_2: 'Annuale',
    toggle_color: '',
    price_yearly: '',

    // Prezzo scontato
    sale_price: '',
    sale_badge_text: 'OFFERTA',
    sale_badge_color: '',

    // Funzionalità
    features: 'Progetti illimitati\n10 GB di spazio\nSupporto prioritario\nDominio personalizzato',
    check_style: 'checkmark',
    check_size: '14',
    feature_dividers: true,

    // Pulsante
    cta_text: 'Inizia ora',
    cta_url: '#',
    cta_target: '_self',
    cta_bg_color: '',
    cta_text_color: '',
    cta_width: '100',
    cta_radius: '8',
    cta_border_width: '0',
    cta_border_color: '',
    cta_hover_effect: 'lift',
    cta_hover_bg_color: '',
    cta_hover_text_color: '',
    additional_info: '',

    // Badge
    is_popular: false,
    badge_text: 'Popolare',
    badge_style: 'pill',
    badge_radius: '20',
    badge_top: '-12',
    badge_bg_color: '',
    badge_text_color: '',

    // Forma prezzo
    price_shape: 'none',
    price_shape_color: '',
    price_shape_glow: false,
    price_shape_glow_color: '',
    price_shape_glow_intensity: '15',
    price_shape_border_width: '0',
    price_shape_border_color: '',

    // Countdown
    countdown_enabled: false,
    countdown_date: '',
    countdown_label: 'Offerta scade tra:',
    countdown_expired_text: 'Offerta scaduta',
    countdown_hide_on_expire: false,
    countdown_bg_color: '',
    countdown_text_color: '',

    // Colori
    price_color: '',
    bg_color: '',
    accent_color: '',
    text_color: '',

    // Sfondo
    bg_type: 'color',
    bg_image: '',
    bg_video: '',
    overlay: false,
    overlay_color: '#000000',
    overlay_opacity: '50',

    // Aspetto tile
    border_radius: '12',
    border_width: '0',
    border_color: '',
    shadow: 'none',
  },
  fields: [
    // ═══════════════════════════════════════
    //  CONTENUTO
    // ═══════════════════════════════════════
    { key: 'plan_name', label: 'Nome piano', type: 'text' },
    { key: 'price', label: 'Prezzo', type: 'text' },
    { key: 'currency', label: 'Valuta', type: 'select', options: [
      { value: '€', label: '€ Euro' },
      { value: '$', label: '$ Dollaro' },
      { value: '£', label: '£ Sterlina' },
      { value: '', label: 'Nessuna' },
    ]},
    { key: 'currency_position', label: 'Posizione valuta', type: 'select', options: [
      { value: 'before', label: 'Prima (€99)' },
      { value: 'after', label: 'Dopo (99€)' },
    ]},
    { key: 'currency_size', label: 'Dimensione valuta (px)', type: 'range', min: 10, max: 40, step: 1 },
    { key: 'period', label: 'Periodo', type: 'text' },

    // ═══════════════════════════════════════
    //  TOGGLE MENSILE / ANNUALE
    // ═══════════════════════════════════════
    { type: 'separator', label: 'Toggle prezzo' },
    { key: 'enable_toggle', label: 'Abilita toggle prezzo', type: 'toggle' },
    { key: 'price_yearly', label: 'Prezzo alternativo', type: 'text',
      condition: { field: 'enable_toggle', value: true } },
    { key: 'toggle_label_1', label: 'Etichetta 1', type: 'text',
      condition: { field: 'enable_toggle', value: true } },
    { key: 'toggle_label_2', label: 'Etichetta 2', type: 'text',
      condition: { field: 'enable_toggle', value: true } },
    { key: 'toggle_color', label: 'Colore toggle', type: 'color',
      condition: { field: 'enable_toggle', value: true } },

    // ═══════════════════════════════════════
    //  PREZZO SCONTATO
    // ═══════════════════════════════════════
    { type: 'separator', label: 'Prezzo scontato' },
    { key: 'sale_price', label: 'Prezzo scontato', type: 'text' },
    { key: 'sale_badge_text', label: 'Testo badge sconto', type: 'text',
      condition: { field: 'sale_price', operator: '!=', value: '' } },
    { key: 'sale_badge_color', label: 'Colore badge sconto', type: 'color',
      condition: { field: 'sale_price', operator: '!=', value: '' } },

    // ═══════════════════════════════════════
    //  FUNZIONALITÀ
    // ═══════════════════════════════════════
    { type: 'separator', label: 'Funzionalità' },
    { key: 'features', label: 'Lista (una per riga)', type: 'textarea' },
    { key: 'check_style', label: 'Icona spunta', type: 'select', options: [
      { value: 'checkmark', label: '✓ Spunta' },
      { value: 'circle-check', label: '● Cerchio pieno' },
      { value: 'dot', label: '• Punto' },
      { value: 'star', label: '★ Stella' },
      { value: 'arrow', label: '→ Freccia' },
      { value: 'none', label: 'Nessuna' },
    ]},
    { key: 'check_size', label: 'Dimensione icona (px)', type: 'range', min: 10, max: 28, step: 1,
      condition: { field: 'check_style', operator: '!=', value: 'none' } },
    { key: 'feature_dividers', label: 'Separatori tra voci', type: 'toggle' },

    // ═══════════════════════════════════════
    //  PULSANTE
    // ═══════════════════════════════════════
    { type: 'separator', label: 'Pulsante' },
    { key: 'cta_text', label: 'Testo', type: 'text' },
    { key: 'cta_url', label: 'URL', type: 'text' },
    { key: 'cta_target', label: 'Apri in', type: 'select', options: [
      { value: '_self', label: 'Stessa finestra' },
      { value: '_blank', label: 'Nuova scheda' },
    ]},
    { key: 'cta_width', label: 'Larghezza (%)', type: 'range', min: 30, max: 100, step: 5 },
    { key: 'cta_radius', label: 'Arrotondamento (px)', type: 'border-radius' },
    { key: 'cta_radius_hover', label: 'Raggio bordo (hover)', type: 'border-radius' },
    { key: 'cta_bg_color', label: 'Sfondo', type: 'color' },
    { key: 'cta_text_color', label: 'Colore testo', type: 'color' },
    { key: 'cta_border_width', label: 'Bordo (px)', type: 'range', min: 0, max: 5, step: 1 },
    { key: 'cta_border_color', label: 'Colore bordo', type: 'color',
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
    { key: 'additional_info', label: 'Info aggiuntive (sotto pulsante)', type: 'text' },

    // ═══════════════════════════════════════
    //  BADGE
    // ═══════════════════════════════════════
    { type: 'separator', label: 'Badge' },
    { key: 'is_popular', label: 'Mostra badge', type: 'toggle' },
    { key: 'badge_text', label: 'Testo', type: 'text',
      condition: { field: 'is_popular', value: true } },
    { key: 'badge_style', label: 'Stile', type: 'select', options: [
      { value: 'pill', label: 'Pillola' },
      { value: 'classic', label: 'Classico' },
      { value: 'minimal', label: 'Minimale' },
    ], condition: { field: 'is_popular', value: true } },
    { key: 'badge_top', label: 'Posizione verticale (px)', type: 'range', min: -20, max: 40, step: 1,
      condition: { field: 'is_popular', value: true } },
    { key: 'badge_radius', label: 'Arrotondamento (px)', type: 'border-radius',
      condition: { field: 'is_popular', value: true } },
    { key: 'badge_radius_hover', label: 'Raggio bordo (hover)', type: 'border-radius' },
    { key: 'badge_bg_color', label: 'Sfondo', type: 'color',
      condition: { field: 'is_popular', value: true } },
    { key: 'badge_text_color', label: 'Colore testo', type: 'color',
      condition: { field: 'is_popular', value: true } },

    // ═══════════════════════════════════════
    //  FORMA PREZZO
    // ═══════════════════════════════════════
    { type: 'separator', label: 'Forma prezzo' },
    { key: 'price_shape', label: 'Forma', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'circle', label: 'Cerchio' },
      { value: 'rounded', label: 'Quadrato arrotondato' },
    ]},
    { key: 'price_shape_color', label: 'Colore forma', type: 'color',
      condition: { field: 'price_shape', operator: '!=', value: 'none' } },
    { key: 'price_shape_border_width', label: 'Bordo (px)', type: 'range', min: 0, max: 5, step: 1,
      condition: { field: 'price_shape', operator: '!=', value: 'none' } },
    { key: 'price_shape_border_color', label: 'Colore bordo', type: 'color',
      condition: { field: 'price_shape_border_width', operator: '>', value: '0' } },
    { key: 'price_shape_glow', label: 'Luce interna', type: 'toggle',
      condition: { field: 'price_shape', operator: '!=', value: 'none' } },
    { key: 'price_shape_glow_color', label: 'Colore luce', type: 'color',
      condition: { field: 'price_shape_glow', value: true } },
    { key: 'price_shape_glow_intensity', label: 'Intensità luce (px)', type: 'range', min: 5, max: 40, step: 5,
      condition: { field: 'price_shape_glow', value: true } },

    // ═══════════════════════════════════════
    //  COUNTDOWN OFFERTA
    // ═══════════════════════════════════════
    { type: 'separator', label: 'Countdown offerta' },
    { key: 'countdown_enabled', label: 'Mostra countdown', type: 'toggle' },
    { key: 'countdown_date', label: 'Data scadenza', type: 'datetime',
      condition: { field: 'countdown_enabled', value: true } },
    { key: 'countdown_label', label: 'Etichetta', type: 'text',
      condition: { field: 'countdown_enabled', value: true } },
    { key: 'countdown_expired_text', label: 'Testo scaduto', type: 'text',
      condition: { field: 'countdown_enabled', value: true } },
    { key: 'countdown_hide_on_expire', label: 'Nascondi card se scaduto', type: 'toggle',
      condition: { field: 'countdown_enabled', value: true } },
    { key: 'countdown_bg_color', label: 'Sfondo', type: 'color',
      condition: { field: 'countdown_enabled', value: true } },
    { key: 'countdown_text_color', label: 'Colore testo', type: 'color',
      condition: { field: 'countdown_enabled', value: true } },

    // ═══════════════════════════════════════
    //  COLORI
    // ═══════════════════════════════════════
    { type: 'separator', label: 'Colori' },
    { key: 'price_color', label: 'Colore prezzo', type: 'color' },
    { key: 'bg_color', label: 'Sfondo card', type: 'color' },
    { key: 'accent_color', label: 'Colore accento', type: 'color' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },

    // ═══════════════════════════════════════
    //  SFONDO AVANZATO
    // ═══════════════════════════════════════
    { type: 'separator', label: 'Sfondo avanzato' },
    { key: 'bg_type', label: 'Tipo sfondo', type: 'select', options: [
      { value: 'color', label: 'Colore' },
      { value: 'image', label: 'Immagine' },
      { value: 'video', label: 'Video' },
    ]},
    { key: 'bg_image', label: 'Immagine', type: 'image',
      condition: { field: 'bg_type', value: 'image' } },
    { key: 'bg_video', label: 'Video (mp4)', type: 'media',
      condition: { field: 'bg_type', value: 'video' } },
    { key: 'overlay', label: 'Overlay', type: 'toggle',
      condition: { field: 'bg_type', operator: '!=', value: 'color' } },
    { key: 'overlay_color', label: 'Colore overlay', type: 'color',
      condition: { field: 'overlay', value: true } },
    { key: 'overlay_opacity', label: 'Opacità overlay (%)', type: 'range', min: 10, max: 100, step: 5,
      condition: { field: 'overlay', value: true } },

    // ═══════════════════════════════════════
    //  ASPETTO CARD
    // ═══════════════════════════════════════
    { type: 'separator', label: 'Aspetto card' },
    { key: 'border_radius', label: 'Arrotondamento (px)', type: 'border-radius' },
    { key: 'border_radius_hover', label: 'Raggio bordo (hover)', type: 'border-radius' },
    { key: 'border_width', label: 'Bordo (px)', type: 'range', min: 0, max: 5, step: 1 },
    { key: 'border_color', label: 'Colore bordo', type: 'color',
      condition: { field: 'border_width', operator: '>', value: '0' } },
    ...shadowField,
  ],
};
