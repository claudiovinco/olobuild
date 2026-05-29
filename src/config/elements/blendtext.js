import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared';
import { t } from '@/i18n';

/**
 * Tile BlendText — split CONTENUTO/STILE.
 *   fields[]      → text, tag HTML
 *   styleFields[] → preset, bg, typo, blend mode, text color, text-effects, tipografia (size/weight/family/transform/spacing/line-h/align), padding, border
 */
export default {
  type: 'blendtext',
  name: t('Blend Text'),
  icon: 'dashicons-editor-textcolor',
  category: 'creative',
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    text: 'BLEND',
    tag: 'div',
    font_size: '120',
    font_size_tablet: '80',
    font_size_mobile: '50',
    font_weight: '900',
    font_family: '',
    text_transform: 'uppercase',
    letter_spacing: '5',
    line_height: '1',
    text_align: 'center',
    text_color: '#ffffff',
    blend_mode: 'difference',
    tile_padding: { top: 40, right: 20, bottom: 40, left: 20 },
    ...textEffectsDefaults,
    text_effect_target: 'text',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'text', label: t('Testo'), type: 'text' },
    { key: 'tag', label: t('Tag HTML'), type: 'select', options: [
      { value: 'div', label: t('Div') },
      { value: 'h1', label: t('H1') },
      { value: 'h2', label: t('H2') },
      { value: 'h3', label: t('H3') },
      { value: 'h4', label: t('H4') },
      { value: 'p', label: t('Paragrafo') },
    ]},
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

    { type: 'separator', label: t('Blend') },
    { key: 'blend_mode', label: t('Blend Mode'), type: 'select', options: [
      { value: 'normal', label: t('Normale') },
      { value: 'multiply', label: t('Moltiplica') },
      { value: 'screen', label: t('Schermo') },
      { value: 'overlay', label: t('Sovrapposizione') },
      { value: 'darken', label: t('Scurisci') },
      { value: 'lighten', label: t('Schiarisci') },
      { value: 'color-dodge', label: t('Color Dodge') },
      { value: 'color-burn', label: t('Color Burn') },
      { value: 'hard-light', label: t('Hard Light') },
      { value: 'soft-light', label: t('Soft Light') },
      { value: 'difference', label: t('Differenza') },
      { value: 'exclusion', label: t('Esclusione') },
      { value: 'hue', label: t('Tonalità') },
      { value: 'saturation', label: t('Saturazione') },
      { value: 'color', label: t('Colore') },
      { value: 'luminosity', label: t('Luminosità') },
    ]},

    ...textEffectsFields([ { value: 'text', label: t('Solo Testo') } ]),

    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Testo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size', 'lineHeight', 'letterSpacing'],
      keys: {
        family:        'font_family',
        size:          'font_size',
        weight:        'font_weight',
        transform:     'text_transform',
        letterSpacing: 'letter_spacing',
        lineHeight:    'line_height',
        color:         'text_color',
      },
      sizeMin: 12, sizeMax: 300, sizeStep: 1,
    },
    { key: 'text_align', label: t('Allineamento'), type: 'select', responsive: true, options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},

    { type: 'separator', label: t('Spaziatura') },
    { key: 'tile_padding', label: t('Padding (px)'), type: 'spacing', max: 200 },

    ...borderFields(),
  ],
};
