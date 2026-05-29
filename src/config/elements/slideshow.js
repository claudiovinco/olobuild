import { textEffectsFields, textEffectsDefaults, filterFields, filterDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, widgetTemplateField, wowEffectsFields, wowEffectsDefaults } from './_shared';
import { shadowField } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Slideshow — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → slide items (image+title+subtitle+link), autoplay+speed, controls (arrows/dots), transition
 *   styleFields[] → preset, bg, typo, text-effects, altezza, overlay color, colore testo, shadow, filter, border
 *   AVANZATE      → meta tecnico
 */
export default {
  type: 'slideshow',
  name: t('Slideshow'),
  icon: 'dashicons-slides',
  category: 'media',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    slides: [
      { id: 's-1', image: '', title: t('Prima slide'), subtitle: 'Prima slide', link: '' },
    ],
    autoplay: true,
    autoplay_speed: '5000',
    show_arrows: true,
    show_dots: true,
    slide_height: '400',
    overlay_color: '#000000',
    text_color: '',
    transition: 'slide',
    shadow: 'none',
    ...textEffectsDefaults,
    text_effect_target: 'title',
    ...filterDefaults,
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
    ...wowEffectsDefaults,
    effect_color: '',
    effect_intensity: 'medium',
    effect_speed: 0,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'slides', label: t('Slide'), type: 'content-items', supportsDynamic: true,
      itemFields: [
        widgetTemplateField,
        { key: 'image', label: t('Immagine'), type: 'image' },
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'subtitle', label: t('Sottotitolo'), type: 'text' },
        { key: 'link', label: t('URL link'), type: 'link', placeholder: t('https://...') },
      ],
      newItemDefaults: { image: '', title: t('Nuova slide'), subtitle: '', link: '', widget_template_id: 0 },
      itemLabel: 'Slide',
    },

    { type: 'separator', label: t('Riproduzione') },
    { key: 'autoplay', label: t('Riproduzione automatica'), type: 'toggle' },
    { key: 'autoplay_speed', label: t('Velocità riproduzione (ms)'), type: 'range', min: 2000, max: 10000, step: 500 },

    { type: 'separator', label: t('Controlli') },
    { key: 'show_arrows', label: t('Mostra frecce'), type: 'toggle' },
    { key: 'show_dots', label: t('Mostra punti'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'cinema-hero',     label: t('Cinema Hero') },
      { value: 'magazine-fade',   label: t('Magazine Fade') },
      { value: 'minimal-clean',   label: t('Minimal Clean') },
      { value: 'editorial-split', label: t('Editorial Split') },
      { value: 'fullscreen-cta',  label: t('Fullscreen CTA') },
      { value: 'glass-overlay',   label: t('Glass Overlay') },
      { value: 'neon-tron',       label: t('Neon Tron') },
      { value: 'brutalist-mega',  label: t('Brutalist Mega') },
      { value: 'gradient-aurora', label: t('Gradient Aurora') },
      { value: 'sticker-collage', label: t('Sticker Collage') },
      { value: 'retro-vhs',       label: t('Retro VHS') },
      { value: 'tilt-parallax',   label: t('Tilt Parallax') },
      { value: 'custom',          label: t('Personalizzato') },
    ]},
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    ...textEffectsFields([
      { value: 'title', label: t('Solo Titolo') },
      { value: 'subtitle', label: t('Solo Sottotitolo') },
      { value: 'all', label: t('Tutti gli elementi testuali') },
    ]),

    { type: 'separator', label: t('Aspetto') },
    { key: 'transition', label: t('Transizione'), type: 'select', options: [
      { value: 'slide', label: t('Slide') },
      { value: 'fade', label: t('Fade') },
    ]},
    { key: 'slide_height', label: t('Altezza slide (px)'), type: 'range', min: 200, max: 800, step: 25 },
    { key: 'overlay_color', label: t('Colore overlay'), type: 'color' },
    { key: 'text_color', label: t('Colore testo'), type: 'color' },

    ...shadowField,
    ...filterFields,
    ...wowEffectsFields(),
    ...borderFields(),
  ],
};
