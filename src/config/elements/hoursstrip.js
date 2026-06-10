import { t } from '@/i18n';

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
    items: [
      { day: 'Lun — Gio', time: '12 — 23', note: 'Cucina fino alle 22' },
      { day: 'Ven — Sab', time: '12 — 24', note: 'Aperitivo dalle 18' },
      { day: 'Domenica',  time: '12 — 16', note: 'Solo pranzo' },
      { day: 'Martedì',   time: 'Chiuso',  note: 'Riposo settimanale' },
    ],
    columns: 4,
    band_padding_y: 36,

    show_dividers: true,
    divider_color: '#d7d1c2',
    band_border: true,

    day_color: '#8d8a82',
    day_size: 12,

    time_font_family: 'heading',
    time_color: '#18181a',
    time_size: 30,
    time_weight: '500',

    note_color: '#8d8a82',
    note_size: 13,

    mono_font_family: '',
  },

  // ═══ CONTENUTO ════════════════════════════════════════════════
  fields: [
    { type: 'separator', label: t('Orari') },
    { key: 'items', label: t('Voci'), type: 'content-items',
      itemLabel: t('Fascia'),
      defaults: { day: 'Giorni', time: '00 — 00', note: '' },
      itemFields: [
        { key: 'day',  label: t('Giorni (es. Lun — Ven)'), type: 'text' },
        { key: 'time', label: t('Orario (es. 12 — 23)'),   type: 'text' },
        { key: 'note', label: t('Nota (opzionale)'),       type: 'text' },
      ],
    },
  ],

  // ═══ STILE ════════════════════════════════════════════════════
  styleFields: [
    { type: 'separator', label: t('Banda') },
    { key: 'columns',        label: t('Colonne'), type: 'range', min: 1, max: 6, step: 1, responsive: true },
    { key: 'band_padding_y', label: t('Padding verticale (px)'), type: 'range', min: 0, max: 100, step: 2 },

    { type: 'separator', label: t('Divisori') },
    { key: 'show_dividers', label: t('Divisori tra le celle'), type: 'toggle' },
    { key: 'band_border',   label: t('Bordo sopra e sotto'),   type: 'toggle' },
    { key: 'divider_color', label: t('Colore linee'),          type: 'color' },

    { type: 'separator', label: t('Giorno') },
    { key: 'mono_font_family', label: t('Font etichette (vuoto = mono del tema)'), type: 'font-family' },
    { key: 'day_color', label: t('Colore'),          type: 'color' },
    { key: 'day_size',  label: t('Dimensione (px)'), type: 'range', min: 10, max: 18, step: 1 },

    { type: 'separator', label: t('Orario') },
    { key: 'time_font_family', label: t('Famiglia'), type: 'font-family' },
    { key: 'time_color',  label: t('Colore'),          type: 'color' },
    { key: 'time_size',   label: t('Dimensione (px)'), type: 'range', min: 16, max: 56, step: 1 },
    { key: 'time_weight', label: t('Peso'), type: 'select', options: [
      { value: '400', label: '400' }, { value: '500', label: '500' }, { value: '600', label: '600' }, { value: '700', label: '700' },
    ]},

    { type: 'separator', label: t('Nota') },
    { key: 'note_color', label: t('Colore'),          type: 'color' },
    { key: 'note_size',  label: t('Dimensione (px)'), type: 'range', min: 10, max: 18, step: 1 },
  ],
};
