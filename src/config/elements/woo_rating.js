import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile WC Valutazione Prodotto — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → toggle conteggio/media (sorgente dati WooCommerce)
 *   styleFields[] → preset, sfondo, tipografia, dimensioni stelle/testo, colori, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'woo_rating',
  name: t('Valutazione Prodotto'),
  icon: 'dashicons-star-filled',
  category: 'woocommerce',
  placeholder: t('Valutazione stelle prodotto WooCommerce'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    show_count: true,
    show_average: true,
    star_color: '',
    empty_star_color: '',
    text_color: '',
    star_size: '20',
    text_size: '14',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Elementi visibili') },
    { key: 'show_count', label: t('Mostra numero recensioni'), type: 'toggle' },
    { key: 'show_average', label: t('Mostra media'), type: 'toggle' },
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
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Dimensioni') },
    { key: 'star_size', label: t('Dimensione stelle (px)'), type: 'range', min: 12, max: 48, step: 2 },
    { key: 'text_size', label: t('Dimensione testo (px)'), type: 'range', min: 10, max: 24, step: 1 },

    { type: 'separator', label: t('Colori') },
    { key: 'star_color', label: t('Colore stelle piene'), type: 'color' },
    { key: 'empty_star_color', label: t('Colore stelle vuote'), type: 'color' },
    { key: 'text_color', label: t('Colore testo'), type: 'color' },

    ...borderFields(),
  ],
};
