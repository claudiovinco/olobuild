export default {
  type: 'content',
  name: 'Contenuto',
  icon: 'dashicons-text-page',
  category: 'content',
  defaults: {
    heading: 'Titolo sezione',
    text: 'Aggiungi il tuo contenuto qui.',
    image: '',
  },
  fields: [
    { key: 'heading', label: 'Titolo', type: 'editor', mode: 'inline' },
    { key: 'text', label: 'Testo', type: 'editor', mode: 'block' },
    { key: 'image', label: 'Immagine', type: 'image' },
  ],
};
