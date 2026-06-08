/* Field & Co — ricomposizione TILE-PURE (image-free). Olive + tan. Oswald + Figtree. */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('fc');

// ── palette dal :root del CSS ──────────────────────────────────────────────
const BG    = '#232a1c';
const BG2   = '#283020';
const PANEL = '#2e3826';
const PANEL2= '#384430';
const INK   = '#161b11';
const TAN   = '#c2ab63';
const TANL  = '#d2bd7c';
const RUST  = '#c2724a';
const CREAM = '#eef0e3';
const TXT   = '#aab09a';
const DIM   = '#79806a';
const LINE  = 'rgba(238,240,227,.12)';
const LINE2 = 'rgba(194,171,99,.42)';
const VTINT = 'rgba(194,171,99,.14)';
const WHITE = '#ffffff';

const home = [];

// ── helper: section-header centrato ──────────────────────────────────────
const shead = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: TAN, eyebrow_dot_color: TAN, eyebrow_separator: '',
  headline_lines: [
    { text: l1,     color: CREAM, italic: false },
    { text: accent, color: TAN,   italic: true  },
  ],
  headline_font_family: 'serif', headline_font_size: 46, headline_font_weight: '600',
  headline_align: 'center', headline_inline: true,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false,
  tagline_text_color: DIM, tagline_text_size: 16.5,
  layout: 'center', gap: 16,
});

// ── helper: section-header sinistra ──────────────────────────────────────
const sheadLeft = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: TAN, eyebrow_dot_color: TAN, eyebrow_separator: '',
  headline_lines: [
    { text: l1,     color: CREAM, italic: false },
    { text: accent, color: TAN,   italic: true  },
  ],
  headline_font_family: 'serif', headline_font_size: 44, headline_font_weight: '600',
  headline_align: 'left', headline_inline: true,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false,
  tagline_text_color: DIM, tagline_text_size: 16,
  layout: 'stack', gap: 12,
});

// ═══════════════════════════════════════════════════════════════════════════
// 1) HERO — hero-split (image-free: showcase panel astratto a destra)
// ═══════════════════════════════════════════════════════════════════════════
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('hero-split', {
  eyebrow_text:  'Outdoor goods · since 2009',
  eyebrow_dot_color: TAN, eyebrow_color: TAN,
  headline_lines: [
    { text: 'Built for',      color: CREAM,  italic: false },
    { text: 'the long way.',  color: TAN,    italic: true  },
  ],
  headline_font_family: 'serif', headline_font_size: 72,
  headline_line_height: 0.92, headline_font_weight: '600', headline_align: 'left',
  subhead: `Hard-wearing packs, shells and tools — over-built, field-tested, and guaranteed for life. Buy it once.`,
  subhead_color: CREAM, subhead_size: 18, subhead_italic: false, subhead_max_width: 480,
  cta1_text: 'Shop the kit',   cta1_url: '#shop',
  cta1_bg: TAN, cta1_color: INK, cta1_size: 14, cta1_radius: R(4), cta1_radius_hover: R(4),
  cta2_text: 'Build your kit', cta2_url: '#kit',
  cta2_bg: 'transparent', cta2_color: CREAM, cta2_border: LINE2, cta2_size: 14,
  cta2_radius: R(4), cta2_radius_hover: R(4),
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: PANEL },
  showcase_padding: 28, showcase_radius: R(16), showcase_radius_hover: R(16),
  showcase_badge_text: 'GEAR HIGHLIGHTS',
  showcase_badge_dot: TAN, showcase_badge_bg: INK, showcase_badge_color: CREAM,
  showcase_items: [
    { number: 'Ridgeline 40L',  text: `€180`, italic: false, text_color: TAN,   bg: { type: 'solid', color: BG2 } },
    { number: 'Storm Shell',    text: `€240`, italic: false, text_color: CREAM, bg: { type: 'solid', color: BG2 } },
    { number: 'Scout Boot',     text: `€195`, italic: false, text_color: CREAM, bg: { type: 'solid', color: BG2 } },
    { number: 'Trail Flask',    text: `€38`,  italic: false, text_color: CREAM, bg: { type: 'solid', color: BG2 } },
  ],
  showcase_card_radius: R(10), showcase_card_radius_hover: R(10), showcase_card_shadow: 'none',
  showcase_caption_left: 'FIELD TESTED', showcase_caption_right: 'SS 2026',
  showcase_hover_effect: 'none',
  split_ratio: '1.25fr .75fr', gap: 52, min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
}) ]) ]) ]));

