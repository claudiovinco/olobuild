/* Tavola — ricomposizione TILE-PURE (image-free). Food & Drink family trattoria.
   Palette: cream/espresso/terracotta/olive. Font: Zilla Slab (display) + Karla (body).
   Blueprint: OLOtheme - Tavola (Food & Drink).html                                     */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('tv');

/* ── Palette ─────────────────────────────────────────────────────────────── */
const CREAM   = '#f4e9dc';
const CREAM2  = '#ecdfcd';
const PAPER   = '#fbf5ec';
const ESPR    = '#2a1512';
const ESPR2   = '#3a2018';
const TERRA   = '#d6552f';
const TERRAD  = '#bf471f';
const OLIVE   = '#6c6a3d';
const TXT     = '#54453d';
const DIM     = '#8a7567';
const LINE    = '#ddcdb8';
const LINE2   = '#c9b49a';
const WHITE   = '#ffffff';

/* ── Helper: section-header centrato con eyebrow + headline ─────────────── */
const shead = (eyebrow, h1, h2, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: TERRA, eyebrow_dot_color: TERRA, eyebrow_separator: '',
  headline_lines: [
    { text: h1, color: ESPR, italic: false },
    ...(h2 ? [{ text: h2, color: TERRA, italic: true }] : []),
  ],
  headline_font_family: 'serif', headline_font_size: 48, headline_font_weight: '600', headline_align: 'center', headline_inline: !!h2,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false, tagline_text_color: DIM, tagline_text_size: 16.5,
  layout: 'center', gap: 16,
});

/* ── Helper: section-header sinistra con eyebrow ────────────────────────── */
const sheadLeft = (eyebrow, h1, h2) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: TERRA, eyebrow_dot_color: TERRA, eyebrow_separator: '',
  headline_lines: [
    { text: h1, color: ESPR, italic: false },
    ...(h2 ? [{ text: h2, color: TERRA, italic: true }] : []),
  ],
  headline_font_family: 'serif', headline_font_size: 44, headline_font_weight: '600', headline_align: 'left', headline_inline: !!h2,
  tagline_show: false, layout: 'stack', gap: 12,
});

const home = [];

/* ══════════════════════════════════════════════════════════════════════════
   1) ANNOUNCEMENT BAR — approssimato con newsticker (strisce testo scorrente)
      Tile originale: AnnouncementBar (NEW, non esiste). Approssima con newsticker.
   ══════════════════════════════════════════════════════════════════════════ */
home.push(sec(ESPR, 'small', [
  row([col('1-1', [tile('newsticker', {
    items: [
      { text: `Open for Sunday lunch from 12:30` },
      { text: `Fresh pasta made daily` },
      { text: `Walk-ins welcome at the bar` },
    ],
    speed: 40, gap: 60, separator: '·',
    text_color: CREAM, accent_color: TERRA,
    font_family: 'sans-serif', font_size: 12, font_weight: '600', letter_spacing: '0.06em',
    bg_color: ESPR, tile_padding: { top: 9, right: 20, bottom: 9, left: 20 },
  })])])
], { padding: 'small' }));

/* ══════════════════════════════════════════════════════════════════════════
   2) HERO — hero-split image-free (pannello showcase con badge "38 years")
      Blueprint: split 1.05fr/.95fr, hero badge circolare "38 / years at the stove",
      CTA: btn--terra "Reserve a table" + btn--ghost "See the menu"
   ══════════════════════════════════════════════════════════════════════════ */
