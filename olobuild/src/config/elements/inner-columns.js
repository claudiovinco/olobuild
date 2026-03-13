import { flexContainerFields, flexContainerDefaults } from './_shared.js';

export default {
  type: 'inner-columns',
  name: 'Colonne interne',
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
  fields: [
    { key: 'layout', label: 'Layout', type: 'select', options: [
      { value: '50-50', label: '50 / 50' },
      { value: '33-33-33', label: '33 / 33 / 33' },
      { value: '25-75', label: '25 / 75' },
      { value: '75-25', label: '75 / 25' },
      { value: '25-50-25', label: '25 / 50 / 25' },
    ] },
    { key: 'gap', label: 'Spaziatura (px)', type: 'range', min: 0, max: 48, step: 4 },
    { key: 'vertical_align', label: 'Allineamento verticale', type: 'select', options: [
      { value: 'stretch', label: 'Stretch' },
      { value: 'start', label: 'Alto' },
      { value: 'center', label: 'Centro' },
      { value: 'end', label: 'Basso' },
    ] },
    { key: 'stack_mobile', label: 'Impila su mobile', type: 'toggle' },
    { key: 'stack_tablet', label: 'Impila su tablet', type: 'toggle' },
    ...flexContainerFields,
  ],
};
