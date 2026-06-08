/* Nimbus — ricomposizione TILE-PURE (image-free). Software & Tech · cloud infra.
   Dark navy + blue #5aa9ff + cyan #56e0e0. Font: Onest (display+body) + JetBrains Mono.
   Sezioni: hero, logo-cloud, stat-strip, region-map, capabilities, feature-split,
            pricing, testimonial, projector, cta. */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('nb');

const BG    = '#0a1020';
const BG2   = '#0d1426';
const PANEL = '#111a30';
const PANEL2= '#16223c';
const INK   = '#060a14';
const BLUE  = '#5aa9ff';
const BLUEDL= '#3f8fe8';
const CYAN  = '#56e0e0';
const GREEN = '#46d18a';
const AMBER = '#f0b95a';
const TXT   = '#aebbd0';
const DIM   = '#6b7a96';
const LINE  = 'rgba(255,255,255,.08)';
const LINE2 = 'rgba(90,169,255,.34)';
const WHITE = '#ffffff';
const BTINT = 'rgba(90,169,255,.13)';

const home = [];

// ─── 1) HERO + TERMINAL PANEL (showcase con dati deploy) ─────────────────────
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('hero-split', {
  eyebrow_text: `All systems operational · 30 regions`,
  eyebrow_dot_color: GREEN,
  eyebrow_color: BLUE,
  eyebrow_bg: 'rgba(90,169,255,.10)',
  eyebrow_border: LINE2,
  headline_lines: [
    { text: 'Ship to the edge', color: WHITE, italic: false },
    { text: 'in one command.', color: BLUE,  italic: false },
  ],
  headline_font_family: 'sans-serif',
  headline_font_size: 68,
  headline_line_height: 1.04,
  headline_font_weight: '700',
  headline_align: 'left',
  subhead: `Nimbus runs your apps on a global network with cold starts under 40ms. Push once — we place it in every region your users are in.`,
  subhead_color: DIM,
  subhead_size: 18,
  subhead_italic: false,
  subhead_max_width: 480,
  cta1_text: 'Deploy free',
  cta1_url: '#pricing',
  cta1_bg: BLUE,
  cta1_color: INK,
  cta1_size: 15,
  cta1_radius: R(9),
  cta1_radius_hover: R(9),
  cta2_text: 'Read the docs',
  cta2_url: '#platform',
  cta2_bg: 'rgba(255,255,255,.05)',
  cta2_color: WHITE,
  cta2_border: 'rgba(255,255,255,.16)',
  cta2_size: 15,
  cta2_radius: R(9),
  cta2_radius_hover: R(9),
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: PANEL },
  showcase_padding: 0,
  showcase_radius: R(14),
  showcase_radius_hover: R(14),
  showcase_badge_text: 'zsh — nimbus deploy',
  showcase_badge_dot: BLUE,
  showcase_badge_bg: PANEL2,
  showcase_badge_color: TXT,
  showcase_items: [
    { number: '~/app ❯ nimbus deploy', text: '✓ built in 8.2s',         italic: false, text_color: GREEN, bg: { type: 'solid', color: PANEL } },
    { number: '✓ pushed to 30 regions', text: '✓ live at app.nimbus.dev', italic: false, text_color: GREEN, bg: { type: 'solid', color: PANEL } },
    { number: '# cold start p50',        text: '37ms',                    italic: false, text_color: BLUE,  bg: { type: 'solid', color: PANEL } },
  ],
  showcase_card_radius: R(0),
  showcase_card_radius_hover: R(0),
  showcase_card_shadow: 'none',
  showcase_caption_left: 'NIMBUS DEPLOY',
  showcase_caption_right: 'LIVE',
  showcase_hover_effect: 'none',
  split_ratio: '1.1fr .9fr',
  gap: 52,
  min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
}) ]) ]) ]));

