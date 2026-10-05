import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Woo Product Filter — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → toggle filtri visibili, range prezzo, toggle pulsante e testo
 *   styleFields[] → preset, sfondo, tipografia, stile filtro, colori, ombra, bordo
 */
export default {
  type: 'woo_product_filter',
  name: t('WC Filtro Prodotti'),
  icon: 'dashicons-filter',
  category: 'woocommerce',
  placeholder: t('Filtri prodotti WooCommerce'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    show_price_range: true,
    show_categories: true,
    show_attributes: true,
    show_stock: true,
    filter_style: 'sidebar',
    price_range_min: '0',
    price_range_max: '500',
    price_step: '10',
    show_count: true,
    collapsible: true,
    apply_button: true,
    button_text: 'Applica filtri',
    button_bg: '',
    button_color: '',
    label_color: '',
    active_color: '',
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // Come nasce dalla palette: il pannello dei filtri del negozio (prezzo, categorie col conteggio,
  // disponibilità) con «Applica filtri» nel colore del sito. Scioglie il disaccordo sul testo del
  // pulsante fra PHP («Filtra») e config.
  // Attributi vuoti = tutti quelli del negozio (il default PHP, pa_color,pa_size, li fissava).
  partenza: {
    button_text: t('Applica filtri'),
    attributes: '',
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Filtri visibili') },
    { key: 'show_price_range', label: t('Filtro prezzo'), type: 'toggle' },
    { key: 'show_categories', label: t('Filtro categorie'), type: 'toggle' },
    { key: 'show_attributes', label: t('Filtro attributi'), type: 'toggle' },
    { key: 'attributes', label: t('Attributi'), type: 'text', placeholder: t('es. color, size'),
      description: t('Gli attributi WooCommerce da offrire, separati da virgola (color oppure pa_color). Vuoto = tutti gli attributi del negozio.'),
      condition: { field: 'show_attributes', value: true } },
    { key: 'show_stock', label: t('Filtro disponibilita'), type: 'toggle' },
    { key: 'show_count', label: t('Mostra conteggio prodotti'), type: 'toggle' },

    // «Prezzo minimo/massimo» (price_range_min/max) non ci sono più: il cursore prende sempre
    // il prezzo più basso e più alto dei prodotti del negozio, e quei due campi non agivano.
    { type: 'separator', label: t('Range prezzo') },
    { key: 'price_step', label: t('Step prezzo'), type: 'range', min: 1, max: 100, step: 1,
      condition: { field: 'show_price_range', value: true } },

    { type: 'separator', label: t('Pulsante') },
    { key: 'apply_button', label: t('Mostra pulsante applica'), type: 'toggle',
      description: t('Spento: i filtri si applicano a ogni scelta, senza premere il pulsante.') },
    { key: 'button_text', label: t('Testo pulsante'), type: 'text',
      condition: { field: 'apply_button', value: true } },
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
    { type: 'typography', label: t('Labels'),
      responsiveKeys: [],
      keys: {
        color: 'label_color',
      },
      sizeMin: 12, sizeMax: 60,
    },

    { type: 'separator', label: t('Stile') },
    { key: 'filter_style', label: t('Stile filtro'), type: 'select', options: [
      { value: 'sidebar', label: t('Sidebar') },
      { value: 'horizontal', label: t('Orizzontale') },
      { value: 'dropdown', label: t('Dropdown') },
    ]},
    // A tendina le sezioni si aprono sempre una alla volta: l'interruttore lì non agisce.
    { key: 'collapsible', label: t('Sezioni richiudibili'), type: 'toggle',
      condition: { field: 'filter_style', op: 'neq', value: 'dropdown' } },

    { type: 'separator', label: t('Colori') },
    { key: 'active_color', label: t('Colore filtro attivo'), type: 'color' },
    { key: 'button_color', label: t('Colore testo pulsante'), type: 'color',
      condition: { field: 'apply_button', value: true } },
    { key: 'button_bg', label: t('Sfondo pulsante'), type: 'color',
      condition: { field: 'apply_button', value: true } },

    ...shadowField,
    ...borderFields(),
  ],
};
