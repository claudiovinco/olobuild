import { t } from '@/i18n';
import { oloxAccentField } from './_oloxShared.js';

/**
 * OLOX Pricing — "la gru cala il Pro": lastre Free/Pro, la Pro scende
 * dall'alto appesa a un cavo tratteggiato con gancio.
 * Render Vue == PHP (OloxPricingTile.vue / class-oloxpricing-tile.php).
 */
export default {
  type: 'oloxpricing',
  name: t('OLOX — Pricing gru (Free/Pro)'),
  icon: 'dashicons-money-alt',
  category: 'marketing',
  // Ritirata dalla palette: le pagine della replica olotheme.com sono composte
  // con tile classiche (headline, badge, info-cards, pricing, cta-banner, toc,
  // desclist, flipcard, counter, marquee). Resta per i template salvati.
  hidden: true,

  defaults: {
    accent: 'build',
    anchor: 'prezzi',
    kicker: 'Due edizioni',
    title_html: 'La gru cala il <em>Pro</em>',
    free_kicker: 'OLObuild · Free',
    free_price: '€0',
    free_per: 'per sempre · GPL · su WP.org',
    free_items: [
      { text_html: '<strong>Oltre 100 tile nativi</strong> + form builder + dark mode' },
    ],
    free_cta: 'Scarica Free',
    free_url: './',
    pro_kicker: 'OLObuild · Pro',
    pro_price: '€29<em>*</em>',
    pro_per: '*prezzo lancio · poi €59/anno',
    pro_items: [
      { text_html: 'L’intera libreria: <strong>187 tile</strong>' },
    ],
    pro_cta: 'Passa a Pro',
    pro_url: './',
  },

  fields: [
    { type: 'separator', label: t('Testata') },
    { key: 'kicker', label: t('Kicker'), type: 'text' },
    { key: 'title_html', label: t('Titolo (HTML)'), type: 'textarea' },
    { key: 'anchor', label: t('Ancora (id)'), type: 'text' },
    { type: 'separator', label: t('Lastra Free') },
    { key: 'free_kicker', label: t('Kicker'), type: 'text' },
    { key: 'free_price', label: t('Prezzo (HTML)'), type: 'text' },
    { key: 'free_per', label: t('Sotto il prezzo'), type: 'text' },
    { key: 'free_items', label: t('Voci'), type: 'content-items', itemLabel: t('Voce'),
      defaults: { text_html: 'voce' },
      itemFields: [ { key: 'text_html', label: t('Testo (HTML)'), type: 'textarea' } ] },
    { key: 'free_cta', label: t('CTA — testo'), type: 'text' },
    { key: 'free_url', label: t('CTA — link'), type: 'link' },
    { type: 'separator', label: t('Lastra Pro (appesa alla gru)') },
    { key: 'pro_kicker', label: t('Kicker'), type: 'text' },
    { key: 'pro_price', label: t('Prezzo (HTML)'), type: 'text' },
    { key: 'pro_per', label: t('Sotto il prezzo'), type: 'text' },
    { key: 'pro_items', label: t('Voci'), type: 'content-items', itemLabel: t('Voce'),
      defaults: { text_html: 'voce' },
      itemFields: [ { key: 'text_html', label: t('Testo (HTML)'), type: 'textarea' } ] },
    { key: 'pro_cta', label: t('CTA — testo'), type: 'text' },
    { key: 'pro_url', label: t('CTA — link'), type: 'link' },
  ],

  styleFields: [ oloxAccentField() ],
};
