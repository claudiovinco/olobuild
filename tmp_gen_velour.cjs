/* Vélour — ricomposizione TILE-PURE (image-free). Beauty & Fashion salon. Plum + rose-gold + Forum/Albert Sans. */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('ve');

// ─── PALETTE (da :root velour.css) ───────────────────────────────────────────
const BG     = '#241820';
const BG2    = '#2a1d26';
const PANEL  = '#352630';
const PANEL2 = '#41303b';
const INK    = '#170f14';
const ROSE   = '#cf8e6f';
const ROSEL  = '#dda588';
const GOLD   = '#d9b78a';
const CREAM  = '#f2e8e6';
const TXT    = '#c1aab2';
const DIM    = '#8f7780';
const LINE   = 'rgba(242,232,230,.13)';
const LINE2  = 'rgba(207,142,111,.42)';

const home = [];

// ─── HELPER section-header ───────────────────────────────────────────────────
const shead = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: ROSE, eyebrow_dot_color: ROSE, eyebrow_separator: '',
  headline_lines: [ {text: l1, color: CREAM, italic: false}, {text: accent, color: ROSE, italic: true} ],
  headline_font_family: 'serif', headline_font_size: 52, headline_font_weight: '400', headline_align: 'center', headline_inline: true,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false, tagline_text_color: DIM, tagline_text_size: 16,
  layout: 'center', gap: 16,
});

// ─── HELPER team member ───────────────────────────────────────────────────────
const vMember = (name, role) => col('1-4', [ tile('team', {
  photo: '', name, role, bio: '', link_url: '', link_text: '',
  photo_size: '120', photo_shape: 'circle', photo_border_width: '0', photo_shadow: 'none', photo_gap: '16',
  info_bg_color: 'transparent', info_text_color: CREAM, role_color: ROSE, info_align: 'center',
  name_size: '22', name_weight: '400', role_size: '12',
  bg_color: 'transparent', tile_padding: {top: 0, right: 0, bottom: 0, left: 0}, border_radius: '0',
}) ]);

// ─── 1) HERO ─────────────────────────────────────────────────────────────────
// image-free: showcase con statistiche salone al posto del pannello immagine
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('hero-split', {
  eyebrow_text: `Hair & beauty salon`, eyebrow_dot_color: ROSE, eyebrow_color: ROSE,
  headline_lines: [
    {text: `Leave feeling`, color: CREAM, italic: false},
    {text: `like you.`,     color: ROSE,  italic: true},
  ],
  headline_font_family: 'serif', headline_font_size: 80, headline_line_height: 1.08, headline_font_weight: '400', headline_align: 'left',
  subhead: `Colour, cuts and care by senior stylists who actually listen — in a warm, unhurried space where you're never rushed out the door.`,
  subhead_color: TXT, subhead_size: 17, subhead_italic: false, subhead_max_width: 480,
  cta1_text: `Book your chair`, cta1_url: '#book', cta1_bg: ROSE, cta1_color: INK, cta1_size: 12, cta1_radius: R(999), cta1_radius_hover: R(999),
  cta2_text: `View services`,   cta2_url: '#services', cta2_bg: 'transparent', cta2_color: CREAM, cta2_border: LINE2, cta2_size: 12, cta2_radius: R(999), cta2_radius_hover: R(999),
  stats: [],
  showcase_enabled: true, showcase_bg: {type: 'solid', color: PANEL}, showcase_padding: 32, showcase_radius: R(20), showcase_radius_hover: R(20),
  showcase_badge_text: `VÉLOUR SALON`, showcase_badge_dot: ROSE, showcase_badge_bg: INK, showcase_badge_color: CREAM,
  showcase_items: [
    {number: `Senior stylists`,    text: `4`,          italic: false, text_color: CREAM, bg: {type: 'solid', color: BG2}},
    {number: `Years open`,         text: `9`,          italic: false, text_color: ROSE,  bg: {type: 'solid', color: BG2}},
    {number: `Open`,               text: `Tue – Sat`,  italic: false, text_color: CREAM, bg: {type: 'solid', color: BG2}},
    {number: `New client welcome`, text: `Consultation included`, italic: false, text_color: CREAM, bg: {type: 'solid', color: BG2}},
  ],
  showcase_card_radius: R(12), showcase_card_radius_hover: R(12), showcase_card_shadow: 'none',
  showcase_caption_left: 'EST. 2017', showcase_caption_right: 'BY APPOINTMENT', showcase_hover_effect: 'none',
  split_ratio: '1.25fr .75fr', gap: 52, min_height: 0, tile_padding: {top: 0, right: 0, bottom: 0, left: 0},
}) ]) ]) ]));

