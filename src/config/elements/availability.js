import { t } from '@/i18n';

/**
 * Availability — zona interattiva: griglia toggle (fasce × giorni) → conteggio →
 * verdetto a soglie (es. track Reset/Build/Peak). Estratto dai demo OLOthemes
 * (AvailabilityHeat). Render Vue == PHP (AvailabilityTile.vue). Lookup conteggio→tier
 * precomputato lato PHP → JS senza '&&' né '<'/'>'.
 */
export default {
  type: 'availability',
  name: t('Availability (griglia → verdetto)'),
  icon: 'dashicons-calendar',
  category: 'interactive',

  defaults: {
    eyebrow: '',
    heading: t('Quando puoi?'),
    intro: '',
    days: 'Mon, Tue, Wed, Thu, Fri, Sat, Sun',
    bands: 'Morning, Midday, Evening',
    count_label: t('Slot scelti'),
    verdict_label: t('Consigliato'),
    tiers: [
      { min: 0, label: 'Reset', text: 'Poco tempo: una base sostenibile.' },
      { min: 5, label: 'Build', text: 'Buona costanza: progressione vera.' },
      { min: 10, label: 'Peak', text: 'Massima disponibilità: spingi.' },
    ],
    zone_accent: '',
    zone_on: 'var(--olo-color-surface, #ffffff)',
    cell_bg: '',
    card_border: '',
    align: 'left',
  },

  fields: [
    { key: 'eyebrow', label: t('Occhiello'), type: 'text' },
    { key: 'heading', label: t('Titolo'), type: 'text' },
    { key: 'intro', label: t('Introduzione'), type: 'textarea' },
    { key: 'days', label: t('Giorni (separati da virgola)'), type: 'text' },
    { key: 'bands', label: t('Fasce (righe, separate da virgola)'), type: 'text' },
    { key: 'count_label', label: t('Etichetta conteggio'), type: 'text' },
    { key: 'verdict_label', label: t('Etichetta verdetto'), type: 'text' },

    { type: 'separator', label: t('Soglie verdetto (min crescente)') },
    { key: 'tiers', label: t('Tier'), type: 'content-items',
      itemLabel: t('Tier'),
      newItemDefaults: { min: 0, label: 'Tier', text: 'Descrizione.' },
      itemFields: [
        { key: 'min', label: t('Slot minimi'), type: 'number' },
        { key: 'label', label: t('Etichetta'), type: 'text' },
        { key: 'text', label: t('Testo'), type: 'textarea' },
      ],
    },
  ],

  styleFields: [
    { type: 'separator', label: t('Zona') },
    { key: 'zone_accent', label: t('Colore slot attivo'), type: 'color' },
    { key: 'zone_on', label: t('Testo su accento'), type: 'color' },
    { key: 'cell_bg', label: t('Sfondo celle'), type: 'color' },
    { key: 'card_border', label: t('Bordo'), type: 'color' },
    { key: 'align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
    ]},
  ],
};
