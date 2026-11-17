import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Variable Specimen — playground per font variabili (rif. 66-tema-type-foundry.html).
 *
 *   fields[]      → sampleText, axes[] (nome/sigla/min/max/iniziale), comportamento (controllo, suggerimento,
 *                   movimento a riposo, durata dell'onda, valori)
 *   styleFields[] → Aspetto (stile, accento, sfondo, ombra) · Testo (stile tipografico, font variabile, misura e colore,
 *                   peso di riserva) · Disposizione (allineamento) · Forma (padding) · Bordo
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
  // «Variazione testo» (utente, 17 nov 2026): «Specimen» è gergo da fonderia; la chiave resta variablespecimen.
  name: t('Variazione testo'),
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
    font_size_tablet: '',                  // vuoto = come il computer
    font_size_mobile: '',
    font_weight_fallback: '700',           // peso statico se assi non supportati
    text_align: 'left',
    padding_y: '48',

    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // Appena nato: la coppia di lettere gigante che respira fra i pesi, gli slider dei due assi e
  // un suggerimento in parole semplici (prima: «X = primo asse · Y = secondo asse»).
  // Nasce con Recursive, il font variabile che ha proprio i due assi di partenza: col carattere del tema l'inclinazione
  // non faceva niente (pochi temi usano un font variabile con l'asse slnt). 17 nov 2026.
  partenza: {
    font_family: 'Recursive',
    sample_text: 'Aa',
    drag_hint: t('Trascina la lettera: in orizzontale cambia il peso, in verticale l\'inclinazione'),
  },

  // 17 nov 2026 (episodio 48): Contenuto = il testo, gli assi e il comportamento; Stile nelle zone Aspetto · Testo ·
  // Disposizione · Forma (il carattere è tipografia: dal Contenuto al Testo). Nomi in italiano, spiegazioni nelle (i),
  // quattro stili onesti al posto del menu di dodici nomi inglesi senza stili registrati. Chiavi invariate.
  fields: [
    { key: 'sample_text', label: t('Testo campione'), type: 'text',
      description: t('Lettera, parola o breve frase mostrata in grande. Si scrive anche direttamente nel canvas.') },

    { type: 'separator', label: t('Assi variabili') },
    { key: 'axes', label: t('Assi'), type: 'content-items',
      itemLabel: t('Asse'), etichettaDa: 'label',
      defaults: { tag: 'wght', label: t('Peso'), min: 0, max: 100, default_val: 50 },
      itemFields: [
        { key: 'label',       label: t('Nome'),             type: 'text', placeholder: t('Peso') },
        { key: 'tag',         label: t('Sigla'),            type: 'text', placeholder: 'wght', partenzaFissa: true,
          description: t('Le quattro lettere dell’asse nel font: wght peso, wdth larghezza, slnt inclinazione, ital corsivo, opsz misura ottica; gli assi propri del font sono in maiuscolo (CASL, MONO, GRAD).') },
        { key: 'min',         label: t('Minimo'),           type: 'number', step: 1 },
        { key: 'max',         label: t('Massimo'),          type: 'number', step: 1 },
        { key: 'default_val', label: t('Valore iniziale'),  type: 'number', step: 1 },
      ],
    },

    { type: 'separator', label: t('Comportamento') },
    { key: 'interaction', label: t('Controllo'), type: 'select', options: [
      { value: 'drag',    label: t('Trascina la lettera') },
      { value: 'sliders', label: t('Solo cursori') },
      { value: 'both',    label: t('Trascina e cursori') },
      { value: 'pressione', label: t('Pressione') },
    ], description: t('«Trascina»: in orizzontale si muove il primo asse, in verticale il secondo. «Pressione»: ogni lettera va verso il massimo dei suoi assi quando il puntatore (o il dito, scorrendo di lato) le passa vicino, e torna al valore iniziale con una molla; per un effetto elastico tieni basso il valore iniziale (per esempio larghezza 60 su 50–150).') },
    { key: 'drag_hint', label: t('Suggerimento'), type: 'text',
      condition: { field: 'interaction', op: 'in', value: ['drag', 'both'] } },
    { key: 'auto_animate', label: t('Si muove da solo a riposo'), type: 'toggle',
      description: t('Quando nessuno la tocca, gli assi oscillano piano (con «Pressione» un’onda attraversa la parola). Chi ha chiesto meno movimento al sistema la vede ferma.') },
    { key: 'auto_speed', label: t('Durata di un’onda'), type: 'range', unit: 's', min: 2, max: 16, step: 1,
      condition: { field: 'auto_animate', op: 'eq', value: true } },
    { key: 'show_readout', label: t('Mostra i valori'), type: 'toggle',
      condition: { field: 'interaction', op: 'neq', value: 'pressione' } },
  ],

  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'pulito',   label: t('Pulito') },
      { value: 'riquadro', label: t('Riquadro tenue') },
      { value: 'scuro',    label: t('Scuro') },
      { value: 'pieno',    label: t('Pieno') },
      { value: 'custom',   label: t('Personalizzato') },
    ] },
    { key: 'accent_color', label: t('Colore accento'), type: 'color',
      description: t('I cursori e i valori.') },
    { key: 'bg_color', label: t('Colore sfondo'), type: 'color' },
    ...shadowField,

    { type: 'separator', label: t('Testo') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { key: 'font_family', label: t('Font variabile'), type: 'font-family',
      placeholder: t('es. Recursive, "Roboto Flex"'),
      description: t('Un font variabile, per esempio Recursive o Roboto Flex: solo i font variabili cambiano davvero coi cursori. Vuoto: il carattere del tema.') },
    { type: 'typography', label: t('Testo campione'),
      responsiveKeys: ['size'],
      keys: {
        size:  'font_size',
        color: 'text_color',
      },
      sizeMin: 48, sizeMax: 560, sizeStep: 2,
    },
    { key: 'font_weight_fallback', label: t('Peso di riserva'), type: 'select', options: [
      { value: '300', label: '300' },
      { value: '400', label: '400' },
      { value: '500', label: '500' },
      { value: '600', label: '600' },
      { value: '700', label: '700' },
      { value: '900', label: '900' },
    ], description: t('Il peso fisso che si vede se il browser non sa variare il font.') },

    { type: 'separator', label: t('Disposizione') },
    { key: 'text_align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left',   label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right',  label: t('Destra') },
    ]},

    { type: 'separator', label: t('Forma') },
    { key: 'padding', label: t('Padding'), type: 'spacing', min: 0, max: 200,
      legacyKeys: { y: 'padding_y' } },

    ...borderFields(),
  ],
};
