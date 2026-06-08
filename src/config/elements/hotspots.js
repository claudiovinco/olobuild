import { t } from '@/i18n';

/**
 * Hotspots — pannello con marker posizionati (x%,y%): click → scheda info.
 * Estratto dai demo OLOthemes (data-hotspot). Image-free: pannello astratto.
 * Render Vue == PHP (HotspotsTile.vue). Runtime inline scoped, senza '&&'.
 */
export default {
  type: 'hotspots',
  name: t('Hotspots (marker interattivi)'),
  icon: 'dashicons-location',
  category: 'interactive',

  defaults: {
    eyebrow: '',
    heading: t('Esplora'),
    intro: '',
    panel_label: 'SCENE',
    aspect_ratio: '16/10',
    items: [
      { x: 28, y: 36, title: 'Punto 1', text: 'Descrizione del punto.', meta: '' },
      { x: 62, y: 58, title: 'Punto 2', text: 'Descrizione del punto.', meta: '' },
      { x: 44, y: 74, title: 'Punto 3', text: 'Descrizione del punto.', meta: '' },
    ],
    zone_accent: '',
    zone_on: '#ffffff',
    panel_bg: '',
    card_bg: '',
    card_border: '',
    align: 'left',
  },

  fields: [
    { key: 'eyebrow', label: t('Occhiello'), type: 'text' },
    { key: 'heading', label: t('Titolo'), type: 'text' },
    { key: 'intro', label: t('Introduzione'), type: 'textarea' },
    { key: 'panel_label', label: t('Etichetta pannello'), type: 'text' },
    { key: 'aspect_ratio', label: t('Proporzioni pannello'), type: 'select', options: [
      { value: '16/10', label: '16:10' }, { value: '16/9', label: '16:9' },
      { value: '4/3', label: '4:3' }, { value: '3/2', label: '3:2' }, { value: '1/1', label: '1:1' },
    ]},

    { type: 'separator', label: t('Marker') },
    { key: 'items', label: t('Punti'), type: 'content-items',
      itemLabel: t('Marker'),
      defaults: { x: 50, y: 50, title: 'Nuovo punto', text: 'Descrizione.', meta: '' },
      itemFields: [
        { key: 'x', label: t('Posizione X (%)'), type: 'number' },
        { key: 'y', label: t('Posizione Y (%)'), type: 'number' },
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'text', label: t('Testo'), type: 'textarea' },
        { key: 'meta', label: t('Meta (prezzo/nota)'), type: 'text' },
      ],
    },
  ],

  styleFields: [
    { type: 'separator', label: t('Stile') },
    { key: 'zone_accent', label: t('Colore marker'), type: 'color' },
    { key: 'zone_on', label: t('Testo su accento'), type: 'color' },
    { key: 'panel_bg', label: t('Sfondo pannello'), type: 'color' },
    { key: 'card_bg', label: t('Sfondo scheda info'), type: 'color' },
    { key: 'card_border', label: t('Bordo'), type: 'color' },
    { key: 'align', label: t('Allineamento testo'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
    ]},
  ],
};
