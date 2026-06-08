/* Honeycomb — ricomposizione TILE-PURE (image-free). Food & Drink (artisan bakery).
   Warm wheat + honey palette. Gloock (display) + Be Vietnam Pro (body).
   LIGHT theme — cursor:false. */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('hc');

// ── Palette dal :root CSS ──
const PAPER   = '#f6ecd6';   // sfondo pagina
const PAPER2  = '#efe1c4';   // bg-2 / surface alt
const CARD    = '#fbf5e7';   // card bg
const INK     = '#3a2a12';   // heading / CTA bg
const INK2    = '#574020';   // hover ink
const HONEY   = '#e09a18';   // accento
const HONEYD  = '#c8870f';   // accento scuro (hover)
const RYE     = '#9a6a2f';   // terziario
const TXT     = '#6b552f';   // testo corpo
const TXDIM   = '#9c8253';   // testo dimmed
const LINE    = '#e0cfa6';   // bordo leggero
const LINE2   = '#cdb47f';   // bordo medio
const WHITE   = '#ffffff';

const home = [];

// ─── helper: section-header centrato ───────────────────────────────────────
const shead = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: HONEYD, eyebrow_dot_color: HONEYD, eyebrow_separator: '',
  headline_lines: [
    { text: l1, color: INK, italic: false },
    { text: accent, color: HONEYD, italic: true },
  ],
  headline_font_family: 'serif', headline_font_size: 52, headline_font_weight: '400',
  headline_align: 'center', headline_inline: true,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false,
  tagline_text_color: TXDIM, tagline_text_size: 16,
  layout: 'center', gap: 14,
});

// helper: section-header sinistra
const sheadLeft = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: HONEYD, eyebrow_dot_color: HONEYD, eyebrow_separator: '',
  headline_lines: [
    { text: l1, color: INK, italic: false },
    { text: accent, color: HONEYD, italic: true },
  ],
  headline_font_family: 'serif', headline_font_size: 46, headline_font_weight: '400',
  headline_align: 'left', headline_inline: true,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false,
  tagline_text_color: TXDIM, tagline_text_size: 15,
  layout: 'stack', gap: 10,
});

// ─── 1) HERO SPLIT ──────────────────────────────────────────────────────────
home.push(sec(PAPER, 'large', [row([col('1-1', [tile('hero-split', {
  eyebrow_text: `Neighbourhood bakery · since 2016`,
  eyebrow_dot_color: HONEYD, eyebrow_color: HONEYD,
  headline_lines: [
    { text: 'Real bread,', color: INK, italic: false },
    { text: 'real', color: HONEYD, italic: true },
    { text: `early.`, color: INK, italic: false },
  ],
  headline_font_family: 'serif', headline_font_size: 76, headline_line_height: 1.0,
  headline_font_weight: '400', headline_align: 'left',
  subhead: `Slow-proved sourdough, buttery viennoiserie and proper cakes — made by hand before the sun's up, every single day.`,
  subhead_color: TXT, subhead_size: 18, subhead_italic: false, subhead_max_width: 430,
  cta1_text: `See today's bakes`, cta1_url: '#bakes',
  cta1_bg: HONEY, cta1_color: WHITE, cta1_size: 15, cta1_radius: R(999), cta1_radius_hover: R(999),
  cta2_text: 'Our story', cta2_url: '#story',
  cta2_bg: 'transparent', cta2_color: INK,
  cta2_border: LINE2, cta2_size: 15, cta2_radius: R(999), cta2_radius_hover: R(999),
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: PAPER2 },
  showcase_padding: 32, showcase_radius: R(24), showcase_radius_hover: R(24),
  showcase_badge_text: `OVENS ON AT 5AM`, showcase_badge_dot: HONEY,
  showcase_badge_bg: INK, showcase_badge_color: WHITE,
  showcase_items: [
    { number: 'Sourdough out at', text: '8:00', italic: false, text_color: HONEYD, bg: { type: 'solid', color: CARD } },
    { number: 'Croissants ready at', text: '7:30', italic: false, text_color: HONEYD, bg: { type: 'solid', color: CARD } },
    { number: 'Bake days', text: `Tue – Sun`, italic: false, text_color: INK, bg: { type: 'solid', color: CARD } },
    { number: 'Everything gone by', text: 'noon', italic: false, text_color: INK, bg: { type: 'solid', color: CARD } },
  ],
  showcase_card_radius: R(12), showcase_card_radius_hover: R(12), showcase_card_shadow: 'none',
  showcase_caption_left: 'MILL STREET', showcase_caption_right: 'OPEN 2026',
  showcase_hover_effect: 'none',
  split_ratio: '1.05fr .95fr', gap: 48, min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
})])])]));

