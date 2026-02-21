export default {
  type: 'breadcrumbs',
  name: 'Breadcrumbs',
  icon: 'dashicons-arrow-right-alt2',
  category: 'content',
  defaults: {
    separator: '/',
    home_label: 'Home',
    show_home: true,
    show_current: true,
  },
  fields: [
    { key: 'separator', label: 'Separatore', type: 'text' },
    { key: 'home_label', label: 'Etichetta Home', type: 'text' },
    { key: 'show_home', label: 'Mostra Home', type: 'toggle' },
    { key: 'show_current', label: 'Mostra pagina corrente', type: 'toggle' },
  ],
};
