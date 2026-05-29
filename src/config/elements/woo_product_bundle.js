import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Woo Product Bundle — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → ID prodotti, titolo bundle, tag, sconto, toggle elementi visibili, testo pulsante
 *   styleFields[] → preset, sfondo, tipografia, layout, colonne, stile card, colori, ombra, bordo
 */
export default {
  type: 'woo_product_bundle',
  name: t('WC Bundle Prodotti'),
  icon: 'dashicons-archive',
  category: 'woocommerce',
  placeholder: t('Bundle prodotti WooCommerce'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    product_ids: '',
    discount_percent: '10',
    bundle_title: 'Pacchetto risparmio',
    show_savings: true,
    button_text: 'Aggiungi tutto al carrello',
    show_images: true,
    show_prices: true,
    show_descriptions: false,
    layout: 'grid',
    columns: '3',
    gap: '24',
    card_style: 'border',
    title_tag: 'h3',
    title_color: '',
    price_color: '',
    savings_color: '',
    bundle_bg: '',
    button_color: '',
    button_bg: '',
    divider_color: '',
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Prodotti bundle') },
    { key: 'product_ids', label: t('ID prodotti (virgola)'), type: 'text', placeholder: t('es. 12,34,56') },
    { key: 'bundle_title', label: t('Titolo bundle'), type: 'text' },
    { key: 'title_tag', label: t('Tag titolo'), type: 'select', options: [
      { value: 'h2', label: t('H2') },
      { value: 'h3', label: t('H3') },
      { value: 'h4', label: t('H4') },
      { value: 'div', label: t('DIV') },
    ]},

    { type: 'separator', label: t('Sconto') },
    { key: 'discount_percent', label: t('Sconto (%)'), type: 'range', min: 0, max: 50, step: 1 },
    { key: 'show_savings', label: t('Mostra risparmio'), type: 'toggle' },

    { type: 'separator', label: t('Elementi visibili') },
    { key: 'show_images', label: t('Mostra immagini'), type: 'toggle' },
    { key: 'show_prices', label: t('Mostra prezzi singoli'), type: 'toggle' },
    { key: 'show_descriptions', label: t('Mostra descrizioni'), type: 'toggle' },

    { type: 'separator', label: t('Pulsante') },
    { key: 'button_text', label: t('Testo pulsante'), type: 'text' },
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

    { type: 'separator', label: t('Layout') },
    { key: 'layout', label: t('Layout'), type: 'select', options: [
      { value: 'grid', label: t('Griglia') },
      { value: 'list', label: t('Lista') },
      { value: 'compact', label: t('Compatto') },
    ]},
    { key: 'columns', label: t('Colonne (griglia)'), type: 'range', min: 1, max: 6, step: 1 },
    { key: 'gap', label: t('Gap (px)'), type: 'range', min: 0, max: 48, step: 4 },
    { key: 'card_style', label: t('Stile card'), type: 'select', options: [
      { value: 'default', label: t('Default') },
      { value: 'shadow', label: t('Ombra') },
      { value: 'border', label: t('Bordo') },
    ]},

    { type: 'separator', label: t('Colori') },
    { key: 'title_color', label: t('Colore titolo'), type: 'color' },
    { key: 'price_color', label: t('Colore prezzo'), type: 'color' },
    { key: 'savings_color', label: t('Colore risparmio'), type: 'color' },
    { key: 'bundle_bg', label: t('Sfondo bundle'), type: 'color' },
    { key: 'button_color', label: t('Colore testo pulsante'), type: 'color' },
    { key: 'button_bg', label: t('Sfondo pulsante'), type: 'color' },
    { key: 'divider_color', label: t('Colore divisore'), type: 'color' },

    ...shadowField,
    ...borderFields(),
  ],
};
