export default {
  type: 'fragment',
  name: 'Frammento',
  icon: 'dashicons-screenoptions',
  category: 'layout',
  defaults: {
    html_id: '',
    css_classes: '',
  },
  fields: [
    { key: 'html_id', label: 'ID HTML', type: 'text' },
    { key: 'css_classes', label: 'Classi CSS', type: 'text' },
  ],
};
