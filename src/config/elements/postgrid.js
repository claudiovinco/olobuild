export default {
  type: 'postgrid',
  name: 'Griglia articoli',
  icon: 'dashicons-grid-view',
  category: 'dynamic',
  defaults: {
    post_type: 'post',
    posts_per_page: '12',
    orderby: 'date',
    order: 'DESC',
    meta_key: '',
    taxonomy: '',
    show_filters: false,
    filter_style: 'pills',
    filter_align: 'left',
    show_sort: false,
    sort_options: 'date|title',
    columns: '3',
    columns_mobile: '1',
    gap: 'medium',
    match_height: false,
    masonry: false,
    card_style: 'default',
    card_primary_bg: '',
    image_height: '200',
    image_radius: '0',
    card_radius: '4',
    show_image: true,
    show_category: true,
    show_excerpt: true,
    excerpt_length: '20',
    show_meta: true,
    show_price: false,
    price_field: 'rental_price_night',
    price_prefix: '€',
    price_suffix: '/notte',
    link_text: 'Vedi',
    link_style: 'button',
    hover_effect: 'none',
    hover_image_field: '',
    hover_video_field: '',
    ribbon_field: '',
    ribbon_position: 'top-right',
    ribbon_bg: '',
    ribbon_color: '',
    meta_filter: '',
    // Stile testo
    tile_padding: { top: 15, right: 15, bottom: 15, left: 15 },
    title_size: '1',
    excerpt_size: '0.92',
    title_color: '',
    excerpt_color: '',
    meta_color: '',
    body_bg: '',
    body_bg_opacity: '100',
    // Paginazione
    pagination: false,
    items_per_page: '6',
    pagination_style: 'dots',
    // Ken Burns
    fx_kenburns: false,
    fx_kenburns_speed: '20',
    fx_kenburns_scale: '1.12',
    // Dati servizio
    show_service_stats: false,
    show_service_club: false,
    show_service_opening: false,
    opening_bg_annual: '',
    opening_bg_seasonal: '',
    opening_size: '11',
    // Overlay gradient
    overlay_gradient: false,
    overlay_color: '#000000',
    overlay_opacity: '50',
    overlay_direction: 'bottom',
    overlay_height: '50',
  },
  fields: [
    // ── Query ──
    { key: 'post_type', label: 'Tipo di contenuto', type: 'select', optionsSource: 'postTypes', options: [] },
    { key: 'posts_per_page', label: 'Max articoli', type: 'range', min: 1, max: 50, step: 1 },
    { key: 'orderby', label: 'Ordina per', type: 'select', options: [
      { value: 'date', label: 'Data' },
      { value: 'title', label: 'Titolo' },
      { value: 'modified', label: 'Data modifica' },
      { value: 'rand', label: 'Casuale' },
      { value: 'meta_value_num', label: 'Meta value (numerico)' },
    ]},
    { key: 'order', label: 'Ordine', type: 'select', options: [
      { value: 'DESC', label: 'Decrescente' },
      { value: 'ASC', label: 'Crescente' },
    ]},
    { key: 'meta_key', label: 'Meta key (per ordinamento meta)', type: 'text',
      condition: { field: 'orderby', value: 'meta_value_num' } },
    { key: 'meta_filter', label: 'Filtro meta (chiave=valore)', type: 'text' },

    { type: 'separator' },

    // ── Filtri ──
    { key: 'taxonomy', label: 'Tassonomia', type: 'select', optionsSource: 'taxonomies' },
    { key: 'show_filters', label: 'Mostra filtri tassonomia', type: 'toggle' },
    { key: 'filter_style', label: 'Stile filtri', type: 'select', options: [
      { value: 'pills', label: 'Pills' },
      { value: 'minimal', label: 'Minimale' },
      { value: 'dropdown', label: 'Dropdown' },
    ], condition: { field: 'show_filters', value: true } },
    { key: 'filter_align', label: 'Allineamento filtri', type: 'select', options: [
      { value: 'left', label: 'Sinistra' },
      { value: 'center', label: 'Centro' },
      { value: 'right', label: 'Destra' },
    ], condition: { field: 'show_filters', value: true } },
    { key: 'show_sort', label: 'Mostra dropdown ordinamento', type: 'toggle' },
    { key: 'sort_options', label: 'Opzioni ordinamento (separate da pipe)', type: 'text',
      condition: { field: 'show_sort', value: true } },

    { type: 'separator', label: 'Layout' },

    // ── Layout ──
    { key: 'columns', label: 'Colonne (desktop)', type: 'select', options: [
      { value: '2', label: '2' },
      { value: '3', label: '3' },
      { value: '4', label: '4' },
      { value: '5', label: '5' },
    ]},
    { key: 'columns_mobile', label: 'Colonne (mobile)', type: 'select', options: [
      { value: '1', label: '1' },
      { value: '2', label: '2' },
    ]},
    { key: 'gap', label: 'Spaziatura', type: 'select', options: [
      { value: 'collapse', label: 'Nessuna' },
      { value: 'small', label: 'Piccola' },
      { value: 'default', label: 'Predefinita' },
      { value: 'medium', label: 'Media' },
      { value: 'large', label: 'Grande' },
    ]},
    { key: 'match_height', label: 'Stessa altezza', type: 'toggle' },
    { key: 'masonry', label: 'Masonry', type: 'toggle' },
    { key: 'card_style', label: 'Stile card', type: 'select', options: [
      { value: 'default', label: 'Predefinito' },
      { value: 'hover', label: 'Effetto hover' },
      { value: 'primary', label: 'Primary' },
      { value: 'minimal', label: 'Minimale' },
    ]},
    { key: 'card_primary_bg', label: 'Colore sfondo card', type: 'color',
      condition: { field: 'card_style', value: 'primary' } },

    { type: 'separator', label: 'Contenuto card' },

    // ── Contenuto card ──
    { key: 'show_image', label: 'Mostra immagine', type: 'toggle' },
    { key: 'image_height', label: 'Altezza immagine (px)', type: 'range', min: 100, max: 500, step: 10,
      condition: { field: 'show_image', value: true } },
    { key: 'image_radius', label: 'Raggio bordo immagine (px)', type: 'border-radius',
      condition: { field: 'show_image', value: true } },
    { key: 'card_radius', label: 'Raggio bordo card (px)', type: 'border-radius' },
    { key: 'show_category', label: 'Mostra badge categoria', type: 'toggle' },
    { key: 'show_excerpt', label: 'Mostra estratto', type: 'toggle' },
    { key: 'excerpt_length', label: 'Parole estratto', type: 'range', min: 5, max: 50, step: 1,
      condition: { field: 'show_excerpt', value: true } },
    { key: 'show_meta', label: 'Mostra data e autore', type: 'toggle' },

    { type: 'separator', label: 'Dati servizio' },

    // ── Dati servizio ──
    { key: 'show_service_stats', label: 'Mostra stats servizio', type: 'toggle',
      condition: { field: 'post_type', value: 'olo_service' } },
    { key: 'show_service_club', label: 'Mostra club', type: 'toggle',
      condition: { field: 'post_type', value: 'olo_service' } },
    { key: 'show_service_opening', label: 'Mostra badge apertura', type: 'toggle',
      condition: { field: 'post_type', value: 'olo_service' } },
    { key: 'opening_bg_annual', label: 'Sfondo apertura annuale', type: 'color',
      condition: { field: 'show_service_opening', value: true } },
    { key: 'opening_bg_seasonal', label: 'Sfondo apertura stagionale', type: 'color',
      condition: { field: 'show_service_opening', value: true } },
    { key: 'opening_size', label: 'Dimensione testo badge (px)', type: 'range', min: 8, max: 18, step: 1,
      condition: { field: 'show_service_opening', value: true } },

    { type: 'separator', label: 'Prezzo' },

    // ── Prezzo ──
    { key: 'show_price', label: 'Mostra prezzo', type: 'toggle' },
    { key: 'price_field', label: 'Meta key prezzo', type: 'text',
      condition: { field: 'show_price', value: true } },
    { key: 'price_prefix', label: 'Prefisso prezzo', type: 'text',
      condition: { field: 'show_price', value: true } },
    { key: 'price_suffix', label: 'Suffisso prezzo', type: 'text',
      condition: { field: 'show_price', value: true } },

    { type: 'separator', label: 'Link' },

    // ── Link ──
    { key: 'link_text', label: 'Testo link', type: 'text' },
    { key: 'link_style', label: 'Stile link', type: 'select', options: [
      { value: 'button', label: 'Pulsante' },
      { value: 'text', label: 'Link testuale' },
      { value: 'card', label: 'Card cliccabile' },
    ]},

    { type: 'separator', label: 'Stile testo' },

    // ── Stile testo ──
    { key: 'tile_padding', label: 'Padding (px)', type: 'spacing', max: 40 },
    { key: 'title_size', label: 'Dimensione titolo (em)', type: 'range', min: 0.7, max: 2.5, step: 0.05 },
    { key: 'title_color', label: 'Colore titolo', type: 'color' },
    { key: 'excerpt_size', label: 'Dimensione estratto (em)', type: 'range', min: 0.7, max: 1.5, step: 0.05 },
    { key: 'excerpt_color', label: 'Colore estratto', type: 'color' },
    { key: 'meta_color', label: 'Colore meta (data/autore)', type: 'color' },
    { key: 'body_bg', label: 'Sfondo area testo', type: 'color' },
    { key: 'body_bg_opacity', label: 'Opacità sfondo testo (%)', type: 'range', min: 0, max: 100, step: 5,
      condition: { field: 'body_bg', value: '', operator: '!=' } },

    { type: 'separator', label: 'Paginazione' },

    // ── Paginazione ──
    { key: 'pagination', label: 'Abilita paginazione', type: 'toggle' },
    { key: 'items_per_page', label: 'Articoli per pagina', type: 'range', min: 2, max: 24, step: 1,
      condition: { field: 'pagination', value: true } },
    { key: 'pagination_style', label: 'Stile paginazione', type: 'select', options: [
      { value: 'dots', label: 'Punti' },
      { value: 'numbers', label: 'Numeri' },
      { value: 'arrows', label: 'Frecce' },
      { value: 'loadmore', label: 'Carica altri' },
      { value: 'infinite', label: 'Scroll infinito' },
    ], condition: { field: 'pagination', value: true } },

    { type: 'separator', label: 'Effetti hover' },

    // ── Hover ──
    { key: 'hover_effect', label: 'Effetto immagine', type: 'select', options: [
      { value: 'none', label: 'Nessuno' },
      { value: 'zoom', label: 'Zoom' },
      { value: 'zoom-rotate', label: 'Zoom + rotazione' },
      { value: 'brightness', label: 'Luminosità' },
      { value: 'desaturate', label: 'Desatura → colore' },
      { value: 'blur-in', label: 'Sfocatura → nitido' },
      { value: 'slide-up', label: 'Scorrimento in alto' },
      { value: 'glow', label: 'Bagliore' },
      { value: 'tilt', label: 'Tilt 3D' },
    ]},
    { key: 'hover_image_field', label: 'Meta key immagine hover', type: 'text',
      condition: { field: 'show_image', value: true } },
    { key: 'hover_video_field', label: 'Meta key video hover (mp4)', type: 'text',
      condition: { field: 'show_image', value: true } },

    { type: 'separator', label: 'Effetti immagine' },

    // ── Effetti immagine ──
    { key: 'fx_kenburns', label: 'Ken Burns (zoom cinematico)', type: 'toggle',
      condition: { field: 'show_image', value: true } },
    { key: 'fx_kenburns_speed', label: 'Velocità Ken Burns (s)', type: 'range', min: 10, max: 40, step: 1,
      condition: { field: 'fx_kenburns', value: true } },
    { key: 'fx_kenburns_scale', label: 'Intensità zoom', type: 'range', min: 1.05, max: 1.25, step: 0.01,
      condition: { field: 'fx_kenburns', value: true } },
    { key: 'overlay_gradient', label: 'Overlay sfumato', type: 'toggle',
      condition: { field: 'show_image', value: true } },
    { key: 'overlay_color', label: 'Colore overlay', type: 'color',
      condition: { field: 'overlay_gradient', value: true } },
    { key: 'overlay_opacity', label: 'Opacità overlay (%)', type: 'range', min: 10, max: 90, step: 5,
      condition: { field: 'overlay_gradient', value: true } },
    { key: 'overlay_direction', label: 'Direzione sfumatura', type: 'select', options: [
      { value: 'bottom', label: 'Dal basso' },
      { value: 'top', label: "Dall'alto" },
      { value: 'left', label: 'Da sinistra' },
      { value: 'right', label: 'Da destra' },
    ], condition: { field: 'overlay_gradient', value: true } },
    { key: 'overlay_height', label: 'Altezza gradiente (%)', type: 'range', min: 20, max: 100, step: 5,
      condition: { field: 'overlay_gradient', value: true } },

    { type: 'separator', label: 'Ribbon' },

    // ── Ribbon ──
    { key: 'ribbon_field', label: 'Meta key ribbon', type: 'text' },
    { key: 'ribbon_position', label: 'Posizione ribbon', type: 'select', options: [
      { value: 'top-left', label: 'Alto sinistra' },
      { value: 'top-right', label: 'Alto destra' },
    ]},
    { key: 'ribbon_bg', label: 'Sfondo ribbon', type: 'color' },
    { key: 'ribbon_color', label: 'Testo ribbon', type: 'color' },
  ],
};
