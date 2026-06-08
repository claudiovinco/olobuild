/* Forge — ricomposizione TILE-PURE (image-free). Software & Tech. Black+orange, Geologica. */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('fg');

/* ── Palette (da :root forge.css) ── */
const BG     = '#121212', BG2    = '#171717', PANEL  = '#1c1c1c', PANEL2 = '#242424', INK = '#0a0a0a';
const ORANGE = '#ff6a2b', ORANGED = '#ec5a1d', AMBER = '#ffb155', LIME = '#b6e85a';
const TXT    = '#a8a8a8', DIM    = '#6b6b6b', LINE   = 'rgba(255,255,255,.09)', LINE2 = 'rgba(255,106,43,.4)';
const WHITE  = '#ffffff';
const OTINT  = 'rgba(255,106,43,.14)';

const home = [];

/* ═══════════════════════════════════════════════════════════
   1) HERO — hero-split con showcase che simula il terminale CLI.
   Blueprint: .fg-hero — grid 1.05fr .95fr, hero h1 76px, pill eyebrow,
   2 CTA (orange + ghost), terminale a destra con barra + corpo mono.
   split_ratio usa '1.2fr 1fr' (opzione piu vicina a 1.05fr .95fr)
   ═══════════════════════════════════════════════════════════ */
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('hero-split', {
  eyebrow_text: `forge 2.0 · now with remote caching`,
  eyebrow_dot_color: ORANGE,
  eyebrow_color: ORANGE,
  headline_lines: [
    { text: 'Build, test, ship.', color: WHITE,  italic: false },
    { text: 'Absurdly fast.',     color: ORANGE, italic: false },
  ],
  headline_font_family: 'sans-serif',
  headline_font_size: 72,
  headline_line_height: 1.0,
  headline_font_weight: '800',
  headline_align: 'left',
  subhead: `Forge caches everything, parallelises your pipeline across machines, and puts every action one keystroke away. Green builds in seconds, not coffee breaks.`,
  subhead_color: TXT,
  subhead_size: 18,
  subhead_italic: false,
  subhead_max_width: 480,
  cta1_text: 'Start building free',
  cta1_url: '#pricing',
  cta1_bg: ORANGE,
  cta1_color: WHITE,
  cta1_size: 15,
  cta1_radius: R(7), cta1_radius_hover: R(7),
  cta2_text: 'See how it works',
  cta2_url: '#features',
  cta2_bg: 'rgba(255,255,255,.05)',
  cta2_color: WHITE,
  cta2_border: 'rgba(255,255,255,.16)',
  cta2_size: 15,
  cta2_radius: R(7), cta2_radius_hover: R(7),
  stats: [],
  /* Showcase: simula il terminale CLI con badge = titlebar e items = righe output */
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: PANEL },
  showcase_padding: 0,
  showcase_radius: R(12), showcase_radius_hover: R(12),
  showcase_badge_text: 'forge run ci',
  showcase_badge_dot: ORANGE,
  showcase_badge_bg: PANEL2,
  showcase_badge_color: TXT,
  showcase_items: [
    { number: `# restoring cache… hit (2.1s)`, text: '',            italic: false, text_color: DIM,  bg: { type: 'solid', color: PANEL } },
    { number: `✓ lint`,                         text: '1.4s',        italic: false, text_color: LIME, bg: { type: 'solid', color: PANEL } },
    { number: `✓ test ×128`,                    text: '9.7s',        italic: false, text_color: LIME, bg: { type: 'solid', color: PANEL } },
    { number: `✓ deploy prod`,                  text: `4.8s ✓ green`, italic: false, text_color: LIME, bg: { type: 'solid', color: PANEL } },
  ],
  showcase_card_radius: R(0), showcase_card_radius_hover: R(0), showcase_card_shadow: 'none',
  showcase_caption_left: 'total: 24.2s', showcase_caption_right: '✓ GREEN',
  showcase_hover_effect: 'none',
  split_ratio: '1.2fr 1fr',
  gap: 48,
  min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
}) ]) ]) ]));

