export default {
  type: 'row',
  name: 'Riga / Colonne',
  icon: 'dashicons-columns',
  category: 'layout',
  defaults: {
    layout: '50-50',
    gap: '16',
    column_gap: 'default',
    vertical_align: 'stretch',
    stack_mobile: true,
  },
  fields: [
    { key: 'layout', label: 'Layout', type: 'select', options: [
      { value: '100', label: '100' },
      { value: '50-50', label: '50 / 50' },
      { value: '33-33-33', label: '33 / 33 / 33' },
      { value: '25-50-25', label: '25 / 50 / 25' },
      { value: '25-25-25-25', label: '25 / 25 / 25 / 25' },
      { value: '66-33', label: '66 / 33' },
      { value: '33-66', label: '33 / 66' },
      { value: 'custom', label: 'Personalizzato (%)' },
    ]},
    { key: 'custom_widths', label: 'Larghezze personalizzate', type: 'text', placeholder: 'es: 20,30,50', condition: { field: 'layout', value: 'custom' } },
    { key: 'gap', label: 'Gap (px)', type: 'range', min: 0, max: 48, step: 4 },
    { key: 'vertical_align', label: 'Allineamento verticale', type: 'select', options: [
      { value: 'stretch', label: 'Stretch' },
      { value: 'start', label: 'Alto' },
      { value: 'center', label: 'Centro' },
      { value: 'end', label: 'Basso' },
    ]},
    { key: 'stack_mobile', label: 'Impila su mobile', type: 'toggle' },
  ],
};
