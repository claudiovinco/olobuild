/* Pasaje -- ricomposizione TILE-PURE (image-free). Boutique hotel Travel. Terracotta + sabbia. */
/* Note approssimazioni: BookingBar (check-in/out/guests) -> info-cards 3 col (tile non esiste) */
/*                       Finder interattivo (opt chip + result card) -> tile finder ✓ */
/*                       Gallery (ProductGallery) -> section-header + trust-strip astratta */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('pj');

const BG    = '#2a1a14';
const BG2   = '#311f17';
const PANEL = '#3a261c';
const PANEL2= '#472f22';
const INK   = '#1b110b';
const TERRA = '#d98a4e';
const TERAL = '#e6a06a';
const SAND  = '#e4cdab';
const CREAM = '#f3e7d6';
const TXT   = '#c5ac96';
const DIM   = '#967861';
const LINE  = 'rgba(243,231,214,.13)';
const LINE2 = 'rgba(217,138,78,.42)';
const TERAINT = 'rgba(217,138,78,.14)';
const WHITE = '#ffffff';

const home = [];

// --- helpers ---
const shead = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: TERRA, eyebrow_dot_color: TERRA, eyebrow_separator: '',
  headline_lines: [ {text: l1, color: CREAM, italic: false}, {text: accent, color: TERRA, italic: true} ],
  headline_font_family: 'serif', headline_font_size: 48, headline_font_weight: '400', headline_align: 'center', headline_inline: true,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false, tagline_text_color: DIM, tagline_text_size: 16.5,
  layout: 'center', gap: 16,
});

const sheadLeft = (eyebrow, l1, accent) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: TERRA, eyebrow_dot_color: TERRA, eyebrow_separator: '',
  headline_lines: [ {text: l1, color: CREAM, italic: false}, {text: accent, color: TERRA, italic: true} ],
  headline_font_family: 'serif', headline_font_size: 44, headline_font_weight: '400', headline_align: 'left', headline_inline: true,
  tagline_show: false, layout: 'stack', gap: 12,
});

// 1) HERO -- centrato, pannello astratto destro
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('hero-split', {
  eyebrow_text: 'Boutique hotel · the old town',
  eyebrow_dot_color: TERRA,
  eyebrow_color: TERRA,
  headline_lines: [
    {text: 'Stay down', color: WHITE, italic: false},
    {text: 'the passage.', color: CREAM, italic: false},
  ],
  headline_font_family: 'serif',
  headline_font_size: 82,
  headline_line_height: 1.02,
  headline_font_weight: '400',
  headline_align: 'left',
  subhead: 'Fourteen sun-warmed rooms around a courtyard kitchen, two minutes from the sea and a world away from the crowd.',
  subhead_color: TXT,
  subhead_size: 18,
  subhead_italic: false,
  subhead_max_width: 500,
  cta1_text: 'Book a room',
  cta1_url: '#book',
  cta1_bg: TERRA,
  cta1_color: INK,
  cta1_size: 13,
  cta1_radius: R(999),
  cta1_radius_hover: R(999),
  cta2_text: 'See the rooms',
  cta2_url: '#rooms',
  cta2_bg: 'transparent',
  cta2_color: CREAM,
  cta2_border: LINE2,
  cta2_size: 13,
  cta2_radius: R(999),
  cta2_radius_hover: R(999),
  stats: [],
  showcase_enabled: true,
  showcase_bg: {type: 'solid', color: PANEL},
  showcase_padding: 28,
  showcase_radius: R(14),
  showcase_radius_hover: R(14),
  showcase_badge_text: 'PASAJE · EST. 2019',
  showcase_badge_dot: TERRA,
  showcase_badge_bg: INK,
  showcase_badge_color: CREAM,
  showcase_items: [
    {number: 'Rooms', text: '14', italic: false, text_color: TERRA, bg: {type: 'solid', color: BG2}},
    {number: 'From the sea', text: '2 min', italic: false, text_color: CREAM, bg: {type: 'solid', color: BG2}},
    {number: 'Open since', text: '2019', italic: false, text_color: CREAM, bg: {type: 'solid', color: BG2}},
    {number: 'Check-in', text: '15:00', italic: false, text_color: CREAM, bg: {type: 'solid', color: BG2}},
  ],
  showcase_card_radius: R(10),
  showcase_card_radius_hover: R(10),
  showcase_card_shadow: 'none',
  showcase_caption_left: 'THE HOTEL',
  showcase_caption_right: 'OLD TOWN',
  showcase_hover_effect: 'none',
  split_ratio: '1.2fr .8fr',
  gap: 52,
  min_height: 0,
  tile_padding: {top: 0, right: 0, bottom: 0, left: 0},
}) ]) ]) ]));

