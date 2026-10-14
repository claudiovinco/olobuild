import { ratioOptions } from './_imageFrame.js';
import { demo, demoAlt } from '../demoMedia.js';
import { t } from '@/i18n';

/**
 * Prova materiale — la foto di un materiale in alta risoluzione con la LENTE che ingrandisce dove passa il
 * puntatore, e i campioni per cambiare finitura o colore: piastrelle, marmi, tessuti, legni, vernici. Sul
 * telefono un tocco accende la lente, trascinando si sposta, un altro tocco la spegne; da tastiera Invio, le
 * frecce ed Esc. Il cambio di campione passa in dissolvenza e aggiorna foto, testo alternativo e testi.
 * Nata per i temi industriali della fase 3 (14 ott 2026). Renderer: includes/tiles/class-provamateriale-tile.php.
 *   fields[]      → campioni (foto, nome, codice, testo, miniatura) · lente (ingrandimento, suggerimento) · testi
 *   styleFields[] → Aspetto · Testo · Forma · Lente · Campioni (forma, misura, tinte) · Disposizione
 */
const conLente = { field: 'lente', op: 'eq', value: true };
const conSuggerimento = [conLente, { field: 'mostra_suggerimento', op: 'eq', value: true }];
const conTesti = { field: 'mostra_testi', op: 'eq', value: true };
const aLato = { field: 'disposizione', op: 'eq', value: 'lato' };
const piuCampioni = (s) => Array.isArray(s?.voci) && s.voci.length > 1;

// Il campione iniziale si sceglie per nome fra le voci (il valore salvato resta l'indice, da 0).
const campioniDi = (s) => (Array.isArray(s?.voci) ? s.voci : []).map((v, i) => ({
  value: i,
  label: String(v?.nome || '').trim() || t('Campione') + ' ' + (i + 1),
}));

const OMBRE = [
  { value: 'none', label: t('Nessuna') },
  { value: 'sm', label: t('Leggera') },
  { value: 'md', label: t('Media') },
  { value: 'lg', label: t('Forte') },
  { value: 'xl', label: t('Molto forte') },
  { value: 'custom', label: t('Personalizzata') },
];

