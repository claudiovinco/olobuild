import { t } from '@/i18n';
import { withHover } from './_shared';

const R = (n) => ({ tl: n, tr: n, br: n, bl: n, linked: true });

/**
 * Step Timeline — timeline orizzontale con N step.
 * Ogni step: numero grande, tag pallino+testo, mockup placeholder/immagine,
 * pre-title, titolo con accent italic, descrizione, footer con metric.
 * Connessi da timeline orizzontale tratteggiata con pallini.
 */
export default {
  type: 'step-timeline',
  name: t('Step Timeline'),
  icon: 'dashicons-clock',
  category: 'layout',

  defaults: {
    items: [
      {
        counter: '01',
        tag_text: 'PRONTO IN 30"',
        tag_dot_color: '#b3261e',
        media_label: 'TERMINAL · INSTALL',
        media_type: 'terminal',
        media_content: '# installazione\n$ wp plugin install olobuild --activate\n✓ Plugin installato\n✓ 187 tile registrati\n# pronto\n$',
        media_image: '',
        media_bg: '#0f172a',
        media_color: '#10b981',
        pre_title: 'INSTALLA',
        title: 'Scarichi',
        title_accent: 'OLObuild',
        title_accent_italic: true,
        title_after: 'direttamente da WordPress.org.',
        title_after_italic: false,
        description: 'Zero configurazione obbligatoria. Funziona con qualunque tema WP. Anche dal nostro sito al lancio — un click e sei dentro.',
        footer_value: '30"',
        footer_label: 'TEMPO MEDIO',
        separator_text: '→ POI',
      },
      {
        counter: '02',
        tag_text: 'DRAG & DROP',
        tag_dot_color: '#b3261e',
        media_label: 'OLOBUILD · EDITOR LIVE',
        media_type: 'placeholder',
        media_content: '',
        media_image: '',
        media_bg: '#f5efe7',
        media_color: '#b3261e',
        pre_title: 'COSTRUISCI',
        title: 'Trascini i tile, scegli i colori,',
        title_accent: 'doppio click',
        title_accent_italic: true,
        title_after: 'per editare.',
        title_after_italic: false,
        description: 'Anteprima fedele in tempo reale. Mobile, tablet, desktop con un click. Niente shortcode, niente "preview separato".',
        footer_value: '≈ 1h',
        footer_label: 'PRIMA PAGINA',
        separator_text: '→ VAI LIVE',
      },
      {
        counter: '03',
        tag_text: 'ONLINE',
        tag_dot_color: '#10b981',
        media_label: 'TUOSITO.COM · LIVE',
        media_type: 'placeholder',
        media_content: '',
        media_image: '',
        media_bg: '#f5efe7',
        media_color: '#10b981',
        pre_title: 'PUBBLICHI & SCALI',
        title: 'Quando ti serve,',
        title_accent: 'aggiungi',
        title_accent_italic: true,
        title_after: 'OLOlang o OLObooking.',
        title_after_italic: false,
        description: 'Stesso stack, stessa interfaccia, niente migration. Pronto al traffico — i pezzi crescono insieme al sito.',
        footer_value: '0"',
        footer_label: 'PRONTO AL TRAFFICO',
        separator_text: '',
      },
    ],

    // Timeline
    show_timeline:           true,
    timeline_line_color:     '#fde8e8',
    timeline_dot_color:      '#b3261e',
    timeline_dot_size:       14,
    timeline_height:         3,
    timeline_margin_bottom:  50,

    // Counter
    counter_font_family: 'serif',
    counter_size:        96,
    counter_color:       '#b3261e',
    counter_italic:      true,
    counter_weight:      '500',

    // Tag
    tag_size:        12,
    tag_color:       '#374151',

    // Media card
    media_aspect_ratio:           '5/4',
    media_object_position:        'center center',
    media_radius:                 { ...R(14) },
    media_radius_hover:           { ...R(14) },
    media_radius_hover_duration:  400,
    media_shadow:                 'sm',
    show_media_label:             true,

    // Pre-title
    pre_title_size:  12,
    pre_title_color: '#9ca3af',

    // Title
    title_font_family: 'serif',
    title_size:        30,
    title_weight:      '500',
    title_color:       '#0f172a',
    title_accent_color: '#b3261e',

    // Description
    description_size: 14,
    description_color: '#6b7280',

    // Footer metric
    footer_icon:        'clock',
    footer_value_size:  18,
    footer_label_size:  11,
    footer_value_color: '#0f172a',
    footer_label_color: '#9ca3af',

    // Separator arrow (dashed)
    separator_color:  '#b3261e',
    show_separator:   true,

    // Layout
    columns:    3,
    gap:        32,
    items_align: 'start',
  },

  // ═══ CONTENUTO ════════════════════════════════════════════════
  fields: [
    { type: 'separator', label: t('Step') },
    { key: 'items', label: t('Step cards'), type: 'content-items',
      itemLabel: t('Step'),
      defaults: { counter: '0X', tag_text: 'TAG', tag_dot_color: '#b3261e', media_label: 'MOCKUP', media_type: 'placeholder', media_content: '', media_image: '', media_bg: '#f5efe7', media_color: '#b3261e', pre_title: 'PRE-TITLE', title: 'Titolo', title_accent: 'accent', title_accent_italic: true, title_after: '', title_after_italic: false, description: 'Descrizione…', footer_value: '0"', footer_label: 'LABEL', separator_text: '' },
      itemFields: [
        { key: 'counter',             label: t('Numero step'),           type: 'text' },
        { key: 'tag_text',            label: t('Tag testo'),             type: 'text' },
        { key: 'tag_dot_color',       label: t('Tag pallino colore'),    type: 'color' },
        { key: 'media_type',          label: t('Tipo mockup'),           type: 'select', options: [
          { value: 'placeholder', label: t('Placeholder generico') },
          { value: 'terminal',    label: t('Terminal (testo mono)') },
          { value: 'image',       label: t('Immagine') },
        ]},
        { key: 'media_label',         label: t('Header mockup (testo)'),    type: 'text' },
        { key: 'media_content',       label: t('Contenuto terminal (testo)'), type: 'textarea' },
        { key: 'media_image',         label: t('Immagine mockup'),       type: 'image' },
        { key: 'media_bg',            label: t('Sfondo mockup'),         type: 'color' },
        { key: 'media_color',         label: t('Colore accent mockup'),  type: 'color' },
        { key: 'pre_title',           label: t('Pre-title (mono uppercase)'), type: 'text' },
        { key: 'title',               label: t('Titolo (base)'),         type: 'text' },
        { key: 'title_accent',        label: t('Titolo (accent)'),       type: 'text' },
        { key: 'title_accent_italic', label: t('Accent italico'),        type: 'toggle' },
        { key: 'title_after',         label: t('Titolo (dopo accent)'),  type: 'text' },
        { key: 'title_after_italic',  label: t('"Dopo accent" italico'), type: 'toggle' },
        { key: 'description',         label: t('Descrizione'),           type: 'editor', mode: 'inline' },
        { key: 'footer_value',        label: t('Metric valore'),         type: 'text' },
        { key: 'footer_label',        label: t('Metric label'),          type: 'text' },
        { key: 'separator_text',      label: t('Separatore (es. "→ POI")'), type: 'text' },
      ],
    },

    { type: 'separator', label: t('Visibilità globale') },
    { key: 'show_timeline',     label: t('Mostra timeline orizzontale'),   type: 'toggle' },
    { key: 'show_separator',    label: t('Mostra separatori tra step'),    type: 'toggle' },
    { key: 'show_media_label',  label: t('Mostra header mockup'),          type: 'toggle' },
  ],

  // ═══ STILE ═══════════════════════════════════════════════════
  styleFields: [
    { type: 'separator', label: t('Griglia') },
    { key: 'columns',     label: t('Numero colonne'),    type: 'range', min: 1, max: 5, step: 1 },
    { key: 'gap',         label: t('Gap tra step (px)'), type: 'range', min: 0, max: 80, step: 4 },
    { key: 'items_align', label: t('Allineamento contenuto'), type: 'select', options: [
      { value: 'start',  label: t('Sinistra') },
      { value: 'center', label: t('Centro') },
    ]},

    { type: 'separator', label: t('Timeline') },
    { key: 'timeline_line_color',    label: t('Colore linea'),    type: 'color' },
    { key: 'timeline_dot_color',     label: t('Colore pallini'),  type: 'color' },
    { key: 'timeline_dot_size',      label: t('Dimensione pallini (px)'), type: 'range', min: 6, max: 24, step: 1 },
    { key: 'timeline_height',        label: t('Altezza linea (px)'),       type: 'range', min: 1, max: 8, step: 1 },
    { key: 'timeline_margin_bottom', label: t('Spazio sotto timeline (px)'), type: 'range', min: 0, max: 120, step: 4 },

    { type: 'separator', label: t('Numero step') },
    { key: 'counter_font_family', label: t('Famiglia'), type: 'font-family' },
    { key: 'counter_size',   label: t('Dimensione (px)'), type: 'range', min: 40, max: 200, step: 4 },
    { key: 'counter_color',  label: t('Colore'),          type: 'color' },
    { key: 'counter_italic', label: t('Italico'),         type: 'toggle' },
    { key: 'counter_weight', label: t('Peso'),            type: 'select', options: [
      { value: '300', label: t('300 — Light') },
      { value: '400', label: t('400 — Regular') },
      { value: '500', label: t('500 — Medium') },
      { value: '600', label: t('600 — SemiBold') },
      { value: '700', label: t('700 — Bold') },
    ]},

    { type: 'separator', label: t('Tag') },
    { key: 'tag_size',  label: t('Dimensione (px)'), type: 'range', min: 10, max: 16, step: 1 },
    { key: 'tag_color', label: t('Colore testo'),    type: 'color' },

    { type: 'separator', label: t('Mockup card') },
    { key: 'media_aspect_ratio', label: t('Aspect ratio'), type: 'select', options: [
      { value: '16/9', label: '16 / 9' },
      { value: '5/4',  label: '5 / 4' },
      { value: '4/3',  label: '4 / 3' },
      { value: '1/1',  label: t('1 / 1 (quadrato)') },
      { value: '3/2',  label: '3 / 2' },
    ]},
    { key: 'media_shadow', label: t('Ombra mockup'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'sm',   label: t('Leggera') },
      { value: 'md',   label: t('Media') },
      { value: 'lg',   label: t('Forte') },
    ]},
    withHover({ key: 'media_radius', label: t('Border radius mockup'), type: 'border-radius' }, { hoverKey: 'media_radius_hover', hoverDurationKey: 'media_radius_hover_duration' }),
    { key: 'media_object_position', label: t('Posizione contenuto'), type: 'object-position', reveal: true, contextKeys: { ratio: 'media_aspect_ratio' } },

    { type: 'separator', label: t('Pre-title') },
    { key: 'pre_title_size',  label: t('Dimensione (px)'), type: 'range', min: 9, max: 16, step: 1 },
    { key: 'pre_title_color', label: t('Colore'),          type: 'color' },

    { type: 'separator', label: t('Titolo') },
    { key: 'title_font_family', label: t('Famiglia'), type: 'font-family' },
    { key: 'title_size',         label: t('Dimensione (px)'), type: 'range', min: 18, max: 60, step: 2 },
    { key: 'title_weight',       label: t('Peso'), type: 'select', options: [
      { value: '300', label: t('300 — Light') },
      { value: '400', label: t('400 — Regular') },
      { value: '500', label: t('500 — Medium') },
      { value: '600', label: t('600 — SemiBold') },
      { value: '700', label: t('700 — Bold') },
    ]},
    { key: 'title_color',        label: t('Colore base'),   type: 'color' },
    { key: 'title_accent_color', label: t('Colore accent'), type: 'color' },

    { type: 'separator', label: t('Descrizione') },
    { key: 'description_size',  label: t('Dimensione (px)'), type: 'range', min: 11, max: 20, step: 1 },
    { key: 'description_color', label: t('Colore'),          type: 'color' },

    { type: 'separator', label: t('Footer metric') },
    { key: 'footer_icon', label: t('Icona'), type: 'icon' },
    { key: 'footer_value_size',  label: t('Valore dimensione (px)'), type: 'range', min: 12, max: 30, step: 1 },
    { key: 'footer_value_color', label: t('Valore colore'),          type: 'color' },
    { key: 'footer_label_size',  label: t('Label dimensione (px)'),  type: 'range', min: 9, max: 14, step: 1 },
    { key: 'footer_label_color', label: t('Label colore'),           type: 'color' },

    { type: 'separator', label: t('Separatori tra step') },
    { key: 'separator_color', label: t('Colore separatore'), type: 'color' },
  ],
};
