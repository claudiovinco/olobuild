
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile WooCommerce Categorie — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → query (orderby, parent_only, hide_empty),
 *                   toggle visibilita (image/count/description),
 *                   tag titolo (HTML semantico)
 *   styleFields[] → preset, sfondo, tipografia, columns/gap/image_ratio, hover effect,
 *                   border-radius, overlay/colore, colori, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'woo_categories',
  name: t('Categorie Prodotti'),
  icon: 'dashicons-category',
  category: 'woocommerce',
  placeholder: t('Griglia categorie WooCommerce'),
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    columns: '4',
    show_image: true,
    show_count: true,
    show_description: false,
    hide_empty: true,
    orderby: 'name',
    parent_only: false,
    overlay: true,
    overlay_color: 'rgba(0,0,0,0.4)',
    text_color: '',
    title_tag: 'h3',
    gap: '24',
    image_ratio: '1-1',
    border_radius: '8',
    hover_effect: 'zoom',
    columns_tablet: '2',
    columns_mobile: '1',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Query') },
    { key: 'orderby', label: t('Ordina per'), type: 'select', options: [
      { value: 'name', label: t('Nome') },
      { value: 'count', label: t('Conteggio prodotti') },
      { value: 'id', label: t('ID') },
      { value: 'slug', label: t('Slug') },
    ]},
    { key: 'parent_only', label: t('Solo categorie padre'), type: 'toggle' },
    { key: 'hide_empty', label: t('Nascondi vuote'), type: 'toggle' },

    { type: 'separator', label: t('Elementi visibili') },
    { key: 'show_image', label: t('Mostra immagine'), type: 'toggle' },
    { key: 'show_count', label: t('Mostra conteggio prodotti'), type: 'toggle' },
    { key: 'show_description', label: t('Mostra descrizione'), type: 'toggle' },
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
    { type: 'typography', label: t('Labels'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        tag:   'title_tag',
        color: 'text_color',
      },
      sizeMin: 12, sizeMax: 60,
    },

    { type: 'separator', label: t('Layout') },
    { key: 'columns', label: t('Colonne'), type: 'range', min: 1, max: 6, step: 1 },
    { key: 'columns_tablet', label: t('Colonne tablet'), type: 'range', min: 1, max: 4, step: 1 },
    { key: 'columns_mobile', label: t('Colonne mobile'), type: 'range', min: 1, max: 2, step: 1 },
    { key: 'gap', label: t('Gap (px)'), type: 'range', min: 0, max: 48, step: 4 },
    { key: 'image_ratio', label: t('Proporzione immagine'), type: 'select', options: [
      { value: '1-1', label: t('1:1 Quadrato') },
      { value: '4-3', label: '4:3' },
      { value: '3-4', label: t('3:4 Verticale') },
      { value: '16-9', label: '16:9' },
    ]},
    { key: 'hover_effect', label: t('Effetto hover'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'zoom', label: t('Zoom') },
      { value: 'darken', label: t('Scurisci') },
    ]},
    withHover({ key: 'border_radius', label: t('Arrotondamento (px)'), type: 'border-radius' }),

    { type: 'separator', label: t('Colori') },
    { key: 'overlay', label: t('Overlay'), type: 'toggle' },
    { key: 'overlay_color', label: t('Colore overlay'), type: 'color',
      condition: { field: 'overlay', value: true } },
    ...borderFields(),
  ],
};