// ─── helper: caption piccola centrata ────────────────────────────────────────
const caption = (txt) => tile('section-header', {
  eyebrow_show: false,
  headline_lines: [ { text: txt, color: DIM, italic: false } ],
  headline_font_family: 'sans-serif',
  headline_font_size: 13,
  headline_font_weight: '600',
  headline_align: 'center',
  tagline_show: false,
  layout: 'center',
  gap: 0,
});

// helper: section-header centrato eyebrow + headline
const shead = (eyebrow, l1, l2, intro) => tile('section-header', {
  eyebrow_show: true,
  eyebrow_text: eyebrow,
  eyebrow_color: BLUE,
  eyebrow_dot_color: BLUE,
  eyebrow_separator: '',
  headline_lines: [
    { text: l1, color: WHITE, italic: false },
    ...(l2 ? [{ text: l2, color: BLUE, italic: false }] : []),
  ],
  headline_font_family: 'sans-serif',
  headline_font_size: 44,
  headline_font_weight: '700',
  headline_align: 'center',
  headline_inline: l2 ? true : false,
  tagline_show: !!intro,
  tagline_text: intro || '',
  tagline_text_italic: false,
  tagline_text_color: DIM,
  tagline_text_size: 16.5,
  layout: 'center',
  gap: 16,
});

// ─── 2) LOGO CLOUD (caption + trust-strip pill) — sfondo BG2, border-bottom LINE ─
home.push(sec(BG2, 'small', [
  row([ col('1-1', [ caption(`Running production for teams that can't go down`) ]) ]),
  row([ col('1-1', [ tile('trust-strip', {
    items: [
      { text: 'TIDELINE' },
      { text: 'FROSTPEAK' },
      { text: 'AURORADATA' },
      { text: 'HELIX IO' },
      { text: 'SANDSTORM' },
      { text: 'VAULTKITE' },
    ],
    variant: 'pill',
    separator_char: '',
    align: 'center',
    flow: 'wrap',
    gap: 16,
    font_family: 'sans-serif',
    text_color: DIM,
    text_size: 13,
    pill_bg: 'rgba(255,255,255,.05)',
    pill_border: LINE,
    pill_text_color: DIM,
  }) ]) ], { gap: 16 }),
]));

// ─── 3) STAT STRIP — sfondo BG2 (border-block:1px solid var(--line)) ────────
const stat = (prefix, number, suffix, label) => col('1-4', [ tile('counter', {
  number, suffix, prefix, label,
  icon_emoji: '',
  text_color: WHITE,
  number_color: WHITE,
  number_font_size: '50',
  number_font_weight: '700',
  label_color: DIM,
  label_font_size: '13',
  bg_type: 'color',
  bg_color: 'transparent',
  padding: '8',
  border_radius: '0',
}) ]);

// suffisso colorato in blu (ms / % / B) — counter non ha suffix_color nativo,
// usiamo BLUE per il number e suffix via number_color; label dimmed
const statBlue = (prefix, number, suffix, label) => col('1-4', [ tile('counter', {
  number, suffix, prefix, label,
  icon_emoji: '',
  text_color: WHITE,
  number_color: WHITE,
  suffix_color: BLUE,
  number_font_size: '50',
  number_font_weight: '700',
  label_color: DIM,
  label_font_size: '13',
  bg_type: 'color',
  bg_color: 'transparent',
  padding: '8',
  border_radius: '0',
}) ]);

home.push(sec(BG2, 'small', [ row([
  stat('',  '30',    '',   'Edge regions'),
  statBlue('', '37',  'ms', 'p50 cold start'),
  statBlue('', '99.99', '%', 'Network uptime'),
  statBlue('', '14',  'B',  'Requests / day'),
], { gap: 24 }) ]));

