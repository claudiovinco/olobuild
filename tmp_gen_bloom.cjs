/* Bloom — ricomposizione TILE-PURE (image-free). Beauty & Fashion. Plum + pink. */
/* Rozha One (display) + Plus Jakarta Sans (body). */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('bl');

// ── PALETTE (dal :root di bloom.css) ──────────────────────────────────────
const BG    = '#3a2230';
const BG2   = '#432838';
const PANEL = '#4d2f40';
const PANEL2= '#5a384c';
const INK   = '#23131d';
const PINK  = '#e7a0b4';
const PINK_D= '#d98aa1';
const ROSE  = '#f4c9d4';
const CREAM = '#f6e9ec';
const GOLD  = '#e3b778';
const TXT   = '#cdb1bd';
const DIM   = '#9c7e8c';
const LINE  = 'rgba(246,233,236,.13)';
const LINE2 = 'rgba(231,160,180,.42)';

const home = [];

// ── 1) HERO — hero-split ──────────────────────────────────────────────────
// .bl-hero__art .media: aspect-ratio 4/5, border-radius 200px 200px 24px 24px
// .bl-hero__in: grid 1.05fr .95fr, gap 48px
// CTA: btn--pink (pill 999px) + btn--out (border LINE2, pill)
// Titolo: clamp(46px,7.4vw,96px), font-weight:400 (Rozha One)
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('hero-split', {
  eyebrow_text: 'Clean colour cosmetics',
  eyebrow_dot_color: PINK,
  eyebrow_color: PINK,
  headline_lines: [
    { text: 'Your colour,', color: CREAM, italic: false },
    { text: 'your', color: CREAM, italic: false },
    { text: 'rules.', color: PINK, italic: true },
  ],
  headline_font_family: 'serif',
  headline_font_size: 80,
  headline_line_height: 1.0,
  headline_font_weight: '400',
  headline_align: 'left',
  subhead: 'High-pigment, skin-kind makeup in shades for every undertone — built to be mixed, layered and absolutely yours.',
  subhead_color: TXT,
  subhead_size: 18,
  subhead_italic: false,
  subhead_max_width: 420,
  cta1_text: 'Shop the range',
  cta1_url: '#new',
  cta1_bg: PINK,
  cta1_color: INK,
  cta1_size: 15,
  cta1_radius: R(999),
  cta1_radius_hover: R(999),
  cta2_text: 'Find your shade',
  cta2_url: '#finder',
  cta2_bg: 'transparent',
  cta2_color: CREAM,
  cta2_border: LINE2,
  cta2_size: 15,
  cta2_radius: R(999),
  cta2_radius_hover: R(999),
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: PANEL },
  showcase_padding: 28,
  showcase_radius: R(200),
  showcase_radius_hover: R(200),
  showcase_badge_text: 'BLOOM · BESTSELLERS',
  showcase_badge_dot: PINK,
  showcase_badge_bg: INK,
  showcase_badge_color: CREAM,
  showcase_items: [
    { number: 'Velvet Matte Lip',  text: '€22', italic: false, text_color: PINK,  bg: { type: 'solid', color: BG2 } },
    { number: 'Dewdrop Blush',     text: '€19', italic: false, text_color: CREAM, bg: { type: 'solid', color: BG2 } },
    { number: 'Second-Skin Tint',  text: '€28', italic: false, text_color: CREAM, bg: { type: 'solid', color: BG2 } },
    { number: 'Everyday Palette',  text: '€34', italic: false, text_color: CREAM, bg: { type: 'solid', color: BG2 } },
  ],
  showcase_card_radius: R(14),
  showcase_card_radius_hover: R(14),
  showcase_card_shadow: 'none',
  showcase_caption_left: 'COLOUR FOR EVERYONE',
  showcase_caption_right: 'CRUELTY-FREE',
  showcase_hover_effect: 'none',
  split_ratio: '1.05fr .95fr',
  gap: 48,
  min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
}) ]) ]) ]));

// ── helpers ────────────────────────────────────────────────────────────────
const shead = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true,
  eyebrow_text: eyebrow,
  eyebrow_color: PINK,
  eyebrow_dot_color: PINK,
  eyebrow_separator: '',
  headline_lines: [
    { text: l1, color: CREAM, italic: false },
    { text: accent, color: PINK, italic: true },
  ],
  headline_font_family: 'serif',
  headline_font_size: 48,
  headline_font_weight: '400',
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

