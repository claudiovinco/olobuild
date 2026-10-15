import { ratioOptions } from './_imageFrame.js';
import { t } from '@/i18n';

/**
 * Tour 360 — foto sferiche equirettangolari da guardare trascinando, con le scene collegate fra loro e i punti
 * dentro le foto: «Scena» porta a un'altra scena (dissolvenza con un passo verso il punto), «Informazioni» apre un
 * riquadro con un testo, «Collegamento» apre un indirizzo. Disegno in WebGL senza librerie (ripiego: la foto che
 * scorre in orizzontale), zoom con due dita, Ctrl + rotellina, + e −; rotazione automatica che si ferma alla prima
 * interazione; miniature delle scene, schermo intero, suggerimento che sparisce al primo trascinamento. Nel builder,
 * senza foto, una vista d'esempio disegnata nel browser e la lettura di direzione e inclinazione per piazzare i punti.
 * Nata per i temi della serie 6 (15 ott 2026). Renderer: includes/tiles/class-tour360-tile.php.
 *   fields[]      → scene · punti · vista (campo visivo, zoom, titolo, elenco, schermo intero) · rotazione · testi
 *   styleFields[] → Vista (proporzioni, raggio, bordo, ombra) · Punti · Etichette · Pannello · Controlli
 */
const conAuto = { field: 'autorotazione', op: 'eq', value: true };
const conAiuto = { field: 'mostra_suggerimento', op: 'eq', value: true };
const tipoScena = { field: 'tipo', op: 'eq', value: 'scena' };
const piuScene = (s) => Array.isArray(s?.scene) && s.scene.length > 1;

// La scena iniziale si sceglie per titolo; il valore salvato è l'id della scena (o il suo numero, se non ha id).
const sceneDi = (s) => [{ value: '', label: t('La prima') }].concat((Array.isArray(s?.scene) ? s.scene : []).map((v, i) => ({
  value: String(v?.id || i + 1),
  label: String(v?.titolo || '').trim() || t('Scena') + ' ' + (i + 1),
})));

const OMBRE = [
  { value: 'none', label: t('Nessuna') },
  { value: 'sm', label: t('Leggera') },
  { value: 'md', label: t('Media') },
  { value: 'lg', label: t('Forte') },
  { value: 'xl', label: t('Molto forte') },
  { value: 'custom', label: t('Personalizzata') },
];

