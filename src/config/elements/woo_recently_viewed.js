import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared';
import { shadowField } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile WC Visti di Recente — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → titolo intestazione, tag heading, limit query, colonne responsive, toggle visibilita, testo lista vuota
 *   styleFields[] → preset, sfondo, tipografia, text effects, gap, ratio immagine, card style, hover, colori, ombra, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'woo_recently_viewed',
  name: t('WC Visti di Recente'),
  icon: 'dashicons-clock',
  category: 'woocommerce',
  placeholder: t('Prodotti visti di recente WooCommerce'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    columns: '4',
    limit: '8',
    card_style: 'default',
    show_price: true,
    heading: 'Visti di recente',
    show_image: true,
    show_rating: false,
    show_add_to_cart: true,
    image_ratio: '4-3',
    gap: '24',
    hover_effect: 'zoom',
    heading_tag: 'h3',
    heading_color: '',
    title_color: '',
    price_color: '',
    button_color: '',
    button_bg: '',
    empty_text: 'Nessun prodotto visualizzato di recente',
    empty_color: '',
    columns_tablet: '2',
    columns_mobile: '1',
    shadow: 'none',
    ...textEffectsDefaults,
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Intestazione') },
    { key: 'heading', label: t('Titolo sezione'), type: 'text' },
    { key: 'heading_tag', label: t('Tag heading'), type: 'select', options: [
      { value: 'h2', label: t('H2') },
      { value: 'h3', label: t('H3') },
      { value: 'h4', label: t('H4') },
      { value: 'div', label: t('DIV') },
    ]},

    { type: 'separator', label: t('Query') },
    { key: 'limit', label: t('Numero massimo prodotti'), type: 'range', min: 1, max: 24, step: 1 },

    { type: 'separator', label: t('Colonne responsive') },
    { key: 'columns', label: t('Colonne'), type: 'range', min: 1, max: 6, step: 1 },
    { key: 'columns_tablet', label: t('Colonne tablet'), type: 'range', min: 1, max: 4, step: 1 },
    { key: 'columns_mobile', label: t('Colonne mobile'), type: 'range', min: 1, max: 2, step: 1 },

    { type: 'separator', label: t('Elementi visibili') },
    { key: 'show_image', label: t('Mostra immagine'), type: 'toggle' },
    { key: 'show_price', label: t('Mostra prezzo'), type: 'toggle' },
    { key: 'show_rating', label: t('Mostra valutazione'), type: 'toggle' },
    { key: 'show_add_to_cart', label: t('Mostra pulsante carrello'), type: 'toggle' },
    { key: 'empty_text', label: t('Testo lista vuota'), type: 'text' },
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

    ...textEffectsFields([ { value: 'heading', label: t('Solo Titolo') } ]),

    { type: 'separator', label: t('Layout grafico') },
    { key: 'gap', label: t('Gap (px)'), type: 'range', min: 0, max: 48, step: 4 },
    { key: 'image_ratio', label: t('Proporzione immagine'), type: 'select', options: [
      { value: '1-1', label: t('1:1 Quadrato') },
      { value: '4-3', label: '4:3' },
      { value: '3-4', label: t('3:4 Verticale') },
      { value: '16-9', label: '16:9' },
      { value: 'auto', label: t('Automatico') },
    ]},
    { key: 'card_style', label: t('Stile card'), type: 'select', options: [
      { value: 'default', label: t('Default') },
      { value: 'shadow', label: t('Ombra') },
      { value: 'border', label: t('Bordo') },
    ]},
    { key: 'hover_effect', label: t('Effetto hover'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'zoom', label: t('Zoom') },
      { value: 'shadow', label: t('Ombra') },
    ]},

    { type: 'separator', label: t('Colori') },
    { key: 'heading_color', label: t('Colore intestazione'), type: 'color' },
    { key: 'title_color', label: t('Colore titolo'), type: 'color' },
    { key: 'price_color', label: t('Colore prezzo'), type: 'color' },
    { key: 'button_color', label: t('Colore testo pulsante'), type: 'color' },
    { key: 'button_bg', label: t('Sfondo pulsante'), type: 'color' },
    { key: 'empty_color', label: t('Colore testo vuoto'), type: 'color' },

    ...shadowField,
    ...borderFields(),
  ],
};