// ─── 2) PRODUCT GRID — "Today's bakes" (product-cards 4 col, image-free) ────
// CSS: .hc-bake → card con media 1:1 + tag + h3 + p + price "Add". product-cards
// con monogramma (prima lettera), brand_label = tag categoria, cta_text = prezzo.
home.push(sec(CARD, 'large', [
  row([col('1-1', [shead(`Out of the oven`, `Today's `, `bakes`, null)])]),
  row([col('1-1', [tile('product-cards', {
    columns: 4,
    gap: 20,
    items: [
      {
        letter: 'S', letter_color: HONEYD,
        top_bg: { type: 'gradient', gradient_from: PAPER2, gradient_to: LINE, gradient_angle: 135 },
        screenshot_label: 'SOURDOUGH LOAVES · FLOUR DUST',
        brand_label: 'BREAD', brand_color: RYE,
        show_badge: false, badge_text: '', badge_bg: INK, badge_color: WHITE,
        title: 'Country Sourdough', title_accent: '', title_accent_italic: false,
        description: '48-hour ferment, blistered crust.',
        cta_text: `€6 — Add`, cta_url: '#',
      },
      {
        letter: 'C', letter_color: HONEYD,
        top_bg: { type: 'gradient', gradient_from: PAPER2, gradient_to: LINE, gradient_angle: 135 },
        screenshot_label: 'CROISSANT · LAMINATION',
        brand_label: 'VIENNOISERIE', brand_color: RYE,
        show_badge: false, badge_text: '', badge_bg: INK, badge_color: WHITE,
        title: 'Butter Croissant', title_accent: '', title_accent_italic: false,
        description: '27 layers, French butter.',
        cta_text: `€3.50 — Add`, cta_url: '#',
      },
      {
        letter: 'C', letter_color: HONEYD,
        top_bg: { type: 'gradient', gradient_from: PAPER2, gradient_to: LINE, gradient_angle: 135 },
        screenshot_label: 'CINNAMON BUN · STICKY',
        brand_label: 'SWEET', brand_color: RYE,
        show_badge: true, badge_text: 'GONE BY 10', badge_bg: INK, badge_color: PAPER,
        title: 'Cardamom Bun', title_accent: '', title_accent_italic: false,
        description: 'Sticky, fragrant, gone by 10.',
        cta_text: `€4 — Add`, cta_url: '#',
      },
      {
        letter: 'F', letter_color: HONEYD,
        top_bg: { type: 'gradient', gradient_from: PAPER2, gradient_to: LINE, gradient_angle: 135 },
        screenshot_label: 'FOCACCIA · OLIVE OIL',
        brand_label: 'BREAD', brand_color: RYE,
        show_badge: false, badge_text: '', badge_bg: INK, badge_color: WHITE,
        title: 'Rosemary Focaccia', title_accent: '', title_accent_italic: false,
        description: 'Olive oil, flaky salt, dimples.',
        cta_text: `€5 — Add`, cta_url: '#',
      },
    ],
    card_bg: { type: 'solid', color: CARD },
    card_color: TXDIM,
    card_radius: R(18), card_radius_hover: R(18), card_radius_hover_duration: 300,
    card_shadow: 'sm',
    card_padding: 18,
    top_aspect_ratio: '1/1',
    top_padding: 24,
    letter_font_family: 'serif',
    letter_size: 96,
    letter_italic: true,
    letter_align: 'center',
    show_screenshot_label: true,
    screenshot_label_color: `rgba(58,42,18,.4)`,
    brand_size: 10,
    brand_letter_spacing: 0.08,
    title_font_family: 'serif',
    title_size: 21,
    title_weight: '400',
    description_size: 13,
    cta_size: 12,
    cta_arrow: false,
    card_hover_effect: 'lift',
  })])])
]));