/* ═══════════════════════════════════════════════════════════
   2) LOGO CLOUD — etichetta mono + trust-strip 6 placeholder.
   Blueprint: .fg-logos — padding 42px 0, border-bottom 1px var(--line).
   Label "Trusted on the critical path at fast-moving teams" in var(--mono).
   6 pill placeholder grigi (.fg-logo: width 104px height 22px).
   ═══════════════════════════════════════════════════════════ */
home.push(sec(BG2, 'small', [
  row([ col('1-1', [ tile('section-header', {
    eyebrow_show: false,
    headline_lines: [{ text: 'Trusted on the critical path at fast-moving teams', color: DIM, italic: false }],
    headline_font_family: 'sans-serif',
    headline_font_size: 12,
    headline_font_weight: '500',
    headline_align: 'center',
    tagline_show: false,
    layout: 'center',
    gap: 0,
  }) ]) ]),
  row([ col('1-1', [ tile('trust-strip', {
    items: [
      { text: 'ACME LABS' },
      { text: 'STACKIFY' },
      { text: 'DEVFLOW' },
      { text: 'TURBO INC' },
      { text: 'NANOFORGE' },
      { text: 'BUILDBASE' },
    ],
    variant: 'pill',
    separator_char: '',
    align: 'center',
    flow: 'wrap',
    gap: 46,
    font_family: 'sans-serif',
    text_color: DIM,
    text_size: 12,
    pill_bg: 'rgba(255,255,255,.05)',
    pill_border: LINE,
    pill_text_color: DIM,
  }) ]) ], { gap: 24 }),
]));

/* ═══════════════════════════════════════════════════════════
   3) STAT STRIP — counter ×4.
   Blueprint: .fg-stats — bg var(--bg-2), border-block 1px solid var(--line),
   4 colonne: 14×, 92%, 500+, 40M. Numero bianco peso 800, suffisso arancio.
   Il tile counter non ha suffix_color separato — numero bianco, suffisso integrato.
   ═══════════════════════════════════════════════════════════ */
const stat = (number, suffix, label) => col('1-4', [ tile('counter', {
  number,
  suffix,
  prefix: '',
  label,
  icon_emoji: '',
  text_color: WHITE,
  number_font_size: '48',
  number_font_weight: '800',
  label_color: DIM,
  label_font_size: '12',
  bg_type: 'color',
  bg_color: 'transparent',
  tile_padding: { top: 36, right: 16, bottom: 36, left: 16 },
  border_radius: '0',
}) ]);

home.push(sec(BG2, 'small', [ row([
  stat('14',  '×', 'Faster cold builds'),
  stat('92',  '%', 'Cache hit rate'),
  stat('500', '+', 'Parallel runners'),
  stat('40',  'M', 'Builds / month'),
], { gap: 24 }) ]));

/* ═══════════════════════════════════════════════════════════
   4) FEATURE — Remote Cache.
   Blueprint: .fg-feat — 2 colonne (1fr 1fr gap 54px): sinistra = eyebrow +
   h2 + p + ul(3 check items) + btn ghost; destra = media panel (.fg-feat__media).
   Usiamo section-header (sinistra, layout stack) + list tile per i 3 bullet,
   + info-cards 1 col con icon check arancio (senza card bg) per i check items.
   ═══════════════════════════════════════════════════════════ */
