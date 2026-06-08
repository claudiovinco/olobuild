/* Kiln — ricomposizione TILE-PURE (image-free). Ceramic artist & studio.
   Light cream + terracotta. Petrona (display) + Mulish (body). */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('kl');

/* ── Palette dal :root di kiln.css ── */
const PAPER  = '#efe5da';
const PAPER2 = '#e5d8c9';
const CARD   = '#f7f0e7';
const INK    = '#2a211c';
const INK2   = '#473a31';
const TERRA  = '#bd6a40';
const TERRAD = '#a85932';
const CLAY   = '#9a7b5e';
const TXT    = '#5e5043';
const DIM    = '#928271';
const LINE   = '#dac9b6';
const LINE2  = '#c7b29a';
const WHITE  = '#ffffff';

/* ── Helper section-header centrato ── */
const shead = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: TERRA, eyebrow_dot_color: TERRA, eyebrow_separator: '',
  headline_lines: [ {text: l1, color: INK, italic: false}, {text: accent, color: TERRA, italic: true} ],
  headline_font_family: 'serif', headline_font_size: 46, headline_font_weight: '500', headline_align: 'center', headline_inline: true,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false, tagline_text_color: DIM, tagline_text_size: 16.5,
  layout: 'center', gap: 16,
});

const home = [];

/* ══════════════════════════════════════════════════
   1) HERO — hero-split
   (image-free: showcase con badge + 4 stat del pezzo)
   ══════════════════════════════════════════════════ */
home.push(sec(PAPER, 'large', [ row([ col('1-1', [ tile('hero-split', {
  eyebrow_text: 'Ceramic artist · studio of Mara Sole',
  eyebrow_dot_color: TERRA, eyebrow_color: TERRA,
  headline_lines: [
    {text: 'Made by', color: INK, italic: false},
    {text: `hand, ‘kept’`, color: INK, italic: false},
    {text: 'for years.', color: TERRA, italic: true},
  ],
  headline_font_family: 'serif', headline_font_size: 72, headline_line_height: 1.04,
  headline_font_weight: '500', headline_align: 'left',
  subhead: `Wheel-thrown, wood-fired vessels — each one a little different, none of them in a hurry. Functional pieces for slow mornings and long tables.`,
  subhead_color: TXT, subhead_size: 18, subhead_italic: false, subhead_max_width: 440,
  cta1_text: 'Shop the collection', cta1_url: '#collection',
  cta1_bg: TERRA, cta1_color: WHITE, cta1_size: 14, cta1_radius: R(999), cta1_radius_hover: R(999),
  cta2_text: 'Commission a piece', cta2_url: '#commission',
  cta2_bg: 'transparent', cta2_color: INK, cta2_border: LINE2, cta2_size: 14,
  cta2_radius: R(999), cta2_radius_hover: R(999),
  stats: [],
  showcase_enabled: true,
  showcase_bg: {type: 'solid', color: CARD},
  showcase_padding: 28, showcase_radius: R(180), showcase_radius_hover: R(180),
  showcase_badge_text: '1 OF 1 · EVERY PIECE', showcase_badge_dot: TERRA,
  showcase_badge_bg: PAPER2, showcase_badge_color: INK,
  showcase_items: [
    {number: 'Material', text: 'Stoneware', italic: false, text_color: INK, bg: {type: 'solid', color: PAPER2}},
    {number: 'Firing', text: 'Wood-fired', italic: false, text_color: INK, bg: {type: 'solid', color: PAPER2}},
    {number: 'Edition', text: 'One of one', italic: false, text_color: TERRA, bg: {type: 'solid', color: PAPER2}},
    {number: 'Glaze', text: 'Mixed in-house', italic: false, text_color: INK, bg: {type: 'solid', color: PAPER2}},
  ],
  showcase_card_radius: R(10), showcase_card_radius_hover: R(10), showcase_card_shadow: 'none',
  showcase_caption_left: 'KILN STUDIO', showcase_caption_right: 'MARA SOLE',
  showcase_hover_effect: 'none',
  split_ratio: '1.05fr .95fr', gap: 48, min_height: 0,
  tile_padding: {top: 0, right: 0, bottom: 0, left: 0},
}) ]) ]) ]));

/* ══════════════════════════════════════════════════
   2) STUDIO / Hotspots — tile Hotspots (marker su pannello astratto)
   Blueprint: 3 data-dot su .media (aspect-ratio 16/9, scene studio)
   Posizioni estratte da data-dot style del blueprint HTML
   ══════════════════════════════════════════════════ */
