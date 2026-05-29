import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile WC Quick View — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → testo pulsante, toggle di visibilita, dimensione modale
 *   styleFields[] → preset, sfondo, tipografia, stile pulsante, colori, ombra, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'woo_quickview',
  name: t('WC Quick View'),
  icon: 'dashicons-visibility',
  category: 'woocommerce',
  placeholder: t('Quick View prodotto WooCommerce'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    button_text: 'Quick View',
    button_style: 'default',
    show_gallery: true,
    show_add_to_cart: true,
    show_description: true,
    modal_size: '',
    show_price: true,
    show_rating: true,
    show_sku: false,
    show_categories: true,
    overlay_color: 'rgba(0,0,0,0.5)',
    modal_bg: '',
    title_color: '',
    price_color: '',
    button_color: '',
    button_bg: '',
    close_color: '',
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Pulsante trigger') },
    { key: 'button_text', label: t('Testo pulsante'), type: 'text' },

    { type: 'separator', label: t('Contenuto modale') },
    { key: 'show_gallery', label: t('Mostra galleria'), type: 'toggle' },
    { key: 'show_add_to_cart', label: t('Mostra aggiungi al carrello'), type: 'toggle' },
    { key: 'show_description', label: t('Mostra descrizione'), type: 'toggle' },
    { key: 'show_price', label: t('Mostra prezzo'), type: 'toggle' },
    { key: 'show_rating', label: t('Mostra valutazione'), type: 'toggle' },
    { key: 'show_sku', label: t('Mostra SKU'), type: 'toggle' },
    { key: 'show_categories', label: t('Mostra categorie'), type: 'toggle' },

    { type: 'separator', label: t('Layout modale') },
    { key: 'modal_size', label: t('Dimensione modale'), type: 'select', options: [
      { value: '', label: t('Default') },
      { value: 'small', label: t('Piccolo') },
      { value: 'large', label: t('Grande') },
      { value: 'full', label: t('Full width') },
    ]},
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
    { type: 'typography', label: t('Titolo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'title_color',
      },
      sizeMin: 12, sizeMax: 60,
    },
    { type: 'typography', label: t('Prezzo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'price_color',
      },
      sizeMin: 12, sizeMax: 60,
    },
    { type: 'typography', label: t('Pulsante'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'button_color',
      },
      sizeMin: 12, sizeMax: 60,
    },

    { type: 'separator', label: t('Stile pulsante') },
    { key: 'button_style', label: t('Stile pulsante'), type: 'select', options: [
      { value: 'default', label: t('Default') },
      { value: 'outline', label: t('Outline') },
      { value: 'icon-only', label: t('Solo icona') },
      { value: 'text', label: t('Solo testo') },
    ]},

    { type: 'separator', label: t('Colori') },
    { key: 'overlay_color', label: t('Colore overlay'), type: 'color' },
    { key: 'modal_bg', label: t('Sfondo modale'), type: 'color' },
    { key: 'button_bg', label: t('Sfondo pulsante'), type: 'color' },
    { key: 'close_color', label: t('Colore icona chiudi'), type: 'color' },

    ...shadowField,
    ...borderFields(),
  ],
};
