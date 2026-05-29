
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile WC Meta Prodotto — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → toggle SKU/categorie/tag, separatore inline
 *   styleFields[] → preset, sfondo, tipografia, layout, dimensione, peso etichetta, colori, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'woo_product_meta',
  name: t('Meta Prodotto'),
  icon: 'dashicons-info-outline',
  category: 'woocommerce',
  placeholder: t('SKU, categorie e tag del prodotto WooCommerce'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    show_sku: true,
    show_categories: true,
    show_tags: true,
    layout: 'stacked',
    separator: '|',
    text_color: '',
    label_color: '',
    link_color: '',
    font_size: '14',
    label_weight: '600',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'show_sku', label: t('Mostra SKU'), type: 'toggle' },
    { key: 'show_categories', label: t('Mostra categorie'), type: 'toggle' },
    { key: 'show_tags', label: t('Mostra tag'), type: 'toggle' },
    { key: 'separator', label: t('Separatore (inline)'), type: 'text', placeholder: t('|') },
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
    { key: 'layout', label: t('Disposizione'), type: 'select', options: [
      { value: 'stacked', label: t('In colonna') },
      { value: 'inline', label: t('In riga') },
    ]},

    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Meta'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:  'font_size',
        color: 'text_color',
      },
      sizeMin: 11, sizeMax: 24,
    },

    { type: 'separator', label: t('Stile') },
    { key: 'label_weight', label: t('Peso etichetta'), type: 'select', options: [
      { value: '400', label: t('Normale') },
      { value: '500', label: t('Medium') },
      { value: '600', label: t('Semi-bold') },
      { value: '700', label: t('Bold') },
    ]},

    { type: 'separator', label: t('Colori') },
    { key: 'label_color', label: t('Colore etichette'), type: 'color' },
    { key: 'link_color', label: t('Colore link'), type: 'color' },
    ...borderFields(),
  ],
};
