import { t } from '@/i18n';

/**
 * Builder — zona interattiva "componi e somma": righe con stepper +/− e totale live.
 * Estratto dai demo OLOthemes (setupBuilder in fx.js). Token-first: `zone_accent`.
 * Render Vue == PHP (BuilderTile.vue). Opzionale `cap` = max quantità totale.
 */
export default {
  type: 'builder',
  name: t('Builder (stepper + totale)'),
  icon: 'dashicons-cart',
  category: 'interactive',

  defaults: {
    eyebrow: t('Componi'),
    heading: t('Crea la tua selezione'),
    intro: '',
    currency: '€',
    cap: 0,
    items: [
      { name: 'Articolo A', price: '12', note: '', start: 0 },
      { name: 'Articolo B', price: '8', note: '', start: 0 },
      { name: 'Articolo C', price: '15', note: '', start: 0 },
    ],
    total_label: t('Totale'),
    count_label: t('articoli'),
    cta_text: t('Aggiungi al carrello'),
    cta_url: '#',
    zone_accent: '',
    zone_on: '#ffffff',
    card_bg: '',
    card_border: '',
    align: 'left',
    layout: 'panel',
    heading_accent: '',
    heading_color: '',
    tally_bg: '',
    item_name_color: '',
    item_price_color: '',
  },

  fields: [
    { key: 'eyebrow', label: t('Occhiello'), type: 'text' },
    { key: 'heading', label: t('Titolo'), type: 'text' },
    { key: 'heading_accent', label: t('Parola accento titolo'), type: 'text' },
    { key: 'intro', label: t('Introduzione'), type: 'textarea' },

    { type: 'separator', label: t('Articoli') },
    { key: 'items', label: t('Voci'), type: 'content-items',
      itemLabel: t('Voce'),
      defaults: { name: 'Nuova voce', price: '10', note: '', start: 0 },
      itemFields: [
        { key: 'name', label: t('Nome'), type: 'text' },
        { key: 'price', label: t('Prezzo'), type: 'text' },
        { key: 'note', label: t('Nota (opzionale)'), type: 'text' },
        { key: 'start', label: t('Quantità iniziale'), type: 'number' },
      ],
    },

    { type: 'separator', label: t('Calcolo') },
    { key: 'currency', label: t('Valuta'), type: 'text' },
    { key: 'cap', label: t('Limite quantità totale (0 = nessuno)'), type: 'number' },
    { key: 'total_label', label: t('Etichetta totale'), type: 'text' },
    { key: 'count_label', label: t('Etichetta conteggio'), type: 'text' },
    { key: 'cta_text', label: t('Testo CTA'), type: 'text' },
    { key: 'cta_url', label: t('URL CTA'), type: 'link' },
  ],

  styleFields: [
    { type: 'separator', label: t('Zona') },
    { key: 'zone_accent', label: t('Colore zona (accento)'), type: 'color' },
    { key: 'zone_on', label: t('Testo su accento'), type: 'color' },
    { key: 'card_bg', label: t('Sfondo pannello'), type: 'color' },
    { key: 'card_border', label: t('Bordo pannello'), type: 'color' },
    { key: 'align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
    ]},

    { type: 'separator', label: t('Layout') },
    { key: 'layout', label: t('Disposizione'), type: 'select', options: [
      { value: 'panel', label: t('Pannello (lista + footer)') },
      { value: 'split', label: t('Split (header + griglia card)') },
    ]},
    { key: 'heading_color', label: t('Colore titolo (split)'), type: 'color', condition: { field: 'layout', value: 'split' } },
    { key: 'tally_bg', label: t('Sfondo pill totale (split)'), type: 'color', condition: { field: 'layout', value: 'split' } },
    { key: 'item_name_color', label: t('Colore nome voce (split)'), type: 'color', condition: { field: 'layout', value: 'split' } },
    { key: 'item_price_color', label: t('Colore prezzo (split)'), type: 'color', condition: { field: 'layout', value: 'split' } },
  ],
};