// ─── 4) REGION MAP — sfondo BG (.nb-sec senza .panel) → SEGNALATO best-effort ─
// Blueprint: dotted-grid con pin pulsanti animati (nb-node) — RegionMap tile non esistente.
// Approssimazione: info-cards 4 colonne (schede regione con dot operativo).
home.push(sec(BG, 'large', [
  row([ col('1-1', [ shead('// global edge', 'Your code, where your users are', '', `Thirty regions across five continents, one anycast network. Requests resolve to the nearest healthy node automatically.`) ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0,
    container_gap: 16,
    columns: 4,
    items_gap: 16,
    card_bg: { type: 'solid', color: PANEL },
    card_border: LINE,
    card_color: DIM,
    card_radius: R(12),
    card_padding: 22,
    show_icon: true,
    show_counter: false,
    show_arrow: false,
    show_footer: true,
    show_media: false,
    icon_color: BLUE,
    icon_bg_color: BTINT,
    title_color: WHITE,
    title_font_family: 'sans-serif',
    title_size: 16,
    title_weight: '700',
    title_italic: false,
    description_size: 13,
    card_hover_effect: 'lift',
    items: [
      { icon: 'globe', title: 'us-west',    description: 'North America West — Los Angeles PoP',   footer_text: '● operational', footer_dot_color: GREEN },
      { icon: 'globe', title: 'sao-paulo',  description: 'South America — direct peering',          footer_text: '● operational', footer_dot_color: GREEN },
      { icon: 'globe', title: 'london',     description: 'Europe West — Tier IV data centre',       footer_text: '● operational', footer_dot_color: CYAN  },
      { icon: 'globe', title: 'frankfurt',  description: 'Europe Central — DE hub',                 footer_text: '● operational', footer_dot_color: BLUE  },
      { icon: 'globe', title: 'cape-town',  description: 'Africa South — 2025 expansion',           footer_text: '● operational', footer_dot_color: CYAN  },
      { icon: 'globe', title: 'singapore',  description: 'APAC South-East — low-latency gateway',   footer_text: '● operational', footer_dot_color: GREEN },
      { icon: 'globe', title: 'tokyo',      description: 'APAC East — Japan + Korea coverage',      footer_text: '● operational', footer_dot_color: BLUE  },
      { icon: 'globe', title: 'sydney',     description: 'Australia — ANZ edge node',               footer_text: '● operational', footer_dot_color: CYAN  },
    ],
  }) ]) ]),
]));
// ⚠️ ZONA SEGNALATA: RegionMap — mappa dotted-grid con pin pulsanti animati non riproducibile
// con tile esistenti. Approssimazione con info-cards 4col accettabile; tile dedicato futuro.

// ─── 5) CAPABILITIES — sfondo BG2 (.nb-sec.panel) — card con border LINE ─────
home.push(sec(BG2, 'large', [
  row([ col('1-1', [ shead('// the platform', 'Infrastructure that gets out of the way', '', '') ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0,
    container_gap: 16,
    columns: 3,
    items_gap: 16,
    card_bg: { type: 'solid', color: PANEL },
    card_border: LINE,
    card_color: DIM,
    card_radius: R(14),
    card_padding: 26,
    show_icon: true,
    show_counter: false,
    show_arrow: false,
    show_footer: false,
    show_media: false,
    icon_color: BLUE,
    icon_bg_color: BTINT,
    title_color: WHITE,
    title_font_family: 'sans-serif',
    title_size: 19,
    title_weight: '700',
    title_italic: false,
    description_size: 14,
    card_hover_effect: 'lift',
    items: [
      { icon: 'zap',          title: 'Instant cold starts',     description: `Lightweight isolates boot in tens of milliseconds — no containers spinning up, no idle bill.` },
      { icon: 'globe',        title: 'Anycast networking',       description: `One address, every region. Traffic lands on the closest healthy node, always.` },
      { icon: 'database',     title: 'Data at the edge',         description: `Replicated key-value and SQL that read where they're requested, write where they're owned.` },
      { icon: 'shield-check', title: 'Secure by default',        description: `Automatic TLS, per-request isolation, secrets that never touch your image.` },
      { icon: 'terminal',     title: 'Git-push deploys',         description: `Connect a repo and every push ships a preview. Promote to production with one click.` },
      { icon: 'trending-up',  title: 'Observability built in',   description: `Per-region latency, logs and traces — no agents, no extra bill, no setup.` },
    ],
  }) ]) ]),
]));

