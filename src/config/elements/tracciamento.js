import { t } from '@/i18n';

/**
 * Tracciamento spedizione — il campo per il numero della spedizione che apre il portale del corriere: l'indirizzo
 * si scrive come modello col segnaposto {codice} (https://traccia.esempio.it/?n={codice}); senza segnaposto il
 * numero va in fondo. Con due o più operatori, prima del campo compare la scelta del corriere. Il numero si
 * ripulisce (spazi, maiuscolo) e si controlla prima di partire; il messaggio d'errore lo legge anche chi usa un
 * lettore di schermo. «Valore dall'indirizzo» compila il campo dal parametro della pagina (la pagina d'arrivo).
 * Il pulsante segue lo stile pulsanti del tema. Nata per i temi industriali della fase 3 (14 ott 2026).
 * Renderer: includes/tiles/class-tracciamento-tile.php.
 *   fields[]      → collegamento (indirizzo, operatori, scheda, parametro) · campo · pulsante
 *   styleFields[] → Aspetto · Etichetta · Campo · Pulsante · Nota · Disposizione
 */
const conEtichetta = { field: 'mostra_etichetta', op: 'eq', value: true };

const OMBRE = [
  { value: 'none', label: t('Nessuna') },
  { value: 'sm', label: t('Leggera') },
  { value: 'md', label: t('Media') },
  { value: 'lg', label: t('Forte') },
  { value: 'xl', label: t('Molto forte') },
  { value: 'custom', label: t('Personalizzata') },
];

