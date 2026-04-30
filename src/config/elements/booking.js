export default {
  type: 'booking',
  name: 'Booking Struttura',
  icon: 'dashicons-clipboard',
  category: 'booking',
  defaults: {
    service_id: 'auto',
    primary_color: '',
    show_price: true,
    show_duration: true,
    widget_max_width: '480',
    widget_bg: '',
    widget_border_radius: '12',
    widget_border_color: '',
    widget_shadow: 'sm',
    btn_bg: '',
    btn_color: '',
    btn_radius: '8',
    available_color: '',
    full_color: '',
    slot_border_radius: '8',
    title_size: '18',
    title_weight: '700',
    title_color: '',
    meta_color: '',
    success_color: '',
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
    { key: 'widget_border_radius', label: 'Border radius (px)', type: 'border-radius' },
    { key: 'widget_border_radius_hover', label: 'Raggio bordo (hover)', type: 'border-radius' },
    { key: 'widget_border_color', label: 'Bordo colore', type: 'color' },
    { key: 'widget_shadow', label: 'Ombra', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'sm', label: 'Leggera' },
      { value: 'md', label: 'Media' },
      { value: 'lg', label: 'Forte' },
      { value: 'custom', label: 'Personalizzata' },
    ]},
    { key: 'widget_shadow_h', label: 'Offset H (px)', type: 'range', min: -50, max: 50, step: 1,
      condition: { field: 'widget_shadow', op: 'eq', value: 'custom' } },
    { key: 'widget_shadow_v', label: 'Offset V (px)', type: 'range', min: -50, max: 50, step: 1,
      condition: { field: 'widget_shadow', op: 'eq', value: 'custom' } },
    { key: 'widget_shadow_blur', label: 'Sfocatura (px)', type: 'range', min: 0, max: 100, step: 1,
      condition: { field: 'widget_shadow', op: 'eq', value: 'custom' } },
    { key: 'widget_shadow_spread', label: 'Espansione (px)', type: 'range', min: -50, max: 50, step: 1,
      condition: { field: 'widget_shadow', op: 'eq', value: 'custom' } },
    { key: 'widget_shadow_color', label: 'Colore ombra', type: 'color',
      condition: { field: 'widget_shadow', op: 'eq', value: 'custom' } },
    { key: 'widget_shadow_inset', label: 'Ombra interna', type: 'toggle',
      condition: { field: 'widget_shadow', op: 'eq', value: 'custom' } },

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
    { key: 'btn_radius', label: 'Border radius (px)', type: 'border-radius' },
    { key: 'btn_radius_hover', label: 'Raggio bordo (hover)', type: 'border-radius' },
    { key: 'slot_border_radius', label: 'Radius slot orari (px)', type: 'border-radius' },
    { key: 'slot_border_radius_hover', label: 'Raggio bordo (hover)', type: 'border-radius' },

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
