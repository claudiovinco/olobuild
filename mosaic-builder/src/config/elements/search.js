export default {
  type: 'search',
  name: 'Ricerca',
  icon: 'dashicons-search',
  category: 'content',
  defaults: {
    placeholder: 'Cerca...',
    style: 'default',
    size: '',
    show_icon: true,
  },
  fields: [
    { key: 'placeholder', label: 'Placeholder', type: 'text' },
    { key: 'style', label: 'Stile', type: 'select', options: [
      { value: 'default', label: 'Predefinito' },
      { value: 'large', label: 'Grande' },
      { value: 'navbar', label: 'Navbar' },
    ]},
    { key: 'size', label: 'Dimensione', type: 'select', options: [
      { value: '', label: 'Normale' },
      { value: 'large', label: 'Grande' },
    ]},
    { key: 'show_icon', label: 'Mostra icona', type: 'toggle' },
  ],
};
