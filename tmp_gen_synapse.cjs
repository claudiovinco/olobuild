/* Synapse — ricomposizione TILE-PURE (image-free). Software & Tech · AI workspace. Violet + ink. */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('sy');

// ── palette esatta da :root ──────────────────────────────────────────────────
const BG    = '#140e22';
const BG2   = '#171029';
const PANEL = '#1e1633';
const PANEL2= '#271d42';
const INK   = '#0c0818';
const VIOLET= '#a06bff';
const VIOLETD='#8a52f0';
const VTINT = 'rgba(160,107,255,.14)';
const CYAN  = '#5ad8e0';
const PINK  = '#ff7ad1';
const TXT   = '#b3a9cc';
const DIM   = '#776e92';
const LINE  = 'rgba(255,255,255,.08)';
const LINE2 = 'rgba(160,107,255,.4)';
const WHITE = '#ffffff';

const home = [];

// ─── helper: caption solo-headline piccola (label striscia logo) ─────────────
const caption = (txt) => tile('section-header', {
  eyebrow_show: false,
  headline_lines: [{ text: txt, color: DIM, italic: false }],
  headline_font_family: 'sans-serif', headline_font_size: 12, headline_font_weight: '400',
  headline_align: 'center',
  tagline_show: false, layout: 'center', gap: 0,
});

// ─── helper: section-header centrato con eyebrow + headline inline ───────────
const shead = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: VIOLET, eyebrow_dot_color: VIOLET, eyebrow_separator: '',
  headline_lines: [
    { text: l1, color: WHITE, italic: false },
    { text: accent, color: VIOLET, italic: false },
  ],
  headline_font_family: 'sans-serif', headline_font_size: 46, headline_font_weight: '700',
  headline_align: 'center', headline_inline: true,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false,
  tagline_text_color: DIM, tagline_text_size: 16.5,
  layout: 'center', gap: 16,
});

// ─── helper: section-header sinistra (feature splits) ───────────────────────
const sheadLeft = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: VIOLET, eyebrow_dot_color: VIOLET, eyebrow_separator: '',
  headline_lines: [
    { text: l1, color: WHITE, italic: false },
    { text: accent, color: VIOLET, italic: false },
  ],
  headline_font_family: 'sans-serif', headline_font_size: 40, headline_font_weight: '700',
  headline_align: 'left', headline_inline: true,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false,
  tagline_text_color: DIM, tagline_text_size: 16,
  layout: 'stack', gap: 12,
});

// ─── helper: stat col ────────────────────────────────────────────────────────
const stat = (prefix, number, suffix, label) => col('1-4', [tile('counter', {
  number, suffix, prefix, label, icon_emoji: '',
  text_color: WHITE, number_color: VIOLET,
  number_font_size: '48', number_font_weight: '700', label_color: DIM, label_font_size: '11.5',
  bg_type: 'color', bg_color: 'transparent', padding: '8', border_radius: '0',
})]);