// ═══════════════════════════════════════════════════════════════════════════
// 2) GEAR RAIL — ProductRail non esiste → product-cards (5 prodotti con
//    badge, categoria-brand, nome, prezzo-come-brand, button CTA).
//    SEGNALATO: blueprint originale è drag-scroll orizzontale (ProductRail);
//    qui usiamo griglia product-cards 5 colonne — fedeltà best-effort.
// ═══════════════════════════════════════════════════════════════════════════
home.push(sec(BG, 'large', [
  row([ col('1-1', [ sheadLeft(
    `New & restocked`,
    `This season's `, `gear`,
    ''
  ) ]) ]),
  row([ col('1-1', [ tile('product-cards', {
    columns: 5,
    gap: 18,
    // Card style — sfondo PANEL, bordo LINE, radius 10
    card_bg:     { type: 'solid', color: PANEL },
    card_color:  DIM,
    card_radius: R(10), card_radius_hover: R(10), card_radius_hover_duration: 300,
    card_shadow: 'none',
    card_padding: 18,
    card_hover_effect: 'lift',
    // Metà alta — aspect ratio 4/3 (fc-prod__media)
    top_aspect_ratio: '4/3',
    top_padding: 0,
    // Lettera monogramma grande in TAN su sfondo PANEL (placeholder astratto)
    letter_font_family: 'serif',
    letter_size: 96,
    letter_italic: false,
    letter_align: 'center',
    show_screenshot_label: true,
    screenshot_label_color: DIM,
    // Titolo in Oswald/serif, peso 600
    title_font_family: 'serif',
    title_size: 22,
    title_weight: '600',
    brand_size: 11,
    brand_letter_spacing: 0.06,
    description_size: 13,
    cta_size: 12,
    cta_arrow: true,
    items: [
      {
        letter: 'P',          letter_color: TAN,
        top_bg: { type: 'solid', color: PANEL2 },
        screenshot_label: 'PACKS',
        brand_label: 'New',   brand_color: TAN,
        show_badge: true, badge_text: 'New', badge_bg: INK, badge_color: TAN,
        title: 'Ridgeline 40L', title_accent: '', title_accent_italic: false,
        description: `40L trail pack — ventilated back system. Lifetime guarantee.`,
        cta_text: `€180`, cta_url: '#shop',
      },
      {
        letter: 'S',          letter_color: CREAM,
        top_bg: { type: 'solid', color: PANEL2 },
        screenshot_label: 'SHELLS',
        brand_label: 'Shells', brand_color: TXT,
        show_badge: false, badge_text: '', badge_bg: INK, badge_color: CREAM,
        title: 'Storm Shell',   title_accent: '', title_accent_italic: false,
        description: `Hardshell — waterproof, breathable, seam-sealed for any condition.`,
        cta_text: `€240`, cta_url: '#shop',
      },
      {
        letter: 'B',          letter_color: TAN,
        top_bg: { type: 'solid', color: PANEL2 },
        screenshot_label: 'FOOTWEAR',
        brand_label: 'Restocked', brand_color: TAN,
        show_badge: true, badge_text: 'Restocked', badge_bg: INK, badge_color: TAN,
        title: 'Scout Boot',    title_accent: '', title_accent_italic: false,
        description: `Waterproof leather boot — resoleable, field-repairable. Built to last.`,
        cta_text: `€195`, cta_url: '#shop',
      },
      {
        letter: 'F',          letter_color: CREAM,
        top_bg: { type: 'solid', color: PANEL2 },
        screenshot_label: 'TOOLS',
        brand_label: 'Tools', brand_color: TXT,
        show_badge: false, badge_text: '', badge_bg: INK, badge_color: CREAM,
        title: 'Trail Flask',   title_accent: '', title_accent_italic: false,
        description: `Insulated flask — 750ml. Keeps cold 24h, hot 12h. Zero plastic.`,
        cta_text: `€38`,  cta_url: '#shop',
      },
      {
        letter: 'L',          letter_color: CREAM,
        top_bg: { type: 'solid', color: PANEL2 },
        screenshot_label: 'LAYERS',
        brand_label: 'Layers', brand_color: TXT,
        show_badge: false, badge_text: '', badge_bg: INK, badge_color: CREAM,
        title: 'Camp Fleece',   title_accent: '', title_accent_italic: false,
        description: `100% recycled fleece — camp-weight mid-layer. Packable to fist-size.`,
        cta_text: `€120`, cta_url: '#shop',
      },
    ],
  }) ]) ]),
]));

