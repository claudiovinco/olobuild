import { shadowField } from './_shared.js';
import { t } from '@/i18n';

/**
 * Pianta della sala — la sala di un teatro disegnata da pochi numeri: palcoscenico, buca dell'orchestra, platea a file
 * di posti e, attorno, gli ordini di palchi (l'ultimo può essere il loggione), a ferro di cavallo, a ventaglio o a
 * scatola. I settori di prezzo raccolgono zone della sala (file della platea, palchi centrali o laterali di ogni
 * ordine, barcacce, loggione): passando o toccando un settore gli altri si attenuano e la scheda mostra prezzi, nota,
 * descrizione, la foto della vista dal posto e un link. Nata per il tema Teatro della Seta (13 ott 2026).
 * Renderer: includes/tiles/class-piantasala-tile.php.
 *   fields[]      → sala · settori · testi
 *   styleFields[] → Aspetto (colori della sala, colori dei settori, scheda) · Testo · Forma · Disposizione
 */
const ZONE = [
  { value: 'platea-a', label: t('Platea, file davanti') },
  { value: 'platea-b', label: t('Platea, file dietro') },
  { value: 'o1-c', label: t('I ordine, palchi centrali') },
  { value: 'o1-l', label: t('I ordine, palchi laterali') },
  { value: 'o2-c', label: t('II ordine, palchi centrali') },
  { value: 'o2-l', label: t('II ordine, palchi laterali') },
  { value: 'o3-c', label: t('III ordine, palchi centrali') },
  { value: 'o3-l', label: t('III ordine, palchi laterali') },
  { value: 'o4-c', label: t('IV ordine, palchi centrali') },
  { value: 'o4-l', label: t('IV ordine, palchi laterali') },
  { value: 'o5-c', label: t('V ordine, palchi centrali') },
  { value: 'o5-l', label: t('V ordine, palchi laterali') },
  { value: 'barcacce', label: t('Barcacce (palchi accanto al palcoscenico)') },
  { value: 'loggione', label: t('Loggione') },
];

