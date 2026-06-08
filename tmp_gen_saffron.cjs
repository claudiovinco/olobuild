/* Saffron — ricomposizione TILE-PURE (image-free). Food & Drink restaurant. */
/* Palette: ink/cream/saffron/terra. DM Serif Display + Work Sans. */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('sf');

const INK   = '#241a16';
const INK2  = '#312420';
const INK3  = '#41312b';
const SAF   = '#e0a23a';
const SAFD  = '#c8862a';
const TERRA = '#c75d3a';
const CREAM = '#f6efe2';
const CREAM2= '#efe6d4';
const PAPER = '#fffdf8';
const TXT   = '#2a201b';
const SOFT  = '#6e6258';
const FAINT = '#9c8f82';
const LINE  = '#e7ddca';
const LINEDK= 'rgba(255,255,255,.12)';
const WHITE = '#ffffff';

const home = [];

// ─── helpers ────────────────────────────────────────────────────────────────

const shead = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: TERRA, eyebrow_dot_color: TERRA, eyebrow_separator: '',
  headline_lines: [
    { text: l1,     color: INK,   italic: false },
    { text: accent, color: TERRA, italic: true  },
  ],
  headline_font_family: 'serif', headline_font_size: 48, headline_font_weight: '400',
  headline_align: 'center', headline_inline: true,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false,
  tagline_text_color: SOFT, tagline_text_size: 16.5,
  layout: 'center', gap: 16,
});

const sheadLeft = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: TERRA, eyebrow_dot_color: TERRA, eyebrow_separator: '',
  headline_lines: [
    { text: l1,     color: INK,   italic: false },
    { text: accent, color: TERRA, italic: true  },
  ],
  headline_font_family: 'serif', headline_font_size: 44, headline_font_weight: '400',
  headline_align: 'left', headline_inline: true,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false,
  tagline_text_color: SOFT, tagline_text_size: 16,
  layout: 'stack', gap: 14,
});

// ─── 1) HERO (hero-split) ────────────────────────────────────────────────────
home.push(sec(INK, 'large', [row([col('1-1', [tile('hero-split', {
  eyebrow_text: `Seasonal kitchen \xB7 est. 2012 \xB7 Milan`,
  eyebrow_dot_color: SAF, eyebrow_color: SAF,
  headline_lines: [
    { text: `Cooked over`,   color: WHITE, italic: false },
    { text: `fire, served`,  color: WHITE, italic: false },
    { text: `with care.`,    color: SAF,   italic: true  },
  ],
  headline_font_family: 'serif', headline_font_size: 80,
  headline_line_height: 0.98, headline_font_weight: '400', headline_align: 'left',
  subhead: `A short menu that changes with the market, an open kitchen you can watch, and a wine list we actually drink ourselves.`,
  subhead_color: 'rgba(255,255,255,.74)', subhead_size: 18, subhead_italic: false, subhead_max_width: 520,
  cta1_text: `Reserve a table`, cta1_url: '#visit',
  cta1_bg: TERRA, cta1_color: WHITE, cta1_size: 15, cta1_radius: R(999), cta1_radius_hover: R(999),
  cta2_text: `View the menu`, cta2_url: '#menu',
  cta2_bg: 'rgba(255,255,255,.06)', cta2_color: WHITE,
  cta2_border: 'rgba(255,255,255,.30)', cta2_size: 15, cta2_radius: R(999), cta2_radius_hover: R(999),
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: INK2 },
  showcase_padding: 28, showcase_radius: R(14), showcase_radius_hover: R(14),
  showcase_badge_text: `SAFFRON \xB7 MILAN`,
  showcase_badge_dot: SAF, showcase_badge_bg: INK3, showcase_badge_color: WHITE,
  showcase_items: [
    { number: `Since`, text: `2012`, italic: false, text_color: SAF,   bg: { type: 'solid', color: INK3 } },
    { number: `Open fire kitchen`, text: `+14 years`, italic: true, text_color: WHITE, bg: { type: 'solid', color: INK3 } },
    { number: `Via Brera 18`, text: `Milan`, italic: false, text_color: WHITE, bg: { type: 'solid', color: INK3 } },
    { number: `Reservations`, text: `+39 02 555 12`, italic: false, text_color: SAF, bg: { type: 'solid', color: INK3 } },
  ],
  showcase_card_radius: R(10), showcase_card_radius_hover: R(10), showcase_card_shadow: 'none',
  showcase_caption_left: 'THE RESTAURANT', showcase_caption_right: 'MILAN 2026',
  showcase_hover_effect: 'none',
  split_ratio: '1.25fr .75fr', gap: 52, min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
})])])]));