home.push(sec(CARD, 'large', [
  row([ col('1-1', [ tile('hotspots', {
    eyebrow:     'Have a look around',
    heading:     `Inside the studio`,
    intro:       `Hover the marks to see where each piece comes to life.`,
    panel_label: 'STUDIO SCENE',
    aspect_ratio: '16/9',
    items: [
      { x: 22, y: 62, title: 'The wheel',     text: 'Where everything starts — thrown wet, by hand.',                              meta: '' },
      { x: 55, y: 38, title: 'Glaze shelf',   text: 'Forty tested glazes, mixed in-house from raw oxides.',                       meta: '' },
      { x: 80, y: 70, title: 'The wood kiln', text: `Fired over three days to 1300°C. The flame finishes the work.`,          meta: '' },
    ],
    zone_accent: TERRA,
    zone_on:     WHITE,
    panel_bg:    PAPER2,
    card_bg:     PAPER,
    card_border: LINE,
    align:       'left',
  }) ]) ]),
]));

/* ══════════════════════════════════════════════════
   3) COLLECTION — product-cards
   6 pezzi ceramici (Ash Bowl, Celadon Vase, Tea Cups, Serving Plate, Water Carafe, Footed Planter)
   Sfondo sezione: PAPER (bianco carta)
   ══════════════════════════════════════════════════ */
home.push(sec(PAPER, 'large', [
  row([ col('1-1', [ shead('Available now', `This firing’s `, 'pieces', '') ]) ]),
  row([ col('1-1', [ tile('product-cards', {
    columns: 3,
    gap: 24,
    card_bg: {type: 'solid', color: CARD},
    card_color: DIM,
    card_radius: R(10),
    card_radius_hover: R(10),
    card_radius_hover_duration: 300,
    card_shadow: 'sm',
    card_padding: 20,
    top_aspect_ratio: '1/1',
    top_padding: 20,
    letter_font_family: 'serif',
    letter_size: 120,
    letter_italic: true,
    letter_align: 'center',
    show_screenshot_label: true,
    screenshot_label_color: DIM,
    brand_size: 12,
    brand_letter_spacing: 0.06,
    title_font_family: 'serif',
    title_size: 22,
    title_weight: '500',
    description_size: 13,
    cta_size: 12,
    cta_arrow: true,
    card_hover_effect: 'lift',
    items: [
      {
        letter: 'A', letter_color: TERRA,
        top_bg: {type: 'gradient', gradient_from: PAPER, gradient_to: PAPER2, gradient_angle: 180},
        screenshot_label: 'Stoneware · 1/1',
        brand_label: 'ASH BOWL', brand_color: TERRA,
        show_badge: false, badge_text: '', badge_bg: INK, badge_color: WHITE,
        title: 'Ash Bowl', title_accent: '', title_accent_italic: false,
        description: 'Wheel-thrown stoneware with natural ash glaze from the firing.',
        cta_text: '€90', cta_url: '#',
      },
      {
        letter: 'C', letter_color: CLAY,
        top_bg: {type: 'gradient', gradient_from: PAPER, gradient_to: PAPER2, gradient_angle: 180},
        screenshot_label: 'Porcelain · 1/1',
        brand_label: 'CELADON VASE', brand_color: CLAY,
        show_badge: false, badge_text: '', badge_bg: INK, badge_color: WHITE,
        title: 'Celadon Vase', title_accent: '', title_accent_italic: false,
        description: 'Tall form in translucent porcelain with pale jade celadon glaze.',
        cta_text: '€180', cta_url: '#',
      },
      {
        letter: 'T', letter_color: INK2,
        top_bg: {type: 'gradient', gradient_from: PAPER, gradient_to: PAPER2, gradient_angle: 180},
        screenshot_label: 'Wood-fired',
        brand_label: 'TEA CUPS, PAIR', brand_color: INK2,
        show_badge: false, badge_text: '', badge_bg: INK, badge_color: WHITE,
        title: 'Tea Cups, pair', title_accent: '', title_accent_italic: false,
        description: 'Two cups that belong together, different enough to tell apart.',
        cta_text: '€64', cta_url: '#',
      },
      {
        letter: 'S', letter_color: CLAY,
        top_bg: {type: 'gradient', gradient_from: PAPER, gradient_to: PAPER2, gradient_angle: 180},
        screenshot_label: 'Stoneware · 1/1',
        brand_label: 'SERVING PLATE', brand_color: CLAY,
        show_badge: true, badge_text: 'SOLD', badge_bg: CLAY, badge_color: WHITE,
        title: 'Serving Plate', title_accent: '', title_accent_italic: false,
        description: 'Wide flat form for slow meals. This one found its home already.',
        cta_text: 'Sold', cta_url: '#',
      },
      {
        letter: 'W', letter_color: TERRA,
        top_bg: {type: 'gradient', gradient_from: PAPER, gradient_to: PAPER2, gradient_angle: 180},
        screenshot_label: 'Wood-fired · 1/1',
        brand_label: 'WATER CARAFE', brand_color: TERRA,
        show_badge: false, badge_text: '', badge_bg: INK, badge_color: WHITE,
        title: 'Water Carafe', title_accent: '', title_accent_italic: false,
        description: `Tall, narrow, balanced in the hand. A morning ritual object.`,
        cta_text: '€120', cta_url: '#',
      },
      {
        letter: 'F', letter_color: INK2,
        top_bg: {type: 'gradient', gradient_from: PAPER, gradient_to: PAPER2, gradient_angle: 180},
        screenshot_label: 'Stoneware',
        brand_label: 'FOOTED PLANTER', brand_color: INK2,
        show_badge: false, badge_text: '', badge_bg: INK, badge_color: WHITE,
        title: 'Footed Planter', title_accent: '', title_accent_italic: false,
        description: 'A planter that deserves a good plant. Drainage hole included.',
        cta_text: '€78', cta_url: '#',
      },
    ],
  }) ]) ]),
]));

