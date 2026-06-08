/* Cadence — ricomposizione TILE-PURE (image-free). Health & Fitness (personal trainer).
   Charcoal + coral. Big Shoulders Display (display) + Figtree (body).
   CORREZIONI v2: process-steps con card per sezione "How it works";
   counter stat-strip con font/colori corretti; headline hero inline; copy backtick. */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('cd');

// ── PALETTE (da :root cadence.css) ──────────────────────────────────────────
const BG    = '#1a1d24';
const BG2   = '#1e2129';
const PANEL = '#252932';
const PANEL2= '#2e333e';
const INK   = '#101218';
const CORAL = '#ff6b54';
const CORALD= '#ed5840';
const SKY   = '#5ec8d8';
const TXT   = '#aab1be';
const DIM   = '#6f7785';
const LINE  = 'rgba(255,255,255,.09)';
const LINE2 = 'rgba(255,107,84,.42)';
const WHITE = '#ffffff';
const CTINT = 'rgba(255,107,84,.14)';

const home = [];

// ── 1) HERO ──────────────────────────────────────────────────────────────────
// Blueprint: grid 1.1fr .9fr, h1 uppercase "Stronger / on your / terms."
// coral su "your" (em), font Big Shoulders Display weight 800, line-height .9
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('hero-split', {
  eyebrow_text: `Coach Theo · 1:1 & online`,
  eyebrow_dot_color: CORAL,
  eyebrow_color: CORAL,
  headline_lines: [
    { text: 'Stronger',  color: WHITE, italic: false },
    { text: 'on',        color: WHITE, italic: false },
    { text: `your`,      color: CORAL, italic: false },
    { text: 'terms.',    color: WHITE, italic: false },
  ],
  headline_font_family: 'sans-serif',
  headline_font_size: 96,
  headline_line_height: 0.9,
  headline_font_weight: '800',
  headline_align: 'left',
  subhead: `Personal training and online coaching built around your life — proper programming, weekly check-ins, and the accountability that finally makes it stick.`,
  subhead_color: TXT,
  subhead_size: 18,
  subhead_italic: false,
  subhead_max_width: 420,
  cta1_text: 'Book a free consult',
  cta1_url: '#pricing',
  cta1_bg: CORAL,
  cta1_color: WHITE,
  cta1_size: 14,
  cta1_radius: R(6),
  cta1_radius_hover: R(6),
  cta2_text: 'See results',
  cta2_url: '#results',
  cta2_bg: 'transparent',
  cta2_color: WHITE,
  cta2_border: LINE2,
  cta2_size: 14,
  cta2_radius: R(6),
  cta2_radius_hover: R(6),
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: PANEL },
  showcase_padding: 28,
  showcase_radius: R(12),
  showcase_radius_hover: R(12),
  showcase_badge_text: 'COACH THEO · AT A GLANCE',
  showcase_badge_dot: CORAL,
  showcase_badge_bg: INK,
  showcase_badge_color: WHITE,
  showcase_items: [
    { number: 'Clients coached', text: '500+',  italic: false, text_color: CORAL, bg: { type: 'solid', color: BG2 } },
    { number: 'Years coaching',  text: '9',     italic: false, text_color: WHITE, bg: { type: 'solid', color: BG2 } },
    { number: 'Hit their goal',  text: '92%',   italic: false, text_color: WHITE, bg: { type: 'solid', color: BG2 } },
    { number: 'Avg. rating',     text: '4.9',   italic: false, text_color: WHITE, bg: { type: 'solid', color: BG2 } },
  ],
  showcase_card_radius: R(10),
  showcase_card_radius_hover: R(10),
  showcase_card_shadow: 'none',
  showcase_caption_left: 'RESULTS',
  showcase_caption_right: '2026',
  showcase_hover_effect: 'none',
  split_ratio: '1.1fr .9fr',
  gap: 48,
  min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
}) ]) ]) ]));

