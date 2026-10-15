import { t } from '@/i18n';

/**
 * Lucchetto — un lucchetto a combinazione da aprire risolvendo un enigma: il testo da risolvere, le ruote (cifre,
 * lettere o simboli tuoi) e il pulsante «Apri». Aperto, il gancio si alza e compaiono il messaggio, il codice premio
 * da copiare e un pulsante. Le ruote si girano con le frecce, la tastiera (anche scrivendo il simbolo), la rotellina
 * e trascinandole col dito o col mouse. La combinazione non compare nella pagina: il PHP ne scrive un'impronta, e il
 * premio arriva cifrato con la combinazione stessa, quindi non si legge nel sorgente. Indizio dietro un pulsante (o da
 * solo dopo N tentativi), memoria dell'apertura nel browser. Nata per i temi delle escape room virtuali (15 ott 2026).
 * Renderer: includes/tiles/class-lucchetto-tile.php.
 *   fields[]      → enigma e indizio · combinazione e simboli · premio · lucchetto (disegno o icona)
 *   styleFields[] → Aspetto (stile, colori, ombra) · Testo · Forma (raggio, misura delle ruote) · Disposizione
 */
const conIndizio = { field: 'indizio', op: 'notEmpty' };
const conCodice = { field: 'codice_premio', op: 'notEmpty' };
const conPulsantePremio = { field: 'pulsante_premio_testo', op: 'notEmpty' };
const conLucchetto = { field: 'mostra_lucchetto', op: 'eq', value: true };

const OMBRE = [
  { value: 'none', label: t('Nessuna') },
  { value: 'sm', label: t('Leggera') },
  { value: 'md', label: t('Media') },
  { value: 'lg', label: t('Forte') },
  { value: 'xl', label: t('Molto forte') },
  { value: 'custom', label: t('Personalizzata') },
];

