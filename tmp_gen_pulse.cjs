/* Pulse — Health & Fitness (strength & conditioning studio). Ricomposizione TILE-PURE image-free.
   Palette: dark charcoal + lime #c6f24e. Font: Anton (display) + Barlow (body). */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('pu');

// ── Palette ─────────────────────────────────────────────────────────────────
const BG     = '#14161a';
const BG2    = '#181b20';
const PANEL  = '#1c2026';
const PANEL2 = '#23272f';
const INK    = '#0c0d10';
const LIME   = '#c6f24e';
const LIMED  = '#b2e02f';
const TXT    = '#c7ccd4';
const DIM    = '#8b919c';
const LINE   = 'rgba(255,255,255,.09)';
const LINE2  = 'rgba(255,255,255,.16)';
const WHITE  = '#ffffff';
const LTINT  = 'rgba(198,242,78,.12)';

const home = [];

// ── 1) HERO ──────────────────────────────────────────────────────────────────
// hero-split: image-free; showcase pannello con stat studio
home.push(sec(INK, 'large', [ row([ col('1-1', [ tile('hero-split', {
  eyebrow_text: `Strength & conditioning · Est. 2019`,
  eyebrow_dot_color: LIME,
  eyebrow_color: LIME,
  headline_lines: [
    { text: 'TRAIN', color: WHITE, italic: false },
    { text: 'WITH', color: LIME,  italic: false },
    { text: 'INTENT.', color: WHITE, italic: false },
  ],
  headline_font_family: 'sans-serif',
  headline_font_size: 80,
  headline_line_height: 0.9,
  headline_font_weight: '400',
  headline_align: 'left',
  subhead: `No mirrors-and-selfies gym. Coached classes, honest programming and a room full of people chasing the same thing you are — getting stronger, every week.`,
  subhead_color: TXT,
  subhead_size: 17,
  subhead_italic: false,
  subhead_max_width: 440,
  cta1_text: 'Claim a free session',
  cta1_url: '#membership',
  cta1_bg: LIME,
  cta1_color: INK,
  cta1_size: 13,
  cta1_radius: R(4),
  cta1_radius_hover: R(4),
  cta2_text: 'View timetable',
  cta2_url: '#schedule',
  cta2_bg: 'transparent',
  cta2_color: WHITE,
  cta2_border: LINE2,
  cta2_size: 13,
  cta2_radius: R(4),
  cta2_radius_hover: R(4),
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: PANEL },
  showcase_padding: 28,
  showcase_radius: R(12),
  showcase_radius_hover: R(12),
  showcase_badge_text: 'THE STUDIO · AT A GLANCE',
  showcase_badge_dot: LIME,
  showcase_badge_bg: INK,
  showcase_badge_color: WHITE,
  showcase_items: [
    { number: 'Active members',    text: '640+',       italic: false, text_color: LIME,  bg: { type: 'solid', color: BG2 } },
    { number: 'Classes per week',  text: '52',         italic: false, text_color: WHITE, bg: { type: 'solid', color: BG2 } },
    { number: 'Head coaches',      text: '9',          italic: false, text_color: WHITE, bg: { type: 'solid', color: BG2 } },
    { number: 'Doors open from',   text: '6 AM',       italic: false, text_color: LIME,  bg: { type: 'solid', color: BG2 } },
  ],
  showcase_card_radius: R(8),
  showcase_card_radius_hover: R(8),
  showcase_card_shadow: 'none',
  showcase_caption_left: 'PULSE STUDIO',
  showcase_caption_right: 'EST. 2019',
  showcase_hover_effect: 'none',
  split_ratio: '1.2fr .8fr',
  gap: 52,
  min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
}) ]) ]) ]));