home.push(sec(BG, 'large', [
  row([
    col('1-2', [
      tile('section-header', {
        eyebrow_show: true,
        eyebrow_text: '// remote cache',
        eyebrow_color: ORANGE,
        eyebrow_dot_color: ORANGE,
        eyebrow_separator: '',
        headline_lines: [
          { text: 'Never build the',    color: WHITE,  italic: false },
          { text: 'same thing twice',   color: ORANGE, italic: false },
        ],
        headline_font_family: 'sans-serif',
        headline_font_size: 44,
        headline_font_weight: '800',
        headline_align: 'left',
        headline_inline: true,
        tagline_show: true,
        tagline_text: `Forge fingerprints every task and shares results across your whole team and CI. Change one file, rebuild one file — everything else is a cache hit.`,
        tagline_text_color: DIM,
        tagline_text_size: 16,
        tagline_text_italic: false,
        layout: 'stack',
        gap: 16,
      }),
      tile('list', {
        items: [
          { text: 'Content-addressed, deterministic', icon: 'check' },
          { text: 'Shared between local & CI',        icon: 'check' },
          { text: 'Works with any language',          icon: 'check' },
        ],
        icon_default: 'check',
        icon_color: ORANGE,
        text_color: TXT,
        text_align: 'left',
        spacing: '12',
        icon_size: '18',
        icon_gap: '11',
        tile_padding: { top: 8, right: 0, bottom: 8, left: 0 },
      }),
    ]),
    col('1-2', [
      tile('info-cards', {
        container_bg: { type: 'solid', color: PANEL },
        container_padding: 20,
        container_gap: 0,
        columns: 1,
        items_gap: 0,
        card_bg: { type: 'solid', color: 'transparent' },
        card_color: DIM,
        card_radius: R(8),
        card_padding: 20,
        show_icon: false,
        show_counter: false,
        show_arrow: false,
        show_footer: false,
        show_media: false,
        title_color: DIM,
        title_font_family: 'sans-serif',
        title_size: 12,
        title_weight: '400',
        description_size: 12,
        card_hover_effect: 'none',
        items: [
          { title: 'dependency graph — cached vs rebuilt nodes', description: '' },
        ],
      }),
    ]),
  ], { gap: 54 }),
]));

/* ═══════════════════════════════════════════════════════════
   5) COMMAND PALETTE ZONE — cta-banner (best-effort).
   Blueprint: .fg-cmd — border 1px solid var(--line-2), border-radius 16px,
   background var(--ink), padding 72px 56px, text-align center.
   Spotlight interattivo non esiste come tile → segnalato come best-effort.
   ═══════════════════════════════════════════════════════════ */
home.push(sec(BG2, 'large', [ row([ col('1-1', [ tile('cta-banner', {
  headline: 'The command palette',
  headline_accent: 'for your whole stack',
  headline_accent_italic: false,
  subtitle: `Run tasks, jump to logs, roll back a deploy or open a PR — without leaving the keyboard.`,
  cta_text: `⌘  forge deploy production`,
  cta_url: '#',
  bg: { type: 'solid', color: INK },
  text_color: WHITE,
  accent_color: ORANGE,
  subtitle_color: DIM,
  cta_bg: PANEL,
  cta_color: TXT,
  cta_border: LINE,
  cta_radius: R(10),
  cta_size: 13,
  headline_font_family: 'sans-serif',
  headline_size: 44,
  headline_weight: '800',
  subtitle_size: 16,
  layout: 'stack',
  vertical_align: 'center',
  banner_radius: R(16),
  banner_padding: 72,
  eyebrow_text: '// everything, one keystroke away',
  eyebrow_color: ORANGE,
  eyebrow_show: true,
}) ]) ]) ]));

/* ═══════════════════════════════════════════════════════════
   6) INTEGRATIONS — section-header centrato + info-cards 6 col.
   Blueprint: .fg-intg — grid 6 col, gap 14px; ogni .fg-intg__c:
   aspect-ratio 1/1, border 1px solid var(--line), border-radius 12px,
   background var(--panel), testo mono 9px. 12 voci (GitHub..+50).
   ═══════════════════════════════════════════════════════════ */
