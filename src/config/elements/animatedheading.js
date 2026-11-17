import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile AnimatedHeading — split CONTENUTO/STILE.
 *   fields[]      → before_text, animated_words, after_text; comportamento: animation, typing_speed, pause_time
 *   styleFields[] → Aspetto (stile, evidenziazione, ombra) · Testo (tipografia del titolo e della parola) ·
 *                   Disposizione (allineamento) · Bordo
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
    font_size_tablet: '',
    font_size_mobile: '',
    font_weight: '700',
    font_family: '',
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

  // Appena nato: un titolo che alterna tre promesse, evidenziate da una pillola tinta del colore
  // primario che si scambia con una dissolvenza. Stile «Evidenziatore»: la parola resta nel colore del testo.
  partenza: {
    preset: 'evidenziatore',
    text_color: 'var(--olo-color-text)',
    animated_color: 'var(--olo-color-text)',
    highlight_color: 'var(--olo-color-primary)',
    before_text: t('Ti aiutiamo a'),
    animated_words: t('risparmiare tempo\nlavorare meglio\ncrescere davvero'),
    animation: 'highlight',
    highlight_style: 'background',
    font_size: '40',
  },

  // 17 nov 2026 (episodio 47): Contenuto = le parole e il comportamento; Stile nelle zone Aspetto · Testo ·
  // Disposizione. Gli stili sono cinque, coi colori del sito (prima i dodici generici in inglese, che scrivevano
  // sfondo e raggio: chiavi che il titolo non legge). Chiavi invariate.
  fields: [
    { key: 'before_text', label: t('Testo prima'), type: 'text' },
    { key: 'animated_words', label: t('Parole animate'), type: 'textarea',
      description: t('Una per riga: si alternano nello stesso punto, che resta largo quanto la più lunga, così il titolo non cambia forma.') },
    { key: 'after_text', label: t('Testo dopo'), type: 'text' },

    { type: 'separator', label: t('Comportamento') },
    { key: 'animation', label: t('Animazione'), type: 'select', options: [
      { value: 'typing', label: t('Digitazione') },
      { value: 'rotating', label: t('Scorre dal basso') },
      { value: 'fade', label: t('Dissolvenza') },
      { value: 'slide', label: t('Scorre di lato') },
      { value: 'highlight', label: t('Evidenziazione') },
      { value: 'clip', label: t('Tendina') },
    ]},
    { key: 'typing_speed', label: t('Ritmo della digitazione'), type: 'range', unit: 'ms', min: 50, max: 300, step: 10,
      description: t('Il tempo di ogni lettera: più è basso, più scrive in fretta. Cancella al doppio della velocità.'),
      condition: { field: 'animation', operator: '==', value: 'typing' } },
    { key: 'pause_time', label: t('Pausa fra le parole'), type: 'range', unit: 'ms', min: 500, max: 5000, step: 100 },
  ],

  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'pulito',        label: t('Pulito') },
      { value: 'evidenziatore', label: t('Evidenziatore') },
      { value: 'cerchiato',     label: t('Cerchiato') },
      { value: 'sobrio',        label: t('Sobrio') },
      { value: 'su-scuro',      label: t('Su fondo scuro') },
      { value: 'custom',        label: t('Personalizzato') },
    ] },
    { key: 'highlight_style', label: t('Evidenziazione'), type: 'select', options: [
      { value: 'underline', label: t('Sottolineatura') },
      { value: 'background', label: t('Sfondo') },
      { value: 'circle', label: t('Cerchio') },
      { value: 'strikethrough', label: t('Barrato') },
    ], condition: { field: 'animation', operator: '==', value: 'highlight' } },
    { key: 'highlight_color', label: t('Colore evidenziazione'), type: 'color',
      condition: { field: 'animation', operator: '==', value: 'highlight' } },
    ...shadowField,

    { type: 'separator', label: t('Testo') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Titolo'),
      responsiveKeys: ['size'],
      keys: {
        tag:    'tag',
        family: 'font_family',
        size:   'font_size',
        weight: 'font_weight',
        color:  'text_color',
      },
      sizeMin: 18, sizeMax: 80, sizeStep: 2,
    },
    { type: 'typography', label: t('Parola animata'),
      keys: {
        color: 'animated_color',
      },
    },

    { type: 'separator', label: t('Disposizione') },
    { key: 'alignment', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},

    ...borderFields(),
  ],
};
