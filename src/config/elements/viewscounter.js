import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared';
import { t } from '@/i18n';

/**
 * Tile ViewsCounter — split CONTENUTO/STILE.
 *   fields[]      → show_icon, icon_position, label, show_label, number_format, layout
 *   styleFields[] → preset, bg, typo, text-effects, icon size, tipografia, colori, border
 */
export default {
  type: 'viewscounter',
  name: t('Contatore Visite'),
  icon: 'dashicons-visibility',
  category: 'dynamic',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    show_icon: true,
    icon_position: 'before',
    label: t('visualizzazioni'),
    show_label: true,
    text_color: '',
    icon_color: '',
    font_size: '14',
    font_weight: '400',
    layout: 'inline',
    icon_size: '16',
    number_format: true,
    ...textEffectsDefaults,
    text_effect_target: 'label',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'show_icon', label: t('Mostra icona'), type: 'toggle' },
    { key: 'label', label: t('Etichetta'), type: 'text' },
    { key: 'show_label', label: t('Mostra etichetta'), type: 'toggle' },
    { key: 'number_format', label: t('Formato con separatore migliaia'), type: 'toggle' },

    { type: 'separator', label: t('Layout') },
    { key: 'layout', label: t('Layout'), type: 'select', options: [
      { value: 'inline', label: t('In linea') },
      { value: 'block', label: t('A blocchi') },
    ]},
  ],

  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean',    label: t('Modern Clean') },
      { value: 'minimal-mono',    label: t('Minimal Mono') },
      { value: 'magazine-pill',   label: t('Magazine Pill') },
      { value: 'badge-floating',  label: t('Badge Floating') },
      { value: 'compact-icon',    label: t('Compact Icon') },
      { value: 'glass-pill',      label: t('Glass Pill') },
      { value: 'neon-glow',       label: t('Neon Glow') },
      { value: 'brutalist-stamp', label: t('Brutalist Stamp') },
      { value: 'gradient-soft',   label: t('Gradient Soft') },
      { value: 'sticker-badge',   label: t('Sticker Badge') },
      { value: 'retro-digital',   label: t('Retro Digital') },
      { value: 'tilt-3d',         label: t('3D Tilt') },
      { value: 'custom',          label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    ...textEffectsFields([ { value: 'label', label: t('Solo Etichetta') } ]),

    { type: 'separator', label: t('Icona — Aspetto') },
    { key: 'icon_position', label: t('Posizione icona'), type: 'select', options: [
      { value: 'before', label: t('Prima del numero') },
      { value: 'after', label: t('Dopo il numero') },
    ], condition: { field: 'show_icon', value: true } },
    { key: 'icon_size', label: t('Dimensione icona (px)'), type: 'range', min: 10, max: 40, step: 1,
      condition: { field: 'show_icon', value: true } },

    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Testo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:   'font_size',
        weight: 'font_weight',
        color:  'text_color',
      },
      sizeMin: 10, sizeMax: 40,
    },

    { type: 'separator', label: t('Colori') },
    { key: 'icon_color', label: t('Colore icona'), type: 'color',
      condition: { field: 'show_icon', value: true } },

    ...borderFields(),
  ],
};
