/* Meridian — ricomposizione TILE-PURE (image-free). Consulting & Finance.
   Navy + brass + cream. Spectral (display serif) + Work Sans (body). */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('md');

// ── Palette ──────────────────────────────────────────────────────────────────
const NAVY    = '#16263d';
const NAVY2   = '#1e3350';
const NAVY3   = '#2a456a';
const BRASS   = '#caa45a';
const BRASSD  = '#b58e44';
const CREAM   = '#f5f2ea';
const CREAM2  = '#ece7da';
const PAPER   = '#ffffff';
const TXT     = '#1b2433';
const TXTSOFT = '#54607a';
const TXTFAINT= '#8a91a3';
const LINE    = '#e4e1d6';
const LINEDK  = 'rgba(255,255,255,.13)';
const WHITE   = '#ffffff';
const BRASST  = 'rgba(202,164,90,.14)';

const home = [];

// ── helpers ──────────────────────────────────────────────────────────────────
const shead = (eyebrow, l1, accent, intro, align) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: BRASSD, eyebrow_dot_color: BRASSD, eyebrow_separator: '',
  headline_lines: [
    { text: l1, color: (align === 'left' ? TXT : TXT), italic: false },
    { text: accent, color: BRASSD, italic: true },
  ],
  headline_font_family: 'serif', headline_font_size: 46, headline_font_weight: '600',
  headline_align: align || 'center', headline_inline: true,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false,
  tagline_text_color: TXTSOFT, tagline_text_size: 16.5,
  layout: align === 'left' ? 'stack' : 'center', gap: 16,
});

const caption = (txt) => tile('section-header', {
  eyebrow_show: false,
  headline_lines: [{ text: txt, color: TXTFAINT, italic: false }],
  headline_font_family: 'sans-serif', headline_font_size: 12, headline_font_weight: '600',
  headline_align: 'center', tagline_show: false, layout: 'center', gap: 0,
});

// ── 1) HERO (hero-split, image-free con showcase stats) ──────────────────────
// Blueprint: "Clear thinking\nfor <em>complex</em> decisions."
// italic solo su "complex" → riga 2 split: "for " + accent italic "complex" non
// supportato inline; approssimazione fedele: r1=plain, r2=italic accent
home.push(sec(NAVY, 'large', [row([col('1-1', [tile('hero-split', {
  eyebrow_text: 'Independent advisory · since 2004',
  eyebrow_dot_color: BRASS, eyebrow_color: BRASS,
  headline_lines: [
    { text: 'Clear thinking', color: WHITE, italic: false },
    { text: 'for complex decisions.', color: WHITE, italic: true },
  ],
  headline_font_family: 'serif', headline_font_size: 68, headline_line_height: 1.04,
  headline_font_weight: '600', headline_align: 'left',
  subhead: `Meridian helps boards and management teams cut through noise — on strategy, capital and growth — and act with conviction.`,
  subhead_color: 'rgba(255,255,255,.72)', subhead_size: 18, subhead_italic: false, subhead_max_width: 480,
  cta1_text: 'Book a consultation', cta1_url: '#contact',
  cta1_bg: BRASS, cta1_color: NAVY, cta1_size: 15, cta1_radius: R(4), cta1_radius_hover: R(4),
  cta2_text: 'Our services', cta2_url: '#services',
  cta2_bg: 'rgba(255,255,255,.06)', cta2_color: WHITE,
  cta2_border: 'rgba(255,255,255,.3)', cta2_size: 15, cta2_radius: R(4), cta2_radius_hover: R(4),
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: NAVY2 },
  showcase_padding: 28, showcase_radius: R(10), showcase_radius_hover: R(10),
  showcase_badge_text: 'MERIDIAN · AT A GLANCE',
  showcase_badge_dot: BRASS, showcase_badge_bg: NAVY3, showcase_badge_color: WHITE,
  showcase_items: [
    { number: 'Transactions advised', text: '€18B+', italic: false, text_color: BRASS,  bg: { type: 'solid', color: NAVY3 } },
    { number: 'Years independent',    text: '20',    italic: false, text_color: WHITE, bg: { type: 'solid', color: NAVY3 } },
    { number: 'Engagements delivered',text: '240+',  italic: false, text_color: WHITE, bg: { type: 'solid', color: NAVY3 } },
    { number: 'Repeat and referral',  text: '92%',   italic: false, text_color: WHITE, bg: { type: 'solid', color: NAVY3 } },
  ],
  showcase_card_radius: R(8), showcase_card_radius_hover: R(8), showcase_card_shadow: 'none',
  showcase_caption_left: 'FIRM PROFILE', showcase_caption_right: 'SINCE 2004',
  showcase_hover_effect: 'none',
  split_ratio: '1.1fr .9fr', gap: 48, min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
})])])]));

