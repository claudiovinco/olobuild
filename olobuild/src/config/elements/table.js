import { borderFields, borderDefaults, shadowField } from './_shared.js';

export default {
  type: 'table',
  name: 'Tabella',
  icon: 'dashicons-editor-table',
  category: 'content',
  defaults: {
    table_data: 'Funzionalità|Base|Pro\nSpazio|5 GB|50 GB\nUtenti|1|10',
    striped: true,
    bordered: true,
    hover_effect: true,
    header_bg: '#1F2937',
    header_text_color: '#F3F4F6',
    text_color: '#D1D5DB',
    shadow: 'none',
    ...borderDefaults,
  },
  fields: [
    { key: 'table_data', label: 'Dati tabella (col|col per riga)', type: 'textarea' },
    { key: 'striped', label: 'Righe alternate', type: 'toggle' },
    { key: 'bordered', label: 'Con bordi', type: 'toggle' },
    { key: 'hover_effect', label: 'Effetto hover', type: 'toggle' },
    { key: 'header_bg', label: 'Sfondo intestazione', type: 'color' },
    { key: 'header_text_color', label: 'Colore testo intestazione', type: 'color' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },
    shadowField,
    ...borderFields,
  ],
};
