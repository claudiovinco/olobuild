/* Brewline — ricomposizione TILE-PURE (image-free). Food & Drink / specialty coffee roastery.
   Palette: warm dark (#20160f) + caramel (#c98a4b) + crema (#ead9c2).
   Font: Familjen Grotesk (display+body) + Spline Sans Mono (mono/specs).
*/
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('br');

const BG       = '#20160f';
const BG2      = '#251a12';
const PANEL    = '#2d2017';
const PANEL2   = '#372818';
const INK      = '#170f0a';
const CARAMEL  = '#c98a4b';
const CARAMELLIB = '#dba869';
const CREMA    = '#ead9c2';
const GREEN    = '#8a9a5b';
const TXT      = '#cbb89f';
const DIM      = '#8f7a63';
const LINE     = 'rgba(234,217,194,.12)';
const LINE2    = 'rgba(201,138,75,.4)';

const home = [];

// ── helpers ──────────────────────────────────────────────────────────────────
const shead = (eyebrow, l1, accent, intro, align) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: CARAMEL, eyebrow_dot_color: CARAMEL, eyebrow_separator: '',
  headline_lines: [ {text: l1, color: CREMA, italic: false}, {text: accent, color: CARAMEL, italic: true} ],
  headline_font_family: 'sans-serif', headline_font_size: 48, headline_font_weight: '700', headline_align: align || 'center', headline_inline: true,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false, tagline_text_color: DIM, tagline_text_size: 16.5,
  layout: align === 'left' ? 'stack' : 'center', gap: 16,
});

const sheadSimple = (eyebrow, headline, intro, align) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: CARAMEL, eyebrow_dot_color: CARAMEL, eyebrow_separator: '',
  headline_lines: [ {text: headline, color: CREMA, italic: false} ],
  headline_font_family: 'sans-serif', headline_font_size: 46, headline_font_weight: '700', headline_align: align || 'center', headline_inline: false,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false, tagline_text_color: DIM, tagline_text_size: 16,
  layout: align === 'left' ? 'stack' : 'center', gap: 14,
});

// ── 1) HERO (hero-split, image-free) ─────────────────────────────────────────
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('hero-split', {
  eyebrow_text: `Roasted to order \xB7 shipped in 24h`, eyebrow_dot_color: CARAMEL, eyebrow_color: CARAMEL,
  headline_lines: [
    {text: `Coffee with`, color: CREMA, italic: false},
    {text: `a provenance,`, color: CARAMEL, italic: true},
    {text: `not a label.`, color: CREMA, italic: false},
  ],
  headline_font_family: 'sans-serif', headline_font_size: 72, headline_line_height: 1.0, headline_font_weight: '700', headline_align: 'left',
  subhead: `We buy whole-lot, roast in small batches the morning your order lands, and tell you exactly who grew it. No blends hiding behind a brand.`,
  subhead_color: TXT, subhead_size: 18, subhead_italic: false, subhead_max_width: 440,
  cta1_text: `Find your roast`, cta1_url: '#shop', cta1_bg: CARAMEL, cta1_color: INK, cta1_size: 15, cta1_radius: R(999), cta1_radius_hover: R(999),
  cta2_text: `Start a subscription`, cta2_url: '#subscribe', cta2_bg: 'transparent', cta2_color: CREMA, cta2_border: LINE2, cta2_size: 15, cta2_radius: R(999), cta2_radius_hover: R(999),
  stats: [],
  showcase_enabled: true, showcase_bg: {type: 'solid', color: PANEL}, showcase_padding: 26, showcase_radius: R(16), showcase_radius_hover: R(16),
  showcase_badge_text: `THIS MONTH'S LOTS`, showcase_badge_dot: CARAMEL, showcase_badge_bg: INK, showcase_badge_color: CREMA,
  showcase_items: [
    {number: `Ethiopia \xB7 Yirgacheffe`, text: `Idido Washed`, italic: false, text_color: CREMA,  bg: {type: 'solid', color: BG2}},
    {number: `Colombia \xB7 Huila`,       text: `La Esperanza`, italic: false, text_color: CREMA,  bg: {type: 'solid', color: BG2}},
    {number: `Indonesia \xB7 Sumatra`,    text: `Lintong Dark`, italic: false, text_color: CARAMEL, bg: {type: 'solid', color: BG2}},
  ],
  showcase_card_radius: R(11), showcase_card_radius_hover: R(11), showcase_card_shadow: 'none',
  showcase_caption_left: 'SPECIALTY', showcase_caption_right: 'DIRECT TRADE', showcase_hover_effect: 'none',
  split_ratio: '1.05fr .95fr', gap: 52, min_height: 0, tile_padding: {top: 0, right: 0, bottom: 0, left: 0},
}) ]) ]) ]));

