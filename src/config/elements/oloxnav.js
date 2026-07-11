import { t } from '@/i18n';
import { oloxAccentField } from './_oloxShared.js';

/**
 * OLOX Nav — barra di navigazione fissa del sito OLOtheme (.dnav):
 * logo bianco, link prodotto mono uppercase (attivo colorato, pallini su
 * mobile), lang switcher, link "← il viaggio" pill.
 * Render Vue == PHP (OloxNavTile.vue / class-oloxnav-tile.php).
 */
export default {
  type: 'oloxnav',
  name: t('OLOX — Nav prodotti'),
  icon: 'dashicons-menu-alt3',
  category: 'navigation',

  defaults: {
    logo: '',
    logo_url: './',
    links: [
      { label: 'build', url: 'olobuild', color: 'build', active: false },
      { label: 'booking', url: 'olobooking', color: 'booking', active: false },
      { label: 'lang', url: 'ololang', color: 'lang', active: false },
      { label: 'security', url: 'olosecurity', color: 'secur', active: false },
      { label: 'tour', url: 'olotour', color: 'tour', active: false },
      { label: 'tutor', url: 'olotutor', color: 'tutor', active: false },
    ],
    show_lang: true,
    langs: [
      { code: 'IT', url: '/', active: true },
      { code: 'EN', url: '#', active: false },
      { code: 'FR', url: '#', active: false },
      { code: 'DE', url: '#', active: false },
      { code: 'ES', url: '#', active: false },
    ],
    exp_text: '← il viaggio',
    exp_url: '/',
    active_auto: false,
    exp_auto: false,
    exp_manual_text: '← scheda prodotto',
    accent: 'olo',
  },

  fields: [
    { type: 'separator', label: t('Logo') },
    { key: 'logo', label: t('Logo (reso bianco)'), type: 'image' },
    { key: 'logo_url', label: t('Link logo'), type: 'link' },
    { type: 'separator', label: t('Link prodotti') },
    { key: 'links', label: t('Link'), type: 'content-items', itemLabel: t('Link'),
      defaults: { label: 'prodotto', url: '#', color: 'olo', active: false },
      itemFields: [
        { key: 'label', label: t('Etichetta'), type: 'text' },
        { key: 'url', label: t('Link'), type: 'link' },
        { key: 'color', label: t('Colore'), type: 'select', options: [
          { value: 'build', label: 'build' }, { value: 'booking', label: 'booking' },
          { value: 'lang', label: 'lang' }, { value: 'secur', label: 'security' },
          { value: 'tour', label: 'tour' }, { value: 'tutor', label: 'tutor' }, { value: 'olo', label: 'olo' },
        ] },
        { key: 'active', label: t('Attivo (pagina corrente)'), type: 'toggle' },
      ],
    },
    { type: 'separator', label: t('Destra') },
    { key: 'show_lang', label: t('Mostra switch lingua'), type: 'toggle' },
    { key: 'langs', label: t('Lingue'), type: 'content-items', itemLabel: t('Lingua'),
      defaults: { code: 'EN', url: '#', active: false },
      itemFields: [
        { key: 'code', label: t('Codice'), type: 'text' },
        { key: 'url', label: t('Link (# = non ancora attiva)'), type: 'link' },
        { key: 'active', label: t('Lingua corrente'), type: 'toggle' },
      ],
      condition: { field: 'show_lang', op: 'eq', value: true } },
    { key: 'exp_text', label: t('Testo link esperienza'), type: 'text' },
    { key: 'exp_url', label: t('Link esperienza'), type: 'link' },
    { type: 'separator', label: t('Modalità header condiviso') },
    { key: 'active_auto', label: t('Link attivo automatico (dalla pagina corrente)'), type: 'toggle',
      description: t('Per l’uso come template Header: lo slug X o X-manuale attiva il prodotto X.') },
    { key: 'exp_auto', label: t('Pill automatica sui manuali (← scheda prodotto)'), type: 'toggle' },
    { key: 'exp_manual_text', label: t('Testo pill sui manuali'), type: 'text',
      condition: { field: 'exp_auto', op: 'eq', value: true } },
  ],

  styleFields: [
    oloxAccentField(t('Accento (hover/attivo)')),
  ],
};