export default {
  type: 'lucchetto',
  name: t('Lucchetto'),
  icon: 'dashicons-lock',
  category: 'interactive',

  defaults: {
    enigma: t('<p><strong>Quattro cifre</strong> aprono il lucchetto, in quest\'ordine:</p><p>i colli su cui sorge Roma, i lati di un triangolo, le lune della Terra, le zampe di un gatto.</p>'),
    indizio: t('Sommate fanno 15, e solo l\'ultima è pari.'),
    testo_indizio: t('Mostra un indizio'),
    combinazione: '7314',
    simboli: 'cifre',
    simboli_personalizzati: 'I,II,III,IV,V',
    testo_pulsante: t('Apri'),
    messaggio_errore: t('Non si apre. Riprova.'),
    messaggio_successo: t('Aperto! Il codice qui sotto vale uno sconto del 10% sulla prossima prenotazione.'),
    codice_premio: 'APRITI10',
    testo_copia: t('Copia il codice'),
    testo_copiato: t('Copiato'),
    pulsante_premio_testo: '',
    pulsante_premio_url: '',
    tentativi_indizio: 3,
    mostra_tentativi: false,
    ricorda: false,
    mostra_lucchetto: true,
    icona_lucchetto: '',

    stile: 'ottone',
    colore_corpo: '',
    colore_ruota: '',
    colore_simbolo: '',
    colore_accento: '',
    colore_successo: '',
    colore_errore: '',
    sfondo_pannello: '',
    raggio: { tl: 16, tr: 16, br: 16, bl: 16 },
    ombra: 'none',
    ombra_h: '0',
    ombra_v: '4',
    ombra_blur: '10',
    ombra_spread: '0',
    ombra_color: 'rgba(0,0,0,0.15)',
    ombra_inset: false,
    enigma_font_family: '',
    enigma_font_size: 18,
    enigma_font_size_tablet: '',
    enigma_font_size_mobile: '',
    enigma_font_weight: '400',
    enigma_line_height: 1.6,
    enigma_color: '',
    simbolo_font_family: '',
    simbolo_font_size: '',
    simbolo_font_size_tablet: '',
    simbolo_font_size_mobile: '',
    simbolo_font_weight: '700',
    simbolo_color: '',
    messaggio_font_family: '',
    messaggio_font_size: 17,
    messaggio_font_size_tablet: '',
    messaggio_font_size_mobile: '',
    messaggio_font_weight: '500',
    messaggio_color: '',
    misura_ruota: 60,
    gap: 32,
    allineamento: 'center',
  },

  fields: [
    { type: 'separator', label: t('Enigma') },
    { key: 'enigma', label: t('Enigma'), type: 'editor', mode: 'block',
      description: t('Il testo da risolvere: un indovinello, una domanda, gli indizi sparsi nella pagina. Grassetto, corsivo e a capo si vedono come li scrivi.') },
    { key: 'indizio', label: t('Indizio'), type: 'textarea',
      description: t('Si apre dal pulsante «Mostra un indizio». Vuoto: niente pulsante.') },
    { key: 'testo_indizio', label: t('Pulsante dell\'indizio'), type: 'text', condition: conIndizio },
    { key: 'tentativi_indizio', label: t('Tentativi prima dell\'indizio'), type: 'range', min: 0, max: 10, step: 1, condition: conIndizio,
      description: t('Dopo questo numero di tentativi sbagliati l\'indizio si apre da solo. 0: mai.') },

    { type: 'separator', label: t('Combinazione') },
    { key: 'combinazione', label: t('Combinazione'), type: 'text', placeholder: '7314',
      description: t('La soluzione, da 3 a 6 simboli: tanti quante le ruote. Cifre e lettere si scrivono di seguito («7314», «LUNA»), i simboli personalizzati separati da una virgola («III,I,IV»). Maiuscole e minuscole valgono uguale. Nella pagina non compare: il sito ne conserva solo un\'impronta, e il premio si legge solo con la combinazione giusta.') },
    { key: 'simboli', label: t('Simboli sulle ruote'), type: 'select', options: [
      { value: 'cifre', label: t('Cifre (0–9)') },
      { value: 'lettere', label: t('Lettere (A–Z)') },
      { value: 'personalizzati', label: t('Personalizzati') },
    ] },
    { key: 'simboli_personalizzati', label: t('Simboli personalizzati'), type: 'text', placeholder: 'I,II,III,IV,V',
      condition: { field: 'simboli', op: 'eq', value: 'personalizzati' },
      description: t('Separati da una virgola, nell\'ordine in cui girano sulle ruote: numeri romani, parole brevi, segni. Almeno due.') },
    { key: 'testo_pulsante', label: t('Testo del pulsante'), type: 'text' },
    { key: 'messaggio_errore', label: t('Messaggio d\'errore'), type: 'text' },
    { key: 'mostra_tentativi', label: t('Mostra i tentativi'), type: 'toggle',
      description: t('Sotto i pulsanti, quanti tentativi sbagliati sono stati fatti.') },

    { type: 'separator', label: t('Premio') },
    { key: 'messaggio_successo', label: t('Messaggio di apertura'), type: 'textarea',
      description: t('Compare quando il lucchetto si apre. Come il codice e il pulsante, non si legge nella pagina finché non si trova la combinazione.') },
    { key: 'codice_premio', label: t('Codice premio'), type: 'text', placeholder: 'APRITI10',
      description: t('Un codice da copiare: uno sconto, la parola d\'ordine della stanza dopo. Vuoto: niente codice.') },
    { key: 'testo_copia', label: t('Pulsante «Copia»'), type: 'text', condition: conCodice },
    { key: 'testo_copiato', label: t('Testo dopo la copia'), type: 'text', condition: conCodice },
    { key: 'pulsante_premio_testo', label: t('Pulsante del premio'), type: 'text',
      description: t('Un pulsante sotto il premio, per esempio verso la stanza successiva. Senza testo o senza collegamento non compare.') },
    { key: 'pulsante_premio_url', label: t('Collegamento del pulsante'), type: 'link', condition: conPulsantePremio },
    { key: 'ricorda', label: t('Ricorda l\'apertura'), type: 'toggle',
      description: t('Il browser ricorda che questo lucchetto è stato aperto: alla visita dopo è già aperto, col premio in vista. Nel builder riparte sempre chiuso.') },

    { type: 'separator', label: t('Lucchetto') },
    { key: 'mostra_lucchetto', label: t('Mostra il lucchetto'), type: 'toggle',
      description: t('Il lucchetto disegnato attorno alle ruote, col gancio che si alza all\'apertura. Spento: restano solo le ruote.') },
    { key: 'icona_lucchetto', label: t('Icona al posto del disegno'), type: 'icon', condition: conLucchetto,
      description: t('Un\'icona sopra le ruote al posto del lucchetto disegnato. Vuota: il disegno.') },
  ],

  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'stile', label: t('Stile'), type: 'select', options: [
      { value: 'ottone', label: t('Ottone') },
      { value: 'acciaio', label: t('Acciaio') },
      { value: 'minimal', label: t('Minimal') },
    ], description: t('Ottone e acciaio sono metalli disegnati coi riflessi; Minimal è piatto, coi colori del tema.') },
    { key: 'colore_corpo', label: t('Colore del corpo'), type: 'color', condition: conLucchetto,
      description: t('Il corpo del lucchetto, o l\'icona. Vuoto: l\'ottone, l\'acciaio o il primario del tema, secondo lo stile.') },
    { key: 'colore_ruota', label: t('Colore delle ruote'), type: 'color',
      description: t('Vuoto: scure su ottone e acciaio, il fondo del tema in Minimal.') },
    { key: 'colore_simbolo', label: t('Colore dei simboli'), type: 'color',
      description: t('Cifre, lettere o simboli sulle ruote. Vuoto: quello che si legge sulle ruote.') },
    { key: 'colore_accento', label: t('Colore accento'), type: 'color',
      description: t('Il pulsante «Apri», il pulsante del premio e l\'anello della ruota attiva. Vuoto: il primario del tema.') },
    { key: 'colore_successo', label: t('Colore del successo'), type: 'color',
      description: t('Le ruote e il riquadro del premio quando il lucchetto si apre. Vuoto: il colore «successo» del tema.') },
    { key: 'colore_errore', label: t('Colore errore'), type: 'color',
      description: t('Il messaggio e le ruote quando la combinazione è sbagliata. Vuoto: il colore «pericolo» del tema.') },
    { key: 'sfondo_pannello', label: t('Sfondo pannello'), type: 'color',
      description: t('Un riquadro attorno a tutta la tile. Vuoto: trasparente.') },
    { key: 'ombra', label: t('Ombra'), type: 'select', options: OMBRE,
      description: t('Sotto il pannello, se ha uno sfondo; altrimenti sotto il lucchetto, o sotto le ruote quando il disegno è spento.') },
    { key: 'ombra_custom', label: t('Ombra personalizzata'), type: 'box-shadow',
      legacyKeys: { h: 'ombra_h', v: 'ombra_v', blur: 'ombra_blur', spread: 'ombra_spread', color: 'ombra_color', inset: 'ombra_inset' },
      condition: { field: 'ombra', op: 'eq', value: 'custom' } },

    { type: 'separator', label: t('Testo') },
    { type: 'typography', label: t('Enigma'), responsiveKeys: ['size'],
      keys: { family: 'enigma_font_family', size: 'enigma_font_size', weight: 'enigma_font_weight', lineHeight: 'enigma_line_height', color: 'enigma_color' },
      sizeMin: 13, sizeMax: 40 },
    { type: 'typography', label: t('Simboli'), responsiveKeys: ['size'],
      keys: { family: 'simbolo_font_family', size: 'simbolo_font_size', weight: 'simbolo_font_weight' },
      sizeMin: 14, sizeMax: 60 },
    { type: 'typography', label: t('Messaggio di apertura'), responsiveKeys: ['size'],
      keys: { family: 'messaggio_font_family', size: 'messaggio_font_size', weight: 'messaggio_font_weight', color: 'messaggio_color' },
      sizeMin: 13, sizeMax: 36 },

    { type: 'separator', label: t('Forma') },
    { key: 'raggio', label: t('Raggio'), type: 'border-radius',
      description: t('Gli angoli del pannello, del lucchetto, dell\'indizio e del premio; le ruote ne prendono una parte.') },
    { key: 'misura_ruota', label: t('Misura delle ruote'), type: 'range', min: 44, max: 88, step: 2, unit: 'px',
      description: t('La larghezza di una ruota: tutto il lucchetto cresce con lei. Dove lo spazio non basta, le ruote si stringono da sole.') },

    { type: 'separator', label: t('Disposizione') },
    { key: 'gap', label: t('Gap'), type: 'range', min: 0, max: 80, step: 2, unit: 'px',
      description: t('Fra l\'enigma, il lucchetto, i pulsanti e il premio.') },
    { key: 'allineamento', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('A sinistra') },
      { value: 'center', label: t('Al centro') },
    ] },
  ],
};