export default {
  type: 'tour360',
  name: t('Tour 360°'),
  icon: 'dashicons-image-rotate',
  category: 'media',

  defaults: {
    scene: [
      { id: 'ingresso', titolo: t('Ingresso'), immagine: '', immagine_leggera: '', anteprima: '', yaw: 0, pitch: 0, descrizione: t('La porta si apre su un corridoio luminoso col pavimento in rovere.') },
      { id: 'soggiorno', titolo: t('Soggiorno'), immagine: '', immagine_leggera: '', anteprima: '', yaw: 0, pitch: 0, descrizione: t('Il divano guarda la vetrata sul giardino; la cucina è a vista, dietro la penisola.') },
    ],
    punti: [
      { id: 'p-1', scena: 'ingresso', yaw: 32, pitch: -6, tipo: 'scena', destinazione: 'soggiorno', titolo: t('Vai al soggiorno'), testo: '', url: '', icona: '' },
      { id: 'p-2', scena: 'ingresso', yaw: -58, pitch: -18, tipo: 'info', destinazione: '', titolo: t('Pavimento in rovere'), testo: t('Doghe larghe 20 cm, oliate a mano: scaldano la casa anche d\'inverno.'), url: '', icona: '' },
      { id: 'p-3', scena: 'soggiorno', yaw: 180, pitch: -6, tipo: 'scena', destinazione: 'ingresso', titolo: t('Torna all\'ingresso'), testo: '', url: '', icona: '' },
      { id: 'p-4', scena: 'soggiorno', yaw: 64, pitch: 8, tipo: 'info', destinazione: '', titolo: t('Vetrata sul giardino'), testo: t('Tre metri di vetro basso emissivo, esposti a sud: luce piena fino al tramonto.'), url: '', icona: '' },
    ],
    scena_iniziale: '',
    fov: 75,
    zoom: true,
    mostra_titolo: true,
    elenco_scene: true,
    schermo_intero: true,
    autorotazione: true,
    velocita: 3,
    ripresa: 6,
    mostra_suggerimento: true,
    testo_suggerimento: t('Trascina per guardarti intorno'),
    testo_suggerimento_touch: t('Scorri col dito per guardarti intorno'),
    testo_caricamento: t('Carico la vista…'),

    proporzione: '16/9',
    proporzione_mobile: '4/5',
    raggio: { tl: 14, tr: 14, br: 14, bl: 14 },
    bordo: {},
    ombra: 'none',
    ombra_h: '0',
    ombra_v: '4',
    ombra_blur: '10',
    ombra_spread: '0',
    ombra_color: 'rgba(0,0,0,0.15)',
    ombra_inset: false,
    stile_punto: 'anello',
    colore_punto: '',
    misura_punto: 40,
    sfondo_etichetta: '',
    colore_etichetta: '',
    etichetta_font_family: '',
    etichetta_font_size: 13,
    etichetta_font_size_tablet: '',
    etichetta_font_size_mobile: '',
    etichetta_font_weight: '600',
    etichetta_color: '',
    sfondo_pannello: '',
    colore_pannello: '',
    titolo_font_family: 'heading',
    titolo_font_size: 19,
    titolo_font_size_tablet: '',
    titolo_font_size_mobile: '',
    titolo_font_weight: '600',
    titolo_color: '',
    testo_font_family: '',
    testo_font_size: 15,
    testo_font_size_tablet: '',
    testo_font_size_mobile: '',
    testo_font_weight: '400',
    testo_line_height: 1.55,
    testo_color: '',
    sfondo_comandi: '',
    colore_comandi: '',
  },

  fields: [
    { type: 'separator', label: t('Scene') },
    { key: 'scene', label: t('Scene'), type: 'content-items', itemLabel: t('Scena'), etichettaDa: 'titolo', miniaturaDa: 'anteprima',
      description: t('Una voce per ogni foto sferica: equirettangolare, larga il doppio dell\'altezza (per esempio 4096 × 2048, in JPG). La foto leggera (2048 × 1024) va ai telefoni e alle schede grafiche più piccole; l\'anteprima è la miniatura nell\'elenco delle scene. Direzione e inclinazione dicono dove guarda la vista quando la scena si apre.'),
      newItemDefaults: { titolo: '', immagine: '', immagine_leggera: '', anteprima: '', yaw: 0, pitch: 0, descrizione: '' },
      itemFields: [
        { key: 'titolo', label: t('Titolo'), type: 'text' },
        { key: 'immagine', label: t('Foto sferica'), type: 'image',
          description: t('Equirettangolare 2:1, per esempio 4096 × 2048. Le foto piatte si vedrebbero stirate.') },
        { key: 'immagine_leggera', label: t('Foto leggera'), type: 'image',
          description: t('La stessa foto a 2048 × 1024: la usano i telefoni e le schede grafiche che non reggono le foto grandi. Vuota: la foto grande, rimpicciolita nel browser quando serve.') },
        { key: 'anteprima', label: t('Anteprima'), type: 'image',
          description: t('La miniatura nell\'elenco delle scene. Vuota: la foto leggera.') },
        { key: 'yaw', label: t('Direzione'), type: 'range', min: -180, max: 180, step: 1,
          description: t('In gradi: 0 è il centro della foto, 90 un quarto di giro a destra, −90 a sinistra.') },
        { key: 'pitch', label: t('Inclinazione'), type: 'range', min: -85, max: 85, step: 1,
          description: t('In gradi: positiva guarda in alto, negativa in basso.') },
        { key: 'descrizione', label: t('Descrizione'), type: 'textarea' },
      ] },
    { key: 'scena_iniziale', label: t('Scena iniziale'), type: 'select', ui: 'dropdown',
      options: [{ value: '', label: t('La prima') }], optionsFn: sceneDi, show: piuScene,
      description: t('Quella che si vede quando si apre la pagina.') },

    { type: 'separator', label: t('Punti') },
    { key: 'punti', label: t('Punti'), type: 'content-items', itemLabel: t('Punto'), etichettaDa: 'titolo',
      description: t('I punti da toccare dentro le foto: «Scena» porta a un\'altra scena, «Informazioni» apre un riquadro col testo, «Collegamento» apre un indirizzo. Le scene si indicano col numero nell\'elenco (1, 2…) o col titolo. Nel builder la vista mostra direzione e inclinazione del centro, e di un punto della foto quando ci fai clic.'),
      newItemDefaults: { scena: '', yaw: 0, pitch: 0, tipo: 'scena', destinazione: '', titolo: '', testo: '', url: '', icona: '' },
      itemFields: [
        { key: 'tipo', label: t('Tipo'), type: 'select', options: [
          { value: 'scena', label: t('Scena') },
          { value: 'info', label: t('Informazioni') },
          { value: 'link', label: t('Collegamento') },
        ] },
        { key: 'titolo', label: t('Titolo'), type: 'text',
          description: t('L\'etichetta del punto e il titolo del riquadro. Per «Scena», vuoto: il titolo della scena di arrivo.') },
        { key: 'scena', label: t('Nella scena'), type: 'text', placeholder: '1',
          description: t('La scena in cui si trova il punto: il numero nell\'elenco o il titolo. Vuota: la prima.') },
        { key: 'yaw', label: t('Direzione'), type: 'range', min: -180, max: 180, step: 1,
          description: t('In gradi: 0 è il centro della foto, 90 un quarto di giro a destra, −90 a sinistra.') },
        { key: 'pitch', label: t('Inclinazione'), type: 'range', min: -90, max: 90, step: 1,
          description: t('In gradi: positiva guarda in alto, negativa in basso.') },
        { key: 'destinazione', label: t('Porta alla scena'), type: 'text', placeholder: '2', condition: tipoScena,
          description: t('Il numero nell\'elenco o il titolo della scena che si apre.') },
        { key: 'testo', label: t('Testo'), type: 'textarea', condition: { field: 'tipo', op: 'eq', value: 'info' } },
        { key: 'url', label: t('Indirizzo'), type: 'link', condition: { field: 'tipo', op: 'eq', value: 'link' },
          description: t('Una pagina del sito si apre nella stessa scheda, un altro sito in una nuova.') },
        { key: 'icona', label: t('Icona'), type: 'icon',
          description: t('Vuota: la freccia per le scene, il più per le informazioni, la freccia obliqua per i collegamenti.') },
      ] },

    { type: 'separator', label: t('Vista') },
    { key: 'fov', label: t('Campo visivo'), type: 'range', min: 35, max: 100, step: 1, unit: '°',
      description: t('Quanto si vede in altezza quando la scena si apre: meno gradi, più vicino. Lo zoom va da 35 a 100.') },
    { key: 'zoom', label: t('Zoom'), type: 'toggle',
      description: t('Due dita sul telefono, Ctrl e rotellina sul computer, + e − da tastiera, i pulsanti sulla vista. La rotellina da sola fa scorrere la pagina.') },
    { key: 'mostra_titolo', label: t('Mostra il titolo della scena'), type: 'toggle' },
    { key: 'elenco_scene', label: t('Elenco delle scene'), type: 'toggle', show: piuScene,
      description: t('Le miniature delle scene in fondo alla vista, per saltare da una all\'altra.') },
    { key: 'schermo_intero', label: t('Pulsante schermo intero'), type: 'toggle',
      description: t('Compare solo dove il browser lo permette: sull\'iPhone no.') },

    { type: 'separator', label: t('Rotazione automatica') },
    { key: 'autorotazione', label: t('Rotazione automatica'), type: 'toggle',
      description: t('La vista gira piano finché nessuno la tocca e si ferma alla prima interazione. Ferma per chi ha chiesto al sistema meno movimento.') },
    { key: 'velocita', label: t('Velocità'), type: 'range', min: 0.5, max: 12, step: 0.5, unit: '°/s', condition: conAuto },
    { key: 'ripresa', label: t('Riparte dopo'), type: 'range', min: 0, max: 30, step: 1, unit: 's', condition: conAuto,
      description: t('Secondi senza interazioni prima che la rotazione riparta. 0: non riparte.') },

    { type: 'separator', label: t('Suggerimento') },
    { key: 'mostra_suggerimento', label: t('Mostra il suggerimento'), type: 'toggle',
      description: t('Una riga al centro della vista che dice come si usa: sparisce al primo trascinamento.') },
    { key: 'testo_suggerimento', label: t('Suggerimento'), type: 'text', condition: conAiuto },
    { key: 'testo_suggerimento_touch', label: t('Suggerimento sul telefono'), type: 'text', condition: conAiuto },

    { type: 'separator', label: t('Caricamento') },
    { key: 'testo_caricamento', label: t('Testo del caricamento'), type: 'text' },
  ],

  styleFields: [
    { type: 'separator', label: t('Vista') },
    { key: 'proporzione', label: t('Proporzioni'), type: 'select', options: ratioOptions({ auto: false, skip: ['3/2', '3/4', '4/5', '9/16', '2/3'] }) },
    { key: 'proporzione_mobile', label: t('Proporzioni sul telefono'), type: 'select', options: ratioOptions({ auto: false, skip: ['4/3', '3/2', '21/9', '3/4', '9/16', '2/3'] }) },
    { key: 'raggio', label: t('Raggio'), type: 'border-radius',
      description: t('Gli angoli della vista; il riquadro, le miniature e i pulsanti lo seguono. A zero, tutto ad angoli vivi.') },
    { key: 'bordo', label: t('Bordo'), type: 'border' },
    { key: 'ombra', label: t('Ombra'), type: 'select', options: OMBRE },
    { key: 'ombra_custom', label: t('Ombra personalizzata'), type: 'box-shadow',
      legacyKeys: { h: 'ombra_h', v: 'ombra_v', blur: 'ombra_blur', spread: 'ombra_spread', color: 'ombra_color', inset: 'ombra_inset' },
      condition: { field: 'ombra', op: 'eq', value: 'custom' } },

    { type: 'separator', label: t('Punti') },
    { key: 'stile_punto', label: t('Stile dei punti'), type: 'select', options: [
      { value: 'anello', label: t('Anello') },
      { value: 'pieno', label: t('Cerchio pieno') },
      { value: 'icona', label: t('Solo icona') },
    ], description: t('Anello: il cerchio colorato su un velo scuro, che pulsa piano. Cerchio pieno: il disco colorato. Solo icona: l\'icona colorata, senza disco.') },
    { key: 'colore_punto', label: t('Colore dei punti'), type: 'color',
      description: t('Vuoto: il colore accento del tema.') },
    { key: 'misura_punto', label: t('Misura dei punti'), type: 'range', min: 28, max: 56, step: 2, unit: 'px' },

    { type: 'separator', label: t('Etichette') },
    { key: 'sfondo_etichetta', label: t('Sfondo etichette'), type: 'color',
      description: t('I nomi dei punti, il titolo della scena, il suggerimento e i nomi delle scene. Vuoto: un velo scuro.') },
    { type: 'typography', label: t('Etichette'), responsiveKeys: ['size'],
      keys: { family: 'etichetta_font_family', size: 'etichetta_font_size', weight: 'etichetta_font_weight', color: 'colore_etichetta' },
      sizeMin: 11, sizeMax: 20 },

    { type: 'separator', label: t('Pannello') },
    { key: 'sfondo_pannello', label: t('Sfondo pannello'), type: 'color',
      description: t('Il riquadro che si apre dai punti «Informazioni». Vuoto: il fondo del tema.') },
    { key: 'colore_pannello', label: t('Colore testo pannello'), type: 'color',
      description: t('Titolo e testo del riquadro, quando non hanno un colore loro. Vuoto: il testo del tema, o quello che si legge sullo sfondo scelto.') },
    { type: 'typography', label: t('Titolo'), responsiveKeys: ['size'],
      keys: { family: 'titolo_font_family', size: 'titolo_font_size', weight: 'titolo_font_weight', color: 'titolo_color' },
      sizeMin: 14, sizeMax: 32 },
    { type: 'typography', label: t('Testo'), responsiveKeys: ['size'],
      keys: { family: 'testo_font_family', size: 'testo_font_size', weight: 'testo_font_weight', lineHeight: 'testo_line_height', color: 'testo_color' },
      sizeMin: 12, sizeMax: 22 },

    { type: 'separator', label: t('Controlli') },
    { key: 'sfondo_comandi', label: t('Sfondo pulsanti'), type: 'color',
      description: t('I pulsanti sulla vista: schermo intero e zoom. Vuoto: un velo scuro.') },
    { key: 'colore_comandi', label: t('Colore icone'), type: 'color',
      description: t('Le icone dei pulsanti. Vuoto: il chiaro del tema, o quello che si legge sullo sfondo scelto.') },
  ],
};
