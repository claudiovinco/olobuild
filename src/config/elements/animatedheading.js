import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile AnimatedHeading — split CONTENUTO/STILE.
 *   fields[]      → before_text, animated_words, after_text, tag, animation, typing_speed, pause_time, highlight_style
 *   styleFields[] → preset, typo, alignment, colori, font_size+weight, highlight_color, shadow, border
 */
export default {
  type: 'animatedheading',
  name: t('Titolo animato'),
  icon: 'dashicons-editor-textcolor',
  category: 'text',
  defaults: {
    preset: 'custom',
    typography_preset: '',
    before_text: 'Noi siamo',
    animated_words: 'creativi\ninnovativi\nappassionati',
    after_text: '',
    animation: 'typing',
    tag: 'h2',
    alignment: 'center',
    text_color: '',
    animated_color: '',
    font_size: '36',
    font_weight: '700',
    typing_speed: '100',
    pause_time: '2000',
    highlight_style: 'underline',
    highlight_color: '',
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'before_text', label: t('Testo prima'), type: 'text' },
    { key: 'animated_words', label: t('Parole animate (una per riga)'), type: 'textarea' },
    { key: 'after_text', label: t('Testo dopo'), type: 'text' },
    { key: 'animation', label: t('Animazione'), type: 'select', options: [
      { value: 'typing', label: t('Digitazione') },
      { value: 'rotating', label: t('Rotazione verticale') },
      { value: 'fade', label: t('Dissolvenza') },
      { value: 'slide', label: t('Scorrimento') },
      { value: 'highlight', label: t('Evidenziazione') },
      { value: 'clip', label: t('Clip (rivela)') },
    ]},

    { type: 'separator', label: t('Velocità') },
    { key: 'typing_speed', label: t('Velocità digitazione (ms)'), type: 'range', min: 50, max: 300, step: 10 },
    { key: 'pause_time', label: t('Pausa tra parole (ms)'), type: 'range', min: 500, max: 5000, step: 100 },

    { type: 'separator', label: t('Evidenziazione (se animation=highlight)') },
    { key: 'highlight_style', label: t('Stile evidenziazione'), type: 'select', options: [
      { value: 'underline', label: t('Sottolineatura') },
      { value: 'background', label: t('Sfondo') },
      { value: 'circle', label: t('Cerchio') },
      { value: 'strikethrough', label: t('Barrato') },
    ], condition: { field: 'animation', operator: '==', value: 'highlight' } },
  ],

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

    { type: 'separator', label: t('Allineamento') },
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},

    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Testo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        tag:    'tag',
        size:   'font_size',
        weight: 'font_weight',
        color:  'text_color',
      },
      sizeMin: 18, sizeMax: 80, sizeStep: 2,
    },
    { type: 'typography', label: t('Highlight'),
      presetKey: 'typography_preset',
      keys: {
        color: 'animated_color',
      },
    },
    { key: 'highlight_color', label: t('Colore evidenziazione'), type: 'color',
      condition: { field: 'animation', operator: '==', value: 'highlight' } },

    ...shadowField,
    ...borderFields(),
  ],
};