// ── 2) TICKER / MARQUEE ───────────────────────────────────────────────────────
home.push(sec(CARAMEL, 'small', [ row([ col('1-1', [ tile('marquee', {
  items: [
    {text: `Single origin`},
    {text: `Roasted to order`},
    {text: `Direct trade`},
    {text: `Free shipping over €40`},
    {text: `Compostable bags`},
    {text: `Roasted in Trieste`},
  ],
  speed: 40, separator: `✶`, separator_color: INK,
  font_family: 'monospace', font_size: '13', font_weight: '500', text_transform: 'uppercase', letter_spacing: '0.06',
  text_color: INK, bg_color: CARAMEL,
  tile_padding: {top: 11, right: 0, bottom: 11, left: 0},
}) ]) ]) ]));

// ── 3) FEATURED PRODUCTS
// product-cards (3 prodotti: origin=brand_label, note=screenshot_label, prezzo+badge).
// CSS: .br-prod { background:var(--panel); border:1px solid var(--line); border-radius:16px; }
// Roast meter NON supportato dal tile — segnalato come best-effort.
home.push(sec(BG, 'large', [
  row([ col('1-1', [
    sheadSimple('// this month\'s lots', 'Fresh on the roaster', null, 'left'),
  ]) ]),
  row([ col('1-1', [ tile('product-cards', {
    columns: 3,
    gap: 20,
    items: [
      {
        letter: 'I', letter_color: CARAMEL, letter_italic: false,
        top_bg: {type: 'solid', color: BG2},
        screenshot_label: `Jasmine \xB7 bergamot \xB7 stone fruit`,
        brand_label: `Ethiopia \xB7 Yirgacheffe`,
        brand_color: DIM,
        show_badge: true, badge_text: 'New lot', badge_bg: INK, badge_color: CARAMEL,
        title: 'Idido Washed', title_accent: '', title_accent_italic: false,
        description: `Washed process \xB7 light roast \xB7 <strong>€19</strong> per 250g`,
        cta_text: 'Add to cart', cta_url: '#shop',
      },
      {
        letter: 'L', letter_color: CARAMEL, letter_italic: false,
        top_bg: {type: 'solid', color: BG2},
        screenshot_label: `Red apple \xB7 caramel \xB7 cocoa`,
        brand_label: `Colombia \xB7 Huila`,
        brand_color: DIM,
        show_badge: false, badge_text: '', badge_bg: INK, badge_color: CARAMEL,
        title: 'La Esperanza', title_accent: '', title_accent_italic: false,
        description: `Washed process \xB7 medium roast \xB7 <strong>€17</strong> per 250g`,
        cta_text: 'Add to cart', cta_url: '#shop',
      },
      {
        letter: 'L', letter_color: CARAMEL, letter_italic: false,
        top_bg: {type: 'solid', color: BG2},
        screenshot_label: `Dark chocolate \xB7 cedar \xB7 molasses`,
        brand_label: `Indonesia \xB7 Sumatra`,
        brand_color: DIM,
        show_badge: true, badge_text: 'Espresso', badge_bg: INK, badge_color: CARAMEL,
        title: 'Lintong Dark', title_accent: '', title_accent_italic: false,
        description: `Natural process \xB7 dark roast \xB7 <strong>€18</strong> per 250g`,
        cta_text: 'Add to cart', cta_url: '#shop',
      },
    ],
    card_bg:               {type: 'solid', color: PANEL},
    card_color:            TXT,
    card_radius:           R(16),
    card_radius_hover:     R(16),
    card_radius_hover_duration: 300,
    card_shadow:           'none',
    card_padding:          20,
    top_aspect_ratio:      '1/1',
    top_padding:           18,
    letter_font_family:    'sans-serif',
    letter_size:           96,
    letter_italic:         false,
    letter_align:          'center',
    show_screenshot_label: true,
    screenshot_label_color: DIM,
    brand_size:            11,
    brand_letter_spacing:  0.05,
    title_font_family:     'sans-serif',
    title_size:            21,
    title_weight:          '700',
    description_size:      13,
    cta_size:              13,
    cta_arrow:             true,
    card_hover_effect:     'lift',
  }) ]) ]),
]));

