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
    typography_preset: '',
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
    { key: 'gap', label: t('Gap colonne'), type: 'range', min: 0, max: 80, step: 2 },
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
    { key: 'number_bg', label: t('Sfondo/bordo cerchio'), type: 'color',
      condition: { field: 'number_style', operator: '!=', value: 'plain' } },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Numero'), responsiveKeys: [], keys: { size: 'number_size', family: 'number_font', weight: 'number_weight', color: 'number_color' }, sizeMin: 12, sizeMax: 96, sizeStep: 2 },

    { type: 'separator', label: t('Titolo') },
    { type: 'typography', label: t('Titolo'), responsiveKeys: [], keys: { size: 'title_size', family: 'title_font', weight: 'title_weight', color: 'title_color' }, sizeMin: 14, sizeMax: 48 },

    { type: 'separator', label: t('Descrizione') },
    { type: 'typography', label: t('Descrizione'), responsiveKeys: [], keys: { size: 'desc_size', color: 'desc_color' }, sizeMin: 11, sizeMax: 22 },

    { type: 'separator', label: t('Card (opzionale)') },
    { key: 'card_bg', label: t('Sfondo card'), type: 'color' },
    { key: 'card_border', label: t('Bordo card'), type: 'border', legacyWidth: 1 },
    { key: 'card_radius', label: t('Raggio'), type: 'border-radius' },
    { key: 'card_padding', label: t('Padding card'), type: 'spacing', min: 0, max: 80 },
  ],
};
