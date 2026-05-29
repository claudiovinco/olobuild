
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Woo Checkout Multi-step — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → etichette step, stile step, toggle riepilogo ordine
 *   styleFields[] → preset, sfondo, tipografia, colori, raggio card, bordo
 */
export default {
  type: 'woo_checkout_multistep',
  name: t('Checkout Multi-step WC'),
  icon: 'dashicons-cart',
  category: 'woocommerce',
  placeholder: t('Checkout multi-step WooCommerce'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    step_labels: 'Dati,Spedizione,Pagamento,Conferma',
    step_style: 'progress',
    accent_color: '',
    step_bg: '',
    active_color: '',
    text_color: '',
    card_radius: 12,
    show_order_review: true,
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'step_labels', label: t('Etichette step (virgola)'), type: 'text' },
    { key: 'step_style', label: t('Stile step'), type: 'select', options: [
      { value: 'progress', label: t('Barra progresso') },
      { value: 'tabs', label: t('Tab') },
      { value: 'numbered', label: t('Numeri') },
    ]},
    { key: 'show_order_review', label: t('Mostra riepilogo ordine'), type: 'toggle' },
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
    { type: 'typography', label: t('Steps'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'text_color',
      },
      sizeMin: 12, sizeMax: 60,
    },

    { type: 'separator', label: t('Colori') },
    { key: 'accent_color', label: t('Colore accento'), type: 'color' },
    { key: 'active_color', label: t('Colore step attivo'), type: 'color' },
    { key: 'step_bg', label: t('Sfondo step'), type: 'color' },
    withHover({ key: 'card_radius', label: t('Arrotondamento (px)'), type: 'border-radius' }),
    ...borderFields(),
  ],
};
