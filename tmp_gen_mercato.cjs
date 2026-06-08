/* Mercato — ricomposizione TILE-PURE (image-free). E-commerce concept store.
   Palette: paper #f5f2ec + ink #141414 + red #e1474f. Type: Hanken Grotesk + Work Sans.
   AGGIORNAMENTO pixel-perfect pass:
   - SpinViewer: hero-split statico → viewer360 (mode:object-rotate) + section-header copy
   - Promise strip: sfondo CARD (#fff) con border LINE top/bottom
   - Featured products: sezione staccata da categories (padding-top gestito dal kit)
   - Copy: backtick corretti per apostrofi curvi
   - Sfondi sezione allineati al blueprint */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('mc');

// ── Palette (da :root mercato.css)
const PAPER  = '#f5f2ec';
const PAPER2 = '#ebe6dd';
const CARD   = '#ffffff';
const INK    = '#141414';
const INK2   = '#1f1f1f';
const RED    = '#e1474f';
const REDD   = '#c8323a';
const TXT    = '#181715';
const SOFT   = '#5a5650';
const FAINT  = '#8e897f';
const LINE   = '#e3ddd1';
const WHITE  = '#ffffff';

const home = [];

// ── helper: section-header left-aligned (eyebrow + h2 + optional tagline)
const shead = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: RED, eyebrow_dot_color: RED, eyebrow_separator: '',
  headline_lines: [
    { text: l1,     color: INK, italic: false },
    { text: accent, color: RED, italic: false },
  ],
  headline_font_family: 'sans-serif', headline_font_size: 46, headline_font_weight: '800',
  headline_align: 'left', headline_inline: !!accent,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false,
  tagline_text_color: SOFT, tagline_text_size: 16,
  layout: 'stack', gap: 12,
});

// ── 1) HERO — Objects worth keeping
// Blueprint: sfondo ink sinistra / media placeholder destra, hero full-bleed 2 col
// Titolo: "Objects / worth / keeping." uppercase, 84px, weight 800
// Badge info: category / price / status / season
home.push(sec(INK, 'large', [row([col('1-1', [tile('hero-split', {
  eyebrow_text: 'New · Spring collection', eyebrow_dot_color: RED, eyebrow_color: RED,
  headline_lines: [
    { text: 'Objects',  color: WHITE, italic: false },
    { text: 'worth',    color: WHITE, italic: false },
    { text: 'keeping.', color: RED,   italic: false },
  ],
  headline_font_family: 'sans-serif', headline_font_size: 84, headline_line_height: 0.92,
  headline_font_weight: '800', headline_align: 'left',
  subhead: `A concept store for well-made everyday things — homeware, accessories and small-batch design, chosen by hand.`,
  subhead_color: 'rgba(255,255,255,.70)', subhead_size: 17, subhead_italic: false, subhead_max_width: 400,
  cta1_text: 'Shop the drop', cta1_url: '#shop', cta1_bg: RED, cta1_color: WHITE, cta1_size: 13,
  cta1_radius: R(0), cta1_radius_hover: R(0),
  cta2_text: 'Browse all', cta2_url: '#categories', cta2_bg: 'transparent', cta2_color: WHITE,
  cta2_border: 'rgba(255,255,255,.50)', cta2_size: 13,
  cta2_radius: R(0), cta2_radius_hover: R(0),
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: INK2 },
  showcase_padding: 28, showcase_radius: R(0), showcase_radius_hover: R(0),
  showcase_badge_text: 'THE STONEWARE SET · NEW IN', showcase_badge_dot: RED,
  showcase_badge_bg: INK, showcase_badge_color: WHITE,
  showcase_items: [
    { number: 'Category', text: 'Homeware',    italic: false, text_color: WHITE, bg: { type: 'solid', color: INK2 } },
    { number: 'Price',    text: `€74`,         italic: false, text_color: RED,   bg: { type: 'solid', color: INK2 } },
    { number: 'Status',   text: 'In stock',    italic: false, text_color: WHITE, bg: { type: 'solid', color: INK2 } },
    { number: 'Season',   text: 'Spring 2026', italic: false, text_color: WHITE, bg: { type: 'solid', color: INK2 } },
  ],
  showcase_card_radius: R(0), showcase_card_radius_hover: R(0), showcase_card_shadow: 'none',
  showcase_caption_left: 'NEW DROP', showcase_caption_right: 'SPRING 2026', showcase_hover_effect: 'none',
  split_ratio: '1fr 1fr', gap: 0, min_height: 580,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
})])])]));

