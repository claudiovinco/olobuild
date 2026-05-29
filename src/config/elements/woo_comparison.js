import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile WC Confronto Prodotti — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → numero massimo prodotti, toggle di visibilita righe
 *   styleFields[] → preset, sfondo, tipografia, colori intestazione, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'woo_comparison',
  name: t('WC Confronto Prodotti'),
  icon: 'dashicons-columns',
  category: 'woocommerce',
  placeholder: t('Tabella confronto prodotti WooCommerce'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    max_products: 4,
    show_image: true,
    show_price: true,
    show_rating: true,
    show_stock: true,
    show_sku: true,
    show_description: true,
    show_attributes: true,
    show_add_to_cart: true,
    header_bg: '#F9FAFB',
    header_color: '',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'max_products', label: t('Massimo prodotti'), type: 'range', min: 2, max: 6 },

    { type: 'separator', label: t('Visibilita righe') },
    { key: 'show_image', label: t('Mostra immagine'), type: 'toggle' },
    { key: 'show_price', label: t('Mostra prezzo'), type: 'toggle' },
    { key: 'show_rating', label: t('Mostra valutazione'), type: 'toggle' },
    { key: 'show_stock', label: t('Mostra disponibilita'), type: 'toggle' },
    { key: 'show_sku', label: t('Mostra SKU'), type: 'toggle' },
    { key: 'show_description', label: t('Mostra descrizione'), type: 'toggle' },
    { key: 'show_attributes', label: t('Mostra attributi'), type: 'toggle' },
    { key: 'show_add_to_cart', label: t('Mostra aggiungi al carrello'), type: 'toggle' },
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
    { type: 'typography', label: t('Cells'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'header_color',
      },
      sizeMin: 12, sizeMax: 60,
    },

    { type: 'separator', label: t('Colori') },
    { key: 'header_bg', label: t('Sfondo intestazione'), type: 'color' },

    ...borderFields(),
  ],
};
