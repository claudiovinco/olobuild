import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared';
import { t } from '@/i18n';

/**
 * Tile TextPath — split CONTENUTO/STILE.
 *   fields[]      → text, animazione, rotazione
 *   styleFields[] → Testo (tipografia, effetti), Forma (tracciato, percorso SVG), bordo
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
    font_family: '',
    font_size: '24',
    font_weight: '',
    text_transform: '',
    font_italic: false,
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

  // Appena nato: una frase breve sull'arco, nel colore primario, che riempie il tracciato senza
  // essere tagliata (quella di prima era più lunga del tracciato e si interrompeva a metà).
  partenza: {
    text: t('Fatto a mano, con cura'),
    font_size: '22',
    text_color: 'var(--olo-color-primary)',
  },

  // 16 nov 2026 (episodio 33): tolto il menu «Stile» (dodici nomi inglesi dei preset base che scrivevano sfondo, ombra e
  // raggio: il renderer non li legge, agiva solo il colore); etichette senza parentesi, la spiegazione nelle (i); il
  // percorso SVG nella Forma accanto al tracciato; al «Testo» carattere, peso, maiuscolo e corsivo, letti dal renderer.
  fields: [
    { key: 'text', label: t('Testo'), type: 'text' },

    { type: 'separator', label: t('Animazione') },
    { key: 'animation', label: t('Movimento'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'scroll', label: t('Scorrimento una volta') },
      { value: 'continuous', label: t('Scorrimento continuo') },
    ], description: t('Una volta: il testo entra dalla fine del tracciato quando la tile si vede, e si ferma. Continuo: la frase si ripete e scorre senza fine. Chi nel sistema ha chiesto meno movimento lo vede fermo.') },
    { key: 'animation_speed', label: t('Durata'), type: 'range', min: 1, max: 20, unit: 's',
      condition: { field: 'animation', value: ['scroll', 'continuous'] } },

    { type: 'separator', label: t('Rotazione') },
    { key: 'spin', label: t('Ruota tutto'), type: 'toggle',
      description: t('Il testo gira in continuazione col suo tracciato, come un timbro ad anello: col Cerchio è perfetto. Chi nel sistema ha chiesto meno movimento lo vede fermo.') },
    { key: 'spin_speed', label: t('Durata di un giro'), type: 'range', min: 3, max: 40, step: 1, unit: 's',
      condition: { field: 'spin', op: 'eq', value: true } },
    { key: 'spin_direction', label: t('Direzione'), type: 'select', options: [
      { value: 'cw',  label: t('Oraria') },
      { value: 'ccw', label: t('Antioraria') },
    ], condition: { field: 'spin', op: 'eq', value: true } },
  ],

  styleFields: [
    { type: 'separator', label: t('Testo') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Testo'),
      responsiveKeys: [],
      keys: {
        family:        'font_family',
        size:          'font_size',
        weight:        'font_weight',
        letterSpacing: 'letter_spacing',
        transform:     'text_transform',
        italic:        'font_italic',
        color:         'text_color',
      },
      sizeMin: 12, sizeMax: 72,
    },
    ...textEffectsFields([ { value: 'text', label: t('Solo Testo') } ]),

    { type: 'separator', label: t('Forma') },
    { key: 'path_preset', label: t('Tracciato'), type: 'select', options: [
      { value: 'arc', label: t('Arco') },
      { value: 'wave', label: t('Onda') },
      { value: 'circle', label: t('Cerchio') },
      { value: 'spiral', label: t('Spirale') },
      { value: 'custom', label: t('Personalizzato') },
    ]},
    { key: 'custom_path', label: t('Percorso SVG'), type: 'text', placeholder: t('M 0 50 Q 150 0 300 50'),
      condition: { field: 'path_preset', value: 'custom' },
      description: t('Il tracciato in formato SVG, cioè l’attributo «d» di un path, in un riquadro largo 300 e alto 100: per esempio M 0 50 Q 150 0 300 50.') },

    ...borderFields(),
  ],
};
