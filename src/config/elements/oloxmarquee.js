import { t } from '@/i18n';
import { oloxAccentField } from './_oloxShared.js';

/**
 * OLOX Marquee — nastro mono uppercase a scorrimento infinito (.dmarq)
 * con separatore colorato e direzione invertibile.
 * Render Vue == PHP (OloxMarqueeTile.vue / class-oloxmarquee-tile.php).
 */
export default {
  type: 'oloxmarquee',
  name: t('OLOX — Marquee'),
  icon: 'dashicons-ellipsis',
  category: 'marketing',

  defaults: {
    items: [
      { text: 'no SaaS' }, { text: 'GPL' }, { text: '187 tile' },
      { text: '28 lingue' }, { text: '100% locale' }, { text: 'made in Trento' },
    ],
    sep: '●',
    reverse: false,
    duration: 28,
    accent: 'olo',
  },

  fields: [
    { key: 'items', label: t('Voci'), type: 'content-items', itemLabel: t('Voce'),
      defaults: { text: 'voce' },
      itemFields: [ { key: 'text', label: t('Testo'), type: 'text' } ] },
    { key: 'sep', label: t('Separatore'), type: 'select', options: [
      { value: '●', label: '●' }, { value: '▪', label: '▪' }, { value: '✕', label: '✕' },
      { value: '·', label: '·' }, { value: '★', label: '★' },
    ] },
  ],

  styleFields: [
    oloxAccentField(t('Colore separatore')),
    { key: 'reverse', label: t('Direzione inversa'), type: 'toggle' },
    { key: 'duration', label: t('Durata loop (s)'), type: 'range', min: 8, max: 60, step: 1 },
  ],
};
