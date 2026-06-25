import { t } from '@/i18n';
import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';

/**
 * Quote Slider — North : slider quote stile "Cohere North".
 * A sinistra logo cliente + quote display + autore + frecce/dots; a destra una card
 * scura la cui forma MORFA da rettangolo verticale a parallelogramma a ogni cambio
 * slide, con linee punteggiate "topografiche" animate. Render Vue == PHP.
 * Estratta dal blueprint cohere.com/north (§ "Why enterprises and innovators choose Cohere").
 */
export default {
  type: 'northquoteslider',
  name: t('Quote Slider — North'),
  icon: 'dashicons-format-quote',
  category: 'marketing',

  defaults: {
    heading: 'Why enterprises and innovators choose Cohere',
    items: [
      {
        quote: "We jointly announced a customized platform, North for Banking, to enable RBC to accelerate the development of our genAI solutions securely and efficiently and we're pleased with our results to date.",
        author_name: 'Dr. Foteini Agrafioti',
        author_role: 'SVP, Data & AI & Chief Science Officer, RBC',
        logo_text: 'RBC',
      },
      {
        quote: 'North lets our teams move from question to verified answer in seconds — grounded in our own data, without the risk.',
        author_name: 'Head of Data',
        author_role: 'Global Enterprise',
        logo_text: '',
      },
    ],

    slant: true,
    autoplay: false,
    autoplay_speed: 6,

    bg_color: 'var(--olo-color-light, #f8f9fa)',
    heading_color: 'var(--olo-color-text, #1f2937)',
    quote_color: 'var(--olo-color-text, #1f2937)',
    author_color: 'var(--olo-color-text, #1f2937)',
    role_color: 'var(--olo-color-text-soft, #6b7280)',
    logo_color: 'var(--olo-color-text, #1f2937)',
    arrow_color: 'var(--olo-color-text, #1f2937)',
    graphic_color: 'var(--olo-color-text, #1f2937)',
    graphic_line_color: 'var(--olo-color-accent, #f4a23b)',
    quote_size: 26,

    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'heading', label: t('Titolo sezione'), type: 'text' },
    {
      key: 'items', label: t('Quote'), type: 'content-items', addLabel: t('Aggiungi quote'),
      titleKey: 'author_name',
      newItemDefaults: { quote: 'Nuova citazione…', author_name: 'Nome', author_role: 'Ruolo', logo_text: '' },
      itemFields: [
        { key: 'quote', label: t('Citazione'), type: 'textarea' },
        { key: 'author_name', label: t('Autore'), type: 'text' },
        { key: 'author_role', label: t('Ruolo'), type: 'text' },
        { key: 'logo_text', label: t('Logo / cliente (testo)'), type: 'text' },
      ],
    },

    { type: 'separator', label: t('Animazione') },
    { key: 'slant', label: t('Morph rettangolo → parallelogramma'), type: 'toggle' },
    { key: 'autoplay', label: t('Autoplay'), type: 'toggle' },
    { key: 'autoplay_speed', label: t('Velocità autoplay (s)'), type: 'number', min: 2, max: 20,
      condition: { field: 'autoplay', op: 'eq', value: true } },
  ],

  styleFields: [
    { type: 'separator', label: t('Tipografia') },
    { key: 'quote_size', label: t('Dimensione quote'), type: 'unit', units: ['px'], min: 16, max: 48 },

    { type: 'separator', label: t('Colori — testo') },
    { key: 'heading_color', label: t('Titolo'), type: 'color' },
    { key: 'quote_color', label: t('Quote'), type: 'color' },
    { key: 'author_color', label: t('Autore'), type: 'color' },
    { key: 'role_color', label: t('Ruolo'), type: 'color' },
    { key: 'logo_color', label: t('Logo'), type: 'color' },
    { key: 'arrow_color', label: t('Frecce / controlli'), type: 'color' },

    { type: 'separator', label: t('Grafica (card destra)') },
    { key: 'graphic_color', label: t('Sfondo grafica'), type: 'color' },
    { key: 'graphic_line_color', label: t('Linee punteggiate'), type: 'color' },

    { type: 'separator', label: t('Sfondo sezione') },
    { key: 'bg_color', label: t('Sfondo sezione'), type: 'color' },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },

    { type: 'separator', label: t('Ombra') },
    ...shadowField,

    ...borderFields(),
  ],
};