// ─── 6) FEATURE SPLIT — Scaling (hero-split: testo sx + terminale dx) ────────
// Blueprint: nb-feat = display:grid 1fr 1fr, h2 + p + checklist + btn sx, terminal dx.
// Usiamo hero-split (layout due colonne) con checklist sx e showcase terminal dx.
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('hero-split', {
  eyebrow_text: '// scaling',
  eyebrow_dot_color: BLUE,
  eyebrow_color: BLUE,
  eyebrow_bg: 'transparent',
  eyebrow_border: 'transparent',
  headline_lines: [
    { text: 'Scales to zero,', color: WHITE, italic: false },
    { text: 'and to millions', color: BLUE,  italic: false },
  ],
  headline_font_family: 'sans-serif',
  headline_font_size: 44,
  headline_line_height: 1.06,
  headline_font_weight: '700',
  headline_align: 'left',
  subhead: `Nimbus spins capacity up the instant traffic arrives and back to nothing when it leaves. You pay for requests served, never for idle machines.`,
  subhead_color: DIM,
  subhead_size: 16,
  subhead_italic: false,
  subhead_max_width: 480,
  cta1_text: 'How autoscaling works',
  cta1_url: '#platform',
  cta1_bg: 'rgba(255,255,255,.05)',
  cta1_color: WHITE,
  cta1_size: 14,
  cta1_radius: R(9),
  cta1_radius_hover: R(9),
  cta2_text: '',
  cta2_url: '',
  cta2_bg: 'transparent',
  cta2_color: WHITE,
  cta2_border: 'transparent',
  cta2_size: 14,
  cta2_radius: R(9),
  cta2_radius_hover: R(9),
  stats: [
    { label: 'No min instances, no warm-up tax',      value: '✓', value_color: GREEN },
    { label: 'Per-region autoscaling in milliseconds', value: '✓', value_color: GREEN },
    { label: 'Hard spend caps you actually control',   value: '✓', value_color: GREEN },
  ],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: PANEL },
  showcase_padding: 0,
  showcase_radius: R(14),
  showcase_radius_hover: R(14),
  showcase_badge_text: 'nimbus scale --live',
  showcase_badge_dot: GREEN,
  showcase_badge_bg: PANEL2,
  showcase_badge_color: TXT,
  showcase_items: [
    { number: '# live · eu-central', text: '',          italic: false, text_color: DIM,   bg: { type: 'solid', color: PANEL } },
    { number: 'req/s   12,408  ▲',  text: '',          italic: false, text_color: WHITE, bg: { type: 'solid', color: PANEL } },
    { number: 'isolates 86 (auto)', text: '',          italic: false, text_color: TXT,   bg: { type: 'solid', color: PANEL } },
    { number: 'p99   41ms',         text: '',          italic: false, text_color: BLUE,  bg: { type: 'solid', color: PANEL } },
    { number: 'errors 0.00%',       text: '',          italic: false, text_color: GREEN, bg: { type: 'solid', color: PANEL } },
    { number: 'cost/hr $0.84',      text: '',          italic: false, text_color: AMBER, bg: { type: 'solid', color: PANEL } },
  ],
  showcase_card_radius: R(0),
  showcase_card_radius_hover: R(0),
  showcase_card_shadow: 'none',
  showcase_caption_left: 'NIMBUS SCALE',
  showcase_caption_right: 'LIVE',
  showcase_hover_effect: 'none',
  split_ratio: '1fr 1fr',
  gap: 54,
  min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
}) ]) ]) ]));

