import { t } from '@/i18n';

// I giorni di ogni fascia, per «Evidenzia oggi» (16 nov 2026): gli stessi della Lavagna a gesso (1 lunedì … 7 domenica).
const GIORNI = [
  { value: '1', label: t('Lun') },
  { value: '2', label: t('Mar') },
  { value: '3', label: t('Mer') },
  { value: '4', label: t('Gio') },
  { value: '5', label: t('Ven') },
  { value: '6', label: t('Sab') },
  { value: '7', label: t('Dom') },
];
const oggiAcceso = { field: 'evidenzia_oggi', value: true };

/**
 * Hours Strip — banda orari di apertura (giorno · orario · nota) con divisori.
 * Token-first: orario dal font heading del tema, giorno/nota in monospace.
 */
export default {
  type: 'hoursstrip',
  name: t('Hours Strip'),
  icon: 'dashicons-clock',
  category: 'layout',

  defaults: {
    typography_preset: '',
    items: [
      { day: 'Lun — Gio', time: '12 — 23', note: 'Cucina fino alle 22' },
      { day: 'Ven — Sab', time: '12 — 24', note: 'Aperitivo dalle 18' },
      { day: 'Domenica',  time: '12 — 16', note: 'Solo pranzo' },
      { day: 'Martedì',   time: 'Chiuso',  note: 'Riposo settimanale' },
    ],
    columns: 4,
    band_padding_y: 36,

    show_dividers: true,
    divider_color: 'var(--olo-color-border, #e5e7eb)',
    band_border: true,

    day_color: 'var(--olo-color-text-soft, #6b7280)',
    day_size: 12,

    time_font_family: 'heading',
    time_color: 'var(--olo-color-text, #1f2937)',
    time_size: 30,
    time_weight: '500',

    note_color: 'var(--olo-color-text-soft, #6b7280)',
    note_size: 13,

    mono_font_family: '',

    // Spento nelle tile già salvate (che restano com'erano), acceso nella partenza.
    evidenzia_oggi: false,
    scritta_oggi: 'Oggi',
    colore_oggi: '',
  },

  // Appena nata: la banda degli orari di apertura di un negozio o di uno studio, quattro fasce
  // coerenti fra loro (giorni · orario · nota) con i divisori e i colori del tema.
  partenza: {
    items: [
      { day: t('Lun — Ven'), time: '9 — 19', note: t('Orario continuato'), settimana: '1,2,3,4,5' },
      { day: t('Sabato'), time: '9 — 13', note: t('Pomeriggio su appuntamento'), settimana: '6' },
      { day: t('Domenica'), time: t('Chiuso'), note: t('Riposo settimanale'), settimana: '7' },
      { day: t('Festivi'), time: '10 — 13', note: t('Verifica sul calendario'), settimana: '' },
    ],
    evidenzia_oggi: true,
    scritta_oggi: t('Oggi'),
    divider_color: 'var(--olo-color-border, #e5e7eb)',
    day_color: 'var(--olo-color-text-soft, #6b7280)',
    time_color: 'var(--olo-color-text, #1f2937)',
    note_color: 'var(--olo-color-text-soft, #6b7280)',
  },

  // ═══ CONTENUTO ════════════════════════════════════════════════
  fields: [
    { type: 'separator', label: t('Orari') },
    { key: 'items', label: t('Fasce'), type: 'content-items', etichettaDa: 'day',
      itemLabel: t('Fascia'),
      defaults: { day: t('Giorni'), time: '00 — 00', note: '', settimana: '' },
      itemFields: [
        { key: 'day',  label: t('Giorni'), type: 'text', placeholder: t('Lun — Ven') },
        { key: 'time', label: t('Orario'), type: 'text', placeholder: '9 — 19' },
        { key: 'note', label: t('Nota'),   type: 'text', placeholder: t('Facoltativa') },
        { key: 'settimana', label: t('Giorni della settimana'), type: 'multi_pills', options: GIORNI,
          condizioneTile: oggiAcceso,
          description: t('Servono a evidenziare la fascia di oggi. Nessun giorno: la fascia non si evidenzia mai, come i festivi.') },
      ],
    },

    { type: 'separator', label: t('Oggi') },
    { key: 'evidenzia_oggi', label: t('Evidenzia oggi'), type: 'toggle',
      description: t('La fascia che vale oggi, nel fuso orario del sito, con un fondo tenue e la scritta qui sotto. Cambia da sola ogni giorno, anche con la pagina in cache.') },
    { key: 'scritta_oggi', label: t('Scritta'), type: 'text', placeholder: t('Oggi'), condition: oggiAcceso },
  ],

  // ═══ STILE ════════════════════════════════════════════════════
  // Zone standard (16 nov 2026): Aspetto · Giorno · Orario · Nota · Forma · Disposizione. Prima «Banda» e «Divisori»,
  // e il font dei giorni in un campo a parte; ora sta nel controllo tipografia del Giorno (chiave INVARIATA).
  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'show_dividers', label: t('Divisori tra le celle'), type: 'toggle' },
    { key: 'band_border',   label: t('Bordo sopra e sotto'),   type: 'toggle' },
    { key: 'divider_color', label: t('Colore linee'),          type: 'color',
      show: (s) => s.show_dividers !== false || s.band_border !== false },
    { key: 'colore_oggi', label: t('Colore di oggi'), type: 'color', condition: oggiAcceso,
      description: t('Il fondo tenue, il giorno e la scritta della fascia di oggi. Vuoto: il primario del tema.') },

    { type: 'separator', label: t('Giorno') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Giorno'), responsiveKeys: [], keys: { family: 'mono_font_family', size: 'day_size', color: 'day_color' }, sizeMin: 10, sizeMax: 18,
      description: t('Font vuoto: il monospazio del tema.') },

    { type: 'separator', label: t('Orario') },
    { type: 'typography', label: t('Orario'), responsiveKeys: [], keys: { size: 'time_size', weight: 'time_weight', color: 'time_color', family: 'time_font_family' }, sizeMin: 16, sizeMax: 56 },

    { type: 'separator', label: t('Nota') },
    { type: 'typography', label: t('Nota'), responsiveKeys: [], keys: { size: 'note_size', color: 'note_color' }, sizeMin: 10, sizeMax: 18 },

    { type: 'separator', label: t('Forma') },
    { key: 'band_padding', label: t('Padding'), type: 'spacing', min: 0, max: 120,
      legacyKeys: { y: 'band_padding_y' } },

    { type: 'separator', label: t('Disposizione') },
    { key: 'columns',        label: t('Colonne'), type: 'range', min: 1, max: 6, step: 1, responsive: true },
  ],
};
