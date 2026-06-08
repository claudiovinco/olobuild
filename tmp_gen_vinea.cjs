/* Vinea — ricomposizione TILE-PURE pixel-perfect. Food & Drink (boutique winery).
   Palette: deep wine + gold. Prata (display) + Albert Sans (body).
   SEZIONI: Hero (centrato), Story (split), Wine List (pricelist 4-card),
            Experiences (info-cards 3 col media-top), Hours (info-cards 4 col),
            Testimonial, Finder (tile vero — 4 chip interattivi),
            CTA (cta-banner 2 bottoni). */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('vn');

/* ─── PALETTE (da :root vinea.css) ─────────────────────────── */
const BG    = '#2a0f1a';
const BG2   = '#33121f';
const PANEL = '#3a1626';
const PANEL2= '#481d30';
const INK   = '#1c0a12';
const GOLD  = '#c79a4e';
const GOLDL = '#d9b370';
const BLUSH = '#d99fae';
const CREAM = '#f1e4d8';
const TXT   = '#cbb0b8';
const DIM   = '#977483';
const LINE  = 'rgba(241,228,216,.13)';
const LINE2 = 'rgba(199,154,78,.42)';
const WHITE = '#ffffff';

const home = [];

/* ────────────────────────────────────────────────────────────
   1) HERO — centrato (testo centrato, 2 CTA, sfondo wine scuro)
   Blueprint: .vn-hero — text-align:center, max-width:740px, h1 con em gold
   ──────────────────────────────────────────────────────────── */
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('hero', {
  title: `Wine that tastes of <em style="font-style:italic;color:${GOLD}">this hill.</em>`,
  subtitle: `Small-lot wines grown, fermented and bottled on our estate — honest, place-driven and made in numbers small enough to remember each one.`,

  text_color: CREAM,
  title_color: WHITE,
  subtitle_color: CREAM,

  title_tag: 'h1',
  title_font_family: 'serif',
  title_font_size: '72',
  title_font_weight: '400',
  title_letter_spacing: '0',
  title_line_height: '1.04',
  title_text_transform: 'none',

  subtitle_font_size: '18',
  subtitle_font_weight: '400',
  subtitle_max_width: '480',

  min_height: '620px',
  content_max_width: '740',
  vertical_align: 'center',
  horizontal_align: 'center',
  text_align: 'center',

  cta_text: 'Book a tasting',
  cta_url: '#experiences',
  cta_target: '_self',
  cta_style: 'filled',
  cta_bg_color: GOLD,
  cta_text_color: INK,
  cta_size: '12',
  cta_radius: R(2),

  cta2_text: 'Explore the wines',
  cta2_url: '#wines',
  cta2_target: '_self',
  cta2_style: 'outline',
  cta2_bg_color: 'transparent',
  cta2_text_color: CREAM,

  tile_padding: { top: 80, right: 20, bottom: 80, left: 20 },

  bg: { type: 'solid', color: BG },
}) ]) ]) ]));

/* ────────────────────────────────────────────────────────────
   helper: section-header centrato (per .sec-head centrato)
   ──────────────────────────────────────────────────────────── */
const shead = (eyebrow, l1, accent) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: GOLD, eyebrow_dot_color: GOLD, eyebrow_separator: '',
  headline_lines: [
    { text: l1,     color: CREAM, italic: false },
    { text: accent, color: GOLD,  italic: true  },
  ],
  headline_font_family: 'serif', headline_font_size: 52, headline_font_weight: '400', headline_align: 'center', headline_inline: true,
  tagline_show: false,
  layout: 'center', gap: 16,
});

/* ────────────────────────────────────────────────────────────
   2) STORY (FeatureSplit) — hero-split image-free
   Blueprint: .vn-story — 2 col 1:1, media sinistra (aspect 4/5), testo destra
   ──────────────────────────────────────────────────────────── */
