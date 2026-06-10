import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile WC Prodotti Correlati — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → titolo intestazione, query (numero prodotti), colonne responsive, toggle visibilita
 *   styleFields[] → preset, sfondo, tipografia, gap, stile card, colori, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'woo_related',
  name: t('Prodotti Correlati'),
  icon: 'dashicons-networking',
  category: 'woocommerce',
  placeholder: t('Griglia prodotti correlati WooCommerce'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    posts_per_page: '4',
    columns: '4',
    show_image: true,
    show_title: true,
    show_price: true,
    card_style: 'shadow',
    gap: '24',
    columns_tablet: '2',
    columns_mobile: '1',
    title_color: '',
    price_color: '',
    heading_text: 'Prodotti correlati',
    show_heading: true,
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Intestazione') },
    { key: 'show_heading', label: t('Mostra titolo sezione'), type: 'toggle' },
    { key: 'heading_text', label: t('Testo titolo'), type: 'text', condition: { field: 'show_heading', value: true } },

    { type: 'separator', label: t('Query') },
    { key: 'posts_per_page', label: t('Numero prodotti'), type: 'range', min: 1, max: 12, step: 1 },

    { type: 'separator', label: t('Colonne responsive') },
    { key: 'columns', label: t('Colonne'), type: 'range', min: 1, max: 6, step: 1 },
    { key: 'columns_tablet', label: t('Colonne tablet'), type: 'range', min: 1, max: 4, step: 1 },
    { key: 'columns_mobile', label: t('Colonne mobile'), type: 'range', min: 1, max: 2, step: 1 },

    { type: 'separator', label: t('Elementi visibili') },
    { key: 'show_image', label: t('Mostra immagine'), type: 'toggle' },
    { key: 'show_title', label: t('Mostra titolo'), type: 'toggle' },
    { key: 'show_price', label: t('Mostra prezzo'), type: 'toggle' },
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

    { type: 'separator', label: t('Layout grafico') },
    { key: 'gap', label: t('Gap (px)'), type: 'range', min: 0, max: 48, step: 4 },
    { key: 'card_style', label: t('Stile card'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'shadow', label: t('Ombra') },
      { value: 'border', label: t('Bordo') },
    ]},

    ...borderFields(),
  ],
};
