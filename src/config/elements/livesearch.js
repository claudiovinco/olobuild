import { shadowField } from './_shared.js';

export default {
  type: 'livesearch',
  name: 'Ricerca Live',
  icon: 'dashicons-search',
  category: 'navigation',

  defaults: {
    // Input e UX
    placeholder: 'Cerca...',
    mode: 'expanded',
    modal_width: '560',
    backdrop_color: 'rgba(0,0,0,0.5)',
    min_chars: '2',
    debounce_ms: '300',
    animated_placeholder: false,
    placeholder_words: '',

    // Risultati
    max_results: '10',
    results_columns: '1',
    show_all_url: '',
    show_all_text: 'Vedi tutti i risultati',
    show_thumbnail: true,
    show_excerpt: true,
    title_only: false,
    no_results_text: 'Nessun risultato trovato',

    // Filtri
    post_types: 'post,page',
    taxonomy_filter: '',
    taxonomy_terms: '',
    exclude_terms: '',

    // Stile Input
    input_bg: '#ffffff',
    input_color: '',
    icon_color: '',
    input_font_size: '14',
    input_height: '44',
    input_border_color: '#e5e7eb',
    input_border_radius: '8',
    focus_border_color: '#6366f1',

    // Stile Popup
    results_bg: '#ffffff',
    results_border_color: '',
    item_hover_bg: '',
    title_color: '',
    excerpt_color: '#6b7280',
    results_max_height: '400',
    results_border_radius: '10',
    thumb_width: '48',
    thumb_height: '48',
    thumb_radius: '6',

    // Bordo/Ombra
    shadow: 'none',
  },

  fields: [
    // ─── Input e UX ───
    { type: 'separator', label: 'Input e UX' },
    { key: 'placeholder', label: 'Placeholder', type: 'text' },
    { key: 'animated_placeholder', label: 'Placeholder animato', type: 'toggle' },
    { key: 'placeholder_words', label: 'Parole animate (una per riga)', type: 'textarea',
      show: s => s.animated_placeholder },
    {
      key: 'mode',
      label: 'Modalit\u00e0',
      type: 'select',
      options: [
        { value: 'expanded', label: 'Estesa (campo visibile)' },
        { value: 'compact', label: 'Compatta (solo icona)' },
        { value: 'modal', label: 'Modale (popup centrato)' },
        { value: 'inline', label: 'Inline (per megamenu/sidebar)' },
      ],
    },
    { key: 'modal_width', label: 'Larghezza modale (px)', type: 'range', min: 400, max: 800, step: 20,
      show: s => s.mode === 'modal' },
    { key: 'backdrop_color', label: 'Colore backdrop', type: 'color',
      show: s => s.mode === 'modal' },
    { key: 'min_chars', label: 'Caratteri minimi', type: 'range', min: 1, max: 3, step: 1 },
    { key: 'debounce_ms', label: 'Debounce (ms)', type: 'range', min: 100, max: 800, step: 50 },

    // ─── Risultati ───
    { type: 'separator', label: 'Risultati' },
    { key: 'max_results', label: 'Max risultati', type: 'range', min: 3, max: 100, step: 1 },
    { key: 'results_columns', label: 'Colonne risultati', type: 'range', min: 1, max: 4, step: 1 },
    { key: 'show_all_url', label: 'Pagina "Vedi tutti"', type: 'select', optionsSource: 'wpPages' },
    { key: 'show_all_text', label: 'Testo "Vedi tutti"', type: 'text',
      show: s => !!s.show_all_url },
    { key: 'show_thumbnail', label: 'Mostra miniatura', type: 'toggle' },
    { key: 'show_excerpt', label: 'Mostra estratto', type: 'toggle' },
    { key: 'title_only', label: 'Cerca solo nel titolo', type: 'toggle' },
    { key: 'no_results_text', label: 'Testo nessun risultato', type: 'text' },

    // ─── Filtri ───
    { type: 'separator', label: 'Filtri' },
    {
      key: 'post_types',
      label: 'Tipi di contenuto',
      type: 'multi_pills',
      options: [
        { value: 'post', label: 'Articoli', icon: 'file-text' },
        { value: 'page', label: 'Pagine', icon: 'album' },
        { value: 'olo_service', label: 'Strutture', icon: 'home' },
        { value: 'olo_room', label: 'Sale', icon: 'grid' },
      ],
    },
    { key: 'taxonomy_filter', label: 'Filtra per tassonomia', type: 'select', optionsSource: 'taxonomies' },
    { key: 'taxonomy_terms', label: 'Termini (slug separati da virgola)', type: 'text',
      show: s => !!s.taxonomy_filter },
    { key: 'exclude_terms', label: 'Escludi parole (separate da virgola)', type: 'text' },

    // ─── Stile Input ───
    { type: 'separator', label: 'Stile Input' },
    { key: 'input_bg', label: 'Sfondo input', type: 'color' },
    { key: 'input_color', label: 'Colore testo', type: 'color' },
    { key: 'icon_color', label: 'Colore icona', type: 'color' },
    { key: 'input_border_color', label: 'Colore bordo', type: 'color' },
    { key: 'focus_border_color', label: 'Colore bordo focus', type: 'color' },
    { key: 'input_font_size', label: 'Dimensione font (px)', type: 'range', min: 12, max: 24, step: 1 },
    { key: 'input_height', label: 'Altezza input (px)', type: 'range', min: 32, max: 72, step: 2 },
    { key: 'input_border_radius', label: 'Arrotondamento input (px)', type: 'border-radius' },

    // ─── Stile Popup ───
    { type: 'separator', label: 'Stile Popup risultati' },
    { key: 'results_bg', label: 'Sfondo popup', type: 'color' },
    { key: 'results_border_color', label: 'Bordo popup', type: 'color' },
    { key: 'results_border_radius', label: 'Arrotondamento popup (px)', type: 'border-radius' },
    { key: 'item_hover_bg', label: 'Sfondo hover elemento', type: 'color' },
    { key: 'title_color', label: 'Colore titolo', type: 'color' },
    { key: 'excerpt_color', label: 'Colore estratto', type: 'color',
      show: s => s.show_excerpt !== false },
    { key: 'results_max_height', label: 'Altezza max popup (px)', type: 'range', min: 200, max: 800, step: 20 },
    { key: 'thumb_width', label: 'Larghezza miniatura (px)', type: 'range', min: 32, max: 120, step: 4,
      show: s => s.show_thumbnail !== false },
    { key: 'thumb_height', label: 'Altezza miniatura (px)', type: 'range', min: 32, max: 120, step: 4,
      show: s => s.show_thumbnail !== false },
    { key: 'thumb_radius', label: 'Arrotondamento miniatura (px)', type: 'border-radius',
      show: s => s.show_thumbnail !== false },

    // ─── Bordo / Ombra ───
    ...shadowField,
  ],
};