// ─── 2) HOURS STRIP — [best-effort] info-cards orizzontale su striscia scura ──
// HoursStrip non esiste come tile. Blueprint: "Open now · until 23:00" + clock icons + Lunch/Dinner/Address.
// Approssimazione con 3 info-cards icon (clock/clock/map-pin) su sfondo ink semitrasparente.
home.push(sec(INK2, 'small', [row([
  col('1-3', [tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0, container_gap: 0, columns: 1, items_gap: 0,
    card_bg: { type: 'solid', color: 'transparent' },
    card_color: 'rgba(255,255,255,.78)', card_radius: R(0), card_padding: 0,
    show_icon: true, show_counter: false, show_counter_label: false,
    show_arrow: false, show_footer: false, show_media: false,
    icon_color: SAF, icon_bg_color: 'transparent',
    title_color: WHITE,
    title_font_family: 'sans-serif', title_size: 14, title_weight: '600', title_italic: false,
    description_size: 13.5,
    items: [
      { icon: 'clock', title: `Lunch`, description: `12:00 \x2013 14:30` },
    ],
  })]),
  col('1-3', [tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0, container_gap: 0, columns: 1, items_gap: 0,
    card_bg: { type: 'solid', color: 'transparent' },
    card_color: 'rgba(255,255,255,.78)', card_radius: R(0), card_padding: 0,
    show_icon: true, show_counter: false, show_counter_label: false,
    show_arrow: false, show_footer: false, show_media: false,
    icon_color: SAF, icon_bg_color: 'transparent',
    title_color: WHITE,
    title_font_family: 'sans-serif', title_size: 14, title_weight: '600', title_italic: false,
    description_size: 13.5,
    items: [
      { icon: 'clock', title: `Dinner`, description: `19:00 \x2013 23:00` },
    ],
  })]),
  col('1-3', [tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0, container_gap: 0, columns: 1, items_gap: 0,
    card_bg: { type: 'solid', color: 'transparent' },
    card_color: 'rgba(255,255,255,.78)', card_radius: R(0), card_padding: 0,
    show_icon: true, show_counter: false, show_counter_label: false,
    show_arrow: false, show_footer: false, show_media: false,
    icon_color: SAF, icon_bg_color: 'transparent',
    title_color: WHITE,
    title_font_family: 'sans-serif', title_size: 14, title_weight: '600', title_italic: false,
    description_size: 13.5,
    items: [
      { icon: 'map-pin', title: `Via Brera 18, Milan`, description: `+39 02 555 12` },
    ],
  })]),
], { gap: 24 })]));

// ─── 3) STORY SPLIT (hero-split image-free, testo a destra, media a sinistra) ─
// Blueprint: grid 1fr 1fr — media sinistra con badge "12 years" circolare, testo destra.
// hero-split con split_ratio inverso (.75fr 1.25fr): showcase=media a sinistra, testo a destra.
// Badge "12 years" reso come showcase_badge_text; 4 card non presenti nel blueprint → 2 sole voci.
home.push(sec(CREAM, 'large', [row([col('1-1', [tile('hero-split', {
  eyebrow_text: `Our story`, eyebrow_dot_color: TERRA, eyebrow_color: TERRA,
  headline_lines: [
    { text: `A neighbourhood kitchen,`,  color: INK, italic: false },
    { text: `run by the people`,         color: INK, italic: false },
    { text: `who cook in it.`,           color: TERRA, italic: true },
  ],
  headline_font_family: 'serif', headline_font_size: 46,
  headline_line_height: 1.04, headline_font_weight: '400', headline_align: 'left',
  subhead: `Saffron started as a six-table room with one wood oven. We still cook the way we did then \x2014 buying what\x27s good that morning, writing the menu in the afternoon, and serving it the same night. No deliveries we wouldn\x27t eat ourselves, no dish that hasn\x27t earned its place. Just a kitchen that takes its time.`,
  subhead_color: SOFT, subhead_size: 16.5, subhead_italic: false, subhead_max_width: 480,
  cta1_text: `More about us`, cta1_url: '#story',
  cta1_bg: INK, cta1_color: CREAM, cta1_size: 14, cta1_radius: R(999), cta1_radius_hover: R(999),
  cta2_text: '', cta2_url: '',
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: CREAM2 },
  showcase_padding: 32, showcase_radius: R(8), showcase_radius_hover: R(8),
  showcase_badge_text: `12 YEARS \xB7 OPEN KITCHEN`,
  showcase_badge_dot: TERRA, showcase_badge_bg: TERRA, showcase_badge_color: WHITE,
  showcase_items: [
    { number: `Est.`,   text: `2012`,        italic: false, text_color: TERRA, bg: { type: 'solid', color: PAPER } },
    { number: `Tables`, text: `6 \x2192 24`, italic: false, text_color: INK,   bg: { type: 'solid', color: PAPER } },
  ],
  showcase_card_radius: R(8), showcase_card_radius_hover: R(8), showcase_card_shadow: 'none',
  showcase_caption_left: 'OUR STORY', showcase_caption_right: '',
  showcase_hover_effect: 'none',
  split_ratio: '.75fr 1.25fr', gap: 54, min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
})])])]));

