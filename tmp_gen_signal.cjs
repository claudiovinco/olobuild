/* Signal — ricomposizione TILE-PURE (image-free). Media & News: ink/lime + IBM Plex. */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('sg');

// ── Palette (da :root signal.css)
const BG     = '#11161c';
const BG2    = '#141b22';
const PANEL  = '#1a212a';
const PANEL2 = '#222b36';
const INK    = '#0a0e12';
const LIME   = '#a3e635';
const LIMED  = '#8fce28';
const SKY    = '#5bc8ff';
const CREAM  = '#eef2ec';
const TXT    = '#9aa6b0';
const DIM    = '#5f6b76';
const LINE   = 'rgba(238,242,236,.1)';
const LINE2  = 'rgba(163,230,53,.4)';

const home = [];

// ──────────────────────────────────────────────────────
// 1) TICKER — newsticker marquee lime/ink
//    Blueprint: 4 voci, separator "//", font mono 12.5px
//    bg: var(--lime), color: var(--ink), h: 38px (padding 9px 0)
// ──────────────────────────────────────────────────────
home.push(sec(LIME, 'small', [
  row([col('1-1', [tile('newsticker', {
    items: [
      { id: 'sg-t1', title: 'The case against microservices (again)', url: '#', badge: '', icon: '', timestamp: '' },
      { id: 'sg-t2', title: 'What we got wrong about caching',        url: '#', badge: '', icon: '', timestamp: '' },
      { id: 'sg-t3', title: 'A love letter to the CLI',               url: '#', badge: '', icon: '', timestamp: '' },
      { id: 'sg-t4', title: 'Why your tests are slow',                url: '#', badge: '', icon: '', timestamp: '' },
    ],
    direction: 'horizontal',
    animation_type: 'marquee',
    marquee_direction: 'left',
    marquee_duration: 32,
    marquee_gap: 60,
    pause_on_hover: true,
    show_label: true,
    label_text: '//',
    label_shape: 'square',
    label_bg: INK,
    label_color: LIME,
    show_controls: false,
    show_indicators: false,
    show_counter: false,
    bg_color: LIME,
    text_color: INK,
    font_size: 12,
    font_weight: '500',
    height: '38',
    separator: '//',
    tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
    border_radius: { tl: 0, tr: 0, br: 0, bl: 0 },
  })])])
], { padding: 'none' }));

// ──────────────────────────────────────────────────────
// 2) FEATURED STORY — hero-split image-free
//    Blueprint: solo btn--lime "Read the post" (no ghost)
//    h1 clamp(34px,4.8vw,58px) → headline_font_size: 56
//    subhead max-width: 46ch ≈ 520px, meta sotto subhead (non nei stats)
//    showcase: 4 data-label card (TOPIC/READ TIME/AUTHOR/PUBLISHED)
// ──────────────────────────────────────────────────────
home.push(sec(BG, 'large', [row([col('1-1', [tile('hero-split', {
  eyebrow_text: '// featured · systems',
  eyebrow_dot_color: LIME,
  eyebrow_color: LIME,
  headline_lines: [
    { text: 'The cache that',       color: CREAM, italic: false },
    { text: 'lied to us',          color: LIME,  italic: false },
    { text: 'for three months',    color: CREAM, italic: false },
  ],
  headline_font_family: 'sans-serif',
  headline_font_size: 56,
  headline_line_height: 1.05,
  headline_font_weight: '600',
  headline_align: 'left',
  subhead: `A debugging story about stale reads, false confidence, and the one-line config that quietly broke production — plus what we changed so it can't happen again.`,
  subhead_color: TXT,
  subhead_size: 17,
  subhead_italic: false,
  subhead_max_width: 520,
  cta1_text: 'Read the post',
  cta1_url: '#featured',
  cta1_bg: LIME,
  cta1_color: INK,
  cta1_size: 14,
  cta1_radius: R(8),
  cta1_radius_hover: R(8),
  cta2_text: '',
  cta2_url: '',
  cta2_bg: '',
  cta2_color: '',
  cta2_border: '',
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: PANEL },
  showcase_padding: 28,
  showcase_radius: R(10),
  showcase_radius_hover: R(10),
  showcase_badge_text: 'by Devin Cho · 14 min read · 2 days ago',
  showcase_badge_dot: LIME,
  showcase_badge_bg: INK,
  showcase_badge_color: TXT,
  showcase_items: [
    { number: 'TOPIC',     text: 'Systems',   italic: false, text_color: LIME,  bg: { type: 'solid', color: BG2 } },
    { number: 'READ TIME', text: '14 min',    italic: false, text_color: CREAM, bg: { type: 'solid', color: BG2 } },
    { number: 'AUTHOR',    text: 'Devin Cho', italic: false, text_color: CREAM, bg: { type: 'solid', color: BG2 } },
    { number: 'PUBLISHED', text: '2 days ago',italic: false, text_color: CREAM, bg: { type: 'solid', color: BG2 } },
  ],
  showcase_card_radius: R(9),
  showcase_card_radius_hover: R(9),
  showcase_card_shadow: 'none',
  showcase_caption_left: 'FEATURED',
  showcase_caption_right: 'SIGNAL',
  showcase_hover_effect: 'none',
  split_ratio: '1.1fr .9fr',
  gap: 44,
  min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
})])])]));

