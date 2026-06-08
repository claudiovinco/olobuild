/* Terra — ricomposizione TILE-PURE pixel-perfect (image-free). Home & Living (plants/garden). Outfit + light off-white + leaf green. */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('te');

/* ─── Palette (dal :root terra.css) ─── */
const PAPER  = '#f3f5ee';
const PAPER2 = '#e8ede0';
const CARD   = '#fbfcf8';
const INK    = '#1d2a1c';
const INK2   = '#33432f';
const LEAF   = '#5e9442';
const LEAFD  = '#4f8137';
const CLAY   = '#c47b50';
const TXT    = '#566053';
const DIM    = '#8a957f';
const LINE   = '#dde3d4';
const LINE2  = '#c7d1ba';
const WHITE  = '#ffffff';
/* icon-bg: dalla .te-card__ic { background: rgba(127,174,90,.16) } nel CSS */
const LEAFA  = 'rgba(127,174,90,.16)';

const home = [];

/* ═══════════════════════════════════════════════════════
   1) HERO — hero-split image-free
   Blueprint: testo sinistra + arte destra (square media + tag badge).
   Badge unico: "30 days / happy guarantee". Nessuna griglia di stat.
   ═══════════════════════════════════════════════════════ */
home.push(sec(PAPER, 'large', [ row([ col('1-1', [ tile('hero-split', {
  eyebrow_text: 'Plants & garden',
  eyebrow_dot_color: LEAF,
  eyebrow_color: LEAF,
  headline_lines: [
    { text: 'Greener,', color: INK, italic: false },
    { text: 'the easy', color: LEAF, italic: true },
    { text: 'way.', color: INK, italic: false },
  ],
  headline_font_family: 'sans-serif',
  headline_font_size: 80,
  headline_line_height: 0.98,
  headline_font_weight: '700',
  headline_align: 'left',
  subhead: `Healthy houseplants matched to your light, delivered in a box built to protect them — with care cards even a beginner can follow.`,
  subhead_color: TXT,
  subhead_size: 18,
  subhead_italic: false,
  subhead_max_width: 400,
  cta1_text: 'Shop plants',
  cta1_url: '#shop',
  cta1_bg: LEAF,
  cta1_color: WHITE,
  cta1_size: 15,
  cta1_radius: R(999),
  cta1_radius_hover: R(999),
  cta2_text: 'Find my match',
  cta2_url: '#care',
  cta2_bg: 'transparent',
  cta2_color: INK,
  cta2_border: LINE2,
  cta2_size: 15,
  cta2_radius: R(999),
  cta2_radius_hover: R(999),
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: PAPER2 },
  showcase_padding: 0,
  showcase_radius: R(24),
  showcase_radius_hover: R(24),
  /* Floating badge in basso-sinistra: "30 days / happy guarantee" */
  showcase_badge_text: '30-DAY HAPPY GUARANTEE',
  showcase_badge_dot: LEAF,
  showcase_badge_bg: CARD,
  showcase_badge_color: INK,
  showcase_items: [],
  showcase_card_radius: R(12),
  showcase_card_radius_hover: R(12),
  showcase_card_shadow: 'none',
  showcase_caption_left: 'TERRA',
  showcase_caption_right: 'BOTANICALS',
  showcase_hover_effect: 'none',
  split_ratio: '1fr 1fr',
  gap: 48,
  min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
}) ]) ]) ]));

/* ═══════════════════════════════════════════════════════
   2) NEW ARRIVALS — section-header + product-cards 5 piante
   Blueprint: drag-scroll rail, .te-prod cards (border: 1px solid --line,
   radius: 16px, bg: --card), badge tag difficulty, prezzo, "Add" button.
   Tile product-cards (monogramma = iniziale, brand = genere Latin, badge = difficulty).
   ═══════════════════════════════════════════════════════ */
