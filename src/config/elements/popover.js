import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared';
import { shadowField } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Popover — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → image (asset), markers (items con coordinate, titolo, contenuto, immagine popup), image_alt
 *   styleFields[] → preset, bg, typography_preset, textEffectsFields, image_height,
 *                   colori marcatore/popup, popup_radius, popup_img_height, popup_hover_effect, popup_hover_color,
 *                   shadow, borderFields
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'popover',
  name: t('Popover'),
  icon: 'dashicons-location-alt',
  category: 'interactive',
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    image: '',
    markers: [
      { id: 'mk-1', x: 25, y: 30, title: t('Punto 1'), content: 'Descrizione...', image: '' },
      { id: 'mk-2', x: 70, y: 60, title: t('Punto 2'), content: 'Descrizione...', image: '' },
    ],
    image_alt: '',
    image_height: '0',
    object_position: 'center center',
    marker_color: '',
    popup_bg: '',
    popup_color: '',
    popup_radius: '8',
    popup_img_height: '120',
    popup_hover_effect: 'none',
    popup_hover_color: '',
    shadow: 'none',
    ...textEffectsDefaults,
    text_effect_target: 'title',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Immagine') },
    { key: 'image', label: t('Immagine'), type: 'image' },
    { key: 'image_alt', label: t('Testo alternativo immagine'), type: 'text' },
    { type: 'separator', label: t('Marcatori') },
    { key: 'markers', label: t('Marcatori'), type: 'content-items',
      itemFields: [
        { key: 'x', label: t('Posizione X (%)'), type: 'range', min: 0, max: 100, step: 1 },
        { key: 'y', label: t('Posizione Y (%)'), type: 'range', min: 0, max: 100, step: 1 },
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'content', label: t('Contenuto'), type: 'textarea' },
        { key: 'image', label: t('Immagine popup'), type: 'image' },
      ],
      newItemDefaults: { x: 50, y: 50, title: t('Nuovo punto'), content: 'Descrizione...', image: '' },
      itemLabel: 'Marcatore',
    },
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

    ...textEffectsFields([
      { value: 'title', label: t('Solo Titolo') },
      { value: 'content', label: t('Solo Contenuto') },
      { value: 'all', label: t('Tutti gli elementi testuali') },
    ]),

    { type: 'separator', label: t('Dimensioni immagine') },
    { key: 'image_height', label: t('Altezza immagine (px, 0 = auto)'), type: 'range', min: 0, max: 700, step: 10 },
    { key: 'object_position', label: t('Posizione contenuto'), type: 'object-position', contextKeys: { src: 'image' } },

    { type: 'separator', label: t('Marcatori e popup') },
    { key: 'marker_color', label: t('Colore marcatore'), type: 'color' },
    { key: 'popup_bg', label: t('Sfondo popup'), type: 'color' },
    { key: 'popup_color', label: t('Colore testo popup'), type: 'color' },
    withHover({ key: 'popup_radius', label: t('Arrotondamento popup (px)'), type: 'border-radius' }),

    { type: 'separator', label: t('Immagine popup') },
    { key: 'popup_img_height', label: t('Altezza immagine popup (px)'), type: 'range', min: 60, max: 300, step: 10 },
    { key: 'popup_hover_effect', label: t('Effetto hover immagine'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'zoom', label: t('Zoom') },
      { value: 'zoom-rotate', label: t('Zoom + rotazione') },
      { value: 'brightness', label: t('Luminosità') },
      { value: 'desaturate', label: t('Desatura → colore') },
      { value: 'blur-in', label: t('Sfocatura → nitido') },
      { value: 'color-overlay', label: t('Colore additivo') },
    ]},
    { key: 'popup_hover_color', label: t('Colore overlay hover'), type: 'color',
      condition: { field: 'popup_hover_effect', value: 'color-overlay' } },

    ...shadowField,
    ...borderFields(),
  ],
};
