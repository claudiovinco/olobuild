import { t } from '@/i18n';

/**
 * Evo Notes — layer di annotazioni "Perché questa evoluzione" (blueprint Clod).
 * Tile-layer di pagina: si inserisce UNA volta in fondo alla pagina. Renderizza un
 * bottone fisso in basso a destra che attiva/disattiva il layer; quando attivo
 * compaiono marker numerati circolari ancorati alle sezioni della pagina (per
 * html_id o per ordine) e il click su un marker apre una card con titolo, testo
 * e confronto "Prima → Ora". Render Vue = anteprima inline (i marker reali si
 * ancorano alle sezioni solo sul frontend). Render PHP = autoritativo
 * (class-evonotes-tile.php). Estratta dal blueprint "Clod — Evoluzione v2".
 */
export default {
  type: 'evonotes',
  name: t('Evo Notes (layer annotazioni)'),
  icon: 'dashicons-info',
  category: 'interactive',

  defaults: {
    toggle_label: 'Perché questa evoluzione',
    toggle_label_active: 'Nascondi motivazioni',
    show_hint: true,
    hint_text: 'Tocca i numeri per leggere ogni scelta — Prima → Ora',
    kicker_label: 'Evoluzione',
    accent: '',
    card_bg: '',
    text_color: '',
    items: [
      { number: '01', title: 'Identità tipografica', text: 'Un display industriale proprio dà voce allo studio fin dalla prima schermata, invece di affidarsi al look di un tema preconfezionato.', before: 'Tema YOOtheme', after: 'Carattere proprio', anchor: '', side: 'right', offset: '38%' },
      { number: '02', title: 'Una voce, non effetti', text: 'Un messaggio editoriale netto sostituisce slider e animazioni: chi arriva capisce subito cosa fai e perché conta.', before: 'Slider Revolution', after: 'Messaggio chiaro', anchor: '', side: 'left', offset: '70%' },
      { number: '03', title: 'Gerarchia leggibile', text: "I servizi diventano una lista numerata, scansionabile in un colpo d'occhio — non più cinque parole schiacciate su una riga.", before: 'Riga unica', after: 'Lista 01–05', anchor: 'servizi', side: 'right', offset: '30%' },
      { number: '04', title: 'Il lavoro al centro', text: 'I progetti scorrono in un reel orizzontale cinematografico — trascina, usa la rotella o scorri — al posto di video sparsi senza ordine.', before: 'Media sparsi', after: 'Reel orizzontale', anchor: 'lavori', side: 'left', offset: '18px' },
      { number: '05', title: 'Il sito è il prodotto', text: 'Questo stesso sito è costruito come un OLOtheme: visitarlo significa vedere dal vivo cosa sa fare lo studio. Showreel e prova insieme.', before: 'Portfolio statico', after: 'Showreel vivo', anchor: 'rs', side: 'right', offset: '20%' },
      { number: '06', title: 'Sala di regia', text: 'Mirino col nome della sezione, timecode di scroll, grana pellicola, fotogrammi che si inclinano col drag: il mestiere — video e media — diventa il linguaggio stesso del sito.', before: 'Pagina statica', after: 'Monitor live', anchor: 'contatto', side: 'left', offset: '26%' },
    ],
  },

  fields: [
    { type: 'separator', label: t('Bottone layer') },
    { key: 'toggle_label', label: t('Etichetta bottone'), type: 'text' },
    { key: 'toggle_label_active', label: t('Etichetta quando attivo'), type: 'text' },

    { type: 'separator', label: t('Hint') },
    { key: 'show_hint', label: t('Mostra hint in basso'), type: 'toggle' },
    { key: 'hint_text', label: t('Testo hint'), type: 'text',
      condition: { field: 'show_hint', op: 'eq', value: true } },

    { type: 'separator', label: t('Annotazioni') },
    { key: 'kicker_label', label: t('Kicker card'), type: 'text',
      description: t('Prefisso della riga in alto nella card: "Kicker · 01".') },
    { key: 'items', label: t('Annotazioni'), type: 'content-items',
      description: t('Anteprima nel canvas: bottone + un esempio di marker e card. I marker reali si ancorano alle sezioni della pagina solo sul frontend.'),
      itemFields: [
        { key: 'number', label: t('Numero'), type: 'text' },
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'text', label: t('Testo'), type: 'textarea' },
        { key: 'before', label: t('Prima'), type: 'text' },
        { key: 'after', label: t('Ora'), type: 'text' },
        { type: 'separator', label: t('Ancoraggio') },
        { key: 'anchor', label: t('ID sezione (html_id)'), type: 'text',
          description: t('Es. "servizi". Vuoto = usa la sezione n-esima della pagina (in ordine).') },
        { key: 'side', label: t('Lato'), type: 'select', options: [
          { value: 'left', label: t('Sinistra') },
          { value: 'right', label: t('Destra') },
        ]},
        { key: 'offset', label: t('Distanza dal top'), type: 'text',
          description: t('Distanza del marker dal top della sezione, es. "38%" o "18px".') },
      ],
      newItemDefaults: { number: '07', title: t('Nuova scelta'), text: t('Perché questa scelta migliora il sito.'), before: t('Prima'), after: t('Ora'), anchor: '', side: 'right', offset: '30%' },
      itemLabel: 'Annotazione',
    },
  ],

  styleFields: [
    { type: 'separator', label: t('Colori') },
    { key: 'accent', label: t('Accento (bottone e marker)'), type: 'color',
      description: t('Vuoto = primario del tema.') },
    { key: 'card_bg', label: t('Sfondo card e hint'), type: 'color',
      description: t('Vuoto = superficie scura del tema.') },
    { key: 'text_color', label: t('Colore testo card'), type: 'color',
      description: t('Vuoto = testo del tema.') },
  ],
};
