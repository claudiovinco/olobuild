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

  // Appena nata: i quattro passi con cui lavora uno studio o un'attività di servizi, in
  // italiano, col numero dentro un cerchio tinto del colore primario e più respiro fra le colonne.
  partenza: {
    items: [
      { number: '01', title: t('Ascolto'), description: t('Un primo incontro, anche online, per capire obiettivi, tempi e budget.') },
      { number: '02', title: t('Proposta'), description: t('Ti presentiamo un progetto chiaro, con fasi e costi definiti fin dall\'inizio.') },
      { number: '03', title: t('Realizzazione'), description: t('Seguiamo ogni dettaglio e ti aggiorniamo a ogni passaggio importante.') },
      { number: '04', title: t('Assistenza'), description: t('Restiamo al tuo fianco anche dopo la consegna, ogni volta che serve.') },
    ],
    gap: 32,
    item_gap: 12,
    number_style: 'circle',
    number_bg: 'color-mix(in srgb, var(--olo-color-primary, #e1474f) 14%, transparent)',
    number_size: 36,
    number_weight: '600',
  },

  fields: [
    { key: 'items', label: t('Passi'), type: 'content-items', etichettaDa: 'title|number',
      itemLabel: t('Passo'),
      defaults: { number: '00', title: t('Nuovo passo'), description: t('Descrizione del passo.') },
      itemFields: [
        { key: 'number', label: t('Numero o scritta'), type: 'text',
          condizioneTile: { field: 'auto_number', operator: '!=', value: true } },
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'description', label: t('Descrizione'), type: 'textarea' },
      ],
    },
    { key: 'auto_number', label: t('Numeri automatici (01, 02, …)'), type: 'toggle',
      description: t('Accesa, i passi si numerano da soli nell’ordine in cui stanno e il campo «Numero» di ogni voce non conta.') },
  ],

  // Zone standard (16 nov 2026): Testo · Numero · Schede · Disposizione. Le colonne sono un massimo: in una colonna
  // di pagina stretta i passi vanno a capo (prima uscivano dalla tile).
  styleFields: [
    { type: 'separator', label: t('Testo') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Numero'), responsiveKeys: [], keys: { size: 'number_size', family: 'number_font', weight: 'number_weight', color: 'number_color' }, sizeMin: 12, sizeMax: 96, sizeStep: 2 },
    { type: 'typography', label: t('Titolo'), responsiveKeys: [], keys: { size: 'title_size', family: 'title_font', weight: 'title_weight', color: 'title_color' }, sizeMin: 14, sizeMax: 48 },
    { type: 'typography', label: t('Descrizione'), responsiveKeys: [], keys: { size: 'desc_size', color: 'desc_color' }, sizeMin: 11, sizeMax: 22 },

    { type: 'separator', label: t('Numero') },
    { key: 'number_style', label: t('Forma'), type: 'select', options: [
      { value: 'plain', label: t('Solo il numero, grande') },
      { value: 'circle', label: t('Cerchio pieno') },
      { value: 'outline', label: t('Cerchio col bordo') },
    ]},
    { key: 'number_bg', label: t('Colore cerchio'), type: 'color',
      description: t('Il fondo del cerchio pieno o il bordo del cerchio vuoto.'),
      condition: { field: 'number_style', operator: '!=', value: 'plain' } },

    { type: 'separator', label: t('Schede') },
    { key: 'card_bg', label: t('Sfondo scheda'), type: 'color',
      description: t('Vuoto: i passi stanno sul fondo della sezione, senza scheda.') },
    { key: 'card_border', label: t('Bordo scheda'), type: 'border', legacyWidth: 1 },
    { key: 'card_radius', label: t('Raggio scheda'), type: 'border-radius' },
    { key: 'card_padding', label: t('Padding scheda'), type: 'spacing', min: 0, max: 80 },

    { type: 'separator', label: t('Disposizione') },
    { key: 'columns', label: t('Colonne'), type: 'range', min: 1, max: 6, step: 1, responsive: true,
      description: t('Il massimo: dove la colonna è stretta i passi vanno a capo da soli.') },
    { key: 'gap', label: t('Gap colonne'), type: 'range', unit: 'px', min: 0, max: 80, step: 2 },
    { key: 'item_gap', label: t('Gap interno'), type: 'range', unit: 'px', min: 0, max: 40, step: 2,
      description: t('Lo spazio fra numero, titolo e descrizione.') },
    { key: 'align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
    ]},
  ],
};