// ── 2) CATEGORY TILES — 4 categorie (best-effort: info-cards)
// Blueprint: .cat — immagine aspect-ratio 3/3.6 con gradient overlay e label bianca
// Tile dedicato "CategoryTiles" non esiste → info-cards con card_bg paper2, arrow, no icon
// nota: il CSS ha aspect-ratio 3/3.6 e gradient-overlay; info-cards è la migliore approssimazione
home.push(sec(PAPER, 'large', [
  row([col('1-1', [shead('Shop by category', 'Collections', '', '')])]),
  row([col('1-1', [tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' }, container_padding: 0, container_gap: 14,
    columns: 4, items_gap: 14,
    card_bg: { type: 'solid', color: PAPER2 }, card_color: SOFT,
    card_radius: R(0), card_padding: 32,
    show_icon: true, show_counter: false, show_arrow: true, show_footer: false, show_media: false,
    icon_color: RED, icon_bg_color: 'rgba(225,71,79,.10)',
    title_color: INK, title_font_family: 'sans-serif', title_size: 18, title_weight: '800', title_italic: false,
    description_size: 13,
    card_hover_effect: 'lift',
    items: [
      { icon: 'home',     title: 'Homeware',    description: 'Ceramics, textiles and everyday objects for the home.' },
      { icon: 'coffee',   title: 'Kitchen',     description: 'Oils, pantry goods and small-batch ingredients.' },
      { icon: 'scissors', title: 'Accessories', description: 'Bags, wraps and carry-along essentials.' },
      { icon: 'book',     title: 'Stationery',  description: 'Notebooks, journals and paper goods.' },
    ],
  })])])
]));

// ── 3) FEATURED PRODUCTS — 8 prodotti (product grid, best-effort: info-cards)
// Blueprint: .prod — card con media 3/3.6, badge pill, hover "Quick add", categoria FAINT,
//            nome uppercase disp 17px, prezzo 14px. sfondo PAPER, padding-top:0 (segue la 2)
// Tile "product-cards" ha struttura monogramma/lettera diversa — info-cards è più vicino
// al layout prodotto del blueprint (counter=badge, footer=prezzo, counter_label=categoria)
home.push(sec(PAPER, 'large', [
  row([col('1-1', [shead('Just landed', 'New in', '', '')])]),
  row([col('1-1', [tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' }, container_padding: 0, container_gap: 16,
    columns: 4, items_gap: 20,
    card_bg: { type: 'solid', color: CARD }, card_color: FAINT,
    card_radius: R(0), card_padding: 0,
    show_icon: false, show_counter: true, show_counter_label: true, show_arrow: false,
    show_footer: true, show_media: false,
    counter_shape: 'square', counter_color: WHITE, counter_bg: RED,
    title_color: INK, title_font_family: 'sans-serif', title_size: 17, title_weight: '700', title_italic: false,
    description_size: 12, counter_size: 10,
    card_hover_effect: 'lift',
    items: [
      { counter: 'New',  counter_label: 'Homeware',    title: 'Stoneware set',    description: 'Wheel-thrown stoneware for the everyday table.', footer_text: `€74`,  footer_dot_color: RED   },
      { counter: '',     counter_label: 'Kitchen',     title: 'Linen apron',      description: 'Stone-washed linen, adjustable neck strap.',      footer_text: `€38`,  footer_dot_color: FAINT },
      { counter: '',     counter_label: 'Accessories', title: 'Canvas tote',      description: 'Heavy-duty canvas, naturally dyed.',               footer_text: `€22`,  footer_dot_color: FAINT },
      { counter: 'Sale', counter_label: 'Stationery',  title: 'Notebook trio',    description: 'Three sizes, stitched binding, cream pages.',      footer_text: `€28`,  footer_dot_color: RED   },
      { counter: '',     counter_label: 'Homeware',    title: 'Glass carafe',     description: 'Mouth-blown borosilicate, 1.2 l.',                  footer_text: `€54`,  footer_dot_color: FAINT },
      { counter: '',     counter_label: 'Kitchen',     title: 'Beeswax wraps',    description: 'Reusable food wraps, pack of three.',              footer_text: `€19`,  footer_dot_color: FAINT },
      { counter: 'New',  counter_label: 'Accessories', title: 'Wool throw',       description: '100% merino, oversized, machine-washable.',        footer_text: `€46`,  footer_dot_color: RED   },
      { counter: '',     counter_label: 'Homeware',    title: 'Ceramic planter',  description: 'Terracotta-finish, drainage hole, 14 cm.',          footer_text: `€32`,  footer_dot_color: FAINT },
    ],
  })])])
]));

