import { textEffectsFields, textEffectsDefaults } from './_shared';
import { shadowField } from './_shared.js';

export default {
  type: 'quotation',
  name: 'Citazione',
  icon: 'dashicons-format-quote',
  category: 'text',
  defaults: {
    content: 'La vita è quello che ti succede mentre sei impegnato a fare altri progetti.',
    author: 'John Lennon',
    style: 'default',
    alignment: 'left',
    shadow: 'none',
    ...textEffectsDefaults,
  },
  fields: [
    { key: 'content', label: 'Citazione', type: 'textarea' },
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
    ...shadowField,
    ...textEffectsFields([
      { value: 'content', label: 'Solo Contenuto' },
      { value: 'author', label: 'Solo Autore' },
      { value: 'all', label: 'Tutti gli elementi testuali' },
    ]),
  ],
};
