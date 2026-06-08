/* Circuit — ricomposizione TILE-PURE (image-free). Software & Tech SaaS.
   Dark navy/indigo + violet + mint. Space Grotesk (display) + Work Sans (body). */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('ci');

// ── Palette (da :root circuit.css) ───────────────────────────────────────────
const BG     = '#0b0d18';
const BG2    = '#141a2e';
const PANEL  = '#141a2e';
const PANEL2 = '#1b2238';
const INK    = '#0e1020';
const INDIGO = '#6c8cff';
const INDIGOD= '#5a78e6';
const VIOLET = '#b08bff';
const MINT   = '#4fe0c0';
const TXT    = '#c9cde0';
const DIM    = '#8a90a8';
const LINE   = 'rgba(255,255,255,.09)';
const WHITE  = '#ffffff';
const INDIGO_T = 'rgba(108,140,255,.14)';

// ── Helpers ───────────────────────────────────────────────────────────────────
// section-header centrato con eyebrow + headline multi-riga + paragrafo intro
const shead = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: INDIGO, eyebrow_dot_color: INDIGO, eyebrow_separator: '',
  headline_lines: [
    { text: l1,     color: WHITE,  italic: false },
    { text: accent, color: INDIGO, italic: false },
  ],
  headline_font_family: 'sans-serif', headline_font_size: 46, headline_font_weight: '600',
  headline_align: 'center', headline_inline: true,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false,
  tagline_text_color: DIM, tagline_text_size: 16,
  layout: 'center', gap: 16,
});

// caption-only per etichette logo cloud / integrazioni
const caption = (txt) => tile('section-header', {
  eyebrow_show: false,
  headline_lines: [{ text: txt, color: DIM, italic: false }],
  headline_font_family: 'sans-serif', headline_font_size: 12, headline_font_weight: '600',
  headline_align: 'center',
  tagline_show: false, layout: 'center', gap: 0,
});

// section-header sinistra per feature split
const sheadLeft = (eyebrow, h2line1, h2line2, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: INDIGO,
  eyebrow_dot_color: INDIGO, eyebrow_separator: '',
  headline_lines: [
    { text: h2line1, color: WHITE, italic: false },
    { text: h2line2, color: WHITE, italic: false },
  ],
  headline_font_family: 'sans-serif', headline_font_size: 42, headline_font_weight: '600',
  headline_align: 'left',
  tagline_show: true,
  tagline_text: intro,
  tagline_text_color: DIM, tagline_text_size: 16,
  layout: 'stack', gap: 14,
});

// list tile con check mint (feature list items)
const featureList = (items) => tile('list', {
  items: items.map(t => ({ text: t, icon: 'check' })),
  icon_default: 'check',
  icon_color: MINT,
  text_color: TXT,
  text_align: 'left',
  spacing: '12',
  icon_size: '18',
  icon_gap: '11',
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
});

// ── HOMEPAGE ─────────────────────────────────────────────────────────────────
const home = [];

// 1) HERO (centrato, pill-tag come eyebrow, titolo doppia riga, sub, 2 CTA, no showcase)
home.push(sec(BG, 'large', [
  row([
    col('1-1', [
      tile('hero-split', {
        eyebrow_text: `New · Circuit 3.0 — now with live workflows`,
        eyebrow_dot_color: INDIGO, eyebrow_color: INDIGO,
        headline_lines: [
          { text: 'Ship reliable software,', color: WHITE,  italic: false },
          { text: 'without the busywork.',   color: INDIGO, italic: false },
        ],
        headline_font_family: 'sans-serif', headline_font_size: 68,
        headline_line_height: 1.02, headline_font_weight: '700', headline_align: 'center',
        subhead: 'Circuit connects the tools your team already uses, automates the hand-offs, and gives everyone one honest view of every release.',
        subhead_color: DIM, subhead_size: 18, subhead_italic: false, subhead_max_width: 560,
        cta1_text: 'Start free — no card', cta1_url: '#pricing',
        cta1_bg: INDIGO, cta1_color: WHITE, cta1_size: 15, cta1_radius: R(9), cta1_radius_hover: R(9),
        cta2_text: 'See how it works', cta2_url: '#features',
        cta2_bg: 'rgba(255,255,255,.06)', cta2_color: WHITE,
        cta2_border: 'rgba(255,255,255,.16)', cta2_size: 15,
        cta2_radius: R(9), cta2_radius_hover: R(9),
        stats: [],
        showcase_enabled: false,
        split_ratio: '1fr', gap: 0, min_height: 0,
        tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
      }),
    ]),
  ]),
]));

