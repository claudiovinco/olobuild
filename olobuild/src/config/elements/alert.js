import { shadowField } from './_shared.js';

export default {
  type: 'alert',
  name: 'Avviso',
  icon: 'dashicons-warning',
  category: 'text',
  defaults: {
    alert_type: 'info',
    title: 'Attenzione!',
    message: 'Questo è un avviso informativo.',
    show_icon: true,
    custom_icon: '',
    dismissible: false,
    custom_bg_color: '',
    custom_border_color: '',
    custom_text_color: '',
    shadow: 'none',
  },
  fields: [
    { key: 'alert_type', label: 'Tipo', type: 'select', options: [
      { value: 'info', label: 'Info' },
      { value: 'success', label: 'Successo' },
      { value: 'warning', label: 'Attenzione' },
      { value: 'error', label: 'Errore' },
    ]},
    { key: 'title', label: 'Titolo', type: 'text' },
    { key: 'message', label: 'Messaggio', type: 'textarea' },
    { key: 'show_icon', label: 'Mostra icona', type: 'toggle' },
    { key: 'custom_icon', label: 'Icona personalizzata', type: 'icon' },
    { key: 'dismissible', label: 'Chiudibile', type: 'toggle' },
    { type: 'separator', label: 'Colori personalizzati (sovrascrivono tipo)' },
    { key: 'custom_bg_color', label: 'Sfondo personalizzato', type: 'color' },
    { key: 'custom_border_color', label: 'Bordo personalizzato', type: 'color' },
    { key: 'custom_text_color', label: 'Testo personalizzato', type: 'color' },
    ...shadowField,
  ],
};
