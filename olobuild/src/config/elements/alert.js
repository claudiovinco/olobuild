import { borderFields, borderDefaults, shadowField } from './_shared.js';

export default {
  type: 'alert',
  name: 'Avviso',
  icon: 'dashicons-warning',
  category: 'content',
  defaults: {
    alert_type: 'info',
    title: 'Attenzione!',
    message: 'Questo è un avviso informativo.',
    show_icon: true,
    shadow: 'none',
    ...borderDefaults,
  },
  fields: [
    { key: 'alert_type', label: 'Tipo', type: 'select', options: [
      { value: 'info', label: 'Info' },
      { value: 'success', label: 'Successo' },
      { value: 'warning', label: 'Attenzione' },
      { value: 'error', label: 'Errore' },
    ]},
    { key: 'title', label: 'Titolo', type: 'editor', mode: 'inline' },
    { key: 'message', label: 'Messaggio', type: 'editor', mode: 'block' },
    { key: 'show_icon', label: 'Mostra icona', type: 'toggle' },
    shadowField,
    ...borderFields,
  ],
};
