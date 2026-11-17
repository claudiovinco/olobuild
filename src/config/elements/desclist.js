import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, textEffectsFields, textEffectsDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile DescList — split CONTENUTO/STILE.
 *   fields[]      → items (term+definition+link+icon), layout (stacked/inline/grid), show_icon
 *   styleFields[] → preset, bg, typo, text-effects, icona aspetto, tipografia, allineamento, colori, separator/striped, shadow, border
 */
export default {
  type: 'desclist',
  name: t('Lista descrittiva'),
  icon: 'dashicons-editor-justify',
  category: 'text',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    items: [
      { id: 'dl-1', term: 'Framework', definition: 'Vue.js 3 con Composition API', icon: 'code' },
      { id: 'dl-2', term: 'Linguaggio', definition: 'PHP 7.4+ con WordPress', icon: 'server' },
      { id: 'dl-3', term: 'Build Tool', definition: 'Vite 5', icon: 'bolt' },
    ],
    layout: 'stacked',
    show_icon: true,
    icon_color: '',
    icon_size: '20',
    term_color: '',
    term_font_size: '15',
    term_font_weight: '600',
    definition_color: '',
    definition_font_size: '14',
    text_align: 'left',
    separator: true,
    border_color: '',
    spacing: '16',
    striped: false,
    striped_color: '',
    shadow: 'none',
    ...textEffectsDefaults,
    text_effect_target: 'definition',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // Appena nata: la scheda di un servizio — voce e dettaglio in coppia, con un'icona per voce
  // e il filo fra le righe (al posto delle voci tecniche di prima: framework, linguaggio…).
  partenza: {
    items: [
      { id: 'dl-1', term: t('Durata'), definition: t('60 minuti, su appuntamento'), icon: 'clock' },
      { id: 'dl-2', term: t('Dove'), definition: t('In studio oppure online, in videochiamata'), icon: 'location' },
      { id: 'dl-3', term: t('Prezzo'), definition: t('70 € a incontro, 320 € il pacchetto da cinque'), icon: 'tag' },
      { id: 'dl-4', term: t('Disdetta'), definition: t('Gratuita fino a 24 ore prima'), icon: 'calendar' },
    ],
  },

  fields: [
    { key: 'items', label: t('Voci'), type: 'content-items', supportsDynamic: true, etichettaDa: 'term',
      itemFields: [
        { key: 'term', label: t('Termine'), type: 'text' },
        { key: 'definition', label: t('Definizione'), type: 'textarea' },
        { key: 'link', label: t('Link'), type: 'link', placeholder: t('https://...') },
        { key: 'icon', label: t('Icona'), type: 'icon' },
      ],
      newItemDefaults: { term: 'Nuovo termine', definition: 'Definizione.', icon: '' },
      itemLabel: 'Voce',
    },

    { key: 'show_icon', label: t('Mostra le icone'), type: 'toggle' },
  ],

  // Zone standard (16 nov 2026): Aspetto · Testo · Effetti testo · Icone · Bordo · Disposizione. Prima «Preset stilistico»,
  // «Icona — Aspetto», «Tipografia», «Colori», e filo e righe alterne nel Contenuto.
  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'clean', label: t('Pulita') },
      { value: 'essential', label: t('Essenziale') },
      { value: 'spec', label: t('Scheda tecnica') },
      { value: 'striped', label: t('A righe alterne') },
      { value: 'grid', label: t('In griglia') },
      { value: 'bold', label: t('Termini forti') },
      { value: 'custom', label: t('Personalizzato') },
    ]},
    ...shadowField,

    { type: 'separator', label: t('Testo') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Termine'),
      responsiveKeys: [],
      keys: {
        size:   'term_font_size',
        weight: 'term_font_weight',
        color:  'term_color',
      },
      sizeMin: 12, sizeMax: 24, sizeStep: 1,
    },
    { type: 'typography', label: t('Definizione'),
      responsiveKeys: [],
      keys: {
        size:  'definition_font_size',
        color: 'definition_color',
      },
      sizeMin: 12, sizeMax: 20, sizeStep: 1,
    },
    ...textEffectsFields([
      { value: 'term', label: t('Solo termine') },
      { value: 'definition', label: t('Solo definizione') },
      { value: 'both', label: t('Termine e definizione') },
    ]),

    { type: 'separator', label: t('Icone'), show: (s) => s.show_icon !== false },
    { key: 'icon_color', label: t('Colore icone'), type: 'color' },
    { key: 'icon_size', label: t('Dimensione icone'), type: 'range', unit: 'px', min: 14, max: 32, step: 2 },

    ...borderFields(),

    { type: 'separator', label: t('Disposizione') },
    { key: 'layout', label: t('Disposizione'), type: 'select', options: [
      { value: 'stacked', label: t('Termine sopra, definizione sotto') },
      { value: 'inline', label: t('Termine e definizione affiancati') },
      { value: 'grid', label: t('In griglia') },
    ]},
    { key: 'text_align', label: t('Allineamento'), type: 'select', options: [
      { value: 'left', label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
      { value: 'right', label: t('Destra') },
      { value: 'justify', label: t('Giustificato') },
    ]},
    { key: 'spacing', label: t('Gap voci'), type: 'range', unit: 'px', min: 4, max: 32, step: 2 },
    { key: 'separator', label: t('Filo fra le voci'), type: 'toggle' },
    { key: 'border_color', label: t('Colore filo'), type: 'color',
      condition: { field: 'separator', operator: '==', value: true } },
    { key: 'striped', label: t('Righe alterne'), type: 'toggle' },
    { key: 'striped_color', label: t('Colore righe alterne'), type: 'color',
      condition: { field: 'striped', operator: '==', value: true } },
  ],
};