home.push(sec(BG2, 'large', [ row([ col('1-1', [ tile('hero-split', {
  eyebrow_text: 'Our estate', eyebrow_dot_color: GOLD, eyebrow_color: GOLD,
  headline_lines: [
    { text: 'Three generations,', color: CREAM, italic: false },
    { text: 'one hillside',       color: GOLD,  italic: true  },
  ],
  headline_font_family: 'serif', headline_font_size: 52, headline_font_weight: '400', headline_align: 'left',
  subhead: `We farm eleven hectares on a south-facing slope our family has worked since 1962. Organic in the vineyard, patient in the cellar, and stubborn about letting the place speak.

Everything is hand-picked, wild-fermented and aged unhurried. We make about thirty thousand bottles a year — and not one more than the hill can give well.

— The Aldobrandi family`,
  subhead_color: TXT, subhead_size: 16, subhead_italic: false, subhead_max_width: 520,
  cta1_text: '', cta1_url: '',
  cta1_bg: 'transparent', cta1_color: 'transparent', cta1_size: 0, cta1_radius: R(0),
  cta2_text: '', cta2_url: '',
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: PANEL },
  showcase_padding: 36, showcase_radius: R(4), showcase_radius_hover: R(4),
  showcase_badge_text: 'ESTATE · TOSCANA',
  showcase_badge_dot: GOLD, showcase_badge_bg: INK, showcase_badge_color: CREAM,
  showcase_items: [
    { number: 'Est.',       text: '1962',      italic: false, text_color: GOLD,  bg: { type: 'solid', color: BG } },
    { number: 'Hectares',   text: '11',        italic: false, text_color: CREAM, bg: { type: 'solid', color: BG } },
    { number: 'Bottles/yr', text: '30k',       italic: false, text_color: CREAM, bg: { type: 'solid', color: BG } },
    { number: 'Farming',    text: 'Organic',   italic: false, text_color: GOLD,  bg: { type: 'solid', color: BG } },
  ],
  showcase_card_radius: R(8), showcase_card_radius_hover: R(8), showcase_card_shadow: 'none',
  showcase_caption_left: 'PODERE VINEA', showcase_caption_right: 'EST. 1962',
  showcase_hover_effect: 'none',
  split_ratio: '1.05fr .95fr', gap: 56, min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
}) ]) ]) ]));

/* ────────────────────────────────────────────────────────────
   3) WINE LIST — pricelist 4 vini (card layout)
   Blueprint: .vn-wines — 4 col, .vn-wine: bg panel, border 1px LINE,
   radius 10px, padding 26px 22px, text-align:center
   Anno gold (11px uppercase), h3 serif 20px, tipo 13px DIM, prezzo Prata 22px CREAM
   ──────────────────────────────────────────────────────────── */
home.push(sec(PANEL, 'large', [
  row([ col('1-1', [ shead('The current release', `This year's `, 'wines') ]) ]),
  row([ col('1-1', [ tile('pricelist', {
    show_image: false,
    price_position: 'below',
    separator_style: 'none',
    separator_color: 'transparent',
    title_color: CREAM,
    price_color: CREAM,
    description_color: GOLD,
    card_bg: PANEL,
    card_border_radius: '10',
    card_border_color: LINE,
    gap: '18',
    tile_padding: { top: 22, right: 22, bottom: 22, left: 22 },
    hover_lift: true,
    badge_bg: GOLD, badge_color: INK, badge_border_radius: '4',
    items: [
      { id: 'vn-pl-1', title: 'Costa Rossa',       description: `2022 · Sangiovese · red`,        price: '€28', image_url: '', highlighted: false, badge: '' },
      { id: 'vn-pl-2', title: 'Bianco di Sasso',   description: '2023 · Vermentino · white',      price: '€24', image_url: '', highlighted: false, badge: '' },
      { id: 'vn-pl-3', title: 'Riserva del Nonno', description: `2021 · Sangiovese · reserve`,    price: '€46', image_url: '', highlighted: false, badge: '' },
      { id: 'vn-pl-4', title: `Rosa d'Estate`,     description: '2024 · Rosé · skin contact',     price: '€22', image_url: '', highlighted: false, badge: '' },
    ],
  }) ]) ]),
]));

/* ────────────────────────────────────────────────────────────
   4) EXPERIENCES (CardGrid) — info-cards 3 esperienze
   Blueprint: .vn-exp — 3 col, .vn-card: radius 10px, border LINE, min-h 340px
   media assoluta + overlay gradient dal basso, body z-index 2 (px/h3/p)
   ──────────────────────────────────────────────────────────── */
