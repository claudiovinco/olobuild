/* DataFold — ricomposizione TILE-PURE (image-free). Software & Tech analytics platform.
   Palette: teal #36d6c3 + lime #b6e85a + dark bg #0c1418. Font: Sora (display+body).
   Pixel-perfect pass: hero centrato, stat-strip su BG2, cta2 secondo pulsante, copy esatto,
   LINE2 aggiunto, showcase_badge URL reale, icone integration-grid corrette. */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('df');

const BG     = '#0c1418';
const BG2    = '#0e171c';
const PANEL  = '#13212a';
const PANEL2 = '#182b35';
const INK    = '#070e11';
const TEAL   = '#36d6c3';
const TEALD  = '#25bdab';
const TINT   = 'rgba(54,214,195,.13)';
const LIME   = '#b6e85a';
const AMBER  = '#f0b35a';
const TXT    = '#aebec6';
const DIM    = '#6e8088';
const LINE   = 'rgba(255,255,255,.08)';
const LINE2  = 'rgba(54,214,195,.28)';
const WHITE  = '#ffffff';

const home = [];

// ─── helpers ────────────────────────────────────────────────────────────────

const caption = (txt) => tile('section-header', {
  eyebrow_show: false,
  headline_lines: [{ text: txt, color: DIM, italic: false }],
  headline_font_family: 'sans-serif', headline_font_size: 12, headline_font_weight: '500',
  headline_align: 'center', tagline_show: false, layout: 'center', gap: 0,
});

const shead = (eyebrow, l1, l2, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: TEAL, eyebrow_dot_color: TEAL, eyebrow_separator: '',
  headline_lines: [
    { text: l1, color: WHITE, italic: false },
    { text: l2, color: TEAL,  italic: false },
  ],
  headline_font_family: 'sans-serif', headline_font_size: 46, headline_font_weight: '700',
  headline_align: 'center', headline_inline: true,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false,
  tagline_text_color: DIM, tagline_text_size: 16.5,
  layout: 'center', gap: 16,
});

// ─── 1) HERO + DASHBOARD SHOWCASE ────────────────────────────────────────────
// Blueprint: hero centrato (text-align:center, max-width:840px) con dashboard mockup sotto.
// hero-split con headline_align:'center' e showcase e` la resa piu` fedele disponibile.
home.push(sec(BG, 'large', [row([col('1-1', [tile('hero-split', {
  eyebrow_text: 'v4 · now with real-time models',
  eyebrow_dot_color: TEAL, eyebrow_color: TEAL,
  headline_lines: [
    { text: 'Every metric,',        color: WHITE, italic: false },
    { text: 'one source of truth.', color: TEAL,  italic: false },
  ],
  headline_font_family: 'sans-serif', headline_font_size: 68,
  headline_line_height: 1.05, headline_font_weight: '700', headline_align: 'center',
  subhead: 'DataFold pulls your scattered data into one live workspace — model it once, query in plain SQL, and ship dashboards the whole team actually trusts.',
  subhead_color: TXT, subhead_size: 18, subhead_italic: false, subhead_max_width: 560,
  cta1_text: 'Start free', cta1_url: '#pricing',
  cta1_bg: TEAL, cta1_color: INK, cta1_size: 15, cta1_radius: R(8), cta1_radius_hover: R(8),
  cta2_text: 'See the platform', cta2_url: '#platform',
  cta2_bg: 'rgba(255,255,255,.05)', cta2_color: WHITE,
  cta2_border: 'rgba(255,255,255,.16)', cta2_size: 15, cta2_radius: R(8), cta2_radius_hover: R(8),
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: PANEL },
  showcase_padding: 24, showcase_radius: R(16), showcase_radius_hover: R(16),
  showcase_badge_text: 'app.datafold.io / workspace / revenue',
  showcase_badge_dot: TEAL, showcase_badge_bg: INK, showcase_badge_color: TXT,
  showcase_items: [
    { number: 'Net revenue',     text: '$4.82M', italic: false, text_color: WHITE, bg: { type: 'solid', color: BG2 } },
    { number: 'Active accounts', text: '12,408', italic: false, text_color: WHITE, bg: { type: 'solid', color: BG2 } },
    { number: 'Churn',           text: '1.9%',   italic: false, text_color: AMBER, bg: { type: 'solid', color: BG2 } },
    { number: 'MoM revenue',     text: '+18.4%', italic: false, text_color: TEAL,  bg: { type: 'solid', color: BG2 } },
  ],
  showcase_card_radius: R(11), showcase_card_radius_hover: R(11), showcase_card_shadow: 'none',
  showcase_caption_left: 'REVENUE', showcase_caption_right: 'LIVE', showcase_hover_effect: 'none',
  split_ratio: '1fr 1fr', gap: 52, min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
})])])]));