// 2) LOGO CLOUD (caption + trust-strip pill)
// ci-logos: padding 46px 0; border-bottom 1px solid var(--line)
home.push(sec(BG2, 'small', [
  row([col('1-1', [caption('Trusted by teams shipping every day')])]),
  row([col('1-1', [
    tile('trust-strip', {
      items: [
        { text: 'NORTHWIND' }, { text: 'VELLUM' }, { text: 'ARCLIGHT' },
        { text: 'TOLLKEEP' }, { text: 'KESTREL' }, { text: 'AURIC' },
      ],
      variant: 'pill', separator_char: '', align: 'center', flow: 'wrap', gap: 16,
      font_family: 'sans-serif', text_color: DIM, text_size: 13,
      pill_bg: 'rgba(255,255,255,.06)', pill_border: LINE, pill_text_color: DIM,
    }),
  ])], { gap: 16 }),
]));

// 3) BENTO FEATURES — approssimato con info-cards (BentoGrid non esiste come tile)
// SEGNALATO: BentoGrid = griglia mista con card wide/tall/accent + media. Best-effort.
// Bcard: background: var(--panel); border:1px solid var(--line); border-radius:16px; padding:26px
home.push(sec(BG, 'large', [
  row([col('1-1', [
    shead('// the platform', 'Everything between idea and', 'ship',
      'One connected workspace for planning, building and releasing — so nothing falls through the cracks.'),
  ])]),
  row([col('1-1', [
    tile('info-cards', {
      container_bg: { type: 'solid', color: 'transparent' },
      container_padding: 0, container_gap: 16, columns: 3, items_gap: 16,
      card_bg: { type: 'solid', color: PANEL2 },
      card_color: DIM, card_radius: R(16), card_padding: 26,
      show_icon: true, show_counter: false, show_arrow: false, show_footer: false, show_media: false,
      icon_color: INDIGO, icon_bg_color: INDIGO_T, title_color: WHITE,
      title_font_family: 'sans-serif', title_size: 21, title_weight: '600', title_italic: false,
      description_size: 14,
      items: [
        { icon: 'zap',        title: 'Live workflows',        description: 'Drag-and-drop pipelines that run themselves — triggers, approvals and hand-offs without the Slack archaeology.' },
        { icon: 'clock',      title: 'Real-time status',      description: 'Every release, one source of truth — no more "is this done?".' },
        { icon: 'database',   title: 'Deep integrations',     description: 'Plugs into the 40+ tools your team already lives in.' },
        { icon: 'pie-chart',  title: 'Insights',              description: 'Cycle time, bottlenecks and trends — measured, not guessed.' },
        { icon: 'monitor',    title: 'Works where you work',  description: 'Native desktop, web and CLI — plus an API for everything else. Bring Circuit to your stack, not the other way around.' },
      ],
      card_hover_effect: 'lift',
    }),
  ])]),
]));

// 4) FEATURE SPLIT 1 — Automation
// Blueprint: 2-col grid (text+list left, mock media right) su sfondo BG (stesso dell'hero)
// ci-feat__list: checklist con svg check color mint
home.push(sec(BG, 'large', [
  row([
    col('1-2', [
      sheadLeft('// automation',
        'Automate the work',
        'nobody wants to do',
        'Circuit watches your pipeline and handles the routine — assigning reviewers, chasing approvals, updating status, closing the loop.'),
      featureList([
        'Conditional triggers & branching logic',
        'Approvals that route to the right person',
        'Audit trail on every action',
      ]),
    ]),
    col('1-2', [
      tile('info-cards', {
        container_bg: { type: 'solid', color: PANEL },
        container_padding: 24, container_gap: 0, columns: 1, items_gap: 0,
        card_bg: { type: 'solid', color: 'transparent' },
        card_color: DIM, card_radius: R(0), card_padding: 0,
        show_icon: false, show_counter: false, show_arrow: false, show_footer: false, show_media: false,
        title_color: DIM, title_font_family: 'sans-serif', title_size: 12, title_weight: '500',
        items: [
          { title: 'automation builder' },
        ],
        card_hover_effect: 'none',
      }),
    ]),
  ], { gap: 54, vertical_align: 'center' }),
]));

