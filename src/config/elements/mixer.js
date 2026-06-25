import { t } from '@/i18n';

/**
 * Mixer — zona interattiva "fondi swatch → preview": seleziona fino a `max` campioni
 * colore e vedi la media RGB. Estratto dai demo OLOthemes (setupMixer in fx.js).
 * Render Vue == PHP (MixerTile.vue). Blend = media RGB (no color-mix, robusto).
 */
export default {
  type: 'mixer',
  name: t('Mixer (blend colore)'),
  icon: 'dashicons-art',
  category: 'interactive',

  defaults: {
    eyebrow: t('Prova'),
    heading: t('Componi la tua tinta'),
    intro: '',
    max: 3,
    empty_label: t('Tocca i campioni per fondere'),
    items: [
      { name: 'Ocra', color: '#caa44a' },
      { name: 'Terra', color: '#9c6b4a' },
      { name: 'Crema', color: '#efe5da' },
      { name: 'Inchiostro', color: '#1a1a1a' },
    ],
    zone_accent: '',
    zone_on: 'var(--olo-color-surface, #ffffff)',
    card_bg: '',
    card_border: '',
    align: 'left',
  },

  fields: [
    { key: 'eyebrow', label: t('Occhiello'), type: 'text' },
    { key: 'heading', label: t('Titolo'), type: 'text' },
    { key: 'intro', label: t('Introduzione'), type: 'textarea' },

    { type: 'separator', label: t('Campioni') },
    { key: 'items', label: t('Swatch'), type: 'content-items',
      itemLabel: t('Swatch'),
      defaults: { name: 'Nuovo', color: '#cccccc' },
      itemFields: [
        { key: 'name', label: t('Nome'), type: 'text' },
        { key: 'color', label: t('Colore'), type: 'color' },
      ],
    },
    { key: 'max', label: t('Max selezioni'), type: 'number' },
    { key: 'empty_label', label: t('Testo iniziale (nessuna selezione)'), type: 'text' },
  ],

  styleFields: [
    { type: 'separator', label: t('Zona') },
    { key: 'zone_accent', label: t('Colore zona (accento)'), type: 'color' },
    { key: 'zone_on', label: t('Testo su accento'), type: 'color' },
    { key: 'card_bg', label: t('Sfondo pannello'), type: 'color' },
    { key: 'card_border', label: t('Bordo pannello'), type: 'color' },
    { key: 'align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
    ]},
  ],
};
