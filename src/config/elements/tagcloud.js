import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared';
import { t } from '@/i18n';

/**
 * Tile Tag Cloud — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → taxonomy, custom_taxonomy, max_tags, orderby, order, show_count,
 *                   layout, columns (griglia), link_underline
 *   styleFields[] → bg, typography_preset, preset, textEffectsFields, gap (spaziatura layout),
 *                   tipografia (min_font, max_font, font_weight), colori (text_color +
 *                   hover_color, background_color + hover_background), border_radius,
 *                   padding, borderFields
 */
export default {
  type: 'tagcloud',
  name: t('Tag Cloud'),
  icon: 'dashicons-tag',
  category: 'dynamic',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    ...textEffectsDefaults,
    text_effect_target: 'all',
    taxonomy: 'post_tag',
    custom_taxonomy: '',
    min_font: '12',
    max_font: '28',
    max_tags: '30',
    orderby: 'name',
    order: 'ASC',
    show_count: false,
    separator: ' ',
    layout: 'cloud',
    columns: '3',
    text_color: '',
    hover_color: '',
    background_color: '',
    hover_background: '',
    border_radius: '16',
    padding: { top: 6, right: 14, bottom: 6, left: 14 },
    gap: '8',
    font_weight: '500',
    link_underline: false,
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'taxonomy', label: t('Tassonomia'), type: 'select', options: [
      { value: 'post_tag', label: t('Tag') },
      { value: 'category', label: t('Categorie') },
      { value: 'custom', label: t('Tassonomia personalizzata') },
    ]},
    { key: 'custom_taxonomy', label: t('Slug tassonomia'), type: 'text', placeholder: t('es. product_tag'),
      condition: { field: 'taxonomy', operator: '==', value: 'custom' } },
    { key: 'max_tags', label: t('Numero massimo tag'), type: 'range', min: 5, max: 100, step: 5 },
    { key: 'orderby', label: t('Ordina per'), type: 'select', options: [
      { value: 'name', label: t('Nome') },
      { value: 'count', label: t('Conteggio') },
    ]},
    { key: 'order', label: t('Ordine'), type: 'select', options: [
      { value: 'ASC', label: t('Crescente') },
      { value: 'DESC', label: t('Decrescente') },
    ]},
    { key: 'show_count', label: t('Mostra conteggio'), type: 'toggle' },

    { type: 'separator', label: t('Layout') },
    { key: 'layout', label: t('Layout'), type: 'select', options: [
      { value: 'cloud', label: t('Cloud (flex)') },
      { value: 'list', label: t('Lista verticale') },
      { value: 'grid', label: t('Griglia') },
    ]},
    { key: 'columns', label: t('Colonne (griglia)'), type: 'range', min: 2, max: 6, step: 1,
      condition: { field: 'layout', operator: '==', value: 'grid' } },
    { key: 'link_underline', label: t('Sottolineatura link'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'cloud-weighted',  label: t('Cloud Weighted') },
      { value: 'pills-uniform',   label: t('Pills Uniform') },
      { value: 'minimal-line',    label: t('Minimal Line') },
      { value: 'magazine-tags',   label: t('Magazine Tags') },
      { value: 'compact-chips',   label: t('Compact Chips') },
      { value: 'glass-pills',     label: t('Glass Pills') },
      { value: 'neon-tags',       label: t('Neon Tags') },
      { value: 'brutalist-stamp', label: t('Brutalist Stamp') },
      { value: 'gradient-tags',   label: t('Gradient Tags') },
      { value: 'sticky-notes',    label: t('Sticky Notes') },
      { value: 'retro-terminal',  label: t('Retro Terminal') },
      { value: 'tilt-3d',         label: t('3D Tilt') },
      { value: 'custom',          label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    ...textEffectsFields([
      { value: 'all', label: t('Tutti i tag') },
    ]),

    { type: 'separator', label: t('Spaziatura layout') },
    { key: 'gap', label: t('Gap (px)'), type: 'range', min: 0, max: 24, step: 2 },

    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Tag'),
      presetKey: 'typography_preset',
      keys: {
        weight:    'font_weight',
        color:     'text_color',
        colorHover: 'hover_color',
      },
    },
    { key: 'min_font', label: t('Dimensione min (px)'), type: 'range', min: 8, max: 24, step: 1 },
    { key: 'max_font', label: t('Dimensione max (px)'), type: 'range', min: 16, max: 60, step: 1 },

    { type: 'separator', label: t('Colori') },
    withHover({ key: 'background_color', label: t('Sfondo tag'),   type: 'color' }, { hoverKey: 'hover_background' }),

    { type: 'separator', label: t('Stile tag') },
    withHover({ key: 'border_radius', label: t('Arrotondamento (px)'), type: 'border-radius' }),
    { key: 'padding', label: t('Padding (px)'), type: 'spacing', max: 32 },

    ...borderFields(),
  ],
};