// ─── 7) PRICING — sfondo BG2 (.nb-sec.panel) — card con border LINE + Pro border BLUE ─
home.push(sec(BG2, 'large', [
  row([ col('1-1', [ shead('// pricing', 'Pay for requests,', `not for idle`, '') ]) ]),
  row([
    col('1-3', [ tile('pricing', {
      plan_name: 'Hobby',
      price: '$0',
      currency: '',
      period: '/mo',
      description: 'For side projects and prototypes.',
      features: '1M requests / mo\n3 regions\nCommunity support',
      is_popular: false,
      bg_color: PANEL,
      border_color: LINE,
      price_color: WHITE,
      accent_color: BLUE,
      cta_text: 'Start free',
      cta_url: '#pricing',
      cta_bg: 'rgba(255,255,255,.07)',
      cta_color: WHITE,
      border_radius: R(18),
      feature_icon: 'check',
      feature_icon_color: BLUE,
    }) ]),
    col('1-3', [ tile('pricing', {
      plan_name: 'Pro',
      price: '$0.18',
      currency: '',
      period: '/M req',
      description: 'Usage-based, for apps in production.',
      features: 'All 30 regions\nEdge data & KV\nCustom domains & TLS\nEmail support',
      is_popular: true,
      badge_text: 'Most popular',
      badge_bg_color: 'rgba(90,169,255,.12)',
      badge_text_color: BLUE,
      bg_color: PANEL2,
      border_color: BLUE,
      price_color: BLUE,
      accent_color: BLUE,
      cta_text: 'Start building',
      cta_url: '#pricing',
      cta_bg: BLUE,
      cta_color: INK,
      border_radius: R(18),
      feature_icon: 'check',
      feature_icon_color: BLUE,
    }) ]),
    col('1-3', [ tile('pricing', {
      plan_name: 'Enterprise',
      price: 'Custom',
      currency: '',
      period: '',
      description: 'For scale, compliance & SLAs.',
      features: 'Dedicated regions\n99.99% SLA\nSSO, audit & SCIM',
      is_popular: false,
      bg_color: PANEL,
      border_color: LINE,
      price_color: WHITE,
      accent_color: BLUE,
      cta_text: 'Talk to sales',
      cta_url: '#pricing',
      cta_bg: 'rgba(255,255,255,.07)',
      cta_color: WHITE,
      border_radius: R(18),
      feature_icon: 'check',
      feature_icon_color: BLUE,
    }) ]),
  ], { gap: 18, vertical_align: 'stretch' }),
]));

// ─── 8) TESTIMONIAL — sfondo BG (.nb-sec, no .panel) ─────────────────────────
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('testimonial', {
  quote: `"We moved our whole API to Nimbus over a weekend. Latency dropped by half in Asia and the idle bill just vanished."`,
  author_name: 'Wei Chen',
  author_role: 'Platform Lead, Tideline',
  rating: '0',
  layout: 'single',
  show_line: false,
  bg_color: 'transparent',
  text_color: WHITE,
  border_radius: '0',
  avatar: '',
}) ]) ]) ]));

// ─── 9) PROJECTOR — Cost Estimator — sfondo BG (.nb-sec) ─────────────────────
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('projector', {
  eyebrow: '// estimate',
  heading: `Only pay for what <em>runs</em>`,
  intro: `Drag to your monthly request volume. No idle charges, no per-seat tax — just €0.15 per million requests, billed to the second.`,
  min: '10',
  max: '1000',
  step: '10',
  value: '250',
  rate: '0',
  years: '0.15',
  currency: '€',
  input_label: 'Requests / month (millions)',
  out_caption: 'Estimated monthly bill',
  note: `Includes 1M free requests & global edge routing. Volume discounts above 500M.`,
  show_contrib: false,
  zone_accent: BLUE,
  align: 'left',
  tile_padding: { top: 52, right: 52, bottom: 52, left: 52 },
  border_radius: '16',
  shadow: 'sm',
}) ]) ]) ]));