// ── 2) TICKER ─────────────────────────────────────────────────────────────────
// marquee su sfondo lime — testo scuro
home.push(sec(LIME, 'small', [ row([ col('1-1', [ tile('marquee', {
  items: [
    { text: 'Strength' },
    { text: 'Conditioning' },
    { text: 'Olympic lifting' },
    { text: 'Mobility' },
    { text: 'Coached every rep' },
    { text: 'No egos' },
    { text: 'Strength' },
    { text: 'Conditioning' },
    { text: 'Olympic lifting' },
    { text: 'Mobility' },
    { text: 'Coached every rep' },
    { text: 'No egos' },
  ],
  speed: 26,
  font_family: 'sans-serif',
  font_size: 22,
  font_weight: '400',
  text_color: INK,
  separator: '●',
  separator_color: INK,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
}) ]) ]) ]));

// ── 3) STAT STRIP ────────────────────────────────────────────────────────────
// counter ×4: text_color=numero lime, label dim
const stat = (prefix, number, suffix, label) => col('1-4', [ tile('counter', {
  number, suffix, prefix, label,
  icon_emoji: '',
  text_color: LIME,
  number_color: LIME,
  number_font_size: '64',
  number_font_weight: '400',
  label_color: DIM,
  label_font_size: '12',
  bg_type: 'color',
  bg_color: 'transparent',
  padding: '8',
  border_radius: '0',
}) ]);
home.push(sec(INK, 'small', [ row([
  stat('', '640', '+', 'Active members'),
  stat('', '52',  '',  'Classes / week'),
  stat('', '9',   '',  'Head coaches'),
  stat('', '6',   'AM', 'Doors open'),
], { gap: 0 }) ]));

// ── helpers sezione ─────────────────────────────────────────────────────────
const shead = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true,
  eyebrow_text: eyebrow,
  eyebrow_color: LIME,
  eyebrow_dot_color: LIME,
  eyebrow_separator: '',
  headline_lines: [
    { text: l1,     color: WHITE, italic: false },
    { text: accent, color: LIME,  italic: false },
  ],
  headline_font_family: 'sans-serif',
  headline_font_size: 56,
  headline_font_weight: '400',
  headline_align: 'left',
  headline_inline: true,
  tagline_show: !!intro,
  tagline_text: intro || '',
  tagline_text_italic: false,
  tagline_text_color: DIM,
  tagline_text_size: 17,
  layout: 'stack',
  gap: 16,
});
const sheadCenter = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true,
  eyebrow_text: eyebrow,
  eyebrow_color: LIME,
  eyebrow_dot_color: LIME,
  eyebrow_separator: '',
  headline_lines: [
    { text: l1,     color: WHITE, italic: false },
    { text: accent, color: LIME,  italic: false },
  ],
  headline_font_family: 'sans-serif',
  headline_font_size: 56,
  headline_font_weight: '400',
  headline_align: 'center',
  headline_inline: true,
  tagline_show: !!intro,
  tagline_text: intro || '',
  tagline_text_italic: false,
  tagline_text_color: DIM,
  tagline_text_size: 17,
  layout: 'center',
  gap: 16,
});

// ── 4) PROGRAMS ───────────────────────────────────────────────────────────────
// section-header left + process-steps con card (3 programmi)
// .pl-prog: background:var(--panel); border:1px solid var(--line); border-radius:10px; padding:32px 28px
home.push(sec(BG2, 'large', [
  row([ col('1-1', [
    shead('// what we run', 'Three ways to ', 'get strong',
      `Every program is coached, scaled to you, and built on a 12-week block — so you always know why you\`re doing what you\`re doing.`),
  ]) ]),
  row([ col('1-1', [ tile('process-steps', {
    columns: 3,
    gap: 18,
    align: 'left',
    auto_number: false,
    item_gap: 10,
    number_style: 'plain',
    number_color: LIME,
    number_size: 13,
    number_font: 'sans-serif',
    number_weight: '500',
    title_color: WHITE,
    title_size: 30,
    title_font: 'sans-serif',
    title_weight: '400',
    desc_color: DIM,
    desc_size: 14.5,
    card_bg: PANEL,
    card_border: LINE,
    card_radius: R(10),
    card_padding: 32,
    items: [
      {
        number: '01 / Foundations',
        title: `Barbell\nFoundations`,
        description: `Six weeks to own the squat, press, hinge and pull. For first-timers and anyone resetting their technique.\n\nBeginner · Technique · Small group`,
      },
      {
        number: '02 / Engine',
        title: `Engine\nConditioning`,
        description: `Intervals, rowing and mixed-modal work that builds a base you can feel — on the floor and off it.\n\nAll levels · Cardio · 45 min`,
      },
      {
        number: '03 / Platform',
        title: `Platform\nWeightlifting`,
        description: `Snatch and clean & jerk, programmed in blocks with video review. For lifters ready to chase numbers.\n\nAdvanced · Olympic · Programmed`,
      },
    ],
  }) ]) ]),
]));

