import { textEffectsFields, textEffectsDefaults, borderFields, borderDefault, borderHoverDefault, borderEffectDefaults, focalField } from './_shared';
import { ratioOptions, ADATTAMENTI } from './_imageFrame.js';
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
        { key: 'x', label: t('Posizione X'), type: 'range', min: 0, max: 100, step: 0.5 },
        { key: 'y', label: t('Posizione Y'), type: 'range', min: 0, max: 100, step: 0.5 },
        { key: 'color', label: t('Colore'), type: 'color' },
        { key: 'icon', label: t('Icona'), type: 'icon' },
        { key: 'title', label: t('Titolo'), type: 'text' },
        { key: 'description', label: t('Descrizione'), type: 'textarea' },
        { key: 'image_url', label: t('Immagine'), type: 'media' },
        // Cornice dell'immagine del popover. Qui sta per HOTSPOT e non per tile
        // perché i popover si aprono uno alla volta: non c'è una griglia da
        // tenere allineata. Con «Auto» non si emette nulla e l'immagine resta ad
        // altezza naturale, esattamente come prima di questo controllo.
        { key: 'image_ratio', label: t('Proporzioni'), type: 'select', options: ratioOptions(),
          condition: { field: 'image_url', op: 'notEmpty' } },
        // ⚠️ La condizione è POSITIVA — «il rapporto c'è E non è Auto» — e non la
        // forma breve `neq 'auto'`. Gli item di un repeater NON ricevono mai i
        // default della tile (normalizeNodes() fonde solo quelli di livello tile,
        // e `newItemDefaults` vale per gli hotspot creati da qui in avanti):
        // su un hotspot salvato prima di questo sprint `image_ratio` è undefined,
        // e `neq 'auto'` su undefined risulta VERO. I due controlli comparirebbero
        // senza poter fare nulla, perché il runtime li legge solo dentro
        // `if (hs.image_ratio)` (assets/js/olo-pdfpro.js) e il PHP manda '' quando
        // il rapporto manca. Basta scegliere una proporzione per vederli apparire.
        { key: 'image_fit', label: t('Adattamento'), type: 'select', options: ADATTAMENTI,
          condition: [{ field: 'image_url', op: 'notEmpty' }, { field: 'image_ratio', op: 'notEmpty' },
            { field: 'image_ratio', op: 'neq', value: 'auto' }] },
        focalField('image_url', { label: t('Punto focale'), ratio: 'image_ratio', fit: 'image_fit',
          // Ultima clausola: con «Deforma per riempire» l'immagine copre tutta la
          // cornice e il CSS ignora object-position — il focale non avrebbe nulla
          // da spostare. Su un hotspot vecchio `image_fit` è undefined e `neq 'fill'`
          // lo lascia visibile: il caso «chiave assente» cade dalla parte giusta.
          condition: [{ field: 'image_url', op: 'notEmpty' }, { field: 'image_ratio', op: 'notEmpty' },
            { field: 'image_ratio', op: 'neq', value: 'auto' }, { field: 'image_fit', op: 'neq', value: 'fill' }] }),
        { key: 'video_url', label: t('Video'), type: 'media' },
        { key: 'btn_label', label: t('Testo pulsante'), type: 'text' },
        { key: 'btn_url', label: t('URL pulsante'), type: 'link' },
        { key: 'btn_target', label: t('Apri in nuova scheda'), type: 'toggle' },
        // Tipografia pulsante — controllo unico standard (stesse chiavi salvate)
        { type: 'typography', label: t('Tipografia pulsante'),
          keys: {
            size:          'btn_font_size',
            weight:        'btn_font_weight',
            transform:     'btn_text_transform',
            letterSpacing: 'btn_letter_spacing',
            color:         'btn_color',
          },
          sizeMin: 10, sizeMax: 24, sizeStep: 1,
        },
        // Colori pulsante
        { key: 'btn_bg', label: t('Sfondo pulsante'), type: 'color' },
        // Spaziatura interna
        { key: 'btn_padding', label: t('Padding pulsante'), type: 'spacing', min: 0, max: 60,
          legacyKeys: { y: 'btn_padding_v', x: 'btn_padding_h' } },
        // Bordo
        { key: 'btn_radius', label: t('Raggio'), type: 'border-radius' },
        { key: 'btn_border', label: t('Bordo pulsante'), type: 'border',
          legacyKeys: { width: 'btn_border_width', style: 'btn_border_style', color: 'btn_border_color' } },
        // Layout
        { key: 'btn_align', label: t('Allineamento'), type: 'select', options: [
          { value: '', label: t('Sinistra') },
          { value: 'center', label: t('Centro') },
          { value: 'right', label: t('Destra') },
          { value: 'stretch', label: t('Larghezza piena') },
        ]},
      ],
      newItemDefaults: { page: 1, x: 50, y: 50, title: t('Nuovo hotspot'), description: '',
        color: '', icon: '', image_url: '', image_ratio: 'auto', image_fit: 'cover',
        image_url_object_position: 'center center', video_url: '', btn_label: '', btn_url: '', btn_target: false,
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
    { key: 'viewer_height', label: t('Altezza viewer'), type: 'range', min: 300, max: 1200, step: 10 },

    { type: 'separator', label: t('Colori') },
    { key: 'bg_color', label: t('Colore sfondo'), type: 'color' },

    { type: 'separator', label: t('Hotspot — stile') },
    { key: 'hotspot_color', label: t('Colore hotspot'), type: 'color' },
    { key: 'hotspot_size', label: t('Dimensione hotspot'), type: 'range', min: 8, max: 30, step: 1 },

    ...borderFields(),
  ],
};
