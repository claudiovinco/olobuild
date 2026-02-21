export default {
  type: 'team',
  name: 'Membro del team',
  icon: 'dashicons-businessperson',
  category: 'content',
  defaults: {
    // Contenuto
    photo: '',
    hover_image: '',
    hover_video: '',
    name: 'Jane Smith',
    role: 'Lead Designer',
    bio: 'Appassionata di creare esperienze utente eccellenti.',
    link_text: 'Profilo',
    link_url: '',

    // Foto
    photo_size: '120',
    photo_shape: 'circle',
    photo_radius: '12',
    photo_border_width: '3',
    photo_border_color: '#FFFFFF',
    photo_shadow: 'md',
    photo_gap: '12',

    // Contenitore info
    info_bg_color: '#6366F1',
    info_text_color: '#FFFFFF',
    info_padding: '24',
    info_width: '100',
    info_margin: '0',
    info_radius: '16',
    info_border_width: '0',
    info_border_color: '#4B5563',
    info_align: 'center',

    // Tipografia
    name_size: '20',
    name_weight: '600',
    role_size: '14',
    role_color: '',
    bio_size: '14',

    // Tile
    bg_color: '',
    tile_padding: '16',
    border_radius: '16',
    border_width: '0',
    border_color: '#374151',
  },
  fields: [
    // ── Contenuto ──
    { key: 'photo', label: 'Foto / Video', type: 'media' },
    { key: 'hover_image', label: 'Immagine hover', type: 'image' },
    { key: 'hover_video', label: 'Video hover', type: 'media' },
    { key: 'name', label: 'Nome', type: 'editor', mode: 'inline' },
    { key: 'role', label: 'Ruolo', type: 'editor', mode: 'inline' },
    { key: 'bio', label: 'Biografia', type: 'editor', mode: 'block' },
    { key: 'link_text', label: 'Testo link', type: 'text' },
    { key: 'link_url', label: 'URL link', type: 'text' },

    // ── Foto ──
    { type: 'separator', label: 'Foto' },
    { key: 'photo_size', label: 'Dimensione foto (px)', type: 'range', min: 60, max: 250, step: 5 },
    { key: 'photo_shape', label: 'Maschera foto', type: 'select', options: [
      { value: 'circle', label: 'Tonda' },
      { value: 'square', label: 'Quadrata' },
      { value: 'rounded', label: 'Arrotondata' },
      { value: 'hexagon', label: 'Esagonale' },
    ]},
    { key: 'photo_radius', label: 'Raggio bordo foto (px)', type: 'range', min: 0, max: 40, step: 2,
      condition: { field: 'photo_shape', value: 'rounded' } },
    { key: 'photo_border_width', label: 'Bordo foto (px)', type: 'range', min: 0, max: 8, step: 1 },
    { key: 'photo_border_color', label: 'Colore bordo foto', type: 'color',
      condition: { field: 'photo_border_width', operator: '>', value: '0' } },
    { key: 'photo_shadow', label: 'Ombra foto', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'sm', label: 'Leggera' },
      { value: 'md', label: 'Media' },
      { value: 'lg', label: 'Grande' },
    ]},
    { key: 'photo_gap', label: 'Distanza foto-contenitore (px)', type: 'range', min: -40, max: 40, step: 4 },

    // ── Contenitore info ──
    { type: 'separator', label: 'Contenitore info' },
    { key: 'info_bg_color', label: 'Sfondo contenitore', type: 'color' },
    { key: 'info_text_color', label: 'Colore testo', type: 'color' },
    { key: 'info_width', label: 'Larghezza contenitore (%)', type: 'range', min: 50, max: 100, step: 5 },
    { key: 'info_margin', label: 'Margine dal tile (px)', type: 'range', min: 0, max: 40, step: 4 },
    { key: 'info_padding', label: 'Padding interno (px)', type: 'range', min: 8, max: 48, step: 4 },
    { key: 'info_radius', label: 'Arrotondamento (px)', type: 'range', min: 0, max: 30, step: 1 },
    { key: 'info_border_width', label: 'Bordo contenitore (px)', type: 'range', min: 0, max: 5, step: 1 },
    { key: 'info_border_color', label: 'Colore bordo contenitore', type: 'color',
      condition: { field: 'info_border_width', operator: '>', value: '0' } },
    { key: 'info_align', label: 'Allineamento testo', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'center', label: 'Centro' },
      { value: 'right', label: 'Destra' },
    ]},

    // ── Tipografia ──
    { type: 'separator', label: 'Tipografia' },
    { key: 'name_size', label: 'Dim. nome (px)', type: 'range', min: 14, max: 36, step: 1 },
    { key: 'name_weight', label: 'Peso nome', type: 'select', options: [
      { value: '400', label: 'Normale' },
      { value: '500', label: 'Medio' },
      { value: '600', label: 'Semi-grassetto' },
      { value: '700', label: 'Grassetto' },
    ]},
    { key: 'role_size', label: 'Dim. ruolo (px)', type: 'range', min: 10, max: 24, step: 1 },
    { key: 'role_color', label: 'Colore ruolo', type: 'color' },
    { key: 'bio_size', label: 'Dim. biografia (px)', type: 'range', min: 10, max: 20, step: 1 },

    // ── Tile ──
    { type: 'separator', label: 'Aspetto tile' },
    { key: 'bg_color', label: 'Colore sfondo tile', type: 'color' },
    { key: 'tile_padding', label: 'Padding tile (px)', type: 'range', min: 0, max: 40, step: 4 },
    { key: 'border_radius', label: 'Arrotondamento tile (px)', type: 'range', min: 0, max: 30, step: 1 },
    { key: 'border_width', label: 'Bordo tile (px)', type: 'range', min: 0, max: 5, step: 1 },
    { key: 'border_color', label: 'Colore bordo tile', type: 'color',
      condition: { field: 'border_width', operator: '>', value: '0' } },
  ],
};
