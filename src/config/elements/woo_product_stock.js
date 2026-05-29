
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile WC Stock Prodotto — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → toggle quantità/icona, soglia scorte basse (configurazione comportamentale)
 *   styleFields[] → preset, sfondo, tipografia, dimensione, peso, allineamento, dimensione icona, colori, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'woo_product_stock',
  name: t('Stock Prodotto'),
  icon: 'dashicons-archive',
  category: 'woocommerce',
  placeholder: t('Stato disponibilita prodotto WooCommerce'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    show_quantity: true,
    show_icon: true,
    in_stock_color: '',
    out_of_stock_color: '',
    low_stock_color: '',
    low_stock_threshold: '5',
    font_size: '14',
    font_weight: '500',
    text_align: 'left',
    icon_size: '10',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'show_quantity', label: t('Mostra quantita'), type: 'toggle' },
    { key: 'show_icon', label: t('Mostra indicatore'), type: 'toggle' },
    { key: 'low_stock_threshold', label: t('Soglia scorte basse'), type: 'range', min: 1, max: 50, step: 1 },
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
    { type: 'typography', label: t('Stock'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:   'font_size',
        weight: 'font_weight',
      },
      sizeMin: 11, sizeMax: 24,
    },

    { type: 'separator', label: t('Stile') },
    { key: 'text_align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},
    { key: 'icon_size', label: t('Dimensione indicatore (px)'), type: 'range', min: 6, max: 16, step: 2 },

    { type: 'separator', label: t('Colori') },
    { key: 'in_stock_color', label: t('Colore disponibile'), type: 'color' },
    { key: 'out_of_stock_color', label: t('Colore non disponibile'), type: 'color' },
    { key: 'low_stock_color', label: t('Colore scorte basse'), type: 'color' },
    ...borderFields(),
  ],
};
