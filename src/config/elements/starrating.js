import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared';
import { shadowField } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile StarRating — split CONTENUTO/STILE.
 *   fields[]      → rating, max_stars, title, subtitle
 *   styleFields[] → preset, bg, typo, star size/color/empty/style, title color/subtitle color, alignment, text-effects, shadow, border
 */
export default {
  type: 'starrating',
  name: t('Valutazione'),
  icon: 'dashicons-star-filled',
  category: 'marketing',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    rating: '4',
    max_stars: '5',
    star_size: '32',
    star_color: '',
    empty_color: '',
    style: 'filled',
    title: '',
    subtitle: '',
    title_color: '',
    subtitle_color: '',
    alignment: 'center',
    shadow: 'none',
    ...textEffectsDefaults,
    text_effect_target: 'title',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'rating', label: t('Valutazione'), type: 'range', min: 0, max: 5, step: 0.5 },
    { key: 'max_stars', label: t('Stelle massime'), type: 'range', min: 1, max: 10, step: 1 },
    { key: 'title', label: t('Titolo'), type: 'text' },
    { key: 'subtitle', label: t('Sottotitolo'), type: 'text' },
  ],

  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'classic-stars',   label: t('Classic Stars') },
      { value: 'minimal-line',    label: t('Minimal Line') },
      { value: 'compact-numeric', label: t('Compact Numeric') },
      { value: 'hearts-pink',     label: t('Hearts Pink') },
      { value: 'diamonds-luxury', label: t('Diamonds Luxury') },
      { value: 'glass-stars',     label: t('Glass Stars') },
      { value: 'neon-glow',       label: t('Neon Glow') },
      { value: 'brutalist-stamp', label: t('Brutalist Stamp') },
      { value: 'gradient-rainbow', label: t('Gradient Rainbow') },
      { value: 'sticker-stars',   label: t('Sticker Stars') },
      { value: 'retro-arcade',    label: t('Retro Arcade') },
      { value: 'tilt-3d',         label: t('3D Tilt') },
      { value: 'custom',          label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Stile stelle') },
    { key: 'star_size', label: t('Dimensione stelle (px)'), type: 'range', min: 16, max: 64, step: 4 },
    { key: 'star_color', label: t('Colore stelle piene'), type: 'color' },
    { key: 'empty_color', label: t('Colore stelle vuote'), type: 'color' },
    { key: 'style', label: t('Stile'), type: 'select', options: [
      { value: 'filled', label: t('Pieno') },
      { value: 'outline', label: t('Contorno') },
      { value: 'rounded', label: t('Arrotondato') },
    ]},

    { type: 'separator', label: t('Allineamento') },
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},

    ...textEffectsFields([
      { value: 'title', label: t('Solo Titolo') },
      { value: 'subtitle', label: t('Solo Sottotitolo') },
      { value: 'all', label: t('Tutti gli elementi testuali') },
    ]),

    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Titolo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'title_color',
      },
      sizeMin: 12, sizeMax: 60,
    },
    { type: 'typography', label: t('Sottotitolo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'subtitle_color',
      },
      sizeMin: 12, sizeMax: 60,
    },

    ...shadowField,
    ...borderFields(),
  ],
};
