import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared';
import { t } from '@/i18n';

/**
 * Tile CounterCircle — split CONTENUTO/STILE.
 *   fields[]      → value, max_value, prefix, suffix, title, title_position
 *   styleFields[] → preset, bg, typo, text-effects, size, stroke width, colori, animation duration, border
 */
export default {
  type: 'countercircle',
  name: t('Counter Circle'),
  icon: 'dashicons-marker',
  category: 'content',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    value: '75',
    max_value: '100',
    suffix: '%',
    prefix: '',
    title: t('Progresso'),
    size: '160',
    stroke_width: '10',
    stroke_color: '',
    track_color: '',
    text_color: '',
    title_color: '',
    duration: '1500',
    title_position: 'below',
    ...textEffectsDefaults,
    text_effect_target: 'title',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'value', label: t('Valore'), type: 'range', min: 0, max: 1000, step: 1 },
    { key: 'max_value', label: t('Valore massimo'), type: 'range', min: 1, max: 1000, step: 1 },
    { key: 'suffix', label: t('Suffisso'), type: 'text' },
    { key: 'prefix', label: t('Prefisso'), type: 'text' },
    { key: 'title', label: t('Titolo'), type: 'text' },
    { key: 'title_position', label: t('Posizione titolo'), type: 'select', options: [
      { value: 'below', label: t('Sotto') },
      { value: 'inside', label: t('Dentro') },
      { value: 'above', label: t('Sopra') },
    ]},
  ],

  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean',     label: t('Modern Clean') },
      { value: 'minimal-thin',     label: t('Minimal Thin') },
      { value: 'magazine-bold',    label: t('Magazine Bold') },
      { value: 'gauge-meter',      label: t('Gauge Meter') },
      { value: 'centered-large',   label: t('Centered Large') },
      { value: 'glass-ring',       label: t('Glass Ring') },
      { value: 'neon-pulse',       label: t('Neon Pulse') },
      { value: 'brutalist-arc',    label: t('Brutalist Arc') },
      { value: 'gradient-rainbow', label: t('Gradient Rainbow') },
      { value: 'sticker-badge',    label: t('Sticker Badge') },
      { value: 'retro-dial',       label: t('Retro Dial') },
      { value: 'tilt-3d',          label: t('3D Tilt') },
      { value: 'custom',           label: t('Personalizzato') },
    ]},
    ...textEffectsFields([ { value: 'title', label: t('Solo Titolo') } ]),

    { type: 'separator', label: t('Tipografia') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Valore'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'text_color',
      },
      sizeMin: 12, sizeMax: 60,
    },
    { type: 'typography', label: t('Titolo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'title_color',
      },
      sizeMin: 12, sizeMax: 60,
    },

    { type: 'separator', label: t('Aspetto') },
    { key: 'size', label: t('Dimensione (px)'), type: 'range', min: 60, max: 400, step: 10 },
    { key: 'stroke_width', label: t('Spessore traccia'), type: 'range', min: 2, max: 30, step: 1 },

    { type: 'separator', label: t('Colori') },
    { key: 'stroke_color', label: t('Colore progresso'), type: 'color' },
    { key: 'track_color', label: t('Colore traccia'), type: 'color' },

    { type: 'separator', label: t('Animazione') },
    { key: 'duration', label: t('Durata animazione (ms)'), type: 'range', min: 0, max: 5000, step: 100 },

    ...borderFields(),
  ],
};
