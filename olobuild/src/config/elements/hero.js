export default {
  type: 'hero',
  name: 'Hero',
  icon: 'dashicons-cover-image',
  category: 'layout',
  defaults: {
    // Contenuto
    title: 'Benvenuto nel nostro sito',
    subtitle: 'Scopri qualcosa di straordinario',
    text_color: '#FFFFFF',

    // Layout
    min_height: '500px',
    content_max_width: '700',
    vertical_align: 'center',
    horizontal_align: 'center',
    text_align: 'center',
    padding_y: '80',
    padding_x: '30',

    // Sfondo
    bg_type: 'color',
    bg_color: '#6366F1',
    bg_gradient_from: '#6366F1',
    bg_gradient_to: '#8B5CF6',
    bg_gradient_angle: '135',
    bg_image: '',
    bg_video: '',
    bg_position: 'center',
    bg_size: 'cover',
    bg_fixed: false,

    // Overlay
    overlay: false,
    overlay_color: '#000000',
    overlay_opacity: '50',
    overlay_gradient: false,
    overlay_gradient_to: 'transparent',
    overlay_gradient_angle: '180',

    // CTA Primario
    cta_text: 'Inizia ora',
    cta_url: '#',
    cta_target: '_self',
    cta_bg_color: '#FFFFFF',
    cta_text_color: '',
    cta_radius: '6',
    cta_size: '15',
    cta_style: 'filled',

    // CTA Secondario
    cta2_text: '',
    cta2_url: '#',
    cta2_target: '_self',
    cta2_bg_color: 'transparent',
    cta2_text_color: '#FFFFFF',
    cta2_style: 'outline',

    // Avanzato
    full_bleed: false,
    border_radius: '0',
  },
  fields: [
    // ── Contenuto ──
    { key: 'title', label: 'Titolo', type: 'editor', mode: 'inline' },
    { key: 'subtitle', label: 'Sottotitolo', type: 'editor', mode: 'inline' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },

    // ── Layout ──
    { type: 'separator', label: 'Layout' },
    { key: 'min_height', label: 'Altezza minima (es. 500px, 80vh)', type: 'text' },
    { key: 'content_max_width', label: 'Larghezza max contenuto (px)', type: 'range', min: 200, max: 1200, step: 50 },
    { key: 'vertical_align', label: 'Allineamento verticale', type: 'select', options: [
      { value: 'top', label: 'Alto' },
      { value: 'center', label: 'Centro' },
      { value: 'bottom', label: 'Basso' },
    ]},
    { key: 'horizontal_align', label: 'Allineamento orizzontale', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'center', label: 'Centro' },
      { value: 'right', label: 'Destra' },
    ]},
    { key: 'text_align', label: 'Allineamento testo', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'center', label: 'Centro' },
      { value: 'right', label: 'Destra' },
    ]},
    { key: 'padding_y', label: 'Padding verticale (px)', type: 'range', min: 0, max: 200, step: 10 },
    { key: 'padding_x', label: 'Padding orizzontale (px)', type: 'range', min: 0, max: 100, step: 10 },

    // ── Sfondo ──
    { type: 'separator', label: 'Sfondo' },
    { key: 'bg_type', label: 'Tipo sfondo', type: 'select', options: [
      { value: 'color', label: 'Colore' },
      { value: 'gradient', label: 'Gradiente' },
      { value: 'image', label: 'Immagine' },
      { value: 'video', label: 'Video' },
    ]},
    { key: 'bg_color', label: 'Colore sfondo', type: 'color',
      condition: { field: 'bg_type', value: 'color' } },
    { key: 'bg_gradient_from', label: 'Gradiente da', type: 'color',
      condition: { field: 'bg_type', value: 'gradient' } },
    { key: 'bg_gradient_to', label: 'Gradiente a', type: 'color',
      condition: { field: 'bg_type', value: 'gradient' } },
    { key: 'bg_gradient_angle', label: 'Angolo gradiente (°)', type: 'range', min: 0, max: 360, step: 15,
      condition: { field: 'bg_type', value: 'gradient' } },
    { key: 'bg_image', label: 'Immagine sfondo', type: 'image',
      condition: { field: 'bg_type', value: 'image' } },
    { key: 'bg_video', label: 'Video sfondo (mp4)', type: 'media',
      condition: { field: 'bg_type', value: 'video' } },
    { key: 'bg_position', label: 'Posizione sfondo', type: 'select', options: [
      { value: 'center', label: 'Centro' },
      { value: 'top', label: 'Alto' },
      { value: 'bottom', label: 'Basso' },
      { value: 'left', label: 'Sinistra' },
      { value: 'right', label: 'Destra' },
    ], condition: { field: 'bg_type', value: 'image' } },
    { key: 'bg_size', label: 'Dimensione sfondo', type: 'select', options: [
      { value: 'cover', label: 'Cover' },
      { value: 'contain', label: 'Contain' },
      { value: 'auto', label: 'Auto' },
    ], condition: { field: 'bg_type', value: ['image', 'video'] } },
    { key: 'bg_fixed', label: 'Sfondo fisso (parallax)', type: 'toggle',
      condition: { field: 'bg_type', value: 'image' } },

    // ── Overlay ──
    { type: 'separator', label: 'Overlay' },
    { key: 'overlay', label: 'Attiva overlay', type: 'toggle' },
    { key: 'overlay_color', label: 'Colore overlay', type: 'color',
      condition: { field: 'overlay', value: true } },
    { key: 'overlay_opacity', label: 'Opacità overlay (%)', type: 'range', min: 10, max: 100, step: 5,
      condition: { field: 'overlay', value: true } },
    { key: 'overlay_gradient', label: 'Overlay sfumato', type: 'toggle',
      condition: { field: 'overlay', value: true } },
    { key: 'overlay_gradient_to', label: 'Sfumatura verso', type: 'color',
      condition: { field: 'overlay_gradient', value: true } },
    { key: 'overlay_gradient_angle', label: 'Angolo sfumatura (°)', type: 'range', min: 0, max: 360, step: 15,
      condition: { field: 'overlay_gradient', value: true } },

    // ── CTA Primario ──
    { type: 'separator', label: 'CTA Primario' },
    { key: 'cta_text', label: 'Testo pulsante', type: 'text' },
    { key: 'cta_url', label: 'URL pulsante', type: 'text' },
    { key: 'cta_target', label: 'Apri in', type: 'select', options: [
      { value: '_self', label: 'Stessa finestra' },
      { value: '_blank', label: 'Nuova scheda' },
    ]},
    { key: 'cta_style', label: 'Stile pulsante', type: 'select', options: [
      { value: 'filled', label: 'Pieno' },
      { value: 'outline', label: 'Contorno' },
      { value: 'ghost', label: 'Trasparente' },
    ]},
    { key: 'cta_size', label: 'Dimensione (px)', type: 'range', min: 12, max: 24, step: 1 },
    { key: 'cta_bg_color', label: 'Colore sfondo CTA', type: 'color' },
    { key: 'cta_text_color', label: 'Colore testo CTA', type: 'color' },
    { key: 'cta_radius', label: 'Raggio bordo CTA (px)', type: 'range', min: 0, max: 50, step: 1 },

    // ── CTA Secondario ──
    { type: 'separator', label: 'CTA Secondario' },
    { key: 'cta2_text', label: 'Testo (vuoto = nascosto)', type: 'text' },
    { key: 'cta2_url', label: 'URL', type: 'text' },
    { key: 'cta2_target', label: 'Apri in', type: 'select', options: [
      { value: '_self', label: 'Stessa finestra' },
      { value: '_blank', label: 'Nuova scheda' },
    ]},
    { key: 'cta2_style', label: 'Stile', type: 'select', options: [
      { value: 'filled', label: 'Pieno' },
      { value: 'outline', label: 'Contorno' },
      { value: 'ghost', label: 'Trasparente' },
    ]},
    { key: 'cta2_bg_color', label: 'Colore sfondo', type: 'color' },
    { key: 'cta2_text_color', label: 'Colore testo', type: 'color' },

    // ── Avanzato ──
    { type: 'separator', label: 'Avanzato' },
    { key: 'full_bleed', label: 'Full width (100vw)', type: 'toggle' },
    { key: 'border_radius', label: 'Raggio bordo (px)', type: 'range', min: 0, max: 50, step: 1 },
  ],
};
