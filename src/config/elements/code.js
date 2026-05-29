import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Code — split CONTENUTO/STILE.
 *   fields[]      → codice, linguaggio, show_line_numbers, show_copy_button, wrap_lines
 *   styleFields[] → tema, font size, max height, shadow, border
 */
export default {
  type: 'code',
  name: t('Codice'),
  icon: 'dashicons-editor-code',
  category: 'text',
  defaults: {
    code: 'console.log("Hello World");',
    language: 'javascript',
    show_line_numbers: false,
    theme: 'github-dark',
    show_copy_button: true,
    font_size: '14',
    max_height: '',
    wrap_lines: false,
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'code', label: t('Codice'), type: 'textarea' },
    { key: 'language', label: t('Linguaggio'), type: 'text' },
    { key: 'show_line_numbers', label: t('Mostra numeri di riga'), type: 'toggle' },
    { key: 'show_copy_button', label: t('Pulsante copia'), type: 'toggle' },
    { key: 'wrap_lines', label: t('Avvolgi righe lunghe'), type: 'toggle' },
  ],

  styleFields: [
    { type: 'separator', label: t('Tema') },
    { key: 'theme', label: t('Tema'), type: 'select', options: [
      { value: 'github-dark', label: t('GitHub Dark') },
      { value: 'monokai', label: t('Monokai') },
      { value: 'dracula', label: t('Dracula') },
      { value: 'one-dark', label: t('One Dark') },
      { value: 'solarized-dark', label: t('Solarized Dark') },
      { value: 'light', label: t('Chiaro') },
    ]},

    { type: 'separator', label: t('Tipografia') },
    { type: 'typography', label: t('Codice'),
      responsiveKeys: ['size'],
      keys: {
        size: 'font_size',
      },
      sizeMin: 10, sizeMax: 24,
    },

    { type: 'separator', label: t('Dimensioni') },
    { key: 'max_height', label: t('Altezza massima (px, vuoto = auto)'), type: 'text' },

    ...shadowField,
    ...borderFields(),
  ],
};
