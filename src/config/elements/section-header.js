import { t } from '@/i18n';

const ALIGN_OPTIONS = () => [
  { value: 'left',    label: t('Sinistra') },
  { value: 'center',  label: t('Centrato') },
  { value: 'right',   label: t('Destra') },
  { value: 'justify', label: t('Giustificato') },
];

/*
 * Visibilità dei controlli: si mostra SOLO ciò che nella configurazione
 * corrente cambia qualcosa sulla pagina. Le condizioni ricevono i settings
 * GREZZI — una chiave mai salvata è undefined — quindi il default va rimesso a
 * mano, altrimenti una tile appena inserita nasconderebbe i controlli della
 * sua disposizione predefinita.
 */
// Un interruttore mai salvato vale il suo default (acceso); gli altri valori si
// leggono come fa il PHP con empty(): '', 0 e '0' sono spenti.
const acceso = (v) => (v === undefined || v === null ? true : !!v && v !== '0');
const disposizione = (s) => (['split', 'stack', 'center'].includes(s?.layout) ? s.layout : 'split');
const affiancata    = (s) => disposizione(s) === 'split';
const conOcchiello  = (s) => acceso(s?.eyebrow_show);
const conSottotitolo = (s) => acceso(s?.tagline_show);
const conPallino    = (s) => conOcchiello(s) && String(s?.eyebrow_separator ?? '— ').trim() === '·';

/**
 * Section Header — occhiello + titolo editoriale multi-riga + sottotitolo.
 * CONTENUTO: solo testi e dati editabili.
 * STILE: una sezione per ogni testo che si vede (occhiello, titolo,
 * sottotitolo), poi la disposizione.
 */