// ─── 2) LOGO CLOUD ───────────────────────────────────────────────────────────
// Blueprint: caption mono uppercase centrata + 6 placeholder-pill (layout wrap centrato)
home.push(sec(BG2, 'small', [
  row([col('1-1', [caption('Powering analytics at data-driven teams')])]),
  row([col('1-1', [tile('trust-strip', {
    items: [
      { text: 'NORTHBEAM' }, { text: 'ARCTYPE' }, { text: 'SEGMENT' },
      { text: 'LIVEBLOCKS' }, { text: 'PRIMER' }, { text: 'HEAP' },
    ],
    variant: 'pill', separator_char: '', align: 'center', flow: 'wrap', gap: 46,
    font_family: 'sans-serif', text_color: DIM, text_size: 13,
    pill_bg: 'rgba(255,255,255,.05)', pill_border: LINE, pill_text_color: DIM,
  })])], { gap: 16 }),
]));

// ─── 3) STAT STRIP ───────────────────────────────────────────────────────────
// Blueprint: background var(--bg-2) = BG2, border-block 1px solid var(--line)
const stat = (number, suffix, label) => col('1-4', [tile('counter', {
  number, suffix, prefix: '', label, icon_emoji: '',
  text_color: WHITE, number_color: TEAL,
  number_font_size: '50', number_font_weight: '700',
  label_color: DIM, label_font_size: '13',
  bg_type: 'color', bg_color: 'transparent', padding: '8', border_radius: '0',
})]);

home.push(sec(BG2, 'small', [row([
  stat('14', 'B+',   'Rows queried daily'),
  stat('220', 'ms',  'Median query time'),
  stat('180', '+',   'Native connectors'),
  stat('99.98', '%', 'Pipeline uptime'),
], { gap: 24 })]));

