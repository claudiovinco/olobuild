/* Lumen — ricomposizione TILE-PURE (image-free). Beauty & Fashion / skincare.
   Palette: cream + ink + blush. Cormorant Garamond (display) + Work Sans (body). */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('lu');

// ── Palette dal :root del CSS ─────────────────────────────────────────────────
const INK    = '#251f24';
const INK2   = '#322a30';
const BLUSH  = '#d9a7a0';
const BLUSHD = '#c4877e';
const ROSE   = '#eccdc7';
const GOLD   = '#b89b78';
const CREAM  = '#f4ece8';
const CREAM2 = '#ede1db';
const PAPER  = '#fffaf8';
const TXT    = '#2a2228';
const TXTSOFT= '#6e636a';
const TXTFNT = '#9c9098';
const LINE   = '#ece0db';
const LINEDK = 'rgba(255,255,255,.13)';
const WHITE  = '#ffffff';

const home = [];

// ── helper: section-header centrato (eyebrow + headline) ─────────────────────
const shead = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: BLUSHD, eyebrow_dot_color: BLUSHD, eyebrow_separator: '',
  headline_lines: [
    { text: l1, color: INK, italic: false },
    { text: accent, color: BLUSHD, italic: true },
  ],
  headline_font_family: 'serif', headline_font_size: 46, headline_font_weight: '600',
  headline_align: 'center', headline_inline: true,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false,
  tagline_text_color: TXTSOFT, tagline_text_size: 16,
  layout: 'center', gap: 16,
});

// ── helper: section-header sinistra ─────────────────────────────────────────
const sheadLeft = (eyebrow, l1, accent) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: BLUSHD, eyebrow_dot_color: BLUSHD, eyebrow_separator: '',
  headline_lines: [
    { text: l1, color: INK, italic: false },
    { text: accent, color: BLUSHD, italic: true },
  ],
  headline_font_family: 'serif', headline_font_size: 40, headline_font_weight: '600',
  headline_align: 'left', headline_inline: true,
  tagline_show: false, layout: 'stack', gap: 12,
});

// ── 1) ANNOUNCEMENT BAR — approssimato con newsticker (una sola voce) ────────
// Il tile AnnouncementBar non esiste; newsticker è il più vicino (striscia testo)
home.push(sec(INK, 'small', [
  row([ col('1-1', [ tile('newsticker', {
    items: [
      { text: `Free carbon-neutral shipping over €45 · 30-day returns` },
    ],
    speed: 0,
    separator: '',
    font_size: '12',
    font_weight: '500',
    text_color: CREAM,
    bg_color: INK,
    letter_spacing: '0.1em',
    text_transform: 'uppercase',
    gap: 0,
    pause_on_hover: false,
  }) ]) ]),
]));

// ── 2) HERO SPLIT ─────────────────────────────────────────────────────────────
home.push(sec(CREAM, 'large', [ row([ col('1-1', [ tile('hero-split', {
  eyebrow_text: `Clean · vegan · refillable`,
  eyebrow_dot_color: BLUSHD,
  eyebrow_color: BLUSHD,
  headline_lines: [
    { text: 'Skin that', color: INK, italic: false },
    { text: 'feels like', color: INK, italic: false },
    { text: `itself again.`, color: BLUSHD, italic: true },
  ],
  headline_font_family: 'serif',
  headline_font_size: 72,
  headline_line_height: 1.04,
  headline_font_weight: '600',
  headline_align: 'left',
  subhead: 'Short ingredient lists that actually do something. Made in small batches, packed in glass you bring back.',
  subhead_color: TXTSOFT,
  subhead_size: 17,
  subhead_italic: false,
  subhead_max_width: 430,
  cta1_text: 'Shop the range', cta1_url: '#shop',
  cta1_bg: INK, cta1_color: CREAM, cta1_size: 13, cta1_radius: R(2), cta1_radius_hover: R(2),
  cta2_text: 'Our ritual', cta2_url: '#ritual',
  cta2_bg: 'transparent', cta2_color: INK, cta2_border: 'rgba(42,34,40,.28)', cta2_size: 13, cta2_radius: R(2), cta2_radius_hover: R(2),
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: CREAM2 },
  showcase_padding: 0,
  showcase_radius: R(3),
  showcase_radius_hover: R(3),
  showcase_badge_text: 'THE DAILY THREE',
  showcase_badge_dot: BLUSHD,
  showcase_badge_bg: PAPER,
  showcase_badge_color: INK,
  showcase_items: [
    { number: 'The Daily Three', text: 'Cleanse · treat · protect', italic: false, text_color: TXTFNT, bg: { type: 'solid', color: PAPER } },
  ],
  showcase_card_radius: R(3),
  showcase_card_radius_hover: R(3),
  showcase_card_shadow: 'none',
  showcase_caption_left: 'LUMEN', showcase_caption_right: 'SKINCARE',
  showcase_hover_effect: 'none',
  split_ratio: '1.02fr 1fr',
  gap: 40, min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
}) ]) ]) ]));

