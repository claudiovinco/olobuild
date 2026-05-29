import { columnWidthOptions, flexContainerFields } from './_shared';
import { t } from '@/i18n';

/**
 * Tile Column — container puro: nessun contenuto editabile.
 *   fields[]      → comportamento sticky (sticky è funzionale, non puro aspetto)
 *   styleFields[] → sfondo, larghezza responsive, flex
 *   AVANZATE      → meta tecnico
 */
export default {
  type: 'column',
  name: t('Colonna'),
  icon: 'dashicons-editor-insertmore',
  category: 'structure',
  defaults: {
    bg: { type: 'none' },
    width_default: '',
    width_small: '',
    width_medium: '',
    width_large: '',
    flex_direction: '',
    flex_justify: '',
    flex_align: '',
    flex_wrap: '',
    flex_column_gap: '',
    flex_row_gap: '',
    sticky: false,
    sticky_offset: 50,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'description', description: t('La Colonna è un contenitore. Trascina al suo interno le tile, oppure passa al tab Stile per configurare sfondo, larghezza e allineamento.') },

    { type: 'separator', label: t('Scroll fisso (sticky)') },
    { key: '_sticky_hint', type: 'description', label: '',
      description: t('Mantiene questa colonna ferma mentre le altre colonne della stessa riga scorrono. Per layout immagine + testo: attiva sulla colonna che contiene l\'immagine.') },
    { key: 'sticky', label: t('Attiva scroll fisso'), type: 'toggle' },
    { key: 'sticky_offset', label: t('Distanza dal bordo superiore (px)'), type: 'range', min: 0, max: 300, step: 5,
      condition: { field: 'sticky', value: true } },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Larghezza responsive') },
    { key: 'width_default', label: t('Larghezza telefono'), type: 'select', options: columnWidthOptions },
    { key: 'width_small', label: t('Larghezza tablet'), type: 'select', options: columnWidthOptions },
    { key: 'width_medium', label: t('Larghezza desktop'), type: 'select', options: columnWidthOptions },
    { key: 'width_large', label: t('Larghezza schermo grande'), type: 'select', options: columnWidthOptions },

    ...flexContainerFields,
  ],
};