home.push(sec(BG, 'large', [
  row([ col('1-1', [ tile('section-header', {
    eyebrow_show: true,
    eyebrow_text: '// integrations',
    eyebrow_color: ORANGE,
    eyebrow_dot_color: ORANGE,
    eyebrow_separator: '',
    headline_lines: [
      { text: 'Plugs into your', color: WHITE,  italic: false },
      { text: 'toolchain',       color: ORANGE, italic: false },
    ],
    headline_font_family: 'sans-serif',
    headline_font_size: 44,
    headline_font_weight: '800',
    headline_align: 'center',
    headline_inline: true,
    tagline_show: true,
    tagline_text: `Forge runs alongside what you already use — no rip-and-replace.`,
    tagline_text_color: DIM,
    tagline_text_size: 16,
    tagline_text_italic: false,
    layout: 'center',
    gap: 16,
  }) ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0,
    container_gap: 14,
    columns: 6,
    items_gap: 14,
    card_bg: { type: 'solid', color: PANEL },
    card_color: DIM,
    card_radius: R(12),
    card_padding: 18,
    show_icon: true,
    show_counter: false,
    show_arrow: false,
    show_footer: false,
    show_media: false,
    icon_color: WHITE,
    icon_bg_color: 'rgba(255,255,255,.07)',
    title_color: DIM,
    title_font_family: 'sans-serif',
    title_size: 10,
    title_weight: '500',
    title_italic: false,
    description_size: 0,
    card_hover_effect: 'lift',
    items: [
      { icon: 'git-branch',    title: 'GitHub'  },
      { icon: 'git-merge',     title: 'GitLab'  },
      { icon: 'box',           title: 'Docker'  },
      { icon: 'package',       title: 'npm'     },
      { icon: 'terminal',      title: 'Cargo'   },
      { icon: 'code-2',        title: 'Go'      },
      { icon: 'cpu',           title: 'Bazel'   },
      { icon: 'message-square', title: 'Slack'  },
      { icon: 'cloud',         title: 'AWS'     },
      { icon: 'triangle',      title: 'Vercel'  },
      { icon: 'shield',        title: 'Sentry'  },
      { icon: 'plus-circle',   title: '+50'     },
    ],
  }) ]) ]),
]));

/* ═══════════════════════════════════════════════════════════
   7) ACCESSIBILITY / ContrastChecker zone — best-effort.
   Blueprint: .fg-ct — 2 col (1fr 1fr), border 1px var(--line),
   border-radius 14px, background var(--panel), padding 40px.
   Zona interattiva WCAG non esiste come tile → segnalato come best-effort.
   Approssimiamo con section-header + info-cards 3 col (badge PASS/FAIL).
   ═══════════════════════════════════════════════════════════ */
home.push(sec(BG2, 'large', [
  row([ col('1-1', [ tile('section-header', {
    eyebrow_show: true,
    eyebrow_text: '// accessibility',
    eyebrow_color: ORANGE,
    eyebrow_dot_color: ORANGE,
    eyebrow_separator: '',
    headline_lines: [
      { text: 'Ship',         color: WHITE,  italic: false },
      { text: 'accessible',   color: ORANGE, italic: false },
      { text: 'colour',       color: WHITE,  italic: false },
    ],
    headline_font_family: 'sans-serif',
    headline_font_size: 44,
    headline_font_weight: '800',
    headline_align: 'center',
    headline_inline: true,
    tagline_show: true,
    tagline_text: `Pair any text and background and read the WCAG contrast ratio instantly — with AA, AAA and large-text verdicts as you go.`,
    tagline_text_color: DIM,
    tagline_text_size: 16,
    tagline_text_italic: false,
    layout: 'center',
    gap: 16,
  }) ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg: { type: 'solid', color: PANEL },
    container_padding: 32,
    container_gap: 16,
    columns: 3,
    items_gap: 16,
    card_bg: { type: 'solid', color: PANEL2 },
    card_color: DIM,
    card_radius: R(10),
    card_padding: 24,
    show_icon: true,
    show_counter: false,
    show_arrow: false,
    show_footer: true,
    show_media: false,
    icon_color: LIME,
    icon_bg_color: 'rgba(182,232,90,.12)',
    title_color: WHITE,
    title_font_family: 'sans-serif',
    title_size: 14,
    title_weight: '700',
    title_italic: false,
    description_size: 13,
    card_hover_effect: 'none',
    items: [
      { icon: 'check-circle', title: 'AA — Pass',       description: 'Contrast ratio >= 4.5:1. Required for normal text.', footer_text: '21.00:1', footer_dot_color: LIME },
      { icon: 'check-circle', title: 'AAA — Pass',      description: 'Contrast ratio >= 7:1. Enhanced accessibility standard.', footer_text: 'Enhanced', footer_dot_color: LIME },
      { icon: 'check-circle', title: 'AA Large — Pass', description: 'Contrast ratio >= 3:1 for large text (18px+ bold).', footer_text: 'Large text', footer_dot_color: LIME },
    ],
  }) ]) ]),
]));

