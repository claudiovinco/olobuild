/* Sterling — ricomposizione TILE-PURE (image-free). Consulting & Finance — wealth management.
   Dark forest green + gold. EB Garamond (display) + Outfit (sans). */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('st');

// ── PALETTE (da :root sterling.css) ─────────────────────────────────────────
const BG    = '#14241c';
const BG2   = '#172a20';
const PANEL = '#1d3328';
const PANEL2= '#254032';
const INK   = '#0c1812';
const GOLD  = '#c9a44a';
const GOLDL = '#d8b766';
const SAGE  = '#8fae8f';
const CREAM = '#eef2e9';
const TXT   = '#aebcae';
const DIM   = '#74866f';
const LINE  = 'rgba(238,242,233,.13)';
const LINE2 = 'rgba(201,164,74,.42)';
const GTINT = 'rgba(201,164,74,.14)';
const WHITE = '#ffffff';

const home = [];

// ─── 1) HERO + FIGURES PANEL (showcase) ─────────────────────────────────────
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('hero-split', {
  eyebrow_text: 'Independent wealth management',
  eyebrow_dot_color: GOLD,
  eyebrow_color: GOLD,
  headline_lines: [
    { text: 'Wealth, managed', color: CREAM, italic: false },
    { text: 'with patience.', color: GOLD,  italic: true  },
  ],
  headline_font_family: 'serif',
  headline_font_size: 72,
  headline_line_height: 1.1,
  headline_font_weight: '500',
  headline_align: 'left',
  subhead: `For three decades we’ve looked after the families and founders who’d rather think in decades than quarters. Independent, unhurried, and quietly thorough.`,
  subhead_color: TXT,
  subhead_size: 18,
  subhead_italic: false,
  subhead_max_width: 520,
  cta1_text: 'Arrange a conversation',
  cta1_url: '#contact',
  cta1_bg: GOLD,
  cta1_color: INK,
  cta1_size: 13,
  cta1_radius: R(4),
  cta1_radius_hover: R(4),
  cta2_text: 'Our services',
  cta2_url: '#services',
  cta2_bg: 'transparent',
  cta2_color: CREAM,
  cta2_border: LINE2,
  cta2_size: 13,
  cta2_radius: R(4),
  cta2_radius_hover: R(4),
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: PANEL },
  showcase_padding: 28,
  showcase_radius: R(14),
  showcase_radius_hover: R(14),
  showcase_badge_text: 'THE FIRM, IN FIGURES',
  showcase_badge_dot: GOLD,
  showcase_badge_bg: INK,
  showcase_badge_color: CREAM,
  showcase_items: [
    { number: 'Assets under care',     text: '£4.2bn', italic: false, text_color: GOLD,  bg: { type: 'solid', color: BG2 } },
    { number: 'Client families',       text: '320',         italic: false, text_color: CREAM, bg: { type: 'solid', color: BG2 } },
    { number: 'Avg. relationship',     text: '14 yrs',      italic: false, text_color: CREAM, bg: { type: 'solid', color: BG2 } },
    { number: 'Independent since',     text: '1994',        italic: false, text_color: CREAM, bg: { type: 'solid', color: BG2 } },
  ],
  showcase_card_radius: R(11),
  showcase_card_radius_hover: R(11),
  showcase_card_shadow: 'none',
  showcase_caption_left: 'STERLING',
  showcase_caption_right: 'EST. 1994',
  showcase_hover_effect: 'none',
  split_ratio: '1.25fr .75fr',
  gap: 52,
  min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
}) ]) ]) ]));

// ─── 2) TRUST STRIP (logo cloud placeholder) ────────────────────────────────
home.push(sec(BG2, 'small', [
  row([ col('1-1', [ tile('trust-strip', {
    items: [
      { text: 'CAVENDISH' },
      { text: 'NORTHGATE' },
      { text: 'AILSWORTH' },
      { text: 'THORNBURY' },
      { text: 'BEXCELL & SONS' },
    ],
    variant: 'pill',
    separator_char: '',
    align: 'center',
    flow: 'wrap',
    gap: 16,
    font_family: 'sans-serif',
    text_color: DIM,
    text_size: 13,
    pill_bg: 'rgba(238,242,233,.06)',
    pill_border: LINE,
    pill_text_color: DIM,
  }) ]) ], { gap: 16 }),
]));

