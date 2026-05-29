
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Woo Notices — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → toggle visibilità (successo/errore/info)
 *   styleFields[] → preset, sfondo, tipografia, raggio, dimensione testo, bordo
 */
export default {
  type: 'woo_notices',
  name: t('Notifiche WooCommerce'),
  icon: 'dashicons-info',
  category: 'woocommerce',
  placeholder: t('Notifiche successo, errore e info WooCommerce'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    show_success: true,
    show_error: true,
    show_info: true,
    border_radius: '8',
    font_size: '14',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'show_success', label: t('Mostra successo'), type: 'toggle' },
    { key: 'show_error', label: t('Mostra errore'), type: 'toggle' },
    { key: 'show_info', label: t('Mostra info'), type: 'toggle' },
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
    { type: 'typography', label: t('Testo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size: 'font_size',
      },
      sizeMin: 10, sizeMax: 24,
    },

    { type: 'separator', label: t('Stile') },
    withHover({ key: 'border_radius', label: t('Arrotondamento (px)'), type: 'border-radius' }),
    ...borderFields(),
  ],
};