home.push(sec(BG, 'large', [
  row([ col('1-1', [ shead('Visit us', 'Come to the ', 'source') ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0, container_gap: 0, columns: 3, items_gap: 18,
    card_bg: { type: 'solid', color: PANEL },
    card_color: CREAM, card_radius: R(10), card_padding: 26,
    show_icon: false, show_counter: false, show_counter_label: false,
    show_arrow: false, show_footer: true, show_media: false,
    title_color: WHITE,
    title_font_family: 'serif', title_size: 25, title_weight: '400', title_italic: false,
    description_size: 14,
    description_color: CREAM,
    footer_size: 12,
    footer_color: GOLD,
    card_hover_effect: 'lift',
    items: [
      {
        title: 'Cellar Tasting',
        description: 'Five wines, a tour of the cellar and a seat on the terrace overlooking the rows.',
        footer_text: 'From €25 · 1 hr', footer_dot_color: GOLD,
      },
      {
        title: `Walk & Long Lunch`,
        description: 'A walk through the vines into a slow estate lunch paired with the full range.',
        footer_text: 'From €65 · 3 hrs', footer_dot_color: GOLD,
      },
      {
        title: 'Harvest Day',
        description: `Pick with us at dawn, press by hand, and eat like family. Our favourite day of the year.`,
        footer_text: 'Sept · by request', footer_dot_color: GOLD,
      },
    ],
  }) ]) ]),
]));

/* ────────────────────────────────────────────────────────────
   5) HOURS STRIP (tasting room) — info-cards 4 slot orari
   Blueprint: .vn-hours__in — 4 col grid-gap 1px su sfondo LINE (separator visivo)
   border 1px LINE su contenitore, radius 12px, overflow hidden
   Ogni .vn-hour: bg BG2, padding 26px 24px
   .d = day (gold 700 11px uppercase), .t = orario (Prata 21px CREAM), .n = nota (12.5px DIM)
   ──────────────────────────────────────────────────────────── */
home.push(sec(BG2, 'large', [
  row([ col('1-1', [ shead('The tasting room', 'When to ', 'find us') ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg: { type: 'solid', color: LINE },
    container_padding: 1, container_gap: 0, columns: 4, items_gap: 1,
    card_bg: { type: 'solid', color: BG2 },
    card_color: TXT, card_radius: R(0), card_padding: 26,
    show_icon: false, show_counter: true, show_counter_label: false,
    show_arrow: false, show_footer: false, show_media: false,
    counter_shape: 'plain', counter_color: GOLD, counter_size: 11,
    title_color: CREAM,
    title_font_family: 'serif', title_size: 21, title_weight: '400', title_italic: false,
    description_size: 12.5,
    description_color: DIM,
    card_hover_effect: 'none',
    items: [
      { counter: 'Wed – Fri', title: '11:00 – 18:00', description: 'Walk-ins welcome'   },
      { counter: 'Saturday',  title: '10:00 – 19:00', description: 'Tastings hourly'    },
      { counter: 'Sunday',    title: '11:00 – 16:00', description: 'Long lunch only'    },
      { counter: 'Mon – Tue', title: 'By appointment', description: `Groups & trade`    },
    ],
  }) ]) ]),
]));

/* ────────────────────────────────────────────────────────────
   6) TESTIMONIAL
   Blueprint: .vn-testi — centrato, q font Prata italic clamp(24px–40px) CREAM
   byline: 600 12px uppercase .14em GOLD
   ──────────────────────────────────────────────────────────── */
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('testimonial', {
  quote: `"We tasted six wines and bought a case of each. But it's the afternoon on that terrace we keep talking about."`,
  author_name: `Claire & Yann`,
  author_role: 'cellar club members',
  rating: '0',
  layout: 'single', show_line: false,
  bg_color: 'transparent', text_color: CREAM,
  author_color: GOLD,
  quote_font_family: 'serif',
  quote_size: 34, quote_style: 'italic',
  border_radius: '0', avatar: '',
}) ]) ]) ]));

/* ────────────────────────────────────────────────────────────
   7) FINDER — tile vero "finder" (chip → result-card interattivo)
   Blueprint: data-finder — 4 chip (Big & red / Crisp & white / Easy rosé / One to keep)
   → result card con nome vino, tipo, descrizione, prezzo.
   zone_accent = #b8893f (gold, da --fx-zone-accent nel blueprint)
   zone_on     = #2a0f1a (ink scuro, da --fx-zone-on)
   card_bg     = PANEL (#3a1626), card_border = LINE2 (rgba gold)
   ──────────────────────────────────────────────────────────── */
