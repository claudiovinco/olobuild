import { borderFields, borderDefaults, shadowField } from './_shared.js';

export default {
  type: 'progallery',
  name: 'Pro Gallery',
  icon: 'dashicons-images-alt2',
  category: 'media',

  defaults: {
    images: [],
    // Layout
    layout: 'grid',
    puzzle_style: 'classic',
    columns: '3',
    gap: '8',
    img_height: '250px',
    object_fit: 'cover',
    thumb_radius: '8',
    rows: '0',
    mobile_columns: '2',
    // Entrance
    entrance: 'none',
    entrance_stagger: '120',
    entrance_duration: '600',
    // Hover
    hover_effect: 'zoom',
    hover_zoom_scale: '1.08',
    hover_tilt_angle: '10',
    hover_magnetic_strength: '24',
    hover_caption: 'none',
    hover_caption_bg: 'rgba(0,0,0,0.6)',
    hover_caption_color: '#ffffff',
    // Continuous
    continuous: 'none',
    continuous_speed: '20',
    // Filter
    filter: 'none',
    filter_hover_restore: false,
    duotone_dark: '#1a1a2e',
    duotone_light: '#e94560',
    // Frame
    frame: 'none',
    frame_color: '#ffffff',
    // Lightbox
    lightbox: true,
    lightbox_animation: 'slide',
    show_caption: false,
    // "+N" overlay
    more_bg: 'rgba(0,0,0,0.55)',
    more_color: '#ffffff',
    more_size: '28',
    // Avanzato
    shadow: 'none',
    ...borderDefaults,
  },

  fields: [
    // ─── Immagini ───
    { key: 'images', label: 'Immagini', type: 'gallery' },

    // ─── Layout ───
    { type: 'separator', label: 'Layout' },
    { key: 'layout', label: 'Schema', type: 'select', options: [
      { value: 'grid', label: 'Griglia' },
      { value: 'masonry', label: 'Masonry' },
      { value: 'scattered', label: 'Sparso' },
      { value: 'collage', label: 'Collage' },
      { value: 'filmstrip', label: 'Pellicola' },
      { value: 'mosaic', label: 'Mosaico' },
      { value: 'honeycomb', label: 'Esagoni' },
      { value: 'hexgrid', label: 'Esagoni incastro' },
      { value: 'puzzle', label: 'Puzzle' },
      { value: 'diagonal', label: 'Diagonale' },
    ]},
    { key: 'puzzle_style', label: 'Stile puzzle', type: 'select', options: [
      { value: 'classic', label: 'Classico' },
      { value: 'zigzag', label: 'Zigzag' },
      { value: 'wave', label: 'Onda' },
      { value: 'castle', label: 'Castello' },
      { value: 'fir', label: 'Abeti' },
    ], show: s => s.layout === 'puzzle' },
    { key: 'columns', label: 'Colonne', type: 'range', min: 2, max: 6, step: 1 },
    { key: 'gap', label: 'Gap (px)', type: 'range', min: 0, max: 24, step: 2 },
    { key: 'img_height', label: 'Altezza immagine', type: 'text' },
    { key: 'object_fit', label: 'Adattamento', type: 'select', options: [
      { value: 'cover', label: 'Riempi' },
      { value: 'contain', label: 'Contieni' },
    ]},
    { key: 'thumb_radius', label: 'Raggio bordi (px)', type: 'range', min: 0, max: 32, step: 2 },
    { key: 'rows', label: 'Righe visibili (0 = tutte)', type: 'range', min: 0, max: 5, step: 1 },
    { key: 'mobile_columns', label: 'Colonne mobile', type: 'range', min: 1, max: 4, step: 1 },

    // ─── Entrance ───
    { type: 'separator', label: 'Animazione ingresso (solo frontend)' },
    { key: 'entrance', label: 'Effetto', type: 'select', options: [
      { value: 'none', label: 'Nessuno' },
      { value: 'fade-up', label: 'Fade su' },
      { value: 'fade-scale', label: 'Fade + Scala' },
      { value: 'flip', label: 'Flip' },
      { value: 'slide-in', label: 'Scorrimento' },
      { value: 'blur-in', label: 'Sfocatura' },
    ]},
    { key: 'entrance_stagger', label: 'Stagger (ms)', type: 'range', min: 80, max: 400, step: 20,
      show: s => s.entrance && s.entrance !== 'none' },
    { key: 'entrance_duration', label: 'Durata (ms)', type: 'range', min: 300, max: 1200, step: 50,
      show: s => s.entrance && s.entrance !== 'none' },

    // ─── Hover ───
    { type: 'separator', label: 'Effetti hover' },
    { key: 'hover_effect', label: 'Effetto', type: 'select', options: [
      { value: 'none', label: 'Nessuno' },
      { value: 'zoom', label: 'Zoom' },
      { value: 'lift', label: 'Sollevamento' },
      { value: 'tilt3d', label: 'Tilt 3D' },
      { value: 'glow', label: 'Bagliore' },
      { value: 'blur-peers', label: 'Sfoca gli altri' },
      { value: 'magnetic', label: 'Magnetico' },
    ]},
    { key: 'hover_zoom_scale', label: 'Intensità zoom', type: 'range', min: 1.05, max: 1.30, step: 0.01,
      show: s => s.hover_effect === 'zoom' },
    { key: 'hover_tilt_angle', label: 'Angolo tilt (deg)', type: 'range', min: 5, max: 20, step: 1,
      show: s => s.hover_effect === 'tilt3d' },
    { key: 'hover_magnetic_strength', label: 'Intensità magnetismo', type: 'range', min: 8, max: 60, step: 2,
      show: s => s.hover_effect === 'magnetic' },
    { key: 'hover_caption', label: 'Didascalia hover', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'slide-up', label: 'Scorrimento dal basso' },
      { value: 'fade', label: 'Dissolvenza' },
      { value: 'overlay', label: 'Overlay pieno' },
    ]},
    { key: 'hover_caption_bg', label: 'Sfondo didascalia', type: 'color',
      show: s => s.hover_caption && s.hover_caption !== 'none' },
    { key: 'hover_caption_color', label: 'Colore testo didascalia', type: 'color',
      show: s => s.hover_caption && s.hover_caption !== 'none' },

    // ─── Animazione continua ───
    { type: 'separator', label: 'Animazione continua' },
    { key: 'continuous', label: 'Effetto', type: 'select', options: [
      { value: 'none', label: 'Nessuno' },
      { value: 'float', label: 'Galleggiamento' },
      { value: 'drift', label: 'Deriva' },
      { value: 'breathe', label: 'Respiro' },
      { value: 'rotate-slow', label: 'Rotazione lenta' },
      { value: 'kenburns', label: 'Ken Burns' },
    ]},
    { key: 'continuous_speed', label: 'Durata ciclo (s)', type: 'range', min: 10, max: 40, step: 1,
      show: s => s.continuous && s.continuous !== 'none' },

    // ─── Filtro ───
    { type: 'separator', label: 'Filtro immagine' },
    { key: 'filter', label: 'Filtro', type: 'select', options: [
      { value: 'none', label: 'Nessuno' },
      { value: 'grayscale', label: 'Bianco e nero' },
      { value: 'sepia', label: 'Seppia' },
      { value: 'high-contrast', label: 'Alto contrasto' },
      { value: 'warm', label: 'Caldo' },
      { value: 'cool', label: 'Freddo' },
      { value: 'vintage', label: 'Vintage' },
      { value: 'duotone', label: 'Duotono' },
    ]},
    { key: 'filter_hover_restore', label: 'Rimuovi filtro al hover', type: 'toggle',
      show: s => s.filter && s.filter !== 'none' },
    { key: 'duotone_dark', label: 'Colore scuro', type: 'color',
      show: s => s.filter === 'duotone' },
    { key: 'duotone_light', label: 'Colore chiaro', type: 'color',
      show: s => s.filter === 'duotone' },

    // ─── Cornice ───
    { type: 'separator', label: 'Cornice' },
    { key: 'frame', label: 'Stile cornice', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'polaroid', label: 'Polaroid' },
      { value: 'rounded', label: 'Arrotondata' },
      { value: 'shadow-box', label: 'Riquadro ombra' },
      { value: 'torn', label: 'Strappata' },
      { value: 'tape', label: 'Nastro adesivo' },
    ]},
    { key: 'frame_color', label: 'Colore cornice', type: 'color',
      show: s => s.frame === 'polaroid' || s.frame === 'shadow-box' },

    // ─── Lightbox ───
    { type: 'separator', label: 'Lightbox' },
    { key: 'lightbox', label: 'Attiva lightbox', type: 'toggle' },
    { key: 'lightbox_animation', label: 'Animazione', type: 'select', options: [
      { value: 'slide', label: 'Scorrimento' },
      { value: 'fade', label: 'Dissolvenza' },
      { value: 'scale', label: 'Scala' },
    ], show: s => !!s.lightbox },
    { key: 'show_caption', label: 'Mostra didascalie', type: 'toggle',
      show: s => !!s.lightbox },

    // ─── +N overlay ───
    { type: 'separator', label: 'Indicatore "+N"' },
    { key: 'more_bg', label: 'Sfondo overlay', type: 'color' },
    { key: 'more_color', label: 'Colore testo', type: 'color' },
    { key: 'more_size', label: 'Dimensione testo (px)', type: 'range', min: 16, max: 48, step: 2 },

    // ─── Avanzato ───
    shadowField,
    ...borderFields,
  ],
};
