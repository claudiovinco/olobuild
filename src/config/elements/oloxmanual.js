import { t } from '@/i18n';
import { oloxAccentField } from './_oloxShared.js';

/**
 * OLOX Manual — pagina manuale tecnico completa: testata documento, TOC sticky
 * scrollspy, capitoli §n con corpo HTML, scheda tecnica di chiusura.
 * Render Vue == PHP (OloxManualTile.vue / class-oloxmanual-tile.php).
 */
export default {
  type: 'oloxmanual',
  name: t('OLOX — Manuale tecnico'),
  icon: 'dashicons-media-document',
  category: 'marketing',
  // Ritirata dalla palette: le pagine della replica olotheme.com sono composte
  // con tile classiche (headline, badge, info-cards, pricing, cta-banner, toc,
  // desclist, flipcard, counter, marquee). Resta per i template salvati.
  hidden: true,

  defaults: {
    accent: 'build',
    doc_codes: [
      { html: 'doc <b>OLO-BLD-M01</b>' },
      { html: 'manuale base' },
      { html: '+ scheda tecnica' },
    ],
    logo: '',
    title_html: 'Manuale <em>base</em>',
    sub_html: 'Cos’è OLObuild, come è costruito e perché regge 187 tile con un motore solo.',
    chapters: [
      { anchor: 'c1', no: '§1', title_html: 'Cos’è <em>OLObuild</em>', body_html: '<p>…</p>' },
    ],
    toc_spec: 'Scheda tecnica',
    spec_title: 'Scheda <em>tecnica</em>',
    spec_name: 'OLObuild',
    spec_sub: 'page builder · GPL',
    spec_rows: [
      { f: 'Tipo', text_html: 'Page builder WordPress tile-based, render server-side' },
    ],
    spec_cta1: '← Torna alla scheda prodotto',
    spec_url1: 'olobuild',
    spec_cta2: 'Il viaggio OLOtheme',
    spec_url2: './',
  },

  fields: [
    { type: 'separator', label: t('Testata documento') },
    { key: 'doc_codes', label: t('Codici documento'), type: 'content-items', itemLabel: t('Codice'),
      defaults: { html: 'doc <b>OLO-XXX-M01</b>' },
      itemFields: [ { key: 'html', label: t('Testo (HTML)'), type: 'text' } ] },
    { key: 'logo', label: t('Logo prodotto (reso bianco)'), type: 'image' },
    { key: 'title_html', label: t('Titolo (HTML)'), type: 'text' },
    { key: 'sub_html', label: t('Sottotitolo (HTML)'), type: 'textarea' },
    { type: 'separator', label: t('Capitoli') },
    { key: 'chapters', label: t('Capitoli'), type: 'content-items', itemLabel: t('Capitolo'),
      defaults: { anchor: 'c1', no: '§1', title_html: 'Titolo <em>capitolo</em>', body_html: '<p>Testo…</p>' },
      itemFields: [
        { key: 'anchor', label: t('Ancora (id)'), type: 'text' },
        { key: 'no', label: t('Numero (§1)'), type: 'text' },
        { key: 'title_html', label: t('Titolo (HTML)'), type: 'text' },
        { key: 'body_html', label: t('Corpo (HTML: p, ul.dash, notice, dtab)'), type: 'textarea' },
      ] },
    { type: 'separator', label: t('Scheda tecnica') },
    { key: 'toc_spec', label: t('Voce TOC'), type: 'text' },
    { key: 'spec_title', label: t('Titolo (HTML)'), type: 'text' },
    { key: 'spec_name', label: t('Nome prodotto'), type: 'text' },
    { key: 'spec_sub', label: t('Sotto il nome'), type: 'text' },
    { key: 'spec_rows', label: t('Righe tabella'), type: 'content-items', itemLabel: t('Riga'),
      defaults: { f: 'Voce', text_html: 'Valore' },
      itemFields: [
        { key: 'f', label: t('Voce (sinistra)'), type: 'text' },
        { key: 'text_html', label: t('Valore (HTML)'), type: 'textarea' },
      ] },
    { key: 'spec_cta1', label: t('CTA 1 — testo'), type: 'text' },
    { key: 'spec_url1', label: t('CTA 1 — link'), type: 'link' },
    { key: 'spec_cta2', label: t('CTA 2 — testo (ghost)'), type: 'text' },
    { key: 'spec_url2', label: t('CTA 2 — link'), type: 'link' },
  ],

  styleFields: [ oloxAccentField() ],
};