// ─── 4) SIGNATURE DISHES (section-header + info-cards 3 col image-free) ──────
// Blueprint: .dish = background:var(--paper), border:1px solid var(--line), border-radius:10px.
// Nome serif 22px + prezzo terra (footer_text) + descrizione 13.5px. NON ha counter numerico.
// info-cards con show_counter:false, show_footer:true per il prezzo (footer_text=€XX).
// border riprodotto via card_border 1px solid LINE.
home.push(sec(CREAM2, 'large', [
  row([col('1-1', [sheadLeft('From the pass', `This week\x27s `, `signatures`, '')])]),
  row([col('1-1', [tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0, container_gap: 20, columns: 3, items_gap: 20,
    card_bg: { type: 'solid', color: PAPER },
    card_border: `1px solid ${LINE}`,
    card_color: SOFT, card_radius: R(10), card_padding: 24,
    show_icon: false, show_counter: false, show_counter_label: false,
    show_arrow: false, show_footer: true, show_media: false,
    title_color: INK,
    title_font_family: 'serif', title_size: 22, title_weight: '400', title_italic: false,
    description_size: 13.5, footer_size: 20, footer_font_family: 'serif',
    items: [
      {
        title: `Fire-roasted quail`,
        description: `Grape must, charred radicchio, toasted hazelnut.`,
        footer_text: `€24`, footer_dot_color: TERRA,
      },
      {
        title: `Saffron tagliolini`,
        description: `Hand-cut pasta, mussels, bottarga, lemon.`,
        footer_text: `€19`, footer_dot_color: TERRA,
      },
      {
        title: `Embered celeriac`,
        description: `Whole-roasted, brown butter, capers, herbs.`,
        footer_text: `€16`, footer_dot_color: SAF,
      },
    ],
    card_hover_effect: 'lift',
  })])])
]));

