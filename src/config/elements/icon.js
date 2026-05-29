import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { buildDefaults } from '@/composables/oloTileDefaults';
import { t } from '@/i18n';

/**
 * Tile Icon — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → icona, link
 *   styleFields[] → dimensione, colori, sfondo, forma, padding, hover-anim, rotazione, ombra, bordo
 *   AVANZATE      → meta tecnico
 */
export default {
  type: 'icon',
  name: t('Icona'),
  icon: 'dashicons-star-filled',
  category: 'essential',
  // Default da FONTE UNICA (buildDefaults): icon/size/color:''/view/bg_color:''/
  // bg_shape/tile_padding(SPACE)/hover_animation/rotation.
  defaults: {
    ...buildDefaults('icon'),
    secondary_color: '',
    link_url: '',
    link_target: '_self',
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'icon', label: t('Nome icona'), type: 'icon' },
    { key: 'link_url', label: t('URL link'), type: 'link' },
    { key: 'link_target', label: t('Apri in'), type: 'select', options: [
      { value: '_self', label: t('Stessa finestra') },
      { value: '_blank', label: t('Nuova finestra') },
    ]},
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'size', label: t('Dimensione (px)'), type: 'range', min: 16, max: 120, step: 4 },
    { key: 'color', label: t('Colore'), type: 'color' },
    { key: 'view', label: t('Visualizzazione'), type: 'select', options: [
      { value: 'default', label: t('Solo icona') },
      { value: 'stacked', label: t('Con sfondo') },
      { value: 'framed', label: t('Cornice') },
    ]},
    { key: 'bg_color', label: t('Colore sfondo/cornice'), type: 'color',
      condition: { field: 'view', operator: '!=', value: 'default' } },
    { key: 'bg_shape', label: t('Forma'), type: 'select', options: [
      { value: 'circle', label: t('Cerchio') },
      { value: 'square', label: t('Quadrato') },
      { value: 'rounded', label: t('Arrotondato') },
    ]},
    { key: 'tile_padding', label: t('Padding (px)'), type: 'spacing', max: 60 },
    { key: 'rotation', label: t('Rotazione (°)'), type: 'range', min: -180, max: 180, step: 15 },

    { type: 'separator', label: t('Hover') },
    { key: 'hover_animation', label: t('Animazione hover'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'grow', label: t('Ingrandisci') },
      { value: 'shake', label: t('Vibra') },
      { value: 'bounce', label: t('Rimbalza') },
      { value: 'spin', label: t('Ruota') },
      { value: 'pulse', label: t('Pulsazione') },
    ]},

    ...shadowField,
    ...borderFields(),
  ],
};
