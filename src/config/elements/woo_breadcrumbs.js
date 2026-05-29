
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile WooCommerce Breadcrumbs — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → carattere separatore, allineamento
 *   styleFields[] → preset, sfondo, tipografia, dimensione font, colori testo/link, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'woo_breadcrumbs',
  name: t('Breadcrumbs WooCommerce'),
  icon: 'dashicons-admin-links',
  category: 'woocommerce',
  placeholder: t('Breadcrumbs navigazione WooCommerce'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    separator: '/',
    text_color: '',
    link_color: '',
    font_size: '14',
    alignment: 'left',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'separator', label: t('Separatore'), type: 'select', options: [
      { value: '/', label: t('/') },
      { value: '>', label: t('>') },
      { value: '-', label: '-' },
      { value: '>>', label: t('>>') },
    ]},
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
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
    { type: 'typography', label: t('Links'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:  'font_size',
        color: 'text_color',
      },
      sizeMin: 10, sizeMax: 24,
    },

    { type: 'separator', label: t('Stile') },
    { key: 'link_color', label: t('Colore link'), type: 'color' },
    ...borderFields(),
  ],
};