// ─── 5) MENU LIST (pricelist ×2 colonne: starter + mains su sfondo ink) ──────
// Blueprint: 2 colonne con h3 intestazione "To start" / "Mains" in saffron + border-bottom 1px.
// pricelist ha titoli categoria e voci con prezzo. Le voci "To start"/"Mains" = titolo gruppo.
home.push(sec(INK, 'large', [
  row([col('1-1', [tile('section-header', {
    eyebrow_show: true, eyebrow_text: 'A taste of it',
    eyebrow_color: SAF, eyebrow_dot_color: SAF, eyebrow_separator: '',
    headline_lines: [
      { text: 'On the menu', color: WHITE, italic: false },
      { text: 'tonight',     color: SAF,   italic: true  },
    ],
    headline_font_family: 'serif', headline_font_size: 48,
    headline_font_weight: '400', headline_align: 'center', headline_inline: true,
    tagline_show: false, layout: 'center', gap: 16,
  })])]),
  row([
    col('1-2', [tile('pricelist', {
      show_image: false, price_position: 'right',
      separator_style: 'dashed', separator_color: 'rgba(255,255,255,.10)',
      title_color: WHITE, price_color: SAF,
      title_font_family: 'serif', title_size: 19, title_weight: '400',
      price_font_family: 'serif', price_size: 19,
      description_color: 'rgba(255,255,255,.55)', description_size: 13,
      category_color: SAF, category_font_family: 'serif', category_size: 26,
      category_border_color: LINEDK, category_border_show: true,
      card_bg: 'transparent', card_border_radius: '0',
      badge_bg: 'transparent', badge_color: TERRA, badge_border_color: 'rgba(199,93,58,.5)',
      badge_border_width: '1', badge_border_radius: '999',
      gap: '0', tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
      items: [
        {
          id: 'sf-pl-s0', title: `To start`,
          description: '', price: '', image_url: '', highlighted: false, badge: '', is_category: true,
        },
        {
          id: 'sf-pl-s1', title: `Burrata \x26 winter tomato`,
          description: `Aged balsamic, basil oil, sourdough.`, price: `€14`,
          image_url: '', highlighted: false, badge: '',
        },
        {
          id: 'sf-pl-s2', title: `Beef tartare`,
          description: `Hand-cut, egg yolk, mustard, crisp shallot.`, price: `€16`,
          image_url: '', highlighted: false, badge: 'raw',
        },
        {
          id: 'sf-pl-s3', title: `Charred leeks`,
          description: `Romesco, almond, smoked paprika.`, price: `€12`,
          image_url: '', highlighted: false, badge: 'vg',
        },
      ],
    })]),
    col('1-2', [tile('pricelist', {
      show_image: false, price_position: 'right',
      separator_style: 'dashed', separator_color: 'rgba(255,255,255,.10)',
      title_color: WHITE, price_color: SAF,
      title_font_family: 'serif', title_size: 19, title_weight: '400',
      price_font_family: 'serif', price_size: 19,
      description_color: 'rgba(255,255,255,.55)', description_size: 13,
      category_color: SAF, category_font_family: 'serif', category_size: 26,
      category_border_color: LINEDK, category_border_show: true,
      card_bg: 'transparent', card_border_radius: '0',
      badge_bg: 'transparent', badge_color: TERRA, badge_border_color: 'rgba(199,93,58,.5)',
      badge_border_width: '1', badge_border_radius: '999',
      gap: '0', tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
      items: [
        {
          id: 'sf-pl-m0', title: `Mains`,
          description: '', price: '', image_url: '', highlighted: false, badge: '', is_category: true,
        },
        {
          id: 'sf-pl-m1', title: `Fire-roasted quail`,
          description: `Grape must, charred radicchio, hazelnut.`, price: `€24`,
          image_url: '', highlighted: false, badge: '',
        },
        {
          id: 'sf-pl-m2', title: `Saffron tagliolini`,
          description: `Mussels, bottarga, lemon.`, price: `€19`,
          image_url: '', highlighted: false, badge: '',
        },
        {
          id: 'sf-pl-m3', title: `Dry-aged ribeye`,
          description: `For two \xB7 embers, bone marrow, watercress.`, price: `€32`,
          image_url: '', highlighted: true, badge: '',
        },
      ],
    })]),
  ], { gap: 48 }),
]));

// ─── 6) GALLERY (section-header + galleria image-free con trust-strip etichette) ─
// Blueprint: griglia foto 4 colonne con g-tall/g-wide. IMAGE-FREE: etichette room come pill.
// Sfondo CREAM, non CREAM2 (lo sfondo del blueprint non ha classe sf-menu / sf-sec non ha bg).
home.push(sec(CREAM, 'large', [
  row([col('1-1', [tile('section-header', {
    eyebrow_show: true, eyebrow_text: 'The room',
    eyebrow_color: TERRA, eyebrow_dot_color: TERRA, eyebrow_separator: '',
    headline_lines: [
      { text: 'A look ', color: INK,   italic: false },
      { text: 'inside', color: TERRA,  italic: true  },
    ],
    headline_font_family: 'serif', headline_font_size: 48,
    headline_font_weight: '400', headline_align: 'center', headline_inline: true,
    tagline_show: false,
    layout: 'center', gap: 16,
  })])]),
  row([col('1-1', [tile('trust-strip', {
    items: [
      { text: 'The dining room' },
      { text: 'Open kitchen' },
      { text: 'Wine cellar' },
      { text: 'The terrace' },
      { text: 'Chef\x27s counter' },
      { text: 'Private dining' },
    ],
    variant: 'pill', separator_char: '', align: 'center', flow: 'wrap', gap: 16,
    font_family: 'sans-serif', text_color: SOFT, text_size: 13,
    pill_bg: CREAM2, pill_border: LINE, pill_text_color: SOFT,
  })])]),
]));