// ─── 3) HOURS STRIP — "Come early" (info-cards ×4 orari) ────────────────────
// CSS: .hc-hours__in → grid 4-col gap:1px bg:var(--line) border+radius:14px
// .hc-hour → CARD bg, padding 24. .d = giorno (uppercase bold honey-d), .t = orario (serif 21 ink), .n = nota (dim 12.5)
// Struttura: titolo = giorno, "counter" = orario (serif), description = nota
home.push(sec(PAPER, 'large', [
  row([col('1-1', [shead(`When we're open`, `Come `, `early`, null)])]),
  row([col('1-1', [tile('info-cards', {
    container_bg: { type: 'solid', color: LINE },
    container_padding: 0, container_gap: 1, columns: 4, items_gap: 1,
    card_bg: { type: 'solid', color: CARD },
    card_color: TXDIM, card_radius: R(14), card_padding: 24,
    show_icon: false, show_counter: true, show_counter_label: false,
    show_arrow: false, show_footer: false, show_media: false,
    counter_shape: 'plain', counter_color: INK, counter_size: 21, counter_font: 'serif',
    title_color: HONEYD,
    title_font_family: 'sans-serif', title_size: 11, title_weight: '700', title_italic: false,
    description_size: 12.5,
    items: [
      { counter: '7:00 – 15:00', title: `TUE – FRI`, description: 'Or until sold out' },
      { counter: '7:30 – 16:00', title: 'SATURDAY', description: 'Cake day' },
      { counter: '8:00 – 13:00', title: 'SUNDAY', description: 'Brunch bakes' },
      { counter: 'Closed', title: 'MONDAY', description: 'Feeding the starter' },
    ],
    card_hover_effect: 'none',
  })])])
]));

// ─── 4) FEATURE SPLIT — "Our story" (hero-split image-free / media sx, testo dx)
// CSS: .hc-story → grid 1fr 1fr gap:56px. Media (aspect 4/5, radius 18) a sx; testo a dx.
home.push(sec(CARD, 'large', [row([col('1-1', [tile('hero-split', {
  eyebrow_text: 'Our story',
  eyebrow_dot_color: HONEYD, eyebrow_color: HONEYD,
  headline_lines: [
    { text: 'One oven, one ', color: INK, italic: false },
    { text: `starter,`, color: HONEYD, italic: true },
    { text: 'a lot of mornings', color: INK, italic: false },
  ],
  headline_font_family: 'serif', headline_font_size: 48, headline_line_height: 1.08,
  headline_font_weight: '400', headline_align: 'left',
  subhead: `Honeycomb began with a wild yeast starter named Doris and a market stall. Ten years on, Doris is still going — and still the boss.\n\nWe mill some of our own flour, prove everything slowly, and bake in small batches so it's always fresh. When it's gone, it's gone.`,
  subhead_color: TXT, subhead_size: 16, subhead_italic: false, subhead_max_width: 520,
  cta1_text: 'Meet the bakers', cta1_url: '#',
  cta1_bg: 'transparent', cta1_color: INK,
  cta1_border: LINE2, cta1_size: 14, cta1_radius: R(999), cta1_radius_hover: R(999),
  cta2_text: '', cta2_url: '',
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: PAPER2 },
  showcase_padding: 32, showcase_radius: R(18), showcase_radius_hover: R(18),
  showcase_badge_text: 'SINCE 2016', showcase_badge_dot: HONEY,
  showcase_badge_bg: INK, showcase_badge_color: WHITE,
  showcase_items: [
    { number: 'Sourdough starter', text: 'Doris', italic: true, text_color: HONEYD, bg: { type: 'solid', color: CARD } },
    { number: 'Flour milled', text: 'in-house', italic: false, text_color: INK, bg: { type: 'solid', color: CARD } },
    { number: 'Batch size', text: 'small', italic: false, text_color: INK, bg: { type: 'solid', color: CARD } },
  ],
  showcase_card_radius: R(11), showcase_card_radius_hover: R(11), showcase_card_shadow: 'none',
  showcase_caption_left: 'HONEYCOMB', showcase_caption_right: 'BAKERY',
  showcase_hover_effect: 'none',
  split_ratio: '1fr 1fr', gap: 56, min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
})])])]));

