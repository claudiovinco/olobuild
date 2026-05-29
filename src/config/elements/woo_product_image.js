
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile WC Immagine Prodotto — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → toggle galleria/lightbox/zoom, posizione galleria
 *   styleFields[] → preset, sfondo, tipografia, proporzione, raggio bordi, dimensioni miniature, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'woo_product_image',
  name: t('Immagine Prodotto'),
  icon: 'dashicons-format-image',
  category: 'woocommerce',
  placeholder: t('Immagine prodotto WooCommerce con galleria'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    show_gallery: true,
    gallery_position: 'bottom',
    lightbox: false,
    zoom_on_hover: true,
    image_ratio: '1-1',
    border_radius: '8',
    thumb_size: '64',
    thumb_gap: '8',
    thumb_border_radius: '4',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'show_gallery', label: t('Mostra miniature galleria'), type: 'toggle' },
    { key: 'gallery_position', label: t('Posizione galleria'), type: 'select', options: [
      { value: 'bottom', label: t('Sotto') },
      { value: 'left', label: t('Sinistra') },
    ]},
    { key: 'lightbox', label: t('Lightbox'), type: 'toggle' },
    { key: 'zoom_on_hover', label: t('Zoom al passaggio mouse'), type: 'toggle' },
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

    { type: 'separator', label: t('Layout') },
    { key: 'image_ratio', label: t('Proporzione immagine'), type: 'select', options: [
      { value: '1-1', label: t('1:1 Quadrato') },
      { value: '4-3', label: '4:3' },
      { value: '3-4', label: t('3:4 Verticale') },
      { value: '3-2', label: '3:2' },
      { value: '16-9', label: '16:9' },
      { value: 'auto', label: t('Automatico') },
    ]},
    withHover({ key: 'border_radius', label: t('Bordo arrotondato (px)'), type: 'border-radius' }),

    { type: 'separator', label: t('Miniature') },
    { key: 'thumb_size', label: t('Dimensione miniature (px)'), type: 'range', min: 40, max: 120, step: 4 },
    { key: 'thumb_gap', label: t('Gap miniature (px)'), type: 'range', min: 4, max: 16, step: 2 },
    withHover({ key: 'thumb_border_radius', label: t('Bordo miniature (px)'), type: 'border-radius' }),
    ...borderFields(),
  ],
};
