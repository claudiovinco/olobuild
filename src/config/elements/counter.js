import { textEffectsFields, textEffectsDefaults, shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Counter — split CONTENUTO/STILE.
 *   fields[]      → number, label, prefix, suffix, icon, sfondo (image/video)
 *   styleFields[] → preset, bg, typo, text-effects, icon size, tipografia, colori, overlay, padding, radius, shadow, border
 */
export default {
  type: 'counter',
  name: t('Contatore'),
  icon: 'dashicons-performance',
  category: 'marketing',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    number: '1250',
    label: t('Clienti soddisfatti'),
    prefix: '',
    suffix: '+',
    icon_emoji: 'bolt',
    icon_size: '40',
    text_color: '',
    number_font_size: '48',
    number_font_weight: '700',
    label_color: '',
    label_font_size: '14',
    label_font_weight: '400',
    bg_type: 'color',
    bg_color: '',
    bg_image: '',
    bg_video: '',
    overlay: false,
    overlay_color: 'var(--olo-color-dark, #16263d)',
    overlay_opacity: '50',
    tile_padding: { top: 32, right: 32, bottom: 32, left: 32 },
    border_radius: '0',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
    shadow: 'none',
    ...textEffectsDefaults,
    text_effect_target: 'label',
  },

  fields: [
    { key: 'number', label: t('Numero'), type: 'text' },
    { key: 'label', label: t('Etichetta'), type: 'text' },
    { key: 'prefix', label: t('Prefisso'), type: 'text' },
    { key: 'suffix', label: t('Suffisso'), type: 'text' },
    { key: 'icon_emoji', label: t('Icona / Emoji'), type: 'icon' },

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg_type', label: t('Tipo sfondo'), type: 'select', options: [
      { value: 'color', label: t('Colore') },
      { value: 'image', label: t('Immagine') },
      { value: 'video', label: t('Video') },
    ]},
    { key: 'bg_image', label: t('Immagine sfondo'), type: 'image',
      condition: { field: 'bg_type', value: 'image' } },
    { key: 'bg_video', label: t('Video sfondo (mp4)'), type: 'media',
      condition: { field: 'bg_type', value: 'video' } },
    { key: 'overlay', label: t('Overlay'), type: 'toggle',
      condition: { field: 'bg_type', operator: '!=', value: 'color' } },
  ],

  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-bold',      label: t('Modern Bold') },
      { value: 'minimal-thin',     label: t('Minimal Thin') },
      { value: 'magazine-editorial', label: t('Magazine Editorial') },
      { value: 'centered-circle',  label: t('Centered Circle') },
      { value: 'highlight-box',    label: t('Highlight Box') },
      { value: 'glass-card',       label: t('Glass Card') },
      { value: 'neon-glow',        label: t('Neon Glow') },
      { value: 'brutalist-mega',   label: t('Brutalist Mega') },
      { value: 'gradient-aurora',  label: t('Gradient Aurora') },
      { value: 'sticker-badge',    label: t('Sticker Badge') },
      { value: 'retro-digital',    label: t('Retro Digital') },
      { value: 'tilt-3d',          label: t('3D Tilt') },
      { value: 'custom',           label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    ...textEffectsFields([ { value: 'label', label: t('Solo Etichetta') } ]),

    { type: 'separator', label: t('Icona') },
    { key: 'icon_size', label: t('Dimensione icona (px)'), type: 'range', min: 16, max: 80, step: 2 },

    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Numero'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:   'number_font_size',
        weight: 'number_font_weight',
        color:  'text_color',
      },
      sizeMin: 20, sizeMax: 120, sizeStep: 2,
    },
    { type: 'typography', label: t('Etichetta'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:   'label_font_size',
        weight: 'label_font_weight',
        color:  'label_color',
      },
      sizeMin: 10, sizeMax: 32, sizeStep: 1,
    },

    { type: 'separator', label: t('Colori') },
    { key: 'bg_color', label: t('Colore sfondo'), type: 'color',
      condition: { field: 'bg_type', value: 'color' } },
    { key: 'overlay_color', label: t('Colore overlay'), type: 'color',
      condition: { field: 'overlay', value: true } },
    { key: 'overlay_opacity', label: t('Opacità overlay (%)'), type: 'range', min: 10, max: 100, step: 5,
      condition: { field: 'overlay', value: true } },

    { type: 'separator', label: t('Aspetto tile') },
    { key: 'tile_padding', label: t('Padding (px)'), type: 'spacing', max: 80 },
    withHover({ key: 'border_radius', label: t('Arrotondamento (px)'), type: 'border-radius' }),

    ...shadowField,
    ...borderFields(),
  ],
};
