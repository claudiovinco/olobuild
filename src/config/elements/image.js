import { textEffectsFields, textEffectsDefaults, shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Image — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → asset (url, hover_image/video), alt, didascalia, link
 *   styleFields[] → preset, dimensioni, fit/position, allineamento, filtri, hover-anim, lightbox, ombra, bordo
 *   AVANZATE      → meta tecnico
 */
export default {
  type: 'image',
  name: t('Immagine'),
  icon: 'dashicons-format-image',
  category: 'essential',
  defaults: {
    typography_preset: '',
    preset: 'custom',
    image_url: '',
    hover_image: '',
    hover_video: '',
    alt_text: '',
    caption: '',
    link_url: '',
    link_target: '_self',
    image_width: '100%',
    height: '300px',
    max_width: '',
    aspect_ratio: 'auto',
    aspect_ratio_custom: '16/9',
    object_fit: 'cover',
    object_position: 'center center',
    image_alignment: 'center',
    align_in_column: '',
    filter_blur: '0',
    filter_brightness: '100',
    filter_contrast: '100',
    filter_saturate: '100',
    filter_grayscale: '0',
    filter_sepia: '0',
    hover_filter_blur: '',
    hover_filter_brightness: '',
    hover_filter_contrast: '',
    hover_filter_saturate: '',
    hover_filter_grayscale: '',
    hover_filter_sepia: '',
    hover_animation: 'none',
    lightbox: false,
    shadow: 'none',
    border_radius: '0',
    hover_border_radius: '',
    hover_radius_duration: '400',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
    ...textEffectsDefaults,
    text_effect_target: 'caption',
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'image_url', label: t('Immagine'), type: 'image' },
    { key: 'alt_text', label: t('Testo alternativo'), type: 'text', aiGenerate: 'alt' },
    { key: 'caption', label: t('Didascalia'), type: 'text' },
    { key: 'link_url', label: t('URL link'), type: 'link' },
    { key: 'link_target', label: t('Apri in'), type: 'select', options: [
      { value: '_self', label: t('Stessa finestra') },
      { value: '_blank', label: t('Nuova scheda') },
    ]},

    { type: 'separator', label: t('Sostituzione al passaggio del mouse') },
    { key: 'hover_image', label: t('Immagine al passaggio (opzionale)'), type: 'image' },
    { key: 'hover_video', label: t('Video al passaggio (opzionale)'), type: 'media', accept: 'video' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean',    label: t('Modern Clean') },
      { value: 'minimal-frame',   label: t('Minimal Frame') },
      { value: 'magazine-bold',   label: t('Magazine Bold') },
      { value: 'cinema-wide',     label: t('Cinema Wide') },
      { value: 'polaroid',        label: t('Polaroid') },
      { value: 'glass-frame',     label: t('Glass Frame') },
      { value: 'neon-glow',       label: t('Neon Glow') },
      { value: 'brutalist-block', label: t('Brutalist Block') },
      { value: 'gradient-border', label: t('Gradient Border') },
      { value: 'sticker-tape',    label: t('Sticker Tape') },
      { value: 'retro-vhs',       label: t('Retro VHS') },
      { value: 'tilt-3d',         label: t('3D Tilt') },
      { value: 'custom',          label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Dimensioni') },
    { key: 'image_width', label: t('Larghezza'), type: 'text', responsive: true,
      placeholder: t('es. 100%, 320px, 50vw') },
    { key: 'height', label: t('Altezza'), type: 'text', responsive: true,
      placeholder: t('es. 300px, auto, 50vh') },
    { key: 'max_width', label: t('Larghezza massima'), type: 'text', responsive: true,
      placeholder: t('es. 600px, none') },
    { key: 'aspect_ratio', label: t('Proporzioni (Aspect Ratio)'), type: 'select', responsive: true, options: [
      { value: 'auto',  label: t('Auto (segui altezza)') },
      { value: '1/1',   label: t('1:1 (Quadrato)') },
      { value: '4/3',   label: t('4:3 (TV classico)') },
      { value: '3/2',   label: t('3:2 (Foto)') },
      { value: '16/9',  label: t('16:9 (Widescreen)') },
      { value: '21/9',  label: t('21:9 (Cinema)') },
      { value: '9/16',  label: t('9:16 (Verticale)') },
      { value: '2/3',   label: t('2:3 (Foto verticale)') },
      { value: 'custom', label: t('Personalizzato') },
    ]},
    { key: 'aspect_ratio_custom', label: t('Proporzioni personalizzate'), type: 'text',
      placeholder: t('es. 5/4, 1.618'),
      condition: { field: 'aspect_ratio', op: 'eq', value: 'custom' } },
    { key: 'object_fit', label: t('Adattamento'), type: 'select', options: [
      { value: 'cover',      label: t('Riempi (cover)') },
      { value: 'contain',    label: t('Contieni') },
      { value: 'fill',       label: t('Riempi (deforma)') },
      { value: 'none',       label: t('Originale') },
      { value: 'scale-down', label: t('Riduci se necessario') },
    ]},
    { key: 'object_position', label: t('Posizione contenuto'), type: 'select', options: [
      { value: 'left top',      label: t('↖ Alto sinistra') },
      { value: 'center top',    label: t('↑ Alto centro') },
      { value: 'right top',     label: t('↗ Alto destra') },
      { value: 'left center',   label: t('← Centro sinistra') },
      { value: 'center center', label: t('• Centro') },
      { value: 'right center',  label: t('→ Centro destra') },
      { value: 'left bottom',   label: t('↙ Basso sinistra') },
      { value: 'center bottom', label: t('↓ Basso centro') },
      { value: 'right bottom',  label: t('↘ Basso destra') },
    ]},
    { key: 'image_alignment', label: t('Allineamento orizzontale'), type: 'select', responsive: true, options: [
      { value: 'left',   label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right',  label: t('Destra') },
    ]},
    { key: 'align_in_column', label: t('Posizione verticale nella colonna'), type: 'select', options: [
      { value: '',       label: t('Predefinita (flusso naturale)') },
      { value: 'top',    label: t('Ancora in alto') },
      { value: 'center', label: t('Centra verticalmente') },
      { value: 'bottom', label: t('Ancora in basso (utile nei loop)') },
    ], description: t('Allinea l\'immagine a una posizione fissa della colonna, indipendentemente dall\'altezza del contenuto sopra/sotto. Tipico per card uniformi nei loop.') },

    { type: 'separator', label: t('Forma') },
    withHover(
      { key: 'border_radius', label: t('Border Radius'), type: 'border-radius' },
      { hoverKey: 'hover_border_radius', hoverDurationKey: 'hover_radius_duration' }
    ),

    ...textEffectsFields([ { value: 'caption', label: t('Solo Didascalia') } ]),

    { type: 'separator', label: t('Filtri CSS') },
    withHover({ key: 'filter_blur',       label: t('Sfocatura (px)'),       type: 'range', min: 0, max: 20,  step: 1 }, { hoverKey: 'hover_filter_blur' }),
    withHover({ key: 'filter_brightness', label: t('Luminosità (%)'),       type: 'range', min: 0, max: 200, step: 5 }, { hoverKey: 'hover_filter_brightness' }),
    withHover({ key: 'filter_contrast',   label: t('Contrasto (%)'),        type: 'range', min: 0, max: 200, step: 5 }, { hoverKey: 'hover_filter_contrast' }),
    withHover({ key: 'filter_saturate',   label: t('Saturazione (%)'),      type: 'range', min: 0, max: 200, step: 5 }, { hoverKey: 'hover_filter_saturate' }),
    withHover({ key: 'filter_grayscale',  label: t('Scala di grigi (%)'),   type: 'range', min: 0, max: 100, step: 5 }, { hoverKey: 'hover_filter_grayscale' }),
    withHover({ key: 'filter_sepia',      label: t('Seppia (%)'),           type: 'range', min: 0, max: 100, step: 5 }, { hoverKey: 'hover_filter_sepia' }),

    { type: 'separator', label: t('Animazione hover') },
    { key: 'hover_animation', label: t('Animazione hover'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'zoom-in', label: t('Zoom in') },
      { value: 'zoom-out', label: t('Zoom out') },
      { value: 'slide-up', label: t('Scorrimento su') },
      { value: 'rotate-cw', label: t('Rotazione oraria') },
      { value: 'rotate-ccw', label: t('Rotazione antioraria') },
      { value: 'blur-in', label: t('Sfocatura → nitido') },
    ]},
    { key: 'lightbox', label: t('Lightbox al click'), type: 'toggle' },

    ...shadowField,
    ...borderFields(),
  ],
};
