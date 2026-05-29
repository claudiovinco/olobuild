import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover, widgetTemplateField } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Carousel — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → slide items (image+alt+link+caption), autoplay+speed, loop, pause_on_hover, show_caption, controls (arrows/dots)
 *   styleFields[] → preset, bg, typo, text-effects, layout (slides_to_show/gap/height/fit/radius/mobile), colori (frecce/dot/caption), border
 *   AVANZATE      → meta tecnico
 */
export default {
  type: 'carousel',
  name: t('Carosello immagini'),
  icon: 'dashicons-format-image',
  category: 'media',
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    slides: [
      { id: 'cs-1', image_url: '', image_alt: '', link_url: '', caption: '' },
      { id: 'cs-2', image_url: '', image_alt: '', link_url: '', caption: '' },
      { id: 'cs-3', image_url: '', image_alt: '', link_url: '', caption: '' },
    ],
    slides_to_show: '3',
    gap: '16',
    autoplay: false,
    autoplay_speed: '4000',
    show_arrows: true,
    show_dots: true,
    loop: true,
    pause_on_hover: true,
    slide_height: 'auto',
    fixed_height: '300',
    border_radius: '8',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
    arrow_color: '',
    arrow_bg: '',
    dot_color: '',
    dot_inactive_color: '',
    show_caption: false,
    caption_color: '',
    caption_bg: '',
    object_fit: 'cover',
    mobile_slides: '1',
    ...textEffectsDefaults,
    text_effect_target: 'caption',
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'slides', label: t('Slide'), type: 'content-items',
      itemFields: [
        widgetTemplateField,
        { key: 'image_url', label: t('Immagine'), type: 'image' },
        { key: 'image_alt', label: t('Testo alternativo'), type: 'text' },
        { key: 'link_url', label: t('URL link'), type: 'link' },
        { key: 'caption', label: t('Didascalia'), type: 'text' },
      ],
      newItemDefaults: { image_url: '', image_alt: '', link_url: '', caption: '', widget_template_id: 0 },
      itemLabel: 'Slide',
    },

    { type: 'separator', label: t('Comportamento') },
    { key: 'autoplay', label: t('Autoplay'), type: 'toggle' },
    { key: 'autoplay_speed', label: t('Velocità autoplay (ms)'), type: 'range', min: 1000, max: 10000, step: 500,
      condition: { field: 'autoplay', value: true } },
    { key: 'loop', label: t('Loop infinito'), type: 'toggle' },
    { key: 'pause_on_hover', label: t('Pausa al passaggio'), type: 'toggle',
      condition: { field: 'autoplay', value: true } },
    { key: 'show_caption', label: t('Mostra didascalie'), type: 'toggle' },

    { type: 'separator', label: t('Navigazione') },
    { key: 'show_arrows', label: t('Frecce navigazione'), type: 'toggle' },
    { key: 'show_dots', label: t('Indicatori punti'), type: 'toggle' },
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
    ...textEffectsFields([ { value: 'caption', label: t('Solo Didascalia') } ]),

    { type: 'separator', label: t('Tipografia') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Didascalia'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        color: 'caption_color',
      },
      sizeMin: 12, sizeMax: 60,
    },

    { type: 'separator', label: t('Layout') },
    { key: 'slides_to_show', label: t('Slide visibili'), type: 'range', min: 1, max: 6, step: 1 },
    { key: 'gap', label: t('Gap (px)'), type: 'range', min: 0, max: 48, step: 4 },
    { key: 'slide_height', label: t('Altezza slide'), type: 'select', options: [
      { value: 'auto', label: t('Automatica') },
      { value: 'fixed', label: t('Fissa') },
    ]},
    { key: 'fixed_height', label: t('Altezza fissa (px)'), type: 'range', min: 150, max: 600, step: 10,
      condition: { field: 'slide_height', value: 'fixed' } },
    { key: 'object_fit', label: t('Adattamento'), type: 'select', options: [
      { value: 'cover', label: t('Riempi') },
      { value: 'contain', label: t('Contieni') },
    ]},
    withHover({ key: 'border_radius', label: t('Raggio bordi (px)'), type: 'border-radius' }),
    { key: 'mobile_slides', label: t('Slide mobile'), type: 'range', min: 1, max: 3, step: 1 },

    { type: 'separator', label: t('Colori') },
    { key: 'arrow_color', label: t('Colore frecce'), type: 'color' },
    { key: 'arrow_bg', label: t('Sfondo frecce'), type: 'color' },
    { key: 'dot_color', label: t('Colore punto attivo'), type: 'color' },
    { key: 'dot_inactive_color', label: t('Colore punto inattivo'), type: 'color' },
    { key: 'caption_bg', label: t('Sfondo didascalia'), type: 'color',
      condition: { field: 'show_caption', value: true } },

    ...borderFields(),
  ],
};