// ──────────────────────────────────────────────────────
// 3) LATEST POSTS — sg-rule + info-cards 3 col (con media)
//    Blueprint: card con panel media 16/10 in cima (aspect-ratio 16/10,
//    border-radius 10px, bg var(--panel) + pattern diagonale).
//    Kicker "// tooling" ecc. → counter field (font mono, color LIME).
//    Footer = meta mono 11.5px, color DIM.
//    sg-posts gap: 24px; sg-post h3: 21px, color CREAM hover LIME.
//    sg-post p: 14px, color DIM.
//    Layout: 3 col, bg trasparente, border-top: 1px solid LINE.
// ──────────────────────────────────────────────────────
home.push(sec(BG, 'large', [
  row([col('1-1', [tile('section-header', {
    eyebrow_show: true, eyebrow_text: '// latest', eyebrow_color: LIME, eyebrow_dot_color: LIME, eyebrow_separator: '',
    headline_lines: [{ text: 'latest', color: DIM, italic: false }],
    headline_font_family: 'mono', headline_font_size: 16, headline_font_weight: '500', headline_align: 'left', headline_inline: false,
    tagline_show: false,
    layout: 'stack', gap: 8,
  })])]),
  row([col('1-1', [tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0,
    container_gap: 24,
    columns: 3,
    items_gap: 24,
    card_bg: { type: 'solid', color: PANEL },
    card_color: DIM,
    card_radius: R(10),
    card_padding: 0,
    show_icon: false,
    show_counter: true,
    show_counter_label: false,
    show_arrow: false,
    show_footer: true,
    show_media: true,
    media_aspect_ratio: '16/10',
    media_radius: { tl: 10, tr: 10, br: 0, bl: 0, linked: false },
    media_position: 'top',
    counter_shape: 'plain',
    counter_color: LIME,
    counter_size: 12,
    title_color: CREAM,
    title_font_family: 'sans-serif',
    title_size: 21,
    title_weight: '600',
    title_italic: false,
    description_size: 14,
    footer_size: 12,
    card_hover_effect: 'lift',
    items: [
      {
        counter: '// tooling',
        counter_label: '',
        title: 'A love letter to the CLI',
        description: 'Why the command line keeps outliving the GUIs built to replace it.',
        footer_text: '9 min · R. Mensah',
        footer_dot_color: LIME,
        media_label: 'terminal',
        arrow_url: '#',
        link_url: '#',
      },
      {
        counter: '// architecture',
        counter_label: '',
        title: 'The case against microservices',
        description: 'Again. But this time with the bill attached.',
        footer_text: '12 min · D. Cho',
        footer_dot_color: LIME,
        media_label: 'graph',
        arrow_url: '#',
        link_url: '#',
      },
      {
        counter: '// testing',
        counter_label: '',
        title: 'Why your tests are slow',
        description: `Five culprits, ranked by how much they're costing your team.`,
        footer_text: '7 min · L. Adeyemi',
        footer_dot_color: LIME,
        media_label: 'tests',
        arrow_url: '#',
        link_url: '#',
      },
    ],
  })])])
], { border_top: `1px solid ${LINE}` }));

// ──────────────────────────────────────────────────────
// 4) TOPICS — sg-rule + trust-strip pill (topic chips mono)
//    Blueprint: .sg-sec.panel bg=BG2, border-top: 1px solid LINE.
//    .sg-topic: border-radius 8px, padding 9px 15px, font mono 13px,
//    color TXT, border: 1px solid LINE (no background).
//    Hover: border-color LIME, color LIME.
// ──────────────────────────────────────────────────────
home.push(sec(BG2, 'large', [
  row([col('1-1', [tile('section-header', {
    eyebrow_show: true, eyebrow_text: '// topics', eyebrow_color: LIME, eyebrow_dot_color: LIME, eyebrow_separator: '',
    headline_lines: [{ text: 'topics', color: DIM, italic: false }],
    headline_font_family: 'mono', headline_font_size: 16, headline_font_weight: '500', headline_align: 'left', headline_inline: false,
    tagline_show: false,
    layout: 'stack', gap: 8,
  })])]),
  row([col('1-1', [tile('trust-strip', {
    items: [
      { text: 'architecture', icon: '', icon_color: LIME },
      { text: 'performance',  icon: '', icon_color: LIME },
      { text: 'tooling',      icon: '', icon_color: LIME },
      { text: 'databases',    icon: '', icon_color: LIME },
      { text: 'frontend',     icon: '', icon_color: LIME },
      { text: 'careers',      icon: '', icon_color: LIME },
      { text: 'AI',           icon: '', icon_color: LIME },
      { text: 'culture',      icon: '', icon_color: LIME },
      { text: 'security',     icon: '', icon_color: LIME },
    ],
    variant: 'pill',
    separator_char: '',
    align: 'left',
    flow: 'wrap',
    gap: 10,
    font_family: 'mono',
    text_color: TXT,
    text_size: 13,
    pill_bg: 'transparent',
    pill_border: LINE,
    pill_text_color: TXT,
    pill_padding_v: 9,
    pill_padding_h: 15,
    pill_radius: 8,
  })])])
], { border_top: `1px solid ${LINE}` }));