// ── 3) TRUST ROW — 4 voci con icona (border-block 1px var(--line), bg paper) ──
// .lm-trust { border-block:1px solid var(--line); background:var(--paper); }
// .lm-trust__in { display:grid; grid-template-columns:repeat(4,1fr); gap:20px; padding:26px 0; }
// .lm-trust__i { display:flex; align-items:center; gap:12px; justify-content:center; text-align:left; }
// .lm-trust__i b { font-size:13px; font-weight:600; } .lm-trust__i span { font-size:11.5px; color:txt-faint }
home.push(sec(PAPER, 'small', [ row([ col('1-1', [ tile('info-cards', {
  container_bg: { type: 'solid', color: 'transparent' },
  container_padding: 0, container_gap: 0, columns: 4, items_gap: 20,
  card_bg: { type: 'solid', color: 'transparent' },
  card_color: TXTFNT, card_radius: R(0), card_padding: 16,
  show_icon: true, show_counter: false, show_arrow: false, show_footer: false, show_media: false,
  icon_color: BLUSHD, icon_bg_color: 'transparent',
  title_color: INK, title_font_family: 'sans-serif', title_size: 13, title_weight: '600', title_italic: false,
  description_size: 11.5,
  items: [
    { icon: 'flame',        title: 'Clean formulas', description: 'No nasties, ever' },
    { icon: 'heart',        title: 'Cruelty-free',   description: 'Never tested on animals' },
    { icon: 'shield-check', title: 'Derm-approved',  description: 'Tested & reviewed' },
    { icon: 'refresh-cw',   title: 'Refillable',     description: 'Return your glass' },
  ],
  card_hover_effect: 'none',
}) ]) ]) ]));