const sheadLeft = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true,
  eyebrow_text: eyebrow,
  eyebrow_color: PINK,
  eyebrow_dot_color: PINK,
  eyebrow_separator: '',
  headline_lines: [
    { text: l1, color: CREAM, italic: false },
    { text: accent, color: PINK, italic: true },
  ],
  headline_font_family: 'serif',
  headline_font_size: 44,
  headline_font_weight: '400',
  headline_align: 'left',
  headline_inline: true,
  tagline_show: !!intro,
  tagline_text: intro || '',
  tagline_text_italic: false,
  tagline_text_color: DIM,
  tagline_text_size: 16.5,
  layout: 'stack',
  gap: 12,
});

// ── 2) PRODUCT GRID — info-cards (IMAGE-FREE)
// .bl-prod: background var(--panel) + border 1px solid var(--line) + border-radius 18px
// 4 prodotti con categoria (Lips/Cheeks/Base/Eyes), nome, prezzo (disp serif 21px), tag pill
// NOTA: ProductGrid non esiste come tile; product-cards ha architettura lettera-monogramma
// non compatibile col layout beauty-commerce. info-cards è la migliore approssimazione.
home.push(sec(BG, 'large', [
  row([ col('1-1', [ shead('Best sellers', 'Loved in', 'every shade', null) ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0,
    container_gap: 20,
    columns: 4,
    items_gap: 20,
    card_bg: { type: 'solid', color: PANEL },
    card_color: DIM,
    card_radius: R(18),
    card_padding: 24,
    card_border: LINE,
    show_icon: false,
    show_counter: true,
    show_counter_label: true,
    show_arrow: false,
    show_footer: true,
    show_media: false,
    counter_shape: 'square',
    counter_color: INK,
    counter_bg: PINK,
    counter_size: 13,
    title_color: CREAM,
    title_font_family: 'serif',
    title_size: 22,
    title_weight: '400',
    title_italic: false,
    description_size: 13.5,
    footer_size: 21,
    items: [
      { counter: 'Lips',   counter_label: 'New',      title: 'Velvet Matte Lip',   description: 'Rich, long-wear matte formula. Four classic shades from cool berry to warm terracotta.',     footer_text: '€22', footer_dot_color: PINK },
      { counter: 'Cheeks', counter_label: '',          title: 'Dewdrop Blush',       description: 'Buildable powder blush in 12 natural shades. Melts into skin for a fresh-from-the-garden glow.', footer_text: '€19', footer_dot_color: PINK },
      { counter: 'Base',   counter_label: '40 shades', title: 'Second-Skin Tint',   description: 'Featherlight skin tint with SPF 20. Forty shades matched to every undertone.',           footer_text: '€28', footer_dot_color: GOLD },
      { counter: 'Eyes',   counter_label: '',          title: 'Everyday Palette',    description: 'Eight versatile shades — matte to shimmer — for a one-and-done eye look any day.',            footer_text: '€34', footer_dot_color: PINK },
    ],
    card_hover_effect: 'lift',
  }) ]) ]),
], { gap: 24 }));

// ── 3) SHADE FINDER — tile finder (chip opzione shade → result card con tono)
home.push(sec(BG2, 'large', [
  row([ col('1-1', [ tile('finder', {
    eyebrow: 'Shade finder',
    heading: 'Find your perfect shade',
    intro: "Pick your undertone and finish — we'll show you the match.",
    zone_accent: PINK,
    zone_on: INK,
    card_bg: PANEL,
    card_border: LINE,
    align: 'center',
    items: [
      {
        option: 'Rosewood',
        icon: 'circle',
        title: 'Velvet Matte — Rosewood',
        text: 'A deep cool-berry matte with serious staying power. Goes from boardroom to bar without a touch-up.',
        meta: 'Cool · matte · €22',
        cta_text: 'Add to bag',
        cta_url: '#',
      },
      {
        option: 'Terracotta',
        icon: 'circle',
        title: 'Velvet Matte — Terracotta',
        text: 'Warm brick-rose that flatters golden and olive undertones. A sell-out every launch.',
        meta: 'Warm · matte · €22',
        cta_text: 'Add to bag',
        cta_url: '#',
      },
      {
        option: 'Peony',
        icon: 'circle',
        title: 'Dewdrop Blush — Peony',
        text: 'Soft, buildable cool-pink blush. Melts into the skin for a fresh-from-the-garden flush.',
        meta: 'Cool · blush · €19',
        cta_text: 'Add to bag',
        cta_url: '#',
      },
      {
        option: 'Apricot',
        icon: 'circle',
        title: 'Dewdrop Blush — Apricot',
        text: 'Sun-kissed warm blush that gives the illusion of a tan with zero commitment.',
        meta: 'Warm · blush · €19',
        cta_text: 'Add to bag',
        cta_url: '#',
      },
      {
        option: 'Merlot',
        icon: 'circle',
        title: 'Velvet Matte — Merlot',
        text: 'Rich, deep wine matte for the moments that deserve more. Long-wear formula, no feathering.',
        meta: 'Deep · matte · €22',
        cta_text: 'Add to bag',
        cta_url: '#',
      },
    ],
  }) ]) ]),
]));