// ─── 10) CTA FINALE — sfondo BG2 (.nb-sec, no .panel qui ma .nb-cta) ─────────
// Blueprint: .nb-cta__box con background:linear-gradient(160deg,rgba(90,169,255,.16),rgba(86,224,224,.05))
//            border:1px solid var(--line-2) = rgba(90,169,255,.34)
//            2 pulsanti: "Deploy free" (blue) + "See live status" (ghost)
home.push(sec(BG2, 'large', [ row([ col('1-1', [ tile('cta-banner', {
  headline: 'Deploy your first app free',
  headline_accent: '',
  headline_accent_italic: false,
  subtitle: `Push a repo, get a global URL in under a minute. No card, no idle charges.`,
  cta_text: 'Deploy free',
  cta_url: '#pricing',
  cta2_text: 'See live status',
  cta2_url: '#status',
  cta2_bg: 'rgba(255,255,255,.05)',
  cta2_color: WHITE,
  cta2_border: 'rgba(255,255,255,.16)',
  bg: { type: 'gradient', value: 'linear-gradient(160deg, rgba(90,169,255,.16), rgba(86,224,224,.05))', angle: 160, stops: [
    { color: 'rgba(90,169,255,.16)', position: 0 },
    { color: 'rgba(86,224,224,.05)', position: 100 },
  ]},
  border_color: LINE2,
  text_color: WHITE,
  accent_color: BLUE,
  subtitle_color: TXT,
  cta_bg: BLUE,
  cta_color: INK,
  cta_radius: R(9),
  cta_size: 15,
  headline_font_family: 'sans-serif',
  headline_size: 48,
  headline_weight: '700',
  subtitle_size: 17,
  layout: 'stack',
  vertical_align: 'center',
  banner_radius: R(24),
  banner_padding: 80,
}) ]) ]) ]));

K.emit({
  slug: 'nimbus',
  name: 'Nimbus',
  tags: ['software', 'tech', 'cloud', 'infrastructure', 'saas'],
  description: `Nimbus — cloud infrastructure for the edge. Dark navy + blue #5aa9ff. Onest (display+body). Deploy hero con terminal showcase, stat strip, region grid, capabilities, autoscaling (hero-split), usage pricing, projector cost estimator, CTA 2-bottoni. Riproduzione fedele dell'OLOtheme Nimbus (Software & Tech).`,
  colors: {
    primary:           BLUE,
    primary_contrast:  INK,
    secondary:         CYAN,
    secondary_contrast:INK,
    muted:             BG2,
    muted_contrast:    TXT,
    text:              TXT,
    text_muted:        DIM,
    background:        BG,
    border:            LINE,
    link:              BLUE,
  },
  css_disp:  `"Onest", -apple-system, sans-serif`,
  css_sans:  `"Onest", -apple-system, sans-serif`,
  heading_weight: '700',
  heading_line_height: '1.06',
  google_fonts: ['Onest', 'JetBrains Mono'],
  logo_variant: 'light',
  menu: [
    { title: 'Platform', url: '#platform' },
    { title: 'Regions',  url: '#regions'  },
    { title: 'Status',   url: '#status'   },
    { title: 'Pricing',  url: '#pricing'  },
  ],
  header: {
    bg: 'rgba(10,16,32,.82)',
    text_color: DIM,
    sticky_bg: 'rgba(10,16,32,.92)',
    logo_width: 130,
  },
  footer: {
    bg: BG2,
    headColor: WHITE,
    brand: {
      name: 'Nimbus',
      tagline: 'Cloud infrastructure for the edge. Deploy once, run everywhere, pay per request.',
    },
    columns: [
      { title: 'Product',    links: ['Platform', 'Regions', 'Pricing', 'Changelog'] },
      { title: 'Developers', links: ['Docs', 'CLI', 'API', 'Status'] },
      { title: 'Company',    links: ['About', 'Security', 'Careers', 'Contact'] },
    ],
    bottom: {
      left:  '© 2026 Nimbus — an OLOtheme demo.',
      right: 'Built with OLObuild',
    },
  },
  cursor: {
    blend_mode: 'exclusion',
    ring_color: '#ffffff',
    dot_color:  '#ffffff',
  },
}, home);