home.push(sec(PAPER, 'large', [
  row([ col('1-1', [ tile('section-header', {
    eyebrow_show: true,
    eyebrow_text: 'Just unboxed',
    eyebrow_color: LEAF,
    eyebrow_dot_color: LEAF,
    eyebrow_separator: '',
    headline_lines: [
      { text: 'New', color: INK, italic: false },
      { text: 'arrivals', color: LEAF, italic: true },
    ],
    headline_font_family: 'sans-serif',
    headline_font_size: 42,
    headline_font_weight: '700',
    headline_align: 'left',
    headline_inline: true,
    tagline_show: false,
    layout: 'stack',
    gap: 10,
  }) ]) ]),
  row([ col('1-1', [ tile('product-cards', {
    columns: 5,
    gap: 18,
    card_bg: { type: 'solid', color: CARD },
    card_color: TXT,
    card_radius: R(16),
    card_radius_hover: R(16),
    card_radius_hover_duration: 300,
    card_shadow: 'none',
    card_padding: 18,
    top_aspect_ratio: '4/5',
    top_padding: 0,
    letter_font_family: 'sans-serif',
    letter_size: 120,
    letter_italic: false,
    letter_align: 'center',
    show_screenshot_label: true,
    screenshot_label_color: 'rgba(29,42,28,.35)',
    brand_size: 13,
    brand_letter_spacing: 0.04,
    title_font_family: 'sans-serif',
    title_size: 20,
    title_weight: '700',
    description_size: 13,
    cta_size: 12,
    cta_arrow: true,
    card_hover_effect: 'lift',
    items: [
      {
        letter: 'M', letter_color: LEAF,
        top_bg: { type: 'solid', color: PAPER2 },
        screenshot_label: 'M. deliciosa',
        brand_label: 'EASY', brand_color: LEAF,
        show_badge: true, badge_text: 'Easy', badge_bg: LEAF, badge_color: WHITE,
        title: 'Monstera', title_accent: '', title_accent_italic: false,
        description: 'Iconic split leaves, bright indirect light.',
        cta_text: 'from €38', cta_url: '#shop',
      },
      {
        letter: 'S', letter_color: INK2,
        top_bg: { type: 'solid', color: PAPER2 },
        screenshot_label: 'Sansevieria',
        brand_label: 'HARDY', brand_color: INK2,
        show_badge: true, badge_text: 'Hardy', badge_bg: LEAF, badge_color: WHITE,
        title: 'Snake Plant', title_accent: '', title_accent_italic: false,
        description: 'Near-indestructible, low light, low water.',
        cta_text: 'from €28', cta_url: '#shop',
      },
      {
        letter: 'C', letter_color: LEAFD,
        top_bg: { type: 'solid', color: PAPER2 },
        screenshot_label: 'C. orbifolia',
        brand_label: 'MEDIUM', brand_color: LEAFD,
        show_badge: false, badge_text: '', badge_bg: LEAF, badge_color: WHITE,
        title: 'Calathea', title_accent: '', title_accent_italic: false,
        description: 'Striking patterns, loves humidity and shade.',
        cta_text: 'from €34', cta_url: '#shop',
      },
      {
        letter: 'F', letter_color: CLAY,
        top_bg: { type: 'solid', color: PAPER2 },
        screenshot_label: 'F. lyrata',
        brand_label: 'HIGH CARE', brand_color: CLAY,
        show_badge: false, badge_text: '', badge_bg: LEAF, badge_color: WHITE,
        title: 'Fiddle-Leaf Fig', title_accent: '', title_accent_italic: false,
        description: 'Statement tree for a bright sunny corner.',
        cta_text: 'from €56', cta_url: '#shop',
      },
      {
        letter: 'P', letter_color: LEAF,
        top_bg: { type: 'solid', color: PAPER2 },
        screenshot_label: 'E. aureum',
        brand_label: 'EASY', brand_color: LEAF,
        show_badge: true, badge_text: 'Easy', badge_bg: LEAF, badge_color: WHITE,
        title: 'Golden Pothos', title_accent: '', title_accent_italic: false,
        description: 'Trailing beauty, adapts to almost any light.',
        cta_text: 'from €22', cta_url: '#shop',
      },
    ],
  }) ]) ]),
]));

/* ═══════════════════════════════════════════════════════
   3) PROMISE — section-header centrato + info-cards 3 card
   Blueprint: .te-sec.card (bg: --card) + .te-card (bg: --card, border: 1px solid --line,
   radius: 14px, padding: 28px, text-align: center).
   Icon bg: rgba(127,174,90,.16) (dal CSS .te-card__ic).
   ═══════════════════════════════════════════════════════ */