home.push(sec(CREAM, 'large', [row([col('1-1', [tile('hero-split', {
  eyebrow_text: `Cucina di famiglia, dal 1987`, eyebrow_dot_color: TERRA, eyebrow_color: TERRA,
  headline_lines: [
    { text: `A table that\`s`, color: ESPR, italic: false },
    { text: `always set for`, color: ESPR, italic: false },
    { text: 'one more.', color: TERRA, italic: true },
  ],
  headline_font_family: 'serif', headline_font_size: 72, headline_line_height: 1.0, headline_font_weight: '600', headline_align: 'left',
  subhead: `Handmade pasta, a menu that changes with the market, and the kind of long lunch you don\`t want to end. Three generations, one small kitchen.`,
  subhead_color: TXT, subhead_size: 18, subhead_italic: false, subhead_max_width: 460,
  cta1_text: 'Reserve a table', cta1_url: '#reserve', cta1_bg: TERRA, cta1_color: WHITE, cta1_size: 14, cta1_radius: R(2), cta1_radius_hover: R(2),
  cta2_text: 'See the menu', cta2_url: '#menu', cta2_bg: 'transparent', cta2_color: ESPR, cta2_border: LINE2, cta2_size: 14, cta2_radius: R(2), cta2_radius_hover: R(2),
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: CREAM2 },
  showcase_padding: 28, showcase_radius: R(4), showcase_radius_hover: R(4),
  showcase_badge_text: `TAVOLA · DAL 1987`, showcase_badge_dot: TERRA, showcase_badge_bg: ESPR, showcase_badge_color: CREAM,
  showcase_items: [
    { number: `38 years`, text: `at the stove`, italic: false, text_color: ESPR, bg: { type: 'solid', color: PAPER } },
    { number: `Fresh pasta`, text: `every morning`, italic: false, text_color: ESPR, bg: { type: 'solid', color: PAPER } },
    { number: `Three generations`, text: `one kitchen`, italic: false, text_color: TERRA, bg: { type: 'solid', color: PAPER } },
  ],
  showcase_card_radius: R(3), showcase_card_radius_hover: R(3), showcase_card_shadow: 'none',
  showcase_caption_left: 'LA FAMIGLIA', showcase_caption_right: 'BERTOLINI', showcase_hover_effect: 'none',
  split_ratio: '1.05fr .95fr', gap: 48, min_height: 0, tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
})])])]));

/* ══════════════════════════════════════════════════════════════════════════
   3) HOURS STRIP — approssimato con 4 counter stat (NEW tile: HoursStrip non esiste)
      Blueprint: 4 celle grid, .d (day uppercase terra), .t (time disp 21px cream),
      .n (note 12.5px cream/60%). Counter: number=time, label=day. Nota persa (best-effort).
   ══════════════════════════════════════════════════════════════════════════ */
const hourStat = (day, hours) => col('1-4', [tile('counter', {
  number: hours, suffix: '', prefix: '', label: day,
  text_color: TERRA, number_color: CREAM, number_font_size: '21', number_font_weight: '500',
  label_color: TERRA, label_font_size: '11',
  bg_type: 'color', bg_color: 'transparent', padding: '8', border_radius: '0',
  tile_padding: { top: 26, right: 24, bottom: 26, left: 24 },
})]);
home.push(sec(ESPR, 'small', [row([
  hourStat('Tue – Thu', '12:30 – 22:00'),
  hourStat('Fri – Sat', '12:30 – 23:30'),
  hourStat('Sunday',    '12:30 – 16:00'),
  hourStat('Monday',    'Chiuso'),
], { gap: 0 })]));

/* ══════════════════════════════════════════════════════════════════════════
   4) MENU PREVIEW — 2 pricelist affiancati (Antipasti + Primi · Pasta fresca)
      Blueprint: titolo seconda colonna = "Primi · Pasta fresca"
      CTA: singolo bottone btn--espr "Full menu & wine list" centrato
   ══════════════════════════════════════════════════════════════════════════ */
