import { t } from '@/i18n';
import { oloxAccentField } from './_oloxShared.js';

/**
 * OLOX Cards — sezione .dsec con testata + griglia card nelle 6 varianti del
 * design OLOtheme (brick/ticket/red/room/hs/dcard).
 * Render Vue == PHP (OloxCardsTile.vue / class-oloxcards-tile.php).
 */
export default {
  type: 'oloxcards',
  name: t('OLOX — Sezione card'),
  icon: 'dashicons-grid-view',
  category: 'marketing',
  // Ritirata dalla palette: le pagine della replica olotheme.com sono composte
  // con tile classiche (headline, badge, info-cards, pricing, cta-banner, toc,
  // desclist, flipcard, counter, marquee). Resta per i template salvati.
  hidden: true,

  defaults: {
    accent: 'build',
    variant: 'brick',
    anchor: '',
    kicker: 'La libreria',
    title_html: '12 famiglie, posate come <em>mattoni</em>',
    lead: 'Ogni famiglia arriva da sinistra e da destra, come in cantiere.',
    head_center: false,
    items: [
      { label: '31', title: 'WooCommerce', text_html: 'Quickview, wishlist, comparazione, bundle.', extra: '' },
      { label: '22', title: 'Booking', text_html: 'Calendario disponibilità, picker, slot orari.', extra: '' },
    ],
    foot_html: '',
    foot_cta: '',
    foot_url: '',
    section_bg: '',
  },

  fields: [
    { type: 'separator', label: t('Testata sezione') },
    { key: 'kicker', label: t('Kicker'), type: 'text' },
    { key: 'title_html', label: t('Titolo (HTML)'), type: 'textarea' },
    { key: 'lead', label: t('Paragrafo'), type: 'textarea' },
    { key: 'head_center', label: t('Testata centrata'), type: 'toggle' },
    { key: 'anchor', label: t('Ancora (id sezione)'), type: 'text' },
    { type: 'separator', label: t('Card') },
    { key: 'variant', label: t('Variante'), type: 'select', options: [
      { value: 'brick', label: t('Mattoni con numero (build)') },
      { value: 'ticket', label: t('Biglietti con strappo (booking)') },
      { value: 'red', label: t('Schede classified (security)') },
      { value: 'room', label: t('Stanze + corridoi (tour)') },
      { value: 'hs', label: t('Hotspot con pallino (tour)') },
      { value: 'dcard', label: t('Card scure generiche') },
    ] },
    { key: 'items', label: t('Card'), type: 'content-items', itemLabel: t('Card'),
      defaults: { label: '', title: 'Titolo', text_html: 'Testo…', extra: '' },
      itemFields: [
        { key: 'label', label: t('Etichetta (numero / kicker / scena)'), type: 'text' },
        { key: 'title', label: t('Titolo (HTML ok)'), type: 'text' },
        { key: 'text_html', label: t('Testo (HTML)'), type: 'textarea' },
        { key: 'extra', label: t('Extra (codice biglietto / evidenzia stanza)'), type: 'text' },
      ] },
    { type: 'separator', label: t('Chiusura') },
    { key: 'foot_html', label: t('Paragrafo finale (HTML)'), type: 'textarea' },
    { key: 'foot_cta', label: t('CTA finale — testo'), type: 'text' },
    { key: 'foot_url', label: t('CTA finale — link'), type: 'link' },
  ],

  styleFields: [
    oloxAccentField(),
    { key: 'section_bg', label: t('Sfondo sezione (vuoto = trasparente)'), type: 'color' },
  ],
};