// ── 4) STAT STRIP (counter x4) ────────────────────────────────────────────────
const stat = (prefix, number, suffix, label) => col('1-4', [ tile('counter', {
  number, suffix, prefix, label, icon_emoji: '',
  text_color: CREMA, number_color: CARAMEL, number_font_size: '50', number_font_weight: '700', label_color: DIM, label_font_size: '12',
  bg_type: 'color', bg_color: 'transparent', padding: '8', border_radius: '0',
}) ]);
home.push(sec(BG2, 'small', [ row([
  stat('', '24',  'h',  'Roast to dispatch'),
  stat('', '18',  '',   'Origins this year'),
  stat('', '7',   '',   'Direct-trade farms'),
  stat('', '11',  'k+', 'Bags shipped'),
], {gap: 24}) ]));

// ── 5) SUBSCRIPTION SPLIT (section-header + iconlist + cta-banner) ────────────
home.push(sec(BG2, 'large', [
  row([ col('1-1', [
    shead('// the standing order', 'A new origin on your shelf,', 'every fortnight',
      'Tell us how you brew and how much you drink. We pick the lot, roast it the day it ships, and you can skip, swap or pause whenever.', 'center'),
  ]) ]),
  row([ col('1-1', [ tile('iconlist', {
    items: [
      {icon: 'check', title: `Roaster’s choice or pick your own`, description: ''},
      {icon: 'check', title: '10% off every bag, free shipping', description: ''},
      {icon: 'check', title: 'Skip, swap or cancel anytime', description: ''},
    ],
    icon_color: CARAMEL, icon_size: '18', icon_style: 'lucide',
    title_color: TXT, title_size: '14.5',
    layout: 'vertical', align: 'center', gap: '14',
    tile_padding: {top: 8, right: 0, bottom: 24, left: 0},
  }) ]) ]),
  row([ col('1-1', [ tile('cta-banner', {
    headline: 'Build my subscription', headline_accent: `— from €15/mo`, headline_accent_italic: false,
    subtitle: '',
    cta_text: `Build my subscription — from €15/mo`, cta_url: '#subscribe',
    bg: {type: 'none', color: 'transparent'}, text_color: CREMA, accent_color: CARAMEL, subtitle_color: TXT,
    cta_bg: CARAMEL, cta_color: INK, cta_radius: R(999), cta_size: 15,
    headline_font_family: 'sans-serif', headline_size: 0, headline_weight: '700', subtitle_size: 0,
    layout: 'stack', vertical_align: 'center', banner_radius: R(0), banner_padding: 0,
    show_headline: false,
  }) ]) ]),
]));

// ── 6) BREW GUIDE STEPS
// process-steps con card bg+border+radius (fedele a .br-step).
// CSS: .br-step { background:var(--panel); border:1px solid var(--line); border-radius:14px; padding:26px 22px; }
// .br-step__n { border-radius:50%; border:1.5px solid var(--caramel); color:var(--caramel); font-family:var(--mono); }
home.push(sec(BG, 'large', [
  row([ col('1-1', [
    sheadSimple('// brew it right', 'Pour-over in four moves',
      'The ratio that works for most of our washed coffees. Adjust to taste — then write it on the bag.', 'center'),
  ]) ]),
  row([ col('1-1', [ tile('process-steps', {
    columns: 4,
    gap: 18,
    align: 'left',
    auto_number: false,
    number_style: 'outline',
    number_color: CARAMEL,
    number_bg: CARAMEL,
    number_size: 38,
    number_font: 'mono',
    number_weight: '400',
    item_gap: 16,
    title_color: CREMA,
    title_size: 18,
    title_font: 'sans-serif',
    title_weight: '700',
    desc_color: DIM,
    desc_size: 13,
    card_bg: PANEL,
    card_border: LINE,
    card_radius: R(14),
    card_padding: 24,
    items: [
      {number: '1', title: 'Weigh',  description: `18g coffee, medium grind. 300g water at 94\xB0C.`},
      {number: '2', title: 'Bloom',  description: 'Pour 50g, wait 40 seconds for the gas to escape.'},
      {number: '3', title: 'Pour',   description: 'Top up in slow circles to 300g over two minutes.'},
      {number: '4', title: 'Enjoy',  description: 'Drawdown by 3:00. Swirl, rest, taste, adjust.'},
    ],
  }) ]) ]),
]));

