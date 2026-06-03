import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared';
import { t } from '@/i18n';

/**
 * Tile TextPath — split CONTENUTO/STILE.
 *   fields[]      → text, path_preset, custom_path, animation, animation_speed
 *   styleFields[] → preset, bg, typo, text-effects, tipografia (size/color/spacing), border
 */
export default {
  type: 'textpath',
  name: t('Testo su Tracciato'),
  icon: 'dashicons-editor-textcolor',
  category: 'text',
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    text: 'Testo che segue un tracciato curvo',
    path_preset: 'arc',
    custom_path: '',
    font_size: '24',
    text_color: '',
    letter_spacing: '2',
    animation: 'none',
    animation_speed: '10',
    // Spin: rotazione continua dell'intero gruppo testo-su-tracciato (es. badge ad anello "type set in motion").
    // Additivo, default OFF: i TextPath esistenti restano invariati. Rispetta prefers-reduced-motion (fermo).
    spin: false,
    spin_speed: '14',
    spin_direction: 'cw',
    ...textEffectsDefaults,
    text_effect_target: 'text',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'text', label: t('Testo'), type: 'text' },

    { type: 'separator', label: t('Tracciato') },
    { key: 'path_preset', label: t('Forma tracciato'), type: 'select', options: [
      { value: 'arc', label: t('Arco') },
      { value: 'wave', label: t('Onda') },
      { value: 'circle', label: t('Cerchio') },
      { value: 'spiral', label: t('Spirale') },
      { value: 'custom', label: t('Personalizzato') },
    ]},
    { key: 'custom_path', label: t('Percorso SVG (d)'), type: 'text', placeholder: t('M 0 50 Q 150 0 300 50'),
      condition: { field: 'path_preset', value: 'custom' } },

    { type: 'separator', label: t('Animazione') },
    { key: 'animation', label: t('Animazione'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'scroll', label: t('Scorrimento una volta') },
      { value: 'continuous', label: t('Scorrimento continuo') },
    ]},
    { key: 'animation_speed', label: t('Velocità animazione (sec)'), type: 'range', min: 1, max: 20,
      condition: { field: 'animation', value: ['scroll', 'continuous'] } },

    { type: 'separator', label: t('Rotazione continua (spin)') },
    { key: 'spin', label: t('Ruota tutto il gruppo'), type: 'toggle',
      description: t('Ruota in continuazione l\'intero testo-su-tracciato (ideale per badge ad anello). Rispetta prefers-reduced-motion: resta fermo.') },
    { key: 'spin_speed', label: t('Velocità rotazione (sec/giro)'), type: 'range', min: 3, max: 40, step: 1,
      condition: { field: 'spin', op: 'eq', value: true } },
    { key: 'spin_direction', label: t('Direzione'), type: 'select', options: [
      { value: 'cw',  label: t('Oraria') },
      { value: 'ccw', label: t('Antioraria') },
    ], condition: { field: 'spin', op: 'eq', value: true } },
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

    ...textEffectsFields([ { value: 'text', label: t('Solo Testo') } ]),

    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Testo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size', 'letterSpacing'],
      keys: {
        size:          'font_size',
        letterSpacing: 'letter_spacing',
        color:         'text_color',
      },
      sizeMin: 12, sizeMax: 72,
    },

    ...borderFields(),
  ],
};
