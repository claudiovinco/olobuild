import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared';
import { shadowField } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile IconList — split CONTENUTO/STILE.
 *   fields[]      → items (icon+text+link+color), layout (vertical/horizontal), divider toggle
 *   styleFields[] → preset, bg, typo, text-effects, icona aspetto (color/size/shape/bg), testo aspetto, gap, divider color, shadow, border
 */
export default {
  type: 'iconlist',
  name: t('Lista icone'),
  icon: 'dashicons-list-view',
  category: 'text',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    items: [
      { id: 'il-1', icon: 'check', text: t('Prima voce della lista'), color: '' },
      { id: 'il-2', icon: 'check', text: t('Seconda voce della lista'), color: '' },
      { id: 'il-3', icon: 'check', text: t('Terza voce della lista'), color: '' },
    ],
    icon_color: '',
    icon_size: '20',
    text_color: '',
    text_size: '16',
    text_align: 'left',
    gap: '12',
    icon_shape: 'none',
    icon_bg_color: '',
    divider: false,
    divider_color: '',
    layout: 'vertical',
    shadow: 'none',
    ...textEffectsDefaults,
    text_effect_target: 'text',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'items', label: t('Voci lista'), type: 'content-items',
      itemFields: [
        { key: 'icon', label: t('Icona'), type: 'icon' },
        { key: 'text', label: t('Testo'), type: 'text' },
        { key: 'link', label: t('Link'), type: 'link', placeholder: t('https://...') },
        { key: 'color', label: t('Colore icona (override)'), type: 'color' },
      ],
      newItemDefaults: { icon: 'check', text: t('Nuova voce'), color: '', link: '' },
    },

    { type: 'separator', label: t('Layout') },
    { key: 'layout', label: t('Orientamento'), type: 'select', options: [
      { value: 'vertical', label: t('Verticale') },
      { value: 'horizontal', label: t('Orizzontale') },
    ]},
    { key: 'divider', label: t('Separatore tra voci'), type: 'toggle' },
  ],

  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean',    label: t('Modern Clean') },
      { value: 'minimal-mono',    label: t('Minimal Mono') },
      { value: 'magazine-numbered', label: t('Magazine Numbered') },
      { value: 'card-rows',       label: t('Card Rows') },
      { value: 'compact-inline',  label: t('Compact Inline') },
      { value: 'glass-rows',      label: t('Glass Rows') },
      { value: 'neon-checks',     label: t('Neon Checks') },
      { value: 'brutalist-block', label: t('Brutalist Block') },
      { value: 'gradient-bullets', label: t('Gradient Bullets') },
      { value: 'sticker-list',    label: t('Sticker List') },
      { value: 'retro-checklist', label: t('Retro Checklist') },
      { value: 'tilt-cards',      label: t('Tilt Cards') },
      { value: 'custom',          label: t('Personalizzato') },
    ]},
    ...textEffectsFields([ { value: 'text', label: t('Solo Testo') } ]),

    { type: 'separator', label: t('Tipografia') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Testo items'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:  'text_size',
        color: 'text_color',
      },
      sizeMin: 12, sizeMax: 28,
    },

    { type: 'separator', label: t('Stile icone') },
    { key: 'icon_color', label: t('Colore icone'), type: 'color' },
    { key: 'icon_size', label: t('Dimensione icone (px)'), type: 'range', min: 12, max: 48, step: 2 },
    { key: 'icon_shape', label: t('Sfondo icona'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'circle', label: t('Cerchio') },
      { value: 'square', label: t('Quadrato') },
      { value: 'rounded', label: t('Arrotondato') },
    ]},
    { key: 'icon_bg_color', label: t('Colore sfondo icona'), type: 'color',
      condition: { field: 'icon_shape', operator: '!=', value: 'none' } },

    { type: 'separator', label: t('Allineamento') },
    { key: 'text_align', label: t('Allineamento testo'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centrato') },
      { value: 'right', label: t('Destra') },
      { value: 'justify', label: t('Giustificato') },
    ]},

    { type: 'separator', label: t('Spaziatura & separatore') },
    { key: 'gap', label: t('Spazio tra voci (px)'), type: 'range', min: 4, max: 32, step: 2 },
    { key: 'divider_color', label: t('Colore separatore'), type: 'color',
      condition: { field: 'divider', operator: '==', value: true } },

    ...shadowField,
    ...borderFields(),
  ],
};
