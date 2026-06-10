import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared';
import { t } from '@/i18n';

/**
 * Tile PDF Pro — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → pdf_url (sorgente), mode (visualizzazione), start_page, initial_zoom,
 *                   theme, toggle controlli toolbar (show_toolbar + show_*),
 *                   toggle barra inferiore (show_bottombar + show_bottombar_*),
 *                   navigazione (nav_click, nav_swipe, nav_keyboard),
 *                   hotspots (content-items con tutti i field di stile interni preservati),
 *                   hotspot_pulse (behavior)
 *   styleFields[] → typography_preset, textEffectsFields, viewer_height (px), bg_color,
 *                   hotspot_color, hotspot_size, borderFields
 */
export default {
  type: 'pdfpro',
  name: t('PDF Pro'),
  icon: 'dashicons-media-document',
  category: 'media',
  defaults: {
    typography_preset: '',
    pdf_url: '',
    mode: 'flipbook',
    viewer_height: '600',
    start_page: '1',
    initial_zoom: 'fit-width',
    theme: 'light',
    bg_color: '',
    show_toolbar: true,
    show_page_nav: true,
    show_zoom: true,
    show_fullscreen: true,
    show_bottombar: true,
    show_bottombar_pages: true,
    show_bottombar_zoom: false,
    show_bottombar_fullscreen: false,
    nav_click: true,
    nav_swipe: true,
    nav_keyboard: true,
    show_download: true,
    show_print: true,
    show_search: false,
    show_thumbnails: false,
    hotspots: [],
    hotspot_color: '',
    hotspot_size: '14',
    hotspot_pulse: true,
    ...textEffectsDefaults,
    text_effect_target: 'title',
    border: { ...borderDefault },
    border_hover: { ...borderHoverDefault },
    border_hover_duration: 300,
    ...borderEffectDefaults,
  },

  // ─── CONTENUTO ─────────────────────────────────────────────
  fields: [
    { type: 'separator', label: t('Sorgente') },
    { key: 'pdf_url', label: t('File PDF'), type: 'media' },

    { type: 'separator', label: t('Visualizzazione') },
    { key: 'mode', label: t('Modalità'), type: 'select', options: [
      { value: 'flipbook', label: t('Flipbook (sfoglia)') },
      { value: 'single', label: t('Pagina singola') },
      { value: 'double', label: t('Doppia pagina') },
      { value: 'scroll', label: t('Scroll continuo') },
    ]},
    { key: 'start_page', label: t('Pagina iniziale'), type: 'range', min: 1, max: 500, step: 1 },
    { key: 'initial_zoom', label: t('Zoom iniziale'), type: 'select', options: [
      { value: 'fit-width', label: t('Adatta alla larghezza') },
      { value: 'fit-page', label: t('Adatta alla pagina') },
      { value: '100', label: '100%' },
      { value: '75', label: '75%' },
      { value: '50', label: '50%' },
    ]},
    { key: 'theme', label: t('Tema'), type: 'select', options: [
      { value: 'light', label: t('Chiaro') },
      { value: 'dark', label: t('Scuro') },
    ]},

    { type: 'separator', label: t('Controlli toolbar') },
    { key: 'show_toolbar', label: t('Mostra toolbar'), type: 'toggle' },
    { key: 'show_page_nav', label: t('Navigazione pagine'), type: 'toggle',
      condition: { field: 'show_toolbar', value: true } },
    { key: 'show_zoom', label: t('Zoom'), type: 'toggle',
      condition: { field: 'show_toolbar', value: true } },
    { key: 'show_fullscreen', label: t('Schermo intero'), type: 'toggle',
      condition: { field: 'show_toolbar', value: true } },
    { key: 'show_download', label: t('Download'), type: 'toggle',
      condition: { field: 'show_toolbar', value: true } },
    { key: 'show_print', label: t('Stampa'), type: 'toggle',
      condition: { field: 'show_toolbar', value: true } },
    { key: 'show_search', label: t('Ricerca nel PDF'), type: 'toggle',
      condition: { field: 'show_toolbar', value: true } },
    { key: 'show_thumbnails', label: t('Miniature'), type: 'toggle',
      condition: { field: 'show_toolbar', value: true } },

    { type: 'separator', label: t('Barra inferiore') },
    { key: 'show_bottombar', label: t('Mostra barra inferiore'), type: 'toggle' },
    { key: 'show_bottombar_pages', label: t('Slider pagine'), type: 'toggle',
      condition: { field: 'show_bottombar', value: true } },
    { key: 'show_bottombar_zoom', label: t('Zoom (barra inferiore)'), type: 'toggle',
      condition: { field: 'show_bottombar', value: true } },
    { key: 'show_bottombar_fullscreen', label: t('Schermo intero (barra inferiore)'), type: 'toggle',
      condition: { field: 'show_bottombar', value: true } },

    { type: 'separator', label: t('Navigazione pagine') },
    { key: 'nav_click', label: t('Click su pagina (volta pagina)'), type: 'toggle' },
    { key: 'nav_swipe', label: t('Swipe touch (mobile)'), type: 'toggle' },
    { key: 'nav_keyboard', label: t('Frecce tastiera'), type: 'toggle' },

    { type: 'separator', label: t('Hotspot interattivi') },
    { key: 'hotspots', label: t('Hotspot'), type: 'content-items',
      itemFields: [
        { key: 'page', label: t('Pagina'), type: 'number', min: 1 },
        { key: '_placer', label: t('Posiziona su PDF'), type: 'hotspot-position' },
        { key: 'x', label: t('Posizione X (%)'), type: 'range', min: 0, max: 100, step: 0.5 },
        { key: 'y', label: t('Posizione Y (%)'), type: 'range', min: 0, max: 100, step: 0.5 },
        { key: 'color', label: t('Colore'), type: 'color' },
        { key: 'icon', label: t('Icona'), type: 'icon' },
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'description', label: t('Descrizione'), type: 'textarea' },
        { key: 'image_url', label: t('Immagine'), type: 'media' },
        { key: 'video_url', label: t('Video'), type: 'media' },
        { key: 'btn_label', label: t('Testo pulsante'), type: 'text' },
        { key: 'btn_url', label: t('URL pulsante'), type: 'link' },
        { key: 'btn_target', label: t('Apri in nuova scheda'), type: 'toggle' },
        // Tipografia pulsante
        { key: 'btn_font_size', label: t('Dim. testo (px)'), type: 'range', min: 10, max: 24, step: 1 },
        { key: 'btn_font_weight', label: t('Peso testo'), type: 'select', options: [
          { value: '', label: t('Medio (default)') },
          { value: '400', label: t('Normale') },
          { value: '600', label: t('Semi-grassetto') },
          { value: '700', label: t('Grassetto') },
          { value: '800', label: t('Extra-grassetto') },
        ]},
        { key: 'btn_letter_spacing', label: t('Spaziatura lettere (px)'), type: 'range', min: 0, max: 5, step: 0.5 },
        { key: 'btn_text_transform', label: t('Trasformazione testo'), type: 'select', options: [
          { value: '', label: t('Nessuna') },
          { value: 'uppercase', label: t('MAIUSCOLO') },
          { value: 'lowercase', label: t('minuscolo') },
          { value: 'capitalize', label: t('Prima Maiuscola') },
        ]},
        // Colori pulsante
        { key: 'btn_bg', label: t('Sfondo pulsante'), type: 'color' },
        { key: 'btn_color', label: t('Colore testo'), type: 'color' },
        // Spaziatura interna
        { key: 'btn_padding_v', label: t('Padding verticale (px)'), type: 'range', min: 0, max: 30, step: 1 },
        { key: 'btn_padding_h', label: t('Padding orizzontale (px)'), type: 'range', min: 0, max: 50, step: 1 },
        // Bordo
        { key: 'btn_radius', label: t('Raggio angoli (px)'), type: 'number', min: 0 },
        { key: 'btn_border_width', label: t('Spessore bordo (px)'), type: 'range', min: 0, max: 5, step: 1 },
        { key: 'btn_border_color', label: t('Colore bordo'), type: 'color' },
        { key: 'btn_border_style', label: t('Stile bordo'), type: 'select', options: [
          { value: 'solid', label: t('Solido') },
          { value: 'dashed', label: t('Tratteggiato') },
          { value: 'dotted', label: t('Punteggiato') },
          { value: 'double', label: t('Doppio') },
        ]},
        // Layout
        { key: 'btn_align', label: t('Allineamento'), type: 'select', options: [
          { value: '', label: t('Sinistra') },
          { value: 'center', label: t('Centro') },
          { value: 'right', label: t('Destra') },
          { value: 'stretch', label: t('Larghezza piena') },
        ]},
      ],
      newItemDefaults: { page: 1, x: 50, y: 50, title: t('Nuovo hotspot'), description: '',
        color: '', icon: '', image_url: '', video_url: '', btn_label: '', btn_url: '', btn_target: false,
        btn_font_size: '', btn_font_weight: '', btn_letter_spacing: '', btn_text_transform: '',
        btn_bg: '', btn_color: '', btn_padding_v: 0, btn_padding_h: 0,
        btn_radius: '', btn_border_width: '', btn_border_color: '', btn_border_style: 'solid',
        btn_align: '' },
      itemLabel: 'Hotspot',
    },
    { key: 'hotspot_pulse', label: t('Animazione pulse'), type: 'toggle' },
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    ...textEffectsFields([
      { value: 'title', label: t('Solo Titolo') },
      { value: 'description', label: t('Solo Descrizione') },
      { value: 'all', label: t('Tutti gli elementi testuali') },
    ]),

    { type: 'separator', label: t('Dimensioni') },
    { key: 'viewer_height', label: t('Altezza viewer (px)'), type: 'range', min: 300, max: 1200, step: 10 },

    { type: 'separator', label: t('Colori') },
    { key: 'bg_color', label: t('Colore sfondo'), type: 'color' },

    { type: 'separator', label: t('Hotspot — stile') },
    { key: 'hotspot_color', label: t('Colore hotspot'), type: 'color' },
    { key: 'hotspot_size', label: t('Dimensione hotspot (px)'), type: 'range', min: 8, max: 30, step: 1 },

    ...borderFields(),
  ],
};