// ─── 4) METRIC CHART TILE → section-header + chart bar MRR + chart donut + chart bar KPI ──
// Blueprint: (1) tall card bar chart MRR Jan-Oct con valori proporzionali (34%..100%)
// (2) donut “Revenue by plan” Team 62% / Business 19% / Enterprise 11% / Starter 8%
// (3) mini-grid KPI progress: Activation 74%, Retention 90d 91%, NPS 79%, Expansion 100%
// Tile `chart` calza perfettamente per tutte e 3 le card → zero counter in questa sezione.
// Layout: riga 1 = bar chart (2/3) + donut (1/3); riga 2 = bar orizzontale KPI full-width.
home.push(sec(BG2, 'large', [
  row([col('1-1', [shead('// the workspace', 'Dashboards that read', 'like the truth',
    'Build a metric once and reuse it everywhere. Every chart is live, drillable, and tied back to the model — no more six versions of “revenue”.')])]),
  row([
    col('2-3', [tile('chart', {
      chart_type: 'bar',
      dataset_label: 'MRR $',
      show_title: true, chart_title: 'Monthly recurring revenue',
      title_color: WHITE, title_font_size: '14', title_font_weight: '600',
      show_subtitle: true, chart_subtitle: '$4.82M · ▲ 18.4%',
      subtitle_color: TEAL, subtitle_font_size: '12',
      items: [
        { id: 'c-1',  label: 'Jan', value: '34',  color: TEAL, border_color: '' },
        { id: 'c-2',  label: 'Feb', value: '42',  color: TEAL, border_color: '' },
        { id: 'c-3',  label: 'Mar', value: '48',  color: TEAL, border_color: '' },
        { id: 'c-4',  label: 'Apr', value: '44',  color: TEAL, border_color: '' },
        { id: 'c-5',  label: 'May', value: '58',  color: TEAL, border_color: '' },
        { id: 'c-6',  label: 'Jun', value: '64',  color: TEAL, border_color: '' },
        { id: 'c-7',  label: 'Jul', value: '72',  color: TEAL, border_color: '' },
        { id: 'c-8',  label: 'Aug', value: '80',  color: TEAL, border_color: '' },
        { id: 'c-9',  label: 'Sep', value: '92',  color: TEALD, border_color: '' },
        { id: 'c-10', label: 'Oct', value: '100', color: TEALD, border_color: '' },
      ],
      chart_height: '220',
      show_legend: false,
      tooltip_enabled: true, tooltip_prefix: '$', tooltip_suffix: 'k',
      tooltip_bg: PANEL, tooltip_text_color: WHITE,
      tooltip_corner_radius: { tl: 6, tr: 6, br: 6, bl: 6 },
      animate: true, begin_at_zero: true,
      bar_radius: { tl: 4, tr: 4, br: 0, bl: 0 },
      bar_percentage: 0.65, category_percentage: 0.85,
      grid_color: 'rgba(255,255,255,.06)', axis_color: 'rgba(255,255,255,.10)',
      text_color: DIM, tick_font_size: '10',
      bg_color: 'transparent',
      bg: { type: 'solid', color: PANEL },
      border: { top: 0, right: 0, bottom: 0, left: 0, style: 'solid', color: LINE },
      tile_padding: { top: 20, right: 20, bottom: 20, left: 20 },
      border_radius: { tl: 14, tr: 14, br: 14, bl: 14 },
    })]),
    col('1-3', [tile('chart', {
      chart_type: 'doughnut',
      dataset_label: 'Plan share',
      show_title: true, chart_title: 'Revenue by plan',
      title_color: WHITE, title_font_size: '14', title_font_weight: '600',
      show_subtitle: false,
      items: [
        { id: 'd-1', label: 'Team',       value: '62', color: TEAL,                       border_color: '' },
        { id: 'd-2', label: 'Business',   value: '19', color: LIME,                       border_color: '' },
        { id: 'd-3', label: 'Enterprise', value: '11', color: AMBER,                      border_color: '' },
        { id: 'd-4', label: 'Starter',    value: '8',  color: 'rgba(255,255,255,.18)',    border_color: '' },
      ],
      chart_height: '220',
      show_legend: true, legend_position: 'bottom', legend_align: 'center',
      legend_color: DIM, legend_font_size: '11', legend_point_style: true,
      doughnut_cutout: 62,
      tooltip_enabled: true, tooltip_suffix: '%',
      tooltip_bg: PANEL, tooltip_text_color: WHITE,
      tooltip_corner_radius: { tl: 6, tr: 6, br: 6, bl: 6 },
      animate: true,
      bg_color: 'transparent',
      bg: { type: 'solid', color: PANEL },
      border: { top: 0, right: 0, bottom: 0, left: 0, style: 'solid', color: LINE },
      tile_padding: { top: 20, right: 20, bottom: 20, left: 20 },
      border_radius: { tl: 14, tr: 14, br: 14, bl: 14 },
    })]),
  ], { gap: 16 }),
  row([
    col('1-1', [tile('chart', {
      chart_type: 'bar',
      index_axis: 'y',
      dataset_label: 'Score',
      show_title: true, chart_title: 'Platform health',
      title_color: WHITE, title_font_size: '14', title_font_weight: '600',
      show_subtitle: false,
      items: [
        { id: 'k-1', label: 'Activation',    value: '74',  color: LIME,  border_color: '' },
        { id: 'k-2', label: 'Retention 90d', value: '91',  color: TEAL,  border_color: '' },
        { id: 'k-3', label: 'NPS',           value: '79',  color: TEAL,  border_color: '' },
        { id: 'k-4', label: 'Expansion',     value: '100', color: TEALD, border_color: '' },
      ],
      chart_height: '180',
      show_legend: false,
      tooltip_enabled: true, tooltip_suffix: '%',
      tooltip_bg: PANEL, tooltip_text_color: WHITE,
      tooltip_corner_radius: { tl: 6, tr: 6, br: 6, bl: 6 },
      animate: true, begin_at_zero: true,
      bar_radius: { tl: 4, tr: 4, br: 4, bl: 4 },
      bar_percentage: 0.55, category_percentage: 0.85,
      y_max: '120',
      grid_color: 'rgba(255,255,255,.06)', axis_color: 'rgba(255,255,255,.10)',
      text_color: DIM, tick_font_size: '10',
      bg_color: 'transparent',
      bg: { type: 'solid', color: PANEL },
      border: { top: 0, right: 0, bottom: 0, left: 0, style: 'solid', color: LINE },
      tile_padding: { top: 20, right: 20, bottom: 20, left: 20 },
      border_radius: { tl: 14, tr: 14, br: 14, bl: 14 },
    })]),
  ], { gap: 16 }),
]));

