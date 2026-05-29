import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared';
import { shadowField } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile TOC (Indice contenuti) — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → title (testo), max_depth (sorgente dati: quali heading includere), list_style (struttura),
 *                   comportamento (sticky, highlight_active, smooth_scroll)
 *   styleFields[] → preset, bg, typography_preset, textEffectsFields, colori (text/link/title),
 *                   font_size, indent, shadow, borderFields
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'toc',
  name: t('Indice contenuti'),
  icon: 'dashicons-list-view',
  category: 'navigation',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    title: t('Sommario'),
    max_depth: '3',
    list_style: 'numbered',
    text_color: '',
    link_color: '',
    title_color: '',
    font_size: '15',
    indent: '20',
    sticky: false,
    highlight_active: true,
    smooth_scroll: true,
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
    { type: 'separator', label: t('Contenuto') },
    { key: 'title', label: t('Titolo'), type: 'text' },
    { key: 'max_depth', label: t('Profondità max'), type: 'select', options: [
      { value: '1', label: t('Solo H1') },
      { value: '2', label: t('Fino a H2') },
      { value: '3', label: t('Fino a H3') },
      { value: '4', label: t('Fino a H4') },
      { value: '5', label: t('Fino a H5') },
      { value: '6', label: t('Fino a H6') },
    ]},
    { key: 'list_style', label: t('Stile lista'), type: 'select', options: [
      { value: 'numbered', label: t('Numerata') },
      { value: 'bullets', label: t('Pallini') },
      { value: 'none', label: t('Senza') },
    ]},

    { type: 'separator', label: t('Comportamento') },
    { key: 'sticky', label: t('Sticky (fisso nello scroll)'), type: 'toggle' },
    { key: 'highlight_active', label: t('Evidenzia sezione attiva'), type: 'toggle' },
    { key: 'smooth_scroll', label: t('Scroll fluido'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean',    label: t('Modern Clean') },
      { value: 'minimal-mono',    label: t('Minimal Mono') },
      { value: 'magazine-numbered', label: t('Magazine Numbered') },
      { value: 'sticky-rail',     label: t('Sticky Rail') },
      { value: 'floating-card',   label: t('Floating Card') },
      { value: 'glass-tier',      label: t('Glass Tier') },
      { value: 'neon-list',       label: t('Neon List') },
      { value: 'brutalist-block', label: t('Brutalist Block') },
      { value: 'gradient-flow',   label: t('Gradient Flow') },
      { value: 'sticky-notes',    label: t('Sticky Notes') },
      { value: 'retro-terminal',  label: t('Retro Terminal') },
      { value: 'tilt-cards',      label: t('Tilt Cards') },
      { value: 'custom',          label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    ...textEffectsFields([ { value: 'title', label: t('Solo Titolo') } ]),

    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Indice'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:  'font_size',
        color: 'text_color',
      },
      sizeMin: 12, sizeMax: 22,
    },

    { type: 'separator', label: t('Colori') },
    { key: 'link_color', label: t('Colore link'), type: 'color' },
    { key: 'title_color', label: t('Colore titolo'), type: 'color' },

    { type: 'separator', label: t('Dimensioni') },
    { key: 'indent', label: t('Indentazione sotto-livelli (px)'), type: 'range', min: 0, max: 40, step: 4 },

    ...shadowField,
    ...borderFields(),
  ],
};
