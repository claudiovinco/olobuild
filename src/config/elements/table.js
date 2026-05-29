import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Table — split CONTENUTO/STILE.
 *   fields[]      → struttura (has_header/striped/bordered/hover/compact/first_col_bold/responsive_mode)
 *   styleFields[] → preset, bg, typo, colori, shadow, border
 */
export default {
  type: 'table',
  name: t('Tabella'),
  icon: 'dashicons-editor-table',
  category: 'text',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    table_data: [
      ['Funzionalità', 'Base', 'Pro'],
      ['Spazio', '5 GB', '50 GB'],
      ['Utenti', '1', '10'],
      ['Supporto', 'Email', 'Prioritario'],
    ],
    has_header: true,
    striped: true,
    bordered: true,
    hover_effect: true,
    compact: false,
    first_col_bold: false,
    col_alignments: [],
    responsive_mode: 'scroll',
    header_bg: '',
    header_text_color: '',
    text_color: '',
    border_color: '',
    even_row_bg: '',
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    {
      key: '_table_info',
      type: 'html',
      html: '<div style="font-size:11px;color:#9CA3AF;margin:-4px 0 8px;line-height:1.4">Clicca sulle celle nel canvas per modificarle. Usa i pulsanti + e − per aggiungere o rimuovere righe e colonne.</div>',
    },

    { type: 'separator', label: t('Struttura') },
    { key: 'has_header', label: t('Riga intestazione'), type: 'toggle' },
    { key: 'striped', label: t('Righe alternate'), type: 'toggle' },
    { key: 'bordered', label: t('Con bordi'), type: 'toggle' },
    { key: 'hover_effect', label: t('Effetto hover'), type: 'toggle' },
    { key: 'compact', label: t('Compatto'), type: 'toggle' },
    { key: 'first_col_bold', label: t('Prima colonna in grassetto'), type: 'toggle' },
    { key: 'responsive_mode', label: t('Responsive'), type: 'select', options: [
      { value: 'scroll', label: t('Scroll orizzontale') },
      { value: 'stack', label: t('Stack verticale') },
    ]},
  ],

  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean',     label: t('Modern Clean') },
      { value: 'magazine-editorial', label: t('Magazine Editorial') },
      { value: 'minimal-line',     label: t('Minimal Line') },
      { value: 'striped-classic',  label: t('Striped Classic') },
      { value: 'compact-data',     label: t('Compact Data') },
      { value: 'glass-tier',       label: t('Glass Tier') },
      { value: 'neon-grid',        label: t('Neon Grid') },
      { value: 'brutalist-stamp',  label: t('Brutalist Stamp') },
      { value: 'gradient-soft',    label: t('Gradient Soft') },
      { value: 'sticker-fun',      label: t('Sticker Fun') },
      { value: 'retro-terminal',   label: t('Retro Terminal') },
      { value: 'tilt-card',        label: t('Tilt Card') },
      { value: 'custom',           label: t('Personalizzato') },
    ]},
    { type: 'separator', label: t('Tipografia') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Header'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'header_text_color',
      },
      sizeMin: 12, sizeMax: 60,
    },
    { type: 'typography', label: t('Celle'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'text_color',
      },
      sizeMin: 12, sizeMax: 60,
    },

    { type: 'separator', label: t('Colori') },
    { key: 'header_bg', label: t('Sfondo intestazione'), type: 'color' },
    { key: 'border_color', label: t('Colore bordi'), type: 'color' },
    { key: 'even_row_bg', label: t('Sfondo righe pari'), type: 'color' },

    ...shadowField,
    ...borderFields(),
  ],
};
