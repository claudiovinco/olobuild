export default {
  type: 'imgcompare',
  name: 'Confronto Immagini',
  icon: 'dashicons-image-flip-horizontal',
  category: 'media',
  defaults: {
    // Immagini
    before_image: '',
    after_image: '',
    before_label: 'Prima',
    after_label: 'Dopo',
    show_labels: true,

    // Slider
    start_position: '50',
    orientation: 'horizontal',
    handle_color: '',
    handle_size: '40',
    handle_border: '3',
    line_width: '3',

    // Card
    height: '400',
    border_radius: '8',
    object_fit: 'cover',
    card_border_width: '0',
    card_border_color: '',
    card_shadow: 'none',

    // Autoplay
    autoplay: false,
    autoplay_delay: '3',
    autoplay_speed: '2',
  },
  fields: [
    // ── Immagini ──
    { key: 'before_image', label: 'Immagine "Prima"', type: 'image' },
    { key: 'after_image', label: 'Immagine "Dopo"', type: 'image' },
    { key: 'before_label', label: 'Etichetta Prima', type: 'text' },
    { key: 'after_label', label: 'Etichetta Dopo', type: 'text' },
    { key: 'show_labels', label: 'Mostra etichette', type: 'toggle' },

    // ── Slider ──
    { type: 'separator', label: 'Slider' },
    { key: 'start_position', label: 'Posizione iniziale (%)', type: 'range', min: 0, max: 100 },
    { key: 'orientation', label: 'Orientamento', type: 'select', options: [
      { value: 'horizontal', label: 'Orizzontale' },
      { value: 'vertical', label: 'Verticale' },
    ]},
    { key: 'handle_color', label: 'Colore maniglia', type: 'color' },
    { key: 'handle_size', label: 'Dimensione maniglia (px)', type: 'range', min: 24, max: 60 },
    { key: 'handle_border', label: 'Spessore bordo (px)', type: 'range', min: 1, max: 6 },
    { key: 'line_width', label: 'Spessore linea (px)', type: 'range', min: 1, max: 6 },

    // ── Aspetto ──
    { type: 'separator', label: 'Aspetto' },
    { key: 'height', label: 'Altezza (px)', type: 'range', min: 200, max: 800, step: 10 },
    { key: 'border_radius', label: 'Raggio bordo (px)', type: 'border-radius' },
    { key: 'border_radius_hover', label: 'Raggio bordo (hover)', type: 'border-radius' },
    { key: 'object_fit', label: 'Adattamento immagini', type: 'select', options: [
      { value: 'cover', label: 'Riempi (cover)' },
      { value: 'contain', label: 'Contieni' },
    ]},
    { key: 'card_border_width', label: 'Bordo (px)', type: 'range', min: 0, max: 6 },
    { key: 'card_border_color', label: 'Colore bordo', type: 'color' },
    { key: 'card_shadow', label: 'Ombra', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'sm', label: 'Leggera' },
      { value: 'md', label: 'Media' },
      { value: 'lg', label: 'Grande' },
      { value: 'xl', label: 'Extra grande' },
      { value: 'custom', label: 'Personalizzata' },
    ]},
    { key: 'card_shadow_h', label: 'Offset H (px)', type: 'range', min: -50, max: 50, step: 1,
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_v', label: 'Offset V (px)', type: 'range', min: -50, max: 50, step: 1,
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_blur', label: 'Sfocatura (px)', type: 'range', min: 0, max: 100, step: 1,
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_spread', label: 'Espansione (px)', type: 'range', min: -50, max: 50, step: 1,
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_color', label: 'Colore ombra', type: 'color',
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_inset', label: 'Ombra interna', type: 'toggle',
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },

    // ── Autoplay ──
    { type: 'separator', label: 'Autoplay' },
    { key: 'autoplay', label: 'Passaggio automatico', type: 'toggle' },
    { key: 'autoplay_delay', label: 'Attesa inattività (sec)', type: 'range', min: 1, max: 10,
      condition: { field: 'autoplay', value: true } },
    { key: 'autoplay_speed', label: 'Durata ciclo (sec)', type: 'range', min: 1, max: 8,
      condition: { field: 'autoplay', value: true } },
  ],
};