// ──────────────────────────────────────────────────────
// 5) NEWSLETTER — subscribe form centrata
//    Blueprint: .sg-news max-width centrata, bg PANEL, border LINE2,
//    border-radius 14px, padding clamp(32px,5vw,52px).
//    h2 clamp(26px,3.6vw,40px), .l color LIME ("every Tuesday").
//    form max-width 420px; input: bg BG (non BG2!), border LINE2.
//    button: btn--lime (LIME bg, INK text).
//    layout: horizontal (input+btn inline).
// ──────────────────────────────────────────────────────
home.push(sec(BG, 'large', [row([col('1-1', [tile('newsletter', {
  title: 'One good read, every Tuesday',
  subtitle: 'No hot takes, no churn — just one carefully edited essay on building software. 24,000 engineers subscribe.',
  layout: 'horizontal',
  email_placeholder: 'you@company.dev',
  button_text: 'Subscribe',
  button_icon: false,
  privacy_text: '',
  max_width: '420',
  alignment: 'center',
  bg_color: PANEL,
  box_border: LINE2,
  border_radius: 14,
  tile_padding: { top: 52, right: 52, bottom: 52, left: 52 },
  title_size: '36',
  title_weight: '600',
  title_color: CREAM,
  subtitle_size: '15',
  subtitle_color: TXT,
  input_bg: BG,
  input_color: CREAM,
  input_border: LINE2,
  input_focus_border: LIME,
  input_radius: 8,
  input_height: '46',
  btn_bg: LIME,
  btn_color: INK,
  btn_hover_bg: LIMED,
  btn_radius: 8,
  btn_font_size: '14',
  btn_font_weight: '600',
  shadow: 'none',
  icon_type: 'none',
})])])]));

// ══════════════════════════════════════════
// EMIT
// ══════════════════════════════════════════
K.emit({
  slug: 'signal',
  name: 'Signal',
  tags: ['blog', 'media', 'news', 'tech', 'newsletter'],
  description: 'Signal — tech & ideas blog, signal over noise. Ink/lime + IBM Plex Sans/Mono. Ticker marquee (4 voci) + featured post (1 CTA) + post grid con media + topics pill + newsletter. Riproduzione fedele OLOtheme Signal.',
  colors: {
    primary:           LIME,
    primary_contrast:  INK,
    secondary:         SKY,
    secondary_contrast: INK,
    muted:             BG2,
    muted_contrast:    TXT,
    text:              TXT,
    text_muted:        DIM,
    background:        BG,
    border:            LINE,
    link:              LIME,
  },
  css_disp:  `"IBM Plex Sans", -apple-system, sans-serif`,
  css_sans:  `"IBM Plex Sans", -apple-system, sans-serif`,
  heading_weight: '600',
  heading_line_height: '1.12',
  google_fonts: ['IBM Plex Sans', 'IBM Plex Mono'],
  logo_variant: 'light',
  menu: [
    { title: 'Articles', url: '#' },
    { title: 'Topics',   url: '#topics' },
    { title: 'Latest',   url: '#latest' },
    { title: 'Subscribe', url: '#subscribe' },
  ],
  header: {
    bg: 'rgba(17,22,28,.86)',
    text_color: DIM,
    sticky_bg: 'rgba(17,22,28,.92)',
    logo_width: 130,
  },
  footer: {
    bg: BG2,
    headColor: CREAM,
    brand: {
      name: 'Signal',
      tagline: 'A tech & ideas blog. Essays on software, systems and the people who build them.',
    },
    columns: [
      { title: 'Read',      links: ['Articles', 'Topics', 'Archive'] },
      { title: 'Signal',    links: ['About', 'Write for us', 'RSS'] },
      { title: 'Subscribe', links: ['Newsletter', 'Podcast', 'Contact'] },
    ],
    bottom: {
      left:  '© 2026 Signal — an OLOtheme demo.',
      right: 'Built with OLObuild',
    },
  },
  cursor: {
    blend_mode: 'exclusion',
    ring_color:  CREAM,
    dot_color:   LIME,
  },
}, home);
