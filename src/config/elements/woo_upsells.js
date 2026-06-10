import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile WC Prodotti Suggeriti (upsell) — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → titolo sezione, colonne responsive, toggle visibilita
 *   styleFields[] → preset, sfondo, tipografia, gap, stile card, colori, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'woo_upsells',
  name: t('Prodotti Suggeriti'),
  icon: 'dashicons-star-filled',
  category: 'woocommerce',
  placeholder: t('Griglia prodotti suggeriti (upsell) WooCommerce'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    columns: '4',
    show_image: true,
    show_title: true,
    show_price: true,
    card_style: 'shadow',
    gap: '24',
    title_text: 'Ti potrebbe interessare',
    show_heading: true,
    columns_tablet: '2',
    columns_mobile: '1',
    title_color: '',
    price_color: '',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Intestazione') },
    { key: 'show_heading', label: t('Mostra titolo sezione'), type: 'toggle' },
    { key: 'title_text', label: t('Testo titolo'), type: 'text', condition: { field: 'show_heading', value: true } },

    { type: 'separator', label: t('Colonne responsive') },
    { key: 'columns', label: t('Colonne'), type: 'range', min: 1, max: 6, step: 1 },
    { key: 'columns_tablet', label: t('Colonne tablet'), type: 'range', min: 1, max: 4, step: 1 },
    { key: 'columns_mobile', label: t('Colonne mobile'), type: 'range', min: 1, max: 2, step: 1 },

    { type: 'separator', label: t('Elementi visibili') },
    { key: 'show_image', label: t('Mostra immagine'), type: 'toggle' },
    { key: 'show_title', label: t('Mostra titolo'), type: 'toggle' },
    { key: 'show_price', label: t('Mostra prezzo'), type: 'toggle' },
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

    { type: 'separator', label: t('Layout grafico') },
    { key: 'gap', label: t('Gap (px)'), type: 'range', min: 0, max: 48, step: 4 },
    { key: 'card_style', label: t('Stile card'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'shadow', label: t('Ombra') },
      { value: 'border', label: t('Bordo') },
    ]},

    { type: 'separator', label: t('Colori') },
    { key: 'title_color', label: t('Colore titolo'), type: 'color' },
    { key: 'price_color', label: t('Colore prezzo'), type: 'color' },

    ...borderFields(),
  ],
};