// ─── 5) GALLERY — "A morning at Honeycomb" ──────────────────────────────────
// CSS: .hc-gal → grid 4-col, auto-rows 180px, gap 12. 6 celle: tall (span 2 row), wide (span 2 col).
// Tile gallery con layout grid (masonry), 4 colonne, 6 immagini placeholder.
// (⚠ segnalato: cella tall/wide span non è riproducibile nativamente dal tile gallery standard)
home.push(sec(PAPER, 'large', [
  row([col('1-1', [shead(`From the bench`, `A morning at `, `Honeycomb`, null)])]),
  row([col('1-1', [tile('gallery', {
    layout: 'masonry',
    columns: '4',
    gap: '12',
    img_height: '180px',
    object_fit: 'cover',
    thumb_radius: '12',
    show_caption: false,
    fx_hover_zoom: true,
    fx_hover_zoom_scale: '1.06',
    fx_hover_tilt: false,
    shadow: 'none',
    images: [
      { url: '', caption: 'scoring a loaf' },
      { url: '', caption: 'croissant lamination' },
      { url: '', caption: 'cooling rack' },
      { url: '', caption: 'the counter' },
      { url: '', caption: 'cardamom buns' },
      { url: '', caption: `flour and hands` },
    ],
    tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
  })])])
]));

// ─── 6) TESTIMONIAL ─────────────────────────────────────────────────────────
// CSS: .hc-testi q → disp italic, 24-40px; .hc-testi__by → honey-d uppercase 12px bold
// author_name = "Greta H.", role = "Saturday regular"
home.push(sec(CARD, 'large', [row([col('1-1', [tile('testimonial', {
  quote: `"I've stopped pretending I'll bake my own. Honeycomb's sourdough ruined me for supermarket bread — in the best way."`,
  author_name: 'Greta H.', author_role: 'Saturday regular', rating: '0',
  layout: 'single', show_line: false,
  bg_color: 'transparent', text_color: INK, border_radius: '0', avatar: '',
})])])]));

// ─── 7) BUILD-A-BOX — tile `builder` interattivo con stepper +/- e totale live.
// Blueprint: section#box data-builder data-currency="€" data-cap="6" — 6 pastries, cap 6.
// zone_accent/zone_on dal CSS --fx-zone-accent:#f0b429 / --fx-zone-on:#3a2a12.
home.push(sec(PAPER, 'large', [
  row([col('1-1', [tile('builder', {
    eyebrow: 'Build a box',
    heading: `Box of six`,
    intro: `Mix any six pastries for weekend pickup — tap to add and we\`ll have it bagged and still warm.`,
    currency: '€',
    cap: 6,
    total_label: 'Total',
    count_label: 'of 6',
    cta_text: 'Reserve your box',
    cta_url: '#',
    zone_accent: '#f0b429',
    zone_on: '#3a2a12',
    card_bg: CARD,
    card_border: `1px solid ${LINE}`,
    align: 'left',
    items: [
      { name: 'Butter Croissant',  price: 3.5,  note: 'Classic, flaky, golden.',         start: false },
      { name: 'Pain au Chocolat',  price: 4,    note: 'Dark chocolate, two rods.',        start: false },
      { name: 'Almond Croissant',  price: 4.5,  note: 'Frangipane, toasted flakes.',      start: false },
      { name: 'Cinnamon Bun',      price: 4,    note: 'Brown sugar, cardamom swirl.',     start: false },
      { name: 'Morning Bun',       price: 3.5,  note: 'Orange zest, sugar crust.',        start: false },
      { name: `Kouign-Amann`,      price: 4.5,  note: 'Caramelised, chewy, unmissable.',  start: false },
    ],
  })])])
]));

