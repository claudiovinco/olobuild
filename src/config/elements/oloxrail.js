import { t } from '@/i18n';

/**
 * OLOX Rail — il binario della Home Experience a sezioni: va nella PRIMA
 * sezione della pagina; raccoglie a runtime le fermate (tile OLOX Panel
 * nelle sezioni successive), le monta nel binario orizzontale e le numera.
 * Porta chrome fisso, progress, hint, alone, credits e modale "olonica".
 * Render Vue == PHP (OloxRailTile.vue / class-oloxrail-tile.php).
 */
export default {
  type: 'oloxrail',
  name: t('OLOX — Binario Experience (rail)'),
  icon: 'dashicons-slides',
  category: 'marketing',

  defaults: {
    logo: '',
    langs: [
      { code: 'IT', url: '/', active: true },
      { code: 'EN', url: '#', active: false },
      { code: 'FR', url: '#', active: false },
      { code: 'DE', url: '#', active: false },
      { code: 'ES', url: '#', active: false },
    ],
    op_kicker: 'olos · intero e parte',
    op_title: 'La cellula <em>olonica</em>',
    op_p1: 'Un <strong>olone</strong> è qualcosa che è insieme <strong>un tutto e una parte</strong>.',
    op_p2: 'Niente monolite: <strong>i prodotti si uniscono a seconda della battaglia</strong>.',
    battles: [
      { q: 'Aprire un B&B', chips: 'build,booking,lang' },
      { q: 'Respingere un attacco', chips: 'secur' },
    ],
    hint_desktop: 'Scrolla in basso',
    hint_desktop2: 'si va a destra',
    hint_mobile: 'Scorri',
    hint_mobile2: 'una fermata alla volta',
    credits_html: 'OLOtheme by <a href="https://clod.eu" target="_blank" rel="noopener">clod.eu</a> | @2026 | sito introduttivo | <a href="mailto:info@olotheme.com">info@olotheme.com</a>',
  },

  fields: [
    { type: 'separator', label: t('Chrome') },
    { key: 'logo', label: t('Logo (reso bianco)'), type: 'image' },
    { key: 'langs', label: t('Lingue'), type: 'content-items', itemLabel: t('Lingua'),
      defaults: { code: 'EN', url: '#', active: false },
      itemFields: [
        { key: 'code', label: t('Codice'), type: 'text' },
        { key: 'url', label: t('Link (# = non ancora attiva)'), type: 'link' },
        { key: 'active', label: t('Lingua corrente'), type: 'toggle' },
      ] },
    { key: 'credits_html', label: t('Credits fissi (HTML)'), type: 'textarea' },
    { type: 'separator', label: t('Modale "olonica"') },
    { key: 'op_kicker', label: t('Kicker'), type: 'text' },
    { key: 'op_title', label: t('Titolo (HTML)'), type: 'text' },
    { key: 'op_p1', label: t('Paragrafo 1 (HTML)'), type: 'textarea' },
    { key: 'op_p2', label: t('Paragrafo 2 (HTML)'), type: 'textarea' },
    { key: 'battles', label: t('Battaglie'), type: 'content-items', itemLabel: t('Battaglia'),
      defaults: { q: 'Obiettivo', chips: 'build,lang' },
      itemFields: [
        { key: 'q', label: t('Obiettivo'), type: 'text' },
        { key: 'chips', label: t('Prodotti (CSV: build,booking,lang,secur,tour,tutor)'), type: 'text' },
      ] },
    { type: 'separator', label: t('Hint scroll') },
    { key: 'hint_desktop', label: t('Hint desktop — prima parte'), type: 'text' },
    { key: 'hint_desktop2', label: t('Hint desktop — seconda parte'), type: 'text' },
    { key: 'hint_mobile', label: t('Hint mobile — prima parte'), type: 'text' },
    { key: 'hint_mobile2', label: t('Hint mobile — seconda parte'), type: 'text' },
  ],

  styleFields: [],
};