home.push(sec(PAPER, 'large', [
  row([col('1-1', [shead('From the kitchen', `This week\`s`, 'table', `A short menu, cooked properly. Everything\`s made in-house — ask us about today\`s specials on the board.`)])]),
  row([
    col('1-2', [tile('pricelist', {
      section_title: 'Antipasti',
      section_title_color: TERRA,
      section_title_font: 'serif',
      section_title_size: '15',
      section_title_weight: '700',
      section_title_letter_spacing: '0.16em',
      section_title_transform: 'uppercase',
      section_title_border_bottom: LINE,
      items: [
        { id: 'a1', title: 'Burrata & pesche', description: 'Creamy burrata, grilled peaches, basil, toasted almonds.', price: '€12', image_url: '', highlighted: false, badge: '' },
        { id: 'a2', title: 'Vitello tonnato', description: `Thin-sliced veal, caper & tuna cream, the way nonna made it.`, price: '€14', image_url: '', highlighted: false, badge: '' },
        { id: 'a3', title: 'Verdure del banco', description: 'Whatever the market gave us this morning, char-grilled.', price: '€10', image_url: '', highlighted: false, badge: 'Veg' },
      ],
      show_image: false, price_position: 'right',
      separator_style: 'dotted', separator_color: LINE,
      title_color: ESPR, price_color: TERRA, description_color: DIM,
      card_bg: '', card_border_radius: '0', card_border_color: 'transparent', hover_lift: false,
      badge_bg: 'transparent', badge_color: OLIVE, badge_border_color: LINE2, badge_border_width: '1', badge_border_radius: '999',
      gap: '0', tile_padding: { top: 0, right: 0, bottom: 0, left: 0 }, shadow: 'none',
      preset: 'custom',
    })]),
    col('1-2', [tile('pricelist', {
      section_title: `Primi · Pasta fresca`,
      section_title_color: TERRA,
      section_title_font: 'serif',
      section_title_size: '15',
      section_title_weight: '700',
      section_title_letter_spacing: '0.16em',
      section_title_transform: 'uppercase',
      section_title_border_bottom: LINE,
      items: [
        { id: 'p1', title: 'Tagliatelle al ragù', description: 'Eight-hour beef & pork ragù, hand-cut ribbons.', price: '€16', image_url: '', highlighted: true, badge: '' },
        { id: 'p2', title: 'Cacio e pepe', description: 'Tonnarelli, pecorino, a lot of black pepper. That\`s it.', price: '€14', image_url: '', highlighted: false, badge: '' },
        { id: 'p3', title: 'Ravioli di zucca', description: 'Pumpkin & amaretti, brown butter, crisp sage.', price: '€15', image_url: '', highlighted: false, badge: 'Veg' },
      ],
      show_image: false, price_position: 'right',
      separator_style: 'dotted', separator_color: LINE,
      title_color: ESPR, price_color: TERRA, description_color: DIM,
      highlighted_bg: CREAM2,
      card_bg: '', card_border_radius: '0', card_border_color: 'transparent', hover_lift: false,
      badge_bg: 'transparent', badge_color: OLIVE, badge_border_color: LINE2, badge_border_width: '1', badge_border_radius: '999',
      gap: '0', tile_padding: { top: 0, right: 0, bottom: 0, left: 0 }, shadow: 'none',
      preset: 'custom',
    })]),
  ], { gap: 64 }),
  row([col('1-1', [tile('cta-banner', {
    headline: 'Full menu', headline_accent: '& wine list', headline_accent_italic: false,
    subtitle: '',
    cta_text: 'Full menu & wine list', cta_url: '#menu',
    bg: { type: 'none' }, text_color: ESPR, accent_color: TERRA,
    cta_bg: ESPR, cta_color: CREAM, cta_radius: R(2), cta_size: 13,
    headline_font_family: 'sans-serif', headline_size: 0, headline_weight: '600',
    layout: 'inline', vertical_align: 'center', banner_radius: R(0), banner_padding: 0,
  })])], { gap: 0 }),
], { padding: 'large' }));

/* ══════════════════════════════════════════════════════════════════════════
   5) GALLERY / IN THE KITCHEN — section-header + info-cards astratte
      Tile originale: ProductGallery (NEW, masonry 4 col tall/wide — non esiste tile dedicato).
      Approssima con info-cards (6 voci = 6 celle blueprint: rolling pasta, ragù pot,
      market veg, the dining room, espresso, dessert trolley).
      SEGNALATA: fedeltà parziale (manca layout masonry con span tall/wide).
   ══════════════════════════════════════════════════════════════════════════ */
