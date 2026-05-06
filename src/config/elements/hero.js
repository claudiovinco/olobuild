import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared';
import { shadowField } from './_shared.js';

export default {
  type: 'hero',
  name: 'Hero',
  icon: 'dashicons-cover-image',
  category: 'layout',
  defaults: {
    // Contenuto
    title: 'Benvenuto nel nostro sito',
    subtitle: 'Scopri qualcosa di straordinario',
    text_color: '',

    // Titolo tipografia
    title_tag: 'h1',
    title_font_family: '',
    title_font_size: '',
    title_font_weight: '700',
    title_letter_spacing: '0',
    title_line_height: '1.2',
    title_text_transform: 'none',
    title_color: '',
    title_text_shadow: '',

    // Sottotitolo tipografia
    subtitle_font_size: '',
    subtitle_font_weight: '400',
    subtitle_letter_spacing: '0',
    subtitle_color: '',
    subtitle_max_width: '',

    // Layout
    min_height: '500px',
    content_max_width: '700',
    vertical_align: 'center',
    horizontal_align: 'center',
    text_align: 'center',
    tile_padding: { top: 60, right: 20, bottom: 60, left: 20 },

    // Sfondo
    bg_type: 'color',
    bg_color: '',
    bg_gradient_from: '',
    bg_gradient_to: '',
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
    cta_bg_color: '',
    cta_text_color: '',
    cta_radius: '6',
    cta_size: '15',
    cta_style: 'filled',

    // CTA Secondario
    cta2_text: '',
    cta2_url: '#',
    cta2_target: '_self',
    cta2_bg_color: '',
    cta2_text_color: '',
    cta2_style: 'outline',

    // Avanzato
    full_bleed: false,
    shadow: 'none',
    ...textEffectsDefaults,
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },
  fields: [
    // ── Contenuto ──
    { key: 'title', label: 'Titolo', type: 'text' },
    { key: 'subtitle', label: 'Sottotitolo', type: 'text' },
    { key: 'text_color', label: 'Colore testo generale', type: 'color' },

    // ── Tipografia titolo ──
    { type: 'separator', label: 'Tipografia titolo' },
    { key: 'title_tag', label: 'Tag HTML', type: 'select', options: [
      { value: 'h1', label: 'H1' },
      { value: 'h2', label: 'H2' },
      { value: 'h3', label: 'H3' },
      { value: 'p', label: 'Paragrafo' },
      { value: 'span', label: 'Span' },
    ]},
    { key: 'title_font_family', label: 'Font family', type: 'font-family' },
    { key: 'title_font_size', label: 'Dimensione (px)', type: 'range', responsive: true, min: 14, max: 120, step: 1 },
    { key: 'title_font_weight', label: 'Peso', type: 'select', options: [
      { value: '300', label: 'Light' },
      { value: '400', label: 'Regular' },
      { value: '500', label: 'Medium' },
      { value: '600', label: 'Semibold' },
      { value: '700', label: 'Bold' },
      { value: '800', label: 'Extra Bold' },
      { value: '900', label: 'Black' },
    ]},
    { key: 'title_letter_spacing', label: 'Spaziatura lettere (px)', type: 'range', min: -5, max: 20, step: 0.5 },
    { key: 'title_line_height', label: 'Interlinea', type: 'range', min: 0.8, max: 2, step: 0.05 },
    { key: 'title_text_transform', label: 'Trasformazione', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'uppercase', label: 'MAIUSCOLO' },
      { value: 'lowercase', label: 'minuscolo' },
      { value: 'capitalize', label: 'Capitalizza' },
    ]},
    { key: 'title_color', label: 'Colore titolo', type: 'color' },
    { key: 'title_text_shadow', label: 'Ombra testo', type: 'select', options: [
      { value: '', label: 'Nessuna' },
      { value: '2px 2px 4px rgba(0,0,0,0.3)', label: 'Leggera' },
      { value: '3px 3px 8px rgba(0,0,0,0.5)', label: 'Media' },
      { value: '4px 4px 12px rgba(0,0,0,0.6)', label: 'Forte' },
      { value: '0 0 20px rgba(0,0,0,0.8)', label: 'Alone scuro' },
      { value: '0 0 30px rgba(255,255,255,0.6)', label: 'Alone chiaro' },
      { value: 'custom', label: 'Personalizzata' },
    ]},
    { key: 'title_text_shadow_h', label: 'Offset H (px)', type: 'range', min: -20, max: 20, step: 1,
      condition: { field: 'title_text_shadow', op: 'eq', value: 'custom' } },
    { key: 'title_text_shadow_v', label: 'Offset V (px)', type: 'range', min: -20, max: 20, step: 1,
      condition: { field: 'title_text_shadow', op: 'eq', value: 'custom' } },
    { key: 'title_text_shadow_blur', label: 'Sfocatura (px)', type: 'range', min: 0, max: 40, step: 1,
      condition: { field: 'title_text_shadow', op: 'eq', value: 'custom' } },
    { key: 'title_text_shadow_color', label: 'Colore ombra', type: 'color',
      condition: { field: 'title_text_shadow', op: 'eq', value: 'custom' } },

    // ── Tipografia sottotitolo ──
    { type: 'separator', label: 'Tipografia sottotitolo' },
    { key: 'subtitle_font_size', label: 'Dimensione (px)', type: 'range', responsive: true, min: 12, max: 48, step: 1 },
    { key: 'subtitle_font_weight', label: 'Peso', type: 'select', options: [
      { value: '300', label: 'Light' },
      { value: '400', label: 'Regular' },
      { value: '500', label: 'Medium' },
      { value: '600', label: 'Semibold' },
      { value: '700', label: 'Bold' },
    ]},
    { key: 'subtitle_letter_spacing', label: 'Spaziatura lettere (px)', type: 'range', min: -2, max: 10, step: 0.5 },
    { key: 'subtitle_color', label: 'Colore sottotitolo', type: 'color' },
    { key: 'subtitle_max_width', label: 'Larghezza max (px)', type: 'range', min: 200, max: 1000, step: 10 },

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
    { key: 'tile_padding', label: 'Padding (px)', type: 'spacing', max: 200 },

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
    { key: 'cta_radius', label: 'Raggio bordo CTA (px)', type: 'border-radius' },
    { key: 'cta_radius_hover', label: 'Raggio bordo (hover)', type: 'border-radius' },

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
    ...shadowField,
    ...textEffectsFields([
      { value: 'title', label: 'Solo Titolo' },
      { value: 'subtitle', label: 'Solo Sottotitolo' },
      { value: 'all', label: 'Tutti gli elementi testuali' },
    ]),
    ...borderFields(),
  ],
};