// ── 4) SPINVIEWER — Inspect it / See every angle
// Blueprint: tile SpinViewer — stage a sinistra (drag-to-spin, 24 frame, readout frame+angolo,
//            ring animato, progress bar, pulsanti step) + copy a destra (eyebrow/h2/p/cta)
// → tile viewer360 (mode: object-rotate) a sinistra + section-header+cta a destra
// Il viewer360 con mode:object-rotate supporta drag, inerzia, show_angle — aderente al blueprint
home.push(sec(PAPER2, 'large', [row([
  col('1-2', [tile('viewer360', {
    mode: 'object-rotate',
    object_image: '',
    spin_inertia: 0.97,
    drag_sensitivity: 0.55,
    show_angle: true,
    autorotate: false,
    mouse_drag: true,
    touch_drag: true,
    show_controls: false,
    show_fullscreen: false,
    show_zoom: false,
    show_compass: false,
    height: '480',
    border_radius: { tl: 0, tr: 0, br: 0, bl: 0, linked: true },
    shadow: 'none',
    caption: '360° — drag to spin',
    preset: 'product-display',
  })]),
  col('1-2', [
    tile('section-header', {
      eyebrow_show: true, eyebrow_text: 'Inspect it', eyebrow_color: RED, eyebrow_dot_color: RED, eyebrow_separator: '',
      headline_lines: [
        { text: 'See every', color: INK, italic: false },
        { text: 'angle',     color: RED, italic: false },
      ],
      headline_font_family: 'sans-serif', headline_font_size: 50, headline_font_weight: '800',
      headline_align: 'left', headline_inline: true,
      tagline_show: true,
      tagline_text: `Spin the bottle to look it over before you buy — drag, swipe, or step through with the arrows.`,
      tagline_text_color: SOFT, tagline_text_size: 15.5, tagline_text_italic: false,
      layout: 'stack', gap: 14,
    }),
    tile('cta-banner', {
      headline: '', headline_accent: '',
      subtitle: '',
      cta_text: `Add to basket — €28`, cta_url: '#shop',
      cta2_text: '', cta2_url: '',
      bg: { type: 'solid', color: 'transparent' },
      text_color: INK, accent_color: RED, subtitle_color: SOFT,
      cta_bg: RED, cta_color: WHITE, cta_radius: R(0), cta_size: 13,
      headline_font_family: 'sans-serif', headline_size: 0, headline_weight: '800', subtitle_size: 0,
      layout: 'stack', vertical_align: 'center', banner_radius: R(0), banner_padding: 0,
    }),
  ]),
], { gap: 52, vertical_align: 'center' })]));