// ── 4) BESTSELLERS — product-cards (4 prodotti: lettera, categoria, nome, prezzo/badge) ──
// .prod-grid { grid-template-columns:repeat(4,1fr); gap:22px; }
// .prod__media { aspect-ratio:3/3.5; border-radius:3px; }  .prod__badge { background:blush }
// .prod__cat { font-size:11px; color:txt-faint; uppercase } .prod__n { font-disp; font-size:22px }
// .prod__p { font-sans; font-weight:600; font-size:14px }
// product-cards: letter monogramma + top_bg + brand_label (categoria) + title+accent (nome) + badge + cta (prezzo)
home.push(sec(CREAM, 'large', [
  row([ col('1-1', [ shead('Bestsellers', 'Loved on ', 'repeat', '') ]) ]),
  row([ col('1-1', [ tile('product-cards', {
    columns: 4,
    gap: 22,
    card_bg: { type: 'solid', color: PAPER },
    card_color: TXTSOFT,
    card_radius: R(3),
    card_radius_hover: R(3),
    card_radius_hover_duration: 300,
    card_shadow: 'sm',
    card_padding: 20,
    card_hover_effect: 'lift',
    top_aspect_ratio: '3/4',
    top_padding: 24,
    letter_font_family: 'serif',
    letter_size: 120,
    letter_italic: true,
    letter_align: 'center',
    show_screenshot_label: true,
    screenshot_label_color: TXTFNT,
    brand_size: 11,
    brand_letter_spacing: 0.06,
    title_font_family: 'serif',
    title_size: 22,
    title_weight: '600',
    description_size: 12,
    cta_size: 12,
    cta_arrow: false,
    items: [
      {
        letter: 'Q', letter_color: BLUSHD,
        top_bg: { type: 'gradient', gradient_from: CREAM2, gradient_to: ROSE, gradient_angle: 160 },
        screenshot_label: 'SERUM',
        brand_label: 'SERUM', brand_color: BLUSHD,
        show_badge: true, badge_text: 'Bestseller', badge_bg: BLUSH, badge_color: INK,
        title: 'Quiet Glow', title_accent: 'Vitamin C', title_accent_italic: false,
        description: 'Brightening serum — slowly, steadily.',
        cta_text: 'Add to bag · €38', cta_url: '#shop',
      },
      {
        letter: 'S', letter_color: TXTSOFT,
        top_bg: { type: 'gradient', gradient_from: CREAM, gradient_to: CREAM2, gradient_angle: 160 },
        screenshot_label: 'CLEANSER',
        brand_label: 'CLEANSER', brand_color: TXTSOFT,
        show_badge: false, badge_text: '', badge_bg: INK, badge_color: WHITE,
        title: 'Soft Reset', title_accent: 'Gel', title_accent_italic: false,
        description: 'Lifts the day without stripping.',
        cta_text: 'Add to bag · €28', cta_url: '#shop',
      },
      {
        letter: 'B', letter_color: BLUSHD,
        top_bg: { type: 'gradient', gradient_from: ROSE, gradient_to: CREAM2, gradient_angle: 160 },
        screenshot_label: 'MOISTURISER',
        brand_label: 'MOISTURISER', brand_color: BLUSHD,
        show_badge: true, badge_text: 'New', badge_bg: INK, badge_color: CREAM,
        title: 'Barrier Cloud', title_accent: 'Cream', title_accent_italic: false,
        description: 'Seal it all in — morning and night.',
        cta_text: 'Add to bag · €42', cta_url: '#shop',
      },
      {
        letter: 'M', letter_color: GOLD,
        top_bg: { type: 'gradient', gradient_from: CREAM2, gradient_to: CREAM, gradient_angle: 160 },
        screenshot_label: 'TREATMENT',
        brand_label: 'TREATMENT', brand_color: GOLD,
        show_badge: false, badge_text: '', badge_bg: INK, badge_color: WHITE,
        title: 'Midnight Retinal', title_accent: '0.2', title_accent_italic: false,
        description: 'Overnight renewal — €34 (was €44).',
        cta_text: 'Add to bag · €34', cta_url: '#shop',
      },
    ],
  }) ]) ]),
]));

