import { t } from '@/i18n';

/**
 * Pellicole — una foto e le pellicole con cui «svilupparla»: i rullini da scegliere (barattolo col colore della
 * pellicola, nome, nota) e la foto divisa da un divisore da trascinare, lo scatto grezzo a sinistra e lo sviluppo a
 * destra. Ogni look è una ricetta (filtri, tinta, vignettatura, grana; il digicam ha la data arancione nell'angolo).
 * Nata per il tema di uno studio fotografico attento alle mode (27 ott 2026).
 * Renderer: includes/tiles/class-pellicole-tile.php.
 *   fields[]      → foto · pellicole · confronto · cosa mostrare
 *   styleFields[] → Aspetto (colori, grana) · Testo · Forma (proporzione, raggi) · Disposizione
 */
const LOOK = [
  { value: 'portra', label: t('Portra — pelle morbida, verdi caldi') },
  { value: 'ektar', label: t('Ektar — colori saturi, rossi accesi') },
  { value: 'cinestill', label: t('Cinestill — luci della sera, alone rosso') },
  { value: 'fuji', label: t('Fuji — verdi freschi, luce piatta') },
  { value: 'bn', label: t('Bianco e nero di grana') },
  { value: 'seppia', label: t('Seppia d\'archivio') },
  { value: 'flash', label: t('Flash diretto — lampo e angoli scuri') },
  { value: 'burro', label: t('Toni burro — giallo morbido') },
  { value: 'notte', label: t('Notte cinematografica — ottanio e arancio') },
  { value: 'digicam', label: t('Digicam anni 2000 — con la data') },
  { value: 'personalizzata', label: t('Personalizzata — i valori nello Stile') },
];
const PROPRIA = { field: 'look', op: 'eq', value: 'personalizzata' };