export default {
  type: 'tracciamento',
  name: t('Tracciamento spedizione'),
  icon: 'dashicons-car',
  category: 'interactive',

  defaults: {
    indirizzo: '',
    operatori: [],
    etichetta: t('Numero di spedizione'),
    mostra_etichetta: true,
    segnaposto: t('Es. 1Z999AA10123456784'),
    testo_pulsante: t('Traccia'),
    icona_pulsante: 'arrow-right',
    icona_campo: 'package',
    nota: '',
    nuova_scheda: true,
    maiuscolo: true,
    lunghezza_minima: 4,
    testo_errore: t('Scrivi il numero di spedizione completo.'),
    parametro: '',

    sfondo_campo: '',
    colore_campo: '',
    bordo_campo: {},
    colore_etichetta: '',
    colore_nota: '',
    colore_errore: '',
    sfondo_pulsante: '',
    colore_pulsante: '',
    ombra: 'none',
    ombra_h: '0',
    ombra_v: '4',
    ombra_blur: '10',
    ombra_spread: '0',
    ombra_color: 'rgba(0,0,0,0.15)',
    ombra_inset: false,
    etichetta_font_family: '',
    etichetta_font_size: 14,
    etichetta_font_size_tablet: '',
    etichetta_font_size_mobile: '',
    etichetta_font_weight: '600',
    campo_font_family: '',
    campo_font_size: 17,
    campo_font_size_tablet: '',
    campo_font_size_mobile: '',
    campo_font_weight: '500',
    pulsante_font_family: '',
    pulsante_font_size: 16,
    pulsante_font_size_tablet: '',
    pulsante_font_size_mobile: '',
    pulsante_font_weight: '600',
    pulsante_text_transform: '',
    pulsante_letter_spacing: '',
    nota_font_family: '',
    nota_font_size: 13,
    nota_font_size_tablet: '',
    nota_font_size_mobile: '',
    nota_font_weight: '',
    raggio_campo: '',
    raggio_pulsante: '',
    altezza: 56,
    larghezza_max: 640,
    disposizione: 'riga',
    allineamento: 'left',
  },

  // Appena nata: con la nota che dice dove si trova il numero.
  partenza: {
    nota: t('Lo trovi nella mail di conferma della spedizione.'),
  },

  fields: [
    { type: 'separator', label: t('Collegamento') },
    { key: 'indirizzo', label: t('Indirizzo del tracciamento'), type: 'text', placeholder: 'https://…/?n={codice}',
      description: t('L\'indirizzo del portale del corriere, col segnaposto {codice} dove va il numero di spedizione: per esempio https://traccia.esempio.it/?n={codice}. Senza {codice} il numero si aggiunge in fondo. Va bene anche una pagina del sito.') },
    { key: 'operatori', label: t('Operatori'), type: 'content-items', itemLabel: t('Operatore'), etichettaDa: 'nome',
      description: t('Facoltativi. Con due o più operatori, prima del campo compare la scelta del corriere e ognuno apre il suo indirizzo (sempre con {codice}). Un operatore senza indirizzo usa quello qui sopra.'),
      newItemDefaults: { nome: '', indirizzo: '' },
      itemFields: [
        { key: 'nome', label: t('Nome'), type: 'text' },
        { key: 'indirizzo', label: t('Indirizzo'), type: 'text', placeholder: 'https://…{codice}' },
      ] },
    { key: 'nuova_scheda', label: t('Apri in una nuova scheda'), type: 'toggle',
      description: t('Per il portale di un corriere. Per una pagina dello stesso sito, spegnilo.') },
    { key: 'parametro', label: t('Valore dall\'indirizzo'), type: 'text', placeholder: 'codice',
      description: t('Il nome di un parametro dell\'indirizzo della pagina: con «codice», chi arriva da …?codice=AB123 trova il campo già scritto. Serve alla pagina d\'arrivo del tracciamento.') },

    { type: 'separator', label: t('Campo') },
    { key: 'etichetta', label: t('Etichetta'), type: 'text' },
    { key: 'mostra_etichetta', label: t('Mostra l\'etichetta'), type: 'toggle',
      description: t('Spenta, l\'etichetta resta per i lettori di schermo.') },
    { key: 'segnaposto', label: t('Segnaposto'), type: 'text' },
    { key: 'icona_campo', label: t('Icona del campo'), type: 'icon' },
    { key: 'nota', label: t('Nota'), type: 'text',
      description: t('Una riga sotto il campo: dove si trova il numero, gli orari del servizio clienti. Vuota: niente.') },
    { key: 'maiuscolo', label: t('Tutto maiuscolo'), type: 'toggle',
      description: t('Il numero si scrive e si invia in maiuscolo, come lo stampano i corrieri. Gli spazi si tolgono sempre.') },
    { key: 'lunghezza_minima', label: t('Lunghezza minima'), type: 'range', min: 1, max: 30, step: 1,
      description: t('Sotto questo numero di caratteri il campo mostra il messaggio d\'errore invece di aprire il collegamento.') },
    { key: 'testo_errore', label: t('Messaggio d\'errore'), type: 'text' },

    { type: 'separator', label: t('Pulsante') },
    { key: 'testo_pulsante', label: t('Testo del pulsante'), type: 'text' },
    { key: 'icona_pulsante', label: t('Icona del pulsante'), type: 'icon' },
  ],

  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'ombra', label: t('Ombra'), type: 'select', options: OMBRE,
      description: t('Sotto il campo e il pulsante.') },
    { key: 'ombra_custom', label: t('Ombra personalizzata'), type: 'box-shadow',
      legacyKeys: { h: 'ombra_h', v: 'ombra_v', blur: 'ombra_blur', spread: 'ombra_spread', color: 'ombra_color', inset: 'ombra_inset' },
      condition: { field: 'ombra', op: 'eq', value: 'custom' } },

    { type: 'separator', label: t('Etichetta'), condition: conEtichetta },
    { type: 'typography', label: t('Etichetta'), responsiveKeys: ['size'],
      keys: { family: 'etichetta_font_family', size: 'etichetta_font_size', weight: 'etichetta_font_weight', color: 'colore_etichetta' },
      sizeMin: 11, sizeMax: 28 },

    { type: 'separator', label: t('Campo') },
    { key: 'sfondo_campo', label: t('Sfondo campo'), type: 'color',
      description: t('Vuoto: il colore di fondo del tema.') },
    { key: 'colore_campo', label: t('Colore testo campo'), type: 'color',
      description: t('Vuoto: il colore del testo del tema, o quello che si legge sullo sfondo scelto.') },
    { key: 'bordo_campo', label: t('Bordo campo'), type: 'border' },
    { key: 'raggio_campo', label: t('Raggio campo'), type: 'border-radius',
      description: t('Vuoto: il raggio dei pulsanti del tema, come il pulsante.') },
    { type: 'typography', label: t('Testo del campo'), responsiveKeys: ['size'],
      keys: { family: 'campo_font_family', size: 'campo_font_size', weight: 'campo_font_weight' },
      sizeMin: 16, sizeMax: 28 },
    { key: 'altezza', label: t('Altezza'), type: 'range', min: 44, max: 72, step: 2, unit: 'px',
      description: t('Del campo e del pulsante.') },

    { type: 'separator', label: t('Pulsante') },
    { key: 'sfondo_pulsante', label: t('Sfondo pulsante'), type: 'color',
      description: t('Vuoto: il primario del tema. Colora anche l\'anello del campo quando si scrive.') },
    { key: 'colore_pulsante', label: t('Colore testo pulsante'), type: 'color',
      description: t('Vuoto: il colore che il tema abbina al primario, o quello che si legge sullo sfondo scelto.') },
    { key: 'raggio_pulsante', label: t('Raggio pulsante'), type: 'border-radius',
      description: t('Vuoto: il raggio dei pulsanti del tema.') },
    { type: 'typography', label: t('Pulsante'), responsiveKeys: ['size'],
      keys: { family: 'pulsante_font_family', size: 'pulsante_font_size', weight: 'pulsante_font_weight', transform: 'pulsante_text_transform', letterSpacing: 'pulsante_letter_spacing' },
      sizeMin: 12, sizeMax: 24 },

    { type: 'separator', label: t('Nota') },
    { type: 'typography', label: t('Nota'), responsiveKeys: ['size'],
      keys: { family: 'nota_font_family', size: 'nota_font_size', weight: 'nota_font_weight', color: 'colore_nota' },
      sizeMin: 12, sizeMax: 20 },
    { key: 'colore_errore', label: t('Colore errore'), type: 'color',
      description: t('Il messaggio quando il numero manca o è troppo corto. Vuoto: il colore «pericolo» del tema.') },

    { type: 'separator', label: t('Disposizione') },
    { key: 'disposizione', label: t('Disposizione'), type: 'select', options: [
      { value: 'riga', label: t('Campo e pulsante in riga') },
      { value: 'colonna', label: t('Pulsante sotto il campo') },
    ], description: t('In riga: sul telefono il pulsante va comunque sotto, largo quanto il campo.') },
    { key: 'larghezza_max', label: t('Larghezza massima'), type: 'range', min: 320, max: 960, step: 10, unit: 'px' },
    { key: 'allineamento', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('A sinistra') },
      { value: 'center', label: t('Al centro') },
    ] },
  ],
};
