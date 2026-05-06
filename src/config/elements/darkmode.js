export default {
  type: 'darkmode',
  name: 'Dark Mode Toggle',
  icon: 'dashicons-admin-appearance',
  category: 'interactive',
  defaults: {
    style: 'toggle',
    light_icon: 'sun',
    dark_icon: 'moon',
    icon_size: 24,
    button_text_light: 'Modalità scura',
    button_text_dark: 'Modalità chiara',
    toggle_color: '',
    toggle_active_color: '',
    save_preference: true,
    respect_system: true,
    transition_duration: 300,
  },
  fields: [
    { type: 'separator', label: 'Stile' },
    { key: 'style', label: 'Stile', type: 'select', options: [
      { value: 'toggle', label: 'Toggle switch' },
      { value: 'icon', label: 'Icona singola' },
      { value: 'button', label: 'Pulsante con testo' },
    ]},

    { type: 'separator', label: 'Icone' },
    { key: 'light_icon', label: 'Icona luce', type: 'icon' },
    { key: 'dark_icon', label: 'Icona scuro', type: 'icon' },
    { key: 'icon_size', label: 'Dimensione icona (px)', type: 'range', min: 16, max: 48, step: 2 },

    { type: 'separator', label: 'Testo (solo button)' },
    { key: 'button_text_light', label: 'Testo (modalità chiara)', type: 'text',
      condition: { field: 'style', operator: '==', value: 'button' } },
    { key: 'button_text_dark', label: 'Testo (modalità scura)', type: 'text',
      condition: { field: 'style', operator: '==', value: 'button' } },

    { type: 'separator', label: 'Colori' },
    { key: 'toggle_color', label: 'Colore toggle', type: 'color' },
    { key: 'toggle_active_color', label: 'Colore toggle attivo', type: 'color' },

    { type: 'separator', label: 'Comportamento' },
    { key: 'save_preference', label: 'Salva preferenza', type: 'toggle' },
    { key: 'respect_system', label: 'Rispetta tema di sistema', type: 'toggle' },
    { key: 'transition_duration', label: 'Durata transizione (ms)', type: 'range', min: 0, max: 1000, step: 50 },
  ],
};
