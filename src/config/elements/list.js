import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared';
import { shadowField } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile List — split CONTENUTO/STILE.
 *   fields[]      → items (text+link+icon), icon_default
 *   styleFields[] → preset, bg, typo, text-effects, colore icona/testo, allineamento, spaziature, padding, shadow, border
 */
export default {
  type: 'list',
  name: t('Lista'),
  icon: 'dashicons-editor-ul',
  category: 'text',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    items: [
      { text: t('Funzionalità uno'), icon: 'check' },
      { text: t('Funzionalità due'), icon: 'check' },
      { text: t('Funzionalità tre'), icon: 'check' },
    ],
    icon_default: 'check',
    icon_color: '',
    text_color: '',
    text_align: 'left',
    spacing: '12',
    icon_size: '18',
    icon_gap: '10',
    tile_padding: { top: 16, right: 16, bottom: 16, left: 16 },
    shadow: 'none',
    ...textEffectsDefaults,
    text_effect_target: 'text',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'items', label: t('Elementi'), type: 'content-items',
      itemFields: [
        { key: 'text', label: t('Testo'), type: 'text' },
        { key: 'link', label: t('Link'), type: 'link', placeholder: t('https://...') },
        { key: 'icon', label: t('Icona'), type: 'select', options: [
          { value: 'check', label: t('✓ Spunta') },
          { value: 'arrow', label: t('→ Freccia') },
          { value: 'star', label: t('★ Stella') },
          { value: 'dot', label: t('● Punto') },
          { value: 'number', label: t('1. Numero') },
          { value: 'x', label: t('✕ Croce') },
          { value: 'heart', label: t('♥ Cuore') },
          { value: 'bolt', label: t('Fulmine') },
          { value: 'info', label: t('ℹ Info') },
          { value: 'warning', label: t('⚠ Attenzione') },
        ]},
      ],
      newItemDefaults: { text: t('Nuovo elemento'), icon: '' },
    },
    { key: 'icon_default', label: t('Icona predefinita'), type: 'select', description: t('Per elementi senza icona'), options: [
      { value: 'check', label: t('✓ Spunta') },
      { value: 'arrow', label: t('→ Freccia') },
      { value: 'star', label: t('★ Stella') },
      { value: 'dot', label: t('● Punto') },
      { value: 'number', label: t('1. Numero') },
      { value: 'x', label: t('✕ Croce') },
      { value: 'heart', label: t('♥ Cuore') },
      { value: 'bolt', label: t('Fulmine') },
      { value: 'info', label: t('ℹ Info') },
      { value: 'warning', label: t('⚠ Attenzione') },
    ]},
  ],

  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean',     label: t('Modern Clean') },
      { value: 'minimal-mono',     label: t('Minimal Mono') },
      { value: 'magazine-numbered', label: t('Magazine Numbered') },
      { value: 'editorial-serif',  label: t('Editorial Serif') },
      { value: 'compact-inline',   label: t('Compact Inline') },
      { value: 'glass-rows',       label: t('Glass Rows') },
      { value: 'neon-checks',      label: t('Neon Checks') },
      { value: 'brutalist-block',  label: t('Brutalist Block') },
      { value: 'gradient-bullets', label: t('Gradient Bullets') },
      { value: 'sticky-notes',     label: t('Sticky Notes') },
      { value: 'retro-terminal',   label: t('Retro Terminal') },
      { value: 'tilt-cards',       label: t('Tilt Cards') },
      { value: 'custom',           label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    ...textEffectsFields([ { value: 'text', label: t('Solo Testo') } ]),

    { type: 'separator', label: t('Colori') },
    { key: 'icon_color', label: t('Colore icona'), type: 'color' },
    { key: 'text_color', label: t('Colore testo'), type: 'color' },

    { type: 'separator', label: t('Layout') },
    { key: 'text_align', label: t('Allineamento testo'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centrato') },
      { value: 'right', label: t('Destra') },
      { value: 'justify', label: t('Giustificato') },
    ]},
    { key: 'spacing', label: t('Spaziatura (px)'), type: 'range', min: 4, max: 32, step: 2 },
    { key: 'icon_size', label: t('Dim. icona (px)'), type: 'range', min: 14, max: 32, step: 2 },
    { key: 'icon_gap', label: t('Spazio icona-testo (px)'), type: 'range', min: 0, max: 32, step: 2 },
    { key: 'tile_padding', label: t('Padding (px)'), type: 'spacing', max: 48 },

    ...shadowField,
    ...borderFields(),
  ],
};
