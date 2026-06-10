import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared';
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
  defaults: {
    preset: 'custom',
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
    bg_overlay_color: '#000000',
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

    { type: 'separator', label: t('Sfondo (asset)') },
    { key: 'bg_image', label: t('Immagine sfondo'), type: 'media' },
    { key: 'bg_parallax', label: t('Parallax'), type: 'toggle', show: s => !!s.bg_image },

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
    { key: 'bg_color', label: t('Colore sfondo'), type: 'color' },
    { key: 'bg_overlay', label: t('Opacita overlay (%)'), type: 'range', min: 0, max: 100, show: s => !!s.bg_image },
    { key: 'bg_overlay_color', label: t('Colore overlay'), type: 'color', show: s => !!s.bg_image },
    { key: 'bg_size', label: t('Dimensione sfondo'), type: 'select', options: [
      { value: 'cover', label: t('Cover') },
      { value: 'contain', label: t('Contain') },
      { value: 'auto', label: t('Auto') },
    ], show: s => !!s.bg_image },
    { key: 'bg_position', label: t('Posizione sfondo'), type: 'select', options: [
      { value: 'center center', label: t('Centro') },
      { value: 'top center', label: t('Alto') },
      { value: 'bottom center', label: t('Basso') },
      { value: 'left center', label: t('Sinistra') },
      { value: 'right center', label: t('Destra') },
    ], show: s => !!s.bg_image },

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
