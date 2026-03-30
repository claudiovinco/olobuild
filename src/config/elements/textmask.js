import { shadowField } from './_shared.js';

export default {
  type: 'textmask',
  name: 'Text Mask Video',
  icon: 'dashicons-editor-textcolor',
  category: 'creative',
  defaults: {
    // Testo
    text: 'WELCOME\nTO THE WORLD',
    multiline: true,
    font_size: '120',
    font_size_tablet: '80',
    font_size_mobile: '50',
    font_weight: '900',
    font_family: '',
    text_transform: 'uppercase',
    letter_spacing: '5',
    line_height: '1',
    text_align: 'center',

    // Video
    video_url: '',
    video_poster: '',
    video_opacity: '100',

    // Layout
    min_height: '100vh',
    tile_padding: { top: 40, right: 20, bottom: 40, left: 20 },
    vertical_align: 'center',
    bg_color: '',

    // Maschera
    mask_mode: 'text_reveals_video',
    blend_mode: 'normal',
    text_fill: '',

    // Animazione scroll
    scroll_animate: true,
    scroll_start: '0',
    scroll_end: '100',
    scroll_scale: true,
    scroll_scale_from: '100',
    scroll_scale_to: '300',
    scroll_opacity: true,
    scroll_opacity_from: '100',
    scroll_opacity_to: '0',
    scroll_blur: false,
    scroll_blur_from: '0',
    scroll_blur_to: '10',

    // Overlay
    overlay_color: '',
    overlay_opacity: '0',

    shadow: 'none',
  },
  fields: [
    // ── Testo ──
    { key: 'text', label: 'Testo', type: 'textarea', rows: 3, placeholder: 'WELCOME\nTO THE WORLD' },
    { key: 'multiline', label: 'Testo su più righe', type: 'toggle' },
    { key: 'font_size', label: 'Dimensione testo (px)', type: 'range', min: 30, max: 300, step: 5 },
    { key: 'font_size_tablet', label: 'Dimensione tablet (px)', type: 'range', min: 20, max: 200, step: 5 },
    { key: 'font_size_mobile', label: 'Dimensione mobile (px)', type: 'range', min: 16, max: 150, step: 5 },
    { key: 'font_weight', label: 'Peso font', type: 'select', options: [
      { value: '400', label: 'Regular' },
      { value: '500', label: 'Medium' },
      { value: '600', label: 'Semi Bold' },
      { value: '700', label: 'Bold' },
      { value: '800', label: 'Extra Bold' },
      { value: '900', label: 'Black' },
    ]},
    { key: 'font_family', label: 'Font family', type: 'font-family' },
    { key: 'text_transform', label: 'Trasformazione', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'uppercase', label: 'Maiuscolo' },
      { value: 'capitalize', label: 'Capitalizzato' },
      { value: 'lowercase', label: 'Minuscolo' },
    ]},
    { key: 'letter_spacing', label: 'Spaziatura lettere (px)', type: 'range', min: 0, max: 30, step: 1 },
    { key: 'line_height', label: 'Altezza riga', type: 'select', options: [
      { value: '0.8', label: '0.8' },
      { value: '0.9', label: '0.9' },
      { value: '1', label: '1' },
      { value: '1.1', label: '1.1' },
      { value: '1.2', label: '1.2' },
      { value: '1.4', label: '1.4' },
    ]},
    { key: 'text_align', label: 'Allineamento testo', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'center', label: 'Centro' },
      { value: 'right', label: 'Destra' },
    ]},

    // ── Video ──
    { type: 'separator', label: 'Video' },
    { key: 'video_url', label: 'Video (MP4/WebM)', type: 'media' },
    { key: 'video_poster', label: 'Immagine poster', type: 'image' },
    { key: 'video_opacity', label: 'Opacità video (%)', type: 'range', min: 10, max: 100, step: 5 },

    // ── Layout ──
    { type: 'separator', label: 'Layout' },
    { key: 'min_height', label: 'Altezza minima (es. 100vh, 600px)', type: 'text' },
    { key: 'tile_padding', label: 'Padding (px)', type: 'spacing', max: 200 },
    { key: 'vertical_align', label: 'Allineamento verticale', type: 'select', options: [
      { value: 'top', label: 'Alto' },
      { value: 'center', label: 'Centro' },
      { value: 'bottom', label: 'Basso' },
    ]},
    { key: 'bg_color', label: 'Colore sfondo', type: 'color' },

    // ── Maschera ──
    { type: 'separator', label: 'Maschera' },
    { key: 'mask_mode', label: 'Modalità maschera', type: 'select', options: [
      { value: 'text_reveals_video', label: 'Testo rivela il video' },
      { value: 'video_behind_text', label: 'Video dietro al testo (clip)' },
      { value: 'blend', label: 'Blend mode' },
    ]},
    { key: 'blend_mode', label: 'Blend mode', type: 'select', options: [
      { value: 'normal', label: 'Normal' },
      { value: 'multiply', label: 'Multiply' },
      { value: 'screen', label: 'Screen' },
      { value: 'overlay', label: 'Overlay' },
      { value: 'darken', label: 'Darken' },
      { value: 'lighten', label: 'Lighten' },
      { value: 'color-dodge', label: 'Color Dodge' },
      { value: 'color-burn', label: 'Color Burn' },
      { value: 'hard-light', label: 'Hard Light' },
      { value: 'soft-light', label: 'Soft Light' },
      { value: 'difference', label: 'Difference' },
      { value: 'exclusion', label: 'Exclusion' },
    ], show: s => s.mask_mode === 'blend' },
    { key: 'text_fill', label: 'Colore testo (blend)', type: 'color',
      show: s => s.mask_mode === 'blend' },

    // ── Animazione scroll ──
    { type: 'separator', label: 'Animazione Scroll' },
    { key: 'scroll_animate', label: 'Animazione su scroll', type: 'toggle' },
    { key: 'scroll_start', label: 'Inizio animazione (% viewport)', type: 'range', min: 0, max: 100, step: 5,
      show: s => s.scroll_animate },
    { key: 'scroll_end', label: 'Fine animazione (% viewport)', type: 'range', min: 0, max: 100, step: 5,
      show: s => s.scroll_animate },

    { key: 'scroll_scale', label: 'Scala', type: 'toggle',
      show: s => s.scroll_animate },
    { key: 'scroll_scale_from', label: 'Scala iniziale (%)', type: 'range', min: 10, max: 200, step: 5,
      show: s => s.scroll_animate && s.scroll_scale },
    { key: 'scroll_scale_to', label: 'Scala finale (%)', type: 'range', min: 50, max: 1000, step: 25,
      show: s => s.scroll_animate && s.scroll_scale },

    { key: 'scroll_opacity', label: 'Opacità', type: 'toggle',
      show: s => s.scroll_animate },
    { key: 'scroll_opacity_from', label: 'Opacità iniziale (%)', type: 'range', min: 0, max: 100, step: 5,
      show: s => s.scroll_animate && s.scroll_opacity },
    { key: 'scroll_opacity_to', label: 'Opacità finale (%)', type: 'range', min: 0, max: 100, step: 5,
      show: s => s.scroll_animate && s.scroll_opacity },

    { key: 'scroll_blur', label: 'Sfocatura', type: 'toggle',
      show: s => s.scroll_animate },
    { key: 'scroll_blur_from', label: 'Sfocatura iniziale (px)', type: 'range', min: 0, max: 30, step: 1,
      show: s => s.scroll_animate && s.scroll_blur },
    { key: 'scroll_blur_to', label: 'Sfocatura finale (px)', type: 'range', min: 0, max: 30, step: 1,
      show: s => s.scroll_animate && s.scroll_blur },

    // ── Overlay ──
    { type: 'separator', label: 'Overlay' },
    { key: 'overlay_color', label: 'Colore overlay', type: 'color' },
    { key: 'overlay_opacity', label: 'Opacità overlay (%)', type: 'range', min: 0, max: 100, step: 5 },

    ...shadowField,
  ],
};
