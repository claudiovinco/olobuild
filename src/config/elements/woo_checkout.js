
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile WooCommerce Checkout — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → layout, stile form, toggle visibilita note ordine
 *   styleFields[] → preset, sfondo, tipografia, colori, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'woo_checkout',
  name: t('Checkout'),
  icon: 'dashicons-clipboard',
  category: 'woocommerce',
  placeholder: t('Pagina checkout WooCommerce'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    layout: 'two_columns',
    show_order_notes: true,
    accent_color: '',
    text_color: '',
    form_style: 'modern',
    heading_color: '',
    border_color: '',
    button_color: '',
    button_bg: '',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Layout') },
    { key: 'layout', label: t('Layout'), type: 'select', options: [
      { value: 'one_column', label: t('Una colonna') },
      { value: 'two_columns', label: t('Due colonne') },
    ]},
    { key: 'form_style', label: t('Stile form'), type: 'select', options: [
      { value: 'modern', label: t('Moderno') },
      { value: 'classic', label: t('Classico') },
    ]},

    { type: 'separator', label: t('Opzioni') },
    { key: 'show_order_notes', label: t('Mostra note ordine'), type: 'toggle' },
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
    { key: 'accent_color', label: t('Colore accento'), type: 'color' },
    { key: 'border_color', label: t('Colore bordi'), type: 'color' },
    { key: 'button_bg', label: t('Sfondo pulsante'), type: 'color' },
    ...borderFields(),
  ],
};
