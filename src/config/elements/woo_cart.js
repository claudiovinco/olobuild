
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile WooCommerce Carrello — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → toggle visibilita elementi (miniatura, coupon, totali)
 *   styleFields[] → preset, sfondo, tipografia, colori, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'woo_cart',
  name: t('Carrello'),
  icon: 'dashicons-cart',
  category: 'woocommerce',
  placeholder: t('Pagina carrello WooCommerce'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    show_thumbnail: true,
    show_coupon: true,
    show_totals: true,
    button_color: '',
    button_bg: '',
    text_color: '',
    heading_color: '',
    border_color: '',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Elementi visibili') },
    { key: 'show_thumbnail', label: t('Mostra miniatura prodotto'), type: 'toggle' },
    { key: 'show_coupon', label: t('Mostra campo coupon'), type: 'toggle' },
    { key: 'show_totals', label: t('Mostra totali carrello'), type: 'toggle' },
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
    { type: 'typography', label: t('Headings'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'heading_color',
      },
      sizeMin: 12, sizeMax: 60,
    },
    { type: 'typography', label: t('Testo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'text_color',
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

    { type: 'separator', label: t('Colori') },
    { key: 'border_color', label: t('Colore bordi'), type: 'color' },
    { key: 'button_bg', label: t('Sfondo pulsante'), type: 'color' },
    ...borderFields(),
  ],
};
