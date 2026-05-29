
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile RelatedPosts — split CONTENUTO/STILE.
 *   fields[]      → query (source/count/orderby), contenuto card (show_image/date/excerpt/category/title_tag), fallback text
 *   styleFields[] → preset, bg, typo, layout (columns/gap/image_ratio), colori, padding, radius, hover effect, border
 */
export default {
  type: 'relatedposts',
  name: t('Articoli Correlati'),
  icon: 'dashicons-screenoptions',
  category: 'dynamic',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    source: 'categories',
    count: '3',
    columns: '3',
    show_image: true,
    show_date: true,
    show_excerpt: false,
    show_category: false,
    excerpt_length: '20',
    image_ratio: '16/9',
    gap: '20',
    title_tag: 'h4',
    title_color: '',
    text_color: '',
    date_color: '',
    card_background: '',
    tile_padding: { top: 16, right: 16, bottom: 16, left: 16 },
    card_border_radius: '8',
    hover_effect: 'shadow',
    fallback_text: 'Nessun articolo correlato',
    orderby: 'rand',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { type: 'separator', label: t('Query') },
    { key: 'source', label: t('Correlazione per'), type: 'select', options: [
      { value: 'categories', label: t('Categorie') },
      { value: 'tags', label: t('Tag') },
      { value: 'both', label: t('Categorie + Tag') },
    ]},
    { key: 'count', label: t('Numero articoli'), type: 'range', min: 1, max: 12, step: 1 },
    { key: 'orderby', label: t('Ordina per'), type: 'select', options: [
      { value: 'rand', label: t('Casuale') },
      { value: 'date', label: t('Data') },
      { value: 'title', label: t('Titolo') },
    ]},

    { type: 'separator', label: t('Contenuto card') },
    { key: 'show_image', label: t('Mostra immagine'), type: 'toggle' },
    { key: 'show_date', label: t('Mostra data'), type: 'toggle' },
    { key: 'show_excerpt', label: t('Mostra estratto'), type: 'toggle' },
    { key: 'excerpt_length', label: t('Parole estratto'), type: 'range', min: 5, max: 50, step: 1,
      condition: { field: 'show_excerpt', value: true } },
    { key: 'show_category', label: t('Mostra categoria'), type: 'toggle' },

    { key: 'fallback_text', label: t('Testo fallback (nessun risultato)'), type: 'text' },
  ],

  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-cards',     label: t('Modern Cards') },
      { value: 'editorial-list',   label: t('Editorial List') },
      { value: 'compact-row',      label: t('Compact Row') },
      { value: 'magazine-trio',    label: t('Magazine Trio') },
      { value: 'minimal-line',     label: t('Minimal Line') },
      { value: 'glass-cards',      label: t('Glass Cards') },
      { value: 'neon-tiles',       label: t('Neon Tiles') },
      { value: 'brutalist-stamp',  label: t('Brutalist Stamp') },
      { value: 'gradient-soft',    label: t('Gradient Soft') },
      { value: 'sticker-cards',    label: t('Sticker Cards') },
      { value: 'retro-zine',       label: t('Retro Zine') },
      { value: 'tilt-3d',          label: t('3D Tilt Cards') },
      { value: 'custom',           label: t('Personalizzato') },
    ]},
    { type: 'separator', label: t('Tipografia') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Titolo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        tag:   'title_tag',
        color: 'title_color',
      },
      sizeMin: 12, sizeMax: 60,
    },
    { type: 'typography', label: t('Testo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'text_color',
      },
      sizeMin: 12, sizeMax: 60,
    },
    { type: 'typography', label: t('Data'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'date_color',
      },
      sizeMin: 12, sizeMax: 60,
    },

    { type: 'separator', label: t('Layout') },
    { key: 'columns', label: t('Colonne'), type: 'select', options: [
      { value: '1', label: '1' },
      { value: '2', label: '2' },
      { value: '3', label: '3' },
      { value: '4', label: '4' },
    ]},
    { key: 'gap', label: t('Spaziatura (px)'), type: 'range', min: 0, max: 40, step: 2 },
    { key: 'image_ratio', label: t('Rapporto immagine'), type: 'select', options: [
      { value: '16/9', label: '16:9' },
      { value: '4/3', label: '4:3' },
      { value: '1/1', label: '1:1' },
      { value: 'auto', label: t('Automatico') },
    ], condition: { field: 'show_image', value: true } },

    { type: 'separator', label: t('Stile') },
    { key: 'card_background', label: t('Sfondo card'), type: 'color' },
    { key: 'tile_padding', label: t('Padding (px)'), type: 'spacing', max: 32 },
    withHover({ key: 'card_border_radius', label: t('Raggio bordo (px)'), type: 'border-radius' }),
    { key: 'hover_effect', label: t('Effetto hover'), type: 'select', options: [
      { value: 'none', label: t('Nessuno') },
      { value: 'shadow', label: t('Ombra') },
      { value: 'scale', label: t('Ingrandisci') },
    ]},

    ...borderFields(),
  ],
};