// ── 2) LOGO STRIP (caption + trust-strip) ────────────────────────────────────
home.push(sec(NAVY2, 'small', [
  row([col('1-1', [caption('Trusted by leaders across sectors')])]),
  row([col('1-1', [tile('trust-strip', {
    items: [
      { text: 'CALDERA GROUP' },
      { text: 'VANTAGE CAPITAL' },
      { text: 'NORDMEER AG' },
      { text: 'BRIGHTFIELD' },
      { text: 'ORVANA' },
    ],
    variant: 'pill', separator_char: '', align: 'center', flow: 'wrap', gap: 16,
    font_family: 'sans-serif', text_color: 'rgba(255,255,255,.5)', text_size: 13,
    pill_bg: 'rgba(255,255,255,.08)', pill_border: LINEDK, pill_text_color: 'rgba(255,255,255,.5)',
  })])], { gap: 16 }),
]));

// ── 3) SERVICES (section-header + info-cards 3×2 con icone — bg:CREAM) ───────
// CSS: .svc { background:var(--paper); border:1px solid var(--line); border-radius:8px; padding:30px }
// icon bg = var(--navy) (#16263d), icon color = var(--brass), titolo serif 23px
home.push(sec(CREAM, 'large', [
  row([col('1-1', [shead('What we do', 'Advisory across the ', 'whole arc', '', 'left')])]),
  row([col('1-1', [tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0, container_gap: 20, columns: 3, items_gap: 20,
    card_bg: { type: 'solid', color: PAPER },
    card_border: LINE,
    card_color: TXTSOFT, card_radius: R(8), card_padding: 30,
    show_icon: true, show_counter: false, show_arrow: true,
    show_footer: false, show_media: false,
    icon_color: BRASS, icon_bg_color: NAVY,
    icon_size: 23,
    title_color: TXT, title_font_family: 'serif', title_size: 23,
    title_weight: '600', title_italic: false, description_size: 14.5,
    card_hover_effect: 'lift',
    items: [
      { icon: 'trending-up', title: 'Corporate strategy',
        description: 'Where to play and how to win — turned into a plan your team can actually execute.' },
      { icon: 'dollar-sign', title: 'M&A advisory',
        description: 'Buy-side, sell-side and integration — disciplined process, fewer surprises.' },
      { icon: 'layout', title: 'Capital & funding',
        description: 'Structure the balance sheet for the next phase, not the last one.' },
      { icon: 'zap', title: 'Performance',
        description: 'Margin, cost and operating model work that holds after we leave.' },
      { icon: 'layers', title: 'Transformation',
        description: 'Complex change, sequenced and governed so it actually lands.' },
      { icon: 'shield-check', title: 'Risk & governance',
        description: 'See round corners — and put the controls in before you need them.' },
    ],
  })])]),
]));

