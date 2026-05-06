import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';

export default {
  type: 'progress',
  name: 'Barra progresso',
  icon: 'dashicons-chart-bar',
  category: 'marketing',
  defaults: {
    bars: 'HTML|90\nJavaScript|80\nVue.js|75',
    bar_color: '',
    bar_bg: '',
    text_color: '',
    height: '20',
    show_percentage: true,
    animated: true,
    layout: 'bar',
    circle_size: '120',
    circle_width: '8',
    inner_text: '',
    animate_counter: true,
    animation_duration: '1500',
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },
  fields: [
    { type: 'separator', label: 'Layout' },
    { key: 'layout', label: 'Layout', type: 'select', options: [
      { value: 'bar', label: 'Barra' },
      { value: 'circle', label: 'Cerchio' },
    ]},

    { type: 'separator', label: 'Contenuto' },
    { key: 'bars', label: 'Barre (etichetta|valore per riga)', type: 'textarea' },
    { key: 'show_percentage', label: 'Mostra percentuale', type: 'toggle' },
    { key: 'inner_text', label: 'Testo interno (vuoto = percentuale)', type: 'text' },

    { type: 'separator', label: 'Colori' },
    { key: 'bar_color', label: 'Colore barra', type: 'color' },
    { key: 'bar_bg', label: 'Sfondo barra', type: 'color' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },

    { type: 'separator', label: 'Dimensioni' },
    { key: 'height', label: 'Altezza (px)', type: 'range', min: 10, max: 600, step: 5 },
    { key: 'circle_size', label: 'Dimensione cerchio (px)', type: 'range', min: 60, max: 200, step: 10,
      condition: { field: 'layout', operator: '==', value: 'circle' } },
    { key: 'circle_width', label: 'Spessore cerchio (px)', type: 'range', min: 2, max: 20, step: 1,
      condition: { field: 'layout', operator: '==', value: 'circle' } },

    { type: 'separator', label: 'Animazione' },
    { key: 'animated', label: 'Animata', type: 'toggle' },
    { key: 'animate_counter', label: 'Anima contatore', type: 'toggle' },
    { key: 'animation_duration', label: 'Durata animazione (ms)', type: 'range', min: 500, max: 3000, step: 100 },

    { type: 'separator', label: 'Aspetto' },
    ...shadowField,
    ...borderFields(),
  ],
};
