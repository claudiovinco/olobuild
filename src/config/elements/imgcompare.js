
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Image Compare — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → 2 immagini before/after, etichette + show_labels, posizione iniziale, orientamento, autoplay+timing
 *   styleFields[] → preset, typo, slider aspect (colore/dimensione maniglia/spessore linea), altezza card, radius, fit, ombra card, bordo
 *   AVANZATE      → meta tecnico
 */
export default {
  type: 'imgcompare',
  name: t('Confronto Immagini'),
  icon: 'dashicons-image-flip-horizontal',
  category: 'media',
  defaults: {
    typography_preset: '',
    preset: 'custom',
    before_image: '',
    after_image: '',
    before_label: 'Prima',
    after_label: 'Dopo',
    show_labels: true,
    start_position: '50',
    orientation: 'horizontal',
    handle_color: '',
    handle_size: '40',
    handle_border: '3',
    line_width: '3',
    height: '400',
    border_radius: '8',
    object_fit: 'cover',
    object_position: 'center center',
    card_border_width: '0',
    card_border_color: '',
    card_shadow: 'none',
    autoplay: false,
    autoplay_delay: '3',
    autoplay_speed: '2',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'before_image', label: 'Immagine "Prima"', type: 'image' },
    { key: 'after_image', label: 'Immagine "Dopo"', type: 'image' },
    { key: 'before_label', label: t('Etichetta Prima'), type: 'text' },
    { key: 'after_label', label: t('Etichetta Dopo'), type: 'text' },
    { key: 'show_labels', label: t('Mostra etichette'), type: 'toggle' },

    { type: 'separator', label: t('Comportamento slider') },
    { key: 'start_position', label: t('Posizione iniziale (%)'), type: 'range', min: 0, max: 100 },
    { key: 'orientation', label: t('Orientamento'), type: 'select', options: [
      { value: 'horizontal', label: t('Orizzontale') },
      { value: 'vertical', label: t('Verticale') },
    ]},

    { type: 'separator', label: t('Autoplay') },
    { key: 'autoplay', label: t('Passaggio automatico'), type: 'toggle' },
    { key: 'autoplay_delay', label: t('Attesa inattività (sec)'), type: 'range', min: 1, max: 10,
      condition: { field: 'autoplay', value: true } },
    { key: 'autoplay_speed', label: t('Durata ciclo (sec)'), type: 'range', min: 1, max: 8,
      condition: { field: 'autoplay', value: true } },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-slider',   label: t('Modern Slider') },
      { value: 'minimal-line',    label: t('Minimal Line') },
      { value: 'magazine-bold',   label: t('Magazine Bold') },
      { value: 'cinema-wide',     label: t('Cinema Wide') },
      { value: 'before-after-tag', label: t('Before/After Tags') },
      { value: 'glass-handle',    label: t('Glass Handle') },
      { value: 'neon-divider',    label: t('Neon Divider') },
      { value: 'brutalist-block', label: t('Brutalist Block') },
      { value: 'gradient-line',   label: t('Gradient Line') },
      { value: 'sticker-handle',  label: t('Sticker Handle') },
      { value: 'retro-vhs',       label: t('Retro VHS') },
      { value: 'tilt-3d',         label: t('3D Tilt') },
      { value: 'custom',          label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Slider — Aspetto') },
    { key: 'handle_color', label: t('Colore maniglia'), type: 'color' },
    { key: 'handle_size', label: t('Dimensione maniglia (px)'), type: 'range', min: 24, max: 60 },
    { key: 'handle_border', label: t('Spessore bordo (px)'), type: 'range', min: 1, max: 6 },
    { key: 'line_width', label: t('Spessore linea (px)'), type: 'range', min: 1, max: 6 },

    { type: 'separator', label: t('Card') },
    { key: 'height', label: t('Altezza (px)'), type: 'range', min: 200, max: 800, step: 10 },
    withHover({ key: 'border_radius', label: t('Raggio bordo (px)'), type: 'border-radius' }),
    { key: 'object_fit', label: t('Adattamento immagini'), type: 'select', options: [
      { value: 'cover', label: t('Riempi (cover)') },
      { value: 'contain', label: t('Contieni') },
    ]},
    { key: 'object_position', label: t('Posizione contenuto'), type: 'object-position',
      contextKeys: { src: 'before_image', fit: 'object_fit', height: 'height' } },
    { key: 'card_border_width', label: t('Bordo (px)'), type: 'range', min: 0, max: 6 },
    { key: 'card_border_color', label: t('Colore bordo'), type: 'color' },
    { key: 'card_shadow', label: t('Ombra'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'sm', label: t('Leggera') },
      { value: 'md', label: t('Media') },
      { value: 'lg', label: t('Grande') },
      { value: 'xl', label: t('Extra grande') },
      { value: 'custom', label: t('Personalizzata') },
    ]},
    { key: 'card_shadow_h', label: t('Offset H (px)'), type: 'range', min: -50, max: 50, step: 1,
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_v', label: t('Offset V (px)'), type: 'range', min: -50, max: 50, step: 1,
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_blur', label: t('Sfocatura (px)'), type: 'range', min: 0, max: 100, step: 1,
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_spread', label: t('Espansione (px)'), type: 'range', min: -50, max: 50, step: 1,
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_color', label: t('Colore ombra'), type: 'color',
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_inset', label: t('Ombra interna'), type: 'toggle',
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },

    ...borderFields(),
  ],
};
