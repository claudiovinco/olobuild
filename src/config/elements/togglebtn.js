import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile ToggleBtn — split CONTENUTO/STILE.
 *   fields[]      → testi show/hide, icone, target_id, initial_state, animation, duration
 *   styleFields[] → preset, bg, typo, icona posizione, stile pulsante (colori/bordo/padding/font/align), border
 */
export default {
  type: 'togglebtn',
  name: t('Pulsante Toggle'),
  icon: 'dashicons-hidden',
  category: 'interactive',
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',
    text_show: 'Mostra di più',
    text_hide: 'Mostra di meno',
    icon_show: 'chevron-down',
    icon_hide: 'chevron-up',
    icon_position: 'right',
    target_id: '',
    initial_state: 'hidden',
    animation: 'collapse',
    duration: '400',
    btn_bg: '',
    btn_color: '',
    btn_hover_bg: '',
    btn_border_width: '2',
    btn_border_color: '',
    btn_border_radius: '8',
    tile_padding: { top: 10, right: 24, bottom: 10, left: 24 },
    btn_font_size: '15',
    btn_font_weight: '600',
    btn_align: 'center',
    btn_full_width: false,
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'text_show', label: t('Testo (quando nascosto)'), type: 'text' },
    { key: 'text_hide', label: t('Testo (quando visibile)'), type: 'text' },
    { key: 'icon_show', label: t('Icona mostra'), type: 'select', options: [
      { value: '', label: t('Nessuna') },
      { value: 'chevron-down', label: t('▼ Chevron giù') },
      { value: 'plus', label: t('＋ Più') },
      { value: 'arrow-down', label: t('↓ Freccia giù') },
      { value: 'eye', label: t('👁 Occhio') },
    ]},
    { key: 'icon_hide', label: t('Icona nascondi'), type: 'select', options: [
      { value: '', label: t('Nessuna') },
      { value: 'chevron-up', label: t('▲ Chevron su') },
      { value: 'minus', label: t('— Meno') },
      { value: 'arrow-up', label: t('↑ Freccia su') },
      { value: 'eye-off', label: t('🚫 Occhio chiuso') },
    ]},

    { type: 'separator', label: t('Sezione target') },
    { key: 'target_id', label: t('ID sezione (html_id in Avanzate)'), type: 'text' },
    { key: 'initial_state', label: t('Stato iniziale sezione'), type: 'select', options: [
      { value: 'hidden', label: t('Nascosta') },
      { value: 'visible', label: t('Visibile') },
    ]},
    { key: 'animation', label: t('Animazione'), type: 'select', options: [
      { value: 'collapse', label: t('Collassa (altezza)') },
      { value: 'fade', label: t('Dissolvenza') },
      { value: 'slide', label: t('Scorrimento + dissolvenza') },
    ]},
    { key: 'duration', label: t('Durata (ms)'), type: 'range', min: 100, max: 800, step: 50 },
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
    { type: 'separator', label: t('Tipografia') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Pulsante'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:   'btn_font_size',
        weight: 'btn_font_weight',
        color:  'btn_color',
      },
      sizeMin: 12, sizeMax: 24,
    },

    { type: 'separator', label: t('Icona') },
    { key: 'icon_position', label: t('Posizione icona'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'right', label: t('Destra') },
    ]},

    { type: 'separator', label: t('Stile pulsante') },
    { key: 'btn_bg', label: t('Sfondo'), type: 'color' },
    { key: 'btn_hover_bg', label: t('Sfondo hover'), type: 'color' },
    { key: 'btn_border_width', label: t('Bordo (px)'), type: 'range', min: 0, max: 4 },
    { key: 'btn_border_color', label: t('Colore bordo'), type: 'color' },
    withHover({ key: 'btn_border_radius', label: t('Raggio bordo (px)'), type: 'border-radius' }),
    { key: 'tile_padding', label: t('Padding (px)'), type: 'spacing', max: 48 },
    { key: 'btn_align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},
    { key: 'btn_full_width', label: t('Larghezza piena'), type: 'toggle' },

    ...borderFields(),
  ],
};