// ─── 2) MENU SERVIZI E PREZZI ─────────────────────────────────────────────────
// Sezione senza .panel → sfondo BG (dark plum)
// Struttura: h3 "Hair"/"Beauty" come primo item vuoto (group header) + 3 voci per colonna
home.push(sec(BG, 'large', [
  row([ col('1-1', [ shead('The menu', `Services & `, `pricing`, '') ]) ]),
  row([
    col('1-2', [ tile('pricelist', {
      show_image: false,
      price_position: 'right',
      separator_style: 'dotted',
      separator_color: LINE2,
      title_color: CREAM,
      price_color: ROSE,
      description_color: DIM,
      card_bg: '',
      card_border_radius: '0',
      card_border_color: 'transparent',
      hover_lift: false,
      gap: '0',
      tile_padding: {top: 0, right: 0, bottom: 0, left: 0},
      items: [
        {id: 've-h1', title: `Hair`, description: '', price: '', highlighted: false, badge: ''},
        {id: 've-h2', title: `Cut & finish`,  description: `Consultation, cut, style. 60 min.`, price: `from €55`,  highlighted: false, badge: ''},
        {id: 've-h3', title: `Full colour`,   description: `Root to tip, gloss included.`,      price: `from €95`,  highlighted: false, badge: ''},
        {id: 've-h4', title: `Balayage`,      description: `Hand-painted, lived-in.`,           price: `from €140`, highlighted: false, badge: ''},
      ],
    }) ]),
    col('1-2', [ tile('pricelist', {
      show_image: false,
      price_position: 'right',
      separator_style: 'dotted',
      separator_color: LINE2,
      title_color: CREAM,
      price_color: ROSE,
      description_color: DIM,
      card_bg: '',
      card_border_radius: '0',
      card_border_color: 'transparent',
      hover_lift: false,
      gap: '0',
      tile_padding: {top: 0, right: 0, bottom: 0, left: 0},
      items: [
        {id: 've-b1', title: `Beauty`, description: '', price: '', highlighted: false, badge: ''},
        {id: 've-b2', title: `Brows & tint`,   description: `Shape, tint, finish.`,           price: `€28`, highlighted: false, badge: ''},
        {id: 've-b3', title: `Gel manicure`,   description: `Two weeks, no chips.`,           price: `€38`, highlighted: false, badge: ''},
        {id: 've-b4', title: `Facial · glow`,  description: `45 min, tailored to your skin.`, price: `€65`, highlighted: false, badge: ''},
      ],
    }) ]),
  ], {gap: 48}),
]));

// ─── 3) STYLISTS (team × 4 in row) ───────────────────────────────────────────
// Blueprint: .vl-sec.panel → sfondo BG2. Avatar cerchio + nome Forum 22px + ruolo rose 12px.
// photo_shape 'circle' → avatar circolare come nel tile team sterling.
home.push(sec(BG2, 'large', [
  row([ col('1-1', [ shead('The chairs', `Your `, `stylists`, '') ]) ]),
  row([
    vMember('Noor',  'Colour director'),
    vMember(`Théo`,  'Senior cutter'),
    vMember('Bea',   'Balayage'),
    vMember('Sami',  `Beauty & brows`),
  ], {gap: 16}),
]));

// ─── 4) GALLERY → approssimata con info-cards astratte (image-free) ───────────
// Blueprint: .vl-sec (no panel) → sfondo BG.
// ProductGallery non esiste come tile → 6 info-cards astratte con icona + titolo + testo
home.push(sec(BG, 'large', [
  row([ col('1-1', [ shead('Recent work', `From the `, `chair`, '') ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg: {type: 'solid', color: 'transparent'}, container_padding: 0, container_gap: 12, columns: 3, items_gap: 12,
    card_bg: {type: 'solid', color: PANEL}, card_color: TXT, card_radius: R(14), card_padding: 32,
    show_icon: true, show_counter: false, show_counter_label: false, show_arrow: false, show_footer: false, show_media: false,
    icon_color: ROSE, icon_bg_color: `rgba(207,142,111,.12)`,
    title_color: CREAM, title_font_family: 'serif', title_size: 20, title_weight: '400', title_italic: true,
    description_size: 14,
    card_hover_effect: 'lift',
    items: [
      {icon: 'scissors',  title: `Colour transformation`, description: `Root-to-tip refresh that gives hair a new life — seamless coverage, brilliant shine.`},
      {icon: 'scissors',  title: `Blunt bob`,             description: `Sharp, graphic and effortlessly chic. Cut to sit perfectly with every head turn.`},
      {icon: 'sparkles',  title: `Balayage`,              description: `Hand-painted sun-kissed tones that grow out beautifully. No hard lines.`},
      {icon: 'sparkles',  title: `Updo`,                  description: `Intricate braids and pinned styles for weddings, events and special occasions.`},
      {icon: 'heart',     title: `Brows`,                 description: `Defined brows that frame your face — shaped, tinted and groomed to perfection.`},
      {icon: 'star',      title: `Gloss treatment`,       description: `High-shine gloss seals the cuticle and boosts colour vibrancy for weeks.`},
    ],
  }) ]) ]),
]));