// ── 4) PROCESS — process-steps (borderless, top-rule, numeri brass serif) ────
// CSS: .mr-proc { background:var(--navy) }
// .proc { position:relative; padding-top:30px }
// .proc::before { top:8px; height:1px; background:rgba(255,255,255,.16) } ← top-rule
// .proc__n { font-family:var(--disp); font-size:15px; color:var(--brass); position:absolute; top:-8px }
// .proc h3 { font-size:21px }
// .proc p { color:rgba(255,255,255,.62); font-size:14px }
// → borderless (card_bg:'', card_border:'', card_padding:0) è il pattern giusto
// → number_size:15 corrisponde a .proc__n font-size:15px
home.push(sec(NAVY, 'large', [
  row([col('1-1', [tile('section-header', {
    eyebrow_show: true, eyebrow_text: 'How we work',
    eyebrow_color: BRASS, eyebrow_dot_color: BRASS, eyebrow_separator: '',
    headline_lines: [
      { text: 'A method,', color: WHITE, italic: false },
      { text: 'not a deck', color: WHITE, italic: false },
    ],
    headline_font_family: 'serif', headline_font_size: 46, headline_font_weight: '600',
    headline_align: 'left', tagline_show: false, layout: 'stack', gap: 12,
  })])]),
  row([col('1-1', [tile('process-steps', {
    columns: 4, gap: 24, align: 'left',
    auto_number: false, item_gap: 10,
    number_style: 'plain',
    number_color: BRASS, number_size: 15,
    number_font: 'serif', number_weight: '600',
    title_color: WHITE, title_size: 21, title_font: 'serif', title_weight: '600',
    desc_color: 'rgba(255,255,255,.62)', desc_size: 14,
    card_bg: '', card_border: '', card_padding: 0,
    items: [
      { number: '01', title: 'Frame',
        description: 'We get to the real question before we touch the spreadsheet.' },
      { number: '02', title: 'Diagnose',
        description: 'Evidence over opinion — what the numbers and the market actually say.' },
      { number: '03', title: 'Decide',
        description: `Clear options, honest trade-offs, a recommendation we'll defend.` },
      { number: '04', title: 'Deliver',
        description: `We stay until it's working, not just until the report ships.` },
    ],
  })])]),
]));

// ── 5) STAT STRIP (counter ×4) ───────────────────────────────────────────────
// CSS: .mr-stats { border-block:1px solid var(--line); background:var(--paper) }
// .mr-stats b { font-family:var(--disp); font-size:clamp(38px,5vw,58px); color:var(--navy) }
// .mr-stats b .u { color:var(--brass-d) }  ← i suffissi in brass
// .mr-stats span { font-size:13px; color:var(--txt-faint) }
const stat = (prefix, number, suffix, label) => col('1-4', [tile('counter', {
  number, suffix, prefix, label, icon_emoji: '',
  text_color: NAVY, number_color: NAVY,
  accent_color: BRASSD,
  number_font_size: '54', number_font_weight: '600',
  number_font_family: 'serif',
  label_color: TXTFAINT, label_font_size: '13',
  bg_type: 'color', bg_color: 'transparent', padding: '8', border_radius: '0',
})]);

home.push(sec(PAPER, 'small', [row([
  stat('',   '20',  '',   'Years independent'),
  stat('€', '18',  'B+', 'Transactions advised'),
  stat('',   '240', '+',  'Engagements delivered'),
  stat('',   '92',  '%',  'Repeat & referral'),
], { gap: 24 })]));

// ── 6) CASE STUDIES (section-header + info-cards 2 col — bg:CREAM) ───────────
// CSS: .case { border-radius:10px } — sfondo CREAM della sezione
// Cards sono le overlay media panels (image-free: card navy con veil gradient)
// approssimazione: info-cards con badge category + titolo serif + descrizione
home.push(sec(CREAM, 'large', [
  row([col('1-1', [shead('Selected work', 'Outcomes, not ', 'slideware', '', 'left')])]),
  row([col('1-1', [tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0, container_gap: 22, columns: 2, items_gap: 22,
    card_bg: { type: 'solid', color: PAPER },
    card_border: LINE,
    card_color: TXTSOFT, card_radius: R(10), card_padding: 32,
    show_icon: false, show_counter: true, show_counter_label: true,
    show_arrow: false, show_footer: true, show_media: false,
    counter_shape: 'square', counter_color: BRASS, counter_bg: NAVY, counter_size: 18,
    title_color: TXT, title_font_family: 'serif', title_size: 22,
    title_weight: '600', title_italic: false, description_size: 14.5, footer_size: 12,
    card_hover_effect: 'lift',
    items: [
      {
        counter: 'IN', counter_label: 'Industrials · Carve-out',
        title: `A €600M divestiture, closed in seven months`,
        description: `Buy-side tension, a tight timeline, and a clean separation that held post-close.`,
        footer_text: 'Carve-out Advisory', footer_dot_color: BRASS,
      },
      {
        counter: 'FS', counter_label: 'Financial services · Strategy',
        title: 'From regional player to national platform',
        description: `A three-year roadmap that doubled the franchise without diluting the brand.`,
        footer_text: 'Corporate Strategy', footer_dot_color: BRASS,
      },
    ],
  })])]),
]));