// ════════════════════════════════════════════════════════════════════════════
// 1) HERO — centrato, showcase = chat mock (hero-split center con showcase)
// ════════════════════════════════════════════════════════════════════════════
home.push(sec(BG, 'large', [row([col('1-1', [tile('hero-split', {
  eyebrow_text: `Synapse 3 · now with long-term memory`,
  eyebrow_dot_color: VIOLET, eyebrow_color: VIOLET,
  headline_lines: [
    { text: 'The AI workspace', color: WHITE, italic: false },
    { text: 'that remembers.', color: VIOLET, italic: false },
  ],
  headline_font_family: 'sans-serif', headline_font_size: 72, headline_line_height: 1.0,
  headline_font_weight: '700', headline_align: 'center',
  subhead: `Chat, agents and your company's knowledge in one place — grounded in your docs, your data and every conversation you've had before.`,
  subhead_color: DIM, subhead_size: 18, subhead_italic: false, subhead_max_width: 560,
  cta1_text: 'Try free', cta1_url: '#pricing',
  cta1_bg: VIOLET, cta1_color: WHITE, cta1_size: 15, cta1_radius: R(9), cta1_radius_hover: R(9),
  cta2_text: 'See how it works', cta2_url: '#features',
  cta2_bg: 'rgba(255,255,255,.05)', cta2_color: WHITE, cta2_border: 'rgba(255,255,255,.16)',
  cta2_size: 15, cta2_radius: R(9), cta2_radius_hover: R(9),
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: PANEL },
  showcase_padding: 22,
  showcase_radius: R(16), showcase_radius_hover: R(16),
  showcase_badge_text: 'SYNAPSE · WORKSPACE',
  showcase_badge_dot: VIOLET, showcase_badge_bg: INK, showcase_badge_color: WHITE,
  showcase_items: [
    { number: 'You', text: `Summarise last week's customer calls and flag anything about pricing.`, italic: false, text_color: WHITE, bg: { type: 'solid', color: VIOLET } },
    { number: 'Synapse', text: `Across 9 calls: 3 flagged pricing — two want annual billing, one found the Team tier "a jump". Drafted a follow-up for each. Want me to send?`, italic: false, text_color: TXT, bg: { type: 'solid', color: PANEL2 } },
    { number: 'You', text: 'Yes, and add them to the CRM.', italic: false, text_color: WHITE, bg: { type: 'solid', color: VIOLET } },
  ],
  showcase_card_radius: R(12), showcase_card_radius_hover: R(12), showcase_card_shadow: 'none',
  showcase_caption_left: 'CHAT', showcase_caption_right: 'AI WORKSPACE',
  showcase_hover_effect: 'none',
  split_ratio: '1fr 1fr', gap: 52, min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
})])])]));

// ════════════════════════════════════════════════════════════════════════════
// 2) LOGO CLOUD (caption + trust-strip pill)
// Blueprint: .sy-logos — bg BG2, border-bottom LINE, padding 44px 0
// ════════════════════════════════════════════════════════════════════════════
home.push(sec(BG2, 'small', [
  row([col('1-1', [caption(`The workspace for teams who'd rather think than search`)])]),
  row([col('1-1', [tile('trust-strip', {
    items: [
      { text: 'VERCEL' }, { text: 'LINEAR' }, { text: 'NOTION' },
      { text: 'GITHUB' }, { text: 'FIGMA' }, { text: 'STRIPE' },
    ],
    variant: 'pill', separator_char: '', align: 'center', flow: 'wrap', gap: 46,
    font_family: 'sans-serif', text_color: DIM, text_size: 13,
    pill_bg: 'rgba(255,255,255,.06)', pill_border: LINE, pill_text_color: DIM,
  })])], { gap: 16 }),
]));

// ════════════════════════════════════════════════════════════════════════════
// 3) STAT STRIP — counter ×4
// Blueprint: .sy-stats — bg BG2, border-block LINE, text-center, 4 col
// ════════════════════════════════════════════════════════════════════════════
home.push(sec(BG2, 'small', [row([
  stat('', '9',    'h',  'Saved / person / week'),
  stat('', '120',  '+',  'Connected sources'),
  stat('', '40',   'k',  'Teams onboard'),
  stat('', '99.9', '%',  'Uptime'),
], { gap: 24 })]));

