/* Verde — ricomposizione TILE-PURE (image-free). Food & Drink (plant-based bistro). Forest + lime. */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('vd');

/* Palette dal :root del CSS */
const BG    = '#15281c';
const BG2   = '#192f22';
const PANEL = '#1f3a2a';
const PANEL2= '#274733';
const INK   = '#0c1a12';
const LIME  = '#9bd35a';
const LIMED = '#86c043';
const CREAM = '#eef3e4';
const TXT   = '#aac0a4';
const DIM   = '#71886c';
const LINE  = 'rgba(238,243,228,.13)';
const LINE2 = 'rgba(155,211,90,.42)';
const WHITE = '#ffffff';

const home = [];

/* ─── helper: section-header centrato standard ─── */
const shead = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: !!eyebrow,
  eyebrow_text: eyebrow || '',
  eyebrow_color: LIME,
  eyebrow_dot_color: LIME,
  eyebrow_separator: '',
  headline_lines: [
    { text: l1, color: CREAM, italic: false },
    { text: accent, color: LIME, italic: true },
  ],
  headline_font_family: 'sans-serif',
  headline_font_size: 54,
  headline_font_weight: '700',
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

/* ─── helper: section-header left ─── */
const sheadLeft = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: !!eyebrow,
  eyebrow_text: eyebrow || '',
  eyebrow_color: LIME,
  eyebrow_dot_color: LIME,
  eyebrow_separator: '',
  headline_lines: [
    { text: l1, color: CREAM, italic: false },
    { text: accent, color: LIME, italic: true },
  ],
  headline_font_family: 'sans-serif',
  headline_font_size: 50,
  headline_font_weight: '700',
  headline_align: 'left',
  headline_inline: true,
  tagline_show: !!intro,
  tagline_text: intro || '',
  tagline_text_italic: false,
  tagline_text_color: TXT,
  tagline_text_size: 16,
  layout: 'stack',
  gap: 12,
});

/* ═══════════════════════════════════════════════════════════════
   1) HERO SPLIT
   blueprint: "Vegetables, taken seriously."
   2 CTA: btn--lime "Book a table" + btn--out "See the menu"
   Tag astratto ".ve-hero__tag": "Seasonal / changes weekly"
   ═══════════════════════════════════════════════════════════════ */
home.push(sec(BG, 'large', [row([col('1-1', [tile('hero-split', {
  eyebrow_text: 'Plant-based bistro',
  eyebrow_dot_color: LIME,
  eyebrow_color: LIME,
  headline_lines: [
    { text: 'Vegetables,', color: CREAM, italic: false },
    { text: 'taken', color: CREAM, italic: false },
    { text: 'seriously.', color: LIME, italic: true },
  ],
  headline_font_family: 'sans-serif',
  headline_font_size: 80,
  headline_line_height: 0.92,
  headline_font_weight: '800',
  headline_align: 'left',
  subhead: `A short, seasonal menu that puts plants at the centre — cooked with the ambition you'd expect from any great kitchen. No imitation, just flavour.`,
  subhead_color: TXT,
  subhead_size: 18,
  subhead_italic: false,
  subhead_max_width: 420,
  cta1_text: 'Book a table',
  cta1_url: '#book',
  cta1_bg: LIME,
  cta1_color: INK,
  cta1_size: 15,
  cta1_radius: R(999),
  cta1_radius_hover: R(999),
  cta2_text: 'See the menu',
  cta2_url: '#menu',
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
  showcase_radius: R(24),
  showcase_radius_hover: R(24),
  /* Tag ".ve-hero__tag": "Seasonal / changes weekly" — badge del panel */
  showcase_badge_text: 'SEASONAL · CHANGES WEEKLY',
  showcase_badge_dot: LIME,
  showcase_badge_bg: INK,
  showcase_badge_color: CREAM,
  showcase_items: [
    { number: 'Kitchen philosophy', text: '100% plant-based', italic: false, text_color: LIME, bg: { type: 'solid', color: BG2 } },
    { number: 'Sourcing radius', text: 'Within 50 miles', italic: false, text_color: CREAM, bg: { type: 'solid', color: BG2 } },
    { number: 'Menu', text: 'Changes weekly', italic: false, text_color: CREAM, bg: { type: 'solid', color: BG2 } },
    { number: 'Made in-house', text: 'Everything', italic: false, text_color: CREAM, bg: { type: 'solid', color: BG2 } },
  ],
  showcase_card_radius: R(12),
  showcase_card_radius_hover: R(12),
  showcase_card_shadow: 'none',
  showcase_caption_left: 'VERDE',
  showcase_caption_right: 'BISTRO',
  showcase_hover_effect: 'none',
  split_ratio: '1.05fr .95fr',
  gap: 48,
  min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
})])])]));

