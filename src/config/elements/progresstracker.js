import { textEffectsFields, textEffectsDefaults } from './_shared';
import { shadowField } from './_shared.js';

export default {
  type: 'progresstracker',
  name: 'Progress tracker',
  icon: 'dashicons-editor-ol',
  category: 'content',
  defaults: {
    items: [
      { id: 'pt-1', title: 'Ordine ricevuto', description: 'Il tuo ordine è stato confermato.', icon: 'check', status: 'completed' },
      { id: 'pt-2', title: 'In preparazione', description: 'Stiamo preparando il tuo ordine.', icon: 'settings', status: 'active' },
      { id: 'pt-3', title: 'Spedito', description: 'Il pacco è in viaggio.', icon: 'cart', status: 'pending' },
      { id: 'pt-4', title: 'Consegnato', description: 'Consegna completata.', icon: 'home', status: 'pending' },
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
  },
  fields: [
    // ── Items ──
    { key: 'items', label: 'Passaggi', type: 'content-items',
      itemFields: [
        { key: 'title', label: 'Titolo', type: 'text' },
        { key: 'description', label: 'Descrizione', type: 'text' },
        { key: 'icon', label: 'Icona (UIkit)', type: 'icon' },
        { key: 'status', label: 'Stato', type: 'select', options: [
          { value: 'completed', label: 'Completato' },
          { value: 'active', label: 'Attivo' },
          { value: 'pending', label: 'In attesa' },
        ]},
      ],
      newItemDefaults: { title: 'Nuovo passaggio', description: 'Descrizione del passaggio.', icon: 'check', status: 'pending' },
      itemLabel: 'Passaggio',
    },

    // ── Layout ──
    { type: 'separator', label: 'Layout' },
    { key: 'layout', label: 'Layout', type: 'select', options: [
      { value: 'horizontal', label: 'Orizzontale' },
      { value: 'vertical', label: 'Verticale' },
    ]},
    { key: 'show_numbers', label: 'Mostra numeri', type: 'toggle' },
    { key: 'show_description', label: 'Mostra descrizione', type: 'toggle' },
    { key: 'circle_size', label: 'Dimensione cerchio (px)', type: 'range', min: 24, max: 60, step: 2 },
    { key: 'font_size', label: 'Dimensione testo (px)', type: 'range', min: 10, max: 20, step: 1 },
    { key: 'gap', label: 'Gap aggiuntivo (px)', type: 'range', min: 0, max: 40, step: 4 },

    // ── Connettore ──
    { type: 'separator', label: 'Connettore' },
    { key: 'connector_style', label: 'Stile connettore', type: 'select', options: [
      { value: 'line', label: 'Linea continua' },
      { value: 'dashed', label: 'Tratteggiato' },
      { value: 'dotted', label: 'Puntinato' },
    ]},
    { key: 'connector_color', label: 'Colore connettore', type: 'color' },

    // ── Colori ──
    { type: 'separator', label: 'Colori' },
    { key: 'completed_color', label: 'Colore completato', type: 'color' },
    { key: 'active_color', label: 'Colore attivo', type: 'color' },
    { key: 'pending_color', label: 'Colore in attesa', type: 'color' },
    { key: 'text_color', label: 'Colore testo', type: 'color' },

    ...shadowField,
    ...textEffectsFields([
      { value: 'title', label: 'Solo Titolo' },
      { value: 'description', label: 'Solo Descrizione' },
      { value: 'all', label: 'Tutti gli elementi testuali' },
    ]),
  ],
};