// 2) BOOKING BAR -- approssimazione con info-cards 3 col (BookingBar tile non esiste)
// BEST-EFFORT SEGNALATA: floating booking bar (check-in/out/guests) overlapping hero
// Il tile BookingBar non esiste. Reso come 3 info-cards su sfondo crema.
home.push(sec(BG2, 'small', [
  row([ col('1-1', [ tile('info-cards', {
    container_bg: {type: 'solid', color: 'transparent'},
    container_padding: 0,
    container_gap: 12,
    columns: 3,
    items_gap: 12,
    card_bg: {type: 'solid', color: CREAM},
    card_color: DIM,
    card_radius: R(12),
    card_padding: 20,
    show_icon: true,
    show_counter: false,
    show_arrow: false,
    show_footer: true,
    show_media: false,
    icon_color: INK,
    icon_bg_color: 'transparent',
    title_color: INK,
    title_font_family: 'sans-serif',
    title_size: 14,
    title_weight: '700',
    title_italic: false,
    description_size: 13,
    items: [
      {icon: 'calendar', title: 'Check in', description: 'From 15:00', footer_text: '10 Jul 2026', footer_dot_color: TERRA},
      {icon: 'calendar', title: 'Check out', description: 'Until 11:00', footer_text: '14 Jul 2026', footer_dot_color: TERRA},
      {icon: 'users', title: 'Guests', description: '2 adults', footer_text: 'Check availability', footer_dot_color: TERRA},
    ],
    card_hover_effect: 'lift',
  }) ]) ]),
]));

// 3) THE STAY -- hero-split (testo sinistra, pannello astratto destra)
// CSS: .pa-stay = grid 1fr 1fr, gap 56px. Signature = font serif 22px color --cream
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('hero-split', {
  eyebrow_text: 'The stay',
  eyebrow_dot_color: TERRA,
  eyebrow_color: TERRA,
  headline_lines: [
    {text: 'A small hotel that', color: CREAM, italic: false},
    {text: "feels like a friend's house", color: CREAM, italic: false},
  ],
  headline_font_family: 'serif',
  headline_font_size: 46,
  headline_line_height: 1.1,
  headline_font_weight: '400',
  headline_align: 'left',
  subhead: "Pasaje sits in a restored merchant's house off a quiet passage. Fourteen rooms, no two alike, all lime-washed walls, local linen and shutters that throw stripes of afternoon light.\n\nBreakfast is whatever the market gave us, served in the courtyard. The roof is yours at sundown.\n\n— Inés & the Pasaje team",
  subhead_color: TXT,
  subhead_size: 16,
  subhead_italic: false,
  subhead_max_width: 480,
  cta1_text: 'Discover the hotel',
  cta1_url: '#stay',
  cta1_bg: TERRA,
  cta1_color: INK,
  cta1_size: 13,
  cta1_radius: R(999),
  cta1_radius_hover: R(999),
  cta2_text: '',
  cta2_url: '',
  cta2_bg: 'transparent',
  cta2_color: CREAM,
  cta2_border: 'transparent',
  cta2_size: 13,
  cta2_radius: R(999),
  cta2_radius_hover: R(999),
  stats: [],
  showcase_enabled: true,
  showcase_bg: {type: 'pattern', color: PANEL, pattern_type: 'diagonal-lines', pattern_color: 'rgba(243,231,214,.07)'},
  showcase_padding: 32,
  showcase_radius: R(10),
  showcase_radius_hover: R(10),
  showcase_badge_text: 'SINCE 2019',
  showcase_badge_dot: TERRA,
  showcase_badge_bg: INK,
  showcase_badge_color: CREAM,
  showcase_items: [
    {number: 'Rooms, no two alike', text: '14', italic: false, text_color: TERRA, bg: {type: 'solid', color: BG2}},
    {number: 'The courtyard kitchen', text: 'Open every morning', italic: true, text_color: CREAM, bg: {type: 'solid', color: BG2}},
  ],
  showcase_card_radius: R(8),
  showcase_card_radius_hover: R(8),
  showcase_card_shadow: 'none',
  showcase_caption_left: 'LINEN & LIGHT',
  showcase_caption_right: 'OLD TOWN',
  showcase_hover_effect: 'none',
  split_ratio: '1fr 1fr',
  gap: 56,
  min_height: 0,
  tile_padding: {top: 0, right: 0, bottom: 0, left: 0},
}) ]) ]) ]));

