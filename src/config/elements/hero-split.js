import { t } from '@/i18n';
import { withHover } from './_shared';

// Standard radius: 4 angoli indipendenti con link/unlink + hover
const RADIUS_999 = { tl: 999, tr: 999, br: 999, bl: 999, linked: true };
const RADIUS_24  = { tl: 24, tr: 24, br: 24, bl: 24, linked: true };
const RADIUS_18  = { tl: 18, tr: 18, br: 18, bl: 18, linked: true };

const ALIGN_OPTIONS = () => [
  { value: 'left',    label: t('Sinistra') },
  { value: 'center',  label: t('Centrato') },
  { value: 'right',   label: t('Destra') },
  { value: 'justify', label: t('Giustificato') },
];
const TARGET_OPTIONS = () => [
  { value: '_self',  label: t('Stessa scheda') },
  { value: '_blank', label: t('Nuova scheda') },
];
const FONT_FAMILY_OPTIONS = () => [
  { value: 'serif',      label: t('Serif (editoriale)') },
  { value: 'sans-serif', label: t('Sans-serif (moderno)') },
  { value: 'mono',       label: t('Monospace') },
];
const FONT_WEIGHT_OPTIONS = () => [
  { value: '300', label: t('300 — Light') },
  { value: '400', label: t('400 — Regular') },
  { value: '500', label: t('500 — Medium') },
  { value: '600', label: t('600 — SemiBold') },
  { value: '700', label: t('700 — Bold') },
  { value: '800', label: t('800 — ExtraBold') },
  { value: '900', label: t('900 — Black') },
];

/**
 * Hero Split — layout editoriale bicolonna.
 *
 * Standard Olobuild applicati (docs/TILE-PERFETTA.md):
 *   - tab CONTENUTO contiene solo testi e dati editabili (no colori, no grafica)
 *   - tab STILE contiene tutti gli aspetti visivi (color, size, radius, layout)
 *   - tutte le opzioni enum via `select`, mai input testuali
 *   - color picker standard con globe globali
 *   - withHover() bilaterale su CTA e radius
 *   - border-radius 4 angoli + stato hover
 *   - sfondo creativo unificato `background` per showcase e singole card
 *   - i18n completo: ogni label/option label tramite t()
 */
