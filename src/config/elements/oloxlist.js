import { t } from '@/i18n';
import { oloxAccentField } from './_oloxShared.js';

/**
 * OLOX List — flipboard (righe che girano come un tabellone) e SEO stream
 * (pill URL che entrano alternate). Design pagina OLOlang.
 * Render Vue == PHP (OloxListTile.vue / class-oloxlist-tile.php).
 */
export default {
  type: 'oloxlist',
  name: t('OLOX — Lista flip/URL'),
  icon: 'dashicons-list-view',
  category: 'marketing',
  // Ritirata dalla palette: le pagine della replica olotheme.com sono composte
  // con tile classiche (headline, badge, info-cards, pricing, cta-banner, toc,
  // desclist, flipcard, counter, marquee). Resta per i template salvati.
  hidden: true,

  defaults: {
    accent: 'lang',
    variant: 'flip',
    anchor: '',
    kicker: 'Tradotto davvero',
    title_html: 'Ogni riga <em>gira</em> come un tabellone',
    lead: 'Non solo i testi: menu, stringhe di tema e plugin.',
    flip_items: [
      { src_label: 'contenuto · it', src_html: 'Prenota il tuo soggiorno', dst_label: 'content · en', dst_html: 'Book your stay' },
    ],
    url_items: [
      { html: 'https://tuosito.it<b>/it/</b>camere-vista-lago', ok: 'indicizzata' },
    ],
  },

  fields: [
    { key: 'variant', label: t('Variante'), type: 'select', options: [
      { value: 'flip', label: t('Flipboard (src ⇄ dst)') },
      { value: 'url', label: t('SEO stream (pill URL)') },
    ] },
    { type: 'separator', label: t('Testata') },
    { key: 'kicker', label: t('Kicker'), type: 'text' },
    { key: 'title_html', label: t('Titolo (HTML)'), type: 'textarea' },
    { key: 'lead', label: t('Paragrafo'), type: 'textarea' },
    { key: 'anchor', label: t('Ancora (id)'), type: 'text' },
    { type: 'separator', label: t('Righe flipboard') },
    { key: 'flip_items', label: t('Righe'), type: 'content-items', itemLabel: t('Riga'),
      defaults: { src_label: 'contenuto · it', src_html: 'Testo', dst_label: 'content · en', dst_html: 'Text' },
      itemFields: [
        { key: 'src_label', label: t('Label sinistra'), type: 'text' },
        { key: 'src_html', label: t('Testo sinistra (HTML)'), type: 'text' },
        { key: 'dst_label', label: t('Label destra'), type: 'text' },
        { key: 'dst_html', label: t('Testo destra (HTML)'), type: 'text' },
      ],
      condition: { field: 'variant', op: 'eq', value: 'flip' } },
    { type: 'separator', label: t('Pill URL') },
    { key: 'url_items', label: t('URL'), type: 'content-items', itemLabel: t('URL'),
      defaults: { html: 'https://tuosito.it<b>/en/</b>pagina', ok: 'indexed' },
      itemFields: [
        { key: 'html', label: t('URL (HTML, <b> evidenzia)'), type: 'text' },
        { key: 'ok', label: t('Badge verde a destra'), type: 'text' },
      ],
      condition: { field: 'variant', op: 'eq', value: 'url' } },
  ],

  styleFields: [ oloxAccentField() ],
};
