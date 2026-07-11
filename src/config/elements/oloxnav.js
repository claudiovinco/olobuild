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
    exp_text: '← il viaggio',
    exp_url: './',
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
    { key: 'exp_text', label: t('Testo link esperienza'), type: 'text' },
    { key: 'exp_url', label: t('Link esperienza'), type: 'link' },
  ],

  styleFields: [
    oloxAccentField(t('Accento (hover/attivo)')),
  ],
};