// ════════════════════════════════════════════════════════════════════════════
// 4) FEATURE SPLIT 1 — Memory (section-header left + iconlist + media card right)
// Blueprint: .sy-feat 2-col, gap 54px, sinistra = eyebrow+h2+p+list+btn ghost
//            destra = .sy-feat__media (border LINE, radius 16, bg PANEL, chrome bar PANEL2)
// ════════════════════════════════════════════════════════════════════════════
home.push(sec(BG, 'large', [
  row([
    col('1-2', [
      sheadLeft(
        '// memory',
        'It learns your ',
        'context, once',
        `Synapse builds a private memory of your projects, people and decisions — so you stop re-explaining yourself and start where you left off.`
      ),
      tile('iconlist', {
        items: [
          { text: 'Grounded in your docs & data', icon: 'check' },
          { text: 'Per-workspace, private by default', icon: 'check' },
          { text: 'Cites its sources, every time', icon: 'check' },
        ],
        icon_color: VIOLET, text_color: TXT, text_size: '14.5',
        icon_shape: 'none', gap: '12', layout: 'vertical',
      }),
    ]),
    col('1-2', [tile('info-cards', {
      container_bg: { type: 'solid', color: 'transparent' }, container_padding: 0, container_gap: 0,
      columns: 1, items_gap: 0,
      card_bg: { type: 'solid', color: PANEL }, card_color: DIM,
      card_radius: R(16), card_padding: 32,
      show_icon: true, show_counter: false, show_arrow: false, show_footer: false, show_media: false,
      icon_color: VIOLET, icon_bg_color: VTINT, title_color: WHITE,
      title_font_family: 'sans-serif', title_size: 18, title_weight: '600', title_italic: false,
      description_size: 14,
      items: [
        { icon: 'database', title: 'Private memory graph', description: `Connected to your docs, Slack, GitHub and every tool in your stack — so context lives where your team already works.` },
      ],
      card_hover_effect: 'none',
    })]),
  ], { gap: 54 }),
]));

// ════════════════════════════════════════════════════════════════════════════
// 5) PROMPT ZONE — "ask anything" spotlight (best-effort: cta-banner)
// Blueprint: .sy-sec.panel (bg BG2) — .sy-prompt border LINE2, radius 18, bg INK
// [SEGNALATA] best-effort: manca prompt-bar animata e spotlight cursor
// ════════════════════════════════════════════════════════════════════════════
home.push(sec(BG2, 'large', [row([col('1-1', [tile('cta-banner', {
  headline: 'One box for your', headline_accent: 'whole company',
  headline_accent_italic: false,
  subtitle: `Move your cursor here. Ask in plain language — Synapse pulls from every tool you've connected and answers with sources.`,
  cta_text: 'Ask anything', cta_url: '#features',
  bg: { type: 'solid', color: INK }, text_color: WHITE,
  accent_color: VIOLET, subtitle_color: DIM,
  cta_bg: VIOLET, cta_color: WHITE, cta_radius: R(9), cta_size: 15,
  headline_font_family: 'sans-serif', headline_size: 46, headline_weight: '700', subtitle_size: 16,
  layout: 'stack', vertical_align: 'center',
  banner_radius: R(18), banner_padding: 64,
})])])]));

// ════════════════════════════════════════════════════════════════════════════
// 6) INTEGRATIONS — section-header + info-cards 6-col (griglia integrazioni)
// Blueprint: .sy-intg grid 6-col gap 14, card aspect 1/1, border LINE, radius 12
// [SEGNALATA] best-effort: info-cards non è aspect-ratio square
// ════════════════════════════════════════════════════════════════════════════
home.push(sec(BG, 'large', [
  row([col('1-1', [shead('// integrations', 'Connected to ', 'everything', 'Plug in your stack and Synapse reasons across all of it.')])]),
  row([col('1-1', [tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' }, container_padding: 0, container_gap: 14,
    columns: 6, items_gap: 14,
    card_bg: { type: 'solid', color: PANEL }, card_color: DIM,
    card_radius: R(12), card_padding: 16,
    show_icon: true, show_counter: false, show_arrow: false, show_footer: false, show_media: false,
    icon_color: VIOLET, icon_bg_color: 'rgba(255,255,255,.07)', title_color: DIM,
    title_font_family: 'sans-serif', title_size: 9, title_weight: '400', title_italic: false,
    description_size: 0,
    items: [
      { icon: 'hard-drive', title: 'Drive' },
      { icon: 'book-open',  title: 'Notion' },
      { icon: 'message-square', title: 'Slack' },
      { icon: 'github',    title: 'GitHub' },
      { icon: 'check-square', title: 'Linear' },
      { icon: 'mail',      title: 'Gmail' },
      { icon: 'database',  title: 'Salesforce' },
      { icon: 'figma',     title: 'Figma' },
      { icon: 'clipboard', title: 'Jira' },
      { icon: 'layers',    title: 'Confluence' },
      { icon: 'users',     title: 'Zendesk' },
      { icon: 'zap',       title: '+110' },
    ],
    card_hover_effect: 'lift',
  })])]),
]));

