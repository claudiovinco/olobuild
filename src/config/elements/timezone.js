import { t } from '@/i18n';

/**
 * Timezone — zona interattiva: slider ora (città base) → orari locali delle città
 * con stato lavoro/limite/notte (pallino colorato). Estratto dai demo OLOthemes.
 * Renderer: class-timezone-tile.php. Ogni città ha il suo fuso (`tz`), da cui il browser
 * ricava l'ora legale del giorno; «Ore da UTC» resta per chi lo vuole fisso.
 */
const FUSI = [
  { value: '', label: t('Ore da UTC (a mano, senza ora legale)') },
  { value: 'Europe/Rome', label: 'Roma · Milano' },
  { value: 'Europe/London', label: 'Londra' },
  { value: 'Europe/Paris', label: 'Parigi' },
  { value: 'Europe/Berlin', label: 'Berlino' },
  { value: 'Europe/Madrid', label: 'Madrid' },
  { value: 'Europe/Lisbon', label: 'Lisbona' },
  { value: 'Europe/Athens', label: 'Atene' },
  { value: 'Europe/Istanbul', label: 'Istanbul' },
  { value: 'Europe/Moscow', label: 'Mosca' },
  { value: 'Africa/Cairo', label: 'Il Cairo' },
  { value: 'Africa/Johannesburg', label: 'Johannesburg' },
  { value: 'Asia/Dubai', label: 'Dubai' },
  { value: 'Asia/Kolkata', label: 'India (Mumbai, Delhi)' },
  { value: 'Asia/Singapore', label: 'Singapore' },
  { value: 'Asia/Shanghai', label: 'Cina (Pechino, Shanghai)' },
  { value: 'Asia/Hong_Kong', label: 'Hong Kong' },
  { value: 'Asia/Tokyo', label: 'Tokyo' },
  { value: 'Australia/Sydney', label: 'Sydney' },
  { value: 'Pacific/Auckland', label: 'Auckland' },
  { value: 'America/Sao_Paulo', label: 'San Paolo' },
  { value: 'America/Mexico_City', label: 'Città del Messico' },
  { value: 'America/New_York', label: 'New York · Toronto' },
  { value: 'America/Chicago', label: 'Chicago' },
  { value: 'America/Denver', label: 'Denver' },
  { value: 'America/Los_Angeles', label: 'Los Angeles · San Francisco' },
  { value: 'UTC', label: 'UTC' },
];
export default {
  type: 'timezone',
  name: t('Timezone (fusi orari)'),
  icon: 'dashicons-clock',
  category: 'interactive',

  defaults: {
    eyebrow: '',
    heading: t('Trova un orario che funziona'),
    intro: '',
    base_label: t('La tua ora'),
    input_value: 14,
    work_start: 9,
    work_end: 18,
    items: [
      { city: 'Milano', tz: 'Europe/Rome', offset: 1, label: '' },
      { city: 'Londra', tz: 'Europe/London', offset: 0, label: '' },
      { city: 'New York', tz: 'America/New_York', offset: -5, label: '' },
      { city: 'Tokyo', tz: 'Asia/Tokyo', offset: 9, label: '' },
    ],
    zone_accent: '',
    work_color: '',
    ok_color: 'var(--olo-color-accent, #f4a23b)',
    sleep_color: '',
    card_bg: '',
    card_border: '',
    align: 'left',
  },

  fields: [
    { type: 'separator', label: t('Testi') },
    { key: 'eyebrow', label: t('Occhiello'), type: 'text' },
    { key: 'heading', label: t('Titolo'), type: 'text' },
    { key: 'intro', label: t('Introduzione'), type: 'textarea' },
    { key: 'base_label', label: t('Etichetta slider (città base = 1ª)'), type: 'text' },

    { type: 'separator', label: t('Orari') },
    { key: 'input_value', label: t('Ora iniziale (0-23)'), type: 'number' },
    { key: 'work_start', label: t('Inizio orario lavoro'), type: 'number' },
    { key: 'work_end', label: t('Fine orario lavoro'), type: 'number' },

    { type: 'separator', label: t('Città (la prima è la base)') },
    { key: 'items', label: t('Città'), type: 'content-items',
      itemLabel: t('Città'),
      defaults: { city: 'Nuova città', tz: 'Europe/Rome', offset: 0, label: '' },
      itemFields: [
        { key: 'city', label: t('Città'), type: 'text' },
        { key: 'tz', label: t('Fuso orario'), type: 'select', options: FUSI,
          description: t("Con il fuso l'ora legale si aggiorna da sola, ogni giorno dell'anno.") },
        { key: 'offset', label: t('Ore da UTC (es. 1, -7, 5.5)'), type: 'number', step: 0.5,
          condition: { key: 'tz', op: 'empty' } },
        { key: 'label', label: t('Sigla fuso (opzionale)'), type: 'text',
          description: t("Vuota: con un fuso scelto si vede lo scarto da UTC (UTC+1). Le sigle con l'ora legale (CET/CEST, PST/PDT…) cambiano da sole con la stagione.") },
      ],
    },
  ],

  styleFields: [
    { type: 'separator', label: t('Stile') },
    { key: 'zone_accent', label: t('Colore accento / lavoro'), type: 'color' },
    { key: 'ok_color', label: t('Colore "ai limiti"'), type: 'color' },
    { key: 'sleep_color', label: t('Colore "notte"'), type: 'color' },
    { key: 'card_bg', label: t('Sfondo righe città'), type: 'color' },
    { key: 'card_border', label: t('Bordo'), type: 'border', legacyWidth: 1 },
    { key: 'align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
    ]},
  ],
};
