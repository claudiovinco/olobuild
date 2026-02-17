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
    show_sort: false,
    sort_options: 'date|title',
    columns: '3',
    columns_mobile: '1',
    gap: 'medium',
    match_height: false,
    card_style: 'default',
    image_height: '200',
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
    ribbon_field: '',
    ribbon_position: 'top-right',
    ribbon_bg: '#e11d48',
    ribbon_color: '#ffffff',
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

    { type: 'separator' },

    // ── Filtri ──
    { key: 'taxonomy', label: 'Tassonomia', type: 'select', optionsSource: 'taxonomies' },
    { key: 'show_filters', label: 'Mostra filtri tassonomia', type: 'toggle' },
    { key: 'filter_style', label: 'Stile filtri', type: 'select', options: [
      { value: 'pills', label: 'Pills' },
      { value: 'dropdown', label: 'Dropdown' },
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
    { key: 'card_style', label: 'Stile card', type: 'select', options: [
      { value: 'default', label: 'Predefinito' },
      { value: 'hover', label: 'Effetto hover' },
      { value: 'primary', label: 'Primary' },
    ]},

    { type: 'separator', label: 'Contenuto card' },

    // ── Contenuto card ──
    { key: 'show_image', label: 'Mostra immagine', type: 'toggle' },
    { key: 'image_height', label: 'Altezza immagine (px)', type: 'range', min: 100, max: 500, step: 10,
      condition: { field: 'show_image', value: true } },
    { key: 'show_category', label: 'Mostra badge categoria', type: 'toggle' },
    { key: 'show_excerpt', label: 'Mostra estratto', type: 'toggle' },
    { key: 'excerpt_length', label: 'Parole estratto', type: 'range', min: 5, max: 50, step: 1,
      condition: { field: 'show_excerpt', value: true } },
    { key: 'show_meta', label: 'Mostra data e autore', type: 'toggle' },

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

    { type: 'separator', label: 'Effetti hover' },

    // ── Hover ──
    { key: 'hover_effect', label: 'Effetto immagine', type: 'select', options: [
      { value: 'none', label: 'Nessuno' },
      { value: 'zoom', label: 'Zoom' },
      { value: 'zoom-rotate', label: 'Zoom + rotazione' },
      { value: 'brightness', label: 'Luminosità' },
      { value: 'desaturate', label: 'Desatura → colore' },
      { value: 'blur-in', label: 'Sfocatura → nitido' },
    ]},

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