// ─── 5) FEATURE SPLIT — modelling ────────────────────────────────────────────
// Blueprint: testo sx + media panel dx (codice SQL mono). showcase con item check-list.
home.push(sec(BG, 'large', [row([col('1-1', [tile('hero-split', {
  eyebrow_text: '// modelling', eyebrow_dot_color: TEAL, eyebrow_color: TEAL,
  headline_lines: [
    { text: 'Model once,',   color: WHITE, italic: false },
    { text: 'in plain SQL.', color: WHITE, italic: false },
  ],
  headline_font_family: 'sans-serif', headline_font_size: 44,
  headline_line_height: 1.08, headline_font_weight: '700', headline_align: 'left',
  subhead: 'Define your metrics as version-controlled models. DataFold compiles them into fast, governed queries — so everyone’s numbers come from the same place.',
  subhead_color: TXT, subhead_size: 16, subhead_italic: false, subhead_max_width: 500,
  cta1_text: 'Read the docs', cta1_url: '#',
  cta1_bg: 'rgba(255,255,255,.05)', cta1_color: WHITE,
  cta1_border: 'rgba(255,255,255,.16)', cta1_size: 14, cta1_radius: R(8), cta1_radius_hover: R(8),
  cta2_text: '', cta2_url: '',
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: PANEL },
  showcase_padding: 24, showcase_radius: R(14), showcase_radius_hover: R(14),
  showcase_badge_text: 'GIT-BACKED SEMANTIC LAYER',
  showcase_badge_dot: TEAL, showcase_badge_bg: INK, showcase_badge_color: TXT,
  showcase_items: [
    { number: 'Git-backed semantic layer',          text: 'check', italic: false, text_color: TEAL, bg: { type: 'solid', color: BG2 } },
    { number: 'Column-level lineage',               text: 'check', italic: false, text_color: TEAL, bg: { type: 'solid', color: BG2 } },
    { number: 'Tests that block bad data shipping', text: 'check', italic: false, text_color: TEAL, bg: { type: 'solid', color: BG2 } },
  ],
  showcase_card_radius: R(10), showcase_card_radius_hover: R(10), showcase_card_shadow: 'none',
  showcase_caption_left: 'MODELLING', showcase_caption_right: 'v4', showcase_hover_effect: 'none',
  split_ratio: '1fr 1fr', gap: 48, min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
})])])]));

// ─── 6) FEATURE SPLIT — monitoring (flip) ────────────────────────────────────
// Blueprint: media sx (flip) + testo dx. layout_direction:'reverse'.
home.push(sec(BG2, 'large', [row([col('1-1', [tile('hero-split', {
  eyebrow_text: '// monitoring', eyebrow_dot_color: TEAL, eyebrow_color: TEAL,
  headline_lines: [
    { text: 'It tells you',     color: WHITE, italic: false },
    { text: 'before they ask.', color: WHITE, italic: false },
  ],
  headline_font_family: 'sans-serif', headline_font_size: 44,
  headline_line_height: 1.08, headline_font_weight: '700', headline_align: 'left',
  subhead: 'DataFold watches every metric for anomalies and routes the alert to the right channel — with the context to act, not just a red number.',
  subhead_color: TXT, subhead_size: 16, subhead_italic: false, subhead_max_width: 500,
  cta1_text: 'See monitoring', cta1_url: '#',
  cta1_bg: 'rgba(255,255,255,.05)', cta1_color: WHITE,
  cta1_border: 'rgba(255,255,255,.16)', cta1_size: 14, cta1_radius: R(8), cta1_radius_hover: R(8),
  cta2_text: '', cta2_url: '',
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: PANEL },
  showcase_padding: 24, showcase_radius: R(14), showcase_radius_hover: R(14),
  showcase_badge_text: 'ANOMALY DETECTION · ML-POWERED',
  showcase_badge_dot: AMBER, showcase_badge_bg: INK, showcase_badge_color: TXT,
  showcase_items: [
    { number: 'ML anomaly detection on any metric',  text: 'zap', italic: false, text_color: TEAL, bg: { type: 'solid', color: BG2 } },
    { number: 'Slack, email and webhook routing',    text: 'zap', italic: false, text_color: TEAL, bg: { type: 'solid', color: BG2 } },
    { number: 'Root-cause drill-downs in one click', text: 'zap', italic: false, text_color: TEAL, bg: { type: 'solid', color: BG2 } },
  ],
  showcase_card_radius: R(10), showcase_card_radius_hover: R(10), showcase_card_shadow: 'none',
  showcase_caption_left: 'MONITORING', showcase_caption_right: 'LIVE', showcase_hover_effect: 'none',
  split_ratio: '1fr 1fr', gap: 48, min_height: 0, layout_direction: 'reverse',
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
})])])]));

