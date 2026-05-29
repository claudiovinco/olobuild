
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile WooCommerce Aggiungi al Carrello — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → testo pulsante, toggle visibilita (icona/quantita), stile quantita, icona
 *   styleFields[] → preset, sfondo, tipografia, stile/dimensione, border-radius, colori, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'woo_addtocart',
  name: t('Aggiungi al Carrello'),
  icon: 'dashicons-cart',
  category: 'woocommerce',
  placeholder: t('Pulsante aggiungi al carrello WooCommerce'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    button_text: 'Aggiungi al carrello',
    show_quantity: true,
    show_icon: true,
    icon: 'cart',
    style: 'filled',
    size: 'medium',
    full_width: false,
    bg_color: '',
    text_color: '',
    hover_bg: '',
    hover_text: '',
    border_radius: '6',
    quantity_style: 'input',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'button_text', label: t('Testo pulsante'), type: 'text' },
    { key: 'show_quantity', label: t('Mostra quantita'), type: 'toggle' },
    { key: 'show_icon', label: t('Mostra icona'), type: 'toggle' },
    { key: 'icon', label: t('Icona'), type: 'select', options: [
      { value: 'cart', label: t('Carrello') },
      { value: 'bag', label: t('Borsa') },
      { value: 'plus', label: t('Piu') },
    ], condition: { field: 'show_icon', value: true } },
    { key: 'quantity_style', label: t('Stile quantita'), type: 'select', options: [
      { value: 'input', label: t('Campo numerico') },
      { value: 'stepper', label: t('Stepper +/-') },
    ], condition: { field: 'show_quantity', value: true } },
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
    { key: 'style', label: t('Stile'), type: 'select', options: [
      { value: 'filled', label: t('Pieno') },
      { value: 'outline', label: t('Contorno') },
      { value: 'text', label: t('Solo testo') },
    ]},
    { key: 'size', label: t('Dimensione'), type: 'select', options: [
      { value: 'small', label: t('Piccolo') },
      { value: 'medium', label: t('Medio') },
      { value: 'large', label: t('Grande') },
    ]},
    { key: 'full_width', label: t('Larghezza piena'), type: 'toggle' },
    withHover({ key: 'border_radius', label: t('Arrotondamento (px)'), type: 'border-radius' }),

    { type: 'separator', label: t('Colori') },
    { key: 'bg_color', label: t('Sfondo'), type: 'color' },
    { key: 'text_color', label: t('Colore testo'), type: 'color' },
    { key: 'hover_bg', label: t('Sfondo hover'), type: 'color' },
    { key: 'hover_text', label: t('Colore testo hover'), type: 'color' },
    ...borderFields(),
  ],
};
