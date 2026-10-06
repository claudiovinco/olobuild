
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

  // Come nasce dalla palette (in una scheda prodotto): il prezzo grande, quello pieno barrato
  // quando è in saldo, lo scontato nel colore del sito (non nel rosso d'errore) e «IVA inclusa».
  partenza: {
    font_size: '28',
    sale_color: 'var(--olo-color-primary)',
    show_suffix: true,
    suffix: t('IVA inclusa'),
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    // Il prodotto a cui si riferisce: senza, la tile funzionava solo nella scheda di un prodotto.
    { type: 'separator', label: t('Prodotto') },
    { key: 'product_id', label: t('ID prodotto'), type: 'number', min: 0, step: 1,
      description: t("Il prodotto a cui si riferisce la tile, per usarla fuori dalla sua scheda (una pagina di lancio). L'ID si legge in Prodotti, passando sul nome. Vuoto o 0 = il prodotto della pagina.") },

    { type: 'separator', label: t('Prezzo') },
    { key: 'show_regular', label: t('Mostra prezzo originale'), type: 'toggle' },
    { key: 'show_sale', label: t('Mostra prezzo scontato'), type: 'toggle' },
    { key: 'show_suffix', label: t('Mostra suffisso prezzo'), type: 'toggle' },
    { key: 'prefix', label: t('Prefisso'), type: 'text', placeholder: t('es. A partire da') },
    { key: 'suffix', label: t('Suffisso'), type: 'text', placeholder: t('es. + IVA'),
      condition: { field: 'show_suffix', value: true } },
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
      responsiveKeys: [],
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
