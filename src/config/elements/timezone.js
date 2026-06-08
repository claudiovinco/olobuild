import { t } from '@/i18n';

/**
 * Timezone — zona interattiva: slider ora (città base) → orari locali delle città
 * con stato lavoro/limite/notte (pallino colorato). Estratto dai demo OLOthemes.
 * Render Vue == PHP (TimezoneTile.vue). Stati precomputati lato PHP → JS senza '<'/'>'.
 */
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
      { city: 'San Francisco', offset: -7, label: 'PDT' },
      { city: 'London', offset: 1, label: 'BST' },
      { city: 'Berlin', offset: 2, label: 'CEST' },
      { city: 'Singapore', offset: 8, label: 'SGT' },
    ],
    zone_accent: '',
    work_color: '',
    ok_color: '#e0a23a',
    sleep_color: '',
    card_bg: '',
    card_border: '',
    align: 'left',
  },

  fields: [
    { key: 'eyebrow', label: t('Occhiello'), type: 'text' },
    { key: 'heading', label: t('Titolo'), type: 'text' },
    { key: 'intro', label: t('Introduzione'), type: 'textarea' },
    { key: 'base_label', label: t('Etichetta slider (città base = 1ª)'), type: 'text' },
    { key: 'input_value', label: t('Ora iniziale (0-23)'), type: 'number' },
    { key: 'work_start', label: t('Inizio orario lavoro'), type: 'number' },
    { key: 'work_end', label: t('Fine orario lavoro'), type: 'number' },

    { type: 'separator', label: t('Città (la prima è la base)') },
    { key: 'items', label: t('Città'), type: 'content-items',
      itemLabel: t('Città'),
      defaults: { city: 'Nuova città', offset: 0, label: '' },
      itemFields: [
        { key: 'city', label: t('Città'), type: 'text' },
        { key: 'offset', label: t('Offset UTC (es. 1, -7)'), type: 'number' },
        { key: 'label', label: t('Sigla fuso (opzionale)'), type: 'text' },
      ],
    },
  ],

  styleFields: [
    { type: 'separator', label: t('Stile') },
    { key: 'zone_accent', label: t('Colore accento / lavoro'), type: 'color' },
    { key: 'ok_color', label: t('Colore "ai limiti"'), type: 'color' },
    { key: 'sleep_color', label: t('Colore "notte"'), type: 'color' },
    { key: 'card_bg', label: t('Sfondo righe città'), type: 'color' },
    { key: 'card_border', label: t('Bordo'), type: 'color' },
    { key: 'align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
    ]},
  ],
};
