import { t } from '@/i18n';
import { oloxAccentField } from './_oloxShared.js';

/**
 * OLOX Banner — follow ("In arrivo" con chip tratteggiata) e next
 * ("prossima fermata" con grande link Fraunces).
 * Render Vue == PHP (OloxBannerTile.vue / class-oloxbanner-tile.php).
 */
export default {
  type: 'oloxbanner',
  name: t('OLOX — Banner follow/next'),
  icon: 'dashicons-migrate',
  category: 'marketing',

  defaults: {
    variant: 'next',
    accent: 'olo',
    fk_text: 'In arrivo',
    body_html: 'Versione demo o gratuita/completa in arrivo: segui <a href="https://www.linkedin.com/company/olotheme/" target="_blank" rel="noopener">OLOtheme su LinkedIn</a> per rimanere aggiornato.',
    label: 'Prossima fermata',
    link_html: 'OLO<em>booking</em> →',
    link_url: 'olobooking',
  },

  fields: [
    { key: 'variant', label: t('Variante'), type: 'select', options: [
      { value: 'next', label: t('Next — prossima fermata') },
      { value: 'follow', label: t('Follow — banner in arrivo') },
    ] },
    { key: 'fk_text', label: t('Chip (follow)'), type: 'text',
      condition: { field: 'variant', op: 'eq', value: 'follow' } },
    { key: 'body_html', label: t('Testo con link (HTML)'), type: 'textarea',
      condition: { field: 'variant', op: 'eq', value: 'follow' } },
    { key: 'label', label: t('Label piccola (next)'), type: 'text',
      condition: { field: 'variant', op: 'eq', value: 'next' } },
    { key: 'link_html', label: t('Link grande (HTML, <em> colorato)'), type: 'text',
      condition: { field: 'variant', op: 'eq', value: 'next' } },
    { key: 'link_url', label: t('Destinazione'), type: 'link',
      condition: { field: 'variant', op: 'eq', value: 'next' } },
  ],

  styleFields: [ oloxAccentField() ],
};
