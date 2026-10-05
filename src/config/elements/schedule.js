import { t } from '@/i18n';

/**
 * Schedule — timetable settimanale (colonne giorni × righe fasce orarie).
 * Statico (nessun runtime). Celle evidenziabili con prefisso "!" o oggetto {text,on}.
 * Render Vue == PHP (ScheduleTile.vue).
 */
export default {
  type: 'schedule',
  name: t('Schedule (timetable)'),
  icon: 'dashicons-calendar-alt',
  category: 'layout',

  defaults: {
    eyebrow: '',
    heading: '',
    days: 'Lun, Mar, Mer, Gio, Ven',
    corner_label: '',
    rows: [
      { time: '07:00', cells: '!Reformer | | Mat | | !Reformer' },
      { time: '12:30', cells: 'Mat | Reformer | | Reformer | Mat' },
      { time: '18:30', cells: '!Reformer | Breath | !Reformer | Mat | Open' },
    ],
    zone_accent: '',
    zone_on: 'var(--olo-color-surface, #ffffff)',
    cell_bg: '',
    card_border: '',
    head_color: '',
    align: 'left',
  },

  // Appena nata: l'orario settimanale dei corsi di una scuola o di uno studio, sei giorni e
  // quattro fasce, con occhiello e titolo e le lezioni del corso base evidenziate nel colore primario.
  partenza: {
    eyebrow: t('Orari dei corsi'),
    heading: t('La settimana tipo'),
    days: t('Lun, Mar, Mer, Gio, Ven, Sab'),
    corner_label: t('Ora'),
    rows: [
      { time: '09:00', cells: t('!Corso base | Intermedio | !Corso base | Intermedio | !Corso base | Lezione aperta') },
      { time: '12:30', cells: t('Avanzato | | Avanzato | | Avanzato | ') },
      { time: '18:00', cells: t('Intermedio | !Corso base | Laboratorio | !Corso base | Intermedio | ') },
      { time: '20:00', cells: t('Avanzato | Laboratorio | Avanzato | Laboratorio | | ') },
    ],
    zone_on: 'var(--olo-color-primary-contrast, #ffffff)',
  },

  fields: [
    { key: 'eyebrow', label: t('Occhiello'), type: 'text' },
    { key: 'heading', label: t('Titolo'), type: 'text' },
    { key: 'days', label: t('Giorni (separati da virgola)'), type: 'text' },
    { key: 'corner_label', label: t('Etichetta angolo (colonna ora)'), type: 'text' },

    { type: 'separator', label: t('Righe (fasce orarie)') },
    { key: 'rows', label: t('Righe'), type: 'content-items',
      itemLabel: t('Riga'),
      defaults: { time: '00:00', cells: ' | | | | ' },
      itemFields: [
        { key: 'time', label: t('Ora'), type: 'text' },
        { key: 'cells', label: t('Celle (separate da | ; "!" = evidenziata)'), type: 'text' },
      ],
    },
  ],

  styleFields: [
    { type: 'separator', label: t('Stile') },
    { key: 'zone_accent', label: t('Colore evidenziato'), type: 'color' },
    { key: 'zone_on', label: t('Testo su evidenziato'), type: 'color' },
    { key: 'cell_bg', label: t('Sfondo celle'), type: 'color' },
    { key: 'card_border', label: t('Bordo griglia'), type: 'border', legacyWidth: 1 },
    { key: 'head_color', label: t('Colore intestazioni'), type: 'color' },
    { key: 'align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
    ]},
  ],
};