// ── 2) STAT STRIP — 4 counter su sfondo INK con border-block ─────────────────
// CSS: background var(--ink), border-block 1px solid var(--line)
// .cd-stat b → font Big Shoulders Display w800 clamp(44-72px) color CORAL
// .cd-stat span → 12px w700 uppercase TXT-dim
const stat = (number, suffix, label) => col('1-4', [ tile('counter', {
  number,
  suffix,
  prefix: '',
  label,
  icon_emoji: '',
  text_color: CORAL,
  number_font_size: '64',
  number_font_weight: '800',
  label_color: DIM,
  label_font_size: '12',
  label_font_weight: '700',
  bg_type: 'color',
  bg_color: 'transparent',
  tile_padding: { top: 40, right: 24, bottom: 40, left: 24 },
  border_radius: '0',
}) ]);

home.push(sec(INK, 'none', [ row([
  stat('500', '+', 'Clients coached'),
  stat('9',   '',  'Years coaching'),
  stat('92',  '%', 'Hit their goal'),
  stat('4.9', '',  'Avg. rating'),
], { gap: 0 }) ]));

// ── helper: section-header centrato ──────────────────────────────────────────
const shead = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true,
  eyebrow_text: eyebrow,
  eyebrow_color: CORAL,
  eyebrow_dot_color: CORAL,
  eyebrow_separator: '',
  headline_lines: [
    { text: l1,     color: WHITE, italic: false },
    { text: accent, color: CORAL, italic: true  },
  ],
  headline_font_family: 'sans-serif',
  headline_font_size: 52,
  headline_font_weight: '800',
  headline_align: 'center',
  headline_inline: true,
  tagline_show: !!intro,
  tagline_text: intro || '',
  tagline_text_italic: false,
  tagline_text_color: DIM,
  tagline_text_size: 16.5,
  layout: 'center',
  gap: 16,
});

// ── 3) PROGRAMMES — "Pick your lane" ─────────────────────────────────────────
// CSS: .cd-prog → background PANEL, border 1px LINE, border-radius 12px
// top media area (image-free placeholder) + label n (coral uppercase) + h3 + p
// Tile giusto: info-cards con show_counter per badge-label "In person/Anywhere/Small group"
// show_media: true per rendere l'area media superiore (image-free, aspect 16/10)
home.push(sec(BG, 'large', [
  row([ col('1-1', [ shead('Ways to train', 'Pick your ', 'lane', '') ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0,
    container_gap: 18,
    columns: 3,
    items_gap: 18,
    card_bg: { type: 'solid', color: PANEL },
    card_color: DIM,
    card_radius: R(12),
    card_padding: 26,
    show_icon: false,
    show_counter: true,
    show_counter_label: false,
    show_arrow: false,
    show_footer: false,
    show_media: false,
    counter_shape: 'plain',
    counter_color: CORAL,
    counter_size: 12,
    title_color: WHITE,
    title_font_family: 'sans-serif',
    title_size: 24,
    title_weight: '700',
    title_italic: false,
    description_size: 14.5,
    card_hover_effect: 'lift',
    items: [
      {
        counter: 'In person',
        title: '1:1 Training',
        description: 'Private sessions at the studio, fully coached, fully tailored.',
      },
      {
        counter: 'Anywhere',
        title: 'Online Coaching',
        description: `Your programme in the app, weekly check-ins, form reviews on video.`,
      },
      {
        counter: 'Small group',
        title: 'Strength Squad',
        description: 'Train with three others, split the cost, keep the accountability.',
      },
    ],
  }) ]) ]),
]));

// ── 4) RESULTS — "The proof" ──────────────────────────────────────────────────
// Blueprint: cd-bacard → PANEL bg, LINE border, radius 12px. 2-col images + caption.
// BeforeAfter non esiste → approssimazione con info-cards (SEGNALATA)
// Manteniamo 3 card con copy fedele al blueprint
home.push(sec(BG2, 'large', [
  row([ col('1-1', [ shead('Real people', 'The ', 'proof', '') ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0,
    container_gap: 18,
    columns: 3,
    items_gap: 18,
    card_bg: { type: 'solid', color: PANEL },
    card_color: DIM,
    card_radius: R(12),
    card_padding: 26,
    show_icon: true,
    show_counter: false,
    show_arrow: false,
    show_footer: true,
    show_media: false,
    icon_color: CORAL,
    icon_bg_color: CTINT,
    title_color: WHITE,
    title_font_family: 'sans-serif',
    title_size: 20,
    title_weight: '700',
    title_italic: false,
    description_size: 14,
    footer_size: 12,
    card_hover_effect: 'lift',
    items: [
      {
        icon: 'award',
        title: 'Marcus · 16 weeks',
        description: `Down 11kg, first-ever pull-up, and a deadlift PB he never thought he'd hit.`,
        footer_text: '1:1 Training',
        footer_dot_color: CORAL,
      },
      {
        icon: 'heart',
        title: 'Priya · 6 months',
        description: 'Built real strength postpartum, pain-free and back to running.',
        footer_text: 'Online Coaching',
        footer_dot_color: CORAL,
      },
      {
        icon: 'trending-up',
        title: 'Sam · 1 year',
        description: 'From couch to first powerlifting meet — and stayed for the community.',
        footer_text: 'Strength Squad',
        footer_dot_color: CORAL,
      },
    ],
  }) ]) ]),
]));