// ─── 8) BAKERS % CALC — tile `scaler` interattivo (mode='percent') ────────────
// Blueprint: slider farina 300–1500g step 25, base 1000g.
// In mode='percent': quantità mostrata = slider × (amount / 100) → baker's %.
// --fx-zone-accent:#e09a18 (HONEY) dal CSS; bg widget = CARD, bordo LINE.
// show_total:true → "Total dough" (blueprint data-bk-total).
home.push(sec(PAPER, 'large', [
  row([col('1-1', [shead(
    `For the home bakers`,
    `The maths in the `,
    `dough`,
    `Our weekend sourdough, written the way bakers think — in percentages of the flour. Set your flour weight and everything else falls into place.`
  )])]),
  row([col('1-1', [tile('scaler', {
    eyebrow: 'Set your flour',
    heading: '',
    intro: `~78% hydration, naturally leavened, a long cold prove. Salt last, always. Bake at 250°C with steam for the first fifteen minutes.`,
    mode: 'percent',
    base_label: 'Flour',
    base_value: 1000,
    base_min: 300,
    base_max: 1500,
    base_step: 25,
    base_suffix: 'g',
    items: [
      { name: 'Water',    amount: 78,  unit: 'g' },
      { name: 'Levain',   amount: 20,  unit: 'g' },
      { name: 'Sea salt', amount: 2,   unit: 'g' },
    ],
    show_total: true,
    total_label: 'Total dough',
    total_unit: 'g',
    zone_accent: HONEY,
    card_bg: CARD,
    card_border: `1px solid ${LINE}`,
    align: 'left',
  })])])
]));

// ─── 9) CTA — "Save your loaf." ─────────────────────────────────────────────
// CSS: .hc-cta → bg INK, h2 color PAPER, h2 em color HONEY. 1 solo bottone (HONEY).
home.push(sec(INK, 'large', [row([col('1-1', [tile('cta-banner', {
  headline: 'Save your ', headline_accent: `loaf.`, headline_accent_italic: true,
  subtitle: `Pre-order sourdough and pastries for weekend pickup — so you never miss out to the early crowd.`,
  cta_text: 'Pre-order for pickup', cta_url: '#',
  cta2_text: '', cta2_url: '',
  bg: { type: 'solid', color: INK }, text_color: PAPER, accent_color: HONEY,
  subtitle_color: '#e3d3ad',
  cta_bg: HONEY, cta_color: WHITE, cta_radius: R(999), cta_size: 15,
  headline_font_family: 'serif', headline_size: 60, headline_weight: '400',
  subtitle_size: 17,
  layout: 'stack', vertical_align: 'center',
  banner_radius: R(0), banner_padding: 80,
})])])]));

// ── Emit ──────────────────────────────────────────────────────────────────────
K.emit({
  slug: 'honeycomb',
  name: 'Honeycomb',
  tags: ['food', 'bakery', 'restaurant', 'artisan', 'light'],
  description: `Honeycomb — artisan bakery. Warm wheat + honey palette, Gloock (display) + Be Vietnam Pro (body). Tile-pure, image-free. Food & Drink category.`,
  colors: {
    primary:          HONEY,
    primary_contrast: WHITE,
    secondary:        RYE,
    secondary_contrast: PAPER,
    muted:            PAPER2,
    muted_contrast:   TXT,
    text:             TXT,
    text_muted:       TXDIM,
    background:       PAPER,
    border:           LINE,
    link:             HONEYD,
  },
  css_disp:  `"Gloock", Georgia, serif`,
  css_sans:  `"Be Vietnam Pro", -apple-system, sans-serif`,
  heading_weight: '400',
  heading_line_height: '1.08',
  google_fonts: ['Gloock', 'Be Vietnam Pro'],
  logo_variant: 'dark',
  menu: [
    { title: 'The bakes', url: '#bakes' },
    { title: 'Hours',     url: '#hours' },
    { title: 'Our story', url: '#story' },
    { title: 'Gallery',   url: '#gallery' },
  ],
  header: {
    bg:        `rgba(246,236,214,.92)`,
    text_color: TXT,
    sticky_bg: `rgba(246,236,214,.95)`,
    logo_width: 130,
  },
  footer: {
    bg:        PAPER2,
    headColor: INK,
    brand: {
      name:    'Honeycomb',
      tagline: 'Neighbourhood bakery. Slow-proved bread, viennoiserie and cakes, fresh before dawn.',
    },
    columns: [
      { title: 'Bakery',  links: ['The bakes', 'Cakes to order', 'Hours', 'Pre-order'] },
      { title: 'About',   links: ['Our story', 'Wholesale', 'Jobs'] },
      { title: 'Find us', links: ['14 Mill Street', `Tue–Sun · from 7am`, 'hello@honeycomb.bakery'] },
    ],
    bottom: {
      left:  `© 2026 Honeycomb — an OLOtheme demo.`,
      right: 'Built with OLObuild',
    },
  },
  cursor: false,
}, home);