// ── 5) LOOKBOOK — Made to be used daily
// Blueprint: split 1fr 1fr — media a sinistra (aspect-ratio 1/1), copy a destra su INK2
// hero-split: copy sinistra + showcase destra → non è il layout corretto.
// Usare 2-col con una col media-placeholder + una col hero per il copy destra
// Il css .mc-look__copy ha sfondo INK2 e .mc-look__media occupa la colonna intera
home.push(sec(INK2, 'large', [row([
  col('1-2', [tile('info-cards', {
    // Colonna sinistra: media placeholder in stile lookbook (card unica, bg pattern)
    container_bg: { type: 'pattern', color: INK, pattern: 'diagonal-lines' },
    container_padding: 0, container_gap: 0, columns: 1, items_gap: 0,
    card_bg: { type: 'solid', color: 'transparent' }, card_color: WHITE,
    card_radius: R(0), card_padding: 0,
    show_icon: false, show_counter: false, show_arrow: false, show_footer: false, show_media: false,
    card_hover_effect: 'none',
    items: [
      { title: 'LOOKBOOK', description: 'Spring 2026 — styled interior scene' },
    ],
  })]),
  col('1-2', [tile('hero-split', {
    // Colonna destra: copy lookbook su INK2
    eyebrow_text: 'Spring lookbook', eyebrow_dot_color: RED, eyebrow_color: RED,
    headline_lines: [
      { text: 'Made to be', color: WHITE, italic: false },
      { text: 'used daily', color: WHITE, italic: false },
    ],
    headline_font_family: 'sans-serif', headline_font_size: 52, headline_line_height: 0.98,
    headline_font_weight: '800', headline_align: 'left',
    subhead: 'We don’t do “too nice to touch”. Everything in the shop is built for real life — dishwasher-safe, hard-wearing, and better with age.',
    subhead_color: 'rgba(255,255,255,.70)', subhead_size: 16, subhead_italic: false, subhead_max_width: 380,
    cta1_text: 'Shop the look', cta1_url: '#shop',
    cta1_bg: 'transparent', cta1_color: WHITE, cta1_border: 'rgba(255,255,255,.50)',
    cta1_size: 13, cta1_radius: R(0), cta1_radius_hover: R(0),
    cta2_text: '', cta2_url: '', cta2_bg: 'transparent', cta2_color: WHITE, cta2_border: '',
    stats: [],
    showcase_enabled: false,
    split_ratio: '1fr 0fr', gap: 0, min_height: 460,
    tile_padding: { top: 52, right: 52, bottom: 52, left: 52 },
  })]),
], { gap: 0 })]));

// ── 6) PROMISE — 4 promise strip
// Blueprint: .mc-promise — sfondo CARD (#ffffff), border-block 1px LINE, 4 col centrate
// Variante: bordo top+bottom con LINE; sfondo bianco; icone rosso; text-icon centrati
home.push(sec(CARD, 'small', [row([col('1-1', [tile('info-cards', {
  container_bg: { type: 'solid', color: 'transparent' }, container_padding: 0, container_gap: 18,
  columns: 4, items_gap: 18,
  card_bg: { type: 'solid', color: 'transparent' }, card_color: FAINT,
  card_radius: R(0), card_padding: 16,
  show_icon: true, show_counter: false, show_arrow: false, show_footer: false, show_media: false,
  icon_color: RED, icon_bg_color: 'transparent',
  title_color: INK, title_font_family: 'sans-serif', title_size: 13, title_weight: '600', title_italic: false,
  description_size: 11.5,
  card_hover_effect: 'none',
  items: [
    { icon: 'truck',        title: 'Free over €60',  description: 'Carbon-neutral delivery' },
    { icon: 'refresh-cw',   title: '60-day returns', description: 'No-quibble, no stress'   },
    { icon: 'shield-check', title: 'Small batch',    description: 'Made by people we know'  },
    { icon: 'leaf',         title: 'Plastic-free',   description: 'Packed in paper, always' },
  ],
})])])]));

// ── 7) BASKET BUILDER — tile Builder (stepper +/− interattivi, totale live)
// zone_accent: #e1474f (RED), zone_on: #fff (WHITE) — dal blueprint HTML.
// card_bg: CARD (bianco), card_border: LINE. Sfondo PAPER2.
home.push(sec(PAPER2, 'large', [
  row([col('1-1', [tile('builder', {
    eyebrow:     'Try it',
    heading:     'Build a basket',
    intro:       `Add a few bestsellers and watch the total tick up. Free shipping kicks in over €50 — the cart does the maths.`,
    currency:    `€`,
    cap:         0,
    total_label: 'Total',
    count_label: 'items',
    cta_text:    'Add to basket',
    cta_url:     '#basket',
    zone_accent: RED,
    zone_on:     WHITE,
    card_bg:     CARD,
    card_border: LINE,
    align:       'left',
    items: [
      { name: 'Estate Olive Oil',  price: '18', note: '500ml',  start: 0 },
      { name: 'Flaky Sea Salt',    price: '12', note: '250g',   start: 0 },
      { name: 'Wildflower Honey',  price: '22', note: '400g',   start: 0 },
      { name: 'Seeded Crackers',   price: '9',  note: 'pack',   start: 0 },
      { name: 'House Roast Beans', price: '26', note: '1kg',    start: 0 },
      { name: `Fig & Walnut Jam`,  price: '15', note: '320g',   start: 0 },
    ],
  })])]),
]));