// ── 5) PROCESS — "Simple, start to finish" ───────────────────────────────────
// CSS: .cd-step → padding:26px 22px; border:1px solid LINE; border-radius:12px;
//                 background:PANEL; ::before → counter CORAL disp 34px
// → USA process-steps CON card settings (bg+border+radius+padding)
home.push(sec(BG, 'large', [
  row([ col('1-1', [ shead('How it works', 'Simple, ', 'start to finish', '') ]) ]),
  row([ col('1-1', [ tile('process-steps', {
    columns: 4,
    gap: 16,
    align: 'left',
    auto_number: false,
    item_gap: 12,
    number_style: 'plain',
    number_color: CORAL,
    number_size: 34,
    number_font: 'sans-serif',
    number_weight: '800',
    title_color: WHITE,
    title_size: 20,
    title_font: 'sans-serif',
    title_weight: '700',
    desc_color: DIM,
    desc_size: 13.5,
    card_bg: PANEL,
    card_border: `1px solid ${LINE}`,
    card_radius: R(12),
    card_padding: 26,
    items: [
      { number: '01', title: 'Consult', description: `A free call to talk goals, history and what's actually realistic.` },
      { number: '02', title: 'Plan',    description: `A programme built for you — training, nutrition guidance, the lot.` },
      { number: '03', title: 'Train',   description: 'We work the plan together, in person or in the app.' },
      { number: '04', title: 'Adjust',  description: 'Weekly check-ins keep it honest and moving forward.' },
    ],
  }) ]) ]),
]));

// ── 6) AVAILABILITY — "When can you train?" ──────────────────────────────────
// Tile nativo `availability` — griglia toggle fasce×giorni + conteggio slot + verdetto a soglie.
// Giorni (colonne): Mon–Sun (7). Fasce (righe): Morning, Midday, Evening (3).
// Tier dal blueprint: Reset ≤2 / Build 3–4 / Peak 5+  (soglie crescenti, primo min=0).
// zone_accent = CORAL (#ff6b54); tema scuro → cell_bg=PANEL, zone_on=CORAL.
home.push(sec(BG2, 'large', [
  row([ col('1-1', [ tile('availability', {
    eyebrow:     `Plan your week`,
    heading:     `When can you train?`,
    intro:       `Tap the slots you can show up. We'll suggest the track that fits your week — no pressure, just a plan.`,
    days:        `Mon,Tue,Wed,Thu,Fri,Sat,Sun`,
    bands:       `Morning,Midday,Evening`,
    count_label: `Sessions / week`,
    verdict_label: `Suggested track`,
    tiers: [
      { min: 0, label: `Reset`,  text: `Light, flexible sessions — deload and reconnect with movement at a pace that suits your week.` },
      { min: 3, label: `Build`,  text: `The sweet spot for steady progress — enough frequency to adapt, enough rest to recover.` },
      { min: 5, label: `Peak`,   text: `High-output week with full programming. For when you mean business.` },
    ],
    zone_accent:  CORAL,
    zone_on:      CORAL,
    cell_bg:      PANEL,
    card_border:  LINE,
    align:        `center`,
  }) ]) ]),
]));