// 4) THE ROOMS (RoomGrid -> info-cards con footer prezzo)
// CSS: .pa-room { background:var(--panel); border:1px solid var(--line); border-radius:10px }
// .pa-room__b h3 { font-size:25px } -- .pa-room__pr = serif 22px terra
home.push(sec(BG2, 'large', [
  row([ col('1-1', [ shead("Where you'll sleep", 'The ', 'rooms', '') ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg: {type: 'solid', color: 'transparent'},
    container_padding: 0,
    container_gap: 18,
    columns: 3,
    items_gap: 18,
    card_bg: {type: 'solid', color: PANEL},
    card_color: DIM,
    card_radius: R(10),
    card_padding: 24,
    card_border_color: LINE,
    card_border_width: 1,
    show_icon: false,
    show_counter: true,
    show_counter_label: false,
    show_arrow: false,
    show_footer: true,
    show_media: false,
    counter_shape: 'plain',
    counter_color: TERRA,
    counter_size: 36,
    title_color: CREAM,
    title_font_family: 'serif',
    title_size: 25,
    title_weight: '400',
    title_italic: false,
    description_color: DIM,
    description_size: 13,
    items: [
      {
        counter: '22 m²',
        title: 'Courtyard',
        description: '22 m² · garden view · queen',
        footer_text: '€140 / night',
        footer_dot_color: TERRA,
      },
      {
        counter: '28 m²',
        title: 'Sea View',
        description: '28 m² · balcony · king',
        footer_text: '€190 / night',
        footer_dot_color: TERRA,
      },
      {
        counter: '40 m²',
        title: 'Roof Suite',
        description: '40 m² · terrace · king',
        footer_text: '€280 / night',
        footer_dot_color: TERRA,
      },
    ],
    card_hover_effect: 'lift',
  }) ]) ]),
]));

// 5) EXPERIENCES (CategoryTiles -> info-cards 4 col)
// CSS: .pa-expc = card overlay-image + titolo serif in basso, nessuna icona
// Tile-pure: info-cards senza icone, titolo serif
home.push(sec(BG, 'large', [
  row([ col('1-1', [ shead("While you're here", 'Slow days, ', 'good evenings', '') ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg: {type: 'solid', color: 'transparent'},
    container_padding: 0,
    container_gap: 14,
    columns: 4,
    items_gap: 14,
    card_bg: {type: 'solid', color: PANEL},
    card_color: DIM,
    card_radius: R(10),
    card_padding: 26,
    show_icon: false,
    show_counter: false,
    show_arrow: false,
    show_footer: false,
    show_media: false,
    title_color: CREAM,
    title_font_family: 'serif',
    title_size: 20,
    title_weight: '400',
    title_italic: false,
    description_color: DIM,
    description_size: 14,
    items: [
      {title: 'Courtyard breakfast', description: 'Whatever the market gave us, laid out in the courtyard every morning until ten.'},
      {title: 'Rooftop at sunset',   description: 'The roof is yours from six. Something cold, the whole city going gold.'},
      {title: 'Market mornings',     description: "We'll point you to the stalls worth your time and the one cheese you shouldn't miss."},
      {title: 'Boat to the cove',    description: 'A small boat, a quiet cove, a packed lunch. Ask at reception the night before.'},
    ],
    card_hover_effect: 'lift',
  }) ]) ]),
]));

// 6) GALLERY (ProductGallery -> section-header + trust-strip astratta con etichette dei frame)
// BEST-EFFORT SEGNALATA: la gallery masonry/grid non ha tile equivalente image-free
home.push(sec(BG2, 'large', [
  row([ col('1-1', [ shead('A look around', 'Pasaje, ', 'in frames', '') ]) ]),
  row([ col('1-1', [ tile('trust-strip', {
    items: [
      {text: 'The passage entrance'},
      {text: 'Room detail'},
      {text: 'Courtyard'},
      {text: 'Rooftop view'},
      {text: 'Breakfast table'},
      {text: 'Tiled bathroom'},
    ],
    variant: 'pill',
    separator_char: '',
    align: 'center',
    flow: 'wrap',
    gap: 12,
    font_family: 'sans-serif',
    text_color: DIM,
    text_size: 13,
    pill_bg: 'rgba(243,231,214,.06)',
    pill_border: LINE,
    pill_text_color: TXT,
  }) ]) ]),
]));

// 7) FINDER -- "Which room is you?" — tile finder
// Blueprint: --fx-zone-accent:#d98a4e; --fx-zone-on:#1b110b
home.push(sec(BG, 'large', [
  row([ col('1-1', [ tile('finder', {
    eyebrow: `Which room is you?`,
    heading: `Find your stay`,
    intro: ``,
    zone_accent: TERRA,
    zone_on: INK,
    card_bg: PANEL,
    card_border: `1px solid ${LINE2}`,
    align: `center`,
    items: [
      {
        option: `A romantic escape`,
        title: `The Garden Suite`,
        text: `A private courtyard, a deep copper tub and breakfast brought to your door. Tell us it's an occasion and we'll do the rest.`,
        meta: `from €280 / night`,
        cta_text: ``,
        cta_url: `#`,
        icon: ``,
      },
      {
        option: `Solo & slow`,
        title: `The Reading Room`,
        text: `A snug corner room with the best armchair in the house, a stack of books and a window onto the old town.`,
        meta: `from €150 / night`,
        cta_text: ``,
        cta_url: `#`,
        icon: ``,
      },
      {
        option: `With the family`,
        title: `The Casa Rooms`,
        text: `Two connecting rooms off a shared terrace — space for everyone, and a courtyard the kids can't get lost in.`,
        meta: `from €240 / night`,
        cta_text: ``,
        cta_url: `#`,
        icon: ``,
      },
      {
        option: `All about the view`,
        title: `The Rooftop`,
        text: `Our top-floor room opens straight onto the terrace — rooftops, bells and a sunset you'll talk about for years.`,
        meta: `from €320 / night`,
        cta_text: ``,
        cta_url: `#`,
        icon: ``,
      },
    ],
  }) ]) ]),
]));