// ── 5) SCHEDULE (tile dedicato "schedule" — griglia settimanale vera) ────────
// is-lime → cella evidenziata (zone_accent = LIME, testo INK)
// is-fill → cella normale (cell_bg = PANEL2, testo bianco)
// is-out  → open gym / slot libero (nessun prefisso "!")
home.push(sec(BG, 'large', [
  row([ col('1-1', [
    sheadCenter('// this week', 'A wall you can ', 'book from',
      `Reserve your spot in seconds. Full classes show a waitlist — drop in and we'll text you when a place opens.`),
  ]) ]),
  row([ col('1-1', [ tile('schedule', {
    eyebrow: '// this week',
    heading: 'A wall you can book from',
    days: 'Mon, Tue, Wed, Thu, Fri',
    corner_label: 'Time',
    rows: [
      { time: '06:00', cells: '!Engine · Rae | Foundations · Milo | !Engine · Rae | Strength · Devi | !Engine · Rae' },
      { time: '12:15', cells: 'Lunch WOD | Open gym | Lunch WOD | Open gym | Lunch WOD' },
      { time: '18:30', cells: 'Strength · Devi | !Platform · Yuki | Strength · Devi | !Platform · Yuki | Mobility' },
    ],
    zone_accent: LIME,
    zone_on: INK,
    cell_bg: PANEL2,
    card_border: LINE,
    head_color: WHITE,
    align: 'left',
  }) ]) ]),
]));

// ── 6) COACHES ────────────────────────────────────────────────────────────────
// team tiles: avatar cerchio + nome + ruolo (x4), grid 4 col
// .pl-coach: border-radius:10px; border:1px solid var(--line)
// .pl-coach__info b: font Anton uppercase 21px; span: mono 11px lime
const puCoach = (name, role) => col('1-4', [ tile('team', {
  photo: '',
  name,
  role,
  bio: '',
  link_url: '',
  link_text: '',
  photo_size: '120',
  photo_shape: 'circle',
  photo_border_width: '0',
  photo_shadow: 'none',
  photo_gap: '16',
  info_bg_color: 'transparent',
  info_text_color: WHITE,
  role_color: LIME,
  info_align: 'center',
  name_size: '21',
  name_weight: '400',
  role_size: '11',
  bg_color: 'transparent',
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
  border_radius: '0',
}) ]);
home.push(sec(BG2, 'large', [
  row([ col('1-1', [
    shead('// the floor', 'Coached by ', 'people who lift', ''),
  ]) ]),
  row([
    puCoach('Rae Okafor',   'Conditioning lead'),
    puCoach('Devi Sharma',  'Strength lead'),
    puCoach('Yuki Tan',     'Weightlifting'),
    puCoach('Milo Bennett', 'Foundations'),
  ], { gap: 16 }),
]));

