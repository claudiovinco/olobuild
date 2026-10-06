import { flexContainerFields, flexContainerDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Inner-columns — container puro: layout strutturale come unico "dato".
 *   fields[]      → layout (n. sotto-colonne) — definizione strutturale
 *   styleFields[] → gap, allineamento, stacking, flex
 *   AVANZATE      → meta tecnico
 */
export default {
  type: 'inner-columns',
  name: t('Colonne interne'),
  icon: 'dashicons-table-col-after',
  category: 'layout',
  defaults: {
    layout: '50-50',
    gap: '16',
    vertical_align: 'stretch',
    stack_mobile: true,
    stack_tablet: false,
    ...flexContainerDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { key: 'layout', label: t('Layout sotto-colonne'), type: 'select', options: [
      { value: '50-50', label: t('50 / 50') },
      { value: '33-33-33', label: t('33 / 33 / 33') },
      { value: '25-75', label: t('25 / 75') },
      { value: '75-25', label: t('75 / 25') },
      { value: '25-50-25', label: t('25 / 50 / 25') },
    ] },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { type: 'separator', label: t('Spaziatura') },
    { key: 'gap', label: t('Gap'), type: 'range', min: 0, max: 48, step: 4, unit: 'px',
      // Il Layout Flex viene dopo nello style in linea: i suoi due gap (o la chiave storica
      // flex_gap, da sola) sostituiscono questo su tutti e due gli assi, e qui non agirebbe.
      // Con uno solo dei due impostato l'altro asse usa ancora questo.
      show: (s) => {
        const fcg = parseInt(s.flex_column_gap || 0);
        const frg = parseInt(s.flex_row_gap || 0);
        const flg = parseInt(s.flex_gap || 0);
        if (!(fcg > 0) && !(frg > 0) && flg > 0) return false;
        return !(fcg > 0 && frg > 0);
      }
    },
    { key: 'vertical_align', label: t('Allineamento verticale'), type: 'select', options: [
      { value: 'stretch', label: t('Stretch') },
      { value: 'start', label: t('Alto') },
      { value: 'center', label: t('Centro') },
      { value: 'end', label: t('Basso') },
    ] },

    { type: 'separator', label: t('Responsive') },
    { key: 'stack_mobile', label: t('Impila su mobile'), type: 'toggle' },
    { key: 'stack_tablet', label: t('Impila su tablet'), type: 'toggle' },

    ...flexContainerFields,
  ],
};
