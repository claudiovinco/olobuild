export default {
  type: 'booking',
  name: 'Booking Struttura',
  icon: 'dashicons-clipboard',
  category: 'booking',
  defaults: {
    service_id: 'auto',
    primary_color: '#6366F1',
    show_price: true,
    show_duration: true,
    widget_max_width: '480',
    widget_bg: '#FFFFFF',
    widget_border_radius: '12',
    widget_border_color: '#E5E7EB',
    widget_shadow: 'sm',
    btn_bg: '#6366F1',
    btn_color: '#FFFFFF',
    btn_radius: '8',
    available_color: '#6366F1',
    full_color: '#EF4444',
    slot_border_radius: '8',
    title_size: '18',
    title_weight: '700',
    title_color: '',
    meta_color: '#6B7280',
    success_color: '#10B981',
  },
  fields: [
    // ── Servizio ──
    { key: 'service_id', label: 'Servizio', type: 'select', options: [
      { value: 'auto', label: 'Auto (dal template)' },
      { value: 'all', label: 'Mostra tutti' },
    ]},
    { key: 'show_price', label: 'Mostra prezzo', type: 'toggle' },
    { key: 'show_duration', label: 'Mostra durata', type: 'toggle' },

    { type: 'separator', label: 'Widget' },

    // ── Widget ──
    { key: 'widget_max_width', label: 'Larghezza max (px)', type: 'range', min: 320, max: 800, step: 10 },
    { key: 'widget_bg', label: 'Sfondo', type: 'color' },
    { key: 'widget_border_radius', label: 'Border radius (px)', type: 'range', min: 0, max: 24, step: 1 },
    { key: 'widget_border_color', label: 'Bordo colore', type: 'color' },
    { key: 'widget_shadow', label: 'Ombra', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'sm', label: 'Leggera' },
      { value: 'md', label: 'Media' },
      { value: 'lg', label: 'Forte' },
    ]},

    { type: 'separator', label: 'Colori' },

    // ── Colori ──
    { key: 'primary_color', label: 'Colore primario', type: 'color' },
    { key: 'available_color', label: 'Giorni disponibili', type: 'color' },
    { key: 'full_color', label: 'Giorni pieni', type: 'color' },
    { key: 'success_color', label: 'Conferma', type: 'color' },

    { type: 'separator', label: 'Pulsanti' },

    // ── Pulsanti ──
    { key: 'btn_bg', label: 'Sfondo pulsante', type: 'color' },
    { key: 'btn_color', label: 'Testo pulsante', type: 'color' },
    { key: 'btn_radius', label: 'Border radius (px)', type: 'range', min: 0, max: 20, step: 1 },
    { key: 'slot_border_radius', label: 'Radius slot orari (px)', type: 'range', min: 0, max: 16, step: 1 },

    { type: 'separator', label: 'Tipografia' },

    // ── Tipografia ──
    { key: 'title_size', label: 'Dimensione titolo (px)', type: 'range', min: 14, max: 28, step: 1 },
    { key: 'title_weight', label: 'Peso titolo', type: 'select', options: [
      { value: '400', label: 'Normal' },
      { value: '600', label: 'Semi Bold' },
      { value: '700', label: 'Bold' },
    ]},
    { key: 'title_color', label: 'Colore titolo', type: 'color' },
    { key: 'meta_color', label: 'Colore info', type: 'color' },
  ],
};
