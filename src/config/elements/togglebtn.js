import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, withHover } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile ToggleBtn — split CONTENUTO/STILE.
 *   fields[]      → testi show/hide, icone, target_id, initial_state, animation, duration
 *   styleFields[] → Aspetto (sfondo), Testo (tipografia), Forma (raggio, padding, larghezza),
 *                   Bordo (UNO: btn_border con hover ed effetti), Disposizione (allineamento, icona)
 *
 * Tolti il 5 ott 2026, chiavi lasciate nei defaults per i template salvati:
 *  - il menu «Stile» (preset): i preset scrivevano background_color/text_color/shadow/border_radius,
 *    che il renderer del pulsante non legge (non cambiava niente);
 *  - il secondo controllo «Bordo» (`border`): sullo stesso pulsante ce n'erano due. Resta
 *    `btn_border` (ponte legacy su btn_border_width/btn_border_color); il PHP legge ancora
 *    `border` dove disegna e `btn_border` non è mai stato salvato (bordo_pulsante()).
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
      { value: 'chevron-down', label: t('Chevron giù') },
      { value: 'plus', label: t('Più') },
      { value: 'arrow-down', label: t('Freccia giù') },
      { value: 'eye', label: t('Occhio') },
    ]},
    { key: 'icon_hide', label: t('Icona nascondi'), type: 'select', options: [
      { value: '', label: t('Nessuna') },
      { value: 'chevron-up', label: t('Chevron su') },
      { value: 'minus', label: t('Meno') },
      { value: 'arrow-up', label: t('Freccia su') },
      { value: 'eye-off', label: t('Occhio chiuso') },
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
    { key: 'duration', label: t('Durata'), type: 'range', min: 100, max: 800, step: 50, unit: 'ms' },
  ],

  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    // withHover: l'occhio scrive sulla chiave legacy btn_hover_bg (formato dati invariato).
    withHover({ key: 'btn_bg', label: t('Sfondo'), type: 'color' }, { hoverKey: 'btn_hover_bg', defaultDuration: 200 }),

    { type: 'separator', label: t('Testo') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Pulsante'),
      responsiveKeys: [],
      keys: {
        size:   'btn_font_size',
        weight: 'btn_font_weight',
        color:  'btn_color',
      },
      sizeMin: 12, sizeMax: 24,
    },

    { type: 'separator', label: t('Forma') },
    withHover({ key: 'btn_border_radius', label: t('Raggio'), type: 'border-radius' }),
    { key: 'tile_padding', label: t('Padding'), type: 'spacing', max: 48 },
    { key: 'btn_full_width', label: t('Larghezza piena'), type: 'toggle' },

    // UN solo Bordo sul pulsante: btn_border, con hover (border_hover) ed effetti (border_effect_*).
    // Il ponte legacy lo inizializza da btn_border_width/btn_border_color (2px primario di serie)
    // e le tiene in sincronia.
    ...borderFields({ key: 'btn_border' }).map((f) => (f.key === 'btn_border'
      ? { ...f, legacyKeys: { width: 'btn_border_width', color: 'btn_border_color' } }
      : f)),

    { type: 'separator', label: t('Disposizione') },
    { key: 'btn_align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},
    { key: 'icon_position', label: t('Posizione icona'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'right', label: t('Destra') },
    ],
      // Senza icone non sposta niente.
      show: (s) => (s.icon_show ?? 'chevron-down') !== '' || (s.icon_hide ?? 'chevron-up') !== '' },
  ],
};
