import { t } from '@/i18n';
import { oloxAccentField } from './_oloxShared.js';

/**
 * OLOX Footer — footer OLOtheme (.dfoot) + credits fissi opzionali.
 * Render Vue == PHP (OloxFootTile.vue / class-oloxfoot-tile.php).
 */
export default {
  type: 'oloxfoot',
  name: t('OLOX — Footer'),
  icon: 'dashicons-align-wide',
  category: 'navigation',
  // Ritirata dalla palette: le pagine della replica olotheme.com sono composte
  // con tile classiche (headline, badge, info-cards, pricing, cta-banner, toc,
  // desclist, flipcard, counter, marquee). Resta per i template salvati.
  hidden: true,

  defaults: {
    logo: '',
    links: [
      { label: 'il viaggio', url: './' },
      { label: 'build', url: 'olobuild' },
      { label: 'booking', url: 'olobooking' },
      { label: 'lang', url: 'ololang' },
      { label: 'security', url: 'olosecurity' },
      { label: 'tour', url: 'olotour' },
    ],
    fine: 'GPL · Trento · no SaaS',
    links_auto: false,
    home_label: 'il viaggio',
    products: [
      { label: 'build', slug: 'olobuild' },
      { label: 'booking', slug: 'olobooking' },
      { label: 'lang', slug: 'ololang' },
      { label: 'security', slug: 'olosecurity' },
      { label: 'tour', slug: 'olotour' },
      { label: 'tutor', slug: 'olotutor' },
    ],
    fine_manual: 'manuali base · GPL · Trento',
    fine_overrides: 'olosecurity:GPL · Trento · no SaaS · 100% locale',
    show_credits: true,
    credits_html: 'OLOtheme by <a href="https://clod.eu" target="_blank" rel="noopener">clod.eu</a> | @2026 | sito introduttivo | <a href="mailto:info@olotheme.com">info@olotheme.com</a>',
    accent: 'olo',
  },

  fields: [
    { key: 'logo', label: t('Logo (reso bianco)'), type: 'image' },
    { key: 'links', label: t('Link'), type: 'content-items', itemLabel: t('Link'),
      defaults: { label: 'link', url: '#' },
      itemFields: [
        { key: 'label', label: t('Etichetta'), type: 'text' },
        { key: 'url', label: t('Link'), type: 'link' },
      ] },
    { key: 'fine', label: t('Riga finale destra'), type: 'text' },
    { type: 'separator', label: t('Modalità footer condiviso') },
    { key: 'links_auto', label: t('Link automatici (dalla pagina corrente)'), type: 'toggle',
      description: t('Prodotti: “il viaggio” + gli altri 5. Manuali: i 6 manuali con fine dedicata.') },
    { key: 'home_label', label: t('Etichetta link home'), type: 'text',
      condition: { field: 'links_auto', op: 'eq', value: true } },
    { key: 'products', label: t('Prodotti (per i link auto)'), type: 'content-items', itemLabel: t('Prodotto'),
      defaults: { label: 'prodotto', slug: 'slug' },
      itemFields: [
        { key: 'label', label: t('Etichetta'), type: 'text' },
        { key: 'slug', label: t('Slug pagina'), type: 'text' },
      ],
      condition: { field: 'links_auto', op: 'eq', value: true } },
    { key: 'fine_manual', label: t('Fine sui manuali'), type: 'text',
      condition: { field: 'links_auto', op: 'eq', value: true } },
    { key: 'fine_overrides', label: t('Eccezioni fine (slug:testo|slug:testo)'), type: 'text',
      condition: { field: 'links_auto', op: 'eq', value: true } },
    { key: 'show_credits', label: t('Credits fissi in basso'), type: 'toggle' },
    { key: 'credits_html', label: t('Testo credits (HTML)'), type: 'textarea',
      condition: { field: 'show_credits', op: 'eq', value: true } },
  ],

  styleFields: [ oloxAccentField() ],
};
