/* Mono — ricomposizione TILE-PURE (image-free). Creative / designer portfolio.
   Light Swiss-minimal, monochrome. DM Sans (display + body) + Space Mono (labels).
   Palette: --paper #f2efe8 / --ink #18181a. */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('mo');

const PAPER  = '#f2efe8';
const PAPER2 = '#e8e3d7';
const CARD   = '#ebe7dc';
const INK    = '#18181a';
const INK2   = '#3a3a3c';
const TXT    = '#4a4a48';
const DIM    = '#8d8a82';
const LINE   = '#d7d1c2';
const LINE2  = '#c2bcab';
const WHITE  = '#ffffff';
const GREEN  = '#3ba776';

const home = [];

// ─────────────────────────────────────────────
// 1) HERO — statement tipografico fullwidth
// Blueprint: mo-hero__top con DUE span .mono (sx: eyebrow, dx: "Selected work / 2018 — 2026")
// CTA assenti nel blueprint hero — solo paragrafo + badge disponibilità con punto verde
// NOTA: hero-split non supporta nativamante il secondo span top-right né il badge dot
//       → eyebrow = span sx · badge disponibilità = subhead (best-effort, segnalato)
// ─────────────────────────────────────────────
home.push(sec(PAPER, 'large', [ row([ col('1-1', [ tile('hero-split', {
  eyebrow_text: `Independent designer · brand & digital · working worldwide from Lisbon`,
  eyebrow_dot_color: 'transparent',
  eyebrow_color: DIM,
  headline_lines: [
    { text: 'Design that', color: INK,  italic: false },
    { text: 'says less,',  color: DIM,  italic: false },
    { text: 'means more.', color: INK,  italic: false },
  ],
  headline_font_family: 'sans-serif',
  headline_font_size: 80,
  headline_line_height: 0.94,
  headline_font_weight: '500',
  headline_align: 'left',
  subhead: `I help founders and cultural institutions find the simplest version of their idea — then make it unmistakable.`,
  subhead_color: INK2,
  subhead_size: 19,
  subhead_italic: false,
  subhead_max_width: 440,
  // Nessun bottone CTA nel blueprint hero — campi cta vuoti
  cta1_text: '',
  cta1_url: '#',
  cta1_bg: 'transparent',
  cta1_color: INK,
  cta1_size: 14,
  cta1_radius: R(999),
  cta1_radius_hover: R(999),
  cta2_text: '',
  cta2_url: '#',
  cta2_bg: 'transparent',
  cta2_color: INK,
  cta2_border: LINE2,
  cta2_size: 14,
  cta2_radius: R(999),
  cta2_radius_hover: R(999),
  stats: [],
  showcase_enabled: false,
  split_ratio: '1fr',
  gap: 0,
  min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
}) ]) ]) ]));

// ─────────────────────────────────────────────
// 2) WORK INDEX — lista progetti con numero / titolo / categoria / anno
// Blueprint: NEW tile candidate HoverList (non esiste) → info-cards 1-col
// hover: padding-left aumenta + bg paper-2 (best-effort con card_hover_effect:'lift')
// ─────────────────────────────────────────────
const sectionRule = (label, count) => tile('section-header', {
  eyebrow_show: false,
  headline_lines: [{ text: label, color: DIM, italic: false }],
  headline_font_family: 'sans-serif',
  headline_font_size: 13,
  headline_font_weight: '400',
  headline_align: 'left',
  tagline_show: !!count,
  tagline_text: count || '',
  tagline_text_italic: false,
  tagline_text_color: DIM,
  tagline_text_size: 12,
  layout: 'stack',
  gap: 0,
});

home.push(sec(PAPER, 'large', [
  row([ col('1-1', [ sectionRule('Selected work', '12 projects') ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0,
    container_gap: 0,
    columns: 1,
    items_gap: 0,
    card_bg: { type: 'solid', color: 'transparent' },
    card_color: DIM,
    card_radius: R(0),
    card_padding: 26,
    card_border: LINE,
    show_icon: false,
    show_counter: true,
    show_counter_label: false,
    show_arrow: true,
    show_footer: true,
    show_media: false,
    title_color: INK,
    title_font_family: 'sans-serif',
    title_size: 36,
    title_weight: '500',
    title_italic: false,
    counter_size: 13,
    description_size: 0,
    footer_size: 12,
    card_hover_effect: 'lift',
    items: [
      { counter: '01', title: 'Marisol',       footer_text: 'Brand identity — 2026', footer_dot_color: LINE },
      { counter: '02', title: 'Atlas Press',   footer_text: 'Editorial · web — 2025', footer_dot_color: LINE },
      { counter: '03', title: 'Field Museum',  footer_text: 'Wayfinding — 2025',      footer_dot_color: LINE },
      { counter: '04', title: 'Cobalt',        footer_text: 'Identity · product — 2024', footer_dot_color: LINE },
      { counter: '05', title: `Höör Festival`, footer_text: 'Campaign — 2023',        footer_dot_color: LINE },
    ],
  }) ]) ]),
]));