// ─── 3) SERVICES — CardGrid (section-header centrato + info-cards 3 col) ─────
home.push(sec(BG, 'large', [
  row([ col('1-1', [ tile('section-header', {
    eyebrow_show: true,
    eyebrow_text: 'What we do',
    eyebrow_color: GOLD,
    eyebrow_dot_color: GOLD,
    eyebrow_separator: '',
    headline_lines: [
      { text: 'Looking after the', color: CREAM, italic: false },
      { text: 'whole picture',     color: GOLD,  italic: true  },
    ],
    headline_font_family: 'serif',
    headline_font_size: 48,
    headline_font_weight: '500',
    headline_align: 'center',
    headline_inline: true,
    tagline_show: false,
    layout: 'center',
    gap: 16,
  }) ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0,
    container_gap: 16,
    columns: 3,
    items_gap: 16,
    card_bg: { type: 'solid', color: PANEL },
    card_color: DIM,
    card_radius: R(12),
    card_padding: 30,
    show_icon: true,
    show_counter: false,
    show_arrow: false,
    show_footer: false,
    show_media: false,
    icon_color: GOLD,
    icon_bg_color: GTINT,
    title_color: CREAM,
    title_font_family: 'serif',
    title_size: 24,
    title_weight: '500',
    title_italic: false,
    description_size: 14.5,
    items: [
      {
        icon: 'trending-up',
        title: 'Investment',
        description: 'Discretionary portfolios built around your goals, risk and time horizon — not a model off the shelf.',
      },
      {
        icon: 'calculator',
        title: 'Planning',
        description: 'Cashflow, tax and retirement modelled together, so every decision is made with the full view.',
      },
      {
        icon: 'shield-check',
        title: 'Legacy',
        description: 'Estate, succession and the next generation — structured so what you build lasts beyond you.',
      },
    ],
    card_hover_effect: 'lift',
  }) ]) ]),
]));

// ─── 4) APPROACH — ProcessSteps (borderless, numeri oro) ────────────────────
home.push(sec(BG2, 'large', [
  row([ col('1-1', [ tile('section-header', {
    eyebrow_show: true, eyebrow_text: 'Our approach', eyebrow_color: GOLD, eyebrow_dot_color: GOLD, eyebrow_separator: '',
    headline_lines: [ { text: 'Considered,', color: CREAM, italic: false }, { text: 'every step', color: GOLD, italic: true } ],
    headline_font_family: 'serif', headline_font_size: 48, headline_font_weight: '500', headline_align: 'center', headline_inline: true,
    tagline_show: false, layout: 'center', gap: 16,
  }) ]) ]),
  row([ col('1-1', [ tile('process-steps', {
    columns: 4, gap: 16, align: 'left', auto_number: false, item_gap: 10,
    number_style: 'plain', number_color: GOLD, number_size: 40, number_font: 'serif', number_weight: '500',
    title_color: CREAM, title_size: 21, title_font: 'serif', title_weight: '600',
    desc_color: DIM, desc_size: 14,
    card_bg: '', card_border: '', card_padding: 0,
    items: [
      { number: '01', title: 'Listen', description: `We start with your life, not your balance sheet. What's it all for?` },
      { number: '02', title: 'Plan', description: 'A clear strategy, modelled and stress-tested against the things that worry you.' },
      { number: '03', title: 'Invest', description: `Patient, diversified, low-cost where it counts — and always explained.` },
      { number: '04', title: 'Review', description: 'We meet regularly, adjust as life changes, and never go quiet.' },
    ],
  }) ]) ]),
]));

// ─── 5) TEAM — section-header + 4 team tiles (avatar circolare + nome + ruolo) ─
const stMember = (name, role) => col('1-4', [ tile('team', {
  photo: '', name, role, bio: '', link_url: '', link_text: '',
  photo_size: '120', photo_shape: 'circle', photo_border_width: '0', photo_shadow: 'none', photo_gap: '16',
  info_bg_color: 'transparent', info_text_color: CREAM, role_color: GOLD, info_align: 'center',
  name_size: '21', name_weight: '600', role_size: '13',
  bg_color: 'transparent', tile_padding: { top: 0, right: 0, bottom: 0, left: 0 }, border_radius: '0',
}) ]);
home.push(sec(BG, 'large', [
  row([ col('1-1', [ tile('section-header', {
    eyebrow_show: true, eyebrow_text: 'Our people', eyebrow_color: GOLD, eyebrow_dot_color: GOLD, eyebrow_separator: '',
    headline_lines: [ { text: 'The same faces,', color: CREAM, italic: false }, { text: 'year after year', color: GOLD, italic: true } ],
    headline_font_family: 'serif', headline_font_size: 48, headline_font_weight: '500', headline_align: 'center', headline_inline: true,
    tagline_show: false, layout: 'center', gap: 16,
  }) ]) ]),
  row([ stMember('Eleanor Vance', 'Managing partner'), stMember('James Okonkwo', 'Head of planning'), stMember('Priya Anand', 'Investment director'), stMember('Hugo Bexcell', 'Estates & legacy') ], { gap: 16 }),
]));

