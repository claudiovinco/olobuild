import { t } from '@/i18n';
import { oloxAccentField } from './_oloxShared.js';

/**
 * OLOX Statement — sezioni statement OLOtheme: contatore attacchi (counter),
 * "0/0" gigante (zerozero), pannello col timbro (stamp), testata semplice (plain).
 * Render Vue == PHP (OloxStatementTile.vue / class-oloxstatement-tile.php).
 */
export default {
  type: 'oloxstatement',
  name: t('OLOX — Statement (counter/0-0/stamp)'),
  icon: 'dashicons-megaphone',
  category: 'marketing',
  // Ritirata dalla palette: le pagine della replica olotheme.com sono composte
  // con tile classiche (headline, badge, info-cards, pricing, cta-banner, toc,
  // desclist, flipcard, counter, marquee). Resta per i template salvati.
  hidden: true,

  defaults: {
    accent: 'secur',
    variant: 'counter',
    anchor: '',
    kicker: 'Mentre leggevi questa pagina',
    title_html: 'WP Plugin Check: zero errori, <em>zero warning</em>',
    body_html: 'bloccati da un WordPress medio esposto in rete.',
    counter_to: 47,
    counter_after: 'tentativi',
    zz_text: '0/0',
    stamp_text: 'No-show ◦ Coperto',
    cta_text: '',
    cta_url: '',
  },

  fields: [
    { key: 'variant', label: t('Variante'), type: 'select', options: [
      { value: 'counter', label: t('Contatore gigante centrato') },
      { value: 'zerozero', label: t('0/0 + testo a fianco') },
      { value: 'stamp', label: t('Pannello con timbro') },
      { value: 'plain', label: t('Testata semplice + CTA') },
    ] },
    { key: 'kicker', label: t('Kicker'), type: 'text' },
    { key: 'title_html', label: t('Titolo (HTML)'), type: 'textarea' },
    { key: 'body_html', label: t('Testo (HTML)'), type: 'textarea' },
    { key: 'anchor', label: t('Ancora (id)'), type: 'text' },
    { type: 'separator', label: t('Contatore') },
    { key: 'counter_to', label: t('Valore finale'), type: 'number',
      condition: { field: 'variant', op: 'eq', value: 'counter' } },
    { key: 'counter_after', label: t('Parola dopo il numero'), type: 'text',
      condition: { field: 'variant', op: 'eq', value: 'counter' } },
    { type: 'separator', label: t('Varianti') },
    { key: 'zz_text', label: t('Testo gigante (0/0)'), type: 'text',
      condition: { field: 'variant', op: 'eq', value: 'zerozero' } },
    { key: 'stamp_text', label: t('Testo timbro'), type: 'text',
      condition: { field: 'variant', op: 'eq', value: 'stamp' } },
    { key: 'cta_text', label: t('CTA — testo'), type: 'text',
      condition: { field: 'variant', op: 'eq', value: 'plain' } },
    { key: 'cta_url', label: t('CTA — link'), type: 'link',
      condition: { field: 'variant', op: 'eq', value: 'plain' } },
  ],

  styleFields: [ oloxAccentField() ],
};
