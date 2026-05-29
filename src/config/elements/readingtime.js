import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile ReadingTime — split CONTENUTO/STILE.
 *   fields[]      → calcolo (words_per_minute/format/prefix/suffix), icona (show + icon)
 *   styleFields[] → bg, typo preset, tipografia (color/size/weight/align), icona color, shadow, border
 */
export default {
  type: 'readingtime',
  label: t('Tempo di Lettura'),
  icon: 'dashicons-clock',
  category: 'dynamic',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    words_per_minute: 200,
    format: 'full',
    prefix: 'Tempo di lettura:',
    suffix: 'min',
    icon: 'clock',
    show_icon: true,
    text_color: '',
    icon_color: '',
    font_size: '',
    font_weight: '',
    text_align: 'left',
    box_shadow: '',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { type: 'separator', label: t('Calcolo') },
    { key: 'words_per_minute', label: t('Parole al minuto'), type: 'number', min: 50, max: 500 },
    { key: 'format', label: t('Formato'), type: 'select', options: [
      { value: 'full', label: t('Completo (Tempo di lettura: 5 min)') },
      { value: 'short', label: t('Breve (5 min di lettura)') },
      { value: 'minutes_only', label: t('Solo minuti (5)') },
    ]},
    { key: 'prefix', label: t('Prefisso'), type: 'text', condition: { format: ['full'] }},
    { key: 'suffix', label: t('Suffisso'), type: 'text' },

    { type: 'separator', label: t('Icona') },
    { key: 'show_icon', label: t('Mostra icona'), type: 'toggle' },
    { key: 'icon', label: t('Icona'), type: 'icon', condition: { show_icon: [true] }},
  ],

  styleFields: [
    { type: 'separator', label: t('Sfondo & tipografia') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Testo'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:   'font_size',
        weight: 'font_weight',
        color:  'text_color',
      },
      sizeMin: 12, sizeMax: 60,
    },
    { key: 'text_align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') }, { value: 'center', label: t('Centro') }, { value: 'right', label: t('Destra') }
    ]},
    { key: 'icon_color', label: t('Colore icona'), type: 'color' },

    ...shadowField,
    ...borderFields(),
  ],
};