/* ═══════════════════════════════════════════════════════════
   8) PRICING — pricing ×3 in row.
   Blueprint: .fg-price-grid — 3 col gap 18px.
   .fg-price: background var(--panel), border 1px var(--line), border-radius 16px, padding 30px.
   .fg-price.feat: border-color var(--orange), box-shadow orange glow.
   .fg-price__tag: background rgba(255,106,43,.12), color var(--orange), NON fill arancio solido.
   ═══════════════════════════════════════════════════════════ */
home.push(sec(BG2, 'large', [
  row([ col('1-1', [ tile('section-header', {
    eyebrow_show: true,
    eyebrow_text: '// pricing',
    eyebrow_color: ORANGE,
    eyebrow_dot_color: ORANGE,
    eyebrow_separator: '',
    headline_lines: [
      { text: 'Free to start,', color: WHITE,  italic: false },
      { text: 'scales with CI', color: ORANGE, italic: false },
    ],
    headline_font_family: 'sans-serif',
    headline_font_size: 44,
    headline_font_weight: '800',
    headline_align: 'center',
    headline_inline: true,
    tagline_show: false,
    layout: 'center',
    gap: 16,
  }) ]) ]),
  row([
    col('1-3', [ tile('pricing', {
      plan_name: 'Open Source',
      price: '0',
      currency: '$',
      currency_position: 'before',
      period: '',
      features: 'Unlimited cache\nCommunity runners\nCommunity support',
      check_style: 'checkmark',
      feature_dividers: false,
      is_popular: false,
      bg_color: PANEL,
      price_color: WHITE,
      accent_color: ORANGE,
      text_color: TXT,
      border_radius: R(16),
      cta_text: 'Get started',
      cta_url: '#',
      cta_bg_color: 'rgba(255,255,255,.05)',
      cta_text_color: WHITE,
      cta_width: '100',
      cta_border_width: '1',
      cta_border_color: 'rgba(255,255,255,.16)',
      cta_radius: R(7),
      cta_hover_effect: 'lift',
    }) ]),
    col('1-3', [ tile('pricing', {
      plan_name: 'Team',
      price: '39',
      currency: '$',
      currency_position: 'before',
      period: '/dev/mo',
      features: '500 parallel runners\nRemote cache, private\nInsights & flaky-test detection\nPriority support',
      check_style: 'checkmark',
      feature_dividers: false,
      is_popular: true,
      badge_text: 'Most popular',
      badge_bg_color: 'rgba(255,106,43,.12)',
      badge_text_color: ORANGE,
      badge_style: 'pill',
      bg_color: PANEL,
      price_color: WHITE,
      accent_color: ORANGE,
      text_color: TXT,
      border_radius: R(16),
      cta_text: 'Start 14-day trial',
      cta_url: '#',
      cta_bg_color: ORANGE,
      cta_text_color: WHITE,
      cta_width: '100',
      cta_border_width: '0',
      cta_radius: R(7),
      cta_hover_effect: 'lift',
    }) ]),
    col('1-3', [ tile('pricing', {
      plan_name: 'Enterprise',
      price: 'Custom',
      currency: '',
      currency_position: 'before',
      period: '',
      features: 'Self-hosted runners\nSSO, SAML & audit\nDedicated support',
      check_style: 'checkmark',
      feature_dividers: false,
      is_popular: false,
      bg_color: PANEL,
      price_color: WHITE,
      accent_color: ORANGE,
      text_color: TXT,
      border_radius: R(16),
      cta_text: 'Talk to sales',
      cta_url: '#',
      cta_bg_color: 'rgba(255,255,255,.05)',
      cta_text_color: WHITE,
      cta_width: '100',
      cta_border_width: '1',
      cta_border_color: 'rgba(255,255,255,.16)',
      cta_radius: R(7),
      cta_hover_effect: 'lift',
    }) ]),
  ], { gap: 18, vertical_align: 'stretch' }),
]));

