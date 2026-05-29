
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Woo Product Navigation — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → toggle thumbnail/etichetta, testi etichette, stile separatore
 *   styleFields[] → preset, sfondo, tipografia, colori (con hover), bordo
 */
export default {
  type: 'woo_product_navigation',
  name: t('Navigazione Prodotti'),
  icon: 'dashicons-leftright',
  category: 'woocommerce',
  placeholder: t('Link prodotto precedente / successivo'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    show_thumbnail: true,
    show_label: true,
    label_prev: 'Prodotto precedente',
    label_next: 'Prodotto successivo',
    text_color: '',
    hover_color: '',
    separator_style: 'line',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'show_thumbnail', label: t('Mostra miniatura'), type: 'toggle' },
    { key: 'show_label', label: t('Mostra etichetta'), type: 'toggle' },
    { key: 'label_prev', label: t('Etichetta precedente'), type: 'text' },
    { key: 'label_next', label: t('Etichetta successivo'), type: 'text' },
    { key: 'separator_style', label: t('Separatore'), type: 'select', options: [
      { value: 'line', label: t('Linea') },
      { value: 'dotted', label: t('Puntinato') },
      { value: 'none', label: t('Nessuno') },
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
        color: 'text_color',
      },
      sizeMin: 12, sizeMax: 60,
    },

    { type: 'separator', label: t('Colori') },
    { key: 'hover_color', label: t('Colore hover'), type: 'color' },
    ...borderFields(),
  ],
};
