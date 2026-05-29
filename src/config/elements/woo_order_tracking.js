import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared';
import { t } from '@/i18n';

/**
 * Tile Woo Order Tracking — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → titolo, tag titolo, stile form
 *   styleFields[] → preset, sfondo, tipografia, effetti testo, colori, bordo
 */
export default {
  type: 'woo_order_tracking',
  name: t('Tracciamento Ordine'),
  icon: 'dashicons-search',
  category: 'woocommerce',
  placeholder: t('Modulo tracciamento ordine WooCommerce'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    title: t('Traccia il tuo ordine'),
    title_tag: 'h2',
    accent_color: '',
    text_color: '',
    button_color: '',
    button_bg: '',
    form_style: 'modern',
    ...textEffectsDefaults,
    text_effect_target: 'title',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'title', label: t('Titolo'), type: 'text' },
    { key: 'form_style', label: t('Stile form'), type: 'select', options: [
      { value: 'modern', label: t('Moderno') },
      { value: 'classic', label: t('Classico') },
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
    ...textEffectsFields([ { value: 'title', label: t('Solo Titolo') } ]),

    { type: 'separator', label: t('Tipografia') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Label'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        tag:   'title_tag',
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
    { key: 'button_bg', label: t('Sfondo pulsante'), type: 'color' },
    ...borderFields(),
  ],
};