/* ═══════════════════════════════════════════════════════════
   9) CTA finale — 2 bottoni (Start building free + See what's new).
   Blueprint: .fg-cta__box — background linear-gradient(160deg,
   rgba(255,106,43,.16), rgba(255,177,85,.05)), border 1px var(--line-2),
   border-radius 20px, padding 84px, testo centrato.
   ═══════════════════════════════════════════════════════════ */
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('cta-banner', {
  headline: 'Your next build is',
  headline_accent: 'already cached',
  headline_accent_italic: false,
  subtitle: `Install the CLI, point it at your repo, and watch your pipeline collapse from minutes to seconds.`,
  cta_text: 'Start building free',
  cta_url: '#pricing',
  cta2_text: `See what's new`,
  cta2_url: '#changelog',
  cta2_bg: 'rgba(255,255,255,.05)',
  cta2_color: WHITE,
  cta2_border: 'rgba(255,255,255,.16)',
  bg: { type: 'gradient', color: PANEL, gradient_end: INK, gradient_angle: 160 },
  text_color: WHITE,
  accent_color: ORANGE,
  subtitle_color: TXT,
  cta_bg: ORANGE,
  cta_color: WHITE,
  cta_radius: R(7),
  cta_size: 15,
  headline_font_family: 'sans-serif',
  headline_size: 52,
  headline_weight: '800',
  subtitle_size: 17,
  layout: 'stack',
  vertical_align: 'center',
  banner_radius: R(20),
  banner_padding: 80,
  eyebrow_text: '',
  eyebrow_show: false,
}) ]) ]) ]));

/* ═══════════════════════════════════════════════════════════
   EMIT
   ═══════════════════════════════════════════════════════════ */
K.emit({
  slug: 'forge',
  name: 'Forge',
  tags: ['software', 'tech', 'devtools', 'ci', 'build-platform'],
  description: `Forge — build & CI platform for developers. Black + orange, Geologica display+body, Martian Mono code labels. CLI hero con terminale showcase. Feature split, integrazioni, pricing 3 piani. Riproduzione fedele dell'OLOtheme Forge (Software & Tech).`,
  colors: {
    primary:           ORANGE,
    primary_contrast:  WHITE,
    secondary:         AMBER,
    secondary_contrast: INK,
    muted:             BG2,
    muted_contrast:    TXT,
    text:              TXT,
    text_muted:        DIM,
    background:        BG,
    border:            LINE,
    link:              ORANGE,
  },
  css_disp:  `"Geologica", -apple-system, sans-serif`,
  css_sans:  `"Geologica", -apple-system, sans-serif`,
  heading_weight: '800',
  heading_line_height: '1.05',
  google_fonts: ['Geologica', 'Martian Mono'],
  logo_variant: 'light',
  menu: [
    { title: 'Features',     url: '#features' },
    { title: 'Integrations', url: '#integrations' },
    { title: 'Changelog',    url: '#changelog' },
    { title: 'Pricing',      url: '#pricing' },
  ],
  header: {
    bg: 'rgba(18,18,18,.84)',
    text_color: DIM,
    sticky_bg: 'rgba(18,18,18,.94)',
    logo_width: 130,
  },
  footer: {
    bg: BG2,
    headColor: WHITE,
    brand: {
      name: 'Forge',
      tagline: `The build & CI platform for developers. Cache everything, ship faster.`,
    },
    columns: [
      { title: 'Product',    links: ['Features', 'Integrations', 'Pricing', 'Changelog'] },
      { title: 'Developers', links: ['Docs', 'CLI reference', 'API', 'Status'] },
      { title: 'Company',    links: ['About', 'Blog', 'Careers', 'Contact'] },
    ],
    bottom: {
      left:  `© 2026 Forge — an OLOtheme demo.`,
      right: 'Built with OLObuild',
    },
  },
  cursor: {
    blend_mode: 'exclusion',
    ring_color: '#ffffff',
    dot_color: '#ffffff',
  },
}, home);