// ── 7) PRICING ────────────────────────────────────────────────────────────────
// CSS: .cd-plan → PANEL bg, LINE border, radius 14px, padding 32px
//      .cd-plan.feat → CORAL bg (evidenziato centrale)
//      Prezzi: Online €90/mo · 1:1+Online €280/mo (feat) · Squad €140/mo
const plan = (name, price, period, description, features, is_popular, badge_text, cta_text, cta_ghost) => tile('pricing', {
  plan_name: name,
  price,
  currency: '€',
  period,
  description,
  features,
  is_popular,
  badge_text,
  badge_bg_color: is_popular ? CORALD : PANEL,
  bg_color: is_popular ? CORAL : PANEL,
  price_color: WHITE,
  accent_color: is_popular ? WHITE : CORAL,
  cta_text,
  cta_url: '#pricing',
  cta_bg: is_popular ? WHITE : 'transparent',
  cta_color: is_popular ? CORAL : WHITE,
  cta_border: is_popular ? 'transparent' : LINE2,
  border_radius: R(14),
});

home.push(sec(BG2, 'large', [
  row([ col('1-1', [ shead('Pricing', 'Invest in ', 'showing up', `First consult is always free. No lock-in — though most people stay.`) ]) ]),
  row([
    col('1-3', [ plan(
      'Online',
      '90', '/mo',
      'Coaching in your pocket.',
      `Custom programme\nWeekly check-in\nForm reviews`,
      false, '', 'Start online', true,
    ) ]),
    col('1-3', [ plan(
      '1:1 + Online',
      '280', '/mo',
      'The full Cadence experience.',
      `4 studio sessions / mo\nEverything in Online\nPriority scheduling`,
      true, 'Most popular', 'Book consult', false,
    ) ]),
    col('1-3', [ plan(
      'Squad',
      '140', '/mo',
      'Small-group strength.',
      `8 group sessions / mo\nMax 4 per group\nShared programme`,
      false, '', 'Join a squad', true,
    ) ]),
  ], { gap: 16, vertical_align: 'stretch' }),
]));

// ── 8) TESTIMONIAL ───────────────────────────────────────────────────────────
// CSS: .cd-testi q → disp w600 uppercase, em → color CORAL
// quote con accento su "actually keep going."
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('testimonial', {
  quote: `"Theo's the first coach who got me to actually keep going. Strongest I've been in my life."`,
  author_name: `Marcus B.`,
  author_role: '16-week client',
  rating: '0',
  layout: 'single',
  show_line: false,
  bg_color: 'transparent',
  text_color: WHITE,
  border_radius: '0',
  avatar: '',
}) ]) ]) ]));

// ── 9) FINDER — "What's the goal?" ───────────────────────────────────────────
// Blueprint: data-finder, 4 chip (data-finder-opt) + 4 card risultato (data-finder-res).
// CSS inline zona: --fx-zone-accent:#ff6b54 (CORAL) + --fx-zone-on:#fff.
// Card: PANEL bg, LINE border, border-left 3px CORAL, radius 8px, padding 32px 36px.
// Eyebrow: "Match me up" — heading: "What's the goal?"
home.push(sec(BG2, 'large', [
  row([ col('1-1', [ tile('finder', {
    eyebrow: `Match me up`,
    heading: `What’s the goal?`,
    intro: '',
    zone_accent: CORAL,
    zone_on: WHITE,
    card_bg: PANEL,
    card_border: LINE,
    align: 'center',
    items: [
      {
        option: `My first 5K`,
        title: `Couch to 5K`,
        text: `Eight weeks of run-walk intervals that build you up gently — and a coach who texts to make sure you actually go.`,
        meta: `3 runs/week · 8 weeks`,
        cta_text: ``,
        cta_url: `#`,
        icon: `mountain`,
      },
      {
        option: `Chase a PR`,
        title: `Race Sharpening`,
        text: `Threshold work, long runs and tapering, periodised around your goal race date to peak on the day.`,
        meta: `4–5 runs/week · 12 weeks`,
        cta_text: ``,
        cta_url: `#`,
        icon: `trending-up`,
      },
      {
        option: `Build strength`,
        title: `Strength Base`,
        text: `Two lifting sessions and one conditioning day a week to build the engine and bullet-proof the joints.`,
        meta: `3 sessions/week · ongoing`,
        cta_text: ``,
        cta_url: `#`,
        icon: `zap`,
      },
      {
        option: `Just move more`,
        title: `The Consistency Plan`,
        text: `Short, flexible sessions built around your week. The goal isn’t intensity — it’s showing up, again and again.`,
        meta: `2–4 sessions/week · flexible`,
        cta_text: ``,
        cta_url: `#`,
        icon: `clock`,
      },
    ],
  }) ]) ]),
]));