home.push(sec(PAPER, 'large', [
  row([col('1-1', [tile('section-header', {
    eyebrow_show: true, eyebrow_text: 'In the kitchen', eyebrow_color: TERRA, eyebrow_dot_color: TERRA, eyebrow_separator: '',
    headline_lines: [
      { text: 'Made by hand,', color: ESPR, italic: false },
      { text: 'every morning', color: ESPR, italic: false },
    ],
    headline_font_family: 'serif', headline_font_size: 52, headline_font_weight: '600', headline_align: 'left',
    tagline_show: false, layout: 'stack', gap: 12,
  })])]),
  row([col('1-1', [tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' }, container_padding: 0, container_gap: 12, columns: 3, items_gap: 12,
    card_bg: { type: 'solid', color: CREAM2 }, card_color: DIM, card_radius: R(4), card_padding: 24,
    show_icon: true, show_counter: false, show_arrow: false, show_footer: false, show_media: false,
    icon_color: TERRA, icon_bg_color: CREAM, title_color: ESPR,
    title_font_family: 'serif', title_size: 18, title_weight: '600', title_italic: false, description_size: 14,
    items: [
      { icon: 'wheat',       title: 'Rolling pasta',          description: 'Hand-rolled every morning before the kitchen opens. Tagliatelle, tonnarelli, ravioli — all cut by hand.' },
      { icon: 'flame',       title: 'The ragù pot',           description: `The pot goes on at 6am. Beef, pork, soffritto, a splash of milk. Nonna\`s method, unchanged since 1962.` },
      { icon: 'shopping-basket', title: 'Market vegetables',  description: `Whatever looked best at the market is on the board by noon. The menu writes itself.` },
      { icon: 'utensils',    title: 'The dining room',        description: `Eight tables, candlelight after dark, and the sound of the kitchen all evening.` },
      { icon: 'coffee',      title: 'The espresso',           description: `A proper shot from the La Marzocco. Dark roast, crema, served short.` },
      { icon: 'cake',        title: 'Dessert trolley',        description: `Tiramisù, panna cotta, torta della nonna — wheeled to the table the old way.` },
    ],
    card_hover_effect: 'lift',
  })])]),
]));

/* ══════════════════════════════════════════════════════════════════════════
   6) RECIPE SCALER — tile `scaler` interattivo (mode='scale')
      Blueprint: "Nonna's ragù, to scale" / base 4 coperti / ingredienti scalati live.
      base × (slider / base_value) = quantità mostrata.
      frazioni blueprint: Olive oil 3 tbsp, Sea salt 1 tsp (già valori decimali esatti).
   ══════════════════════════════════════════════════════════════════════════ */
home.push(sec(PAPER, 'large', [
  row([col('1-1', [tile('scaler', {
    eyebrow: 'From our kitchen',
    heading: `Nonna\`s ragù, to scale`,
    intro: `The sauce we\`ve simmered since 1962. Set how many you\`re feeding and the quantities follow.`,
    mode: 'scale',
    base_label: 'Coperti',
    base_value: 4,
    base_min: 2,
    base_max: 12,
    base_step: 1,
    base_suffix: '',
    items: [
      { name: 'Beef & pork mince',       amount: 500,  unit: 'g' },
      { name: 'Soffritto, finely diced', amount: 200,  unit: 'g' },
      { name: 'San Marzano tomatoes',    amount: 400,  unit: 'g' },
      { name: 'Whole milk',              amount: 150,  unit: 'ml' },
      { name: 'Red wine',                amount: 120,  unit: 'ml' },
      { name: 'Olive oil',               amount: 3,    unit: 'tbsp' },
      { name: 'Sea salt',                amount: 1,    unit: 'tsp' },
    ],
    show_total: false,
    total_label: '',
    total_unit: '',
    zone_accent: TERRA,
    card_bg: PAPER,
    card_border: `1px solid ${LINE}`,
    align: 'left',
  })])])
]));