// ── 8) NEWSLETTER — Get first dibs
// Blueprint: sfondo INK, h2 uppercase, form email + button
// cta-banner best-effort (no form reale), cta_text = "Sign up"
home.push(sec(INK, 'large', [row([col('1-1', [tile('cta-banner', {
  headline: 'Get first ', headline_accent: 'dibs', headline_accent_italic: false,
  subtitle: `New drops sell through fast. Subscribers hear first — and get 10% off to start.`,
  cta_text: 'Sign up', cta_url: '#newsletter',
  cta2_text: '', cta2_url: '',
  bg: { type: 'solid', color: INK }, text_color: WHITE, accent_color: RED,
  subtitle_color: 'rgba(255,255,255,.70)',
  cta_bg: RED, cta_color: WHITE, cta_radius: R(0), cta_size: 13,
  headline_font_family: 'sans-serif', headline_size: 64, headline_weight: '800', subtitle_size: 16,
  layout: 'stack', vertical_align: 'center', banner_radius: R(0), banner_padding: 80,
})])])]));

// ── EMIT ──────────────────────────────────────────────────────────────────────
K.emit({
  slug: 'mercato', name: 'Mercato',
  tags: ['ecommerce', 'shop', 'concept-store', 'editorial'],
  description: `Mercato — concept store for everyday objects. Paper/ink/red palette. Hanken Grotesk (800) + Work Sans. Sezioni: hero split, categorie (4), prodotti (8), SpinViewer 360 (viewer360 object-rotate), lookbook split, promise strip, basket builder (tile Builder nativo, stepper +/−, totale live), newsletter. Riproduzione OLOtheme Mercato tile-pure.`,
  colors: {
    primary:            RED,
    primary_contrast:   WHITE,
    secondary:          INK,
    secondary_contrast: WHITE,
    muted:              PAPER2,
    muted_contrast:     TXT,
    text:               TXT,
    text_muted:         FAINT,
    background:         PAPER,
    border:             LINE,
    link:               RED,
  },
  css_disp: `"Hanken Grotesk", -apple-system, sans-serif`,
  css_sans: `"Work Sans", -apple-system, sans-serif`,
  heading_weight: '800',
  heading_line_height: '0.92',
  google_fonts: ['Hanken Grotesk', 'Work Sans'],
  logo_variant: 'dark',
  menu: [
    { title: 'Shop',        url: '#shop'       },
    { title: 'Collections', url: '#categories' },
    { title: 'Lookbook',    url: '#lookbook'   },
    { title: 'About',       url: '#'           },
  ],
  header: {
    bg:         'rgba(245,242,236,.94)',
    text_color: SOFT,
    sticky_bg:  'rgba(245,242,236,.95)',
    logo_width: 130,
  },
  footer: {
    bg:        INK,
    headColor: WHITE,
    brand: {
      name:    'Mercato',
      tagline: `A concept store for well-made everyday objects. Chosen by hand, made to last.`,
    },
    columns: [
      { title: 'Shop',  links: ['New in', 'Homeware', 'Kitchen', 'Accessories']            },
      { title: 'About', links: ['Our story', 'Lookbook', 'Makers', 'Sustainability']        },
      { title: 'Help',  links: ['Shipping', 'Returns', 'Contact', 'FAQ']                   },
    ],
    bottom: {
      left:  `© 2026 Mercato — an OLOtheme demo.`,
      right: 'Built with OLObuild',
    },
  },
  cursor: false,
}, home);