// 5) FEATURE SPLIT 2 — Insights
// Blueprint: flip = media left, text+list right; stessa sezione ci-sec su BG
home.push(sec(BG, 'large', [
  row([
    col('1-2', [
      tile('info-cards', {
        container_bg: { type: 'solid', color: PANEL },
        container_padding: 24, container_gap: 0, columns: 1, items_gap: 0,
        card_bg: { type: 'solid', color: 'transparent' },
        card_color: DIM, card_radius: R(0), card_padding: 0,
        show_icon: false, show_counter: false, show_arrow: false, show_footer: false, show_media: false,
        title_color: DIM, title_font_family: 'sans-serif', title_size: 12, title_weight: '500',
        items: [
          { title: 'insights dashboard' },
        ],
        card_hover_effect: 'none',
      }),
    ]),
    col('1-2', [
      sheadLeft('// insights',
        'Decisions backed',
        'by your own data',
        'Stop guessing where things slow down. Circuit measures cycle time, throughput and bottlenecks across every team in real time.'),
      featureList([
        'Live dashboards, no setup',
        'Export anywhere via API',
        'Weekly digests to your inbox',
      ]),
    ]),
  ], { gap: 54, vertical_align: 'center' }),
]));

// 6) STATS STRIP — counter ×4
// ci-stats: border-block:1px solid var(--line); stesso sfondo BG
// num color #fff; unit (.u) color var(--indigo)
const stat = (prefix, number, suffix, label) => col('1-4', [
  tile('counter', {
    number, suffix, prefix, label, icon_emoji: '',
    text_color: WHITE, number_color: WHITE,
    number_font_size: '52', number_font_weight: '700',
    label_color: DIM, label_font_size: '13',
    bg_type: 'color', bg_color: 'transparent',
    padding: '8', border_radius: '0',
  }),
]);
home.push(sec(BG, 'small', [
  row([
    stat('', '9000', '+',    'Teams onboard'),
    stat('', '40',   '%',    'Less cycle time'),
    stat('', '99.99','%',    'Platform uptime'),
    stat('', '120',  '+',    'Integrations'),
  ], { gap: 24 }),
]));

// 7) INTEGRATIONS — approssimato con trust-strip (IntegrationGrid non esiste)
// SEGNALATO: IntegrationGrid = grid 6×2 di card quadrate con logo placeholder.
// intg__c: aspect-ratio:1/1; border:1px solid var(--line); border-radius:14px; bg:var(--panel)
home.push(sec(BG2, 'large', [
  row([col('1-1', [
    shead('// integrations', 'Plays nicely with', 'your stack',
      'Connect in a click — Circuit syncs both ways and stays out of the way.'),
  ])]),
  row([col('1-1', [
    tile('trust-strip', {
      items: [
        { text: 'GitHub' }, { text: 'Jira' }, { text: 'Slack' },
        { text: 'Figma' }, { text: 'Linear' }, { text: 'Notion' },
        { text: 'Datadog' }, { text: 'PagerDuty' }, { text: 'Sentry' },
        { text: 'Vercel' }, { text: 'AWS' }, { text: 'Terraform' },
      ],
      variant: 'pill', separator_char: '', align: 'center', flow: 'wrap', gap: 12,
      font_family: 'sans-serif', text_color: DIM, text_size: 13,
      pill_bg: PANEL2, pill_border: LINE, pill_text_color: TXT,
    }),
  ])], { gap: 24 }),
]));