/* ══════════════════════════════════════════════════════════════════════════
   7) STORY SPLIT — hero-split image-free senza showcase
      Blueprint: split 1fr/1fr; sinistra = media astratto con tag "Since 1987";
      destra = eyebrow "Our story" + h2 + 2 paragrafi + firma "— La famiglia Bertolini"
      NESSUN showcase card: showcase_enabled:false, nessuna stat.
   ══════════════════════════════════════════════════════════════════════════ */
home.push(sec(CREAM, 'large', [row([col('1-1', [tile('hero-split', {
  eyebrow_text: 'Our story', eyebrow_dot_color: TERRA, eyebrow_color: TERRA,
  headline_lines: [
    { text: 'One small kitchen,', color: ESPR, italic: false },
    { text: 'three generations', color: ESPR, italic: false },
  ],
  headline_font_family: 'serif', headline_font_size: 50, headline_line_height: 1.04, headline_font_weight: '600', headline_align: 'left',
  subhead: `Nonna Lucia opened Tavola with eight tables and one rule: cook for people the way you\`d cook for family. Her ragù still simmers on the same stove.\n\nToday her grandchildren run the pass — same recipes, same market at dawn, the same long Sunday lunches that go on until the coffee\`s cold.\n\n— La famiglia Bertolini`,
  subhead_color: TXT, subhead_size: 16, subhead_italic: false, subhead_max_width: 500,
  cta1_text: '', cta1_url: '', cta2_text: '', cta2_url: '',
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: ESPR },
  showcase_padding: 32, showcase_radius: R(4), showcase_radius_hover: R(4),
  showcase_badge_text: 'Since 1987', showcase_badge_dot: TERRA, showcase_badge_bg: TERRA, showcase_badge_color: WHITE,
  showcase_items: [
    { number: `La regola di nonna`, text: `Cucina per gli ospiti come cucini per la famiglia`, italic: true, text_color: CREAM, bg: { type: 'solid', color: ESPR2 } },
    { number: `Il mercato all\`alba`, text: 'Ogni mattina, prima che apra la cucina', italic: false, text_color: CREAM, bg: { type: 'solid', color: ESPR2 } },
  ],
  showcase_card_radius: R(3), showcase_card_radius_hover: R(3), showcase_card_shadow: 'none',
  showcase_caption_left: '1987', showcase_caption_right: 'OGGI', showcase_hover_effect: 'none',
  split_ratio: '1fr 1fr', gap: 56, min_height: 0, tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
})])])]));

/* ══════════════════════════════════════════════════════════════════════════
   8) TESTIMONIAL
      Blueprint: sfondo espr, stelle ★★★★★, blockquote italic serif, nome terra uppercase
   ══════════════════════════════════════════════════════════════════════════ */
home.push(sec(ESPR, 'large', [row([col('1-1', [tile('testimonial', {
  quote: `"The kind of place you leave already planning your next visit. The tagliatelle is worth the trip on its own — and they treat you like you\`ve been coming for years."`,
  author_name: 'Marco D.', author_role: 'Local guide · 240 reviews', rating: '5',
  layout: 'single', show_line: false, bg_color: 'transparent', text_color: CREAM,
  border_radius: '0', avatar: '',
})])])]));

/* ══════════════════════════════════════════════════════════════════════════
   9) COMPOSE YOUR TABLE — tile `builder` interattivo con stepper +/- e totale live.
      Blueprint: section#compose data-builder data-currency="€" — 6 piatti, nessun cap.
      Corsi: Antipasti (×2) · Primi (×2) · Secondi · Dolci.
      zone_accent/zone_on dal CSS --fx-zone-accent:#d6552f / --fx-zone-on:#fff.
   ══════════════════════════════════════════════════════════════════════════ */
