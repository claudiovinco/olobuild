import { textEffectsFields, textEffectsDefaults } from './_shared';
export default {
  type: 'timeline',
  name: 'Timeline',
  icon: 'dashicons-backup',
  category: 'interactive',
  defaults: {
    // Items
    items: [
      { id: 'tl-1', title: 'Primo evento', description: 'Descrizione del primo evento della timeline.', date: '2024', image: '', icon: 'star', icon_color: '' },
      { id: 'tl-2', title: 'Secondo evento', description: 'Descrizione del secondo evento della timeline.', date: '2025', image: '', icon: 'check', icon_color: '' },
      { id: 'tl-3', title: 'Terzo evento', description: 'Descrizione del terzo evento della timeline.', date: '2026', image: '', icon: 'heart', icon_color: '' },
    ],

    // Layout
    layout: 'vertical-center',
    mobile_layout: 'vertical-left',

    // Linea
    line_color: '',
    line_width: '3',
    line_style: 'solid',
    line_progress: false,
    line_progress_color: '',
    line_progress_width: '',

    // Marker
    marker_type: 'dot',
    marker_size: '20',
    marker_color: '',
    marker_bg: '',
    marker_border_width: '3',
    marker_border_color: '',
    marker_shape: 'circle',
    marker_pulse: false,

    // Marker finale
    end_marker: true,
    end_marker_icon: 'flag',
    end_marker_color: '',
    end_marker_bg: '',
    end_marker_size: '',

    // Card
    card_bg: '',
    card_text_color: '',
    tile_padding: { top: 20, right: 20, bottom: 20, left: 20 },
    card_border_radius: '12',
    card_shadow: 'md',
    card_border_width: '0',
    card_border_color: '',
    card_hover: 'lift',
    card_arrow: true,
    card_max_width: '',
    card_media_ratio: 'auto',
    card_media_margin: '0',
    card_media_radius: '4',

    // Data
    date_position: 'outside',
    date_color: '',
    date_size: '14',
    date_weight: '600',

    // Tipografia
    title_size: '18',
    title_weight: '600',
    title_color: '',
    description_size: '14',
    description_color: '',

    // Animazione
    animation: 'fade-up',
    stagger_delay: '150',
    animation_duration: '600',

    // Orizzontale
    h_card_width: '300',
    h_visible_items: '3',
    h_gap: '24',
    h_arrow_color: '',
    h_arrow_bg: '',
    ...textEffectsDefaults,
  },
  fields: [
    // ── Items ──
    { key: 'items', label: 'Eventi', type: 'content-items',
      itemFields: [
        { key: 'title', label: 'Titolo', type: 'text' },
        { key: 'description', label: 'Descrizione', type: 'textarea' },
        { key: 'date', label: 'Data / etichetta', type: 'text' },
        { key: 'image', label: 'Immagine', type: 'image' },
        { key: 'video', label: 'Video', type: 'media' },
        { key: 'icon', label: 'Icona marker', type: 'icon' },
        { key: 'icon_color', label: 'Colore icona', type: 'color' },
      ],
      newItemDefaults: { title: 'Nuovo evento', description: 'Descrizione evento.', date: '', image: '', video: '', icon: '', icon_color: '' },
      itemLabel: 'Evento',
    },

    // ── Layout ──
    { type: 'separator', label: 'Layout' },
    { key: 'layout', label: 'Layout', type: 'select', options: [
      { value: 'vertical-center', label: 'Verticale alternato' },
      { value: 'vertical-left', label: 'Verticale sinistra' },
      { value: 'vertical-right', label: 'Verticale destra' },
      { value: 'horizontal', label: 'Orizzontale' },
    ]},
    { key: 'mobile_layout', label: 'Layout mobile', type: 'select', options: [
      { value: 'vertical-left', label: 'Sinistra' },
      { value: 'vertical-right', label: 'Destra' },
    ]},

    // ── Linea ──
    { type: 'separator', label: 'Linea' },
    { key: 'line_color', label: 'Colore linea', type: 'color' },
    { key: 'line_width', label: 'Spessore linea (px)', type: 'range', min: 1, max: 8, step: 1 },
    { key: 'line_style', label: 'Stile linea', type: 'select', options: [
      { value: 'solid', label: 'Continua' },
      { value: 'dashed', label: 'Tratteggiata' },
      { value: 'dotted', label: 'Puntinata' },
    ]},
    { key: 'line_progress', label: 'Linea progressiva', type: 'toggle' },
    { key: 'line_progress_color', label: 'Colore progresso', type: 'color',
      condition: { field: 'line_progress', value: true } },
    { key: 'line_progress_width', label: 'Spessore progresso (px)', type: 'range', min: 1, max: 16, step: 1,
      condition: { field: 'line_progress', value: true } },

    // ── Marker ──
    { type: 'separator', label: 'Marker' },
    { key: 'marker_type', label: 'Tipo marker', type: 'select', options: [
      { value: 'dot', label: 'Pallino' },
      { value: 'icon', label: 'Icona' },
      { value: 'number', label: 'Numero' },
    ]},
    { key: 'marker_size', label: 'Dimensione (px)', type: 'range', min: 10, max: 40, step: 2 },
    { key: 'marker_color', label: 'Colore marker', type: 'color' },
    { key: 'marker_bg', label: 'Sfondo marker', type: 'color' },
    { key: 'marker_border_width', label: 'Bordo marker (px)', type: 'range', min: 0, max: 6, step: 1 },
    { key: 'marker_border_color', label: 'Colore bordo marker', type: 'color',
      condition: { field: 'marker_border_width', operator: '>', value: '0' } },
    { key: 'marker_shape', label: 'Forma', type: 'select', options: [
      { value: 'circle', label: 'Cerchio' },
      { value: 'square', label: 'Quadrato' },
      { value: 'diamond', label: 'Rombo' },
    ]},
    { key: 'marker_pulse', label: 'Animazione pulse', type: 'toggle' },

    // ── Marker finale ──
    { type: 'separator', label: 'Marker finale' },
    { key: 'end_marker', label: 'Mostra marker finale', type: 'toggle' },
    { key: 'end_marker_icon', label: 'Icona finale', type: 'icon',
      condition: { field: 'end_marker', value: true } },
    { key: 'end_marker_color', label: 'Colore icona finale', type: 'color',
      condition: { field: 'end_marker', value: true } },
    { key: 'end_marker_bg', label: 'Sfondo marker finale', type: 'color',
      condition: { field: 'end_marker', value: true } },
    { key: 'end_marker_size', label: 'Dimensione finale (px)', type: 'range', min: 10, max: 60, step: 2,
      condition: { field: 'end_marker', value: true } },

    // ── Card ──
    { type: 'separator', label: 'Card' },
    { key: 'card_bg', label: 'Sfondo card', type: 'color' },
    { key: 'card_text_color', label: 'Colore testo card', type: 'color' },
    { key: 'tile_padding', label: 'Padding (px)', type: 'spacing', max: 40 },
    { key: 'card_border_radius', label: 'Arrotondamento (px)', type: 'border-radius' },
    { key: 'card_border_radius_hover', label: 'Raggio bordo (hover)', type: 'border-radius' },
    { key: 'card_shadow', label: 'Ombra', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'sm', label: 'Leggera' },
      { value: 'md', label: 'Media' },
      { value: 'lg', label: 'Grande' },
      { value: 'custom', label: 'Personalizzata' },
    ]},
    { key: 'card_shadow_h', label: 'Offset H (px)', type: 'range', min: -50, max: 50, step: 1,
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_v', label: 'Offset V (px)', type: 'range', min: -50, max: 50, step: 1,
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_blur', label: 'Sfocatura (px)', type: 'range', min: 0, max: 100, step: 1,
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_spread', label: 'Espansione (px)', type: 'range', min: -50, max: 50, step: 1,
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_color', label: 'Colore ombra', type: 'color',
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_shadow_inset', label: 'Ombra interna', type: 'toggle',
      condition: { field: 'card_shadow', op: 'eq', value: 'custom' } },
    { key: 'card_border_width', label: 'Bordo card (px)', type: 'range', min: 0, max: 4, step: 1 },
    { key: 'card_border_color', label: 'Colore bordo card', type: 'color',
      condition: { field: 'card_border_width', operator: '>', value: '0' } },
    { key: 'card_hover', label: 'Effetto hover', type: 'select', options: [
      { value: 'none', label: 'Nessuno' },
      { value: 'lift', label: 'Sollevamento' },
      { value: 'glow', label: 'Bagliore' },
      { value: 'scale', label: 'Scala' },
    ]},
    { key: 'card_max_width', label: 'Larghezza max card (px)', type: 'range', min: 0, max: 800, step: 10 },
    { key: 'card_arrow', label: 'Freccia verso linea', type: 'toggle' },
    { key: 'card_media_ratio', label: 'Rapporto media', type: 'select', options: [
      { value: 'auto', label: 'Automatico' },
      { value: '16/9', label: '16:9' },
      { value: '4/3', label: '4:3' },
      { value: '3/2', label: '3:2' },
      { value: '1/1', label: '1:1' },
      { value: '2/1', label: '2:1' },
    ]},
    { key: 'card_media_margin', label: 'Margine media (px)', type: 'spacing', max: 20 },
    { key: 'card_media_radius', label: 'Arrotondamento media (px)', type: 'border-radius' },
    { key: 'card_media_radius_hover', label: 'Raggio bordo (hover)', type: 'border-radius' },

    // ── Data ──
    { type: 'separator', label: 'Etichetta data' },
    { key: 'date_position', label: 'Posizione data', type: 'select', options: [
      { value: 'outside', label: 'Esterna' },
      { value: 'inside', label: 'Interna alla card' },
      { value: 'above', label: 'Sopra la card' },
    ]},
    { key: 'date_color', label: 'Colore data', type: 'color' },
    { key: 'date_size', label: 'Dimensione data (px)', type: 'range', min: 10, max: 20, step: 1 },
    { key: 'date_weight', label: 'Peso data', type: 'select', options: [
      { value: '400', label: 'Normale' },
      { value: '500', label: 'Medio' },
      { value: '600', label: 'Semi-grassetto' },
      { value: '700', label: 'Grassetto' },
    ]},

    // ── Tipografia ──
    { type: 'separator', label: 'Tipografia' },
    { key: 'title_size', label: 'Dim. titolo (px)', type: 'range', min: 14, max: 32, step: 1 },
    { key: 'title_weight', label: 'Peso titolo', type: 'select', options: [
      { value: '400', label: 'Normale' },
      { value: '500', label: 'Medio' },
      { value: '600', label: 'Semi-grassetto' },
      { value: '700', label: 'Grassetto' },
    ]},
    { key: 'title_color', label: 'Colore titolo', type: 'color' },
    { key: 'description_size', label: 'Dim. descrizione (px)', type: 'range', min: 12, max: 20, step: 1 },
    { key: 'description_color', label: 'Colore descrizione', type: 'color' },

    // ── Animazione ──
    { type: 'separator', label: 'Animazione' },
    { key: 'animation', label: 'Animazione', type: 'select', options: [
      { value: 'none', label: 'Nessuna' },
      { value: 'fade-up', label: 'Fade up' },
      { value: 'fade-in', label: 'Fade in' },
      { value: 'slide-left', label: 'Scorrimento da sinistra' },
      { value: 'slide-right', label: 'Scorrimento da destra' },
      { value: 'zoom-in', label: 'Zoom in' },
    ]},
    { key: 'stagger_delay', label: 'Ritardo stagger (ms)', type: 'range', min: 0, max: 500, step: 25,
      condition: { field: 'animation', operator: '!=', value: 'none' } },
    { key: 'animation_duration', label: 'Durata animazione (ms)', type: 'range', min: 200, max: 1200, step: 50,
      condition: { field: 'animation', operator: '!=', value: 'none' } },

    // ── Orizzontale ──
    { type: 'separator', label: 'Opzioni orizzontale' },
    { key: 'h_card_width', label: 'Larghezza card (px)', type: 'range', min: 200, max: 500, step: 10,
      condition: { field: 'layout', value: 'horizontal' } },
    { key: 'h_visible_items', label: 'Elementi visibili', type: 'range', min: 1, max: 5, step: 1,
      condition: { field: 'layout', value: 'horizontal' } },
    { key: 'h_gap', label: 'Gap (px)', type: 'range', min: 8, max: 48, step: 4,
      condition: { field: 'layout', value: 'horizontal' } },
    { key: 'h_arrow_color', label: 'Colore frecce', type: 'color',
      condition: { field: 'layout', value: 'horizontal' } },
    { key: 'h_arrow_bg', label: 'Sfondo frecce', type: 'color',
      condition: { field: 'layout', value: 'horizontal' } },
    ...textEffectsFields([
      { value: 'title', label: 'Solo Titolo' },
      { value: 'description', label: 'Solo Descrizione' },
      { value: 'all', label: 'Tutti gli elementi testuali' },
    ]),
  ],
};