/* ══════════════════════════════════════════════════
   4) PROCESS STEPS — process-steps con numeri romani
   .kl-step: text-align:center, padding:0 12px (no card esterna)
   .kl-step__n: cerchio bordato 54px, border:1px solid LINE2, numero TERRA
   4 colonne, sfondo CARD
   ══════════════════════════════════════════════════ */
home.push(sec(CARD, 'large', [
  row([ col('1-1', [ shead('From clay to keep', 'How a piece is ', 'born', '') ]) ]),
  row([ col('1-1', [ tile('process-steps', {
    columns: 4,
    gap: 16,
    align: 'center',
    auto_number: false,
    item_gap: 10,
    number_style: 'outline',
    number_color: TERRA,
    number_bg: LINE2,
    number_size: 22,
    number_font: 'serif',
    number_weight: '500',
    title_color: INK,
    title_size: 20,
    title_font: 'serif',
    title_weight: '500',
    desc_color: DIM,
    desc_size: 13,
    card_bg: '',
    card_border: '',
    card_padding: 0,
    items: [
      {number: 'I',   title: 'Throw',      description: 'Wet clay, the wheel, and a shape that finds itself.'},
      {number: 'II',  title: `Trim & dry`, description: 'Footed, refined, then left to dry slowly for a week.'},
      {number: 'III', title: 'Glaze',      description: 'Dipped in glazes mixed from raw oxides, by eye.'},
      {number: 'IV',  title: 'Fire',       description: 'Three days of wood flame. The kiln has the final say.'},
    ],
  }) ]) ]),
]));

/* ══════════════════════════════════════════════════
   5) GALLERY / Favourites (ProductGallery non esiste — best-effort)
   Approssimato con section-header + counter row (dati del processo)
   ══════════════════════════════════════════════════ */
home.push(sec(PAPER, 'large', [
  row([ col('1-1', [ shead('From the shelf', 'A few ', 'favourites', '') ]) ]),
  row([ col('1-4', [ tile('counter', {
    number: '3', suffix: ' days', prefix: '', label: 'in the kiln', icon_emoji: '',
    text_color: INK, number_color: TERRA, number_font_size: '48', number_font_weight: '500',
    label_color: DIM, label_font_size: '13', bg_type: 'color', bg_color: 'transparent', padding: '8', border_radius: '0',
  }) ]),
  col('1-4', [ tile('counter', {
    number: '40', suffix: '+', prefix: '', label: 'glazes tested', icon_emoji: '',
    text_color: INK, number_color: TERRA, number_font_size: '48', number_font_weight: '500',
    label_color: DIM, label_font_size: '13', bg_type: 'color', bg_color: 'transparent', padding: '8', border_radius: '0',
  }) ]),
  col('1-4', [ tile('counter', {
    number: '1300', suffix: '°C', prefix: '', label: 'peak temperature', icon_emoji: '',
    text_color: INK, number_color: TERRA, number_font_size: '48', number_font_weight: '500',
    label_color: DIM, label_font_size: '13', bg_type: 'color', bg_color: 'transparent', padding: '8', border_radius: '0',
  }) ]),
  col('1-4', [ tile('counter', {
    number: '1', suffix: '/1', prefix: '', label: 'every piece made', icon_emoji: '',
    text_color: INK, number_color: TERRA, number_font_size: '48', number_font_weight: '500',
    label_color: DIM, label_font_size: '13', bg_type: 'color', bg_color: 'transparent', padding: '8', border_radius: '0',
  }) ]) ], {gap: 24}),
]));

/* ══════════════════════════════════════════════════
   6) GLAZE STUDIO / Mixer
   ══════════════════════════════════════════════════ */
