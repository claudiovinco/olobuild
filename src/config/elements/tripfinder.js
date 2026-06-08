import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Trip Finder — barra di ricerca/prenotazione compatta: N campi (label + select)
 * + bottone. Estratta dai blueprint OLOthemes (TripFinder/BookingBar: fjordline,
 * wander, pasaje). Token-first (`accent`). Render Vue == PHP (TripFinderTile.vue).
 */
export default {
  type: 'tripfinder',
  name: t('Trip Finder (barra ricerca)'),
  icon: 'dashicons-search',
  category: 'interactive',

  defaults: {
    fields: [
      { label: 'Destination', value: 'Anywhere', options: 'Anywhere\nNorway · Lofoten\nIceland\nGreenland' },
      { label: 'When', value: 'Any month', options: 'Any month\nMar — aurora\nJun — midnight sun\nSep — autumn light' },
      { label: 'Activity', value: 'Any', options: 'Any\nHiking & trekking\nSea kayaking\nWildlife' },
    ],
    button_text: 'Search',
    button_url: '#',
    accent: '',
    accent_on: '#ffffff',
    bar_bg: '',
    field_bg: '',
    field_border: '',
    label_color: '',
    value_color: '',
    radius: 14,

    // SPAZIATURA additiva — default = padding storico della barra (8px su 4 lati)
    // e dei campi (10px verticale / 16px orizzontale). Render invariato coi default.
    bar_padding: { top: 8, right: 8, bottom: 8, left: 8 },
    field_padding: { top: 10, right: 16, bottom: 10, left: 16 },

    // FORMA additiva — raggio per-angolo della barra/bottone. Default tutto 0 →
    // fallback al raggio uniforme storico `radius` (no-op).
    radius_corners: { tl: 0, tr: 0, br: 0, bl: 0 },

    // KIT standard OLObuild — sfondo completo + ombra + bordo sul contenitore.
    // Default no-op: bg none / shadow none / border 0 → render invariato.
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { type: 'separator', label: t('Campi') },
    { key: 'fields', label: t('Campi della barra'), type: 'content-items',
      itemLabel: t('Campo'),
      defaults: { label: 'Nuovo campo', value: 'Tutti', options: 'Tutti\nOpzione A\nOpzione B' },
      itemFields: [
        { key: 'label', label: t('Etichetta'), type: 'text' },
        { key: 'value', label: t('Valore predefinito'), type: 'text' },
        { key: 'options', label: t('Opzioni (una per riga)'), type: 'textarea' },
      ],
    },
    { type: 'separator', label: t('Bottone') },
    { key: 'button_text', label: t('Testo bottone'), type: 'text' },
    { key: 'button_url', label: t('URL bottone'), type: 'link' },
  ],

  styleFields: [
    { type: 'separator', label: t('Colori') },
    { key: 'accent', label: t('Accento (bottone)'), type: 'color',
      description: t('Sfondo del bottone; vuoto = primario del tema.') },
    { key: 'accent_on', label: t('Testo su accento'), type: 'color' },
    { key: 'bar_bg', label: t('Sfondo barra'), type: 'color' },
    { key: 'field_bg', label: t('Sfondo campo'), type: 'color' },
    { key: 'field_border', label: t('Bordo / divisori'), type: 'color' },
    { key: 'label_color', label: t('Colore etichette'), type: 'color' },
    { key: 'value_color', label: t('Colore valori'), type: 'color' },

    { type: 'separator', label: t('Forma') },
    { key: 'radius', label: t('Raggio bordo (px)'), type: 'range', min: 0, max: 40, step: 1 },
    { key: 'radius_corners', label: t('Raggio per angolo (override)'), type: 'border-radius',
      description: t('Lascia tutto a 0 per usare il «Raggio bordo» uniforme qui sopra.') },

    { type: 'separator', label: t('Spaziatura') },
    { key: 'bar_padding', label: t('Padding barra (px)'), type: 'spacing', min: 0, max: 64 },
    { key: 'field_padding', label: t('Padding campi (px)'), type: 'spacing', min: 0, max: 64 },

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },

    { type: 'separator', label: t('Ombra') },
    ...shadowField,

    ...borderFields(),
  ],
};
