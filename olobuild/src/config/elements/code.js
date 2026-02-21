export default {
  type: 'code',
  name: 'Codice',
  icon: 'dashicons-editor-code',
  category: 'content',
  defaults: {
    code: 'console.log("Hello World");',
    language: 'javascript',
    show_line_numbers: false,
  },
  fields: [
    { key: 'code', label: 'Codice', type: 'textarea' },
    { key: 'language', label: 'Linguaggio', type: 'text' },
    { key: 'show_line_numbers', label: 'Mostra numeri di riga', type: 'toggle' },
  ],
};