// ─── 6) TESTIMONIAL QUOTE ────────────────────────────────────────────────────
home.push(sec(BG2, 'large', [ row([ col('1-1', [ tile('testimonial', {
  quote: `“They talked me out of two things I’d have regretted, and into one I’m grateful for. That’s the whole point of an adviser.”`,
  author_name: 'Client family',
  author_role: `with Sterling since 2009`,
  rating: '0',
  layout: 'single',
  show_line: false,
  bg_color: 'transparent',
  text_color: CREAM,
  border_radius: '0',
  avatar: '',
}) ]) ]) ]));

// ─── 7) PROJECTOR (compound growth slider) ───────────────────────────────────
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('projector', {
  eyebrow: 'A quiet illustration',
  heading: 'Patience <em>compounds</em>',
  intro: `Set what you’d put away each year. This is what it could become over twenty years at a steady 6% — the whole case for starting now, and then leaving it well alone.`,
  min: '2000',
  max: '50000',
  step: '1000',
  value: '12000',
  rate: '0.06',
  years: '20',
  currency: '£',
  input_label: 'Invested each year',
  out_caption: 'Projected after 20 years',
  note: 'Illustrative only — 6% p.a., contributions at year-end. Capital at risk; past performance is not a guide to the future.',
  show_contrib: true,
  zone_accent: GOLD,
  align: 'left',
  tile_padding: { top: 52, right: 52, bottom: 52, left: 52 },
  border_radius: '16',
  shadow: 'sm',
}) ]) ]) ]));

// ─── 8) CTA FINALE ───────────────────────────────────────────────────────────
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('cta-banner', {
  headline: `Let’s talk about`,
  headline_accent: `what’s next`,
  headline_accent_italic: true,
  subtitle: `An initial conversation is complimentary and entirely without obligation. We’ll tell you honestly if we’re the right fit.`,
  cta_text: 'Arrange a call',
  cta_url: '#contact',
  cta2_text: 'Our services', cta2_url: '#services', cta2_bg: 'transparent', cta2_color: CREAM, cta2_border: LINE2,
  bg: { type: 'solid', color: INK },
  text_color: CREAM,
  accent_color: GOLD,
  subtitle_color: TXT,
  cta_bg: GOLD,
  cta_color: INK,
  cta_radius: R(4),
  cta_size: 13,
  headline_font_family: 'serif',
  headline_size: 52,
  headline_weight: '500',
  subtitle_size: 17,
  layout: 'stack',
  vertical_align: 'center',
  banner_radius: R(18),
  banner_padding: 80,
}) ]) ]) ]));

// ─── EMIT ─────────────────────────────────────────────────────────────────────
K.emit({
  slug: 'sterling',
  name: 'Sterling',
  tags: ['consulting', 'finance', 'wealth-management', 'private-bank'],
  description: 'Sterling — private wealth management. Forest green + gold, EB Garamond (display) + Outfit. Sezioni: Hero, Trust strip, Servizi, Approccio, Team, Testimonial, Projector, CTA. Riproduzione fedele dell’OLOtheme Sterling.',
  colors: {
    primary:           GOLD,
    primary_contrast:  INK,
    secondary:         SAGE,
    secondary_contrast: CREAM,
    muted:             BG2,
    muted_contrast:    TXT,
    text:              TXT,
    text_muted:        DIM,
    background:        BG,
    border:            LINE,
    link:              GOLDL,
  },
  css_disp: `"EB Garamond", Georgia, serif`,
  css_sans: `"Outfit", -apple-system, sans-serif`,
  heading_weight: '500',
  heading_line_height: '1.12',
  google_fonts: ['EB Garamond', 'Outfit'],
  logo_variant: 'light',
  menu: [
    { title: 'Services',    url: '#services'  },
    { title: 'Approach',    url: '#approach'  },
    { title: 'Our people',  url: '#team'      },
    { title: 'Contact',     url: '#contact'   },
  ],
  header: {
    bg: 'rgba(20,36,28,.86)',
    text_color: TXT,
    sticky_bg: 'rgba(20,36,28,.92)',
    logo_width: 138,
  },
  footer: {
    bg: BG2,
    headColor: CREAM,
    brand: {
      name: 'Sterling',
      tagline: 'Independent private wealth management for families and founders, since 1994.',
    },
    columns: [
      { title: 'Firm',    links: ['Services', 'Approach', 'Our people', 'Insights'] },
      { title: 'Clients', links: ['Arrange a call', 'Client login', 'Fees']         },
      { title: 'Contact', links: ['1 Sterling Court', '+44 20 7946 00', 'hello@sterling.partners'] },
    ],
    bottom: {
      left:  '© 2026 Sterling — an OLOtheme demo.',
      right: 'Sterling Partners is authorised and regulated for the purposes of this fictional demo. Capital at risk.',
    },
  },
  cursor: {
    blend_mode: 'exclusion',
    ring_color:  '#ffffff',
    dot_color:   GOLD,
  },
}, home);
