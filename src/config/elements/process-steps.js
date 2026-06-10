import { t } from '@/i18n';

const R = (n) => ({ tl: n, tr: n, br: n, bl: n, linked: true });

/**
 * Process Steps — passi numerati borderless (numero + titolo + testo, N colonne).
 * CONTENUTO: items. STILE: numero/titolo/desc + opzionale card.
 */
export default {
  type: 'process-steps',
  name: t('Process Steps'),
  icon: 'dashicons-editor-ol',
  category: 'layout',

  defaults: {
    items: [
      { number: '01', title: 'Listen', description: 'We start with your life, not your balance sheet.' },
      { number: '02', title: 'Plan', description: 'A clear strategy, modelled and stress-tested.' },
      { number: '03', title: 'Invest', description: 'Patient, diversified, low-cost where it counts.' },
      { number: '04', title: 'Review', description: 'We meet regularly and adjust as life changes.' },
    ],
    columns: 4,
    gap: 16,
    auto_number: false,
    number_style: 'plain',
    number_color: 'var(--olo-color-primary, #e1474f)',
    number_bg: '',
    number_size: 40,
    number_font: 'serif',
    number_weight: '500',
    title_color: '',
    title_size: 21,
    title_weight: '600',
    title_font: 'serif',
    desc_color: '',
    desc_size: 14,
    align: 'left',
    item_gap: 8,
    card_bg: '',
    card_border: '',
    card_radius: { ...R(0) },
    card_padding: 0,
  },

  fields: [
    { type: 'separator', label: t('Passi') },
    { key: 'items', label: t('Passi'), type: 'content-items',
      itemLabel: t('Passo'),
      defaults: { number: '00', title: 'Nuovo passo', description: 'Descrizione del passo.' },
      itemFields: [
        { key: 'number', label: t('Numero / etichetta'), type: 'text' },
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'description', label: t('Descrizione'), type: 'textarea' },
      ],
    },
    { key: 'auto_number', label: t('Numerazione automatica (01, 02, …)'), type: 'toggle' },
  ],

  styleFields: [
    { type: 'separator', label: t('Layout') },
    { key: 'columns', label: t('Colonne'), type: 'range', min: 1, max: 6, step: 1, responsive: true },
    { key: 'gap', label: t('Gap colonne (px)'), type: 'range', min: 0, max: 80, step: 2 },
    { key: 'align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},
    { key: 'item_gap', label: t('Gap interno (numero/titolo/testo)'), type: 'range', min: 0, max: 40, step: 2 },

    { type: 'separator', label: t('Numero') },
    { key: 'number_style', label: t('Stile numero'), type: 'select', options: [
      { value: 'plain', label: t('Semplice (grande)') },
      { value: 'circle', label: t('Cerchio pieno') },
      { value: 'outline', label: t('Cerchio bordato') },
    ]},
    { key: 'number_color', label: t('Colore numero'), type: 'color' },
    { key: 'number_bg', label: t('Sfondo/bordo cerchio'), type: 'color',
      condition: { field: 'number_style', operator: '!=', value: 'plain' } },
    { key: 'number_size', label: t('Dimensione (px)'), type: 'range', min: 12, max: 96, step: 2 },
    { key: 'number_font', label: t('Famiglia'), type: 'font-family' },
    { key: 'number_weight', label: t('Peso'), type: 'select', options: [
      { value: '300', label: '300' }, { value: '400', label: '400' }, { value: '500', label: '500' },
      { value: '600', label: '600' }, { value: '700', label: '700' }, { value: '800', label: '800' },
    ]},

    { type: 'separator', label: t('Titolo') },
    { key: 'title_color', label: t('Colore titolo'), type: 'color' },
    { key: 'title_size', label: t('Dimensione (px)'), type: 'range', min: 14, max: 48, step: 1 },
    { key: 'title_font', label: t('Famiglia'), type: 'font-family' },
    { key: 'title_weight', label: t('Peso'), type: 'select', options: [
      { value: '400', label: '400' }, { value: '500', label: '500' }, { value: '600', label: '600' },
      { value: '700', label: '700' }, { value: '800', label: '800' },
    ]},

    { type: 'separator', label: t('Descrizione') },
    { key: 'desc_color', label: t('Colore'), type: 'color' },
    { key: 'desc_size', label: t('Dimensione (px)'), type: 'range', min: 11, max: 22, step: 1 },

    { type: 'separator', label: t('Card (opzionale)') },
    { key: 'card_bg', label: t('Sfondo card'), type: 'color' },
    { key: 'card_border', label: t('Bordo card'), type: 'color' },
    { key: 'card_radius', label: t('Border radius'), type: 'border-radius' },
    { key: 'card_padding', label: t('Padding card (px)'), type: 'range', min: 0, max: 60, step: 2 },
  ],
};
