export default {
  type: 'counter',
  name: 'Contatore',
  icon: 'dashicons-performance',
  category: 'content',
  defaults: {
    number: '1250',
    label: 'Clienti soddisfatti',
    prefix: '',
    suffix: '+',
    icon_emoji: '🏆',
    icon_size: '40',

    // Tipografia
    text_color: '#F3F4F6',
    number_font_size: '48',
    number_font_weight: '700',
    label_color: '',
    label_font_size: '14',
    label_font_weight: '400',

    // Sfondo
    bg_type: 'color',
    bg_color: '',
    bg_image: '',
    bg_video: '',
    overlay: false,
    overlay_color: '#000000',
    overlay_opacity: '50',

    // Tile
    padding: '32',
    border_radius: '0',
    border_width: '0',
    border_color: '#374151',
  },
  fields: [
    // ── Contenuto ──
    { key: 'number', label: 'Numero', type: 'text' },
    { key: 'label', label: 'Etichetta', type: 'editor', mode: 'inline' },
    { key: 'prefix', label: 'Prefisso', type: 'text' },
    { key: 'suffix', label: 'Suffisso', type: 'text' },
    { key: 'icon_emoji', label: 'Icona / Emoji', type: 'icon' },
    { key: 'icon_size', label: 'Dimensione icona (px)', type: 'range', min: 16, max: 80, step: 2 },

    // ── Tipografia ──
    { type: 'separator', label: 'Tipografia' },
    { key: 'number_font_size', label: 'Dim. numero (px)', type: 'range', min: 20, max: 120, step: 2 },
    { key: 'number_font_weight', label: 'Peso numero', type: 'select', options: [
      { value: '400', label: 'Normale' },
      { value: '500', label: 'Medio' },
      { value: '600', label: 'Semi-grassetto' },
      { value: '700', label: 'Grassetto' },
      { value: '800', label: 'Extra-grassetto' },
      { value: '900', label: 'Nero' },
    ]},
    { key: 'label_font_size', label: 'Dim. etichetta (px)', type: 'range', min: 10, max: 32, step: 1 },
    { key: 'label_font_weight', label: 'Peso etichetta', type: 'select', options: [
      { value: '400', label: 'Normale' },
      { value: '500', label: 'Medio' },
      { value: '600', label: 'Semi-grassetto' },
      { value: '700', label: 'Grassetto' },
    ]},

    // ── Colori ──
    { type: 'separator', label: 'Colori' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },
    { key: 'label_color', label: 'Colore etichetta', type: 'color' },

    // ── Sfondo ──
    { type: 'separator', label: 'Sfondo' },
    { key: 'bg_type', label: 'Tipo sfondo', type: 'select', options: [
      { value: 'color', label: 'Colore' },
      { value: 'image', label: 'Immagine' },
      { value: 'video', label: 'Video' },
    ]},
    { key: 'bg_color', label: 'Colore sfondo', type: 'color',
      condition: { field: 'bg_type', value: 'color' } },
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

    // ── Tile ──
    { type: 'separator', label: 'Aspetto tile' },
    { key: 'padding', label: 'Padding (px)', type: 'range', min: 0, max: 80, step: 4 },
    { key: 'border_radius', label: 'Arrotondamento (px)', type: 'range', min: 0, max: 30, step: 1 },
    { key: 'border_width', label: 'Bordo tile (px)', type: 'range', min: 0, max: 5, step: 1 },
    { key: 'border_color', label: 'Colore bordo tile', type: 'color',
      condition: { field: 'border_width', operator: '>', value: '0' } },
  ],
};
