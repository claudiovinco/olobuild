
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile WooCommerce Mini Carrello — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → comportamento + toggle visibilita (style/icona, count/total/dropdown)
 *   styleFields[] → preset, sfondo, tipografia, dimensioni, colori, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'woo_minicart',
  name: t('Mini Carrello'),
  icon: 'dashicons-cart',
  category: 'woocommerce',
  placeholder: t('Mini carrello WooCommerce'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    style: 'icon-text',
    icon: 'cart',
    show_count: true,
    show_total: true,
    dropdown: true,
    icon_size: '24',
    text_color: '',
    icon_color: '',
    badge_bg: '',
    badge_color: '',
    dropdown_width: '320',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'style', label: t('Stile'), type: 'select', options: [
      { value: 'icon', label: t('Solo icona') },
      { value: 'icon-text', label: t('Icona + testo') },
      { value: 'text', label: t('Solo testo') },
    ]},
    { key: 'icon', label: t('Icona'), type: 'select', options: [
      { value: 'cart', label: t('Carrello') },
      { value: 'bag', label: t('Borsa') },
      { value: 'basket', label: t('Cestino') },
    ]},
    { key: 'show_count', label: t('Mostra conteggio'), type: 'toggle' },
    { key: 'show_total', label: t('Mostra totale'), type: 'toggle' },
    { key: 'dropdown', label: t('Dropdown al hover'), type: 'toggle' },
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
    { type: 'typography', label: t('Testo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'text_color',
      },
      sizeMin: 12, sizeMax: 60,
    },

    { type: 'separator', label: t('Dimensioni') },
    { key: 'icon_size', label: t('Dimensione icona (px)'), type: 'range', min: 16, max: 48, step: 2 },
    { key: 'dropdown_width', label: t('Larghezza dropdown (px)'), type: 'range', min: 240, max: 480, step: 20,
      condition: { field: 'dropdown', value: true } },

    { type: 'separator', label: t('Colori') },
    { key: 'icon_color', label: t('Colore icona'), type: 'color' },
    { key: 'badge_bg', label: t('Sfondo badge'), type: 'color' },
    { key: 'badge_color', label: t('Colore badge'), type: 'color' },
    ...borderFields(),
  ],
};
