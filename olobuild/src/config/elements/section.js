export default {
  type: 'section',
  name: 'Sezione',
  icon: 'dashicons-align-center',
  category: 'structure',
  defaults: {
    style: 'default',
    width: 'default',
    padding: 'default',
    sticky_effect: 'none',
    sticky_top: '',
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
    { key: 'sticky_effect', label: 'Effetto sticky', type: 'select', options: [
      { value: 'none', label: 'Nessuno' },
      { value: 'cover', label: 'Cover' },
      { value: 'reveal', label: 'Reveal' },
      { value: 'cover-h', label: 'Cover orizzontale' },
      { value: 'reveal-h', label: 'Reveal orizzontale' },
    ]},
    { key: 'sticky_top', label: 'Offset dall\'alto (px)', type: 'text', placeholder: '0', condition: { field: 'sticky_effect', operator: '!=', value: 'none' } },
  ],
};