// ── 5) RITUAL SPLIT (dark section) — media placeholder sinistra + content destra ──
// .lm-ritual { background:ink; } .lm-ritual__in { grid: 1fr 1fr; gap:54px; align-items:center; }
// Media sinistra (aspect-ratio 4/4.4, border-radius 3px, background ink-2)
// Destra: eyebrow → h2 → p → lista numerata (01/02/03) → btn--blush
// Lista: .n { font-disp; font-size:22px; color:blush; } testo-bold + descrizione
// → section-header + process-steps (3 passi numerati, numero serif blush grande, borderless)
// → cta-banner per il bottone "Build your routine" (blush bg)
home.push(sec(INK, 'large', [
  row([
    // Media sinistra: placeholder astratto image-free — pannello ink-2 con pattern ripetuto
    // .lm-ritual__media { aspect-ratio:4/4.4; border-radius:3px; background:ink-2 }
    col('1-2', [ tile('section-header', {
      eyebrow_show: false, eyebrow_text: '',
      headline_lines: [],
      headline_font_family: 'serif', headline_font_size: 14, headline_font_weight: '400',
      headline_align: 'center', headline_inline: false,
      tagline_show: true,
      tagline_text: 'model applying serum / texture macro',
      tagline_text_italic: true, tagline_text_color: 'rgba(255,255,255,.35)', tagline_text_size: 11,
      layout: 'center', gap: 0,
      tile_padding: { top: 160, right: 20, bottom: 160, left: 20 },
      bg_color: INK2,
      bg_radius: R(3),
    }) ]),
    col('1-2', [
      tile('section-header', {
        eyebrow_show: true, eyebrow_text: 'The Lumen ritual', eyebrow_color: BLUSH, eyebrow_dot_color: BLUSH, eyebrow_separator: '',
        headline_lines: [
          { text: 'Three steps,', color: WHITE, italic: false },
          { text: 'every day.', color: BLUSH, italic: true },
        ],
        headline_font_family: 'serif', headline_font_size: 44, headline_font_weight: '600',
        headline_align: 'left', headline_inline: false,
        tagline_show: true,
        tagline_text: `Skincare shouldn't need a diagram. Three products, morning and night — the rest is just patience.`,
        tagline_text_italic: false, tagline_text_color: 'rgba(255,255,255,.7)', tagline_text_size: 16,
        layout: 'stack', gap: 14,
      }),
      // process-steps: 3 passi numerati — numero serif blush grande (22px), borderless
      // .lm-ritual__list .n { font-disp; font-size:22px; color:blush; }
      // .lm-ritual__list b  { sans; font-size:14px; font-weight:600 }
      // .lm-ritual__list span { font-size:13px; color:rgba(255,255,255,.6) }
      tile('process-steps', {
        columns: 1,
        gap: 0,
        align: 'left',
        auto_number: false,
        item_gap: 6,
        number_style: 'plain',
        number_color: BLUSH,
        number_size: 22,
        number_font: 'serif',
        number_weight: '600',
        title_color: WHITE,
        title_size: 14,
        title_font: 'sans-serif',
        title_weight: '600',
        desc_color: 'rgba(255,255,255,.6)',
        desc_size: 13,
        card_bg: '',
        card_border: `1px solid ${LINEDK}`,
        card_padding: 14,
        card_radius: R(0),
        items: [
          { number: '01', title: 'Cleanse', description: 'Soft Reset Gel — lifts the day without stripping.' },
          { number: '02', title: 'Treat',   description: 'Quiet Glow Vitamin C — brightness, slowly.' },
          { number: '03', title: 'Protect', description: 'Barrier Cloud Cream — seal it all in.' },
        ],
      }),
      // CTA "Build your routine" — btn--blush (background:blush, color:ink)
      tile('cta-banner', {
        headline: '',
        subheadline: '',
        cta1_text: 'Build your routine',
        cta1_url: '#shop',
        cta1_bg: BLUSH,
        cta1_color: INK,
        cta1_size: 13,
        cta1_radius: R(2),
        bg_color: 'transparent',
        text_color: WHITE,
        align: 'left',
        tile_padding: { top: 20, right: 0, bottom: 0, left: 0 },
      }),
    ]),
  ]),
]));