// ─── 5) TESTIMONIAL ──────────────────────────────────────────────────────────
// Blueprint: .vl-sec.panel → sfondo BG2. Citazione Forum italic 40px. Firma "Hannah V. · client since 2022".
home.push(sec(BG2, 'large', [ row([ col('1-1', [ tile('testimonial', {
  quote: `"Noor is the first colourist who actually got what I meant by 'natural'. I won't go anywhere else now."`,
  author_name: 'Hannah V.', author_role: `client since 2022`, rating: '0',
  layout: 'single', show_line: false, bg_color: 'transparent', text_color: CREAM, border_radius: '0', avatar: '',
}) ]) ]) ]));

// ─── 6) MIXER (hair-melt interactive zone) ───────────────────────────────────
// Blueprint: .vl-sec (no panel) → sfondo BG.
home.push(sec(BG, 'large', [
  row([ col('1-1', [ tile('mixer', {
    eyebrow: 'The colour bar',
    heading: 'Blend your shade',
    intro: `Balayage is two tones living together. Tap a base and a gloss to preview the melt — then bring it to your colourist as a starting point.`,
    max: 2,
    empty_label: 'Tap two tones to blend',
    zone_accent: '#cf8e6f',
    zone_on: '#170f14',
    card_bg: PANEL,
    card_border: LINE,
    align: 'center',
    items: [
      {name: 'Espresso',   color: '#2b211c'},
      {name: 'Chestnut',   color: '#6e4a32'},
      {name: 'Caramel',    color: '#a9763f'},
      {name: 'Honey',      color: '#d8a86a'},
      {name: 'Champagne',  color: '#e7c98f'},
      {name: 'Copper',     color: '#b65b4a'},
    ],
  }) ]) ]),
]));

// ─── 7) CTA ──────────────────────────────────────────────────────────────────
// Blueprint: 2 bottoni — btn--rose "Book online" + btn--cream "Call the salon"
// .vl-sec.vl-cta (no panel) → sfondo BG
// Box con border 1px LINE2, border-radius 20px, padding clamp(48,7vw,92)
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('cta-banner', {
  headline: `Ready for a `, headline_accent: `change?`, headline_accent_italic: true,
  subtitle: `Book online in under a minute, or call us — new clients always start with a consultation.`,
  cta_text: `Book online`, cta_url: '#book',
  cta2_text: `Call the salon`, cta2_url: '#', cta2_bg: CREAM, cta2_color: INK, cta2_border: '',
  bg: {type: 'solid', color: PANEL}, text_color: CREAM, accent_color: ROSE, subtitle_color: TXT,
  cta_bg: ROSE, cta_color: INK, cta_radius: R(999), cta_size: 12,
  headline_font_family: 'serif', headline_size: 52, headline_weight: '400', subtitle_size: 17,
  layout: 'stack', vertical_align: 'center', banner_radius: R(20), banner_padding: 80,
}) ]) ]) ]));

K.emit({
  slug: 'velour', name: `Vélour`,
  tags: ['beauty', 'fashion', 'salon', 'hair', 'wellness'],
  description: `Vélour — Hair & beauty salon. Plum + rose-gold, Forum (display) + Albert Sans (body). Sezioni: hero, menu prezzi, stylists (team), gallery astratta, testimonial, colour bar, CTA (2 bottoni). Riproduzione fedele dell'OLOtheme Vélour.`,
  colors: {
    primary: ROSE, primary_contrast: INK,
    secondary: GOLD, secondary_contrast: INK,
    muted: BG2, muted_contrast: TXT,
    text: TXT, text_muted: DIM,
    background: BG, border: LINE, link: ROSE,
  },
  css_disp: `"Forum", Georgia, serif`,
  css_sans: `"Albert Sans", -apple-system, sans-serif`,
  heading_weight: '400', heading_line_height: '1.08',
  google_fonts: ['Forum', 'Albert Sans'],
  logo_variant: 'light',
  menu: [
    {title: 'Services', url: '#services'},
    {title: 'Stylists', url: '#team'},
    {title: 'Gallery',  url: '#gallery'},
    {title: 'Book',     url: '#book'},
  ],
  header: { bg: `rgba(36,24,32,.86)`, text_color: TXT, sticky_bg: `rgba(36,24,32,.88)`, logo_width: 130 },
  footer: {
    bg: BG2, headColor: CREAM,
    brand: {name: `Vélour`, tagline: `Hair & beauty salon. Colour, cuts and care in a warm, unhurried space.`},
    columns: [
      {title: 'Salon',  links: ['Services', 'Stylists', 'Gallery', 'Book']},
      {title: 'About',  links: ['Our story', 'Products', 'Careers']},
      {title: 'Visit',  links: ['8 Rosewood Lane', `Tue–Sat · by appt`, 'hello@velour.salon']},
    ],
    bottom: {left: `© 2026 Vélour — an OLOtheme demo.`, right: 'Built with OLObuild'},
  },
  cursor: {blend_mode: 'exclusion', ring_color: ROSE, dot_color: ROSE},
}, home);