home.push(sec(BG2, 'large', [
  row([ col('1-1', [ tile('finder', {
    eyebrow: `Not sure where to start?`,
    heading: `Find your bottle`,
    intro: '',
    zone_accent: '#b8893f',
    zone_on: '#2a0f1a',
    card_bg: PANEL,
    card_border: LINE2,
    align: 'center',
    items: [
      {
        option: `Big & red`,
        icon: 'flame',
        title: `Costa Rossa`,
        text: `Our table red — bramble, dried herb and soft tannin. The bottle that empties first at dinner.`,
        meta: `2022 · Sangiovese · €28`,
        cta_text: '',
        cta_url: '#',
      },
      {
        option: `Crisp & white`,
        icon: 'droplets',
        title: `Bianco di Sasso`,
        text: `Citrus, sea-salt and a stony finish. Cold from the fridge on a warm evening — done.`,
        meta: `2023 · Vermentino · €24`,
        cta_text: '',
        cta_url: '#',
      },
      {
        option: `Easy rosé`,
        icon: 'heart',
        title: `Rosa d'Estate`,
        text: `Pale copper, wild strawberry, barely-there grip. The easiest yes on the list.`,
        meta: `2024 · Rosé · €22`,
        cta_text: '',
        cta_url: '#',
      },
      {
        option: `One to keep`,
        icon: 'award',
        title: `Riserva del Nonno`,
        text: `Three years before release, built to keep a decade more. Cherry, leather, sweet tobacco.`,
        meta: `2021 · Sangiovese Riserva · €46`,
        cta_text: '',
        cta_url: '#',
      },
    ],
  }) ]) ]),
]));

/* ────────────────────────────────────────────────────────────
   8) CTA — "Join the cellar club" (2 bottoni)
   Blueprint: .vn-cta__cta — btn--gold "Join the club" + btn--cream "Book a visit"
   .vn-cta__box: border 1px LINE2, radius 8px, padding clamp(48–92px)
   ──────────────────────────────────────────────────────────── */
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('cta-banner', {
  headline: 'Join the ',
  headline_accent: 'cellar club',
  headline_accent_italic: true,
  subtitle: 'Two shipments a year, first pick of small lots, and an open invitation to harvest. No membership fee.',
  cta_text: 'Join the club',
  cta_url: '#',
  cta2_text: 'Book a visit',
  cta2_url: '#experiences',
  cta2_bg: CREAM,
  cta2_color: INK,
  cta2_border: 'transparent',
  bg: { type: 'solid', color: PANEL2 },
  text_color: WHITE,
  accent_color: GOLD,
  subtitle_color: TXT,
  cta_bg: GOLD,
  cta_color: INK,
  cta_radius: R(2),
  cta_size: 12,
  headline_font_family: 'serif', headline_size: 52, headline_weight: '400', subtitle_size: 17,
  layout: 'stack', vertical_align: 'center',
  banner_radius: R(8), banner_padding: 80,
}) ]) ]) ]));

/* ────────────────────────────────────────────────────────────
   EMIT
   ──────────────────────────────────────────────────────────── */
K.emit({
  slug: 'vinea', name: 'Vinea',
  tags: ['food', 'drink', 'winery', 'restaurant', 'luxury'],
  description: `Vinea — boutique winery & cellar. Deep wine + gold, Prata (display) + Albert Sans. Riproduzione fedele dell'OLOtheme Vinea (Food & Drink).`,
  colors: {
    primary: GOLD, primary_contrast: INK,
    secondary: BLUSH, secondary_contrast: INK,
    muted: BG2, muted_contrast: TXT,
    text: TXT, text_muted: DIM,
    background: BG, border: LINE,
    link: GOLD,
  },
  css_disp: `"Prata", Georgia, serif`,
  css_sans: `"Albert Sans", -apple-system, sans-serif`,
  heading_weight: '400', heading_line_height: '1.12',
  google_fonts: ['Prata', 'Albert Sans'],
  logo_variant: 'light',
  menu: [
    { title: 'Wines',       url: '#wines'       },
    { title: 'Visit',       url: '#experiences' },
    { title: 'Our estate',  url: '#story'       },
    { title: 'Cellar club', url: '#club'        },
  ],
  header: { bg: 'rgba(42,15,26,.86)', text_color: TXT, sticky_bg: 'rgba(42,15,26,.94)', logo_width: 130 },
  footer: {
    bg: BG2,
    headColor: CREAM,
    brand: { name: 'Vinea', tagline: 'Family winery in the hills. Small-lot, estate-grown, made the patient way.' },
    columns: [
      { title: 'Wine',    links: ['The wines', 'Cellar club', 'Trade & export'] },
      { title: 'Visit',   links: ['Tastings', 'Long lunch', 'Our estate'] },
      { title: 'Find us', links: ['Podere Vinea, Toscana', `Wed–Sun · from 10am`, 'ciao@vinea.wine'] },
    ],
    bottom: { left: `© 2026 Vinea — an OLOtheme demo.`, right: 'Built with OLObuild' },
  },
  cursor: { blend_mode: 'exclusion', ring_color: GOLD, dot_color: GOLD },
}, home);