home.push(sec(CARD, 'large', [
  row([ col('1-1', [ tile('section-header', {
    eyebrow_show: true,
    eyebrow_text: 'Our promise',
    eyebrow_color: LEAF,
    eyebrow_dot_color: LEAF,
    eyebrow_separator: '',
    headline_lines: [
      { text: 'Plants that', color: INK, italic: false },
      { text: 'arrive happy', color: LEAF, italic: true },
    ],
    headline_font_family: 'sans-serif',
    headline_font_size: 46,
    headline_font_weight: '700',
    headline_align: 'center',
    headline_inline: true,
    tagline_show: false,
    layout: 'center',
    gap: 12,
  }) ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0,
    container_gap: 16,
    columns: 3,
    items_gap: 16,
    /* card bg = --card (stessa sezione — definiti dal bordo) */
    card_bg: { type: 'solid', color: CARD },
    card_color: DIM,
    card_radius: R(14),
    card_padding: 28,
    show_icon: true,
    show_counter: false,
    show_arrow: false,
    show_footer: false,
    show_media: false,
    icon_color: LEAF,
    /* esatto dal CSS: rgba(127,174,90,.16) */
    icon_bg_color: LEAFA,
    title_color: INK,
    title_font_family: 'sans-serif',
    title_size: 20,
    title_weight: '700',
    title_italic: false,
    description_size: 14,
    card_hover_effect: 'lift',
    items: [
      { icon: 'shield-check', title: '30-day guarantee', description: `If it's not thriving in a month, we replace it free. No questions.` },
      { icon: 'gift', title: 'Boxed with care', description: `Custom packaging cradles roots and leaves — they arrive as they left.` },
      { icon: 'book-open', title: `Care that's clear`, description: `Every plant ships with a plain-language care card and an app reminder.` },
    ],
  }) ]) ]),
]));

/* ═══════════════════════════════════════════════════════
   4) REPOTTING — hero-split image-free (FeatureSplit)
   Blueprint: .te-split (grid 1fr 1fr, gap 56px) — media sinistra, testo destra.
   CTA = link-u (solo testo underline, non button pill).
   Nessun showcase items: showcase_items vuoto, solo sfondo pannello.
   ═══════════════════════════════════════════════════════ */
home.push(sec(PAPER, 'large', [ row([ col('1-1', [ tile('hero-split', {
  eyebrow_text: 'Repotting & soil',
  eyebrow_dot_color: LEAF,
  eyebrow_color: LEAF,
  headline_lines: [
    { text: `We'll pot it`, color: INK, italic: false },
    { text: 'for you', color: LEAF, italic: true },
  ],
  headline_font_family: 'sans-serif',
  headline_font_size: 52,
  headline_line_height: 1.04,
  headline_font_weight: '700',
  headline_align: 'left',
  subhead: `Add a pot at checkout and we'll repot before it ships — drainage, the right mix, and a saucer, all done. Or grab a soil kit and do it yourself.`,
  subhead_color: TXT,
  subhead_size: 16,
  subhead_italic: false,
  subhead_max_width: 460,
  /* CTA outline (link-u nel blueprint = stile testuale; usiamo cta1 outline) */
  cta1_text: 'Shop pots & soil',
  cta1_url: '#shop',
  cta1_bg: 'transparent',
  cta1_color: INK,
  cta1_border: LINE2,
  cta1_size: 14,
  cta1_radius: R(999),
  cta1_radius_hover: R(999),
  cta2_text: '',
  cta2_url: '',
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: PAPER2 },
  showcase_padding: 0,
  showcase_radius: R(18),
  showcase_radius_hover: R(18),
  showcase_badge_text: 'REPOTTING SERVICE',
  showcase_badge_dot: CLAY,
  showcase_badge_bg: CARD,
  showcase_badge_color: INK,
  showcase_items: [],
  showcase_card_radius: R(11),
  showcase_card_radius_hover: R(11),
  showcase_card_shadow: 'none',
  showcase_caption_left: 'ADD AT CHECKOUT',
  showcase_caption_right: '',
  showcase_hover_effect: 'none',
  /* media a sinistra (split ratio invertito rispetto a hero) */
  split_ratio: '.9fr 1.1fr',
  gap: 56,
  min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
}) ]) ]) ]));

/* ═══════════════════════════════════════════════════════
   5) HARD TO KILL — section-header centrato + product-cards 4 piante
   Blueprint: .te-sec.card (bg: --card) + .te-grid (4 colonne, gap 18px).
   Stessa struttura .te-prod: border 1px --line, radius 16px, bg --card.
   ═══════════════════════════════════════════════════════ */
