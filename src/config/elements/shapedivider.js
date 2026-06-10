
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile ShapeDivider — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → forma, posizione (top/bottom), flip orizzontale/verticale
 *   styleFields[] → colore, altezza, larghezza, z-index, altezze responsive, bordo
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'shapedivider',
  name: t('Shape Divider'),
  icon: 'dashicons-editor-contract',
  category: 'layout',
  defaults: {
    shape: 'wave',
    position: 'bottom',
    flip_horizontal: false,
    flip_vertical: false,
    width: '100',
    height: '80',
    color: '',
    z_index: '1',
    responsive_height_tablet: '',
    responsive_height_mobile: '',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'shape', label: t('Forma'), type: 'select', options: [
      { value: 'wave', label: t('Onda') },
      { value: 'wave2', label: t('Onda doppia') },
      { value: 'triangle', label: t('Triangolo') },
      { value: 'tilt', label: t('Inclinato') },
      { value: 'arrow', label: t('Freccia') },
      { value: 'zigzag', label: t('Zigzag') },
      { value: 'mountains', label: t('Montagne') },
      { value: 'clouds', label: t('Nuvole') },
      { value: 'drops', label: t('Gocce') },
      { value: 'curve', label: t('Curva') },
    ]},
    { key: 'position', label: t('Posizione'), type: 'select', options: [
      { value: 'top', label: t('Alto') },
      { value: 'bottom', label: t('Basso') },
    ]},
    { key: 'flip_horizontal', label: t('Specchia orizzontale'), type: 'toggle' },
    { key: 'flip_vertical', label: t('Specchia verticale'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { key: 'color', label: t('Colore'), type: 'color' },
    { key: 'height', label: t('Altezza (px)'), type: 'range', min: 10, max: 500, step: 5 },
    { key: 'width', label: t('Larghezza (%)'), type: 'range', min: 100, max: 300, step: 5 },
    { key: 'z_index', label: t('Z-Index'), type: 'range', min: 0, max: 99, step: 1 },

    { type: 'separator', label: t('Responsive') },
    { key: 'responsive_height_tablet', label: t('Altezza tablet (px)'), type: 'number', min: 10, max: 500 },
    { key: 'responsive_height_mobile', label: t('Altezza mobile (px)'), type: 'number', min: 10, max: 500 },
    ...borderFields(),
  ],
};
