import { shadowField, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Product Grid — griglia prodotti moda/shop MINIMALE: media (immagine o placeholder a
 * strisce) + tag d'angolo + barra "Quick add" in hover, sotto categoria + titolo + prezzo.
 * Card SENZA sfondo/bordo/padding (contenitore trasparente). Link finale opzionale
 * ("View all 36 pieces"). Estratta 1:1 dal blueprint OLOthemes Atelier Noir (.an-prods/.an-prod).
 * Render Vue == PHP (ProductGridTile.vue). Nessun JS. Riusabile su tutti i temi e-commerce/moda.
 */
export default {
  type: 'productgrid',
  name: t('Griglia Prodotti'),
  icon: 'dashicons-products',
  category: 'media',

  defaults: {
    source: 'custom',
    woo_category: '',
    woo_limit: 8,
    woo_orderby: 'date',
    woo_order: 'DESC',
    woo_on_sale: false,
    woo_quick_add: 'Quick add',
    items: [
      { image: '', media_label: 'wool crêpe coat', tag: 'New', category: 'Outerwear', title: 'Crêpe Tailored Coat', price: '€1,290', link: '#', quick_add: 'Quick add' },
      { image: '', media_label: 'silk column dress', tag: '', category: 'Eveningwear', title: 'Silk Column Dress', price: '€980', link: '#', quick_add: 'Quick add' },
      { image: '', media_label: 'tuxedo jacket', tag: '', category: 'Tailoring', title: 'Le Smoking Jacket', price: '€1,150', link: '#', quick_add: 'Quick add' },
      { image: '', media_label: 'cashmere knit', tag: 'Atelier', category: 'Knitwear', title: 'Cashmere Roll-Neck', price: '€620', link: '#', quick_add: 'Quick add' },
    ],
    columns: 4,
    gap: 22,
    media_aspect: '3/4',
    media_bg: '',
    stripe_dark: false,
    hover_zoom: true,
    tag_bg: '',
    tag_color: '',
    quick_add_show: true,
    quick_add_bg: '',
    quick_add_color: '',
    category_color: '',
    title_font: 'heading',
    title_size: 21,
    title_color: '',
    price_color: '',
    footer_text: '',
    footer_url: '#',
    footer_color: '',

    // Card (default trasparente → tile minimale invariata)
    card_bg: '',
    card_border: '',
    card_radius: 0,
    card_padding: 0,
    // Shade swatches
    shade_size: 16,
    shade_border: 'rgba(255,255,255,0.3)',
    // Note (sottotitolo) + roast meter
    notes_color: '',
    notes_mono: false,
    roast_label: 'Roast',
    roast_on_color: '',
    roast_off_color: '',
    // Add button nel footer (oltre/al posto del Quick add hover)
    add_button: false,
    add_label: 'Add',
    add_bg: '',
    add_color: '',

    show_filters: false,
    filter_all_label: 'All',
    filter_list: '',
    filter_text_color: '',
    filter_active_bg: '',
    filter_active_color: '',
    filter_border_color: '',

    // KIT standard OLObuild (contenitore) — default no-op (render invariato).
    bg: { type: 'none' },
    shadow: 'none',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  fields: [
    { type: 'separator', label: t('Sorgente') },
    { key: 'source', label: t('Prodotti da'), type: 'select', options: [
      { value: 'custom', label: t('Voci manuali') },
      { value: 'woocommerce', label: t('WooCommerce') },
    ]},
    { key: 'woo_category', label: t('Categoria prodotto (slug, vuoto = tutte)'), type: 'text',
      condition: { field: 'source', value: 'woocommerce' }, placeholder: 'outerwear, knitwear' },
    { key: 'woo_limit', label: t('Numero prodotti'), type: 'range', min: 1, max: 24, step: 1,
      condition: { field: 'source', value: 'woocommerce' } },
    { key: 'woo_orderby', label: t('Ordina per'), type: 'select',
      condition: { field: 'source', value: 'woocommerce' }, options: [
      { value: 'date', label: t('Data') },
      { value: 'title', label: t('Titolo') },
      { value: 'price', label: t('Prezzo') },
      { value: 'popularity', label: t('Popolarità') },
      { value: 'rand', label: t('Casuale') },
    ]},
    { key: 'woo_order', label: t('Direzione'), type: 'select',
      condition: { field: 'source', value: 'woocommerce' }, options: [
      { value: 'DESC', label: t('Decrescente') },
      { value: 'ASC', label: t('Crescente') },
    ]},
    { key: 'woo_on_sale', label: t('Solo prodotti in saldo'), type: 'toggle',
      condition: { field: 'source', value: 'woocommerce' } },
    { key: 'woo_quick_add', label: t('Testo hover (Quick add)'), type: 'text',
      condition: { field: 'source', value: 'woocommerce' } },

    { type: 'separator', label: t('Prodotti'), condition: { field: 'source', value: 'custom' } },
    { key: 'items', label: t('Voci'), type: 'content-items',
      condition: { field: 'source', value: 'custom' },
      itemLabel: t('Prodotto'),
      defaults: { image: '', media_bg: { type: 'none' }, media_label: 'product', tag: '', category: 'Category', title: 'Product', price: '€0', shades: '', notes: '', roast: 0, filter_tags: '', link: '#', quick_add: 'Quick add' },
      itemFields: [
        { key: 'image', label: t('Immagine'), type: 'image' },
        { key: 'media_bg', label: t('Sfondo / media (ogni tipo)'), type: 'background', showParallax: false },
        { key: 'media_label', label: t('Etichetta placeholder'), type: 'text' },
        { key: 'tag', label: t('Tag / badge (es. New)'), type: 'text' },
        { key: 'category', label: t('Categoria'), type: 'text' },
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'price', label: t('Prezzo'), type: 'text' },
        { key: 'shades', label: t('Shade swatches (hex separati da virgola)'), type: 'text', placeholder: '#b5564f, #9a3b52, #c77a6a' },
        { key: 'notes', label: t('Note (sottotitolo, es. tasting notes)'), type: 'text' },
        { key: 'roast', label: t('Roast meter — livello (0 = nascosto)'), type: 'range', min: 0, max: 5, step: 1 },
        { key: 'filter_tags', label: t('Tag filtro (CSV — per la barra filtri)'), type: 'text', placeholder: 'Filter, Light roast' },
        { key: 'quick_add', label: t('Testo hover (Quick add)'), type: 'text' },
        { key: 'link', label: t('Link'), type: 'link' },
      ],
    },

    { type: 'separator', label: t('Link finale (opzionale)') },
    { key: 'footer_text', label: t('Testo link (es. View all 36 pieces)'), type: 'text' },
    { key: 'footer_url', label: t('URL link finale'), type: 'link' },

    { type: 'separator', label: t('Filtri categoria') },
    { key: 'show_filters', label: t('Mostra filtri categoria'), type: 'toggle',
      description: t('Chip "Tutti" + una per categoria (dalle voci). Filtrano la griglia.') },
    { key: 'filter_all_label', label: t('Etichetta "tutti"'), type: 'text',
      condition: { field: 'show_filters', value: true } },
    { key: 'filter_list', label: t('Chip filtro espliciti (CSV — vuoto = dalle categorie)'), type: 'text',
      condition: { field: 'show_filters', value: true }, placeholder: 'Filter, Espresso, Decaf' },

    { type: 'separator', label: t('Layout') },
    { key: 'columns', label: t('Colonne'), type: 'range', min: 1, max: 5, step: 1, responsive: true },
    { key: 'gap', label: t('Spazio tra card (px)'), type: 'range', min: 8, max: 40, step: 2 },
  ],

  styleFields: [
    { type: 'separator', label: t('Media') },
    { key: 'media_aspect', label: t('Proporzioni media'), type: 'select', options: [
      { value: '3/4', label: '3:4' },
      { value: '4/5', label: '4:5' },
      { value: '1/1', label: '1:1' },
      { value: '3/3.5', label: '3:3.5 (alto)' },
      { value: '16/11', label: '16:11' },
    ]},
    { key: 'media_bg', label: t('Sfondo media'), type: 'color' },
    { key: 'stripe_dark', label: t('Strisce scure (placeholder)'), type: 'toggle' },
    { key: 'hover_zoom', label: t('Zoom media in hover'), type: 'toggle' },

    { type: 'separator', label: t('Tag / Quick add') },
    { key: 'tag_bg', label: t('Tag — sfondo'), type: 'color' },
    { key: 'tag_color', label: t('Tag — testo'), type: 'color' },
    { key: 'quick_add_show', label: t('Mostra barra "Quick add" in hover'), type: 'toggle' },
    { key: 'quick_add_bg', label: t('Quick add — sfondo'), type: 'color' },
    { key: 'quick_add_color', label: t('Quick add — testo'), type: 'color' },

    { type: 'separator', label: t('Card (sfondo opzionale)') },
    { key: 'card_bg', label: t('Card — sfondo (vuoto = trasparente)'), type: 'color' },
    { key: 'card_border', label: t('Card — bordo'), type: 'color' },
    { key: 'card_radius', label: t('Card — raggio (px)'), type: 'border-radius' },
    { key: 'card_padding', label: t('Card — padding interno (px)'), type: 'range', min: 0, max: 32, step: 1 },

    { type: 'separator', label: t('Shade swatches') },
    { key: 'shade_size', label: t('Pallini shade — dimensione (px)'), type: 'range', min: 10, max: 24, step: 1 },
    { key: 'shade_border', label: t('Pallini shade — bordo interno'), type: 'color' },

    { type: 'separator', label: t('Note & Roast meter') },
    { key: 'notes_color', label: t('Note — colore'), type: 'color' },
    { key: 'notes_mono', label: t('Note in monospace'), type: 'toggle' },
    { key: 'roast_label', label: t('Roast meter — etichetta'), type: 'text' },
    { key: 'roast_on_color', label: t('Roast meter — pallino attivo (vuoto = accento)'), type: 'color' },
    { key: 'roast_off_color', label: t('Roast meter — pallino spento'), type: 'color' },

    { type: 'separator', label: t('Pulsante "Add" (footer)') },
    { key: 'add_button', label: t('Mostra pulsante Add accanto al prezzo'), type: 'toggle' },
    { key: 'add_label', label: t('Testo pulsante'), type: 'text', condition: { field: 'add_button', value: true } },
    { key: 'add_bg', label: t('Add — sfondo (vuoto = cream/testo tema)'), type: 'color', condition: { field: 'add_button', value: true } },
    { key: 'add_color', label: t('Add — testo'), type: 'color', condition: { field: 'add_button', value: true } },

    { type: 'separator', label: t('Testo') },
    { key: 'category_color', label: t('Categoria'), type: 'color' },
    { key: 'title_font', label: t('Font titolo'), type: 'font-family' },
    { key: 'title_size', label: t('Dim. titolo (px)'), type: 'range', min: 14, max: 32, step: 1 },
    { key: 'title_color', label: t('Titolo'), type: 'color' },
    { key: 'price_color', label: t('Prezzo (accento)'), type: 'color' },
    { key: 'footer_color', label: t('Link finale — colore'), type: 'color' },

    { type: 'separator', label: t('Filtri (chip)') },
    { key: 'filter_text_color', label: t('Chip — testo'), type: 'color' },
    { key: 'filter_border_color', label: t('Chip — bordo'), type: 'color' },
    { key: 'filter_active_bg', label: t('Chip attivo/hover — sfondo (vuoto = accento)'), type: 'color' },
    { key: 'filter_active_color', label: t('Chip attivo/hover — testo'), type: 'color' },

    { type: 'separator', label: t('Sfondo') },
    { key: 'bg', label: t('Sfondo completo'), type: 'background', showParallax: false },

    { type: 'separator', label: t('Ombra') },
    ...shadowField,

    ...borderFields(),
  ],
};