export default {
  type: 'section-header',
  name: t('Section Header'),
  icon: 'dashicons-heading',
  category: 'layout',

  defaults: {
    typography_preset: '',
    eyebrow_show: true,
    eyebrow_text: 'PROVALO SUBITO',
    eyebrow_color: 'var(--olo-color-primary, #e1474f)',
    eyebrow_dot_color: 'var(--olo-color-primary, #e1474f)',
    eyebrow_separator: '— ',
    eyebrow_font_family: '',
    eyebrow_font_size: '',
    eyebrow_font_weight: '',

    headline_lines: [
      { text: 'Nessun rischio,', color: 'var(--olo-color-dark, #16263d)', italic: false },
      { text: 'solo prodotto.',  color: 'var(--olo-color-primary, #e1474f)', italic: true  },
    ],
    headline_font_family: 'serif',
    headline_font_size:   96,
    headline_line_height: 1.0,
    headline_font_weight: '700',
    headline_align:       'left',

    tagline_show: true,
    tagline_text: 'Try before you trust',
    tagline_text_italic: true,
    tagline_text_color: 'var(--olo-color-dark, #16263d)',
    tagline_text_size: 22,
    tagline_font_family: '',
    tagline_font_weight: '',
    tagline_caption: 'TRE GARANZIE · CINQUE PROMESSE',
    tagline_caption_color: '',
    tagline_caption_size: 11,
    tagline_caption_font_family: '',
    tagline_caption_font_weight: '',

    layout: 'split',
    split_ratio: '1.6fr 1fr',
    gap: 60,
    vertical_align: 'end',
  },

  // ═══ CONTENUTO ════════════════════════════════════════════════
  fields: [
    { type: 'separator', label: t('Occhiello') },
    { key: 'eyebrow_show', label: t('Mostra occhiello'), type: 'toggle' },
    { key: 'eyebrow_text', label: t('Testo'),            type: 'text', show: conOcchiello },

    { type: 'separator', label: t('Titolo') },
    { key: 'headline_lines', label: t('Righe del titolo'), type: 'content-items',
      itemLabel: t('Riga'),
      defaults: { text: 'Nuova riga', color: 'var(--olo-color-dark, #16263d)', italic: false },
      itemFields: [
        { key: 'text',   label: t('Testo'),   type: 'text' },
      ],
    },

    { type: 'separator', label: t('Sottotitolo') },
    { key: 'tagline_show',    label: t('Mostra sottotitolo'), type: 'toggle' },
    { key: 'tagline_text',    label: t('Testo'),              type: 'text', show: conSottotitolo },
    // La didascalia esiste solo nella colonna di destra: con le altre
    // disposizioni non viene disegnata, e il campo sparisce.
    { key: 'tagline_caption', label: t('Didascalia'),         type: 'text',
      show: (s) => conSottotitolo(s) && affiancata(s),
      description: t('Riga in maiuscolo sotto il sottotitolo, nella colonna di destra.') },
  ],

  // ═══ STILE ════════════════════════════════════════════════════
  styleFields: [
    { type: 'separator', label: t('Occhiello') },
    { key: 'eyebrow_separator', label: t('Separatore'), type: 'select', show: conOcchiello, options: [
      { value: '',    label: t('Nessuno') },
      { value: '— ',  label: t('— (lineetta)') },
      { value: '· ',  label: t('· (pallino)') },
      { value: '/ ',  label: '/' },
      { value: '› ',  label: '›' },
    ]},
    { key: 'eyebrow_dot_color', label: t('Colore pallino'), type: 'color', show: conPallino },
    { type: 'typography', label: t('Occhiello'), show: conOcchiello, responsiveKeys: [],
      keys: {
        family: 'eyebrow_font_family',
        size:   'eyebrow_font_size',
        weight: 'eyebrow_font_weight',
        color:  'eyebrow_color',
      },
      sizeMin: 9, sizeMax: 24,
    },

    { type: 'separator', label: t('Titolo') },
    // Lo stile tipografico qui vale per il SOLO titolo (il renderer lo applica
    // all'h2, non a tutta la tile): a stile collegato il controllo sotto
    // nasconde famiglia, peso e interlinea, che verrebbero ignorati.
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Titolo'),
      linkedPresetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        family:     'headline_font_family',
        size:       'headline_font_size',
        lineHeight: 'headline_line_height',
        weight:     'headline_font_weight',
      },
      sizeMin: 16, sizeMax: 160, sizeStep: 2,
    },

    { key: 'headline_lines', type: 'content-items', label: t('Righe del titolo'), itemLabel: t('Riga'), etichettaDa: 'text', itemFields: [
        { key: 'italic', label: t('Corsivo'), type: 'toggle' },
        { key: 'color',  label: t('Colore'),  type: 'color' },
    ] },
    { type: 'separator', label: t('Sottotitolo') },
    { type: 'typography', label: t('Sottotitolo'), show: conSottotitolo, responsiveKeys: [],
      keys: {
        family: 'tagline_font_family',
        size:   'tagline_text_size',
        weight: 'tagline_font_weight',
        italic: 'tagline_text_italic',
        color:  'tagline_text_color',
      },
      sizeMin: 12, sizeMax: 48,
    },
    // In colonna o centrata il sottotitolo sta sotto il titolo e il gap è la
    // distanza fra i due (il renderer la tiene fra 8 e 80).
    { key: 'gap', label: t('Gap dal titolo'), type: 'range', min: 8, max: 80, step: 4, unit: 'px',
      show: (s) => conSottotitolo(s) && !affiancata(s) },
    { type: 'typography', label: t('Didascalia'), show: (s) => conSottotitolo(s) && affiancata(s), responsiveKeys: [],
      keys: {
        family: 'tagline_caption_font_family',
        size:   'tagline_caption_size',
        weight: 'tagline_caption_font_weight',
        color:  'tagline_caption_color',
      },
      sizeMin: 9, sizeMax: 18,
    },

    { type: 'separator', label: t('Disposizione') },
    { key: 'layout', label: t('Disposizione'), type: 'select', options: [
      { value: 'split',  label: t('Affiancata — sottotitolo a destra') },
      { value: 'stack',  label: t('In colonna — sottotitolo sotto') },
      { value: 'center', label: t('Centrata') },
    ]},
    // Allinea l'intera colonna: occhiello, titolo e sottotitolo insieme.
    // Con «Centrata» è già tutto al centro, e il campo sparisce.
    { key: 'headline_align', label: t('Allineamento'), type: 'select', options: ALIGN_OPTIONS(),
      show: (s) => disposizione(s) !== 'center' },
    { key: 'split_ratio', label: t('Proporzione colonne'), type: 'select', show: affiancata, options: [
      { value: '1fr 1fr',   label: '50 / 50' },
      { value: '1.6fr 1fr', label: '62 / 38' },
      { value: '2fr 1fr',   label: '67 / 33' },
      { value: '1fr 2fr',   label: '33 / 67' },
    ]},
    // Senza sottotitolo la colonna di destra non c'è: né l'allineamento
    // verticale né il gap fra le colonne avrebbero qualcosa da spostare.
    { key: 'vertical_align', label: t('Allineamento verticale'), type: 'select',
      show: (s) => affiancata(s) && conSottotitolo(s), options: [
      { value: 'start',    label: t('In alto') },
      { value: 'center',   label: t('Centro') },
      { value: 'end',      label: t('In basso') },
      { value: 'baseline', label: t('Baseline') },
    ]},
    { key: 'gap', label: t('Gap colonne'), type: 'range', min: 0, max: 200, step: 4, unit: 'px',
      show: (s) => affiancata(s) && conSottotitolo(s) },
  ],
};