// ── 7) STORY / ROASTERY SPLIT (section-header + info-cards testo) ─────────────
home.push(sec(BG2, 'large', [
  row([
    col('1-2', [
      tile('section-header', {
        eyebrow_show: true, eyebrow_text: '// our roastery', eyebrow_color: CARAMEL, eyebrow_dot_color: CARAMEL, eyebrow_separator: '',
        headline_lines: [
          {text: 'A drum roaster, a harbour,', color: CREMA, italic: false},
          {text: 'and a lot of notebooks', color: CREMA, italic: false},
        ],
        headline_font_family: 'sans-serif', headline_font_size: 38, headline_font_weight: '700', headline_align: 'left', headline_inline: false,
        tagline_show: false, layout: 'stack', gap: 12,
      }),
    ]),
    col('1-2', [
      tile('info-cards', {
        container_bg: {type: 'solid', color: 'transparent'}, container_padding: 0, container_gap: 12, columns: 1, items_gap: 12,
        card_bg: {type: 'solid', color: 'transparent'}, card_color: DIM, card_radius: R(0), card_padding: 0,
        show_icon: false, show_counter: false, show_arrow: false, show_footer: false, show_media: false,
        title_color: CREMA, title_font_family: 'sans-serif', title_size: 16, title_weight: '600', description_size: 14.5,
        items: [
          {title: '', description: `Brewline started in a Trieste workshop with one second-hand roaster and a stubborn idea: that you should know your coffee’s farm by name.`},
          {title: '', description: `We still roast every batch by ear and log every curve. When a lot’s gone, it’s gone — that’s the point of buying it fresh.`},
        ],
        card_hover_effect: 'none',
      }),
    ]),
  ], {gap: 52, vertical_align: 'center'}),
]));

// ── 8) TESTIMONIAL ────────────────────────────────────────────────────────────
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('testimonial', {
  quote: `“The first subscription that actually changed how I drink coffee. Every fortnight there’s something I’d never have ordered, and it’s always good.”`,
  author_name: 'Sara L.', author_role: 'subscriber, 14 months', rating: '0',
  layout: 'single', show_line: false, bg_color: 'transparent', text_color: CREMA, border_radius: '0', avatar: '',
}) ]) ]) ]));

// ── 9) BUILD YOUR BOX
// Tile reale: 'builder'. Sezione #box data-builder — 6 item con stepper +/− e totale live.
// zone_accent=#c98a4b (--fx-zone-accent), zone_on=#170f0a. card_bg=PANEL, card_border=LINE.
home.push(sec(BG2, 'large', [
  row([ col('1-1', [ tile('builder', {
    eyebrow: 'Subscription',
    heading: 'Build your box',
    intro: `Pick the bags you want roasted fresh each fortnight. Mix origins freely — your box totals up as you go.`,
    currency: '€',
    cap: 0,
    total_label: 'Total',
    count_label: 'bags',
    cta_text: 'Start subscription',
    cta_url: '#subscribe',
    zone_accent: '#c98a4b',
    zone_on: '#170f0a',
    card_bg: PANEL,
    card_border: LINE,
    align: 'center',
    items: [
      { name: 'Aurora — Ethiopia',   price: '14', note: 'Floral \xB7 jasmine, bergamot',    start: 0 },
      { name: 'Tide — Colombia',     price: '13', note: 'Balanced \xB7 cocoa, red apple',   start: 0 },
      { name: 'Ember — Sumatra',     price: '15', note: 'Deep \xB7 cedar, dark sugar',       start: 0 },
      { name: 'House — Daily Blend', price: '13', note: 'Easy \xB7 caramel, toasted nut',   start: 0 },
      { name: 'Reserve — Gesha',     price: '16', note: 'Rare \xB7 stone fruit, tea-like',  start: 0 },
      { name: 'Decaf — Brazil',      price: '12', note: 'Smooth \xB7 hazelnut, cocoa',      start: 0 },
    ],
  }) ]) ]),
]));