// ════════════════════════════════════════════════════════════════════════════
// 7) PRICING — 3 pricing tile in row
// Blueprint: .sy-price bg PANEL, border LINE, radius 18, padding 30
//            featured: border VIOLET + box-shadow glow violet
// ════════════════════════════════════════════════════════════════════════════
home.push(sec(BG2, 'large', [
  row([col('1-1', [shead('// pricing', 'Start free, ', 'scale per seat')])]),
  row([
    col('1-3', [tile('pricing', {
      plan_name: 'Personal', price: '0', currency: '$', period: '',
      description: 'For individuals getting started.',
      features: 'Unlimited chats\n3 connected sources\n7-day memory',
      is_popular: false, badge_text: '', badge_bg_color: '',
      bg_color: PANEL, price_color: WHITE, accent_color: VIOLET,
      cta_text: 'Get started', cta_url: '#', cta_bg: 'rgba(255,255,255,.08)',
      cta_color: WHITE, cta_border: 'rgba(255,255,255,.18)',
      border_radius: R(18),
    })]),
    col('1-3', [tile('pricing', {
      plan_name: 'Team', price: '24', currency: '$', period: '/seat/mo',
      description: 'For teams that run on shared context.',
      features: 'Unlimited memory\nAll integrations\nShared agents\nAdmin & SSO',
      is_popular: true, badge_text: 'Most popular', badge_bg_color: VTINT,
      bg_color: PANEL, price_color: WHITE, accent_color: VIOLET,
      cta_text: 'Start free trial', cta_url: '#', cta_bg: VIOLET, cta_color: WHITE,
      border_radius: R(18),
    })]),
    col('1-3', [tile('pricing', {
      plan_name: 'Enterprise', price: 'Custom', currency: '', period: '',
      description: 'For scale, security & control.',
      features: 'Private deployment\nSCIM & audit log\nDedicated support',
      is_popular: false, badge_text: '', badge_bg_color: '',
      bg_color: PANEL, price_color: WHITE, accent_color: VIOLET,
      cta_text: 'Talk to sales', cta_url: '#', cta_bg: 'rgba(255,255,255,.08)',
      cta_color: WHITE, cta_border: 'rgba(255,255,255,.18)',
      border_radius: R(18),
    })]),
  ], { gap: 18, vertical_align: 'stretch' }),
]));

// ════════════════════════════════════════════════════════════════════════════
// 8) PROJECTOR — hours-saved estimator (rate:0 = lineare, currency:'' = nessun simbolo)
// Blueprint: .sy-est bg PANEL, border LINE, radius 18, padding 52
//            lbl color CYAN, out span color VIOLET, out small color DIM
// ════════════════════════════════════════════════════════════════════════════
home.push(sec(BG, 'large', [row([col('1-1', [tile('projector', {
  eyebrow: '// the math',
  heading: 'Give your team their <em>week</em> back',
  intro: `Teams on Synapse hand off roughly six hours of busywork per person each week to their agents. Drag to your headcount and see what that adds up to.`,
  min: '5', max: '200', step: '5', value: '40',
  rate: '0', years: '6', currency: '',
  input_label: 'Team size',
  out_caption: 'Hours saved every week',
  note: `≈ 6 hrs/person/week of triage, summarising and data-entry, automated. Based on aggregated customer usage.`,
  show_contrib: true,
  zone_accent: VIOLET,
  align: 'left',
  tile_padding: { top: 52, right: 52, bottom: 52, left: 52 },
  border_radius: '18', shadow: 'sm',
})])])]));

