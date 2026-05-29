
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile WC Titolo Prodotto — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → tag HTML, link al prodotto
 *   styleFields[] → preset, sfondo, tipografia, dimensione, peso, allineamento, colori, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'woo_product_title',
  name: t('Titolo Prodotto'),
  icon: 'dashicons-heading',
  category: 'woocommerce',
  placeholder: t('Titolo del prodotto WooCommerce'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    tag: 'h1',
    text_align: 'left',
    color: '',
    font_size: '32',
    font_weight: '700',
    line_height: '1.2',
    link_to_product: false,
    link_color_hover: '',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'tag', label: t('Tag HTML'), type: 'select', options: [
      { value: 'h1', label: t('H1') },
      { value: 'h2', label: t('H2') },
      { value: 'h3', label: t('H3') },
      { value: 'h4', label: t('H4') },
      { value: 'h5', label: t('H5') },
      { value: 'h6', label: t('H6') },
    ]},
    { key: 'link_to_product', label: t('Link al prodotto'), type: 'toggle' },
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

    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Titolo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size', 'lineHeight'],
      keys: {
        size:       'font_size',
        weight:     'font_weight',
        lineHeight: 'line_height',
        color:      'color',
      },
      sizeMin: 12, sizeMax: 96,
    },

    { type: 'separator', label: t('Stile') },
    { key: 'text_align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},

    { type: 'separator', label: t('Colori') },
    { key: 'link_color_hover', label: t('Colore link hover'), type: 'color' },
    ...borderFields(),
  ],
};