// ── 4) ROUTINE BUILDER (LookbookMixer) — process-steps a 4 step con card
// .bl-lb: grid 1.12fr .88fr, background var(--panel), border 1px LINE, border-radius 24px
// .bl-lb__slot: background var(--bg-2), border 1px LINE, border-radius 14px
// LookbookMixer non esiste → process-steps con card (bg+border) + sezione sommario counter
// SEGNALATA: LookbookMixer con ciclo prodotti e prezzo live non riproducibile staticamente
home.push(sec(BG, 'large', [
  row([ col('1-1', [ shead('Build your ritual', 'Compose your ', 'routine',
    'Four steps, your call. Tap through each to build a regimen — the total updates as you go.') ]) ]),
  row([ col('1-1', [ tile('process-steps', {
    columns: 4,
    gap: 16,
    align: 'left',
    auto_number: false,
    item_gap: 10,
    number_style: 'circle',
    number_color: INK,
    number_size: 32,
    number_font: 'sans-serif',
    number_weight: '700',
    number_bg: PINK,
    title_color: CREAM,
    title_size: 21,
    title_font: 'serif',
    title_weight: '400',
    desc_color: DIM,
    desc_size: 13.5,
    card_bg: BG2,
    card_border: LINE,
    card_radius: R(14),
    card_padding: 24,
    items: [
      { number: '01', title: 'Cleanse',  description: 'Start with Rosewater Gel, Clay Melt Balm or the gentle Milk Cleanser. From €22.' },
      { number: '02', title: 'Treat',    description: 'Layer on Vitamin C Drops, Niacinamide 10% or the Retinal Night Oil. From €32.' },
      { number: '03', title: 'Hydrate',  description: 'Seal with Ceramide Cream for a full barrier, or the light Gel-Water Lotion. From €28.' },
      { number: '04', title: 'Protect',  description: 'Finish every morning with Sheer SPF 50 or the skin-perfecting Tinted SPF 30. From €30.' },
    ],
  }) ]) ]),
]));

// ── 5) VALUES / CATEGORY TILES — info-cards 3 colonne con icona
// .bl-cat: border 1px solid var(--line) + border-radius 16px + min-height 300px
// Le card hanno overlay gradient scuro + testo (h3 26px CREAM + p 13px ROSE) in basso
// IMAGE-FREE: usiamo info-cards con icona grande (niente foto)
// Copy dal blueprint: titoli brevi, descrizioni brevi (non la versione estesa del vecchio gen)
home.push(sec(BG2, 'large', [
  row([ col('1-1', [ shead('What we stand for', 'Pretty, ', 'and principled', null) ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0,
    container_gap: 16,
    columns: 3,
    items_gap: 16,
    card_bg: { type: 'solid', color: PANEL },
    card_color: DIM,
    card_radius: R(16),
    card_padding: 36,
    card_border: LINE,
    show_icon: true,
    show_counter: false,
    show_arrow: false,
    show_footer: false,
    show_media: false,
    icon_color: PINK,
    icon_bg_color: 'rgba(231,160,180,.14)',
    title_color: CREAM,
    title_font_family: 'serif',
    title_size: 26,
    title_weight: '400',
    title_italic: false,
    description_size: 13,
    items: [
      { icon: 'leaf',        title: 'Clean formulas',   description: 'Skin-kind, dermatologist-tested ingredients. No parabens, no sulphates, no compromise.' },
      { icon: 'heart',       title: 'Never on animals', description: 'Certified cruelty-free and vegan — every formula, every shade, every time.' },
      { icon: 'rotate-ccw',  title: 'Refillable',       description: 'Bring the bullet back, save 20%. Packaging designed to last, refills from €9.' },
    ],
    card_hover_effect: 'lift',
  }) ]) ]),
], { gap: 24 }));