// ─── 7) DATA SOURCES / INTEGRATION GRID ─────────────────────────────────────
// [BEST-EFFORT / SEGNALATO] Blueprint: grid 6 colonne, card aspect-ratio 1:1, border 1px
// var(--line), radius 14px, bg var(--panel), icon placeholder 30x30 + label mono.
// NEW tile candidato IntegrationGrid non esiste → resa con info-cards 6 colonne.
// Icone: nomi Lucide corretti (life-buoy rimosso → life-buoy non esiste in Lucide,
// usare help-circle; table non esiste → grid, archive → archive OK).
home.push(sec(BG, 'large', [
  row([col('1-1', [shead('// data sources', 'Connect everything,', 'sync both ways',
    '180+ native connectors. Point DataFold at your warehouse and it does the rest.')])]),
  row([col('1-1', [tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0, container_gap: 16, columns: 6, items_gap: 14,
    card_bg: { type: 'solid', color: PANEL }, card_color: DIM,
    card_radius: R(14), card_padding: 20,
    show_icon: true, show_counter: false, show_arrow: false, show_footer: false, show_media: false,
    icon_color: TEAL, icon_bg_color: TINT, title_color: WHITE,
    title_font_family: 'sans-serif', title_size: 13, title_weight: '500', title_italic: false,
    description_size: 13,
    items: [
      { icon: 'database',      title: 'Warehouse',    description: '' },
      { icon: 'server',        title: 'Postgres',     description: '' },
      { icon: 'zap',           title: 'Events',       description: '' },
      { icon: 'credit-card',   title: 'Billing',      description: '' },
      { icon: 'users',         title: 'CRM',          description: '' },
      { icon: 'globe',         title: 'Ads',          description: '' },
      { icon: 'archive',       title: 'Object store', description: '' },
      { icon: 'grid',          title: 'Sheets',       description: '' },
      { icon: 'help-circle',   title: 'Support',      description: '' },
      { icon: 'cpu',           title: 'Product',      description: '' },
      { icon: 'activity',      title: 'Queue',        description: '' },
      { icon: 'plus-circle',   title: '+170',         description: '' },
    ],
    card_hover_effect: 'lift',
  })])]),
]));

// ─── 8) PRICING ──────────────────────────────────────────────────────────────
// Blueprint: 3 card, piano featured con border-color TEAL + box-shadow TEAL.
// Copy esatto dal blueprint (& → and, apostrofi diretti).
home.push(sec(BG2, 'large', [
  row([col('1-1', [shead('// pricing', 'Pay for seats,', 'not for rows', '')])]),
  row([
    col('1-3', [tile('pricing', {
      plan_name: 'Starter', price: '$0', currency: '', period: '/mo',
      description: 'For analysts kicking the tyres.',
      features: '1 workspace\n5 data sources\nCommunity support',
      is_popular: false, badge_text: '', badge_bg_color: TEAL,
      cta_text: 'Get started', cta_url: '#',
      cta_bg: 'rgba(255,255,255,.07)', cta_color: WHITE,
      bg_color: PANEL, price_color: WHITE, accent_color: TEAL,
      border_color: LINE, border_radius: R(18), border_radius_hover: R(18),
    })]),
    col('1-3', [tile('pricing', {
      plan_name: 'Team', price: '$32', currency: '', period: '/user/mo',
      description: 'For teams running on real numbers.',
      features: 'Unlimited dashboards\nAll 180+ connectors\nAnomaly monitoring\nSemantic layer',
      is_popular: true, badge_text: 'Most popular', badge_bg_color: TEAL,
      cta_text: 'Start 14-day trial', cta_url: '#pricing',
      cta_bg: TEAL, cta_color: INK,
      bg_color: PANEL, price_color: WHITE, accent_color: TEAL,
      border_color: TEAL, border_radius: R(18), border_radius_hover: R(18),
    })]),
    col('1-3', [tile('pricing', {
      plan_name: 'Enterprise', price: 'Custom', currency: '', period: '',
      description: 'For scale, security and governance.',
      features: 'SSO, SCIM and audit log\nRow-level security\nDedicated support',
      is_popular: false, badge_text: '', badge_bg_color: TEAL,
      cta_text: 'Talk to sales', cta_url: '#',
      cta_bg: 'rgba(255,255,255,.07)', cta_color: WHITE,
      bg_color: PANEL, price_color: WHITE, accent_color: TEAL,
      border_color: LINE, border_radius: R(18), border_radius_hover: R(18),
    })]),
  ], { gap: 18, vertical_align: 'stretch' }),
]));

