import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Variable Specimen — playground per font variabili (rif. 66-tema-type-foundry.html).
 *
 *   fields[]      → fontFamily, sampleText, axes[] (repeater: tag/min/max/default),
 *                   interaction (drag/sliders/both), autoAnimate, autoSpeed, dragHint
 *   styleFields[] → tipografia gigante (size responsive, peso base, allineamento),
 *                   colori (testo / accento readout / sfondo), readout (mostra/nascondi),
 *                   shadow + sistema bordi
 *
 * Comportamento (runtime inline nel render PHP):
 *  - drag sulla lettera: X → primo asse, Y → secondo asse → font-variation-settings
 *  - slider nativi per ogni asse (aria-valuenow) con readout live
 *  - autoAnimate: loop sinusoidale quando idle; reduced-motion → niente loop
 *  - fallback: se gli assi non sono supportati dal browser → pesi statici predefiniti
 *
 * Chiavi salvate: additive, scoped per istanza lato render.
 */
export default {
  type: 'variablespecimen',
  name: t('Specimen Variabile'),
  icon: 'dashicons-editor-textcolor',
  category: 'text',
  defaults: {
    preset: 'custom',
    bg: { type: 'none' },
    typography_preset: '',

    // Contenuto
    font_family: '',                       // font variabile (CSS family name); vuoto = eredita
    sample_text: 'Ga',                     // testo/lettera gigante manipolabile
    interaction: 'both',                   // drag | sliders | both
    auto_animate: true,                    // loop sinusoidale a riposo
    auto_speed: '6',                       // velocità del loop auto (s/ciclo, indicativo)
    drag_hint: '↔ trascina · X = primo asse · Y = secondo asse',
    show_readout: true,                    // riga valori sotto la lettera

    // axes[] — repeater (content-items). Default: 2 assi tipici (Recursive).
    axes: [
      { tag: 'wght', label: 'Peso',          min: 300, max: 1000, default_val: 650 },
      { tag: 'slnt', label: 'Inclinazione',  min: -15, max: 0,    default_val: 0 },
    ],

    // Stile
    text_color: '',
    accent_color: '',                      // colore readout/slider/valori
    bg_color: '',
    font_size: '220',                      // px (desktop)
    font_weight_fallback: '700',           // peso statico se assi non supportati
    text_align: 'left',
    padding_y: '48',

    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'font_family', label: t('Font variabile (nome famiglia CSS)'), type: 'text',
      placeholder: t('es. Recursive, "Roboto Flex"'),
      description: t('Deve essere un font variabile già caricato nella pagina (Google Fonts o @font-face). Vuoto = eredita dal tema.') },
    { key: 'sample_text', label: t('Testo campione'), type: 'text',
      description: t('Lettera, parola o breve frase mostrata in grande. Modificabile anche inline nel canvas.') },

    { type: 'separator', label: t('Assi variabili') },
    { key: 'axes', label: t('Assi'), type: 'content-items',
      itemLabel: t('Asse'),
      defaults: { tag: 'wght', label: 'Peso', min: 0, max: 100, default_val: 50 },
      itemFields: [
        { key: 'tag',         label: t('Tag asse (4 lettere)'), type: 'text', placeholder: 'wght' },
        { key: 'label',       label: t('Etichetta'),            type: 'text', placeholder: t('Peso') },
        { key: 'min',         label: t('Minimo'),               type: 'number', step: 1 },
        { key: 'max',         label: t('Massimo'),              type: 'number', step: 1 },
        { key: 'default_val', label: t('Valore iniziale'),      type: 'number', step: 1 },
      ],
    },

    { type: 'separator', label: t('Interazione') },
    { key: 'interaction', label: t('Controllo'), type: 'select', options: [
      { value: 'drag',    label: t('Trascina la lettera (X/Y)') },
      { value: 'sliders', label: t('Solo slider') },
      { value: 'both',    label: t('Trascina + slider') },
    ]},
    { key: 'drag_hint', label: t('Suggerimento drag'), type: 'text',
      condition: { field: 'interaction', op: 'in', value: ['drag', 'both'] } },
    { key: 'auto_animate', label: t('Anima a riposo (loop)'), type: 'toggle',
      description: t('Quando nessuno interagisce, gli assi oscillano dolcemente. Rispetta prefers-reduced-motion (resta fermo).') },
    { key: 'auto_speed', label: t('Velocità loop (s)'), type: 'range', min: 2, max: 16, step: 1,
      condition: { field: 'auto_animate', op: 'eq', value: true } },
    { key: 'show_readout', label: t('Mostra valori (readout)'), type: 'toggle' },
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

    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Lettera campione'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:  'font_size',
        color: 'text_color',
      },
      sizeMin: 48, sizeMax: 560, sizeStep: 2,
    },
    { key: 'font_weight_fallback', label: t('Peso statico (fallback no-varfont)'), type: 'select', options: [
      { value: '300', label: '300' },
      { value: '400', label: '400' },
      { value: '500', label: '500' },
      { value: '600', label: '600' },
      { value: '700', label: '700' },
      { value: '900', label: '900' },
    ]},
    { key: 'text_align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left',   label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right',  label: t('Destra') },
    ]},

    { type: 'separator', label: t('Colori') },
    { key: 'accent_color', label: t('Colore accento (slider/valori)'), type: 'color' },
    { key: 'bg_color', label: t('Colore sfondo'), type: 'color' },

    { type: 'separator', label: t('Spaziatura') },
    { key: 'padding_y', label: t('Padding verticale (px)'), type: 'range', min: 0, max: 160, step: 4 },

    ...shadowField,
    ...borderFields(),
  ],
};
