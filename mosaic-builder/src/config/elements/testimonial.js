export default {
  type: 'testimonial',
  name: 'Testimonianza',
  icon: 'dashicons-format-quote',
  category: 'content',
  defaults: {
    quote: 'Un prodotto fantastico!',
    author_name: 'Mario Rossi',
    author_role: 'CEO',
    avatar: '',
    rating: '5',
    bg_color: '#1F2937',
    text_color: '#F3F4F6',

    // Linea decorativa
    show_line: true,
    line_color: '#6366F1',

    // Posizione testimone
    author_position: 'bottom-left',

    // Avatar
    avatar_size: '48',
    avatar_shape: 'circle',
    avatar_radius: '6',
    avatar_shadow: 'none',
    avatar_border_width: '0',
    avatar_border_color: '#FFFFFF',
    avatar_filter: 'none',

    // Tile
    border_radius: '12',
    border_width: '0',
    border_color: '#374151',
  },
  fields: [
    { key: 'quote', label: 'Citazione', type: 'editor', mode: 'block' },
    { key: 'author_name', label: 'Nome autore', type: 'editor', mode: 'inline' },
    { key: 'author_role', label: 'Ruolo autore', type: 'editor', mode: 'inline' },
    { key: 'avatar', label: 'Avatar', type: 'image' },
    { key: 'rating', label: 'Valutazione', type: 'select', options: [
      { value: '0', label: 'Nessuna' },
      { value: '1', label: '1 stella' },
      { value: '2', label: '2 stelle' },
      { value: '3', label: '3 stelle' },
      { value: '4', label: '4 stelle' },
      { value: '5', label: '5 stelle' },
    ]},

    { type: 'separator', label: 'Colori' },
    { key: 'bg_color', label: 'Colore sfondo', type: 'color' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },

    { type: 'separator', label: 'Linea decorativa' },
    { key: 'show_line', label: 'Mostra linea verticale', type: 'toggle' },
    { key: 'line_color', label: 'Colore linea', type: 'color',
      condition: { field: 'show_line', value: true } },

    { type: 'separator', label: 'Testimone' },
    { key: 'author_position', label: 'Posizione testimone', type: 'select', options: [
      { value: 'bottom-left', label: 'Sotto a sinistra' },
      { value: 'bottom-center', label: 'Sotto al centro' },
      { value: 'bottom-right', label: 'Sotto a destra' },
      { value: 'left', label: 'Sinistra' },
      { value: 'right', label: 'Destra' },
    ]},
    { key: 'avatar_size', label: 'Dimensione avatar (px)', type: 'range', min: 30, max: 120, step: 5 },
    { key: 'avatar_shape', label: 'Forma avatar', type: 'select', options: [
      { value: 'circle', label: 'Tonda' },
      { value: 'square', label: 'Quadrata' },
    ]},
    { key: 'avatar_radius', label: 'Arrotondamento avatar (px)', type: 'range', min: 0, max: 30, step: 1,
      condition: { field: 'avatar_shape', value: 'square' } },
    { key: 'avatar_shadow', label: 'Ombra avatar', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'sm', label: 'Piccola' },
      { value: 'md', label: 'Media' },
      { value: 'lg', label: 'Grande' },
    ]},
    { key: 'avatar_border_width', label: 'Bordo avatar (px)', type: 'range', min: 0, max: 5, step: 1 },
    { key: 'avatar_border_color', label: 'Colore bordo avatar', type: 'color',
      condition: { field: 'avatar_border_width', operator: '>', value: '0' } },
    { key: 'avatar_filter', label: 'Filtro avatar', type: 'select', options: [
      { value: 'none', label: 'Nessuno' },
      { value: 'grayscale', label: 'Scala di grigi' },
      { value: 'sepia', label: 'Seppia' },
      { value: 'brightness', label: 'Luminoso' },
      { value: 'contrast', label: 'Alto contrasto' },
    ]},

    { type: 'separator', label: 'Aspetto' },
    { key: 'border_radius', label: 'Arrotondamento tile (px)', type: 'range', min: 0, max: 30, step: 1 },
    { key: 'border_width', label: 'Spessore bordo (px)', type: 'range', min: 0, max: 5, step: 1 },
    { key: 'border_color', label: 'Colore bordo', type: 'color',
      condition: { field: 'border_width', operator: '>', value: '0' } },
  ],
};
