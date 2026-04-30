import { textEffectsFields, textEffectsDefaults } from './_shared';
import { shadowField } from './_shared.js';

export default {
  type: 'starrating',
  name: 'Valutazione',
  icon: 'dashicons-star-filled',
  category: 'marketing',
  defaults: {
    rating: '4',
    max_stars: '5',
    star_size: '32',
    star_color: '',
    empty_color: '',
    style: 'filled',
    title: '',
    subtitle: '',
    title_color: '',
    subtitle_color: '',
    alignment: 'center',
    shadow: 'none',
    ...textEffectsDefaults,
  },
  fields: [
    { key: 'rating', label: 'Valutazione', type: 'range', min: 0, max: 5, step: 0.5 },
    { key: 'max_stars', label: 'Stelle massime', type: 'range', min: 1, max: 10, step: 1 },
    { key: 'star_size', label: 'Dimensione stelle (px)', type: 'range', min: 16, max: 64, step: 4 },
    { key: 'star_color', label: 'Colore stelle piene', type: 'color' },
    { key: 'empty_color', label: 'Colore stelle vuote', type: 'color' },
    { key: 'style', label: 'Stile', type: 'select', options: [
      { value: 'filled', label: 'Pieno' },
      { value: 'outline', label: 'Contorno' },
      { value: 'rounded', label: 'Arrotondato' },
    ]},
    { key: 'title', label: 'Titolo', type: 'text' },
    { key: 'subtitle', label: 'Sottotitolo', type: 'text' },
    { key: 'title_color', label: 'Colore titolo', type: 'color' },
    { key: 'subtitle_color', label: 'Colore sottotitolo', type: 'color' },
    { key: 'alignment', label: 'Allineamento', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'center', label: 'Centro' },
      { value: 'right', label: 'Destra' },
    ]},
    ...shadowField,
    ...textEffectsFields([
      { value: 'title', label: 'Solo Titolo' },
      { value: 'subtitle', label: 'Solo Sottotitolo' },
      { value: 'all', label: 'Tutti gli elementi testuali' },
    ]),
  ],
};