/* ═══════════════════════════════════════════════════════════════
   2) ANNOUNCEMENT STRIP (.ve-ann)
   blueprint: sfondo lime, 1 riga: "100% plant-based · Everything made in-house · Bookings recommended for dinner"
   CSS: padding 10px 20px, font-size 12px, font-weight 600, letter-spacing .04em
   → trust-strip: 3 voci esatte dal blueprint, niente extra
   ═══════════════════════════════════════════════════════════════ */
home.push(sec(LIME, 'small', [row([col('1-1', [tile('trust-strip', {
  items: [
    { text: '100% plant-based' },
    { text: 'Everything made in-house' },
    { text: 'Bookings recommended for dinner' },
  ],
  variant: 'pill',
  separator_char: '·',
  align: 'center',
  flow: 'wrap',
  gap: 20,
  font_family: 'sans-serif',
  text_color: INK,
  text_size: 12,
  pill_bg: 'rgba(12,26,18,.10)',
  pill_border: 'rgba(12,26,18,.18)',
  pill_text_color: INK,
})])])]));

/* ═══════════════════════════════════════════════════════════════
   3) MENU LIST ("On the plate")
   blueprint: 2 col con h3 titolo categoria + 3 piatti ciascuna
   CSS: .ve-menu__col h3 { color:var(--lime); font-size:26px; border-bottom:2px solid var(--line) }
        .ve-dish__nm { font-size:24px; color:var(--cream) }
        .ve-dish__pr { font-size:24px; color:var(--lime) }
        .ve-dish { border-bottom:1px dashed var(--line) }
        .ve-dish__lead { border-bottom:1.5px dotted var(--line-2) }
   → pricelist ×2 (col 1-2 ciascuna). Heading row usa _heading:true per h3 categoria.
   ═══════════════════════════════════════════════════════════════ */
home.push(sec(BG, 'large', [
  row([col('1-1', [shead('This week', 'On the ', 'plate', null)])]),
  row([
    col('1-2', [tile('pricelist', {
      show_image: false,
      price_position: 'right',
      separator_style: 'dotted',
      separator_color: LINE2,
      title_color: CREAM,
      price_color: LIME,
      description_color: DIM,
      gap: '8',
      card_bg: '',
      card_border_radius: '0',
      highlighted_bg: 'rgba(155,211,90,.08)',
      badge_bg: LIME,
      badge_color: INK,
      badge_border_radius: '999',
      tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
      items: [
        { id: 'vd-1', title: 'To start', description: '', price: '', highlighted: false, badge: '', _heading: true },
        { id: 'vd-2', title: 'Charred leeks, hazelnut', description: 'Romesco, brown butter (vegan), crisp shallot.', price: '€11', highlighted: false, badge: '' },
        { id: 'vd-3', title: 'Heritage tomato', description: 'Basil oil, sourdough crumb, aged balsamic.', price: '€12', highlighted: false, badge: 'GF' },
        { id: 'vd-4', title: 'Smoked beetroot', description: 'Cashew cream, dill, rye.', price: '€10', highlighted: false, badge: '' },
      ],
    })]),
    col('1-2', [tile('pricelist', {
      show_image: false,
      price_position: 'right',
      separator_style: 'dotted',
      separator_color: LINE2,
      title_color: CREAM,
      price_color: LIME,
      description_color: DIM,
      gap: '8',
      card_bg: '',
      card_border_radius: '0',
      highlighted_bg: 'rgba(155,211,90,.08)',
      badge_bg: LIME,
      badge_color: INK,
      badge_border_radius: '999',
      tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
      items: [
        { id: 'vd-5', title: 'Mains', description: '', price: '', highlighted: false, badge: '', _heading: true },
        { id: 'vd-6', title: 'King oyster "scallops"', description: `Pea purée, capers, brown butter.`, price: '€19', highlighted: false, badge: '' },
        { id: 'vd-7', title: 'Celeriac shawarma', description: 'Flatbread, pickles, toum, harissa.', price: '€17', highlighted: false, badge: 'GF' },
        { id: 'vd-8', title: 'Wild mushroom risotto', description: 'Carnaroli, thyme, truffle.', price: '€18', highlighted: false, badge: '' },
      ],
    })]),
  ], { gap: 40 }),
]));

