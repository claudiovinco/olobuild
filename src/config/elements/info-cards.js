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

    card_hover_effect: 'none',
  },

  // ═══ CONTENUTO ════════════════════════════════════════════════
  fields: [
    { type: 'separator', label: t('Card items') },
    { key: 'items', label: t('Cards'), type: 'content-items',
      itemLabel: t('Card'),
      defaults: { counter: '00', counter_label: 'Label', title: 'Titolo', title_accent: '', title_accent_italic: false, description: 'Descrizione…', icon: '', footer_dot_color: '#10b981', footer_text: '', link_url: '', link_text: '', media_image: '', media_label: 'SCREENSHOT' },
      itemFields: [
        { type: 'separator', label: t('Media') },
        { key: 'media_image',         label: t('Immagine media'),          type: 'image' },
        { key: 'media_label',         label: t('Label placeholder media'), type: 'text' },
        { key: 'icon',                label: t('Icona'),                   type: 'icon' },
        { type: 'separator', label: t('Counter') },
        { key: 'counter',             label: t('Counter (es. 01)'),        type: 'text' },
        { key: 'counter_label',       label: t('Counter label'),           type: 'text' },
        { type: 'separator', label: t('Testi') },
        { key: 'title',               label: t('Titolo'),                  type: 'text' },
        { key: 'title_accent',        label: t('Suffisso titolo'),         type: 'text' },
        { key: 'title_accent_italic', label: t('Suffisso italico'),        type: 'toggle' },
        { key: 'description',         label: t('Descrizione'),             type: 'editor', mode: 'block' },
        { type: 'separator', label: t('Footer & Link') },
        { key: 'footer_text',         label: t('Footer testo'),            type: 'text' },
        { key: 'footer_dot_color',    label: t('Footer pallino'),          type: 'color' },
        { key: 'link_url',            label: t('Link (opzionale)'),        type: 'link' },
        { key: 'link_text',           label: t('Testo CTA (es. Learn more)'), type: 'text' },
      ],
    },

    { type: 'separator', label: t('Visibilità elementi') },
    { key: 'show_media',         label: t('Mostra media in alto'),                type: 'toggle' },
    { key: 'show_icon',          label: t('Mostra icona'),                        type: 'toggle' },
    { key: 'show_counter',       label: t('Mostra counter'),                      type: 'toggle' },
    { key: 'show_counter_label', label: t('Mostra counter label'),                type: 'toggle' },
    { key: 'show_arrow',         label: t('Mostra freccia in alto a destra'),    type: 'toggle' },
    { key: 'show_footer',        label: t('Mostra footer (pallino + tag)'),      type: 'toggle' },
    { key: 'show_link_text',     label: t('Mostra CTA testuale (Learn more →)'), type: 'toggle' },
    { key: 'show_divider',       label: t('Mostra separatore sotto descrizione'), type: 'toggle' },
  ],

  // ═══ STILE ════════════════════════════════════════════════════
  styleFields: [
    { type: 'separator', label: t('Layout griglia') },
    { key: 'columns',   label: t('Numero colonne'),    type: 'range', min: 1, max: 6, step: 1, responsive: true },
    { key: 'items_gap', label: t('Gap card'), type: 'range', min: 0, max: 60, step: 2, responsive: true },

    { type: 'separator', label: t('Sfondo container') },
    { key: 'container_bg',      label: t('Sfondo'),               type: 'background', showParallax: false },
    { key: 'container_padding', label: t('Padding'), type: 'spacing', min: 0, max: 80 },
    { key: 'container_gap',     label: t('Gap container-card'), type: 'range', min: 0, max: 40, step: 1 },
    withHover({ key: 'container_radius', label: t('Raggio container'), type: 'border-radius' }, { hoverKey: 'container_radius_hover', hoverDurationKey: 'container_radius_hover_duration' }),

    { type: 'separator', label: t('Card stile') },
    { key: 'card_bg',           label: t('Sfondo card'),            type: 'background', showParallax: false },
    { key: 'card_color',        label: t('Colore testo'),           type: 'color' },
    { key: 'card_accent_color', label: t('Colore accent (titolo)'), type: 'color' },
    { key: 'card_padding',      label: t('Padding interno card'),   type: 'spacing' },
    { key: 'card_border',       label: t('Bordo (vuoto = nessuno)'), type: 'border', legacyWidth: 1 },
    withHover({ key: 'card_radius', label: t('Raggio card'), type: 'border-radius' }, { hoverKey: 'card_radius_hover', hoverDurationKey: 'card_radius_hover_duration' }),

    { type: 'separator', label: t('Media (immagine card)') },
    // Elenco canonico: i 5 rapporti storici ('16/9','4/3','3/2','1/1','21/9') ci stanno
    // tutti dentro, quindi niente `extra`. NIENTE voce automatica: il riquadro media non
    // ha un'altezza propria (solo `aspect-ratio`, PHP :191 e mediaStyle nel .vue), senza
    // ritaglio collasserebbe. La whitelist PHP $aspect_allow e' stata allargata di pari passo.
    { key: 'media_aspect_ratio', label: t('Proporzioni'), type: 'select',
      options: ratioOptions({ auto: false }),
      condition: { field: 'show_media', op: '=', value: true } },
    { key: 'object_fit', label: t('Adattamento'), type: 'select', options: ADATTAMENTI,
      condition: { field: 'show_media', op: '=', value: true } },
    // Il punto focale sposta qualcosa solo quando c'e' un ritaglio, e si nasconde
    // SOLO con 'fill': con 'contain' lavora eccome (decide da che parte l'immagine si
    // appoggia nelle bande vuote, e i due renderer lo emettono), e una card salvata
    // prima che l'adattamento esistesse non ha affatto la chiave `object_fit` —
    // l'inspector valuta le condizioni sui settings GREZZI, senza fondere i default,
    // quindi con un elenco `in` il valore assente non combacerebbe con niente e il
    // controllo sparirebbe per sempre.
    // L'AND si scrive come ARRAY, l'unica forma che evaluateCondition() valuta.
    { key: 'object_position', label: t('Punto focale'), type: 'object-position', reveal: true,
      contextKeys: { ratio: 'media_aspect_ratio', fit: 'object_fit' },
      condition: [
        { field: 'show_media', op: '=', value: true },
        { field: 'object_fit', op: 'neq', value: 'fill' },
      ] },
    withHover({ key: 'media_radius', label: t('Raggio media'), type: 'border-radius' }, { hoverKey: 'media_radius_hover', hoverDurationKey: 'media_radius_hover_duration' }),

    { type: 'separator', label: t('Tipografia titolo') },
    { key: 'typography_preset', label: t('Stile tipografico'), type: 'select', optionsSource: 'globalTypography' },
    { type: 'typography', label: t('Titolo'), responsiveKeys: [], keys: { size: 'title_size', weight: 'title_weight', italic: 'title_italic', family: 'title_font_family' }, sizeMin: 18, sizeMax: 160, sizeStep: 2 },

    { type: 'separator', label: t('Dimensioni secondarie') },
    { type: 'typography', label: t('Numero'), responsiveKeys: [], keys: { size: 'counter_size' }, sizeMin: 9, sizeMax: 22 },
    { type: 'typography', label: t('Descrizione'), responsiveKeys: [], keys: { size: 'description_size' }, sizeMin: 11, sizeMax: 22 },
    { type: 'typography', label: t('Piè di pagina'), responsiveKeys: [], keys: { size: 'footer_size' }, sizeMin: 9, sizeMax: 16 },

    { type: 'separator', label: t('Hover card') },
    { key: 'card_hover_effect', label: t('Effetto hover'), type: 'select', options: [
      { value: 'none',  label: t('Nessuno') },
      { value: 'lift',  label: t('Sollevamento') },
      { value: 'scale', label: t('Scala') },
      { value: 'glow',  label: t('Glow bordo accent') },
      { value: 'tilt',  label: t('Inclinazione 3D') },
    ]},
  ],
};