// ═══════════════════════════════════════════════════════════════════════════
// 3) SHOP THE KIT — tile Hotspots (marker su pannello astratto)
//    Blueprint: 4 data-dot su .fc-kit media (flat-lay pack/shell/boots/flask)
//    Posizioni: left/top % estratti da data-dot style
// ═══════════════════════════════════════════════════════════════════════════
home.push(sec(BG2, 'large', [
  row([ col('1-1', [ tile('hotspots', {
    eyebrow:     'Shop the kit',
    heading:     `Everything for a weekend out`,
    intro:       `Hover the markers to shop each piece of the setup.`,
    panel_label: 'FLAT-LAY GEAR',
    aspect_ratio: '16/10',
    items: [
      { x: 28, y: 40, title: 'Ridgeline 40L', text: '40L trail pack — ventilated back system. Lifetime guarantee.', meta: `€180` },
      { x: 52, y: 30, title: 'Storm Shell',   text: 'Hardshell — waterproof, breathable, seam-sealed for any condition.', meta: `€240` },
      { x: 68, y: 62, title: 'Scout Boots',   text: 'Waterproof leather boot — resoleable, field-repairable. Built to last.', meta: `€195` },
      { x: 40, y: 72, title: 'Trail Flask',   text: 'Insulated flask — 750ml. Keeps cold 24h, hot 12h. Zero plastic.', meta: `€38` },
    ],
    zone_accent: TAN,
    zone_on:     INK,
    panel_bg:    PANEL2,
    card_bg:     PANEL,
    card_border: LINE,
    align:       'left',
  }) ]) ]),
]));

// ═══════════════════════════════════════════════════════════════════════════
// 4) CATEGORIES — CategoryTiles non esiste → info-cards 4 col, no description
//    (blueprint: h3 + count-prodotti solo, overlay su foto, bordo LINE, radius 10)
// ═══════════════════════════════════════════════════════════════════════════
home.push(sec(BG, 'large', [
  row([ col('1-1', [ shead('By category', `Find your `, `gear`, '') ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0, container_gap: 14, columns: 4, items_gap: 14,
    card_bg:     { type: 'solid', color: PANEL },
    card_color:  TAN, card_radius: R(10), card_padding: 24,
    show_icon: true, show_counter: false, show_arrow: false, show_footer: true, show_media: false,
    icon_color: TAN, icon_bg_color: VTINT,
    title_color: CREAM,
    title_font_family: 'serif', title_size: 22, title_weight: '600', title_italic: false,
    description_size: 13,
    footer_size: 12,
    items: [
      { icon: 'mountain', title: 'Packs',    description: '', footer_text: '14 products', footer_dot_color: TAN   },
      { icon: 'cloud',    title: 'Shells',   description: '', footer_text: '9 products',  footer_dot_color: CREAM },
      { icon: 'compass',  title: 'Footwear', description: '', footer_text: '11 products', footer_dot_color: CREAM },
      { icon: 'zap',      title: 'Tools',    description: '', footer_text: '22 products', footer_dot_color: TAN   },
    ],
    card_hover_effect: 'lift',
  }) ]) ]),
]));

// ═══════════════════════════════════════════════════════════════════════════
// 5) STAT STRIP — fc-stats: bg INK, bordo top/bot LINE, 4 stat
//    (blueprint: .fc-stats b .u → suffisso in TAN, numero in CREAM, label in DIM)
// ═══════════════════════════════════════════════════════════════════════════
const stat = (number, suffix, label) => col('1-4', [ tile('counter', {
  number, suffix, prefix: '',
  label,
  icon_emoji: '',
  text_color: CREAM,
  number_color: CREAM,
  suffix_color: TAN,
  number_font_size: '52', number_font_weight: '600',
  label_color: DIM, label_font_size: '12',
  bg_type: 'color', bg_color: 'transparent', padding: '8', border_radius: '0',
}) ]);

home.push(sec(INK, 'small', [ row([
  stat('100', 'yr',  'Guarantee'),
  stat('40',  'k',   'Repairs done free'),
  stat('100', '%',   'Carbon-neutral ship'),
  stat('17',  '',    'Years in the field'),
], { gap: 24 }) ]));