/* ═══════════════════════════════════════════════════════════════
   4) SOURCING SPLIT (.ve-story — "Picked nearby, cooked today")
   blueprint: layout 2-col (media left + testo right) con 2 paragrafi
   CSS: .ve-story__media .media { aspect-ratio:4/5; border-radius:24px }
        .ve-story h2 { font-size clamp(36,5vw,64px) }
        sfondo: .panel = var(--bg-2)
   → hero-split semplificato: showcase come pannello astratto (image-free),
     NO 4 items di dati (non nel blueprint). Layout: media a sinistra.
   ═══════════════════════════════════════════════════════════════ */
home.push(sec(BG2, 'large', [row([col('1-1', [tile('hero-split', {
  eyebrow_text: 'Our sourcing',
  eyebrow_dot_color: LIME,
  eyebrow_color: LIME,
  headline_lines: [
    { text: 'Picked', color: CREAM, italic: false },
    { text: 'nearby,', color: LIME, italic: true },
    { text: 'cooked today', color: CREAM, italic: false },
  ],
  headline_font_family: 'sans-serif',
  headline_font_size: 52,
  headline_line_height: 1.0,
  headline_font_weight: '700',
  headline_align: 'left',
  /* blueprint: 2 paragrafi separati — uniti nel subhead */
  subhead: `We buy from growers within fifty miles and let the harvest write the menu. If it’s not in season, it’s not on the plate.\n\nRoots, leaves, pulses and grains — treated with the same care a great kitchen gives anything. Turns out that’s the whole secret.`,
  subhead_color: TXT,
  subhead_size: 16,
  subhead_italic: false,
  subhead_max_width: 540,
  cta1_text: 'Meet our growers',
  cta1_url: '#sourcing',
  cta1_bg: 'transparent',
  cta1_color: CREAM,
  cta1_border: LINE2,
  cta1_size: 12,
  cta1_radius: R(0),
  cta1_radius_hover: R(0),
  stats: [],
  /* panel astratto 4:5 — image-free placeholder per la media */
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: PANEL2 },
  showcase_padding: 32,
  showcase_radius: R(24),
  showcase_radius_hover: R(24),
  showcase_badge_text: 'SEASONAL GROWERS NETWORK',
  showcase_badge_dot: LIME,
  showcase_badge_bg: INK,
  showcase_badge_color: CREAM,
  showcase_items: [],
  showcase_card_radius: R(0),
  showcase_card_radius_hover: R(0),
  showcase_card_shadow: 'none',
  showcase_caption_left: 'LOCAL',
  showcase_caption_right: '50 MILES',
  showcase_hover_effect: 'none',
  /* media a sinistra → inverti split_ratio rispetto all'hero */
  split_ratio: '.9fr 1.1fr',
  gap: 56,
  min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
})])])]));

/* ═══════════════════════════════════════════════════════════════
   5) GALLERY / THE ROOM ("Come sit a while")
   blueprint: griglia 4 col, 6 celle (tall + wide + standard), immagini placeholder media
   CSS: .ve-gal { grid-template-columns:repeat(4,1fr); grid-auto-rows:180px; gap:12px; border-radius:14px }
        .ve-gal__c.tall { grid-row:span 2 } .ve-gal__c.wide { grid-column:span 2 }
   → IMAGE-FREE: info-cards 3 col, card colore PANEL, 6 spazi come nel blueprint,
     icone Lucide tematiche restaurant, card_radius 14px come il CSS
   ═══════════════════════════════════════════════════════════════ */
home.push(sec(BG, 'large', [
  row([col('1-1', [shead('The room', 'Come ', 'sit a while', null)])]),
  row([col('1-1', [tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0,
    container_gap: 12,
    columns: 3,
    items_gap: 12,
    card_bg: { type: 'solid', color: PANEL },
    card_color: DIM,
    card_radius: R(14),
    card_padding: 30,
    show_icon: true,
    show_counter: false,
    show_arrow: false,
    show_footer: false,
    show_media: false,
    icon_color: LIME,
    icon_bg_color: 'rgba(155,211,90,.12)',
    title_color: CREAM,
    title_font_family: 'sans-serif',
    title_size: 20,
    title_weight: '700',
    title_italic: false,
    description_size: 14,
    card_hover_effect: 'lift',
    items: [
      { icon: 'leaf',         title: 'Dining room',    description: 'Warm light, green walls, plants everywhere. A room designed to make you linger.' },
      { icon: 'flame',        title: 'The kitchen',    description: 'Open kitchen where you can watch the brigade work — theatre and technique, every service.' },
      { icon: 'wine',         title: 'The bar',        description: 'Natural wines, low-intervention pours, and a few cocktails built around herbs from the garden.' },
      { icon: 'sun',          title: 'The terrace',    description: 'Fifty covers outside when the weather allows. Heated, covered, always booked first.' },
      { icon: 'users',        title: 'Private dining', description: 'The back room seats twelve for celebrations, tastings, or a meal for the whole team.' },
      { icon: 'cake',         title: 'Dessert counter', description: 'A dedicated pastry station — everything from cultured cream to roasted stone fruit.' },
    ],
  })])])
]));