// ── 6) REVIEWS (3 testimonial in grid) ───────────────────────────────────────
// .lm-rev { background:paper; border:1px solid var(--line); border-radius:4px; padding:30px; }
// .lm-rev .stars { color:gold; } .lm-rev q { font-disp; font-size:21px; line-height:1.3 }
// .lm-rev__by { font-size:12px; color:txt-faint; uppercase; letter-spacing:.04em }
home.push(sec(CREAM, 'large', [
  row([ col('1-1', [ shead('4.9 / 5 · 2,400 reviews', 'Words from ', 'real skin', '') ]) ]),
  row([ col('1-1', [ tile('testimonial', {
    layout: 'grid',
    grid_columns: 3,
    items: [
      {
        quote: `"Six weeks in and my texture has never been calmer. Nothing else changed — just these three."`,
        author_name: 'Amara',
        author_role: 'Combination skin',
        avatar: '',
        rating: '5',
      },
      {
        quote: `"Finally a vitamin C that doesn't sting. And the refill turns up before I run out."`,
        author_name: 'Jonas',
        author_role: 'Sensitive skin',
        avatar: '',
        rating: '5',
      },
      {
        quote: `"The glass packaging alone made me switch. The results made me stay."`,
        author_name: 'Priya',
        author_role: 'Dry skin',
        avatar: '',
        rating: '5',
      },
    ],
    // .lm-rev { background:paper; border:1px solid var(--line); border-radius:4px; padding:30px; }
    bg_color: PAPER,
    text_color: INK,
    show_line: true,
    line_color: LINE,
    border_radius: '4',
    author_position: 'bottom-left',
    quote_font_family: 'serif',
    quote_font_size: 21,
    avatar: '',
  }) ]) ]),
]));

// ── 7) JOURNAL — approssimato con info-cards (PostList non esiste come tile standalone) ──
// .jrn .media { aspect-ratio:4/2.8; border-radius:3px; margin-bottom:16px; }
// .jrn__cat { font-size:11px; color:blush-d; uppercase } .jrn__t { font-disp; font-size:24px }
home.push(sec(PAPER, 'large', [
  row([ col('1-1', [ sheadLeft('The Journal', 'Notes on ', 'skin') ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0, container_gap: 0, columns: 3, items_gap: 24,
    card_bg: { type: 'solid', color: 'transparent' },
    card_color: TXTSOFT, card_radius: R(3), card_padding: 0,
    show_icon: false, show_counter: false, show_counter_label: false, show_arrow: true, show_footer: true, show_media: false,
    title_color: INK, title_font_family: 'serif', title_size: 24, title_weight: '600', title_italic: false,
    description_color: BLUSHD, description_size: 11,
    items: [
      { title: 'Why we keep our lists short',      description: 'Ingredients',  footer_text: 'Read', footer_dot_color: BLUSHD },
      { title: 'How to layer without the pilling', description: 'Routine',      footer_text: 'Read', footer_dot_color: BLUSHD },
      { title: `What "refillable" really costs`,   description: 'Sustainability', footer_text: 'Read', footer_dot_color: BLUSHD },
    ],
    card_hover_effect: 'none',
  }) ]) ]),
]));

// ── 8) SKIN FINDER — tile finder nativo (chip → result card)
// zone_accent / zone_on dal CSS inline --fx-zone-accent / --fx-zone-on.
// card_bg = PAPER, card_border = LINE.
home.push(sec(CREAM2, 'large', [
  row([ col('1-1', [ tile('finder', {
    eyebrow: 'Skin quiz',
    heading: 'Find your ritual',
    intro: '',
    zone_accent: '#c4877e',
    zone_on: '#ffffff',
    card_bg: PAPER,
    card_border: LINE,
    align: 'center',
    items: [
      { option: 'Dry & tight',        title: 'The Quench Ritual',  text: 'Cream cleanser, hyaluronic essence and the ceramide moisturiser — layered to lock water in and stop the afternoon tightness.',           meta: '3 steps · from €68', cta_text: '', cta_url: '#', icon: '' },
      { option: 'Oily / combination', title: 'The Balance Ritual', text: 'Gel cleanser, niacinamide serum and the oil-free gel-cream — to calm shine without stripping, morning and night.',                       meta: '3 steps · from €62', cta_text: '', cta_url: '#', icon: '' },
      { option: 'Sensitive',          title: 'The Calm Ritual',    text: 'Fragrance-free milk cleanser, centella serum and the barrier balm — a short, gentle routine for reactive skin.',                          meta: '3 steps · from €70', cta_text: '', cta_url: '#', icon: '' },
      { option: 'Dull & uneven',      title: 'The Glow Ritual',    text: 'Vitamin C in the morning, a gentle overnight exfoliating serum, and SPF — to even tone and bring back the light.',                       meta: '3 steps · from €74', cta_text: '', cta_url: '#', icon: '' },
    ],
  }) ]) ]),
]));

