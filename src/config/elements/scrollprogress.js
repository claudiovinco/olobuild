import { t } from '@/i18n';

/**
 * Tile ScrollProgress — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → posizione (top/bottom), toggle mostra percentuale
 *   styleFields[] → colori barra/sfondo/percentuale, altezza barra, z-index
 *   AVANZATE      → meta tecnico (id/class/condizioni)
 */
export default {
  type: 'scrollprogress',
  name: t('Barra Scroll'),
  icon: 'dashicons-ellipsis',
  category: 'interactive',
  defaults: {
    position: 'top',
    bar_color: '',
    bar_bg: '',
    bar_height: '4',
    show_percentage: false,
    percentage_color: '',
    z_index: '9999',
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'position', label: t('Posizione'), type: 'select', options: [
      { value: 'top',    label: t('In alto') },
      { value: 'bottom', label: t('In basso') },
    ]},
    { key: 'show_percentage', label: t('Mostra percentuale'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { key: 'bar_color', label: t('Colore barra'), type: 'color' },
    { key: 'bar_bg', label: t('Colore sfondo'), type: 'color' },
    { key: 'bar_height', label: t('Altezza barra (px)'), type: 'range', min: 2, max: 12, step: 1 },
    { key: 'percentage_color', label: t('Colore percentuale'), type: 'color',
      condition: { field: 'show_percentage', operator: '==', value: true } },
    { key: 'z_index', label: t('Z-index'), type: 'range', min: 100, max: 10000, step: 100 },
  ],
};