// ─────────────────────────────────────────────
// 3) WORK GRID — 4 progetti in griglia 2-col con titolo + descrizione
// Blueprint: NEW tile candidate WorkGrid (non esiste) → info-cards 2-col
// IMAGE-FREE: card_bg = CARD (placeholder astratto striato)
// mo-rule: "Recent / featured" | "↓ scroll"
// ─────────────────────────────────────────────
home.push(sec(PAPER2, 'large', [
  row([ col('1-1', [ sectionRule('Recent / featured', '↓ scroll') ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0,
    container_gap: 24,
    columns: 2,
    items_gap: 24,
    card_bg: { type: 'solid', color: CARD },
    card_color: DIM,
    card_radius: R(0),
    card_padding: 20,
    card_border: '',
    show_icon: false,
    show_counter: true,
    show_counter_label: true,
    show_arrow: false,
    show_footer: false,
    show_media: false,
    title_color: INK,
    title_font_family: 'sans-serif',
    title_size: 22,
    title_weight: '500',
    title_italic: false,
    counter_size: 12,
    description_size: 14,
    card_hover_effect: 'scale',
    items: [
      { counter: `'26`, counter_label: 'Brand',      title: 'Marisol',      description: 'A coastal hotel group, rebuilt around one mark and a lot of restraint.' },
      { counter: `'25`, counter_label: 'Editorial',  title: 'Atlas Press',  description: `An independent publisher's new look, from spine to site.` },
      { counter: `'25`, counter_label: 'Wayfinding', title: 'Field Museum', description: 'A signage and type system that quietly tells you where you are.' },
      { counter: `'24`, counter_label: 'Product',    title: 'Cobalt',       description: 'Brand and interface for a developer tool that hates noise.' },
    ],
  }) ]) ]),
]));

// ─────────────────────────────────────────────
// 4) TYPE TESTER — specimen variabile interattivo
// Blueprint: 4 assi (font-size, font-weight, letter-spacing, line-height) — assi CSS non OpenType.
// Il tile variablespecimen usa assi OpenType (tag 4-lettere). Approssimazione best-effort:
//   wght (200-800) + opsz (9-40) con sample text del blueprint.
// SEGNALATO: gli assi nel blueprint (size/tracking/leading) sono CSS-property, non mappabili 1:1.
// ─────────────────────────────────────────────
home.push(sec(PAPER, 'large', [
  row([ col('1-1', [ sectionRule('Type / specimen', 'drag · edit the words') ]) ]),
  row([ col('1-1', [ tile('variablespecimen', {
    font_family: `"DM Sans", sans-serif`,
    sample_text: 'Typography is a voice you can see.',
    interaction: 'both',
    auto_animate: true,
    auto_speed: '8',
    drag_hint: `Trascina · X = peso · Y = dimensione ottica`,
    show_readout: true,
    axes: [
      { tag: 'wght', label: 'Weight',  min: 200, max: 800, default_val: 400 },
      { tag: 'opsz', label: 'Optical', min: 9,   max: 40,  default_val: 24 },
    ],
    text_color: INK,
    accent_color: DIM,
    bg_color: CARD,
    font_size: '72',
    font_weight_fallback: '400',
    text_align: 'left',
    padding_y: '36',
  }) ]) ]),
]));

// ─────────────────────────────────────────────
// 5) ABOUT — testo bicolonna (IMAGE-FREE) + lista capacità mo-caps
// Blueprint: griglia 1fr / 1.3fr — sinistra: "About" label + media placeholder
//            destra: h2 + 2 paragrafi + ul.mo-caps (4 voci titolo | numero mono)
// Approssimazione: section-header per il blocco testo, poi info-cards 1-col per mo-caps
// (nessun tile nativo per la griglia bicolonna con media left — best-effort)
// ─────────────────────────────────────────────
home.push(sec(PAPER2, 'large', [
  row([
    col('2-3', [ tile('section-header', {
      eyebrow_show: true,
      eyebrow_text: 'About',
      eyebrow_color: DIM,
      eyebrow_dot_color: 'transparent',
      eyebrow_separator: '',
      headline_lines: [
        { text: `I'm Nadia — a designer who'd rather remove ten things than add one.`, color: INK, italic: false },
      ],
      headline_font_family: 'sans-serif',
      headline_font_size: 36,
      headline_font_weight: '500',
      headline_align: 'left',
      headline_inline: false,
      tagline_show: true,
      tagline_text: `For eight years I've worked between brand and digital, mostly with small teams who care about craft. Before going independent I led design at a studio in Copenhagen; now it's just me, which is exactly the right number of people.\n\nI work in tight loops, show early, and treat your constraints as the brief — not the obstacle.`,
      tagline_text_italic: false,
      tagline_text_color: INK2,
      tagline_text_size: 16,
      layout: 'stack',
      gap: 20,
    }) ]),
  ]),
  // mo-caps: lista orizzontale "capacità | numero" con bordi — info-cards 1-col no-card
  row([ col('1-1', [ tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0,
    container_gap: 0,
    columns: 1,
    items_gap: 0,
    card_bg: { type: 'solid', color: 'transparent' },
    card_color: DIM,
    card_radius: R(0),
    card_padding: 15,
    card_border: LINE,
    show_icon: false,
    show_counter: true,
    show_counter_label: false,
    show_arrow: false,
    show_footer: false,
    show_media: false,
    title_color: INK,
    title_font_family: 'sans-serif',
    title_size: 15,
    title_weight: '400',
    title_italic: false,
    counter_size: 12,
    description_size: 0,
    footer_size: 11,
    card_hover_effect: 'none',
    items: [
      { counter: '01', title: 'Brand identity & systems',  description: '' },
      { counter: '02', title: 'Editorial & type',          description: '' },
      { counter: '03', title: 'Web & product design',       description: '' },
      { counter: '04', title: 'Art direction',              description: '' },
    ],
  }) ]) ]),
]));