home.push(sec(CARD, 'large', [
  row([ col('1-1', [ tile('section-header', {
    eyebrow_show: true,
    eyebrow_text: 'Beginner-proof',
    eyebrow_color: LEAF,
    eyebrow_dot_color: LEAF,
    eyebrow_separator: '',
    headline_lines: [
      { text: 'Hard to', color: INK, italic: false },
      { text: 'kill', color: LEAF, italic: true },
    ],
    headline_font_family: 'sans-serif',
    headline_font_size: 46,
    headline_font_weight: '700',
    headline_align: 'center',
    headline_inline: true,
    tagline_show: false,
    layout: 'center',
    gap: 12,
  }) ]) ]),
  row([ col('1-1', [ tile('product-cards', {
    columns: 4,
    gap: 18,
    card_bg: { type: 'solid', color: CARD },
    card_color: TXT,
    card_radius: R(16),
    card_radius_hover: R(16),
    card_radius_hover_duration: 300,
    card_shadow: 'none',
    card_padding: 18,
    top_aspect_ratio: '4/5',
    top_padding: 0,
    letter_font_family: 'sans-serif',
    letter_size: 120,
    letter_italic: false,
    letter_align: 'center',
    show_screenshot_label: true,
    screenshot_label_color: 'rgba(29,42,28,.35)',
    brand_size: 13,
    brand_letter_spacing: 0.04,
    title_font_family: 'sans-serif',
    title_size: 20,
    title_weight: '700',
    description_size: 13,
    cta_size: 12,
    cta_arrow: true,
    card_hover_effect: 'lift',
    items: [
      {
        letter: 'Z', letter_color: LEAF,
        top_bg: { type: 'solid', color: PAPER2 },
        screenshot_label: 'Zamioculcas',
        brand_label: 'EASY', brand_color: LEAF,
        show_badge: true, badge_text: 'Easy', badge_bg: LEAF, badge_color: WHITE,
        title: 'ZZ Plant', title_accent: '', title_accent_italic: false,
        description: 'Glossy, near-indestructible, thrives in dim corners.',
        cta_text: '€30', cta_url: '#shop',
      },
      {
        letter: 'G', letter_color: CLAY,
        top_bg: { type: 'solid', color: PAPER2 },
        screenshot_label: 'Echinopsis',
        brand_label: 'HARDY', brand_color: CLAY,
        show_badge: true, badge_text: 'Hardy', badge_bg: LEAF, badge_color: WHITE,
        title: 'Golden Cactus', title_accent: '', title_accent_italic: false,
        description: 'Loves full sun, needs water roughly once a month.',
        cta_text: '€18', cta_url: '#shop',
      },
      {
        letter: 'P', letter_color: LEAF,
        top_bg: { type: 'solid', color: PAPER2 },
        screenshot_label: 'Spathiphyllum',
        brand_label: 'EASY', brand_color: LEAF,
        show_badge: false, badge_text: '', badge_bg: LEAF, badge_color: WHITE,
        title: 'Peace Lily', title_accent: '', title_accent_italic: false,
        description: 'Elegant blooms, tolerates low light beautifully.',
        cta_text: '€26', cta_url: '#shop',
      },
      {
        letter: 'R', letter_color: INK2,
        top_bg: { type: 'solid', color: PAPER2 },
        screenshot_label: 'Ficus elastica',
        brand_label: 'EASY', brand_color: INK2,
        show_badge: false, badge_text: '', badge_bg: LEAF, badge_color: WHITE,
        title: 'Rubber Plant', title_accent: '', title_accent_italic: false,
        description: 'Bold tropical leaves, very forgiving of neglect.',
        cta_text: '€32', cta_url: '#shop',
      },
    ],
  }) ]) ]),
]));

/* ═══════════════════════════════════════════════════════
   6) PLANT FINDER — tile finder (chip → result card)
   Blueprint: .te-finder (bg: --paper-2, data-finder).
   zone_accent = LEAF (#5e9442), zone_on = #fff (da --fx-zone-accent/--fx-zone-on).
   card_bg = CARD, card_border = LINE2.
   4 opzioni: Low/shady, Bright indirect, Full sun, Forgetful waterer.
   ═══════════════════════════════════════════════════════ */
