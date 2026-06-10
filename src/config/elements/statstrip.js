import { t } from '@/i18n';

/**
 * Stat Strip — banda orizzontale di statistiche/metriche (valore grande + etichetta),
 * con divisori tra le celle. Token-first: valore dal font heading del tema, etichetta in mono.
 * I valori sono testo libero (es. "500+", "92%", "40M", "14x").
 */
export default {
  type: 'statstrip',
  name: t('Stat Strip'),
  icon: 'dashicons-chart-bar',
  category: 'layout',

  defaults: {
    items: [
      { value: '500+', label: 'Progetti consegnati' },
      { value: '12', label: 'Anni di attività' },
      { value: '98%', label: 'Clienti soddisfatti' },
      { value: '40M', label: 'Utenti raggiunti' },
    ],
    columns: 4,
    items_gap: 0,
    band_padding_y: 40,

    show_dividers: true,
    divider_color: '#d7d1c2',
    band_border: true,

    value_font_family: 'heading',
    value_color: '#18181a',
    value_size: 48,
    value_weight: '600',

    label_color: '#8d8a82',
    label_size: 13,
    label_uppercase: false,

    align: 'left',
    mono_font_family: '',
  },

  // ═══ CONTENUTO ════════════════════════════════════════════════
  fields: [
    { type: 'separator', label: t('Statistiche') },
    { key: 'items', label: t('Voci'), type: 'content-items',
      itemLabel: t('Statistica'),
      defaults: { value: '00', label: 'Etichetta' },
      itemFields: [
        { key: 'value', label: t('Valore (es. 500+, 92%)'), type: 'text' },
        { key: 'label', label: t('Etichetta'), type: 'text' },
      ],
    },
  ],

  // ═══ STILE ════════════════════════════════════════════════════
  styleFields: [
    { type: 'separator', label: t('Banda') },
    { key: 'columns',        label: t('Colonne'), type: 'range', min: 1, max: 6, step: 1, responsive: true },
    { key: 'band_padding_y', label: t('Padding verticale (px)'), type: 'range', min: 0, max: 100, step: 2 },
    { key: 'align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left',   label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
    ]},

    { type: 'separator', label: t('Divisori') },
    { key: 'show_dividers', label: t('Divisori tra le celle'), type: 'toggle' },
    { key: 'band_border',   label: t('Bordo sopra e sotto'),   type: 'toggle' },
    { key: 'divider_color', label: t('Colore linee'),          type: 'color' },

    { type: 'separator', label: t('Valore') },
    { key: 'value_font_family', label: t('Famiglia'), type: 'font-family' },
    { key: 'value_color',  label: t('Colore'),          type: 'color' },
    { key: 'value_size',   label: t('Dimensione (px)'), type: 'range', min: 20, max: 96, step: 1, responsive: true },
    { key: 'value_weight', label: t('Peso'), type: 'select', options: [
      { value: '400', label: '400' }, { value: '500', label: '500' }, { value: '600', label: '600' }, { value: '700', label: '700' }, { value: '800', label: '800' },
    ]},

    { type: 'separator', label: t('Etichetta') },
    { key: 'mono_font_family', label: t('Font etichetta (vuoto = mono del tema)'), type: 'font-family' },
    { key: 'label_color',     label: t('Colore'),          type: 'color' },
    { key: 'label_size',      label: t('Dimensione (px)'), type: 'range', min: 10, max: 22, step: 1 },
    { key: 'label_uppercase', label: t('Maiuscolo'),       type: 'toggle' },
  ],
};