// ── 6) TESTIMONIAL ────────────────────────────────────────────────────────
// .bl-testi: panel (bg-2), text-align center, max-width 840px
// .bl-testi .stars: color var(--pink), font-size 18px, margin-bottom 18px
// q: Rozha One, clamp(24px,3.4vw,40px), CREAM; em italic PINK
// .bl-testi__by: sans 700, 12px, uppercase, PINK — "Aisha K. · verified buyer" unificato
home.push(sec(BG2, 'large', [ row([ col('1-1', [ tile('testimonial', {
  quote: `“Finally a brand that had my exact undertone — and the formula lasts all day without going cakey. I've repurchased three times.”`,
  author_name: 'Aisha K.',
  author_role: 'verified buyer',
  rating: '5',
  layout: 'single',
  show_line: false,
  bg_color: 'transparent',
  text_color: CREAM,
  text_size: 36,
  author_color: PINK,
  border_radius: '0',
  avatar: '',
}) ]) ]) ]));

// ── 7) NEWSLETTER / CTA ───────────────────────────────────────────────────
// .bl-news__box: border 1px solid LINE2 + border-radius 24px
//   background: linear-gradient(150deg, rgba(231,160,180,.16), rgba(231,160,180,.04))
// h2: Rozha One, clamp(32px,5vw,58px); em italic PINK
// form: input pill (999px) bg rgba(246,233,236,.08) border LINE2 + btn--pink pill
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('newsletter', {
  title: 'Get 10% off your first order',
  subtitle: 'Join the list for early shade drops, refill reminders and the occasional tutorial.',
  layout: 'minimal',
  email_placeholder: 'you@example.com',
  button_text: 'Sign up',
  button_icon: false,
  max_width: '520',
  alignment: 'center',
  bg_color: PANEL,
  box_border: LINE2,
  border_radius: 24,
  tile_padding: { top: 68, right: 68, bottom: 68, left: 68 },
  title_size: '46',
  title_weight: '400',
  title_color: CREAM,
  subtitle_size: '17',
  subtitle_color: TXT,
  input_bg: 'rgba(246,233,236,.08)',
  input_color: CREAM,
  input_border: LINE2,
  input_focus_border: PINK,
  input_radius: 999,
  input_height: '48',
  btn_bg: PINK,
  btn_color: INK,
  btn_hover_bg: PINK_D,
  btn_radius: 999,
  btn_font_size: '15',
  btn_font_weight: '700',
  shadow: 'none',
}) ]) ]) ]));

// ── EMIT ──────────────────────────────────────────────────────────────────
K.emit({
  slug: 'bloom',
  name: 'Bloom',
  tags: ['beauty', 'fashion', 'cosmetics', 'ecommerce'],
  description: `Bloom — clean colour cosmetics. Plum + pink, Rozha One (display) + Plus Jakarta Sans. Hero vitale, lookbook products, shade list, routine builder (process-steps), values, testimonial, newsletter. Riproduzione fedele dell'OLOtheme Bloom (Beauty & Fashion).`,
  colors: {
    primary: PINK,
    primary_contrast: INK,
    secondary: GOLD,
    secondary_contrast: INK,
    muted: BG2,
    muted_contrast: TXT,
    text: TXT,
    text_muted: DIM,
    background: BG,
    border: LINE,
    link: PINK,
  },
  css_disp: `"Rozha One", Georgia, serif`,
  css_sans: `"Plus Jakarta Sans", -apple-system, sans-serif`,
  heading_weight: '400',
  heading_line_height: '1.08',
  google_fonts: ['Rozha One', 'Plus Jakarta Sans'],
  logo_variant: 'light',
  menu: [
    { title: 'Shop',          url: '#new'    },
    { title: 'Shade finder',  url: '#finder' },
    { title: 'Our values',    url: '#values' },
    { title: 'Journal',       url: '#'       },
  ],
  header: {
    bg: 'rgba(58,34,48,.86)',
    text_color: TXT,
    sticky_bg: 'rgba(58,34,48,.92)',
    logo_width: 130,
  },
  footer: {
    bg: BG2,
    headColor: CREAM,
    brand: {
      name: 'Bloom',
      tagline: 'Clean colour cosmetics in shades for everyone. Cruelty-free, refillable, made in the EU.',
    },
    columns: [
      { title: 'Shop',  links: ['All products', 'Lips', 'Cheeks', 'Base']                    },
      { title: 'Help',  links: ['Shade finder', 'Shipping', 'Returns', 'Refills']             },
      { title: 'Bloom', links: ['Our values', 'Ingredients', 'Journal', 'Contact']            },
    ],
    bottom: {
      left:  '© 2026 Bloom — an OLOtheme demo.',
      right: 'Built with OLObuild',
    },
  },
  cursor: {
    blend_mode: 'exclusion',
    ring_color:  PINK,
    dot_color:   PINK,
  },
}, home);