export default {
  type: 'hero-split',
  name: t('Hero Split'),
  icon: 'dashicons-columns',
  category: 'layout',

  defaults: {
    // Eyebrow
    eyebrow_text: 'STACK WORDPRESS · PER AGENZIE E PMI',
    eyebrow_dot_color: '#10b981',
    eyebrow_color: '#1f2937',

    // Headline
    headline_lines: [
      { text: 'Costruisci.', color: '#0f172a', italic: false },
      { text: 'Traduci.',    color: '#b3261e', italic: true  },
      { text: 'Prenota.',    color: '#0f172a', italic: false },
    ],
    headline_font_family: 'serif',
    headline_font_size:   96,
    headline_line_height: 1.0,
    headline_font_weight: '700',
    headline_align:       'left',

    // Subhead
    subhead: 'Un telaio, cinque prodotti, nessuna catena. Page builder gratis + prenotazioni + multilingua + virtual tour + e-learning, tutto in WordPress.',
    subhead_color:     '#374151',
    subhead_size:      18,
    subhead_italic:    true,
    subhead_max_width: 520,
    subhead_align:     'left',

    // CTAs
    cta1_text:        'Prenota demo →',
    cta1_url:         '#',
    cta1_target:      '_self',
    cta1_bg:          '#0f172a',
    cta1_bg_hover:    '',
    cta1_color:       '#ffffff',
    cta1_color_hover: '',
    cta1_size:        14,
    cta1_radius:                 { ...RADIUS_999 },
    cta1_radius_hover:           { ...RADIUS_999 },
    cta1_radius_hover_duration:  300,

    cta2_text:        'Esplora i prodotti',
    cta2_url:         '#',
    cta2_target:      '_self',
    cta2_bg:          'transparent',
    cta2_bg_hover:    '#0f172a',
    cta2_color:       '#0f172a',
    cta2_color_hover: '#ffffff',
    cta2_border:      '#0f172a',
    cta2_size:        14,
    cta2_radius:                 { ...RADIUS_999 },
    cta2_radius_hover:           { ...RADIUS_999 },
    cta2_radius_hover_duration:  300,

    // Stats
    stats: [
      { value: '5',      value_color: '#0f172a', label: 'PRODOTTI MODULARI' },
      { value: 'Gratis', value_color: '#b3261e', label: 'OLOBUILD, PER SEMPRE' },
      { value: '0 %',    value_color: '#0f172a', label: 'SAAS · LOCK-IN · COMMISSIONI' },
    ],

    // Showcase
    showcase_enabled:                    true,
    showcase_bg:                         { type: 'solid', color: '#f0e9dc' },
    showcase_padding:                    28,
    showcase_radius:                     { ...RADIUS_24 },
    showcase_radius_hover:               { ...RADIUS_24 },
    showcase_radius_hover_duration:      400,
    showcase_badge_text:                 'DEMO LIVE',
    showcase_badge_dot:                  '#dc2626',
    showcase_badge_bg:                   '#ffffff',
    showcase_items: [
      { number: '01', text: 'crea',     italic: true, text_color: '#0f172a', bg: { type: 'solid', color: '#ffffff' } },
      { number: '02', text: 'anima',    italic: true, text_color: '#0f172a', bg: { type: 'solid', color: '#ffffff' } },
      { number: '03', text: 'traduci',  italic: true, text_color: '#0f172a', bg: { type: 'solid', color: '#ffffff' } },
      { number: '04', text: 'pubblica', italic: true, text_color: '#0f172a', bg: { type: 'solid', color: '#ffffff' } },
    ],
    showcase_card_radius:                { ...RADIUS_18 },
    showcase_card_radius_hover:          { ...RADIUS_18 },
    showcase_card_radius_hover_duration: 400,
    showcase_card_shadow:                'sm',
    showcase_caption_left:               'PASSA IL MOUSE SUI TILE',
    showcase_caption_right:              'BORDER-RADIUS ANIMATO',
    showcase_hover_effect:               'none',

    // Layout
    split_ratio: '1fr 1fr',
    gap:         60,
    min_height:  600,

    // Fallback interno (lo style.padding del wrapper esterno ha priorità).
    tile_padding: { top: 80, right: 80, bottom: 60, left: 80 },
    tile_margin:  { top: 0, right: 0, bottom: 0, left: 0 },
  },

  // ═══ CONTENUTO ════════════════════════════════════════════════
  // SOLO testi e dati editabili. Niente colori, dimensioni, layout.
  fields: [
    { type: 'separator', label: t('Eyebrow') },
    { key: 'eyebrow_text', label: t('Testo eyebrow'), type: 'text' },

    { type: 'separator', label: t('Headline (righe)') },
    { key: 'headline_lines', label: t('Righe titolo'), type: 'content-items',
      itemLabel: t('Riga'),
      defaults: { text: 'Nuova riga', color: '#0f172a', italic: false },
      itemFields: [
        { key: 'text',   label: t('Testo'),     type: 'text' },
        { key: 'italic', label: t('Italico'),   type: 'toggle' },
        { key: 'color',  label: t('Colore'),    type: 'color' },
      ],
    },

    { type: 'separator', label: t('Sottotitolo') },
    { key: 'subhead',         label: t('Testo'),   type: 'editor', mode: 'inline' },
    { key: 'subhead_italic',  label: t('Italico'), type: 'toggle' },

    { type: 'separator', label: t('CTA primaria') },
    { key: 'cta1_text',   label: t('Testo'),  type: 'text' },
    { key: 'cta1_url',    label: t('URL'),    type: 'link' },
    { key: 'cta1_target', label: t('Apri in'), type: 'select', options: TARGET_OPTIONS() },

    { type: 'separator', label: t('CTA secondaria') },
    { key: 'cta2_text',   label: t('Testo'),   type: 'text' },
    { key: 'cta2_url',    label: t('URL'),     type: 'link' },
    { key: 'cta2_target', label: t('Apri in'), type: 'select', options: TARGET_OPTIONS() },

    { type: 'separator', label: t('Statistiche (fascia in fondo)') },
    { key: 'stats', label: t('Voci'), type: 'content-items',
      itemLabel: t('Stat'),
      defaults: { value: '0', value_color: '#0f172a', label: 'LABEL' },
      itemFields: [
        { key: 'value',       label: t('Valore'),        type: 'text' },
        { key: 'label',       label: t('Etichetta'),     type: 'text' },
        { key: 'value_color', label: t('Colore valore'), type: 'color' },
      ],
    },

    { type: 'separator', label: t('Showcase (lato destro)') },
    { key: 'showcase_enabled',       label: t('Mostra showcase'), type: 'toggle' },
    { key: 'showcase_badge_text',    label: t('Badge testo'),     type: 'text' },
    { key: 'showcase_items', label: t('Tile (grid 2x2)'), type: 'content-items',
      itemLabel: t('Tile'),
      defaults: { number: '00', text: 'azione', italic: true, text_color: '#0f172a', bg: { type: 'solid', color: '#ffffff' } },
      itemFields: [
        { key: 'number',     label: t('Numero'),       type: 'text' },
        { key: 'text',       label: t('Testo'),        type: 'text' },
        { key: 'italic',     label: t('Italico'),      type: 'toggle' },
        { key: 'text_color', label: t('Colore testo'), type: 'color' },
        { key: 'bg',         label: t('Sfondo card'),  type: 'background', showParallax: false },
      ],
    },
    { key: 'showcase_caption_left',  label: t('Caption sinistra'), type: 'text' },
    { key: 'showcase_caption_right', label: t('Caption destra'),   type: 'text' },
  ],

  // ═══ STILE ════════════════════════════════════════════════════
  // Tutto il resto: colori, dimensioni, tipografia, layout, sfondi, bordi, hover.
  styleFields: [
    { type: 'separator', label: t('Eyebrow stile') },
    { key: 'eyebrow_color',     label: t('Colore testo'),    type: 'color' },
    { key: 'eyebrow_dot_color', label: t('Colore pallino'),  type: 'color' },

    { type: 'separator', label: t('Tipografia headline') },
    { key: 'headline_font_family', label: t('Famiglia'),        type: 'select', options: FONT_FAMILY_OPTIONS() },
    { key: 'headline_font_size',   label: t('Dimensione (px)'), type: 'range', min: 32, max: 160, step: 2 },
    { key: 'headline_line_height', label: t('Interlinea'),      type: 'range', min: 0.8, max: 1.8, step: 0.05 },
    { key: 'headline_font_weight', label: t('Peso'),            type: 'select', options: FONT_WEIGHT_OPTIONS() },
    { key: 'headline_align',       label: t('Allineamento'),    type: 'select', options: ALIGN_OPTIONS() },

    { type: 'separator', label: t('Sottotitolo stile') },
    { key: 'subhead_color',     label: t('Colore'),             type: 'color' },
    { key: 'subhead_size',      label: t('Dimensione (px)'),    type: 'range', min: 12, max: 32, step: 1 },
    { key: 'subhead_max_width', label: t('Larghezza max (px)'), type: 'range', min: 200, max: 900, step: 10 },
    { key: 'subhead_align',     label: t('Allineamento'),       type: 'select', options: ALIGN_OPTIONS() },

    { type: 'separator', label: t('CTA primaria stile') },
    withHover({ key: 'cta1_bg',    label: t('Sfondo'),       type: 'color' }, { hoverKey: 'cta1_bg_hover' }),
    withHover({ key: 'cta1_color', label: t('Colore testo'), type: 'color' }, { hoverKey: 'cta1_color_hover' }),
    { key: 'cta1_size', label: t('Dimensione (px)'), type: 'range', min: 12, max: 22, step: 1 },
    withHover({ key: 'cta1_radius', label: t('Border radius'), type: 'border-radius' }, { hoverKey: 'cta1_radius_hover', hoverDurationKey: 'cta1_radius_hover_duration' }),

    { type: 'separator', label: t('CTA secondaria stile') },
    withHover({ key: 'cta2_bg',     label: t('Sfondo'),       type: 'color' }, { hoverKey: 'cta2_bg_hover' }),
    withHover({ key: 'cta2_color',  label: t('Colore testo'), type: 'color' }, { hoverKey: 'cta2_color_hover' }),
    { key: 'cta2_border', label: t('Colore bordo'),    type: 'color' },
    { key: 'cta2_size',   label: t('Dimensione (px)'), type: 'range', min: 12, max: 22, step: 1 },
    withHover({ key: 'cta2_radius', label: t('Border radius'), type: 'border-radius' }, { hoverKey: 'cta2_radius_hover', hoverDurationKey: 'cta2_radius_hover_duration' }),

    { type: 'separator', label: t('Showcase wrapper') },
    { key: 'showcase_bg',      label: t('Sfondo wrapper'),       type: 'background', showParallax: false },
    { key: 'showcase_padding', label: t('Padding wrapper (px)'), type: 'range', min: 0, max: 80, step: 2 },
    withHover({ key: 'showcase_radius', label: t('Border radius wrapper'), type: 'border-radius' }, { hoverKey: 'showcase_radius_hover', hoverDurationKey: 'showcase_radius_hover_duration' }),

    { type: 'separator', label: t('Showcase badge') },
    { key: 'showcase_badge_dot', label: t('Colore pallino'), type: 'color' },
    { key: 'showcase_badge_bg',  label: t('Sfondo badge'),   type: 'color' },

    { type: 'separator', label: t('Showcase card') },
    withHover({ key: 'showcase_card_radius', label: t('Border radius card'), type: 'border-radius' }, { hoverKey: 'showcase_card_radius_hover', hoverDurationKey: 'showcase_card_radius_hover_duration' }),
    { key: 'showcase_card_shadow', label: t('Ombra card'), type: 'select', options: [
      { value: 'none', label: t('Nessuna') },
      { value: 'sm',   label: t('Leggera') },
      { value: 'md',   label: t('Media') },
      { value: 'lg',   label: t('Forte') },
      { value: 'xl',   label: t('Molto forte') },
    ]},
    { key: 'showcase_hover_effect', label: t('Effetto hover card (transform)'), type: 'select', options: [
      { value: 'none',  label: t('Nessuno') },
      { value: 'lift',  label: t('Sollevamento') },
      { value: 'scale', label: t('Scala') },
      { value: 'tilt',  label: t('Inclinazione 3D') },
    ], description: t('Per animare il border-radius su hover usa il pannello hover del campo "Border radius card" qui sopra.') },

    { type: 'separator', label: t('Layout colonne') },
    { key: 'split_ratio', label: t('Proporzione colonne'), type: 'select', options: [
      { value: '1fr 1fr',     label: '50 / 50' },
      { value: '1.2fr 1fr',   label: '55 / 45' },
      { value: '1fr 1.2fr',   label: '45 / 55' },
      { value: '1fr 0.8fr',   label: '60 / 40' },
      { value: '0.8fr 1fr',   label: '40 / 60' },
    ]},
    { key: 'gap',        label: t('Gap colonne (px)'),    type: 'range', min: 0, max: 160, step: 4 },
    { key: 'min_height', label: t('Altezza minima (px)'), type: 'range', min: 0, max: 1200, step: 20 },
  ],
};