// ─── 7) REVIEW (testimonial su sfondo terra) ────────────────────────────────
// Blueprint: .sf-review bg=var(--terra), stelle ★★★★★ + q serif 42px + autore.
// rating:'5' per le stelle; layout single; quote senza virgolette doppie (già nel campo).
home.push(sec(TERRA, 'large', [row([col('1-1', [tile('testimonial', {
  quote: `The kind of place you cancel other plans for. Everything tasted like someone cared \x2014 because they clearly do.`,
  author_name: 'Gambero Rosso', author_role: 'Two forks', rating: '5',
  layout: 'single', show_line: false,
  bg_color: 'transparent', text_color: WHITE,
  quote_font_family: 'serif', quote_size: 38, quote_weight: '400',
  border_radius: '0', avatar: '',
})])])]));

// ─── 8) MOOD FINDER — tile finder ────────────────────────────────────────────
// Blueprint: .sf-find-sec bg=var(--cream-2); --fx-zone-accent:#e0a23a; --fx-zone-on:#241a16
home.push(sec(CREAM2, 'large', [
  row([col('1-1', [tile('finder', {
    eyebrow: `Tonight, I feel like…`,
    heading: `What are you in the mood for?`,
    intro: ``,
    zone_accent: SAF,
    zone_on: INK,
    card_bg: PAPER,
    card_border: `1px solid ${LINE}`,
    align: `center`,
    items: [
      {
        option: `Something light`,
        title: `The Garden Menu`,
        text: `Three lighter courses — a bright ceviche, grilled stone fruit salad and a citrus sorbet. Lunch that doesn’t slow you down.`,
        meta: `3 courses \xB7 €42`,
        cta_text: ``,
        cta_url: `#`,
        icon: ``,
      },
      {
        option: `The full feast`,
        title: `The Saffron Feast`,
        text: `Our full tasting — seven courses, the kitchen’s choice, paired if you like. Settle in; you’re here for the evening.`,
        meta: `7 courses \xB7 €98 \xB7 pairing +€55`,
        cta_text: ``,
        cta_url: `#`,
        icon: ``,
      },
      {
        option: `Drinks & small plates`,
        title: `At the Bar`,
        text: `A negroni flight and a run of small plates — spiced almonds, croquetas, the famous flatbread. Low lights, no rush.`,
        meta: `small plates from €8`,
        cta_text: ``,
        cta_url: `#`,
        icon: ``,
      },
      {
        option: `All vegetarian`,
        title: `The Green Tasting`,
        text: `A full vegetarian journey that never feels like the afterthought — five courses built around the market that morning.`,
        meta: `5 courses \xB7 €68`,
        cta_text: ``,
        cta_url: `#`,
        icon: ``,
      },
    ],
  })])])
]));

// ─── 9) FLOOR-PLAN PICKER → tile hotspots ────────────────────────────────────
// Blueprint: section#table data-olo-tile="FloorPlanPicker" — 8 tavoli su pianta con
// posizioni left/top % estratte dal CSS inline. Dining room (T1–T5) + Terrace (P1–P3).
// hotspots mappa bene: ogni tavolo = marker con x/y; panel_label = zona; meta = posti/stato.
// zone_accent: SAF (#e0a23a); panel_bg scuro ink; card_bg carta chiara; sfondo sezione CREAM2.
home.push(sec(CREAM2, 'large', [row([col('1-1', [tile('hotspots', {
  eyebrow: `Reserve your spot`,
  heading: `Pick your table`,
  intro: `Tap a table to hold it. Dining room or terrace \x2014 the choice is yours.`,
  panel_label: `DINING ROOM \xB7 TERRACE`,
  aspect_ratio: `16/10`,
  zone_accent: SAF,
  zone_on: INK,
  panel_bg: INK,
  card_bg: PAPER,
  card_border: LINE,
  align: `left`,
  items: [
    { x: 16, y: 32, title: `T1 \xB7 Window`,  text: `Window table, dining room.`, meta: `2 seats` },
    { x: 16, y: 70, title: `T2 \xB7 Window`,  text: `Window table, dining room.`, meta: `2 seats` },
    { x: 38, y: 34, title: `T3 \xB7 Centre`,  text: `Central table, dining room.`, meta: `4 seats` },
    { x: 38, y: 72, title: `T4`,              text: `Dining room table.`,          meta: `4 seats \xB7 taken` },
    { x: 52, y: 52, title: `T5 \xB7 Booth`,   text: `Booth, dining room.`,         meta: `6 seats` },
    { x: 78, y: 30, title: `P1 \xB7 Terrace`, text: `Terrace table.`,              meta: `2 seats` },
    { x: 78, y: 58, title: `P2`,              text: `Terrace table.`,              meta: `2 seats \xB7 taken` },
    { x: 88, y: 82, title: `P3 \xB7 Terrace`, text: `Terrace table.`,              meta: `4 seats` },
  ],
})])])]));


