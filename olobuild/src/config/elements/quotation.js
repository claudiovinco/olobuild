import { borderFields, borderDefaults, shadowField } from './_shared.js';

export default {
  type: 'quotation',
  name: 'Citazione',
  icon: 'dashicons-format-quote',
  category: 'content',
  defaults: {
    content: 'La vita è quello che ti succede mentre sei impegnato a fare altri progetti.',
    author: 'John Lennon',
    style: 'default',
    alignment: 'left',
    shadow: 'none',
    ...borderDefaults,
  },
  fields: [
    { key: 'content', label: 'Citazione', type: 'editor', mode: 'block' },
    { key: 'author', label: 'Autore', type: 'text' },
    { key: 'style', label: 'Stile', type: 'select', options: [
      { value: 'default', label: 'Predefinito' },
      { value: 'footer', label: 'Citazione a piè di pagina' },
    ]},
    { key: 'alignment', label: 'Allineamento', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'center', label: 'Centro' },
      { value: 'right', label: 'Destra' },
    ]},
    shadowField,
    ...borderFields,
  ],
};