// ── 10) CTA FINALE ───────────────────────────────────────────────────────────
// CSS: .cd-cta → background CORAL, testo bianco, h2 uppercase clamp(44-108px)
// Blueprint: 1 solo bottone "Book your free consult" bianco con testo CORAL
home.push(sec(CORAL, 'large', [ row([ col('1-1', [ tile('cta-banner', {
  headline: `Let's get`,
  headline_accent: 'started.',
  headline_accent_italic: true,
  subtitle: `Book a free, no-pressure consult. We'll talk through your goals and whether Cadence is the right fit.`,
  cta_text: 'Book your free consult',
  cta_url: '#pricing',
  cta2_text: '',
  bg: { type: 'solid', color: CORAL },
  text_color: WHITE,
  accent_color: WHITE,
  subtitle_color: 'rgba(255,255,255,.85)',
  cta_bg: WHITE,
  cta_color: CORAL,
  cta_radius: R(6),
  cta_size: 14,
  headline_font_family: 'sans-serif',
  headline_size: 88,
  headline_weight: '800',
  subtitle_size: 17,
  layout: 'stack',
  vertical_align: 'center',
  banner_radius: R(0),
  banner_padding: 80,
}) ]) ]) ]));

// ── EMIT ─────────────────────────────────────────────────────────────────────
K.emit({
  slug: 'cadence',
  name: 'Cadence',
  tags: ['health', 'fitness', 'coaching', 'personal-trainer'],
  description: `Cadence — Health & Fitness personal coach. Charcoal + coral, Big Shoulders Display (display) + Figtree (body). Hero split, stat strip, programmes, results, process-steps (con card), availability (tile nativo: 7 giorni × 3 fasce, 3 tier Reset/Build/Peak), pricing, testimonial, finder (tile nativo, 4 opzioni), CTA. Riproduzione tile-pure fedele all'OLOtheme Cadence.`,
  colors: {
    primary:           CORAL,
    primary_contrast:  WHITE,
    secondary:         SKY,
    secondary_contrast: INK,
    muted:             BG2,
    muted_contrast:    TXT,
    text:              TXT,
    text_muted:        DIM,
    background:        BG,
    border:            LINE,
    link:              CORAL,
  },
  css_disp:  `"Big Shoulders Display", sans-serif`,
  css_sans:  `"Figtree", -apple-system, sans-serif`,
  heading_weight: '800',
  heading_line_height: '0.96',
  google_fonts: ['Big Shoulders Display', 'Figtree'],
  logo_variant: 'light',
  menu: [
    { title: 'Programmes', url: '#programmes' },
    { title: 'Results',    url: '#results'    },
    { title: 'How it works', url: '#process'  },
    { title: 'Pricing',    url: '#pricing'    },
  ],
  header: {
    bg:         'rgba(26,29,36,.86)',
    text_color: DIM,
    sticky_bg:  'rgba(26,29,36,.92)',
    logo_width: 130,
  },
  footer: {
    bg:        INK,
    headColor: WHITE,
    brand: {
      name:    'Cadence',
      tagline: `1:1 personal training & online coaching that fits your life and gets results.`,
    },
    columns: [
      { title: 'Train',        links: ['Programmes', 'Results', 'How it works', 'Pricing'] },
      { title: 'Coach',        links: ['About Theo', 'The studio', 'Blog'] },
      { title: 'Get in touch', links: ['The Forge Gym, Unit 4', 'train@cadence.coach', '@cadence.coach'] },
    ],
    bottom: {
      left:  `© 2026 Cadence — an OLOtheme demo.`,
      right: 'Built with OLObuild',
    },
  },
  cursor: {
    blend_mode: 'exclusion',
    ring_color: WHITE,
    dot_color:  CORAL,
  },
}, home);