/* ═══════════════════════════════════════════════════════════════
   6) HOURS STRIP ("Find us" — 4 slot orari)
   blueprint: HoursStrip — 4 col, struttura d/t/n (giorno / orario / nota)
   CSS: .ve-hours__in { background:var(--line); border:1px solid var(--line); border-radius:14px; overflow:hidden }
        .ve-hour { background:var(--bg-2); padding:24px }
        .ve-hour .d { color:var(--lime); font-size:11px; letter-spacing:.14em }
        .ve-hour .t { font-size:24px; color:var(--cream) }
        .ve-hour .n { font-size:12.5px; color:var(--txt-dim) }
   → counter per ogni slot: number=orario (t), label=giorno (d), icon_emoji=note (n)
     bg_color = BG2 (NON PANEL), border_radius '14', gap 1px (reso con gap:2)
   ═══════════════════════════════════════════════════════════════ */
const hourCard = (day, hours, note) => col('1-4', [tile('counter', {
  number: hours,
  suffix: '',
  prefix: '',
  label: day,
  sublabel: note,
  icon_emoji: '',
  text_color: DIM,
  number_color: CREAM,
  number_font_size: '24',
  number_font_weight: '700',
  label_color: LIME,
  label_font_size: '11',
  bg_type: 'color',
  bg_color: BG2,
  padding: '24',
  border_radius: '0',
})]);

home.push(sec(BG2, 'large', [
  row([col('1-1', [shead('When we’re open', 'Find ', 'us', null)])]),
  row([
    hourCard('Tue – Thu', '12 – 22', 'Lunch & dinner'),
    hourCard('Fri – Sat',  '12 – 23', 'Late kitchen'),
    hourCard('Sunday',         '11 – 16', 'Brunch'),
    hourCard('Monday',         'Closed',       'At the market'),
  ], { gap: 1 }),
]));

/* ═══════════════════════════════════════════════════════════════
   7) TESTIMONIAL
   blueprint: citazione centrata, font display (Darker Grotesque),
     <em> su "booked his birthday here."
   CSS: q { font-family:var(--disp); font-weight:600; font-size clamp(30,4.6vw,54px) }
        .ve-testi__by { color:var(--lime); font-size:12px; letter-spacing:.1em }
   Apostrofo con backtick. Author: "Nadia F." role: "regular" (dal blueprint .ve-testi__by "Nadia F. · regular")
   ═══════════════════════════════════════════════════════════════ */
home.push(sec(BG, 'large', [row([col('1-1', [tile('testimonial', {
  quote: `“I brought my most sceptical, steak-loving friend. He’s now booked his birthday here.”`,
  author_name: 'Nadia F.',
  author_role: 'regular',
  rating: '0',
  layout: 'single',
  show_line: false,
  bg_color: 'transparent',
  text_color: CREAM,
  border_radius: '0',
  avatar: '',
  author_position: 'bottom-center',
})])])]));

/* ═══════════════════════════════════════════════════════════════
   8) BUILD-A-BOWL
   blueprint: sezione #bowl data-builder — 6 item con stepper +/− e totale live.
   Tile reale: 'builder'. zone_accent=#9bd35a (--fx-zone-accent), zone_on=#0c1a12.
   card_bg=PANEL, card_border=LINE. cap=0 (no limite), currency=€.
   ═══════════════════════════════════════════════════════════════ */
home.push(sec(BG2, 'large', [
  row([col('1-1', [tile('builder', {
    eyebrow: 'Make it yours',
    heading: 'Build a bowl',
    intro: `Start with a base, pile on what you fancy. Tap to add — your bowl totals up as you go.`,
    currency: '€',
    cap: 0,
    total_label: 'Total',
    count_label: 'add-ons',
    cta_text: 'Add to order',
    cta_url: '#bowl',
    zone_accent: '#9bd35a',
    zone_on: '#0c1a12',
    card_bg: PANEL,
    card_border: LINE,
    align: 'left',
    items: [
      { name: 'Ancient grains',  price: '9',  note: 'Base',    start: 0 },
      { name: 'Leaves & herbs',  price: '9',  note: 'Base',    start: 0 },
      { name: 'Miso tempeh',     price: '4',  note: 'Protein', start: 0 },
      { name: 'Smoked chickpea', price: '4',  note: 'Protein', start: 0 },
      { name: 'Avocado',         price: '2',  note: 'Extra',   start: 0 },
      { name: 'Pickles & seeds', price: '2',  note: 'Extra',   start: 0 },
    ],
  })])])
]));