export default {
  type: 'piantasala',
  name: t('Pianta della sala'),
  icon: 'dashicons-layout',
  category: 'interactive',

  defaults: {
    forma: 'ferro',
    palco_testo: t('Palcoscenico'),
    buca: true,
    buca_testo: t('Buca'),
    file: 14,
    posti_fila: 24,
    ordini: 4,
    palchi: 19,
    loggione: true,
    palco_centrale: true,
    numeri_ordini: true,
    iniziale: 'primo',
    testo_invito: t('Passa o tocca un settore: prezzi e vista dal posto.'),
    testo_intero: t('Intero'),
    testo_ridotto: t('Ridotto'),
    testo_vista: t('La vista da qui'),
    testo_link: t('Scegli il posto'),
    settori: [
      { id: 'set-1', nome: t('Platea e palchi centrali'), zone: 'platea-a,platea-b,o1-c,o2-c', prezzo: '25 €', ridotto: '20 €', nota: '', descrizione: '', vista: '', link: '', colore: '' },
      { id: 'set-2', nome: t('Palchi laterali'), zone: 'o1-l,o2-l,o3-c,o3-l,barcacce', prezzo: '20 €', ridotto: '16 €', nota: '', descrizione: '', vista: '', link: '', colore: '' },
      { id: 'set-3', nome: t('Loggione'), zone: 'loggione,o4-c,o4-l', prezzo: '14 €', ridotto: '10 €', nota: '', descrizione: '', vista: '', link: '', colore: '' },
    ],

    disposizione: 'lato',
    larghezza_pianta: 58,
    gap: 40,
    colore_sala: '',
    colore_palco: '',
    colore_posti: '',
    colore_etichette: '',
    sfondo_scheda: '',
    bordo_scheda: {},
    raggio: { tl: 16, tr: 16, br: 16, bl: 16 },
    padding_scheda: { top: 32, right: 32, bottom: 32, left: 32 },
    shadow: 'none',
    nome_font_family: 'heading',
    nome_font_size: 30,
    nome_font_weight: '400',
    nome_color: '',
    prezzo_font_family: 'heading',
    prezzo_font_size: 34,
    prezzo_color: '',
    descrizione_font_size: 15,
    descrizione_line_height: 1.6,
    descrizione_color: '',
    legenda_font_size: 14,
    legenda_font_weight: '500',
  },

  fields: [
    { type: 'separator', label: t('Sala') },
    { key: 'forma', label: t('Forma della sala'), type: 'select', options: [
      { value: 'ferro', label: t('A ferro di cavallo (all\'italiana)') },
      { value: 'ventaglio', label: t('A ventaglio') },
      { value: 'rettangolo', label: t('A scatola') },
    ]},
    { key: 'palco_testo', label: t('Scritta del palcoscenico'), type: 'text' },
    { key: 'buca', label: t('Buca dell\'orchestra'), type: 'toggle' },
    { key: 'buca_testo', label: t('Scritta della buca'), type: 'text', condition: { field: 'buca', op: 'eq', value: true } },
    { key: 'file', label: t('File della platea'), type: 'range', min: 4, max: 30, step: 1 },
    { key: 'posti_fila', label: t('Posti nella fila più larga'), type: 'range', min: 8, max: 44, step: 1 },
    { key: 'ordini', label: t('Ordini di palchi'), type: 'range', min: 0, max: 5, step: 1 },
    { key: 'palchi', label: t('Palchi per ordine'), type: 'range', min: 5, max: 41, step: 1,
      condition: { field: 'ordini', op: 'gt', value: 0 } },
    { key: 'loggione', label: t('L\'ultimo ordine è il loggione'), type: 'toggle',
      condition: { field: 'ordini', op: 'gt', value: 0 } },
    { key: 'palco_centrale', label: t('Palco centrale più grande'), type: 'toggle',
      condition: { field: 'ordini', op: 'gt', value: 0 },
      description: t('Il palco di mezzo del primo ordine prende il posto di tre: il palco d\'onore.') },
    { key: 'numeri_ordini', label: t('Numeri degli ordini'), type: 'toggle',
      condition: { field: 'ordini', op: 'gt', value: 0 } },

    { type: 'separator', label: t('Settori') },
    { key: 'settori', label: t('Settori'), type: 'content-items', itemLabel: t('Settore'), etichettaDa: 'nome', miniaturaDa: 'vista',
      description: t('Ogni zona della sala va al primo settore che la sceglie. Le zone senza settore restano neutre.'),
      newItemDefaults: { nome: '', zone: '', prezzo: '', ridotto: '', nota: '', descrizione: '', vista: '', link: '', colore: '' },
      itemFields: [
        { key: 'nome', label: t('Nome'), type: 'text' },
        { key: 'zone', label: t('Zone della sala'), type: 'multi_pills', options: ZONE },
        { key: 'prezzo', label: t('Prezzo intero'), type: 'text' },
        { key: 'ridotto', label: t('Prezzo ridotto'), type: 'text' },
        { key: 'nota', label: t('Nota sui prezzi'), type: 'text' },
        { key: 'descrizione', label: t('Descrizione'), type: 'textarea' },
        { key: 'vista', label: t('Vista dal posto'), type: 'image' },
        { key: 'link', label: t('Link'), type: 'link' },
      ] },
    { key: 'iniziale', label: t('All\'inizio la scheda mostra'), type: 'select', options: [
      { value: 'primo', label: t('Il primo settore') },
      { value: 'nessuno', label: t('L\'invito a scegliere') },
    ]},

    { type: 'separator', label: t('Testi') },
    { key: 'testo_invito', label: t('Invito'), type: 'text' },
    { key: 'testo_intero', label: t('Etichetta del prezzo intero'), type: 'text' },
    { key: 'testo_ridotto', label: t('Etichetta del prezzo ridotto'), type: 'text' },
    { key: 'testo_vista', label: t('Etichetta della vista'), type: 'text' },
    { key: 'testo_link', label: t('Testo del link'), type: 'text' },
  ],

  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'colore_sala', label: t('Colore della sala'), type: 'color',
      description: t('I palchi e le zone senza settore. Vuoto: il colore del testo, tenue.') },
    { key: 'colore_palco', label: t('Colore del palcoscenico'), type: 'color' },
    { key: 'colore_posti', label: t('Colore dei posti senza settore'), type: 'color' },
    { key: 'colore_etichette', label: t('Colore delle scritte'), type: 'color' },
    { key: 'settori', type: 'content-items', label: t('Colori dei settori'), itemLabel: t('Settore'), etichettaDa: 'nome', itemFields: [
        { key: 'colore', label: t('Colore'), type: 'color' },
    ] },
    { key: 'sfondo_scheda', label: t('Sfondo scheda'), type: 'color' },
    { key: 'bordo_scheda', label: t('Bordo scheda'), type: 'border' },
    ...shadowField,

    { type: 'separator', label: t('Testo') },
    { type: 'typography', label: t('Nome del settore'), responsiveKeys: [],
      keys: { family: 'nome_font_family', size: 'nome_font_size', weight: 'nome_font_weight', color: 'nome_color' }, sizeMin: 14, sizeMax: 64 },
    { type: 'typography', label: t('Prezzi'), responsiveKeys: [],
      keys: { family: 'prezzo_font_family', size: 'prezzo_font_size', color: 'prezzo_color' }, sizeMin: 14, sizeMax: 80 },
    { type: 'typography', label: t('Descrizione'), responsiveKeys: [],
      keys: { size: 'descrizione_font_size', lineHeight: 'descrizione_line_height', color: 'descrizione_color' }, sizeMin: 11, sizeMax: 28 },
    { type: 'typography', label: t('Legenda'), responsiveKeys: [],
      keys: { size: 'legenda_font_size', weight: 'legenda_font_weight' }, sizeMin: 10, sizeMax: 24 },

    { type: 'separator', label: t('Forma') },
    { key: 'raggio', label: t('Raggio scheda'), type: 'border-radius' },
    { key: 'padding_scheda', label: t('Padding scheda'), type: 'spacing', max: 96 },

    { type: 'separator', label: t('Disposizione') },
    { key: 'disposizione', label: t('Scheda'), type: 'select', options: [
      { value: 'lato', label: t('Accanto alla pianta') },
      { value: 'sotto', label: t('Sotto la pianta') },
    ]},
    { key: 'larghezza_pianta', label: t('Larghezza della pianta'), type: 'range', min: 30, max: 75, step: 1, unit: '%',
      condition: { field: 'disposizione', op: 'eq', value: 'lato' } },
    { key: 'gap', label: t('Gap'), type: 'range', min: 0, max: 120, step: 4, unit: 'px' },
  ],
};
