
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile WC Tab Prodotto — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → toggle tab visibili (descrizione, info aggiuntive, recensioni)
 *   styleFields[] → preset, sfondo, tipografia, stile tab, colori, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'woo_product_tabs',
  name: t('Tab Prodotto'),
  icon: 'dashicons-index-card',
  category: 'woocommerce',
  placeholder: t('Tab prodotto WooCommerce (descrizione, info, recensioni)'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    show_description: true,
    show_additional: true,
    show_reviews: true,
    tab_style: 'underline',
    active_color: '',
    text_color: '',
    border_color: '',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Tab visibili') },
    { key: 'show_description', label: t('Mostra Descrizione'), type: 'toggle' },
    { key: 'show_additional', label: t('Mostra Info aggiuntive'), type: 'toggle' },
    { key: 'show_reviews', label: t('Mostra Recensioni'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean',    label: t('Modern Clean') },
      { value: 'minimal-mono',    label: t('Minimal Mono') },
      { value: 'magazine-bold',   label: t('Magazine Bold') },
      { value: 'editorial-serif', label: t('Editorial Serif') },
      { value: 'compact-inline',  label: t('Compact Inline') },
      { value: 'glass-frosted',   label: t('Glass Frosted') },
      { value: 'neon-glow',       label: t('Neon Glow') },
      { value: 'brutalist-stamp', label: t('Brutalist Stamp') },
      { value: 'gradient-aurora', label: t('Gradient Aurora') },
      { value: 'sticker-fun',     label: t('Sticker Fun') },
      { value: 'retro-terminal',  label: t('Retro Terminal') },
      { value: 'tilt-3d',         label: t('3D Tilt') },
      { value: 'custom',          label: t('Personalizzato') },
    ] },
    { type: 'separator', label: t('Tipografia') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Tabs'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'text_color',
      },
      sizeMin: 12, sizeMax: 60,
    },

    { type: 'separator', label: t('Stile') },
    { key: 'tab_style', label: t('Stile tab'), type: 'select', options: [
      { value: 'underline', label: t('Sottolineatura') },
      { value: 'pills', label: t('Pillole') },
      { value: 'boxed', label: t('Riquadri') },
    ]},

    { type: 'separator', label: t('Colori') },
    { key: 'active_color', label: t('Colore tab attiva'), type: 'color' },
    { key: 'border_color', label: t('Colore bordo'), type: 'color' },
    ...borderFields(),
  ],
};
