export default {
  type: 'section',
  name: 'Sezione',
  icon: 'dashicons-align-center',
  category: 'structure',
  defaults: {
    style: 'default',
    width: 'default',
    padding: 'default',
  },
  fields: [
    { key: 'style', label: 'Stile', type: 'select', options: [
      { value: 'default', label: 'Predefinito' },
      { value: 'muted', label: 'Attenuato' },
      { value: 'primary', label: 'Primario' },
      { value: 'secondary', label: 'Secondario' },
    ]},
    { key: 'width', label: 'Larghezza max', type: 'select', options: [
      { value: 'default', label: 'Predefinito' },
      { value: 'small', label: 'Piccolo' },
      { value: 'large', label: 'Grande' },
      { value: 'xlarge', label: 'Extra grande' },
      { value: 'expand', label: 'Larghezza piena' },
      { value: 'fullbleed', label: 'Bordo a bordo' },
    ]},
    { key: 'padding', label: 'Padding', type: 'select', options: [
      { value: 'default', label: 'Predefinito' },
      { value: 'small', label: 'Piccolo' },
      { value: 'large', label: 'Grande' },
      { value: 'xlarge', label: 'Extra grande' },
      { value: 'remove-vertical', label: 'Nessuno' },
    ]},
  ],
};