// ── 10) CTA FINALE (2 bottoni: Shop the beans + Subscribe & save) ─────────────
// CSS: .br-cta__box { border:1px solid var(--line-2); border-radius:24px; }
// CTA secondario ghost: transparent bg + crema text + line-2 border
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('cta-banner', {
  headline: 'Taste the difference', headline_accent: 'fresh makes', headline_accent_italic: true,
  subtitle: `Order today, we roast tomorrow, you brew by the weekend. Free shipping over €40.`,
  cta_text: 'Shop the beans', cta_url: '#shop',
  cta2_text: `Subscribe & save`, cta2_url: '#subscribe',
  cta2_bg: 'transparent', cta2_color: CREMA, cta2_border: LINE2,
  bg: {type: 'gradient', gradient_from: 'rgba(201,138,75,.18)', gradient_to: 'rgba(201,138,75,.04)', gradient_angle: 150},
  text_color: CREMA, accent_color: CARAMEL, subtitle_color: TXT,
  cta_bg: CARAMEL, cta_color: INK, cta_radius: R(999), cta_size: 15,
  headline_font_family: 'sans-serif', headline_size: 52, headline_weight: '700', subtitle_size: 17,
  layout: 'stack', vertical_align: 'center', banner_radius: R(24), banner_padding: 80,
}) ]) ]) ]));

// ── EMIT ──────────────────────────────────────────────────────────────────────
K.emit({
  slug: 'brewline', name: 'Brewline',
  tags: ['food', 'drink', 'coffee', 'ecommerce', 'roastery'],
  description: 'Brewline — specialty coffee roastery. Warm dark (#20160f) + caramel (#c98a4b) + crema (#ead9c2). Familjen Grotesk display+body. Brew Guide con process-steps (card: panel+line+r14+p24, numero outline caramel). Prodotti con product-cards 3col (roast-meter non supportato). CTA finale con cta2 ghost. Build-your-box con tile builder (6 item stepper, zone_accent caramel).',
  colors: {
    primary: CARAMEL, primary_contrast: INK,
    secondary: GREEN, secondary_contrast: CREMA,
    muted: BG2, muted_contrast: TXT,
    text: TXT, text_muted: DIM,
    background: BG, border: LINE, link: CARAMEL,
  },
  css_disp: '"Familjen Grotesk", -apple-system, sans-serif',
  css_sans: '"Familjen Grotesk", -apple-system, sans-serif',
  heading_weight: '700', heading_line_height: '1.04',
  google_fonts: ['Familjen Grotesk', 'Spline Sans Mono'],
  logo_variant: 'light',
  menu: [
    {title: 'Shop',         url: '#shop'},
    {title: 'Subscribe',    url: '#subscribe'},
    {title: 'Brew guides',  url: '#brew'},
    {title: 'Our roastery', url: '#story'},
  ],
  header: { bg: 'rgba(32,22,15,.86)', text_color: DIM, sticky_bg: 'rgba(32,22,15,.92)', logo_width: 130 },
  footer: {
    bg: BG2, headColor: CREMA,
    brand: {name: 'Brewline', tagline: 'Specialty coffee, roasted to order in Trieste and shipped within 24 hours.'},
    columns: [
      {title: 'Shop',  links: ['All coffee', 'Espresso', 'Decaf', 'Equipment']},
      {title: 'Learn', links: ['Brew guides', 'Our roastery', 'Sourcing', 'Journal']},
      {title: 'Help',  links: ['Subscriptions', 'Shipping & returns', 'Wholesale', 'Contact']},
    ],
    bottom: {left: '\xA9 2026 Brewline — an OLOtheme demo.', right: 'Built with OLObuild'},
  },
  cursor: { blend_mode: 'exclusion', ring_color: CARAMEL, dot_color: CARAMEL },
}, home);
