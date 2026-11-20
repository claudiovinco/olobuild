import { t } from '@/i18n';

/**
 * Indice argomenti — l'elenco a lato di un manuale (Olobuild_Argomenti_Tile): argomenti e sottoargomenti di un tipo di
 * contenuto con «Argomenti e sottoargomenti» (Impostazioni › Tipi di contenuto), o delle Pagine, in ordine di lettura.
 *   fields[]      → quale tipo e quali argomenti, titolo, ricerca, rami aperti, schermi piccoli (per tenerlo in vista:
 *                   «Scroll fisso» della colonna, che conosce la testata fissa)
 *   styleFields[] → Aspetto (colori) · Testo (voci, titolo) · Forma (raggio e padding delle voci) · Disposizione
 */
export default {
  type: 'argomenti',
  name: t('Indice argomenti'),
  icon: 'dashicons-list-view',
  category: 'navigation',
  defaults: {
    fonte: '',
    quali: 'tutti',
    titolo: t('Argomenti'),
    cerca: true,
    aperti: 'ramo',
    telefono: 'chiuso',
    text_color: '',
    active_color: '',
    active_bg: '',
    line_color: '',
    font_family: '',
    font_size: 15,
    font_weight: '400',
    title_color: '',
    title_size: 12,
    title_weight: '700',
    title_transform: 'uppercase',
    item_radius: { tl: 8, tr: 8, br: 8, bl: 8 },
    item_padding: { top: 6, right: 10, bottom: 6, left: 10 },
    indent: 14,
    gap: 2,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Argomenti') },
    { key: 'fonte', label: t('Tipo di contenuto'), type: 'select', optionsSource: 'tipiArgomenti',
      description: t('Gli argomenti di quale tipo. «Quello del contenuto aperto» va bene nel template del tipo: ogni pagina mostra il suo manuale.') },
    { key: 'quali', label: t('Quali argomenti'), type: 'select', options: [
      { value: 'tutti', label: t('Tutti') },
      { value: 'ramo', label: t('Solo quelli del suo argomento principale') },
      { value: 'figli', label: t('Solo i sottoargomenti del contenuto aperto') },
    ] },
    { key: 'titolo', label: t('Titolo'), type: 'text' },

    { type: 'separator', label: t('Comportamento') },
    { key: 'cerca', label: t('Casella di ricerca'), type: 'toggle' },
    { key: 'aperti', label: t('Rami aperti'), type: 'select', options: [
      { value: 'ramo', label: t('Solo quello del contenuto aperto') },
      { value: 'tutti', label: t('Tutti') },
    ] },
    { key: 'telefono', label: t('Su schermi piccoli'), type: 'select', options: [
      { value: 'chiuso', label: t('Chiuso sotto un pulsante') },
      { value: 'aperto', label: t('Aperto') },
    ] },
    // Lo sticky lo fa la colonna (conosce la testata fissa): quello di una tile funziona di rado.
    { key: '_in_vista', type: 'description', label: '',
      description: t('Per tenere l\'indice fermo a lato mentre la pagina scorre, attiva «Scroll fisso» nella colonna che lo contiene. Se è più alto della finestra, l\'indice scorre da sé. Sugli schermi piccoli, dove sta sopra il testo, segue la pagina.') },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'active_color', label: t('Colore voce aperta'), type: 'color' },
    { key: 'active_bg', label: t('Sfondo voce aperta'), type: 'color' },
    { key: 'line_color', label: t('Colore linee'), type: 'color' },

    { type: 'separator', label: t('Testo') },
    { type: 'typography', label: t('Voci'),
      responsiveKeys: [],
      keys: {
        family: 'font_family',
        size:   'font_size',
        weight: 'font_weight',
        color:  'text_color',
      },
      sizeMin: 11, sizeMax: 24,
    },
    { type: 'typography', label: t('Titolo'),
      responsiveKeys: [],
      keys: {
        size:      'title_size',
        weight:    'title_weight',
        transform: 'title_transform',
        color:     'title_color',
      },
      sizeMin: 10, sizeMax: 24,
      condition: { field: 'titolo', op: 'notEmpty' },
    },

    { type: 'separator', label: t('Forma') },
    { key: 'item_radius', label: t('Raggio voci'), type: 'border-radius' },
    { key: 'item_padding', label: t('Padding voci'), type: 'spacing', min: 0, max: 30 },

    { type: 'separator', label: t('Disposizione') },
    { key: 'indent', label: t('Rientro sottoargomenti px'), type: 'range', min: 0, max: 48, step: 2 },
    { key: 'gap', label: t('Gap voci px'), type: 'range', min: 0, max: 24, step: 1 },
  ],
};
