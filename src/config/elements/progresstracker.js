import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared';
import { shadowField } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile ProgressTracker — split CONTENUTO/STILE.
 *   fields[]      → items (title+description+icon+status), layout, show_numbers, show_description
 *   styleFields[] → preset, bg, typo, text-effects, circle size, font size, gap, connector stile/colore, colori (completato/attivo/pending/testo), shadow, border
 */
export default {
  type: 'progresstracker',
  name: t('Progress tracker'),
  icon: 'dashicons-editor-ol',
  category: 'content',
  defaults: {
    bg: { type: 'none' },
    typography_preset: '',
    preset: 'custom',
    items: [
      { id: 'pt-1', title: t('Ordine ricevuto'), description: t('Il tuo ordine è stato confermato.'), icon: 'check', status: 'completed' },
      { id: 'pt-2', title: t('In preparazione'), description: t('Stiamo preparando il tuo ordine.'), icon: 'settings', status: 'active' },
      { id: 'pt-3', title: t('Spedito'), description: t('Il pacco è in viaggio.'), icon: 'cart', status: 'pending' },
      { id: 'pt-4', title: t('Consegnato'), description: t('Consegna completata.'), icon: 'home', status: 'pending' },
    ],
    layout: 'horizontal',
    connector_style: 'line',
    connector_color: '',
    completed_color: '',
    active_color: '',
    pending_color: '',
    text_color: '',
    show_description: true,
    show_numbers: true,
    circle_size: '40',
    font_size: '14',
    gap: '0',
    shadow: 'none',
    ...textEffectsDefaults,
    text_effect_target: 'title',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { key: 'items', label: t('Passaggi'), type: 'content-items',
      itemFields: [
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'description', label: t('Descrizione'), type: 'text' },
        { key: 'icon', label: t('Icona (UIkit)'), type: 'icon' },
        { key: 'status', label: t('Stato'), type: 'select', options: [
          { value: 'completed', label: t('Completato') },
          { value: 'active', label: t('Attivo') },
          { value: 'pending', label: t('In attesa') },
        ]},
      ],
      newItemDefaults: { title: t('Nuovo passaggio'), description: t('Descrizione del passaggio.'), icon: 'check', status: 'pending' },
      itemLabel: 'Passaggio',
    },

    { type: 'separator', label: t('Layout') },
    { key: 'layout', label: t('Layout'), type: 'select', options: [
      { value: 'horizontal', label: t('Orizzontale') },
      { value: 'vertical', label: t('Verticale') },
    ]},
    { key: 'show_numbers', label: t('Mostra numeri'), type: 'toggle' },
    { key: 'show_description', label: t('Mostra descrizione'), type: 'toggle' },
  ],

  styleFields: [
    { type: 'separator', label: t('Preset stilistico') },
    { key: 'preset', label: t('Stile'), type: 'select', options: [
      { value: 'modern-clean',     label: t('Modern Clean') },
      { value: 'minimal-line',     label: t('Minimal Line') },
      { value: 'magazine-stepped', label: t('Magazine Stepped') },
      { value: 'circle-numbered',  label: t('Circle Numbered') },
      { value: 'card-rows',        label: t('Card Rows') },
      { value: 'glass-tier',       label: t('Glass Tier') },
      { value: 'neon-trail',       label: t('Neon Trail') },
      { value: 'brutalist-block',  label: t('Brutalist Block') },
      { value: 'gradient-flow',    label: t('Gradient Flow') },
      { value: 'sticker-steps',    label: t('Sticker Steps') },
      { value: 'retro-checklist',  label: t('Retro Checklist') },
      { value: 'tilt-cards',       label: t('Tilt Cards') },
      { value: 'custom',           label: t('Personalizzato') },
    ]},
    ...textEffectsFields([
      { value: 'title', label: t('Solo Titolo') },
      { value: 'description', label: t('Solo Descrizione') },
      { value: 'all', label: t('Tutti gli elementi testuali') },
    ]),

    { type: 'separator', label: t('Tipografia') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Items'),
      presetKey: 'typography_preset',
      responsiveKeys: ['size'],
      keys: {
        size:  'font_size',
        color: 'text_color',
      },
      sizeMin: 10, sizeMax: 20,
    },

    { type: 'separator', label: t('Dimensioni') },
    { key: 'circle_size', label: t('Dimensione cerchio (px)'), type: 'range', min: 24, max: 60, step: 2 },
    { key: 'gap', label: t('Gap aggiuntivo (px)'), type: 'range', min: 0, max: 40, step: 4 },

    { type: 'separator', label: t('Connettore') },
    { key: 'connector_style', label: t('Stile connettore'), type: 'select', options: [
      { value: 'line', label: t('Linea continua') },
      { value: 'dashed', label: t('Tratteggiato') },
      { value: 'dotted', label: t('Puntinato') },
    ]},
    { key: 'connector_color', label: t('Colore connettore'), type: 'color' },

    { type: 'separator', label: t('Colori') },
    { key: 'completed_color', label: t('Colore completato'), type: 'color' },
    { key: 'active_color', label: t('Colore attivo'), type: 'color' },
    { key: 'pending_color', label: t('Colore in attesa'), type: 'color' },

    ...shadowField,
    ...borderFields(),
  ],
};
