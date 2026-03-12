export default {
  type: 'serviceprices',
  name: 'Prezzi Servizio',
  icon: 'dashicons-money-alt',
  category: 'booking',
  defaults: {
    meta_prefix: '_olo_service_',
    columns: '3',
    gap: '16',
    card_radius: '12',
    card_border: '',
    card_padding: '20',
    accent_color: '',
    amount_size: '26',
    label_size: '14',
    label_color: '',
    hover_border: true,
  },
  fields: [
    { type: 'separator', label: 'Sorgente dati' },
    { key: 'meta_prefix', label: 'Tipo servizio', type: 'select', optionsSource: 'metaPrefixes' },

    { type: 'separator', label: 'Layout' },
    { key: 'columns', label: 'Colonne', type: 'range', min: 1, max: 4, step: 1 },
    { key: 'gap', label: 'Gap (px)', type: 'range', min: 8, max: 32, step: 4 },
    { key: 'card_radius', label: 'Raggio card (px)', type: 'border-radius' },
    { key: 'card_padding', label: 'Padding card (px)', type: 'range', min: 8, max: 32, step: 4 },

    { type: 'separator', label: 'Colori' },
    { key: 'accent_color', label: 'Colore accento', type: 'color' },
    { key: 'card_border', label: 'Bordo card', type: 'color' },
    { key: 'label_color', label: 'Etichette', type: 'color' },
    { key: 'hover_border', label: 'Bordo hover', type: 'toggle' },

    { type: 'separator', label: 'Tipografia' },
    { key: 'amount_size', label: 'Prezzo (px)', type: 'range', min: 18, max: 40, step: 1 },
    { key: 'label_size', label: 'Etichetta (px)', type: 'range', min: 10, max: 20, step: 1 },
  ],
};