home.push(sec(CARD, 'large', [
  row([ col('1-1', [ tile('mixer', {
    eyebrow: 'Glaze studio',
    heading: 'Blend a glaze',
    intro: `Every firing starts here. Tap two or three glazes and watch them melt together — the way they pool and run against the clay in the kiln.`,
    max: 3,
    empty_label: 'Tap a glaze to begin',
    zone_accent: '#c2724a',
    zone_on: '#fff',
    card_bg: PAPER,
    card_border: LINE,
    align: 'left',
    items: [
      {name: 'Celadon',   color: '#7f9c86'},
      {name: 'Ash',       color: '#b9b3a1'},
      {name: 'Tenmoku',   color: '#3a2a22'},
      {name: 'Oatmeal',   color: '#e0d2b6'},
      {name: 'Persimmon', color: '#c2724a'},
      {name: 'Cobalt',    color: '#365e87'},
    ],
  }) ]) ]),
]));

/* ══════════════════════════════════════════════════
   7) STUDIO ABOUT / story (from kl-story pattern)
   section-header + testimonial (citazione firma)
   ══════════════════════════════════════════════════ */
home.push(sec(PAPER, 'large', [
  row([ col('1-1', [ shead('The studio', 'About the ', 'maker',
    `Mara Sole has been throwing pots in Lisbon since 2014. The studio is small, the kiln is large, and every piece goes through the same four steps.`) ]) ]),
  row([ col('1-1', [ tile('testimonial', {
    quote: `“I’m not making pottery at scale. I’m making things I would want to own — objects that hold heat, hold light, and hold up to twenty years of slow mornings.”`,
    author_name: 'Mara Sole', author_role: `Maker & studio founder`, rating: '0',
    layout: 'single', show_line: false,
    bg_color: 'transparent', text_color: INK, border_radius: '0', avatar: '',
  }) ]) ]),
]));

/* ══════════════════════════════════════════════════
   8) CTA COMMISSION — cta-banner (1 pulsante, come nel blueprint)
   ══════════════════════════════════════════════════ */
home.push(sec(INK, 'large', [ row([ col('1-1', [ tile('cta-banner', {
  headline: 'Commission', headline_accent: 'something yours.', headline_accent_italic: true,
  subtitle: `Wedding registries, restaurant tableware or a single special vessel — tell me what you have in mind.`,
  cta_text: 'Start a commission', cta_url: '#',
  bg: {type: 'solid', color: INK}, text_color: PAPER, accent_color: TERRA, subtitle_color: '#cdbfad',
  cta_bg: TERRA, cta_color: WHITE, cta_radius: R(999), cta_size: 14,
  headline_font_family: 'serif', headline_size: 56, headline_weight: '500', subtitle_size: 17,
  layout: 'stack', vertical_align: 'center', banner_radius: R(0), banner_padding: 80,
}) ]) ]) ]));

/* ══════════════════════════════════════════════════
   EMIT
   ══════════════════════════════════════════════════ */
K.emit({
  slug: 'kiln', name: 'Kiln',
  tags: ['artist', 'ceramics', 'shop', 'maker', 'creative'],
  description: `Kiln — ceramic artist & studio of Mara Sole. Cream + terracotta, Petrona (display) + Mulish (body). Maker portfolio + shop tile-pure, image-free. Hotspots: tile nativo 3 marker (studio scene).`,
  colors: {
    primary: TERRA, primary_contrast: WHITE,
    secondary: CLAY, secondary_contrast: WHITE,
    muted: CARD, muted_contrast: TXT,
    text: TXT, text_muted: DIM,
    background: PAPER, border: LINE, link: TERRA,
  },
  css_disp: `"Petrona", Georgia, serif`,
  css_sans: `"Mulish", -apple-system, sans-serif`,
  heading_weight: '500', heading_line_height: '1.1',
  google_fonts: ['Petrona', 'Mulish'],
  logo_variant: 'dark',
  menu: [
    {title: 'The collection', url: '#collection'},
    {title: 'The studio',     url: '#studio'},
    {title: 'Process',        url: '#process'},
    {title: 'Commission',     url: '#commission'},
  ],
  header: { bg: `rgba(239,229,218,.9)`, text_color: TXT, sticky_bg: `rgba(239,229,218,.95)`, logo_width: 130 },
  footer: {
    bg: PAPER2, headColor: INK,
    brand: { name: 'Kiln', tagline: `The studio of ceramic artist Mara Sole. Wheel-thrown, wood-fired, one at a time.` },
    columns: [
      {title: 'Shop',    links: ['The collection', 'Commissions', 'Gift cards']},
      {title: 'Studio',  links: ['The studio', 'Process', 'Workshops']},
      {title: 'Contact', links: ['Studio 6, The Old Pottery', 'mara@kiln.studio', '@kiln.studio']},
    ],
    bottom: { left: `© 2026 Kiln — an OLOtheme demo.`, right: 'Built with OLObuild' },
  },
  cursor: false,
}, home);