// 8) PRICING — 3 piani (Starter / Team / Enterprise)
// price: bg var(--panel); border:1px solid var(--line); border-radius:18px; padding:30px
// price.feat: border-color:var(--indigo); box-shadow glow indigo
// price__tag: font mono; bg rgba(indigo,.14); color indigo
// price__d (description sotto il prezzo): presente nel blueprint
home.push(sec(BG, 'large', [
  row([col('1-1', [
    shead('// pricing', 'Simple, scales', 'with you', ''),
  ])]),
  row([
    col('1-3', [
      tile('pricing', {
        plan_name: 'Starter',
        plan_description: 'For small teams getting started.',
        price: '0', currency: '€', currency_position: 'before', period: '/mo',
        features: 'Up to 5 members\n3 workflows\nCommunity support',
        feature_dividers: true,
        cta_text: 'Get started', cta_url: '#', cta_target: '_self',
        cta_bg_color: 'rgba(255,255,255,.06)', cta_text_color: WHITE,
        cta_border_width: '1', cta_border_color: LINE,
        cta_width: '100', cta_radius: '9',
        cta_hover_effect: 'lift',
        is_popular: false,
        check_style: 'checkmark', check_size: '14', check_color: MINT,
        price_color: WHITE, text_color: TXT, accent_color: INDIGO,
        bg_color: PANEL,
        border_radius: '18',
        border_color: LINE,
      }),
    ]),
    col('1-3', [
      tile('pricing', {
        plan_name: 'Team',
        plan_description: 'For teams shipping in production.',
        price: '24', currency: '€', currency_position: 'before', period: '/user/mo',
        features: 'Unlimited workflows\nAll integrations\nInsights dashboard\nPriority support',
        feature_dividers: true,
        cta_text: 'Start free trial', cta_url: '#', cta_target: '_self',
        cta_bg_color: INDIGO, cta_text_color: WHITE,
        cta_border_width: '0', cta_border_color: '',
        cta_width: '100', cta_radius: '9',
        cta_hover_effect: 'lift',
        is_popular: true,
        badge_text: 'Most popular', badge_style: 'pill', badge_radius: '6',
        badge_bg_color: 'rgba(108,140,255,.14)', badge_text_color: INDIGO,
        check_style: 'checkmark', check_size: '14', check_color: MINT,
        price_color: WHITE, text_color: TXT, accent_color: INDIGO,
        bg_color: PANEL,
        border_radius: '18',
        border_color: INDIGO,
      }),
    ]),
    col('1-3', [
      tile('pricing', {
        plan_name: 'Enterprise',
        plan_description: 'For orgs with scale & compliance needs.',
        price: 'Custom', currency: '', currency_position: 'before', period: '',
        features: 'SSO & SAML\nAudit & compliance\nDedicated CSM',
        feature_dividers: true,
        cta_text: 'Talk to sales', cta_url: '#', cta_target: '_self',
        cta_bg_color: 'rgba(255,255,255,.06)', cta_text_color: WHITE,
        cta_border_width: '1', cta_border_color: LINE,
        cta_width: '100', cta_radius: '9',
        cta_hover_effect: 'lift',
        is_popular: false,
        check_style: 'checkmark', check_size: '14', check_color: MINT,
        price_color: WHITE, text_color: TXT, accent_color: INDIGO,
        bg_color: PANEL,
        border_radius: '18',
        border_color: LINE,
      }),
    ]),
  ], { gap: 18, vertical_align: 'stretch' }),
]));

// 9) TESTIMONIAL
// ci-testi: text-align center; q: font disp weight 500 size clamp(24-38px); by: txt-dim
home.push(sec(BG2, 'large', [
  row([col('1-1', [
    tile('testimonial', {
      quote: `"We cut our release cycle from two weeks to four days in the first month. Circuit just removed the friction we'd stopped noticing."`,
      author_name: 'Dana Whitfield', author_role: 'VP Engineering, Northwind',
      rating: '0', layout: 'single', show_line: false,
      bg_color: 'transparent', text_color: WHITE, border_radius: '0', avatar: '',
    }),
  ])]),
]));

// 10) PLAN BUILDER
// Tile reale: 'builder'. Sezione #plan data-builder — 6 add-on con stepper +/− e totale live.
// zone_accent=#6c8cff (--fx-zone-accent), zone_on=#fff. card_bg=PANEL, card_border=LINE.
home.push(sec(BG, 'large', [
  row([col('1-1', [
    tile('builder', {
      eyebrow: '// build your plan',
      heading: 'Start with Team, add what you need',
      intro: `Team is €24/seat. Toggle the modules your org actually uses and watch the monthly add-on total update live.`,
      currency: '€',
      cap: 0,
      total_label: 'add-ons / month',
      count_label: 'add-ons',
      cta_text: 'Upgrade plan',
      cta_url: '#pricing',
      zone_accent: '#6c8cff',
      zone_on: '#ffffff',
      card_bg: PANEL,
      card_border: LINE,
      align: 'left',
      items: [
        { name: 'Insights dashboard', price: '19', note: 'cycle-time & throughput', start: 0 },
        { name: 'Automation engine',  price: '29', note: 'unlimited workflows',      start: 0 },
        { name: 'SSO & SAML',         price: '40', note: 'SCIM provisioning',        start: 0 },
        { name: 'Priority support',   price: '25', note: '1-hour SLA',               start: 0 },
        { name: 'Audit log',          price: '15', note: 'compliance export',        start: 0 },
        { name: '+10 seats',          price: '60', note: 'grow the team',            start: 0 },
      ],
    }),
  ])]),
]));