home.push(sec(PAPER, 'large', [
  row([col('1-1', [tile('builder', {
    eyebrow: 'Plan your evening',
    heading: `Compose your table`,
    intro: `Pick a few plates to share — antipasti to dolci. We\`ll total it up so there are no surprises but the good ones.`,
    currency: '€',
    cap: 0,
    total_label: 'Total',
    count_label: 'plates',
    cta_text: 'Book a table',
    cta_url: '#reserve',
    zone_accent: '#d6552f',
    zone_on: '#ffffff',
    card_bg: CREAM2,
    card_border: `1px solid ${LINE}`,
    align: 'left',
    items: [
      { name: `Burrata & peach`,  price: 9,   note: 'Antipasti',       start: false },
      { name: 'Fritto misto',     price: 11,  note: 'Antipasti',       start: false },
      { name: 'Cacio e pepe',     price: 14,  note: 'Primi',           start: false },
      { name: `Wild boar ragù`, price: 16, note: 'Primi',         start: false },
      { name: 'Bistecca, shared', price: 24,  note: 'Secondi',         start: false },
      { name: `Tiramisù`,         price: 8,   note: 'Dolci',           start: false },
    ],
  })])])
]));

/* ══════════════════════════════════════════════════════════════════════════
   10) RESERVATIONS — form prenotazione vera (tile `form`)
       Blueprint: grid .9fr/1.1fr — sinistra = copy (h2 + 2 paragrafi incluso tel/email);
       destra = form con campi: Date/Time (1-2 each), Covers/Name (1-2 each), Phone (1-1),
       submit "Request a table" btn--terra full width.
       Sfondo form = paper, border 1px line, border-radius 6px, padding 30px.
   ══════════════════════════════════════════════════════════════════════════ */
home.push(sec(CREAM2, 'large', [
  row([
    col('2-5', [tile('section-header', {
      eyebrow_show: true, eyebrow_text: 'Reservations', eyebrow_color: TERRA, eyebrow_dot_color: TERRA, eyebrow_separator: '',
      headline_lines: [
        { text: 'Book your', color: ESPR, italic: false },
        { text: 'seat at Tavola', color: ESPR, italic: false },
      ],
      headline_font_family: 'serif', headline_font_size: 44, headline_font_weight: '600', headline_align: 'left',
      tagline_show: true,
      tagline_text: `We hold a few tables for walk-ins, but weekends fill fast. Larger groups and private lunches — just give us a call.\n\n015 88 240 · ciao@tavola.kitchen`,
      tagline_text_italic: false, tagline_text_color: DIM, tagline_text_size: 16,
      layout: 'stack', gap: 16,
    })]),
    col('3-5', [tile('form', {
      fields: [
        { id: 'rv-1', field_type: 'date',   label: 'Date',   name: 'date',   placeholder: '', required: true,  width: '1-2', options: '', icon: 'calendar' },
        { id: 'rv-2', field_type: 'select', label: 'Time',   name: 'time',   placeholder: '', required: true,  width: '1-2', options: "12:30\n13:00\n20:00\n20:30\n21:00", icon: '' },
        { id: 'rv-3', field_type: 'select', label: 'Covers', name: 'covers', placeholder: '', required: true,  width: '1-2', options: "2 people\n4 people\n6 people\n8+ (call us)", icon: '' },
        { id: 'rv-4', field_type: 'text',   label: 'Name',   name: 'name',   placeholder: 'Your name', required: true,  width: '1-2', options: '', icon: 'user' },
        { id: 'rv-5', field_type: 'tel',    label: 'Phone',  name: 'phone',  placeholder: 'So we can confirm', required: false, width: '1-1', options: '', icon: 'phone' },
      ],
      submit_text: 'Request a table',
      submit_full_width: true,
      submit_alignment: 'center',
      email_subject: 'Prenotazione tavolo — Tavola',
      success_message: `Your table request has been received! We\`ll confirm shortly.`,
      /* Stile form dal CSS .tv-resv__form */
      bg: { type: 'solid', color: PAPER },
      input_bg: CREAM, input_color: ESPR, input_border_color: LINE2, input_border_width: '1',
      input_border_style: 'box', input_radius: '3', input_size: 'default',
      input_focus_border: TERRA, input_focus_shadow: true,
      label_color: DIM, label_size: '11', label_weight: '700', label_transform: 'uppercase', label_letter_spacing: '0.1em',
      submit_bg: TERRA, submit_color: WHITE, submit_radius: '2',
      submit_font_size: '14', submit_font_weight: '700', submit_letter_spacing: '0.08',
      submit_text_transform: 'uppercase',
      submit_hover_bg: TERRAD,
      gap: '16',
      tile_padding: { top: 30, right: 30, bottom: 30, left: 30 },
      preset: 'custom',
    })]),
  ], { gap: 52 }),
]));

