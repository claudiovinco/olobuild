import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile WC Wishlist — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → icona, stile trigger, toggle visibilita, testo vuoto, colonne responsive
 *   styleFields[] → preset, sfondo, tipografia, stile card, gap, colori, ombra, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'woo_wishlist',
  name: t('WC Wishlist'),
  icon: 'dashicons-heart',
  category: 'woocommerce',
  placeholder: t('Wishlist WooCommerce'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    icon: 'heart',
    style: 'icon',
    show_count: true,
    columns: '4',
    empty_text: 'La wishlist è vuota',
    show_price: true,
    show_add_to_cart: true,
    show_remove: true,
    card_style: 'default',
    gap: '24',
    icon_color: '',
    icon_color_active: '',
    title_color: '',
    price_color: '',
    empty_color: '',
    button_color: '',
    button_bg: '',
    columns_tablet: '2',
    columns_mobile: '1',
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // Come nasce dalla palette: il cuore nel colore del sito col contatore dei prodotti salvati.
  // Scioglie i disaccordi fra PHP e config (stile del pulsante, testo della lista vuota, card, gap).
  partenza: {
    style: 'icon',
    empty_text: t('La tua wishlist è vuota'),
    card_style: 'shadow',
    gap: '24',
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Pulsante wishlist') },
    { key: 'icon', label: t('Icona'), type: 'select', options: [
      { value: 'heart', label: t('Cuore') },
      { value: 'star', label: t('Stella') },
      { value: 'bookmark', label: t('Segnalibro') },
    ]},
    { key: 'show_count', label: t('Mostra conteggio'), type: 'toggle' },

    // show_grid il PHP lo leggeva da sempre, ma nessun controllo lo accendeva: la lista dei
    // prodotti salvati non si poteva mostrare, e le voci qui sotto non agivano mai.
    { type: 'separator', label: t('Lista dei salvati') },
    { key: 'show_grid', label: t('Mostra lista'), type: 'toggle' },

    { type: 'separator', label: t('Elementi visibili'), condition: { field: 'show_grid', op: 'notEmpty' } },
    { key: 'show_price', label: t('Mostra prezzo'), type: 'toggle' },
    { key: 'show_add_to_cart', label: t('Mostra aggiungi al carrello'), type: 'toggle' },
    { key: 'show_remove', label: t('Mostra rimuovi'), type: 'toggle' },
    { key: 'empty_text', label: t('Testo wishlist vuota'), type: 'text' },
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

    { type: 'separator', label: t('Card'), condition: { field: 'show_grid', op: 'notEmpty' } },
    { key: 'gap', label: t('Gap'), type: 'range', min: 0, max: 48, step: 4 },
    { key: 'card_style', label: t('Stile card'), type: 'select', options: [
      { value: 'default', label: t('Default') },
      { value: 'shadow', label: t('Ombra') },
      { value: 'border', label: t('Bordo') },
    ]},

    { type: 'separator', label: t('Colori') },
    { key: 'icon_color', label: t('Colore icona'), type: 'color' },
    { key: 'icon_color_active', label: t('Colore icona attiva'), type: 'color' },
    { key: 'title_color', label: t('Colore titolo'), type: 'color', condition: { field: 'show_grid', op: 'notEmpty' } },
    { key: 'price_color', label: t('Colore prezzo'), type: 'color', condition: { field: 'show_grid', op: 'notEmpty' } },
    { key: 'empty_color', label: t('Colore testo vuoto'), type: 'color', condition: { field: 'show_grid', op: 'notEmpty' } },
    // Il pulsante «Aggiungi al carrello» delle card e lo stile «Pulsante» del cuore.
    { key: 'button_color', label: t('Colore testo pulsante'), type: 'color', show: (s) => !!s.show_grid || s.style === 'button' },
    { key: 'button_bg', label: t('Sfondo pulsante'), type: 'color', show: (s) => !!s.show_grid || s.style === 'button' },

    ...shadowField,
    ...borderFields(),
    { type: 'separator', label: t('Pulsante wishlist') },
    // 'outline', il default storico del PHP, si è sempre disegnato come «Solo icona».
    { key: 'style', label: t('Stile'), type: 'select', options: [
      { value: 'icon', label: t('Solo icona') },
      { value: 'icon-text', label: t('Icona + testo') },
      { value: 'button', label: t('Pulsante') },
    ], valoriStorici: { outline: 'icon' } },
    { type: 'separator', label: t('Disposizione'), condition: { field: 'show_grid', op: 'notEmpty' } },
    { key: 'columns', label: t('Colonne'), type: 'range', min: 1, max: 6, step: 1 },
    { key: 'columns_tablet', label: t('Colonne tablet'), type: 'range', min: 1, max: 4, step: 1 },
    { key: 'columns_mobile', label: t('Colonne mobile'), type: 'range', min: 1, max: 2, step: 1 },
  ],
};
