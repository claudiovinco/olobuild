import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile PostNavigation — split CONTENUTO/STILE.
 *   fields[]      → show_thumbnail, show_label, prev_label, next_label, show_title, title_length, stesso termine tassonomia
 *   styleFields[] → preset, bg, typo, layout, gap, thumb size, padding, radius, colori, shadow, border
 */
export default {
  type: 'postnavigation',
  name: t('Navigazione articolo'),
  icon: 'dashicons-arrow-left-alt',
  category: 'navigation',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    show_thumbnail: true,
    show_label: true,
    prev_label: 'Precedente',
    next_label: 'Successivo',
    show_title: true,
    title_length: '30',
    layout: 'side-by-side',
    gap: '20',
    thumbnail_size: '60',
    text_color: '',
    link_color: '',
    hover_color: '',
    background_color: '',
    border_radius: '8',
    tile_padding: { top: 16, right: 16, bottom: 16, left: 16 },
    same_taxonomy: false,
    taxonomy: 'category',
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'show_thumbnail', label: t('Mostra miniatura'), type: 'toggle' },
    { key: 'show_label', label: t('Mostra etichetta'), type: 'toggle' },
    { key: 'prev_label', label: t('Etichetta precedente'), type: 'text' },
    { key: 'next_label', label: t('Etichetta successivo'), type: 'text' },
    { key: 'show_title', label: t('Mostra titolo articolo'), type: 'toggle' },
    { key: 'title_length', label: t('Lunghezza titolo (caratteri)'), type: 'range', min: 10, max: 100, step: 5 },

    { type: 'separator', label: t('Tassonomia') },
    { key: 'same_taxonomy', label: t('Stesso termine tassonomia'), type: 'toggle' },
    { key: 'taxonomy', label: t('Tassonomia'), type: 'select', optionsSource: 'taxonomies',
      condition: { field: 'same_taxonomy', value: true } },
  ],

  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean',    label: t('Modern Clean') },
      { value: 'minimal-arrows',  label: t('Minimal Arrows') },
      { value: 'magazine-cards',  label: t('Magazine Cards') },
      { value: 'centered-thumbs', label: t('Centered Thumbs') },
      { value: 'split-bold',      label: t('Split Bold') },
      { value: 'glass-pill',      label: t('Glass Pill') },
      { value: 'neon-arrows',     label: t('Neon Arrows') },
      { value: 'brutalist-block', label: t('Brutalist Block') },
      { value: 'gradient-flow',   label: t('Gradient Flow') },
      { value: 'sticker-pages',   label: t('Sticker Pages') },
      { value: 'retro-terminal',  label: t('Retro Terminal') },
      { value: 'tilt-cards',      label: t('Tilt Cards') },
      { value: 'custom',          label: t('Personalizzato') },
    ]},
    { type: 'separator', label: t('Tipografia') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Items'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'text_color',
      },
      sizeMin: 12, sizeMax: 60,
    },

    { type: 'separator', label: t('Layout') },
    { key: 'layout', label: t('Layout'), type: 'select', options: [
      { value: 'side-by-side', label: t('Affiancato') },
      { value: 'stacked', label: t('Sovrapposto') },
    ]},
    { key: 'gap', label: t('Gap (px)'), type: 'range', min: 0, max: 40, step: 4 },
    { key: 'thumbnail_size', label: t('Dimensione miniatura (px)'), type: 'range', min: 30, max: 120, step: 5,
      condition: { field: 'show_thumbnail', value: true } },
    { key: 'tile_padding', label: t('Padding (px)'), type: 'spacing', max: 40 },
    withHover({ key: 'border_radius', label: t('Arrotondamento card (px)'), type: 'border-radius' }),

    { type: 'separator', label: t('Colori') },
    withHover({ key: 'link_color', label: t('Colore link'), type: 'color' }, { hoverKey: 'hover_color' }),
    { key: 'background_color', label: t('Sfondo card'), type: 'color' },

    ...shadowField,
    ...borderFields(),
  ],
};