// ── 7) TESTIMONIAL ───────────────────────────────────────────────────────────
// CSS: .mr-testi { background:var(--navy-2) } .mr-testi q { font-style:italic; font-size:38px }
// .mr-testi__by b { color:var(--brass) }
home.push(sec(NAVY2, 'large', [row([col('1-1', [tile('testimonial', {
  quote: `"Meridian told us what we needed to hear, not what we wanted to. That's exactly why we keep calling them back."`,
  author_name: 'Margaret Hale', author_role: 'Chair, Caldera Group', rating: '0',
  layout: 'single', show_line: false,
  bg_color: 'transparent', text_color: WHITE, border_radius: '0', avatar: '',
})])])]));

// ── 8) INSIGHTS (section-header + info-cards articoli 3 col — bg:CREAM) ──────
// CSS: .ins { } .ins__cat { color:var(--brass-d) } .ins h3 { font-size:21px }
// .ins .byline { color:var(--txt-faint) }
home.push(sec(CREAM, 'large', [
  row([col('1-1', [shead('Insights', 'Sharper thinking, ', 'shared', '', 'left')])]),
  row([col('1-1', [tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0, container_gap: 24, columns: 3, items_gap: 24,
    card_bg: { type: 'solid', color: PAPER },
    card_border: LINE,
    card_color: TXTFAINT, card_radius: R(8), card_padding: 26,
    show_icon: false, show_counter: false, show_counter_label: false,
    show_arrow: false, show_footer: true, show_media: false,
    title_color: TXT, title_font_family: 'serif', title_size: 21,
    title_weight: '600', title_italic: false, description_size: 11, footer_size: 12.5,
    card_hover_effect: 'lift',
    items: [
      {
        title: 'The cost of capital just reset. Now what?',
        description: 'Capital markets',
        footer_text: 'By J. Okafor · 7 min read', footer_dot_color: BRASSD,
      },
      {
        title: 'Why most growth strategies stall at year two',
        description: 'Strategy',
        footer_text: 'By A. Lindqvist · 9 min read', footer_dot_color: BRASSD,
      },
      {
        title: 'The integration mistakes that erase the premium',
        description: 'M&A',
        footer_text: 'By R. Bianchi · 6 min read', footer_dot_color: BRASSD,
      },
    ],
  })])]),
]));

// ── 9) FINDER — tile `finder` (chip → result-card interattivo)
// Blueprint: zone_accent=#caa45a (brass), zone_on=#16263d (navy), card_bg=paper, card_border=line
// 4 opzioni: sell / grow / capital / turn
home.push(sec(CREAM2, 'large', [
  row([col('1-1', [tile('finder', {
    eyebrow: 'Where to start',
    heading: `What\`s the decision?`,
    intro: '',
    zone_accent: BRASS,
    zone_on: NAVY,
    card_bg: PAPER,
    card_border: LINE,
    align: 'center',
    items: [
      {
        option: 'Selling the company',
        title: 'Exit & Sell-side Advisory',
        text: 'Positioning, valuation and a tightly-run process to the right buyer — so you negotiate from strength and close without surprises.',
        meta: 'Typically 4–7 months',
        cta_text: '',
        cta_url: '#',
        icon: '',
      },
      {
        option: 'Scaling growth',
        title: 'Growth Strategy',
        text: 'Where the next phase of growth really comes from, what to stop doing, and a plan the management team will actually run.',
        meta: 'Typically 6–10 weeks',
        cta_text: '',
        cta_url: '#',
        icon: '',
      },
      {
        option: 'Raising capital',
        title: 'Capital Advisory',
        text: 'The right structure, the right investors and a credible story — from first model to signed term sheet.',
        meta: 'Typically 3–5 months',
        cta_text: '',
        cta_url: '#',
        icon: '',
      },
      {
        option: 'Turning it around',
        title: 'Performance & Turnaround',
        text: 'A clear-eyed diagnostic, a cash plan that holds, and the hard sequencing decisions made early rather than late.',
        meta: 'Typically 8–12 weeks',
        cta_text: '',
        cta_url: '#',
        icon: '',
      },
    ],
  })])]),
]));

