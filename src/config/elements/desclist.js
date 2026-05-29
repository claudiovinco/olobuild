import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, textEffectsFields, textEffectsDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile DescList — split CONTENUTO/STILE.
 *   fields[]      → items (term+definition+link+icon), layout (stacked/inline/grid), show_icon
 *   styleFields[] → preset, bg, typo, text-effects, icona aspetto, tipografia, allineamento, colori, separator/striped, shadow, border
 */
export default {
  type: 'desclist',
  name: t('Lista descrittiva'),
  icon: 'dashicons-editor-justify',
  category: 'text',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    items: [
      { id: 'dl-1', term: 'Framework', definition: 'Vue.js 3 con Composition API', icon: 'code' },
      { id: 'dl-2', term: 'Linguaggio', definition: 'PHP 7.4+ con WordPress', icon: 'server' },
      { id: 'dl-3', term: 'Build Tool', definition: 'Vite 5', icon: 'bolt' },
    ],
    layout: 'stacked',
    show_icon: true,
    icon_color: '',
    icon_size: '20',
    term_color: '',
    term_font_size: '15',
    term_font_weight: '600',
    definition_color: '',
    definition_font_size: '14',
    text_align: 'left',
    separator: true,
    border_color: '',
    spacing: '16',
    striped: false,
    striped_color: '',
    shadow: 'none',
    ...textEffectsDefaults,
    text_effect_target: 'definition',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'items', label: t('Elementi'), type: 'content-items', supportsDynamic: true,
      itemFields: [
        { key: 'term', label: t('Termine'), type: 'text' },
        { key: 'definition', label: t('Definizione'), type: 'textarea' },
        { key: 'link', label: t('Link'), type: 'link', placeholder: t('https://...') },
        { key: 'icon', label: t('Icona'), type: 'icon' },
      ],
      newItemDefaults: { term: 'Nuovo termine', definition: 'Definizione.', icon: '' },
      itemLabel: 'Voce',
    },

    { type: 'separator', label: t('Layout') },
    { key: 'layout', label: t('Layout'), type: 'select', options: [
      { value: 'stacked', label: t('Impilato') },
      { value: 'inline', label: t('In linea') },
      { value: 'grid', label: t('Griglia') },
    ]},
    { key: 'show_icon', label: t('Mostra icona'), type: 'toggle' },
    { key: 'separator', label: t('Separatore tra voci'), type: 'toggle' },
    { key: 'striped', label: t('Righe alternate'), type: 'toggle' },
  ],

  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean',     label: t('Modern Clean') },
      { value: 'minimal-mono',     label: t('Minimal Mono') },
      { value: 'magazine-spec',    label: t('Magazine Spec Sheet') },
      { value: 'editorial-serif',  label: t('Editorial Serif') },
      { value: 'compact-inline',   label: t('Compact Inline') },
      { value: 'glass-rows',       label: t('Glass Rows') },
      { value: 'neon-tech',        label: t('Neon Tech') },
      { value: 'brutalist-block',  label: t('Brutalist Block') },
      { value: 'gradient-soft',    label: t('Gradient Soft') },
      { value: 'sticky-notes',     label: t('Sticky Notes') },
      { value: 'retro-terminal',   label: t('Retro Terminal') },
      { value: 'tilt-cards',       label: t('Tilt Cards') },
      { value: 'custom',           label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Icona — Aspetto') },
    { key: 'icon_size', label: t('Dim. icona (px)'), type: 'range', min: 14, max: 32, step: 2 },

    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Termine'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:   'term_font_size',
        weight: 'term_font_weight',
        color:  'term_color',
      },
      sizeMin: 12, sizeMax: 24, sizeStep: 1,
    },
    { type: 'typography', label: t('Definizione'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:  'definition_font_size',
        color: 'definition_color',
      },
      sizeMin: 12, sizeMax: 20, sizeStep: 1,
    },
    { type: 'typography', label: t('Icona'),
      presetKey: 'typography_preset',
      keys: {
        color: 'icon_color',
      },
    },
    { key: 'text_align', label: t('Allineamento testo'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centrato') },
      { value: 'right', label: t('Destra') },
      { value: 'justify', label: t('Giustificato') },
    ]},
    { key: 'spacing', label: t('Spaziatura tra voci (px)'), type: 'range', min: 4, max: 32, step: 2 },

    { type: 'separator', label: t('Colori') },
    { key: 'border_color', label: t('Colore bordo separatore'), type: 'color' },
    { key: 'striped_color', label: t('Colore riga alternata'), type: 'color' },

    ...textEffectsFields([
      { value: 'term', label: t('Solo termine') },
      { value: 'definition', label: t('Solo definizione') },
      { value: 'both', label: t('Termine e definizione') },
    ]),

    ...shadowField,
    ...borderFields(),
  ],
};
