import { shadowField } from './_shared.js';

export default {
  type: 'pricelist',
  name: 'Lista prezzi',
  icon: 'dashicons-list-view',
  category: 'content',
  defaults: {
    items: [
      { id: 'pl-1', title: 'Bruschetta', description: 'Pomodoro fresco, basilico e olio EVO', price: '€8', image_url: '', highlighted: false, badge: '' },
      { id: 'pl-2', title: 'Risotto ai funghi porcini', description: 'Riso Carnaroli mantecato con porcini freschi', price: '€14', image_url: '', highlighted: false, badge: '' },
      { id: 'pl-3', title: 'Tiramisù', description: 'Mascarpone, savoiardi e caffè espresso', price: '€7', image_url: '', highlighted: false, badge: 'Consigliato' },
    ],
    separator_style: 'dotted',
    separator_color: '',
    title_color: '',
    price_color: '',
    description_color: '',
    image_size: '60',
    image_border_radius: '8',
    show_image: true,
    price_position: 'right',
    highlighted_bg: '',
    badge_bg: '',
    badge_color: '',
    badge_border_color: '',
    badge_border_width: '0',
    badge_border_style: 'solid',
    badge_border_radius: '6',
    gap: '12',
    padding: '14',
    card_bg: '',
    card_border_radius: '12',
    card_border_color: '',
    hover_lift: true,
    shadow: 'none',
  },
  fields: [
    // ── Items ──
    { key: 'items', label: 'Elementi', type: 'content-items',
      itemFields: [
        { key: 'title', label: 'Nome', type: 'text' },
        { key: 'description', label: 'Descrizione', type: 'text' },
        { key: 'price', label: 'Prezzo', type: 'text' },
        { key: 'image_url', label: 'Immagine', type: 'image' },
        { key: 'highlighted', label: 'In evidenza', type: 'toggle' },
        { key: 'badge', label: 'Badge', type: 'text' },
      ],
      newItemDefaults: { title: 'Nuovo piatto', description: 'Descrizione del piatto', price: '€0', image_url: '', highlighted: false, badge: '' },
      itemLabel: 'Piatto',
    },

    // ── Card ──
    { type: 'separator', label: 'Card' },
    { key: 'card_bg', label: 'Sfondo card', type: 'color' },
    { key: 'card_border_color', label: 'Bordo card', type: 'color' },
    { key: 'card_border_radius', label: 'Arrotondamento card (px)', type: 'range', min: 0, max: 24, step: 2 },
    { key: 'hover_lift', label: 'Effetto hover', type: 'toggle' },

    // ── Separatore ──
    { type: 'separator', label: 'Separatore' },
    { key: 'separator_style', label: 'Stile separatore', type: 'select', options: [
      { value: 'dotted', label: 'Puntinato' },
      { value: 'dashed', label: 'Tratteggiato' },
      { value: 'solid', label: 'Continuo' },
      { value: 'none', label: 'Nessuno' },
    ]},
    { key: 'separator_color', label: 'Colore separatore', type: 'color',
      condition: { field: 'separator_style', operator: '!=', value: 'none' } },

    // ── Immagine ──
    { type: 'separator', label: 'Immagine' },
    { key: 'show_image', label: 'Mostra immagine', type: 'toggle' },
    { key: 'image_size', label: 'Dimensione immagine (px)', type: 'range', min: 30, max: 120, step: 5,
      condition: { field: 'show_image', value: true } },
    { key: 'image_border_radius', label: 'Arrotondamento immagine (px)', type: 'border-radius',
      condition: { field: 'show_image', value: true } },

    // ── Layout ──
    { type: 'separator', label: 'Layout' },
    { key: 'price_position', label: 'Posizione prezzo', type: 'select', options: [
      { value: 'right', label: 'A destra' },
      { value: 'below', label: 'Sotto il titolo' },
    ]},
    { key: 'gap', label: 'Gap tra elementi (px)', type: 'range', min: 0, max: 32, step: 2 },
    { key: 'padding', label: 'Padding elemento (px)', type: 'range', min: 4, max: 32, step: 2 },

    // ── Colori ──
    { type: 'separator', label: 'Colori' },
    { key: 'title_color', label: 'Colore titolo', type: 'color' },
    { key: 'price_color', label: 'Colore prezzo', type: 'color' },
    { key: 'description_color', label: 'Colore descrizione', type: 'color' },
    { key: 'highlighted_bg', label: 'Sfondo evidenziato', type: 'color' },
    // ── Badge ──
    { type: 'separator', label: 'Badge' },
    { key: 'badge_bg', label: 'Sfondo', type: 'color' },
    { key: 'badge_color', label: 'Colore testo', type: 'color' },
    { key: 'badge_border_color', label: 'Colore bordo', type: 'color' },
    { key: 'badge_border_width', label: 'Spessore bordo (px)', type: 'range', min: 0, max: 5, step: 1 },
    { key: 'badge_border_style', label: 'Stile bordo', type: 'select', options: [
      { value: 'solid', label: 'Continuo' },
      { value: 'dashed', label: 'Tratteggiato' },
      { value: 'dotted', label: 'Puntinato' },
    ], condition: { field: 'badge_border_width', operator: '!=', value: '0' } },
    { key: 'badge_border_radius', label: 'Arrotondamento (px)', type: 'range', min: 0, max: 20, step: 1 },

    ...shadowField,
  ],
};
