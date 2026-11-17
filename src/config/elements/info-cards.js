import { t } from '@/i18n';
import { withHover } from './_shared';
import { ratioOptions, ADATTAMENTI } from './_imageFrame.js';

const R = (n) => ({ tl: n, tr: n, br: n, bl: n, linked: true });
const R24 = R(24), R18 = R(18);

/**
 * Info Cards — griglia parametrica di card riusabile.
 * CONTENUTO: solo testi, dati item-level (incluso media per item).
 * STILE: colori, dimensioni, sfondi globali, hover, layout griglia.
 */
export default {
  type: 'info-cards',
  name: t('Info Cards'),
  icon: 'dashicons-grid-view',
  category: 'layout',

  defaults: {
    typography_preset: '',
    container_bg:                       { type: 'solid', color: 'var(--olo-color-dark, #16263d)' },
    container_radius:                   { ...R24 },
    container_radius_hover:             { ...R24 },
    container_radius_hover_duration:    400,
    container_padding:                  12,
    container_gap:                      0,

    columns:   3,
    items_gap: 0,

    items: [
      { counter: '01', counter_label: 'Carta',         title: 'Zero',    title_accent: '',   title_accent_italic: true,  description: 'Niente <strong>carta di credito</strong> per scaricare e provare. Niente trial scaduto, niente sblocchi nascosti.', icon: '', footer_dot_color: 'var(--olo-color-accent, #f4a23b)', footer_text: '', link_url: '', media_image: '', media_label: 'SCREENSHOT · 01' },
      { counter: '02', counter_label: 'Registrazione', title: 'Niente',  title_accent: '',   title_accent_italic: true,  description: 'Nessuna <strong>registrazione obbligatoria</strong>. Scarichi, installi, lavori. L\'account lo crei solo se vuoi.', icon: '', footer_dot_color: 'var(--olo-color-accent, #f4a23b)', footer_text: '', link_url: '', media_image: '', media_label: 'SCREENSHOT · 02' },
      { counter: '03', counter_label: 'Pro',           title: '30',      title_accent: 'gg', title_accent_italic: false, description: '<strong>Soddisfatti o rimborsati</strong> su OLObuild Pro. 30 giorni pieni, nessuna domanda, zero ostacoli.', icon: '', footer_dot_color: 'var(--olo-color-accent, #f4a23b)', footer_text: '', link_url: '', media_image: '', media_label: 'SCREENSHOT · 03' },
    ],

    card_bg:                       { type: 'solid', color: 'var(--olo-color-dark, #16263d)' },
    card_color:                    'var(--olo-color-surface-alt, #f6f7f9)',
    card_accent_color:             'var(--olo-color-primary, #e1474f)',
    card_radius:                   { ...R18 },
    card_radius_hover:             { ...R18 },
    card_radius_hover_duration:    400,
    card_padding:                  40,
    card_border:                   '',

    show_icon:           false,
    show_counter:        true,
    show_counter_label:  true,
    show_arrow:          true,
    show_footer:         false,
    show_link_text:      false,
    show_divider:        false,
    show_media:          false,
    media_aspect_ratio:  '4/3',
    // 'cover' era cablato nei due renderer (PHP :193, InfoCardsTile.vue :13): il campo
    // nasce con lo stesso valore, così le card già pubblicate non si muovono.
    object_fit:          'cover',
    object_position:               'center center',
    media_radius:                  { ...R18 },
    media_radius_hover:            { ...R18 },
    media_radius_hover_duration:   400,
    media_position:                'top',

    title_font_family: 'serif',
    title_size:        72,
    title_weight:      '500',
    title_italic:      true,
    counter_size:      11,
    description_size:  15,
    footer_size:       10,
    // Font delle etichette (numero con etichetta, piè di pagina, segnaposto del media).
    // Vuoto = il monospazio di sistema che il PHP usava da sempre.
    mono_font_family:  '',

    card_hover_effect: 'none',
  },

  // Appena nata: le tre promesse di un'attività in una banda scura del tema — numero ed
  // etichetta, cifra grande in corsivo nel primario, descrizione e invito «Scopri di più»;
  // la card si solleva al passaggio. Su telefono le card vanno una sotto l'altra.
  partenza: {
    items: [
      { counter: '01', counter_label: t('Preventivo'), title: t('Gratis'), title_accent: '', title_accent_italic: true, description: t('Sopralluogo e preventivo <strong>senza impegno</strong>: sai subito quanto spendi, prima di decidere.'), icon: '', footer_dot_color: 'var(--olo-color-accent, #f4a23b)', footer_text: '', link_url: '', link_text: t('Scopri di più'), media_image: '', media_label: '' },
      { counter: '02', counter_label: t('Consegna'), title: '48', title_accent: t('ore'), title_accent_italic: true, description: t('Dalla conferma alla consegna in <strong>due giorni lavorativi</strong> per tutti gli ordini standard.'), icon: '', footer_dot_color: 'var(--olo-color-accent, #f4a23b)', footer_text: '', link_url: '', link_text: t('Scopri di più'), media_image: '', media_label: '' },
      { counter: '03', counter_label: t('Garanzia'), title: '2', title_accent: t('anni'), title_accent_italic: true, description: t('Ogni lavoro è <strong>coperto da garanzia</strong>: se qualcosa non va, interveniamo noi.'), icon: '', footer_dot_color: 'var(--olo-color-accent, #f4a23b)', footer_text: '', link_url: '', link_text: t('Scopri di più'), media_image: '', media_label: '' },
    ],
    items_gap: 12,
    card_bg: { type: 'solid', color: 'color-mix(in srgb, var(--olo-color-light, #f8f9fa) 5%, var(--olo-color-dark, #16263d))' },
    card_color: 'var(--olo-color-surface-alt, #f6f7f9)',
    card_accent_color: 'var(--olo-color-primary, #e1474f)',
    show_link_text: true,
    card_hover_effect: 'lift',
    columns_mobile_landscape: 1,
  },

  // ═══ CONTENUTO ════════════════════════════════════════════════
  fields: [
    { type: 'separator', label: t('Schede') },
    { key: 'items', label: t('Schede'), type: 'content-items', supportsDynamic: true,
      itemLabel: t('Scheda'), etichettaDa: 'title', miniaturaDa: 'media_image',
      // newItemDefaults (non `defaults`, che ContentItemsEditor non legge: la card aggiunta
      // nasceva vuota, { id }). Testi d'esempio e pallino dal token come le card di partenza.
      newItemDefaults: { counter: '00', counter_label: t('Etichetta'), title: t('Titolo'), title_accent: '', title_accent_italic: false, description: t('Descrizione della card.'), icon: '', footer_dot_color: 'var(--olo-color-accent, #f4a23b)', footer_text: '', link_url: '', link_text: t('Scopri di più'), media_image: '', media_label: '' },
      // 16 nov 2026: in italiano, e ogni campo compare solo se la sua parte è accesa in «Cosa mostrare»
      // (prima l'immagine, l'icona e il piè di scheda si compilavano anche spenti, senza vedere niente).
      itemFields: [
        { type: 'separator', label: t('Numero'), condizioneTile: { field: 'show_counter', operator: '!=', value: false } },
        { key: 'counter',             label: t('Numero'),               type: 'text', placeholder: '01', condizioneTile: { field: 'show_counter', operator: '!=', value: false } },
        { key: 'counter_label',       label: t('Etichetta del numero'), type: 'text', condizioneTile: { field: 'show_counter_label', operator: '!=', value: false } },
        { type: 'separator', label: t('Testi') },
        { key: 'title',               label: t('Titolo'),               type: 'text' },
        { key: 'title_accent',        label: t('Seguito del titolo'),   type: 'text' },
        { key: 'description',         label: t('Descrizione'),          type: 'editor', mode: 'block' },
        { type: 'separator', label: t('Immagine e icona') },
        { key: 'media_image',         label: t('Immagine'),             type: 'image', condizioneTile: { field: 'show_media', operator: '==', value: true } },
        { key: 'media_label',         label: t('Scritta senza immagine'), type: 'text', condizioneTile: { field: 'show_media', operator: '==', value: true } },
        { key: 'icon',                label: t('Icona'),                type: 'icon', condizioneTile: { field: 'show_icon', operator: '==', value: true } },
        { type: 'separator', label: t('Piè di scheda e link') },
        { key: 'footer_text',         label: t('Piè di scheda'),        type: 'text', condizioneTile: { field: 'show_footer', operator: '==', value: true } },
        { key: 'link_url',            label: t('Link'),                 type: 'link' },
        { key: 'link_text',           label: t('Testo del link'),       type: 'text', placeholder: t('Scopri di più'), condizioneTile: { field: 'show_link_text', operator: '==', value: true } },
      ],
    },

    { type: 'separator', label: t('Cosa mostrare') },
    { key: 'show_counter',       label: t('Mostra il numero'),                 type: 'toggle' },
    { key: 'show_counter_label', label: t('Mostra l\'etichetta del numero'),   type: 'toggle' },
    { key: 'show_media',         label: t('Mostra l\'immagine'),               type: 'toggle' },
    { key: 'show_icon',          label: t('Mostra l\'icona'),                  type: 'toggle' },
    { key: 'show_arrow',         label: t('Mostra la freccia in alto'),        type: 'toggle' },
    { key: 'show_divider',       label: t('Mostra il filo sotto la descrizione'), type: 'toggle' },
    { key: 'show_footer',        label: t('Mostra il piè di scheda'),          type: 'toggle' },
    { key: 'show_link_text',     label: t('Mostra l\'invito «Scopri di più»'), type: 'toggle' },
  ],

  // ═══ STILE ════════════════════════════════════════════════════
  // Zone standard (16 nov 2026), una per parte: Aspetto · Titolo · Numero · Testo · Icona · Immagine · Schede · Forma ·
  // Fascia · Disposizione. Prima «Layout griglia», «Sfondo container», «Card stile», «Dimensioni secondarie», «Hover card».
  styleFields: [
    { type: 'separator', label: t('Aspetto') },
    { key: 'card_bg',           label: t('Sfondo scheda'),          type: 'background', showParallax: false },
    { key: 'card_color',        label: t('Colore testo'),           type: 'color' },
    { key: 'card_accent_color', label: t('Colore accento'), type: 'color',
      description: t('Colora l\'invito testuale, il bagliore al passaggio e il titolo, se al titolo non dai un colore suo.') },
    { key: 'card_hover_effect', label: t('Effetto al passaggio'), type: 'select', options: [
      { value: 'none',  label: t('Nessuno') },
      { value: 'lift',  label: t('Si solleva') },
      { value: 'scale', label: t('Si ingrandisce') },
      { value: 'glow',  label: t('Bordo che si accende') },
      { value: 'tilt',  label: t('Si inclina in 3D') },
    ]},

    { type: 'separator', label: t('Titolo') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    // Colore del titolo: vuoto = il Colore accento, come prima che il controllo esistesse.
    { type: 'typography', label: t('Titolo'), responsiveKeys: [], keys: { size: 'title_size', weight: 'title_weight', italic: 'title_italic', family: 'title_font_family', color: 'title_color' }, sizeMin: 18, sizeMax: 160, sizeStep: 2 },

    // Numero: forma, colore e sfondo del cerchio li leggeva già il PHP senza un controllo.
    // Nel cerchio il numero ha una misura sua (e niente etichetta): la dimensione del
    // controllo tipografia non agirebbe, quindi lì resta solo il colore.
    { type: 'separator', label: t('Numero'), condition: { field: 'show_counter', op: 'neq', value: false } },
    // Le card salvate senza la chiave rendono «Testo con etichetta» (il PHP ripiega su
    // 'plain'): valoriStorici le mostra così invece di una tendina senza voce scelta.
    { key: 'counter_shape', label: t('Forma'), type: 'select', options: [
      { value: 'plain',  label: t('Testo con etichetta') },
      { value: 'circle', label: t('Cerchio') },
    ], valoriStorici: { '': 'plain' } },
    // Il font del numero (chiave mono_font_family) è lo stesso del piè di pagina: le due righe
    // lo mostrano entrambe, così si trova da qualunque testo si parta.
    { type: 'typography', label: t('Numero'), responsiveKeys: [], keys: { size: 'counter_size', color: 'counter_color', family: 'mono_font_family' }, sizeMin: 9, sizeMax: 22,
      description: t('Il font vale anche per il piè di scheda e per la scritta senza immagine. Vuoto: il monospazio di sistema.'),
      condition: { field: 'counter_shape', op: 'neq', value: 'circle' } },
    { key: 'counter_color', label: t('Colore numero'), type: 'color',
      condition: { field: 'counter_shape', op: 'eq', value: 'circle' } },
    { key: 'counter_bg', label: t('Sfondo cerchio'), type: 'color',
      condition: { field: 'counter_shape', op: 'eq', value: 'circle' } },

    { type: 'separator', label: t('Testo') },
    { type: 'typography', label: t('Descrizione'), responsiveKeys: [], keys: { size: 'description_size' }, sizeMin: 11, sizeMax: 22 },
    { type: 'typography', label: t('Piè di scheda'), responsiveKeys: [], keys: { size: 'footer_size', family: 'mono_font_family' }, sizeMin: 9, sizeMax: 16,
      condition: { field: 'show_footer', op: '=', value: true },
      description: t('Il font vale anche per il numero con l\'etichetta e per la scritta senza immagine. Vuoto: il monospazio di sistema.') },

    // Icona: i due colori li leggeva già il PHP, ma nessun controllo li offriva.
    { type: 'separator', label: t('Icona'), condition: { field: 'show_icon', op: '=', value: true } },
    { key: 'icon_color',    label: t('Colore icona'), type: 'color',
      description: t('Vuoto = il colore del testo della card.') },
    { key: 'icon_bg_color', label: t('Sfondo icona'), type: 'color',
      description: t('Con un colore l\'icona sta in un riquadro arrotondato di quel colore; vuoto = icona libera.') },

    { type: 'separator', label: t('Immagine'), condition: { field: 'show_media', op: '=', value: true } },
    { key: 'media_position', label: t('Posizione'), type: 'select', options: [
      { value: 'top',    label: t('In alto') },
      { value: 'bottom', label: t('In fondo') },
    ], valoriStorici: { '': 'top' } },
    // Elenco canonico: i 5 rapporti storici ('16/9','4/3','3/2','1/1','21/9') ci stanno
    // tutti dentro, quindi niente `extra`. NIENTE voce automatica: il riquadro media non
    // ha un'altezza propria (solo `aspect-ratio`), senza ritaglio collasserebbe.
    { key: 'media_aspect_ratio', label: t('Proporzioni'), type: 'select',
      options: ratioOptions({ auto: false }) },
    { key: 'object_fit', label: t('Adattamento'), type: 'select', options: ADATTAMENTI },
    // Il punto focale si nasconde SOLO con 'fill' (con 'contain' decide da che parte l'immagine
    // si appoggia); una card salvata prima dell'adattamento non ha la chiave `object_fit`.
    { key: 'object_position', label: t('Punto focale'), type: 'object-position', reveal: true,
      contextKeys: { ratio: 'media_aspect_ratio', fit: 'object_fit' },
      condition: { field: 'object_fit', op: 'neq', value: 'fill' } },
    withHover({ key: 'media_radius', label: t('Raggio immagine'), type: 'border-radius' }, { hoverKey: 'media_radius_hover', hoverDurationKey: 'media_radius_hover_duration' }),

    { type: 'separator', label: t('Schede') },
    { key: 'items', type: 'content-items', label: t('Schede'), itemLabel: t('Scheda'), etichettaDa: 'title', miniaturaDa: 'media_image', itemFields: [
        { key: 'title_accent_italic', label: t('Seguito del titolo in corsivo'), type: 'toggle' },
        { key: 'footer_dot_color',    label: t('Colore pallino'), type: 'color', condizioneTile: { field: 'show_footer', operator: '==', value: true } },
    ] },

    { type: 'separator', label: t('Forma') },
    { key: 'card_padding',      label: t('Padding scheda'),   type: 'spacing' },
    { key: 'card_border',       label: t('Bordo scheda'), type: 'border', legacyWidth: 1 },
    withHover({ key: 'card_radius', label: t('Raggio scheda'), type: 'border-radius' }, { hoverKey: 'card_radius_hover', hoverDurationKey: 'card_radius_hover_duration' }),

    { type: 'separator', label: t('Fascia') },
    { key: 'container_bg',      label: t('Sfondo fascia'),    type: 'background', showParallax: false },
    { key: 'container_padding', label: t('Padding fascia'),   type: 'spacing', min: 0, max: 80 },
    withHover({ key: 'container_radius', label: t('Raggio fascia'), type: 'border-radius' }, { hoverKey: 'container_radius_hover', hoverDurationKey: 'container_radius_hover_duration' }),

    { type: 'separator', label: t('Disposizione') },
    { key: 'columns',   label: t('Colonne'),    type: 'range', min: 1, max: 6, step: 1, responsive: true,
      description: t('Sul telefono, se non scegli un numero di colonne per il telefono, le card vanno una sotto l\'altra.') },
    { key: 'items_gap', label: t('Gap schede'), type: 'range', unit: 'px', min: 0, max: 60, step: 2, responsive: true },
    { key: 'container_gap',     label: t('Gap attorno alle schede'), type: 'range', unit: 'px', min: 0, max: 40, step: 1,
      description: t('Spazio attorno a ogni scheda, anche verso il bordo della fascia.') },
  ],
};
