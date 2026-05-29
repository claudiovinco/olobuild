
import { borderFields, borderDefault, borderHoverDefault, borderEffectDefaults } from './_shared.js';
import { t } from '@/i18n';

/**
 * Tile PDF Viewer — split CONTENUTO/STILE (regola universale Olobuild).
 *   fields[]      → pdf_url (sorgente), mode (visualizzazione), start_page, initial_zoom,
 *                   theme, toggle controlli toolbar (show_toolbar + show_*),
 *                   toggle barra inferiore (show_bottombar + show_bottombar_*),
 *                   navigazione (nav_click, nav_swipe, nav_keyboard)
 *   styleFields[] → typography_preset, viewer_height (px), bg_color, borderFields
 */
export default {
  type: 'pdfviewer',
  name: t('PDF Viewer'),
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
    show_download: true,
    show_print: true,
    show_search: false,
    show_thumbnails: false,
    show_bottombar: true,
    show_bottombar_pages: true,
    show_bottombar_zoom: false,
    show_bottombar_fullscreen: false,
    nav_click: true,
    nav_swipe: true,
    nav_keyboard: true,
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
  ],

  // ─── STILE ─────────────────────────────────────────────────
  styleFields: [
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },

    { type: 'separator', label: t('Dimensioni') },
    { key: 'viewer_height', label: t('Altezza viewer (px)'), type: 'range', min: 300, max: 1200, step: 10 },

    { type: 'separator', label: t('Colori') },
    { key: 'bg_color', label: t('Colore sfondo'), type: 'color' },

    ...borderFields(),
  ],
};
