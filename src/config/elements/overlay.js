import { textEffectsFields, textEffectsDefaults, filterFields, filterDefaults, shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Overlay — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → immagine, titolo, descrizione, URL link + target, effetto hover (comportamento)
 *   styleFields[] → preset, sfondo, tipografia, border-radius, colore overlay/testo,
 *                   opacità overlay, altezza, text effects, ombra, filtri immagine, bordi
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'overlay',
  name: t('Overlay'),
  icon: 'dashicons-format-image',
  category: 'media',
  defaults: {
    preset: 'custom',
    typography_preset: '',
    bg: { type: 'none' },
    image_url: '',
    object_position: 'center center',
    title: t('Titolo progetto'),
    description: t('Descrizione.'),
    link_url: '',
    link_target: '_self',
    overlay_color: '#000000',
    text_color: '',
    hover_effect: 'fade',
    overlay_opacity: '70',
    height: '300',
    shadow: 'none',
    border_radius: '0',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
    ...textEffectsDefaults,
    text_effect_target: 'title',
    ...filterDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'image_url', label: t('Immagine'), type: 'image' },
    { key: 'title', label: t('Titolo'), type: 'text' },
    { key: 'description', label: t('Descrizione'), type: 'textarea' },
    { key: 'link_url', label: t('URL link'), type: 'link' },
    { key: 'link_target', label: t('Apri in'), type: 'select', options: [
      { value: '_self', label: t('Stessa finestra') },
      { value: '_blank', label: t('Nuova scheda') },
    ]},
    { key: 'hover_effect', label: t('Effetto hover'), type: 'select', options: [
      { value: 'fade', label: t('Fade') },
      { value: 'slide-up', label: t('Slide Up') },
      { value: 'zoom', label: t('Zoom') },
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
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'separator', label: t('Aspetto') },
    withHover({ key: 'border_radius', label: t('Border Radius'), type: 'border-radius' }),
    { key: 'height', label: t('Altezza (px)'), type: 'range', min: 10, max: 600, step: 5 },
    { key: 'object_position', label: t('Posizione contenuto'), type: 'object-position', contextKeys: { src: 'image_url', ratio: 'height' } },
    { type: 'separator', label: t('Overlay') },
    { key: 'overlay_color', label: t('Colore overlay'), type: 'color' },
    { key: 'text_color', label: t('Colore testo'), type: 'color' },
    { key: 'overlay_opacity', label: t('Opacità overlay (%)'), type: 'range', min: 0, max: 100, step: 5 },
    ...textEffectsFields([
      { value: 'title', label: t('Solo Titolo') },
      { value: 'description', label: t('Solo Descrizione') },
      { value: 'all', label: t('Tutti gli elementi testuali') },
    ]),
    ...shadowField,
    ...filterFields,
    ...borderFields(),
  ],
};
