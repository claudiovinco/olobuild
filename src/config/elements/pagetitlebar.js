import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, focalField } from './_shared';
import { shadowField } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Page Title Bar — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → title_tag (HTML), subtitle (testo), toggle show_breadcrumbs, breadcrumb_separator,
 *                   asset sfondo (bg_image), toggle bg_parallax, toggle border_bottom
 *   styleFields[] → preset, bg, typography_preset, colori e dimensioni titolo/sottotitolo/breadcrumbs,
 *                   sfondo (bg_color, overlay, size, position), min_height, tile_padding, content_width,
 *                   border_color, textEffectsFields, shadow, borderFields
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'pagetitlebar',
  name: t('Page Title Bar'),
  icon: 'dashicons-format-aside',
  category: 'structure',

  // Unificazione sfondo: il campo asset legacy bg_image (+ posizione focal bg_position)
  // confluisce nel pannello unico media_bg (immagine/video/gradiente/colore…). Non
  // distruttivo: le chiavi vecchie restano come fallback nei renderer. NB: qui la chiave
  // posizione legacy è `bg_position` (non `bg_image_object_position`).
  bgMigrate: { imageKey: 'bg_image', imagePosKey: 'bg_position' },

  defaults: {
    preset: 'custom',
    media_bg: { type: 'none' },
    bg: { type: 'none' },
    typography_preset: '',
    title_tag: 'h1',
    title_color: '',
    title_size: '36',
    title_weight: '700',
    title_align: 'center',
    subtitle: '',
    subtitle_color: '',
    subtitle_size: '16',
    show_breadcrumbs: true,
    breadcrumb_color: '',
    breadcrumb_separator: '/',
    bg_color: '',
    bg_image: '',
    bg_overlay: '60',
    bg_overlay_color: 'var(--olo-color-dark, #16263d)',
    bg_size: 'cover',
    bg_position: 'center center',
    bg_parallax: false,
    min_height: '200',
    tile_padding: { top: 40, right: 0, bottom: 40, left: 0 },
    content_width: '1200',
    border_bottom: false,
    border_color: '',
    shadow: 'none',
    ...textEffectsDefaults,
    text_effect_target: 'subtitle',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Sottotitolo') },
    { key: 'subtitle', label: t('Sottotitolo'), type: 'text' },

    { type: 'separator', label: t('Breadcrumbs') },
    { key: 'show_breadcrumbs', label: t('Mostra breadcrumbs'), type: 'toggle' },
    { key: 'breadcrumb_separator', label: t('Separatore'), type: 'text', show: s => s.show_breadcrumbs },

    { type: 'separator', label: t('Sfondo') },
    { key: 'media_bg', label: t('Sfondo (immagine, video, gradiente, colore…)'), type: 'background', showParallax: false },
    { key: 'bg_parallax', label: t('Parallax'), type: 'toggle',
      show: s => (s.media_bg && s.media_bg.type === 'image' && !!s.media_bg.image_url) || !!s.bg_image },

    { type: 'separator', label: t('Bordo inferiore') },
    { key: 'border_bottom', label: t('Bordo inferiore'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean',    label: t('Modern Clean') },
      { value: 'minimal-mono',    label: t('Minimal Mono') },
      { value: 'magazine-bold',   label: t('Magazine Bold') },
      { value: 'editorial-serif', label: t('Editorial Serif') },
      { value: 'compact-inline',  label: t('Compact Inline') },
      { value: 'glass-frosted',   label: t('Glass Frosted') },
      { value: 'neon-glow',       label: t('Neon Glow') },
      { value: 'brutalist-stamp', label: t('Brutalist Stamp') },
      { value: 'gradient-aurora', label: t('Gradient Aurora') },
      { value: 'sticker-fun',     label: t('Sticker Fun') },
      { value: 'retro-terminal',  label: t('Retro Terminal') },
      { value: 'tilt-3d',         label: t('3D Tilt') },
      { value: 'custom',          label: t('Personalizzato') },
    ] },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Titolo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        tag:    'title_tag',
        size:   'title_size',
        weight: 'title_weight',
        color:  'title_color',
      },
      sizeMin: 14, sizeMax: 80, sizeStep: 1,
    },
    { type: 'typography', label: t('Sottotitolo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:  'subtitle_size',
        color: 'subtitle_color',
      },
      sizeMin: 12, sizeMax: 40, sizeStep: 1,
    },
    { type: 'typography', label: t('Breadcrumbs'),
      presetKey: 'typography_preset',
      keys: {
        color: 'breadcrumb_color',
      },
    },
    { key: 'title_align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ] },

    { type: 'separator', label: t('Sfondo') },
    // bg_color = superficie di base (backdrop scuro del brand) dietro il media_bg:
    // resta visibile anche con media trasparente/PNG. Sempre disponibile.
    { key: 'bg_color', label: t('Colore sfondo (base)'), type: 'color' },
    // Overlay scuro sul media: vale sia per media_bg immagine/video sia per il legacy bg_image.
    { key: 'bg_overlay', label: t('Opacita overlay (%)'), type: 'range', min: 0, max: 100,
      show: s => (s.media_bg && (s.media_bg.type === 'image' || s.media_bg.type === 'video') && (!!s.media_bg.image_url || !!s.media_bg.video_url)) || !!s.bg_image },
    { key: 'bg_overlay_color', label: t('Colore overlay'), type: 'color',
      show: s => (s.media_bg && (s.media_bg.type === 'image' || s.media_bg.type === 'video') && (!!s.media_bg.image_url || !!s.media_bg.video_url)) || !!s.bg_image },
    // Dimensione/posizione: solo per il legacy bg_image (il pannello media_bg gestisce size/posizione internamente).
    { key: 'bg_size', label: t('Dimensione sfondo'), type: 'select', options: [
      { value: 'cover', label: t('Cover') },
      { value: 'contain', label: t('Contain') },
      { value: 'auto', label: t('Auto') },
    ], show: s => !!s.bg_image && !(s.media_bg && s.media_bg.type && s.media_bg.type !== 'none') },
    { ...focalField('bg_image', { key: 'bg_position', fit: 'bg_size' }),
      show: s => !!s.bg_image && !(s.media_bg && s.media_bg.type && s.media_bg.type !== 'none') },

    { type: 'separator', label: t('Layout') },
    { key: 'min_height', label: t('Altezza minima (px)'), type: 'range', min: 0, max: 600, step: 10 },
    { key: 'tile_padding', label: t('Padding (px)'), type: 'spacing', max: 200 },
    { key: 'content_width', label: t('Larghezza contenuto (px)'), type: 'range', min: 600, max: 1600, step: 50 },

    { type: 'separator', label: t('Bordo inferiore') },
    { key: 'border_color', label: t('Colore bordo'), type: 'color', show: s => s.border_bottom },

    ...textEffectsFields([ { value: 'subtitle', label: t('Solo Sottotitolo') } ]),
    ...shadowField,
    ...borderFields(),
  ],
};
