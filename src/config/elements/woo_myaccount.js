
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile WooCommerce My Account — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → layout dashboard (default/sidebar/tabs)
 *   styleFields[] → preset, sfondo, tipografia, dimensione avatar, border-radius, colori sidebar/contenuto, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'woo_myaccount',
  name: t('WC My Account'),
  icon: 'dashicons-admin-users',
  category: 'woocommerce',
  placeholder: t('Dashboard account WooCommerce'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    layout: 'default',
    sidebar_bg: '',
    sidebar_active_bg: '',
    sidebar_active_color: '',
    sidebar_color: '',
    content_bg: '',
    heading_color: '',
    text_color: '',
    link_color: '',
    button_bg: '',
    button_color: '',
    border_color: '',
    border_radius: '8',
    avatar_size: '64',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'layout', label: t('Layout'), type: 'select', options: [
      { value: 'default', label: t('Default') },
      { value: 'sidebar', label: t('Con sidebar') },
      { value: 'tabs', label: t('Tab') },
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
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Stile') },
    { key: 'avatar_size', label: t('Dimensione avatar (px)'), type: 'range', min: 32, max: 128, step: 8 },
    withHover({ key: 'border_radius', label: t('Arrotondamento (px)'), type: 'border-radius' }),

    { type: 'separator', label: t('Colori sidebar') },
    { key: 'sidebar_bg', label: t('Sfondo sidebar'), type: 'color' },
    { key: 'sidebar_color', label: t('Colore testo'), type: 'color' },
    { key: 'sidebar_active_bg', label: t('Sfondo attivo'), type: 'color' },
    { key: 'sidebar_active_color', label: t('Colore attivo'), type: 'color' },

    { type: 'separator', label: t('Colori contenuto') },
    { key: 'content_bg', label: t('Sfondo contenuto'), type: 'color' },
    { key: 'heading_color', label: t('Colore intestazioni'), type: 'color' },
    { key: 'text_color', label: t('Colore testo'), type: 'color' },
    { key: 'link_color', label: t('Colore link'), type: 'color' },
    { key: 'border_color', label: t('Colore bordi'), type: 'color' },
    { key: 'button_bg', label: t('Sfondo pulsante'), type: 'color' },
    { key: 'button_color', label: t('Colore testo pulsante'), type: 'color' },
    ...borderFields(),
  ],
};
