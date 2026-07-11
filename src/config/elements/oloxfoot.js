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
    { key: 'show_credits', label: t('Credits fissi in basso'), type: 'toggle' },
    { key: 'credits_html', label: t('Testo credits (HTML)'), type: 'textarea',
      condition: { field: 'show_credits', op: 'eq', value: true } },
  ],

  styleFields: [ oloxAccentField() ],
};