// ── 7) MEMBERSHIP / PRICING ───────────────────────────────────────────────────
home.push(sec(BG, 'large', [
  row([ col('1-1', [
    sheadCenter('// membership', 'Pick your ', 'commitment',
      `No joining fee, freeze anytime, cancel with 30 days. First session is always free.`),
  ]) ]),
  row([
    col('1-3', [ tile('pricing', {
      plan_name:   'Drop-in',
      price:       '18',
      currency:    '€',
      period:      '/class',
      description: 'Pay as you train. No strings.',
      features:    `Any single class\nOpen gym access\nBook up to 7 days out`,
      is_popular:  false,
      bg_color:    PANEL,
      text_color:  TXT,
      price_color: WHITE,
      accent_color: LIME,
      cta_text:    'Buy a class',
      cta_url:     '#',
      cta_bg:      'transparent',
      cta_color:   WHITE,
      border_radius: R(12),
    }) ]),
    col('1-3', [ tile('pricing', {
      plan_name:   'Unlimited',
      price:       '96',
      currency:    '€',
      period:      '/month',
      description: 'For people who live here. Train daily.',
      features:    `Unlimited classes & open gym\nPriority booking\nFree InBody scan / quarter\nBring-a-friend passes`,
      is_popular:  true,
      badge_text:  'Most chosen',
      badge_bg_color: INK,
      bg_color:    LIME,
      text_color:  INK,
      price_color: INK,
      accent_color: INK,
      cta_text:    'Start free session',
      cta_url:     '#',
      cta_bg:      INK,
      cta_color:   WHITE,
      border_radius: R(12),
    }) ]),
    col('1-3', [ tile('pricing', {
      plan_name:   '10-Pack',
      price:       '150',
      currency:    '€',
      period:      '/10 classes',
      description: 'Flexible block, valid four months.',
      features:    `10 classes, your pace\nShareable with a partner\nOpen gym included`,
      is_popular:  false,
      bg_color:    PANEL,
      text_color:  TXT,
      price_color: WHITE,
      accent_color: LIME,
      cta_text:    'Buy a pack',
      cta_url:     '#',
      cta_bg:      'transparent',
      cta_color:   WHITE,
      border_radius: R(12),
    }) ]),
  ], { gap: 16, vertical_align: 'stretch' }),
]));

// ── 8) TESTIMONIAL ────────────────────────────────────────────────────────────
home.push(sec(INK, 'large', [ row([ col('1-1', [ tile('testimonial', {
  quote: `“I came in to lose a bit of weight. Two years later I’m deadlifting double bodyweight and it’s the best room I’ve ever trained in.”`,
  author_name: 'Hannah V.',
  author_role: 'member since 2023',
  rating: '0',
  layout: 'single',
  show_line: false,
  bg_color: 'transparent',
  text_color: WHITE,
  border_radius: '0',
  avatar: '',
}) ]) ]) ]));

// ── 9) GOAL FINDER ────────────────────────────────────────────────────────────
// finder tile: chip opzione → result card. zone_accent=LIME, zone_on=INK.
home.push(sec(BG2, 'large', [
  row([ col('1-1', [ tile('finder', {
    eyebrow: `Where do we start?`,
    heading: `Pick a goal`,
    intro: '',
    zone_accent: LIME,
    zone_on: INK,
    card_bg: PANEL,
    card_border: LINE2,
    align: 'center',
    items: [
      {
        option: `Get strong`,
        title: `Barbell Strength`,
        text: `Three heavy sessions a week — squat, press, pull. Linear progression with a coach checking every lift.`,
        meta: `3\xD7/week \xB7 60 min \xB7 all levels`,
        cta_text: ``,
        cta_url: `#`,
        icon: ``,
      },
      {
        option: `Lean out`,
        title: `Conditioning & Cut`,
        text: `Metabolic circuits and intervals that keep the intensity high and the clock short. Nutrition guidance included.`,
        meta: `4\xD7/week \xB7 45 min \xB7 intermediate`,
        cta_text: ``,
        cta_url: `#`,
        icon: ``,
      },
      {
        option: `Build an engine`,
        title: `Engine Builder`,
        text: `Rowing, bike and run intervals to push your VO₂ and make everything else feel easier. Bring a towel.`,
        meta: `3\xD7/week \xB7 50 min \xB7 intermediate+`,
        cta_text: ``,
        cta_url: `#`,
        icon: ``,
      },
      {
        option: `Get back into it`,
        title: `Foundations`,
        text: `A patient on-ramp — movement, mobility and base strength — to rebuild the habit without wrecking you on day one.`,
        meta: `2–3\xD7/week \xB7 45 min \xB7 beginner`,
        cta_text: ``,
        cta_url: `#`,
        icon: ``,
      },
    ],
  }) ]) ]),
]));

