
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile WC Descrizione Prodotto — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → tipo contenuto (full/short), max righe (clamp comportamentale)
 *   styleFields[] → preset, sfondo, tipografia, dimensione, line-height, allineamento, colore, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'woo_product_description',
  name: t('Descrizione Prodotto'),
  icon: 'dashicons-text-page',
  category: 'woocommerce',
  placeholder: t('Descrizione prodotto WooCommerce'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    content_type: 'full',
    text_color: '',
    font_size: '16',
    line_height: '1.6',
    text_align: 'left',
    max_lines: '0',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'content_type', label: t('Tipo contenuto'), type: 'select', options: [
      { value: 'full', label: t('Descrizione completa') },
      { value: 'short', label: t('Descrizione breve') },
    ]},
    { key: 'max_lines', label: t('Righe massime (0 = tutte)'), type: 'range', min: 0, max: 20, step: 1 },
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
    { type: 'typography', label: t('Descrizione'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size', 'lineHeight'],
      keys: {
        size:       'font_size',
        lineHeight: 'line_height',
        color:      'text_color',
      },
      sizeMin: 12, sizeMax: 32,
    },

    { type: 'separator', label: t('Stile') },
    { key: 'text_align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
      { value: 'justify', label: t('Giustificato') },
    ]},
    ...borderFields(),
  ],
};