// ════════════════════════════════════════════════════════════════════════════
// 9) CTA — 2 bottoni: Try free (violet) + See use cases (ghost)
// Blueprint: .sy-cta__box — border LINE2, radius 24, padding 84
//            bg: linear-gradient(160deg, rgba(160,107,255,.18), rgba(255,122,209,.05))
// ════════════════════════════════════════════════════════════════════════════
home.push(sec(BG, 'large', [row([col('1-1', [tile('cta-banner', {
  headline: 'Stop searching.', headline_accent: 'Start asking.',
  headline_accent_italic: false,
  subtitle: `Bring your tools, ask your first question, and watch Synapse connect the dots. Free to try, no card.`,
  cta_text: 'Try free', cta_url: '#pricing',
  cta2_text: 'See use cases', cta2_url: '#', cta2_bg: 'rgba(255,255,255,.05)', cta2_color: WHITE, cta2_border: 'rgba(255,255,255,.16)',
  bg: { type: 'gradient', gradient_angle: 160, gradient_from: 'rgba(160,107,255,.18)', gradient_to: 'rgba(255,122,209,.05)' }, text_color: WHITE,
  accent_color: VIOLET, subtitle_color: TXT,
  cta_bg: VIOLET, cta_color: WHITE, cta_radius: R(9), cta_size: 15,
  headline_font_family: 'sans-serif', headline_size: 52, headline_weight: '700', subtitle_size: 17,
  layout: 'stack', vertical_align: 'center',
  banner_radius: R(24), banner_padding: 80,
})])])]));

K.emit({
  slug: 'synapse', name: 'Synapse',
  tags: ['software', 'tech', 'ai', 'saas', 'workspace'],
  description: 'Synapse — AI workspace per team. Deep ink + violet + pink, Instrument Sans (display+body) + Space Mono (labels). Projector lineare (team-size × 6h). Riproduzione fedele dell\'OLOtheme Synapse.',
  colors: {
    primary: VIOLET, primary_contrast: WHITE,
    secondary: CYAN, secondary_contrast: INK,
    muted: BG2, muted_contrast: TXT,
    text: TXT, text_muted: DIM,
    background: BG, border: LINE, link: VIOLET,
  },
  css_disp: `"Instrument Sans", -apple-system, sans-serif`,
  css_sans: `"Instrument Sans", -apple-system, sans-serif`,
  heading_weight: '700', heading_line_height: '1.05',
  google_fonts: ['Instrument Sans', 'Space Mono'],
  logo_variant: 'light',
  menu: [
    { title: 'Product',      url: '#features' },
    { title: 'Integrations', url: '#integrations' },
    { title: 'Use cases',    url: '#' },
    { title: 'Pricing',      url: '#pricing' },
  ],
  header: {
    bg: 'rgba(20,14,34,.84)', text_color: DIM,
    sticky_bg: 'rgba(20,14,34,.92)', logo_width: 138,
  },
  footer: {
    bg: BG2, headColor: WHITE,
    brand: { name: 'Synapse', tagline: 'The AI workspace that remembers. Chat, agents and your knowledge in one place.' },
    columns: [
      { title: 'Product',     links: ['Memory', 'Integrations', 'Use cases', 'Pricing'] },
      { title: 'Developers',  links: ['Docs', 'API', 'Security', 'Status'] },
      { title: 'Company',     links: ['About', 'Blog', 'Careers', 'Contact'] },
    ],
    bottom: { left: `© 2026 Synapse — an OLOtheme demo.`, right: 'Built with OLObuild' },
  },
  cursor: { blend_mode: 'exclusion', ring_color: '#ffffff', dot_color: '#ffffff' },
}, home);
