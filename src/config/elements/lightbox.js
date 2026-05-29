import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared';
import { shadowField } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Lightbox — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → items (title+type+url+thumb+caption), show_caption (visibilità)
 *   styleFields[] → preset, bg, typo, text-effects, layout (cols/gap/ratio/radius), overlay style, animation, shadow, border
 *   AVANZATE      → meta tecnico
 */
export default {
  type: 'lightbox',
  name: t('Lightbox'),
  icon: 'dashicons-format-gallery',
  category: 'media',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    items: [
      { id: 'lb-1', title: t('Immagine 1'), type: 'image', url: '', thumb: '', caption: '' },
    ],
    columns: '3',
    gap: '15',
    thumb_ratio: '1:1',
    thumb_radius: '8',
    overlay_style: 'dark',
    show_caption: true,
    animation: 'fade',
    shadow: 'none',
    ...textEffectsDefaults,
    text_effect_target: 'all',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'items', label: t('Elementi'), type: 'content-items',
      itemFields: [
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'type', label: t('Tipo'), type: 'select', options: [
          { value: 'image', label: t('Immagine') },
          { value: 'video', label: t('Video (URL)') },
          { value: 'iframe', label: t('iFrame (URL)') },
        ] },
        { key: 'url', label: t('URL media'), type: 'text' },
        { key: 'thumb', label: t('Miniatura'), type: 'image' },
        { key: 'caption', label: t('Didascalia'), type: 'text' },
      ],
      newItemDefaults: { title: t('Nuovo elemento'), type: 'image', url: '', thumb: '', caption: '' },
    },
    { key: 'show_caption', label: t('Mostra didascalia nel lightbox'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean',    label: t('Modern Clean') },
      { value: 'minimal-thumbs',  label: t('Minimal Thumbs') },
      { value: 'magazine-grid',   label: t('Magazine Grid') },
      { value: 'cinema-wide',     label: t('Cinema Wide') },
      { value: 'compact-row',     label: t('Compact Row') },
      { value: 'glass-tiles',     label: t('Glass Tiles') },
      { value: 'neon-frame',      label: t('Neon Frame') },
      { value: 'brutalist-block', label: t('Brutalist Block') },
      { value: 'gradient-soft',   label: t('Gradient Soft') },
      { value: 'sticker-thumbs',  label: t('Sticker Thumbs') },
      { value: 'retro-vhs',       label: t('Retro VHS') },
      { value: 'tilt-3d',         label: t('3D Tilt') },
      { value: 'custom',          label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    ...textEffectsFields([
      { value: 'title', label: t('Solo Titolo') },
      { value: 'caption', label: t('Solo Didascalia') },
      { value: 'all', label: t('Tutti gli elementi testuali') },
    ]),

    { type: 'separator', label: t('Layout') },
    { key: 'columns', label: t('Colonne'), type: 'range', min: 1, max: 6, step: 1 },
    { key: 'gap', label: t('Gap (px)'), type: 'range', min: 0, max: 40, step: 5 },
    { key: 'thumb_ratio', label: t('Proporzione miniature'), type: 'select', options: [
      { value: '1:1', label: '1:1' },
      { value: '4:3', label: '4:3' },
      { value: '16:9', label: '16:9' },
      { value: 'auto', label: t('Auto') },
    ] },
    withHover({ key: 'thumb_radius', label: t('Raggio bordo (px)'), type: 'border-radius' }),

    { type: 'separator', label: t('Lightbox — Aspetto') },
    { key: 'overlay_style', label: t('Stile overlay'), type: 'select', options: [
      { value: 'dark', label: t('Scuro') },
      { value: 'light', label: t('Chiaro') },
      { value: 'none', label: t('Nessuno') },
    ] },
    { key: 'animation', label: t('Animazione'), type: 'select', options: [
      { value: 'fade', label: t('Dissolvenza') },
      { value: 'slide', label: t('Scorrimento') },
      { value: 'scale', label: t('Scala') },
    ] },

    ...shadowField,
    ...borderFields(),
  ],
};