/* ══════════════════════════════════════════════════════════════════════════
   11) CTA FINALE — bg terracotta, CTA singolo btn--cream
       Blueprint: h2 "Come hungry. / Stay late.", p, btn--cream "Book a table"
       Sfondo: terra con pattern diagonale (riprodotto dal bg tile).
   ══════════════════════════════════════════════════════════════════════════ */
home.push(sec(TERRA, 'large', [row([col('1-1', [tile('cta-banner', {
  headline: 'Come hungry.', headline_accent: 'Stay late.', headline_accent_italic: true,
  subtitle: `There\`s always a seat, a glass of something local, and a plate of pasta with your name on it.`,
  cta_text: 'Book a table', cta_url: '#reserve',
  bg: { type: 'solid', color: TERRA }, text_color: WHITE, accent_color: WHITE, subtitle_color: 'rgba(255,255,255,.9)',
  cta_bg: CREAM, cta_color: ESPR, cta_radius: R(2), cta_size: 14,
  headline_font_family: 'serif', headline_size: 64, headline_weight: '600', subtitle_size: 17,
  layout: 'stack', vertical_align: 'center', banner_radius: R(0), banner_padding: 80,
})])])]));

/* ══════════════════════════════════════════════════════════════════════════
   K.emit — genera i 4 file + diagnostica
   ══════════════════════════════════════════════════════════════════════════ */
K.emit({
  slug: 'tavola', name: 'Tavola',
  tags: ['food', 'restaurant', 'trattoria', 'food-drink'],
  description: `Tavola — family trattoria italiana. Cream/espresso/terracotta palette, Zilla Slab (display) + Karla (body). Menu piatti con pricelist, story split, testimonial, form prenotazione. Riproduzione OLOtheme Tavola (Food & Drink).`,
  colors: {
    primary:          TERRA,
    primary_contrast: WHITE,
    secondary:        OLIVE,
    secondary_contrast: WHITE,
    muted:            CREAM2,
    muted_contrast:   TXT,
    text:             TXT,
    text_muted:       DIM,
    background:       CREAM,
    border:           LINE,
    link:             TERRA,
  },
  css_disp:            `"Zilla Slab", Georgia, serif`,
  css_sans:            `"Karla", -apple-system, sans-serif`,
  heading_weight:      '600',
  heading_line_height: '1.04',
  google_fonts:        ['Zilla Slab', 'Karla'],
  logo_variant:        'dark',
  menu: [
    { title: 'Menu',         url: '#menu' },
    { title: 'Our story',    url: '#story' },
    { title: 'The kitchen',  url: '#gallery' },
    { title: 'Reservations', url: '#reserve' },
  ],
  header: {
    bg:        PAPER,
    text_color: TXT,
    sticky_bg: `rgba(244,233,220,.9)`,
    logo_width: 130,
  },
  footer: {
    bg:        ESPR,
    headColor: CREAM,
    brand: {
      name:    'Tavola',
      tagline: 'Family trattoria. Handmade pasta, a short seasonal menu, and a long table.',
    },
    columns: [
      { title: 'Eat',     links: ['Menu', 'Wine list', 'Specials', 'Reservations'] },
      { title: 'About',   links: ['Our story', 'Private events', 'Work with us'] },
      { title: 'Find us', links: ['9 Via del Forno', 'Tue–Sun from 12:30', '015 88 240', 'ciao@tavola.kitchen'] },
    ],
    bottom: {
      left:  '© 2026 Tavola — an OLOtheme demo.',
      right: 'Built with OLObuild',
    },
  },
  cursor: false,
}, home);