// ── 10) CTA FINALE — con 2 bottoni ───────────────────────────────────────────
// Blueprint: btn--brass "Book a consultation" SOLO — no secondo bottone nel CTA HTML
// Ma il blu-print hero ha 2 bottoni. Il CTA finale ha solo 1 bottone.
// CSS: .mr-cta { background:var(--navy); text-align:center }
// .mr-cta h2 { font-size:62px } .mr-cta h2 .it { color:var(--brass) }
// .mr-cta p { color:rgba(255,255,255,.7); font-size:17px }
home.push(sec(NAVY, 'large', [row([col('1-1', [tile('cta-banner', {
  headline: `Let's talk about your`, headline_accent: 'next decision', headline_accent_italic: true,
  subtitle: `A focused first conversation, no obligation. We'll tell you honestly whether we can help.`,
  cta_text: 'Book a consultation', cta_url: '#contact',
  bg: { type: 'solid', color: NAVY }, text_color: WHITE, accent_color: BRASS,
  subtitle_color: 'rgba(255,255,255,.7)',
  cta_bg: BRASS, cta_color: NAVY, cta_radius: R(4), cta_size: 15,
  headline_font_family: 'serif', headline_size: 52, headline_weight: '600', subtitle_size: 17,
  layout: 'stack', vertical_align: 'center', banner_radius: R(0), banner_padding: 80,
})])])]));

// ── EMIT ─────────────────────────────────────────────────────────────────────
K.emit({
  slug: 'meridian', name: 'Meridian',
  tags: ['consulting', 'finance', 'advisory', 'strategy'],
  description: `Meridian — independent strategy & financial advisory. Navy + brass + cream. Spectral (display serif) + Work Sans. Sezioni: hero (showcase), logo strip, services (6 card), process-steps (4 passi borderless), stat strip (4 counter), case studies (2 card), testimonial, insights (3 card), finder (tile finder 4 opzioni interattivo), CTA.`,
  colors: {
    primary: BRASS,
    primary_contrast: NAVY,
    secondary: BRASSD,
    secondary_contrast: NAVY,
    muted: CREAM2,
    muted_contrast: TXT,
    text: TXT,
    text_muted: TXTSOFT,
    background: CREAM,
    border: LINE,
    link: BRASSD,
  },
  css_disp: `"Spectral", Georgia, serif`,
  css_sans: `"Work Sans", -apple-system, sans-serif`,
  heading_weight: '600',
  heading_line_height: '1.1',
  google_fonts: ['Spectral', 'Work Sans'],
  logo_variant: 'dark',
  menu: [
    { title: 'Services', url: '#services' },
    { title: 'Approach', url: '#approach' },
    { title: 'Clients',  url: '#cases' },
    { title: 'Insights', url: '#insights' },
    { title: 'About',    url: '#contact' },
  ],
  header: {
    bg: 'rgba(22,38,61,.96)',
    text_color: 'rgba(255,255,255,.7)',
    sticky_bg: 'rgba(22,38,61,.96)',
    logo_width: 140,
  },
  footer: {
    bg: NAVY,
    headColor: WHITE,
    brand: {
      name: 'Meridian',
      tagline: 'Independent strategy & financial advisory. Clear thinking for complex decisions.',
    },
    columns: [
      { title: 'Services', links: ['Strategy', 'M&A advisory', 'Capital', 'Transformation'] },
      { title: 'Firm',     links: ['About us', 'Clients', 'Insights', 'Careers'] },
      { title: 'Offices',  links: ['Milan · London', 'Frankfurt · Zurich', 'Contact'] },
    ],
    bottom: {
      left: '© 2026 Meridian Advisory — an OLOtheme demo.',
      right: 'Built with OLObuild',
    },
  },
  cursor: false,
}, home);
