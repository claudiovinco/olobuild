
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Pagination — split CONTENUTO/STILE.
 *   fields[]      → modalità (numerata/prev-next/both), show_first_last, prev/next text
 *   styleFields[] → preset, bg, typo, alignment, gap/padding/font/radius/border, colori (testo/sfondo/bordo/attivo), border
 */
export default {
  type: 'pagination',
  name: t('Paginazione'),
  icon: 'dashicons-ellipsis',
  category: 'navigation',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    style: 'both',
    alignment: 'center',
    show_first_last: false,
    prev_text: '« Precedente',
    next_text: 'Successivo »',
    gap: '8',
    button_padding: '8 16',
    text_color: '',
    active_color: '',
    active_text_color: '',
    background_color: '',
    active_background: '',
    border_radius: '4',
    hover_background: '',
    font_size: '14',
    border_color: '',
    border_width: '1',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'style', label: t('Modalità'), type: 'select', options: [
      { value: 'numbered', label: t('Solo numeri') },
      { value: 'prev-next', label: t('Solo Prec/Succ') },
      { value: 'both', label: t('Numeri + Prec/Succ') },
    ]},
    { key: 'show_first_last', label: t('Mostra Primo/Ultimo'), type: 'toggle' },
    { key: 'prev_text', label: t('Testo Precedente'), type: 'text' },
    { key: 'next_text', label: t('Testo Successivo'), type: 'text' },
  ],

  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-pills',    label: t('Modern Pills') },
      { value: 'minimal-thin',    label: t('Minimal Thin') },
      { value: 'magazine-bold',   label: t('Magazine Bold') },
      { value: 'circle-numbers',  label: t('Circle Numbers') },
      { value: 'compact-text',    label: t('Compact Text') },
      { value: 'glass-pills',     label: t('Glass Pills') },
      { value: 'neon-numbers',    label: t('Neon Numbers') },
      { value: 'brutalist-block', label: t('Brutalist Block') },
      { value: 'gradient-pills',  label: t('Gradient Pills') },
      { value: 'sticker-pages',   label: t('Sticker Pages') },
      { value: 'retro-terminal',  label: t('Retro Terminal') },
      { value: 'tilt-3d',         label: t('3D Tilt') },
      { value: 'custom',          label: t('Personalizzato') },
    ]},
    { type: 'separator', label: t('Tipografia') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Numero'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:  'font_size',
        color: 'text_color',
      },
      sizeMin: 10, sizeMax: 24,
    },

    { type: 'separator', label: t('Allineamento') },
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},

    { type: 'separator', label: t('Dimensioni') },
    { key: 'gap', label: t('Distanza tra pulsanti (px)'), type: 'range', min: 0, max: 24, step: 2 },
    { key: 'button_padding', label: t('Padding pulsanti (px)'), type: 'text' },
    withHover({ key: 'border_radius', label: t('Raggio bordo (px)'), type: 'border-radius' }),
    { key: 'border_width', label: t('Spessore bordo (px)'), type: 'range', min: 0, max: 4, step: 1 },

    { type: 'separator', label: t('Colori') },
    withHover({ key: 'background_color', label: t('Sfondo pulsanti'), type: 'color' }, { hoverKey: 'hover_background' }),
    { key: 'border_color', label: t('Colore bordo'), type: 'color' },
    { key: 'active_color', label: t('Colore testo pagina attiva'), type: 'color' },
    { key: 'active_text_color', label: t('Testo pagina attiva'), type: 'color' },
    { key: 'active_background', label: t('Sfondo pagina attiva'), type: 'color' },

    ...borderFields(),
  ],
};