// ─── 10) CTA PRENOTAZIONE (cta-banner) ──────────────────────────────────────
// Blueprint: .sf-cta — sfondo media con overlay ink (.sf-cta__grad), eyebrow "Reservations",
// h2 "Come and <em>sit with us</em>", p, btn--saffron (saffron bg, ink text). UN solo bottone.
// Nessun cta2. Layout centrato. bg solido scuro come sostituto image-free.
home.push(sec(INK, 'large', [row([col('1-1', [tile('cta-banner', {
  eyebrow: 'Reservations',
  headline: 'Come and ',
  headline_accent: 'sit with us',
  headline_accent_italic: true,
  subtitle: `We hold a few walk-in seats at the counter every night, but tables are best booked ahead \x2014 especially at weekends.`,
  cta_text: `Book a table`, cta_url: '#visit',
  cta2_text: '', cta2_url: '',
  bg: { type: 'solid', color: '#2d1e18' },
  text_color: WHITE, accent_color: SAF, subtitle_color: 'rgba(255,255,255,.74)',
  cta_bg: SAF, cta_color: INK, cta_radius: R(999), cta_size: 15,
  headline_font_family: 'serif', headline_size: 58, headline_weight: '400',
  subtitle_size: 17,
  layout: 'stack', vertical_align: 'center',
  banner_radius: R(20), banner_padding: 80,
})])])]));

// ─── emit ────────────────────────────────────────────────────────────────────
K.emit({
  slug: 'saffron',
  name: 'Saffron',
  tags: ['food', 'restaurant', 'drink', 'hospitality'],
  description: `Saffron \x2014 seasonal kitchen restaurant. Warm cream/ink/saffron/terra palette. DM Serif Display (display) + Work Sans (body). 10 sezioni tile-pure: hero, hours-strip, story, dishes, menu, gallery, review, mood-finder, table-picker, CTA. Riproduzione fedele dell\x27OLOtheme Saffron (Food \x26 Drink).`,
  colors: {
    primary: TERRA,
    primary_contrast: WHITE,
    secondary: SAF,
    secondary_contrast: INK,
    muted: CREAM2,
    muted_contrast: TXT,
    text: TXT,
    text_muted: SOFT,
    background: CREAM,
    border: LINE,
    link: TERRA,
  },
  css_disp: `"DM Serif Display", Georgia, serif`,
  css_sans: `"Work Sans", -apple-system, sans-serif`,
  heading_weight: '400',
  heading_line_height: '1.04',
  google_fonts: ['DM Serif Display', 'Work Sans'],
  logo_variant: 'dark',
  menu: [
    { title: 'Menu',         url: '#menu'    },
    { title: 'Our Story',    url: '#story'   },
    { title: 'Gallery',      url: '#gallery' },
    { title: 'Visit',        url: '#visit'   },
  ],
  header: {
    bg: 'rgba(36,26,22,.9)',
    text_color: 'rgba(255,255,255,.66)',
    sticky_bg: 'rgba(36,26,22,.96)',
    logo_width: 130,
  },
  footer: {
    bg: INK,
    headColor: SAF,
    brand: {
      name: 'Saffron',
      tagline: `A seasonal neighbourhood kitchen. Open fire, market produce, a short honest list.`,
    },
    columns: [
      { title: 'Visit',  links: ['Menu', 'Our story', 'Gallery', 'Reservations'] },
      { title: 'Hours',  links: [`Lunch \xB7 12:00\x2013 14:30`, `Dinner \xB7 19:00\x2013 23:00`, 'Closed Mondays'] },
      { title: 'Find us', links: ['Via Brera 18, Milan', '+39 02 555 12', 'hello@saffron.kitchen'] },
    ],
    bottom: {
      left:  `\xA9 2026 Saffron \x2014 an OLOtheme demo.`,
      right: 'Built with OLObuild',
    },
  },
  cursor: false,
}, home);