// ─── 9) TESTIMONIAL ──────────────────────────────────────────────────────────
// Blueprint: quote con <em>one number means one thing</em> (corsivo), attrib. mono.
home.push(sec(BG, 'large', [row([col('1-1', [tile('testimonial', {
  quote: '“We deleted four spreadsheets and a BI tool the week we switched. Now one number means one thing — and the whole company finally agrees on it.”',
  author_name: 'Priya Nair', author_role: 'Head of Data, Northbeam',
  rating: '0', layout: 'single', show_line: false,
  bg_color: 'transparent', text_color: WHITE, border_radius: '0', avatar: '',
})])])]));

// ─── 10) CTA ─────────────────────────────────────────────────────────────────
// Blueprint: 2 bottoni — "Start free" (teal) + "See customer stories" (ghost).
// Bordo box: border:1px solid var(--line-2) = LINE2. Gradient bg come blueprint.
home.push(sec(BG2, 'large', [row([col('1-1', [tile('cta-banner', {
  headline: 'Put your data', headline_accent: 'to work', headline_accent_italic: false,
  subtitle: 'Connect a warehouse and ship your first trusted dashboard this afternoon. Free to start, no card.',
  cta_text: 'Start free', cta_url: '#pricing',
  cta2_text: 'See customer stories', cta2_url: '#',
  cta2_bg: 'transparent', cta2_color: WHITE, cta2_border: LINE2,
  bg: { type: 'gradient', color: 'rgba(54,214,195,.16)', color2: 'rgba(182,232,90,.05)', angle: 160 },
  text_color: WHITE, accent_color: TEAL, subtitle_color: TXT,
  cta_bg: TEAL, cta_color: INK, cta_radius: R(8), cta_size: 15,
  headline_font_family: 'sans-serif', headline_size: 50, headline_weight: '700',
  subtitle_size: 17, layout: 'stack', vertical_align: 'center',
  banner_radius: R(24), banner_padding: 80,
})])])]));

// ─── EMIT ────────────────────────────────────────────────────────────────────
K.emit({
  slug: 'datafold', name: 'DataFold',
  tags: ['software', 'tech', 'saas', 'analytics', 'dashboard'],
  description: 'DataFold — analytics platform SaaS. Dark teal + lime, Sora (display+body). Hero dashboard showcase centrato, stat strip, KPI counter grid, feature splits, data source grid, pricing 3 piani, testimonial, CTA 2 pulsanti. Riproduzione fedele dell\'OLOtheme DataFold.',
  colors: {
    primary: TEAL, primary_contrast: INK,
    secondary: LIME, secondary_contrast: INK,
    muted: PANEL, muted_contrast: TXT,
    text: TXT, text_muted: DIM,
    background: BG, border: LINE, link: TEAL,
  },
  css_disp:  '"Sora", -apple-system, sans-serif',
  css_sans:  '"Sora", -apple-system, sans-serif',
  heading_weight: '700', heading_line_height: '1.06',
  google_fonts: ['Sora'],
  logo_variant: 'light',
  menu: [
    { title: 'Platform',     url: '#platform' },
    { title: 'Data sources', url: '#sources'  },
    { title: 'Customers',    url: '#'         },
    { title: 'Pricing',      url: '#pricing'  },
  ],
  header: {
    bg: 'rgba(12,20,24,.82)', text_color: DIM,
    sticky_bg: 'rgba(12,20,24,.92)', logo_width: 138,
  },
  footer: {
    bg: BG2, headColor: WHITE,
    brand: {
      name: 'DataFold',
      tagline: 'One live workspace for every metric. Model once, query anywhere, trust the number.',
    },
    columns: [
      { title: 'Product',    links: ['Platform', 'Data sources', 'Pricing', 'Changelog'] },
      { title: 'Developers', links: ['Docs', 'SQL reference', 'API', 'Status'] },
      { title: 'Company',    links: ['Customers', 'About', 'Careers', 'Contact'] },
    ],
    bottom: { left: '© 2026 DataFold — an OLOtheme demo.', right: 'Built with OLObuild' },
  },
  cursor: { blend_mode: 'exclusion', ring_color: TEAL, dot_color: TEAL },
}, home);