// ── 9) NEWSLETTER ─────────────────────────────────────────────────────────────
// .lm-news { background:blush; text-align:center; }
// h2 { font-disp; clamp(34px,5vw,60px); } h2 .it { italic }
// p { font-size:16px; margin:14px 0 28px; color:rgba(42,34,40,.7) }
home.push(sec(BLUSH, 'large', [ row([ col('1-1', [ tile('newsletter', {
  title: `Get 10% off your first ritual`,
  subtitle: `Skin notes, restock alerts and the occasional honest word. No daily emails — promise.`,
  layout: 'horizontal',
  icon_type: 'none',
  show_name: false,
  email_placeholder: 'Your email address',
  button_text: 'Subscribe',
  button_icon: false,
  max_width: '440',
  alignment: 'center',
  bg_color: 'transparent',
  border_radius: R(2),
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
  title_size: '48',
  title_weight: '600',
  title_color: INK,
  subtitle_size: '16',
  subtitle_color: 'rgba(42,34,40,.7)',
  input_bg: 'rgba(255,255,255,.5)',
  input_color: INK,
  input_border: 'rgba(42,34,40,.3)',
  input_focus_border: INK,
  input_radius: R(2),
  input_height: '50',
  btn_bg: INK,
  btn_color: CREAM,
  btn_hover_bg: INK2,
  btn_radius: R(2),
  btn_font_size: '13',
  btn_font_weight: '600',
}) ]) ]) ]));

// ── emit ──────────────────────────────────────────────────────────────────────
K.emit({
  slug: 'lumen',
  name: 'Lumen',
  tags: ['beauty', 'fashion', 'skincare', 'ecommerce', 'wellness'],
  description: 'Lumen — clean skincare, honestly made. Cream + ink + blush, Cormorant Garamond (display) + Work Sans. Prodotto beauty/fashion tile-pure, skin finder (tile finder nativo). Riproduzione fedele dell\'OLOtheme Lumen.',
  colors: {
    primary: BLUSHD,
    primary_contrast: INK,
    secondary: GOLD,
    secondary_contrast: INK,
    muted: CREAM2,
    muted_contrast: TXTSOFT,
    text: TXT,
    text_muted: TXTSOFT,
    background: CREAM,
    border: LINE,
    link: BLUSHD,
  },
  css_disp: `"Cormorant Garamond", Georgia, serif`,
  css_sans: `"Work Sans", -apple-system, sans-serif`,
  heading_weight: '600',
  heading_line_height: '1.04',
  google_fonts: ['Cormorant Garamond', 'Work Sans'],
  logo_variant: 'dark',
  menu: [
    { title: 'Shop all',    url: '#shop' },
    { title: 'Skincare',    url: '#skincare' },
    { title: 'Our ritual',  url: '#ritual' },
    { title: 'Journal',     url: '#journal' },
  ],
  header: { bg: 'rgba(244,236,232,.92)', text_color: TXTSOFT, sticky_bg: 'rgba(244,236,232,.92)', logo_width: 130 },
  footer: {
    bg: PAPER,
    headColor: INK,
    brand: { name: 'Lumen', tagline: 'Clean, results-led skincare. Made in small batches, packed in glass you bring back.' },
    columns: [
      { title: 'Shop',  links: ['All products', 'Serums', 'Cleansers', 'Refills'] },
      { title: 'About', links: ['Our ritual', 'Ingredients', 'Journal', 'Sustainability'] },
      { title: 'Help',  links: ['Shipping', 'Returns', 'Contact', 'FAQ'] },
    ],
    bottom: { left: '© 2026 Lumen — an OLOtheme demo.', right: 'Built with OLObuild' },
  },
  cursor: false,
}, home);
