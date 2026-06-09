import { t } from '@/i18n';

const ALIGN_OPTIONS = () => [
  { value: 'left',    label: t('Sinistra') },
  { value: 'center',  label: t('Centrato') },
  { value: 'right',   label: t('Destra') },
  { value: 'justify', label: t('Giustificato') },
];

/**
 * Section Header — eyebrow + headline editoriale multi-riga + tagline destra.
 * CONTENUTO: solo testi e dati editabili.
 * STILE: tipografia, colori, layout.
 */
export default {
  type: 'section-header',
  name: t('Section Header'),
  icon: 'dashicons-heading',
  category: 'layout',

  defaults: {
    eyebrow_show: true,
    eyebrow_text: 'PROVALO SUBITO',
    eyebrow_color: '#b3261e',
    eyebrow_dot_color: '#b3261e',
    eyebrow_separator: '— ',

    headline_lines: [
      { text: 'Nessun rischio,', color: '#0f172a', italic: false },
      { text: 'solo prodotto.',  color: '#b3261e', italic: true  },
    ],
    headline_font_family: 'serif',
    headline_font_size:   96,
    headline_line_height: 1.0,
    headline_font_weight: '700',
    headline_align:       'left',

    tagline_show: true,
    tagline_text: 'Try before you trust',
    tagline_text_italic: true,
    tagline_text_color: '#0f172a',
    tagline_text_size: 22,
    tagline_caption: 'TRE GARANZIE · CINQUE PROMESSE',
    tagline_caption_color: '',
    tagline_caption_size: 11,

    layout: 'split',
    split_ratio: '1.6fr 1fr',
    gap: 60,
    vertical_align: 'end',
  },

  // ═══ CONTENUTO ════════════════════════════════════════════════
  fields: [
    { type: 'separator', label: t('Eyebrow') },
    { key: 'eyebrow_show', label: t('Mostra eyebrow'), type: 'toggle' },
    { key: 'eyebrow_text', label: t('Testo'),          type: 'text' },

    { type: 'separator', label: t('Headline (righe)') },
    { key: 'headline_lines', label: t('Righe titolo'), type: 'content-items',
      itemLabel: t('Riga'),
      defaults: { text: 'Nuova riga', color: '#0f172a', italic: false },
      itemFields: [
        { key: 'text',   label: t('Testo'),   type: 'text' },
        { key: 'italic', label: t('Italico'), type: 'toggle' },
        { key: 'color',  label: t('Colore'),  type: 'color' },
      ],
    },

    { type: 'separator', label: t('Tagline destra') },
    { key: 'tagline_show',        label: t('Mostra tagline'),       type: 'toggle' },
    { key: 'tagline_text',        label: t('Testo principale'),     type: 'text' },
    { key: 'tagline_text_italic', label: t('Italico'),              type: 'toggle' },
    { key: 'tagline_caption',     label: t('Caption sotto (mono)'), type: 'text' },
  ],

  // ═══ STILE ════════════════════════════════════════════════════
  styleFields: [
    { type: 'separator', label: t('Eyebrow stile') },
    { key: 'eyebrow_separator', label: t('Separatore iniziale'), type: 'select', options: [
      { value: '',    label: t('Nessuno') },
      { value: '— ',  label: t('— (em-dash)') },
      { value: '· ',  label: t('· (bullet)') },
      { value: '/ ',  label: '/' },
      { value: '› ',  label: '›' },
    ]},
    { key: 'eyebrow_color',     label: t('Colore testo'),    type: 'color' },
    { key: 'eyebrow_dot_color', label: t('Colore pallino (se separatore = bullet)'), type: 'color' },

    { type: 'separator', label: t('Tipografia headline') },
    { key: 'headline_font_family', label: t('Famiglia'), type: 'select', options: [
      { value: 'serif',      label: t('Serif (editoriale)') },
      { value: 'sans-serif', label: t('Sans-serif (moderno)') },
      { value: 'mono',       label: t('Monospace') },
    ]},
    { key: 'headline_font_size',   label: t('Dimensione (px)'), type: 'range', min: 32, max: 160, step: 2 },
    { key: 'headline_line_height', label: t('Interlinea'),      type: 'range', min: 0.8, max: 1.8, step: 0.05 },
    { key: 'headline_font_weight', label: t('Peso'),            type: 'select', options: [
      { value: '300', label: t('300 — Light') },
      { value: '400', label: t('400 — Regular') },
      { value: '500', label: t('500 — Medium') },
      { value: '600', label: t('600 — SemiBold') },
      { value: '700', label: t('700 — Bold') },
      { value: '800', label: t('800 — ExtraBold') },
      { value: '900', label: t('900 — Black') },
    ]},
    { key: 'headline_align', label: t('Allineamento'), type: 'select', options: ALIGN_OPTIONS() },

    { type: 'separator', label: t('Tagline stile') },
    { key: 'tagline_text_color',    label: t('Colore testo principale'),     type: 'color' },
    { key: 'tagline_text_size',     label: t('Dimensione testo (px)'),       type: 'range', min: 12, max: 48, step: 1 },
    { key: 'tagline_caption_color', label: t('Colore caption'),              type: 'color' },
    { key: 'tagline_caption_size',  label: t('Dimensione caption (px)'),     type: 'range', min: 9, max: 18, step: 1 },

    { type: 'separator', label: t('Composizione (headline / tagline)') },
    { key: 'layout', label: t('Modalità'), type: 'select', options: [
      { value: 'split',  label: t('Split (headline sx, tagline dx)') },
      { value: 'stack',  label: t('Stack (solo headline)') },
      { value: 'center', label: t('Centro (tutto centrato)') },
    ]},
    { key: 'split_ratio', label: t('Proporzione split'), type: 'select', options: [
      { value: '1fr 1fr',   label: '50 / 50' },
      { value: '1.6fr 1fr', label: '62 / 38' },
      { value: '2fr 1fr',   label: '67 / 33' },
      { value: '1fr 2fr',   label: '33 / 67' },
    ]},
    { key: 'vertical_align', label: t('Allineamento verticale'), type: 'select', options: [
      { value: 'start',    label: t('In alto') },
      { value: 'center',   label: t('Centro') },
      { value: 'end',      label: t('In basso') },
      { value: 'baseline', label: t('Baseline') },
    ]},
    { key: 'gap', label: t('Gap colonne (px)'), type: 'range', min: 0, max: 200, step: 4 },
  ],
};