home.push(sec(PAPER2, 'large', [
  row([ col('1-1', [ tile('finder', {
    eyebrow: 'Plant doctor',
    heading: `Match a plant to your light`,
    intro: `Tell us about the spot and we'll point you to something that will actually thrive there — not just survive a fortnight.`,
    zone_accent: LEAF,
    zone_on: WHITE,
    card_bg: CARD,
    card_border: LINE2,
    align: 'center',
    items: [
      {
        option: 'Low / shady',
        icon: 'moon',
        title: 'ZZ Plant',
        text: `Glossy and near-indestructible — perfectly content in a dim corner. Water only when the soil's bone dry.`,
        meta: 'from €24',
        cta_text: 'Shop ZZ Plant',
        cta_url: '#shop',
      },
      {
        option: 'Bright, indirect',
        icon: 'sun',
        title: 'Monstera Deliciosa',
        text: `The crowd favourite. Loves bright, indirect light and rewards you with those iconic split leaves.`,
        meta: 'from €38',
        cta_text: 'Shop Monstera',
        cta_url: '#shop',
      },
      {
        option: 'Full sun',
        icon: 'zap',
        title: 'Bird of Paradise',
        text: `A sun-worshipper that turns a bright room tropical. Give it the sunniest window you've got.`,
        meta: 'from €52',
        cta_text: 'Shop Bird of Paradise',
        cta_url: '#shop',
      },
      {
        option: 'I forget to water',
        icon: 'leaf',
        title: 'Snake Plant',
        text: `The one for the forgetful. Thrives on neglect, cleans your air, and asks for water roughly monthly.`,
        meta: 'from €28',
        cta_text: 'Shop Snake Plant',
        cta_url: '#shop',
      },
    ],
  }) ]) ]),
]));

/* ═══════════════════════════════════════════════════════
   7) CTA — cta-banner (bg: --ink, 1 solo bottone)
   Blueprint: .te-cta { background: --ink } + h2 + p + single btn.
   Nessun secondo bottone nel blueprint.
   ═══════════════════════════════════════════════════════ */
home.push(sec(INK, 'large', [ row([ col('1-1', [ tile('cta-banner', {
  headline: `Let's get`,
  headline_accent: 'growing.',
  headline_accent_italic: true,
  subtitle: `Take the two-minute light quiz and we'll match you with plants that'll actually thrive.`,
  cta_text: 'Find my plant match',
  cta_url: '#care',
  bg: { type: 'solid', color: INK },
  text_color: PAPER,
  accent_color: LEAF,
  subtitle_color: '#bcc7b3',
  cta_bg: LEAF,
  cta_color: WHITE,
  cta_radius: R(999),
  cta_size: 15,
  headline_font_family: 'sans-serif',
  headline_size: 64,
  headline_weight: '700',
  subtitle_size: 17,
  layout: 'stack',
  vertical_align: 'center',
  banner_radius: R(0),
  banner_padding: 80,
}) ]) ]) ]));

/* ─── Emit ─── */
K.emit({
  slug: 'terra',
  name: 'Terra',
  tags: ['plants', 'garden', 'home-living', 'ecommerce', 'botanical'],
  description: `Terra — Plants & garden, delivered happy. Light off-white + leaf green, Outfit. Sezioni: hero, new arrivals (product-cards), promise (info-cards), repotting, hard-to-kill (product-cards), plant finder (approx. info-cards), CTA. Riproduzione fedele dell'OLOtheme Terra.`,
  colors: {
    primary: LEAF,
    primary_contrast: WHITE,
    secondary: CLAY,
    secondary_contrast: WHITE,
    muted: PAPER2,
    muted_contrast: TXT,
    text: TXT,
    text_muted: DIM,
    background: PAPER,
    border: LINE,
    link: LEAF,
  },
  css_disp: `"Outfit", -apple-system, sans-serif`,
  css_sans: `"Outfit", -apple-system, sans-serif`,
  heading_weight: '700',
  heading_line_height: '1.04',
  google_fonts: ['Outfit'],
  logo_variant: 'dark',
  menu: [
    { title: 'Shop plants', url: '#shop' },
    { title: 'Our promise', url: '#promise' },
    { title: 'Repotting', url: '#repot' },
    { title: 'Care', url: '#care' },
  ],
  header: {
    bg: 'rgba(243,245,238,.9)',
    text_color: TXT,
    sticky_bg: 'rgba(243,245,238,.96)',
    logo_width: 130,
  },
  footer: {
    bg: PAPER,
    headColor: INK,
    brand: {
      name: 'Terra',
      tagline: 'Plants & garden, delivered happy. Matched to your light, guaranteed for 30 days.',
    },
    columns: [
      { title: 'Shop', links: ['All plants', 'Easy care', 'Pots', 'Soil & tools'] },
      { title: 'Learn', links: ['Care guides', 'Repotting', 'Plant quiz'] },
      { title: 'Help', links: ['Our guarantee', 'Delivery', 'Contact'] },
    ],
    bottom: {
      left: '© 2026 Terra — an OLOtheme demo.',
      right: 'Built with OLObuild',
    },
  },
  cursor: false,
}, home);