/* ═══════════════════════════════════════════════════════════════
   9) CTA FINALE ("Pull up a chair.")
   blueprint: sfondo lime, 1 solo bottone btn--cream (bg:var(--cream), color:var(--ink))
   CSS: .ve-cta h2 { color:var(--ink); font-size clamp(46,8vw,108px); line-height:.9 }
        .ve-cta h2 em { color:var(--bg) = #15281c }
        button: background:var(--cream); color:var(--ink)
   FIX: era cta_bg:INK / cta_color:CREAM → corretto a cta_bg:CREAM / cta_color:INK
   ═══════════════════════════════════════════════════════════════ */
home.push(sec(LIME, 'large', [row([col('1-1', [tile('cta-banner', {
  headline: 'Pull up a',
  headline_accent: 'chair.',
  headline_accent_italic: true,
  subtitle: `Dinner books up fast — reserve your table and let the kitchen surprise you.`,
  cta_text: 'Book a table',
  cta_url: '#book',
  bg: { type: 'solid', color: LIME },
  text_color: INK,
  accent_color: BG,
  subtitle_color: 'rgba(12,26,18,.78)',
  cta_bg: CREAM,
  cta_color: INK,
  cta_radius: R(999),
  cta_size: 15,
  headline_font_family: 'sans-serif',
  headline_size: 72,
  headline_weight: '800',
  subtitle_size: 17,
  layout: 'stack',
  vertical_align: 'center',
  banner_radius: R(0),
  banner_padding: 80,
})])])]));

/* ═══════════════════════════════════════════════════════════════
   K.emit — genera i 4 file JSON + loghi
   ═══════════════════════════════════════════════════════════════ */
K.emit({
  slug: 'verde',
  name: 'Verde',
  tags: ['food', 'restaurant', 'plant-based', 'bistro', 'healthy'],
  description: `Verde — plant-based bistro. Forest green + lime, Darker Grotesque (display) + Figtree (body). Tile-pure: hero, announcement strip (3 voci blueprint), menu pricelist (2 col, _heading), sourcing split, gallery info-cards (6 card icone Lucide), hours counter (BG2), testimonial, build-a-bowl (tile builder — 6 item stepper, zone_accent lime), CTA lime (btn--cream). Riproduzione fedele dell’OLOtheme Verde.`,
  colors: {
    primary:           LIME,
    primary_contrast:  INK,
    secondary:         CREAM,
    secondary_contrast: INK,
    muted:             BG2,
    muted_contrast:    TXT,
    text:              TXT,
    text_muted:        DIM,
    background:        BG,
    border:            LINE,
    link:              LIME,
  },
  css_disp:             `"Darker Grotesque", sans-serif`,
  css_sans:             `"Figtree", -apple-system, sans-serif`,
  heading_weight:       '700',
  heading_line_height:  '0.96',
  google_fonts:         ['Darker Grotesque', 'Figtree'],
  logo_variant:         'light',
  menu: [
    { title: 'Menu',     url: '#menu' },
    { title: 'Sourcing', url: '#sourcing' },
    { title: 'The room', url: '#gallery' },
    { title: 'Visit',    url: '#hours' },
  ],
  header: {
    bg:         BG,
    text_color: TXT,
    sticky_bg:  'rgba(21,40,28,.86)',
    logo_width: 130,
  },
  footer: {
    bg:        BG2,
    headColor: CREAM,
    brand: {
      name:    'Verde',
      tagline: 'Plant-based bistro. Vegetables first, cooked with ambition, sourced nearby.',
    },
    columns: [
      { title: 'Eat',   links: ['Menu', 'Drinks', 'Sourcing', 'Private dining'] },
      { title: 'About', links: ['Our story', 'Growers', 'Work with us'] },
      { title: 'Visit', links: ['30 Garden Walk', 'Tue–Sun · from 11', 'eat@verde.bistro'] },
    ],
    bottom: {
      left:  '© 2026 Verde — an OLOtheme demo.',
      right: 'Built with OLObuild',
    },
  },
  cursor: {
    blend_mode: 'exclusion',
    ring_color: LIME,
    dot_color:  LIME,
  },
}, home);