// ── 10) CTA ───────────────────────────────────────────────────────────────────
home.push(sec(LIME, 'large', [ row([ col('1-1', [ tile('cta-banner', {
  headline:              'First session',
  headline_accent:       'is on us.',
  headline_accent_italic: false,
  subtitle:             `Book it, show up, train with a coach. No pressure, no contract until you’re sure.`,
  cta_text:             'Claim your free session',
  cta_url:              '#membership',
  bg:                   { type: 'solid', color: LIME },
  text_color:           INK,
  accent_color:         INK,
  subtitle_color:       'rgba(12,13,16,.78)',
  cta_bg:               INK,
  cta_color:            WHITE,
  cta_radius:           R(4),
  cta_size:             14,
  headline_font_family: 'sans-serif',
  headline_size:        80,
  headline_weight:      '400',
  subtitle_size:        17,
  layout:               'stack',
  vertical_align:       'center',
  banner_radius:        R(0),
  banner_padding:       80,
}) ]) ]) ]));

// ── K.emit ─────────────────────────────────────────────────────────────────
K.emit({
  slug:        'pulse',
  name:        'Pulse',
  tags:        ['fitness', 'gym', 'health', 'sport', 'strength'],
  description: `Pulse — strength & conditioning studio. Charcoal dark + lime #c6f24e, Anton (display) + Barlow (body). Riproduzione fedele dell'OLOtheme Pulse (Health & Fitness).`,
  colors: {
    primary:           LIME,
    primary_contrast:  INK,
    secondary:         LIMED,
    secondary_contrast: INK,
    muted:             BG2,
    muted_contrast:    TXT,
    text:              TXT,
    text_muted:        DIM,
    background:        BG,
    border:            LINE,
    link:              LIME,
  },
  css_disp:            `"Anton", "Arial Narrow", sans-serif`,
  css_sans:            `"Barlow", -apple-system, sans-serif`,
  heading_weight:      '400',
  heading_line_height: '0.94',
  google_fonts:        ['Anton', 'Barlow'],
  logo_variant:        'light',
  menu: [
    { title: 'Programs',   url: '#programs'  },
    { title: 'Schedule',   url: '#schedule'  },
    { title: 'Coaches',    url: '#coaches'   },
    { title: 'Membership', url: '#membership'},
  ],
  header: {
    bg:         'rgba(20,22,26,.84)',
    text_color: DIM,
    sticky_bg:  'rgba(20,22,26,.92)',
    logo_width: 130,
  },
  footer: {
    bg:        INK,
    headColor: WHITE,
    brand: {
      name:    'Pulse',
      tagline: `Strength & conditioning studio. Coached classes, honest programming, no egos.`,
    },
    columns: [
      { title: 'Train',  links: ['Programs', 'Schedule', 'Coaches', 'Membership'] },
      { title: 'Studio', links: ['About us', 'The space', 'Careers', 'Contact']   },
      { title: 'Visit',  links: ['14 Forge Lane', 'Open 6am–9pm', 'hello@pulse.studio'] },
    ],
    bottom: {
      left:  `© 2026 Pulse — an OLOtheme demo.`,
      right: 'Built with OLObuild',
    },
  },
  cursor: {
    blend_mode: 'exclusion',
    ring_color: '#ffffff',
    dot_color:  '#ffffff',
  },
}, home);
