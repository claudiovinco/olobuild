
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile WC Prezzo Prodotto — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → toggle regular/sale/suffisso, testi prefisso/suffisso
 *   styleFields[] → preset, sfondo, tipografia, dimensione, peso, allineamento, colori, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'woo_price',
  name: t('Prezzo Prodotto'),
  icon: 'dashicons-tag',
  category: 'woocommerce',
  placeholder: t('Prezzo prodotto WooCommerce'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    show_regular: true,
    show_sale: true,
    show_suffix: false,
    price_color: '',
    sale_color: '',
    regular_color: '',
    font_size: '24',
    font_weight: '700',
    text_align: 'left',
    prefix: '',
    suffix: '',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'show_regular', label: t('Mostra prezzo originale'), type: 'toggle' },
    { key: 'show_sale', label: t('Mostra prezzo scontato'), type: 'toggle' },
    { key: 'show_suffix', label: t('Mostra suffisso prezzo'), type: 'toggle' },
    { key: 'prefix', label: t('Prefisso'), type: 'text', placeholder: t('es. A partire da') },
    { key: 'suffix', label: t('Suffisso'), type: 'text', placeholder: t('es. + IVA') },
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
    { type: 'typography', label: t('Prezzo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:   'font_size',
        weight: 'font_weight',
        color:  'price_color',
      },
      sizeMin: 12, sizeMax: 72,
    },

    { type: 'separator', label: t('Stile') },
    { key: 'text_align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},

    { type: 'separator', label: t('Colori') },
    { key: 'sale_color', label: t('Colore saldo'), type: 'color' },
    { key: 'regular_color', label: t('Colore prezzo barrato'), type: 'color' },
    ...borderFields(),
  ],
};
