import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
export default {
  type: 'readingtime',
  label: 'Tempo di Lettura',
  icon: 'dashicons-clock',
  category: 'dynamic',
  defaults: {
    words_per_minute: 200,
    format: 'full', // full | short | minutes_only
    prefix: 'Tempo di lettura:',
    suffix: 'min',
    icon: 'clock',
    show_icon: true,
    text_color: '',
    icon_color: '',
    font_size: '',
    font_weight: '',
    text_align: 'left',
    box_shadow: '',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },
  fields: [
    { type: 'separator', label: 'Calcolo' },
    { key: 'words_per_minute', label: 'Parole al minuto', type: 'number', min: 50, max: 500 },
    { key: 'format', label: 'Formato', type: 'select', options: [
      { value: 'full', label: 'Completo (Tempo di lettura: 5 min)' },
      { value: 'short', label: 'Breve (5 min di lettura)' },
      { value: 'minutes_only', label: 'Solo minuti (5)' },
    ]},
    { key: 'prefix', label: 'Prefisso', type: 'text', condition: { format: ['full'] }},
    { key: 'suffix', label: 'Suffisso', type: 'text' },

    { type: 'separator', label: 'Icona' },
    { key: 'show_icon', label: 'Mostra icona', type: 'toggle' },
    { key: 'icon', label: 'Icona', type: 'icon', condition: { show_icon: [true] }},
    { key: 'icon_color', label: 'Colore icona', type: 'color' },

    { type: 'separator', label: 'Tipografia' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },
    { key: 'font_size', label: 'Dimensione font', type: 'text', placeholder: '16px' },
    { key: 'font_weight', label: 'Peso font', type: 'select', options: [
      { value: '', label: 'Default' },
      { value: '300', label: 'Light' }, { value: '400', label: 'Regular' },
      { value: '600', label: 'Semi Bold' }, { value: '700', label: 'Bold' },
    ]},
    { key: 'text_align', label: 'Allineamento', type: 'select', options: [
      { value: 'left', label: 'Sinistra' }, { value: 'center', label: 'Centro' }, { value: 'right', label: 'Destra' }
    ]},

    { type: 'separator', label: 'Aspetto' },
    ...shadowField,
    ...borderFields(),
  ],
};