// ═══════════════════════════════════════════════════════════════════════════
// 6) KIT BUILDER ZONE — tile Builder (stepper +/- interattivi, totale live)
//    zone_accent: #c2ab63 (TAN), zone_on: #161b11 (INK) — dal blueprint HTML.
//    card_bg: PANEL (dark card), card_border: LINE. Sfondo BG.
// ═══════════════════════════════════════════════════════════════════════════
home.push(sec(BG, 'large', [
  row([ col('1-1', [ tile('builder', {
    eyebrow:     'Pack list',
    heading:     `Kit your trip`,
    intro:       `Building out for a weekend in the hills? Add the essentials and we'll tally the haul — field-tested gear, nothing you won't use.`,
    currency:    `€`,
    cap:         0,
    total_label: 'Total',
    count_label: 'items',
    cta_text:    'Add to cart',
    cta_url:     '#kit',
    zone_accent: TAN,
    zone_on:     INK,
    card_bg:     PANEL,
    card_border: LINE2,
    align:       'left',
    items: [
      { name: '2-Person Tent',   price: '240', note: '3-season · 2.1kg',    start: 0 },
      { name: 'Down Bag',        price: '160', note: `Comfort -4°C`,         start: 0 },
      { name: '48L Pack',        price: '190', note: 'Ventilated back',       start: 0 },
      { name: 'Trail Boots',     price: '85',  note: 'Waterproof',            start: 0 },
      { name: 'Camp Stove',      price: '55',  note: `Folding · 110g`,        start: 0 },
      { name: 'Insulated Flask', price: '38',  note: '750ml',                 start: 0 },
    ],
  }) ]) ]),
]));

// ═══════════════════════════════════════════════════════════════════════════
// 7) CTA FINALE — 2 bottoni: "Shop the kit" (TAN) + "Our guarantee" (CREAM bg)
//    (blueprint: .fc-cta__cta ha btn--tan + btn--cream; CSS btn--cream = bg:CREAM, color:INK)
// ═══════════════════════════════════════════════════════════════════════════
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('cta-banner', {
  headline: `Gear that `, headline_accent: `outlasts you`, headline_accent_italic: true,
  subtitle: `Every piece is guaranteed for life and repaired free, forever. Buy less, buy once.`,
  cta_text: 'Shop the kit', cta_url: '#shop',
  cta2_text: 'Our guarantee', cta2_url: '#guarantee',
  cta2_bg: CREAM, cta2_color: INK, cta2_border: '',
  bg: { type: 'solid', color: PANEL2 },
  text_color: CREAM, accent_color: TAN, subtitle_color: TXT,
  cta_bg: TAN, cta_color: INK, cta_radius: R(4), cta_size: 14,
  headline_font_family: 'serif', headline_size: 56, headline_weight: '600',
  subtitle_size: 17,
  layout: 'stack', vertical_align: 'center', banner_radius: R(14), banner_padding: 80,
}) ]) ]) ]));

// ═══════════════════════════════════════════════════════════════════════════
K.emit({
  slug:  'fieldco',
  name:  'Field & Co',
  tags:  ['ecommerce', 'outdoor', 'apparel', 'shop'],
  description: `Field & Co — rugged outdoor e-commerce. Olive green + tan gold, Oswald (display) + Figtree. Tile-pure. Approssimazioni: ProductRail (drag-scroll) + CategoryTiles (overlay foto). Kit Builder: tile Builder nativo (stepper +/−, totale live). Hotspots: tile nativo 4 marker.`,
  colors: {
    primary:           TAN,
    primary_contrast:  INK,
    secondary:         RUST,
    secondary_contrast:CREAM,
    muted:             BG2,
    muted_contrast:    TXT,
    text:              TXT,
    text_muted:        DIM,
    background:        BG,
    border:            LINE,
    link:              TANL,
  },
  css_disp:            `"Oswald", sans-serif`,
  css_sans:            `"Figtree", -apple-system, sans-serif`,
  heading_weight:      '600',
  heading_line_height: '1.02',
  google_fonts:        ['Oswald', 'Figtree'],
  logo_variant:        'light',
  menu: [
    { title: 'Shop',       url: '#shop'      },
    { title: 'Build a kit',url: '#kit'       },
    { title: 'Categories', url: '#cats'      },
    { title: 'Guarantee',  url: '#guarantee' },
  ],
  header: {
    bg:         'rgba(35,42,28,.86)',
    text_color: TXT,
    sticky_bg:  'rgba(22,27,17,.9)',
    logo_width: 140,
  },
  footer: {
    bg:        BG2,
    headColor: CREAM,
    brand: {
      name:    'Field & Co',
      tagline: 'Outdoor goods built to last and guaranteed for life. Buy it once.',
    },
    columns: [
      { title: 'Shop',    links: ['All gear', 'Packs', 'Shells', 'Footwear'] },
      { title: 'Help',    links: ['Guarantee', 'Repairs', 'Shipping', 'Returns'] },
      { title: 'Company', links: ['Our story', 'Sustainability', 'Stockists', 'Contact'] },
    ],
    bottom: {
      left:  `© 2026 Field & Co — an OLOtheme demo.`,
      right: 'Built with OLObuild',
    },
  },
  cursor: { blend_mode: 'exclusion', ring_color: '#ffffff', dot_color: '#c2ab63' },
}, home);