// ─────────────────────────────────────────────
// 6) SERVICES MARQUEE — ticker oversized dei servizi
// Blueprint: font-size clamp(30px,4vw,56px) · separatore ✺ · colore ink
// Tile marquee — border-top + border-bottom gestiti dalla sezione (border via sec extra)
// ─────────────────────────────────────────────
home.push(sec(PAPER, 'small', [ row([ col('1-1', [ tile('marquee', {
  items: [
    { text: 'Brand identity' },
    { text: 'Editorial' },
    { text: 'Web design' },
    { text: 'Art direction' },
    { text: 'Type' },
    { text: 'Packaging' },
  ],
  speed: 34,
  direction: 'left',
  separator: '✺',
  separator_color: DIM,
  font_family: 'sans-serif',
  font_size: 52,
  font_weight: '500',
  text_color: INK,
  bg_color: 'transparent',
  pause_on_hover: true,
  letter_spacing: '-0.03em',
}) ]) ]) ]));

// ─────────────────────────────────────────────
// 7) CONTACT CTA — titolo oversized centrato + 2 pulsanti
// Blueprint: .mo-contact text-align:center, h2 clamp(40px,8vw,108px), 2 btn
//   btn--ink: nadia@halford.studio · btn--out: Book a call
// ─────────────────────────────────────────────
home.push(sec(PAPER2, 'large', [ row([ col('1-1', [ tile('cta-banner', {
  headline: `Let's make something`,
  headline_accent: 'clear.',
  headline_accent_italic: false,
  subtitle: 'Currently booking — August 2026 onward',
  cta_text: 'nadia@halford.studio',
  cta_url: '#',
  cta2_text: 'Book a call',
  cta2_url: '#',
  cta2_bg: 'transparent',
  cta2_color: INK,
  cta2_border: LINE2,
  bg: { type: 'solid', color: PAPER2 },
  text_color: INK,
  accent_color: INK,
  subtitle_color: DIM,
  cta_bg: INK,
  cta_color: PAPER,
  cta_radius: R(999),
  cta_size: 14,
  headline_font_family: 'sans-serif',
  headline_size: 96,
  headline_weight: '500',
  subtitle_size: 12,
  layout: 'stack',
  vertical_align: 'center',
  banner_radius: R(0),
  banner_padding: 80,
}) ]) ]) ]));

K.emit({
  slug: 'mono',
  name: 'Mono',
  tags: ['creative', 'portfolio', 'designer', 'minimal', 'monochrome'],
  description: `Mono — independent designer portfolio. Light Swiss-minimal, monochrome. DM Sans (display + body) + Space Mono (labels). Riproduzione fedele dell'OLOtheme Mono (Creative).`,
  colors: {
    primary:            INK,
    primary_contrast:   PAPER,
    secondary:          DIM,
    secondary_contrast: WHITE,
    muted:              PAPER2,
    muted_contrast:     TXT,
    text:               TXT,
    text_muted:         DIM,
    background:         PAPER,
    border:             LINE,
    link:               INK,
  },
  css_disp:            `"DM Sans", -apple-system, sans-serif`,
  css_sans:            `"DM Sans", -apple-system, sans-serif`,
  heading_weight:      '500',
  heading_line_height: '1.02',
  google_fonts:        ['DM Sans', 'Space Mono'],
  logo_variant:        'dark',
  menu: [
    { title: 'Work',     url: '#work' },
    { title: 'About',    url: '#about' },
    { title: 'Services', url: '#services' },
    { title: 'Contact',  url: '#contact' },
  ],
  header: {
    bg:         'rgba(242,239,232,.86)',
    text_color: TXT,
    sticky_bg:  'rgba(242,239,232,.86)',
    logo_width: 140,
  },
  footer: {
    bg:        PAPER2,
    headColor: INK,
    brand: {
      name:    'Nadia Halford',
      tagline: 'Independent brand & digital designer. Working worldwide from Lisbon.',
    },
    columns: [
      { title: 'Navigation', links: ['Work', 'About', 'Services', 'Contact'] },
      { title: 'Social',     links: ['Instagram', 'Are.na', 'LinkedIn', 'Read.cv'] },
    ],
    bottom: {
      left:  '© 2026 Nadia Halford — an OLOtheme demo.',
      right: 'Built with OLObuild',
    },
  },
  cursor: false,
}, home);
