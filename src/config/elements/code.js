import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile Code — split CONTENUTO/STILE.
 *   fields[]      → codice, linguaggio, show_line_numbers, show_copy_button, wrap_lines
 *   styleFields[] → tema, font size, raggio, max height, shadow, border
 */
export default {
  type: 'code',
  name: t('Codice'),
  icon: 'dashicons-editor-code',
  category: 'text',
  defaults: {
    typography_preset: '',
    code: 'console.log("Hello World");',
    language: 'javascript',
    show_line_numbers: false,
    theme: 'github-dark',
    show_copy_button: true,
    font_size: '14',
    max_height: '',
    wrap_lines: false,
    border_radius: { tl: 8, tr: 8, br: 8, bl: 8 },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // Appena nato: un frammento breve e leggibile, commentato in italiano, con i numeri di riga,
  // l'etichetta del linguaggio e il pulsante per copiarlo; angoli col raggio medio della scala.
  partenza: {
    code: '// Prezzo finale con l\'IVA al 22%\nfunction prezzoConIva(imponibile) {\n  const iva = imponibile * 0.22;\n  return Math.round((imponibile + iva) * 100) / 100;\n}\n\nconsole.log(prezzoConIva(100)); // 122',
    show_line_numbers: true,
    border_radius: { tl: 10, tr: 10, br: 10, bl: 10 },
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
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Codice'),
      responsiveKeys: [],
      keys: {
        size: 'font_size',
      },
      sizeMin: 10, sizeMax: 24,
    },

    { type: 'separator', label: t('Forma') },
    { key: 'border_radius', label: t('Raggio'), type: 'border-radius' },
    { key: 'max_height', label: t('Altezza massima (px, vuoto = auto)'), type: 'number', min: 0 },

    ...shadowField,
    ...borderFields(),
  ],
};
