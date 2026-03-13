export default {
  type: 'text-block',
  name: 'Testo',
  icon: 'dashicons-editor-paragraph',
  category: 'essential',
  defaults: {
    content: '<p>Scrivi qui il tuo testo. Puoi formattare con <strong>grassetto</strong>, <em>corsivo</em>, elenchi, titoli e molto altro.</p>',
    text_color: '',
    font_size: '',
    line_height: '',
    max_width: '',
    padding: '16',
  },
  fields: [
    { key: 'content', label: 'Contenuto', type: 'editor', mode: 'block' },

    { type: 'separator', label: 'Tipografia' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },
    { key: 'font_size', label: 'Dimensione (px)', type: 'range', min: 0, max: 48, step: 1,
      description: '0 = predefinito' },
    { key: 'line_height', label: 'Interlinea', type: 'select', options: [
      { value: '', label: 'Predefinita' },
      { value: '1.2', label: 'Stretta (1.2)' },
      { value: '1.4', label: 'Compatta (1.4)' },
      { value: '1.6', label: 'Normale (1.6)' },
      { value: '1.8', label: 'Ampia (1.8)' },
      { value: '2.0', label: 'Doppia (2.0)' },
    ]},

    { type: 'separator', label: 'Layout' },
    { key: 'max_width', label: 'Larghezza max (px)', type: 'range', min: 0, max: 1200, step: 10,
      description: '0 = nessun limite' },
    { key: 'padding', label: 'Padding (px)', type: 'range', min: 0, max: 48, step: 4 },
  ],
};