// 8) TESTIMONIAL -- spirito del luogo
home.push(sec(BG2, 'large', [ row([ col('1-1', [ tile('testimonial', {
  quote: '“We made Pasaje for people who want a room they remember and a city they discover slowly. Come stay with us.”',
  author_name: 'Inés & the Pasaje team',
  author_role: 'Pasaje del Mar 4 · old town',
  rating: '0',
  layout: 'single',
  show_line: false,
  bg_color: 'transparent',
  text_color: CREAM,
  border_radius: '0',
  avatar: '',
}) ]) ]) ]));

// 9) CTA -- "Your room is waiting" (2 pulsanti: btn--terra + btn--cream come nel blueprint)
// CSS: .pa-cta__cta { display:flex; gap:12px; justify-content:center }
// btn--terra = TERRA/INK, btn--cream = CREAM/INK
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('cta-banner', {
  headline: 'Your room is',
  headline_accent: 'waiting',
  headline_accent_italic: false,
  subtitle: 'Book direct for the best rate, a late checkout, and a glass of something on the roof when you arrive.',
  cta_text: 'Book a room',
  cta_url: '#book',
  cta2_text: 'See the rooms',
  cta2_url: '#rooms',
  cta2_bg: CREAM,
  cta2_color: INK,
  cta2_border: 'transparent',
  bg: {type: 'solid', color: PANEL2},
  text_color: WHITE,
  accent_color: TERRA,
  subtitle_color: TXT,
  cta_bg: TERRA,
  cta_color: INK,
  cta_radius: R(999),
  cta_size: 13,
  headline_font_family: 'serif',
  headline_size: 56,
  headline_weight: '400',
  subtitle_size: 17,
  layout: 'stack',
  vertical_align: 'center',
  banner_radius: R(16),
  banner_padding: 80,
}) ]) ]) ]));

K.emit({
  slug: 'pasaje',
  name: 'Pasaje',
  tags: ['travel', 'hotel', 'boutique', 'mediterranean'],
  description: 'Pasaje -- boutique hotel travel theme. Warm terracotta + sand, Yeseva One (display) + Karla. 14-room hotel nel vicolo del centro storico. Booking bar approssimata con info-cards (BEST-EFFORT). Finder interattivo tile-pure (finder). Gallery approssimata con trust-strip (BEST-EFFORT).',
  colors: {
    primary: TERRA,
    primary_contrast: INK,
    secondary: SAND,
    secondary_contrast: INK,
    muted: BG2,
    muted_contrast: TXT,
    text: TXT,
    text_muted: DIM,
    background: BG,
    border: LINE,
    link: TERRA,
  },
  css_disp: '"Yeseva One", Georgia, serif',
  css_sans: '"Karla", -apple-system, sans-serif',
  heading_weight: '400',
  heading_line_height: '1.1',
  google_fonts: ['Yeseva One', 'Karla'],
  logo_variant: 'light',
  menu: [
    {title: 'Rooms', url: '#rooms'},
    {title: 'The stay', url: '#stay'},
    {title: 'Experiences', url: '#experiences'},
    {title: 'Gallery', url: '#gallery'},
  ],
  header: {bg: 'rgba(42,26,20,.84)', text_color: TXT, sticky_bg: 'rgba(42,26,20,.92)', logo_width: 130},
  footer: {
    bg: BG2,
    headColor: CREAM,
    brand: {name: 'Pasaje', tagline: 'A fourteen-room boutique hotel down a quiet passage in the old town.'},
    columns: [
      {title: 'Stay', links: ['Rooms', 'Book', 'Experiences', 'Offers']},
      {title: 'Hotel', links: ['The stay', 'Gallery', 'Dining']},
      {title: 'Find us', links: ['Pasaje del Mar 4', '+34 952 00 00', 'hola@pasaje.hotel']},
    ],
    bottom: {left: '© 2026 Pasaje — an OLOtheme demo.', right: 'Built with OLObuild'},
  },
  cursor: {blend_mode: 'exclusion', ring_color: TERRA, dot_color: TERRA},
}, home);