export default {
  type: 'provamateriale',
  name: t('Prova materiale'),
  icon: 'dashicons-search',
  category: 'media',

  defaults: {
    voci: [
      { id: 'pm-1', nome: t('Smalto bianco'), codice: 'SM-01', testo: t('Smalto lucido steso a mano: la luce scivola sulle curve senza riflessi duri.'), immagine: '', miniatura: '', colore: '', alt: '' },
      { id: 'pm-2', nome: t('Blu cobalto'), codice: 'BC-07', testo: t('Porcellana blu col decoro dipinto a mano, cotta due volte.'), immagine: '', miniatura: '', colore: '', alt: '' },
      { id: 'pm-3', nome: t('Rovere naturale'), codice: 'RV-12', testo: t('Rovere spazzolato e oliato: la venatura resta in rilievo sotto le dita.'), immagine: '', miniatura: '', colore: '', alt: '' },
    ],
    iniziale: 0,
    lente: true,
    lente_zoom: 2.5,
    mostra_suggerimento: true,
    testo_suggerimento: t('Passa sopra la foto per vedere i dettagli'),
    testo_suggerimento_touch: t('Tocca la foto per ingrandirla'),
    mostra_testi: true,
    etichetta_campioni: t('Finitura'),

    accento: '',
    colore_testo: '',
    colore_tenue: '',
    sfondo_pannello: '',
    ombra: 'none',
    ombra_h: '0',
    ombra_v: '4',
    ombra_blur: '10',
    ombra_spread: '0',
    ombra_color: 'rgba(0,0,0,0.15)',
    ombra_inset: false,
    etichetta_font_family: '',
    etichetta_font_size: 13,
    etichetta_font_size_tablet: '',
    etichetta_font_size_mobile: '',
    etichetta_font_weight: '600',
    etichetta_uppercase: true,
    etichetta_letter_spacing: 1.2,
    etichetta_color: '',
    nome_tag: 'h3',
    nome_font_family: 'heading',
    nome_font_size: 26,
    nome_font_size_tablet: '',
    nome_font_size_mobile: '',
    nome_font_weight: '600',
    nome_line_height: 1.15,
    nome_color: '',
    codice_font_family: '',
    codice_font_size: 13,
    codice_font_size_tablet: '',
    codice_font_size_mobile: '',
    codice_font_weight: '600',
    codice_uppercase: true,
    codice_letter_spacing: 1.5,
    codice_color: '',
    testo_font_family: '',
    testo_font_size: 15,
    testo_font_size_tablet: '',
    testo_font_size_mobile: '',
    testo_font_weight: '400',
    testo_line_height: 1.6,
    testo_color: '',
    raggio_immagine: { tl: 14, tr: 14, br: 14, bl: 14 },
    proporzione: '4/3',
    lente_forma: 'cerchio',
    lente_misura: 180,
    campione_forma: 'pallino',
    campione_misura: 44,
    disposizione: 'sotto',
    lato: 'destra',
    larghezza_immagine: 62,
    gap: 32,
    allineamento: 'left',
  },

  // Appena nata: tre finiture con le foto demo (smalto, porcellana, rovere), i campioni accanto alla foto.
  partenza: {
    voci: [
      { id: 'pm-1', nome: t('Smalto bianco'), codice: 'SM-01', testo: t('Smalto lucido steso a mano: la luce scivola sulle curve senza riflessi duri.'), immagine: demo('vaso'), miniatura: '', colore: '', alt: demoAlt('vaso') },
      { id: 'pm-2', nome: t('Blu cobalto'), codice: 'BC-07', testo: t('Porcellana blu col decoro dipinto a mano, cotta due volte.'), immagine: demo('ciotola'), miniatura: '', colore: '', alt: demoAlt('ciotola') },
      { id: 'pm-3', nome: t('Rovere naturale'), codice: 'RV-12', testo: t('Rovere spazzolato e oliato: la venatura resta in rilievo sotto le dita.'), immagine: demo('tavola'), miniatura: '', colore: '', alt: demoAlt('tavola') },
    ],
    disposizione: 'lato',
  },

  fields: [
    { type: 'separator', label: t('Campioni') },
    { key: 'voci', label: t('Campioni'), type: 'content-items', itemLabel: t('Campione'), etichettaDa: 'nome', miniaturaDa: 'immagine',
      description: t('Una voce per finitura o colore: la foto grande, che la lente ingrandisce (caricala in alta risoluzione), il nome, il codice e una frase. Il campione da toccare mostra la miniatura, se c\'è; altrimenti la tinta scelta nello Stile, o la foto stessa.'),
      newItemDefaults: { nome: '', codice: '', testo: '', immagine: '', miniatura: '', colore: '', alt: '' },
      itemFields: [
        { key: 'nome', label: t('Nome'), type: 'text' },
        { key: 'codice', label: t('Codice'), type: 'text' },
        { key: 'testo', label: t('Testo'), type: 'textarea' },
        { key: 'immagine', label: t('Foto'), type: 'image' },
        { key: 'miniatura', label: t('Miniatura del campione'), type: 'image',
          description: t('Il campione da toccare, per esempio il dettaglio del tessuto. Vuota: la tinta scelta nello Stile, o la foto grande.') },
        { key: 'alt', label: t('Testo alternativo'), type: 'text',
          description: t('Cosa mostra la foto, per chi non la vede. Vuoto: il nome del campione.') },
      ] },
    { key: 'iniziale', label: t('Campione iniziale'), type: 'select', ui: 'dropdown',
      options: [{ value: 0, label: t('Il primo') }], optionsFn: campioniDi, show: piuCampioni,
      description: t('Quello che si vede quando si apre la pagina.') },

    { type: 'separator', label: t('Lente') },
    { key: 'lente', label: t('Lente d\'ingrandimento'), type: 'toggle',
      description: t('Al passaggio del puntatore la lente ingrandisce la foto. Sul telefono un tocco la accende, trascinando si sposta e un altro tocco la spegne. Da tastiera: Invio la accende, le frecce la spostano, Esc la chiude.') },
    { key: 'lente_zoom', label: t('Ingrandimento'), type: 'range', min: 1.5, max: 6, step: 0.5, unit: '×', condition: conLente },
    { key: 'mostra_suggerimento', label: t('Mostra il suggerimento'), type: 'toggle', condition: conLente,
      description: t('Una riga piccola sulla foto che dice come si usa la lente: sparisce appena la si usa.') },
    { key: 'testo_suggerimento', label: t('Suggerimento'), type: 'text', condition: conSuggerimento },
    { key: 'testo_suggerimento_touch', label: t('Suggerimento sul telefono'), type: 'text', condition: conSuggerimento },

    { type: 'separator', label: t('Testi') },
    { key: 'mostra_testi', label: t('Mostra nome, codice e testo'), type: 'toggle',
      description: t('Quelli del campione scelto, accanto o sotto ai campioni: cambiano con lui.') },
    { key: 'etichetta_campioni', label: t('Etichetta dei campioni'), type: 'text',
      description: t('Sopra i campioni: «Finitura», «Colore». Vuota: niente.') },
  ],

  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'accento', label: t('Colore accento'), type: 'color',
      description: t('L\'anello del campione scelto e il bordo della lente. Vuoto: il primario del tema.') },
    { key: 'colore_testo', label: t('Colore del testo'), type: 'color',
      description: t('Nome e testo del campione. Vuoto: il colore del testo della sezione.') },
    { key: 'colore_tenue', label: t('Colore tenue'), type: 'color',
      description: t('Etichetta e codice. Vuoto: il colore del testo, più chiaro.') },
    { key: 'sfondo_pannello', label: t('Sfondo pannello'), type: 'color',
      description: t('Il riquadro con i campioni e i testi. Vuoto: trasparente.') },
    { key: 'ombra', label: t('Ombra'), type: 'select', options: OMBRE,
      description: t('Sotto la foto, e sotto il pannello quando ha uno sfondo.') },
    { key: 'ombra_custom', label: t('Ombra personalizzata'), type: 'box-shadow',
      legacyKeys: { h: 'ombra_h', v: 'ombra_v', blur: 'ombra_blur', spread: 'ombra_spread', color: 'ombra_color', inset: 'ombra_inset' },
      condition: { field: 'ombra', op: 'eq', value: 'custom' } },

    { type: 'separator', label: t('Testo') },
    { type: 'typography', label: t('Etichetta'), responsiveKeys: ['size'], show: piuCampioni,
      keys: { family: 'etichetta_font_family', size: 'etichetta_font_size', weight: 'etichetta_font_weight', uppercase: 'etichetta_uppercase', letterSpacing: 'etichetta_letter_spacing', color: 'etichetta_color' },
      sizeMin: 11, sizeMax: 24 },
    { type: 'typography', label: t('Nome'), responsiveKeys: ['size'], condition: conTesti,
      keys: { tag: 'nome_tag', family: 'nome_font_family', size: 'nome_font_size', weight: 'nome_font_weight', lineHeight: 'nome_line_height', color: 'nome_color' },
      sizeMin: 14, sizeMax: 64, sizeStep: 1 },
    { type: 'typography', label: t('Codice'), responsiveKeys: ['size'], condition: conTesti,
      keys: { family: 'codice_font_family', size: 'codice_font_size', weight: 'codice_font_weight', uppercase: 'codice_uppercase', letterSpacing: 'codice_letter_spacing', color: 'codice_color' },
      sizeMin: 10, sizeMax: 24 },
    { type: 'typography', label: t('Testo del campione'), responsiveKeys: ['size'], condition: conTesti,
      keys: { family: 'testo_font_family', size: 'testo_font_size', weight: 'testo_font_weight', lineHeight: 'testo_line_height', color: 'testo_color' },
      sizeMin: 11, sizeMax: 28 },

    { type: 'separator', label: t('Forma') },
    { key: 'raggio_immagine', label: t('Raggio immagine'), type: 'border-radius' },
    { key: 'proporzione', label: t('Proporzioni'), type: 'select', options: ratioOptions({ auto: false }) },

    { type: 'separator', label: t('Lente'), condition: conLente },
    { key: 'lente_forma', label: t('Forma della lente'), type: 'select', options: [
      { value: 'cerchio', label: t('Cerchio') },
      { value: 'quadrato', label: t('Quadrato') },
    ] },
    { key: 'lente_misura', label: t('Misura della lente'), type: 'range', min: 100, max: 320, step: 10, unit: 'px' },

    { type: 'separator', label: t('Campioni'), show: piuCampioni },
    { key: 'campione_forma', label: t('Forma dei campioni'), type: 'select', options: [
      { value: 'pallino', label: t('Pallino') },
      { value: 'quadretto', label: t('Quadretto') },
      { value: 'pillola', label: t('Pillola col nome') },
    ] },
    { key: 'campione_misura', label: t('Misura dei campioni'), type: 'range', min: 28, max: 72, step: 2, unit: 'px' },
    { key: 'voci', label: t('Tinte dei campioni'), type: 'content-items', itemLabel: t('Campione'), etichettaDa: 'nome', miniaturaDa: 'immagine',
      itemFields: [
        { key: 'colore', label: t('Tinta del campione'), type: 'color',
          description: t('Un pallino pieno di questo colore al posto della foto, per i colori dei tessuti. Vale quando il campione non ha una miniatura.') },
      ] },

    { type: 'separator', label: t('Disposizione') },
    { key: 'disposizione', label: t('Disposizione'), type: 'select', options: [
      { value: 'sotto', label: t('Campioni sotto la foto') },
      { value: 'lato', label: t('Campioni accanto alla foto') },
    ], description: t('Accanto alla foto: sul telefono i campioni tornano sotto.') },
    { key: 'lato', label: t('Lato dei campioni'), type: 'select', options: [
      { value: 'destra', label: t('A destra') },
      { value: 'sinistra', label: t('A sinistra') },
    ], condition: aLato },
    { key: 'larghezza_immagine', label: t('Larghezza della foto'), type: 'range', min: 45, max: 75, step: 1, unit: '%', condition: aLato },
    { key: 'gap', label: t('Gap'), type: 'range', min: 0, max: 120, step: 2, unit: 'px',
      description: t('Fra la foto e il pannello dei campioni.') },
    { key: 'allineamento', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('A sinistra') },
      { value: 'center', label: t('Al centro') },
    ] },
  ],
};