// 11) CTA FINALE — 2 bottoni: "Start free" (light/white) + "Book a demo" (ghost)
// ci-cta__box: border:1px solid rgba(indigo,.4); border-radius:24px; padding clamp(48-84px)
//              background: linear-gradient(160deg, rgba(indigo,.18), rgba(violet,.06))
// Bottoni: .btn--light (white) e .btn--ghost-d (ghost dark)
home.push(sec(BG, 'large', [
  row([col('1-1', [
    tile('cta-banner', {
      headline: 'Start shipping with', headline_accent: 'Circuit', headline_accent_italic: false,
      subtitle: 'Free for small teams. No card, no sales call — just connect your tools and go.',
      cta_text: 'Start free', cta_url: '#pricing',
      cta2_text: 'Book a demo', cta2_url: '#',
      cta2_bg: 'rgba(255,255,255,.06)', cta2_color: WHITE, cta2_border: 'rgba(255,255,255,.16)',
      bg: { type: 'gradient', gradient: `linear-gradient(160deg, rgba(108,140,255,.18), rgba(176,139,255,.06))` },
      text_color: WHITE, accent_color: INDIGO, subtitle_color: TXT,
      cta_bg: WHITE, cta_color: INK, cta_radius: R(9), cta_size: 15,
      headline_font_family: 'sans-serif', headline_size: 52, headline_weight: '700',
      subtitle_size: 17,
      layout: 'stack', vertical_align: 'center',
      banner_radius: R(24), banner_padding: 80,
    }),
  ])]),
]));

// ── EMIT ──────────────────────────────────────────────────────────────────────
K.emit({
  slug: 'circuit', name: 'Circuit',
  tags: ['software', 'saas', 'tech', 'startup'],
  description: 'Circuit — SaaS workflow platform. Dark navy + indigo + violet + mint, Space Grotesk (display) + Work Sans (body). Hero centrato, logo-cloud, bento features (best-effort info-cards), feature splits 2-col con list tile, counter strip, trust-strip integrazioni, pricing 3 piani, testimonial, plan-builder (tile builder — 6 add-on stepper, zone_accent indigo), CTA 2 bottoni. Zone segnalate: BentoGrid, IntegrationGrid.',
  colors: {
    primary: INDIGO, primary_contrast: WHITE,
    secondary: VIOLET, secondary_contrast: WHITE,
    muted: BG2, muted_contrast: TXT,
    text: TXT, text_muted: DIM,
    background: BG, border: LINE, link: INDIGO,
  },
  css_disp: `"Space Grotesk", -apple-system, sans-serif`,
  css_sans: `"Work Sans", -apple-system, sans-serif`,
  heading_weight: '600', heading_line_height: '1.05',
  google_fonts: ['Space Grotesk', 'Work Sans'],
  logo_variant: 'light',
  menu: [
    { title: 'Product',      url: '#features' },
    { title: 'Integrations', url: '#integrations' },
    { title: 'Pricing',      url: '#pricing' },
    { title: 'Docs',         url: '#' },
  ],
  header: {
    bg: 'rgba(11,13,24,.82)',
    text_color: DIM,
    sticky_bg: 'rgba(11,13,24,.92)',
    logo_width: 138,
  },
  footer: {
    bg: BG2,
    headColor: WHITE,
    brand: {
      name: 'Circuit',
      tagline: 'The workflow platform for teams that ship. Connect, automate, measure.',
    },
    columns: [
      { title: 'Product',    links: ['Features', 'Integrations', 'Pricing', 'Changelog'] },
      { title: 'Developers', links: ['Docs', 'API', 'CLI', 'Status'] },
      { title: 'Company',    links: ['About', 'Careers', 'Blog', 'Contact'] },
    ],
    bottom: {
      left:  '© 2026 Circuit — an OLOtheme demo.',
      right: 'Built with OLObuild',
    },
  },
  cursor: { blend_mode: 'exclusion', ring_color: '#ffffff', dot_color: '#ffffff' },
}, home);
