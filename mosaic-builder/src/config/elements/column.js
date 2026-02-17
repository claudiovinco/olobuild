import { columnWidthOptions } from './_shared';

export default {
  type: 'column',
  name: 'Colonna',
  icon: 'dashicons-editor-insertmore',
  category: 'structure',
  defaults: {
    width_default: '',
    width_small: '',
    width_medium: '',
    width_large: '',
  },
  fields: [
    { key: 'width_default', label: 'Larghezza telefono', type: 'select', options: columnWidthOptions },
    { key: 'width_small', label: 'Larghezza tablet', type: 'select', options: columnWidthOptions },
    { key: 'width_medium', label: 'Larghezza desktop', type: 'select', options: columnWidthOptions },
    { key: 'width_large', label: 'Larghezza schermo grande', type: 'select', options: columnWidthOptions },
  ],
};