export default {
  type: 'pellicole',
  name: t('Pellicole'),
  icon: 'dashicons-camera',
  category: 'interactive',

  defaults: {
    immagine: '',
    alt: '',
    pellicole: [
      { nome: 'Portra 400', nota: t('La pelle morbida, i verdi caldi'), look: 'portra', colore: '' },
      { nome: 'Cinestill 800T', nota: t('Le luci della sera, l’alone rosso'), look: 'cinestill', colore: '' },
      { nome: 'HP5 Plus', nota: t('Bianco e nero di grana'), look: 'bn', colore: '' },
      { nome: t('Flash diretto'), nota: t('Il lampo in faccia, gli angoli scuri'), look: 'flash', colore: '' },
      { nome: t('Toni burro'), nota: t('Giallo morbido, contrasto basso'), look: 'burro', colore: '' },
    ],
    partenza: 1,
    confronto: true,
    divisore: 50,
    etichetta_grezzo: t('Scatto grezzo'),
    mostra_nota: true,
    mostra_etichette: true,
    data_digicam: '',
    grana: 30,

    colore_accento: '',
    colore_divisore: '',
    sfondo_rullino: '',
    nome_font_family: '',
    nome_font_size: 15,
    nome_font_weight: '600',
    nome_color: '',
    nota_font_family: '',
    nota_font_size: 13,
    nota_font_weight: '',
    nota_color: '',
    proporzione: '3/2',
    raggio: { tl: 10, tr: 10, br: 10, bl: 10 },
    raggio_rullino: { tl: 10, tr: 10, br: 10, bl: 10 },
    posizione_rullini: 'sotto',
    gap: 18,
  },

  fields: [
    { type: 'separator', label: t('Foto') },
    { key: 'immagine', label: t('Foto'), type: 'image' },
    { key: 'alt', label: t('Testo alternativo'), type: 'text' },

    { type: 'separator', label: t('Pellicole') },
    { key: 'pellicole', label: t('Pellicole'), type: 'content-items', itemLabel: t('Pellicola'), etichettaDa: 'nome',
      description: t('Ogni rullino è un look applicato alla foto: scegli la ricetta e dagli il nome che vuoi (una pellicola, un tuo preset, una moda).'),
      newItemDefaults: { nome: '', nota: '', look: 'portra', colore: '', luminosita: 100, contrasto: 100, saturazione: 100, seppia: 0, tonalita: 0, tinta: '', tinta_opacita: 30, vignetta: 0 },
      itemFields: [
        { key: 'nome', label: t('Nome'), type: 'text' },
        { key: 'nota', label: t('Nota'), type: 'text' },
        { key: 'look', label: t('Ricetta'), type: 'select', options: LOOK },
      ] },
    { key: 'partenza', label: t('Pellicola di partenza'), type: 'number', min: 1, max: 12 },

    { type: 'separator', label: t('Confronto') },
    { key: 'confronto', label: t('Confronto col grezzo'), type: 'toggle',
      description: t('Il divisore da trascinare: a sinistra lo scatto com\'è uscito, a destra lo sviluppo. La prima volta che la foto si vede, scorre da solo a metà. Spento: lo sviluppo copre tutta la foto.') },
    { key: 'divisore', label: t('Posizione del divisore'), type: 'range', min: 0, max: 100, step: 1, unit: '%',
      condition: { field: 'confronto', op: 'eq', value: true } },
    { key: 'etichetta_grezzo', label: t('Etichetta del grezzo'), type: 'text',
      condition: { field: 'confronto', op: 'eq', value: true } },
    { key: 'mostra_etichette', label: t('Mostra le etichette sulla foto'), type: 'toggle',
      condition: { field: 'confronto', op: 'eq', value: true } },
    { key: 'mostra_nota', label: t('Mostra la nota dei rullini'), type: 'toggle' },
    { key: 'data_digicam', label: t('Data stampata dal digicam'), type: 'text', placeholder: '\'26 10 27',
      description: t('Compare nell\'angolo con la ricetta «Digicam». Vuota: la data di oggi.') },
  ],

  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'colore_accento', label: t('Colore accento'), type: 'color',
      description: t('Il bordo del rullino scelto e l\'anello del divisore col fuoco. Vuoto: il primario del tema.') },
    { key: 'colore_divisore', label: t('Colore del divisore'), type: 'color',
      description: t('La linea e la maniglia tonda. Vuoto: bianco.') },
    { key: 'sfondo_rullino', label: t('Sfondo dei rullini'), type: 'color' },
    { key: 'pellicole', label: t('Pellicole'), type: 'content-items', etichettaDa: 'nome',
      itemFields: [
        { key: 'colore', label: t('Colore del barattolo'), type: 'color',
          description: t('La fascia sul barattolo del rullino. Vuoto: il colore tipico della ricetta.') },
        { type: 'separator', label: t('Ricetta personalizzata'), condition: PROPRIA },
        { key: 'luminosita', label: t('Luminosità'), type: 'range', min: 40, max: 160, step: 1, unit: '%', condition: PROPRIA },
        { key: 'contrasto', label: t('Contrasto'), type: 'range', min: 40, max: 160, step: 1, unit: '%', condition: PROPRIA },
        { key: 'saturazione', label: t('Saturazione'), type: 'range', min: 0, max: 220, step: 1, unit: '%', condition: PROPRIA },
        { key: 'seppia', label: t('Seppia'), type: 'range', min: 0, max: 100, step: 1, unit: '%', condition: PROPRIA },
        { key: 'tonalita', label: t('Tonalità'), type: 'range', min: -180, max: 180, step: 1, unit: '°', condition: PROPRIA },
        { key: 'tinta', label: t('Tinta'), type: 'color', condition: PROPRIA,
          description: t('Un colore fuso sopra la foto in modo morbido: caldo per l\'olio, scuro per un mordente.') },
        { key: 'tinta_opacita', label: t('Forza della tinta'), type: 'range', min: 0, max: 100, step: 1, unit: '%', condition: PROPRIA },
        { key: 'vignetta', label: t('Vignettatura'), type: 'range', min: 0, max: 100, step: 1, unit: '%', condition: PROPRIA },
      ] },
    { key: 'grana', label: t('Grana'), type: 'range', min: 0, max: 100, step: 5, unit: '%',
      description: t('La grana di pellicola sopra lo sviluppo. 0: niente grana.') },

    { type: 'separator', label: t('Testo') },
    { type: 'typography', label: t('Nome dei rullini'),
      keys: { family: 'nome_font_family', size: 'nome_font_size', weight: 'nome_font_weight', color: 'nome_color' },
      sizeMin: 11, sizeMax: 24 },
    { type: 'typography', label: t('Nota dei rullini'),
      keys: { family: 'nota_font_family', size: 'nota_font_size', weight: 'nota_font_weight', color: 'nota_color' },
      sizeMin: 10, sizeMax: 20 },

    { type: 'separator', label: t('Forma') },
    { key: 'proporzione', label: t('Proporzione foto'), type: 'select', options: [
      { value: '3/2', label: '3:2' },
      { value: '4/3', label: '4:3' },
      { value: '16/9', label: '16:9' },
      { value: '1/1', label: '1:1' },
      { value: '4/5', label: '4:5' },
      { value: '2/3', label: '2:3' },
    ] },
    { key: 'raggio', label: t('Raggio foto'), type: 'border-radius' },
    { key: 'raggio_rullino', label: t('Raggio rullini'), type: 'border-radius' },

    { type: 'separator', label: t('Disposizione') },
    { key: 'posizione_rullini', label: t('Posizione dei rullini'), type: 'select', options: [
      { value: 'sotto', label: t('Sotto la foto') },
      { value: 'sopra', label: t('Sopra la foto') },
    ] },
    { key: 'gap', label: t('Gap'), type: 'range', min: 0, max: 80, step: 2, unit: 'px' },
  ],
};
