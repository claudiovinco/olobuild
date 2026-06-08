/* Voyage — ricomposizione TILE-PURE (image-free). Travel journal. Navy + coral. */
/* Vollkorn (display serif) + Figtree (sans). */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('vy');

// ── PALETTE da :root voyage.css ──────────────────────────────────────────────
const BG     = '#141d2e';
const BG2    = '#172338';
const PANEL  = '#1e2a40';
const PANEL2 = '#27344c';
const INK    = '#0c1320';
const CORAL  = '#ff8c69';
const CORALX = '#ffa585';
const SKY    = '#7fb5d6';
const CREAM  = '#eef2f7';
const TXT    = '#a6b2c4';
const DIM    = '#6c7a90';
const NUM_DIM = '#37445c';   // .vy-most__n{color:#37445c} — NON LINE2
const LINE   = 'rgba(238,242,247,.12)';
const LINE2  = 'rgba(255,140,105,.42)';

const home = [];

// helper: section-header centrato con eyebrow
const shead = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: CORAL, eyebrow_dot_color: CORAL, eyebrow_separator: '',
  headline_lines: [
    { text: l1, color: CREAM, italic: false },
    { text: accent, color: CORAL, italic: true },
  ],
  headline_font_family: 'serif', headline_font_size: 44, headline_font_weight: '700',
  headline_align: 'center', headline_inline: true,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false,
  tagline_text_color: TXT, tagline_text_size: 16,
  layout: 'center', gap: 16,
});

// ─────────────────────────────────────────────────────────────────────────
// 1) FEATURED STORY (.vy-lead)
//    Blueprint: 2 col (1.15fr .85fr) — left: media placeholder, right: kicker+h1+stand+byline
//    Tile: hero-split senza showcase (NO stats panel nel blueprint)
// ─────────────────────────────────────────────────────────────────────────
home.push(sec(BG, 'large', [row([col('1-1', [tile('hero-split', {
  eyebrow_text: 'The Long Way · Patagonia',
  eyebrow_dot_color: CORAL,
  eyebrow_color: CORAL,
  headline_lines: [
    { text: 'The road that goes nowhere,', color: CREAM, italic: false },
    { text: 'slowly', color: CORAL, italic: true },
  ],
  headline_font_family: 'serif',
  headline_font_size: 64,
  headline_line_height: 1.04,
  headline_font_weight: '700',
  headline_align: 'left',
  // .vy-lead .stand — font-family:var(--disp); font-style:italic; font-size:19px; color:var(--cream)
  subhead: `Forty hours of gravel, three flat tyres and one unrepeatable silence. What we found at the end of the Carretera Austral — and what we found out about ourselves.`,
  subhead_color: CREAM,
  subhead_size: 19,
  subhead_italic: true,
  subhead_max_width: 540,
  // .byline — font-size:12.5px; text-transform:uppercase; color:var(--txt-dim)
  cta1_text: 'Read the story',
  cta1_url: '#dispatches',
  cta1_bg: CORAL,
  cta1_color: INK,
  cta1_size: 13,
  cta1_radius: R(999),
  cta1_radius_hover: R(999),
  cta2_text: 'All dispatches',
  cta2_url: '#dispatches',
  cta2_bg: 'transparent',
  cta2_color: CREAM,
  cta2_border: LINE2,
  cta2_size: 13,
  cta2_radius: R(999),
  cta2_radius_hover: R(999),
  stats: [],
  // No showcase panel — il blueprint .vy-lead NON ha pannello statistiche
  showcase_enabled: false,
  split_ratio: '1.15fr .85fr',
  gap: 48,
  min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
})])])]));

// ─────────────────────────────────────────────────────────────────────────
// 2) LATEST DISPATCHES (.vy-sec / StoryRail)
//    Blueprint: .vy-rule h2 "Latest dispatches" + span.more "← drag · all stories →"
//    4 × .vy-card: kicker (category·tag) + h3 + p + .meta (author · min)
//    Tile: section-header (regola) + info-cards 4-col con kicker nel counter_label,
//    media placeholder, footer per autore·durata
//    SEGNALATO: drag-scroll rail → approssimato con 4 card statiche
// ─────────────────────────────────────────────────────────────────────────
home.push(sec(BG2, 'large', [
  row([col('1-1', [tile('section-header', {
    eyebrow_show: false,
    headline_lines: [
      { text: 'Latest dispatches', color: CREAM, italic: false },
    ],
    headline_font_family: 'serif', headline_font_size: 38, headline_font_weight: '700',
    headline_align: 'left',
    // .vy-rule .more — font-size:12px; letter-spacing:.06em; text-transform:uppercase; color:var(--coral)
    tagline_show: true,
    tagline_text: '← drag · all stories →',
    tagline_text_color: CORAL,
    tagline_text_size: 12,
    tagline_text_italic: false,
    layout: 'split',
    gap: 0,
  })])]),
  row([col('1-1', [tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0,
    container_gap: 0,
    columns: 4,
    items_gap: 22,
    // .vy-card — no background, no border; solo media+kicker+h3+p+meta
    card_bg: { type: 'solid', color: 'transparent' },
    card_color: DIM,
    card_radius: R(0),
    card_padding: 0,
    card_border: '',
    card_hover_effect: 'none',
    // media placeholder — .vy-card__media aspect-ratio:3/2; border-radius:8px
    show_media: true,
    media_aspect_ratio: '3/2',
    media_radius: R(8),
    media_radius_hover: R(8),
    media_position: 'top',
    show_icon: false,
    // counter_label = kicker (categoria)
    show_counter: true,
    show_counter_label: true,
    counter_size: 0,          // nasconde il numero, mostra solo il label
    show_arrow: false,
    show_footer: true,
    // .vy-card h3 — font:var(--disp); font-size:24px
    title_color: CREAM,
    title_font_family: 'serif',
    title_size: 24,
    title_weight: '700',
    title_italic: false,
    // .vy-card p — font-size:14px; color:var(--txt-dim)
    description_size: 14,
    // .vy-card .meta — font-size:11.5px; text-transform:uppercase; color:var(--txt-dim)
    footer_size: 11,
    items: [
      {
        counter: '',
        counter_label: 'Essay · Rail',
        title: 'Sleeping across a continent',
        description: 'In praise of the night train, and arriving rumpled.',
        footer_text: 'A. Demir · 9 min',
        footer_dot_color: CORAL,
        media_label: 'night train',
      },
      {
        counter: '',
        counter_label: 'Guide · Food',
        title: 'Eat where the drivers eat',
        description: 'A field guide to the roadside, from Oaxaca to Hanoi.',
        footer_text: 'J. Okoro · 7 min',
        footer_dot_color: CORAL,
        media_label: 'market food',
      },
      {
        counter: '',
        counter_label: 'Dispatch · Islands',
        title: 'The last ferry of the season',
        description: 'What a Greek island becomes when the boats stop.',
        footer_text: 'L. Haddad · 11 min',
        footer_dot_color: CORAL,
        media_label: 'island ferry',
      },
      {
        counter: '',
        counter_label: 'Essay · Desert',
        title: 'A week with no signal',
        description: 'What the silence of the Sahara does to a restless mind.',
        footer_text: 'R. Bianchi · 13 min',
        footer_dot_color: CORAL,
        media_label: 'desert sky',
      },
    ],
  })])], { gap: 22 }),
]));

// ─────────────────────────────────────────────────────────────────────────
// 3) FOLLOW THE ROUTE (.vy-sec / RouteScrubber)
//    BEST-EFFORT: RouteScrubber interattivo → step-timeline 5 tappe (statico)
//    .vy-rule: h2 "Follow the route" + span.more "Carretera Austral · 1,240 km south"
//    5 pannelli: kicker (Day N · Km N) + h3 + .stand (italic serif) + p
// ─────────────────────────────────────────────────────────────────────────
home.push(sec(BG, 'large', [
  row([col('1-1', [tile('section-header', {
    eyebrow_show: false,
    headline_lines: [{ text: 'Follow the route', color: CREAM, italic: false }],
    headline_font_family: 'serif', headline_font_size: 38, headline_font_weight: '700',
    headline_align: 'left',
    tagline_show: true,
    tagline_text: 'Carretera Austral · 1,240 km south',
    tagline_text_color: CORAL,
    tagline_text_size: 12,
    tagline_text_italic: false,
    layout: 'split',
    gap: 0,
  })])]),
  row([col('1-1', [tile('step-timeline', {
    items: [
      {
        counter: '01',
        tag_text: 'DAY 1 · KM 0',
        tag_dot_color: CORAL,
        media_label: `VILLA O'HIGGINS`,
        media_type: 'placeholder',
        media_content: '',
        media_image: '',
        media_bg: PANEL2,
        media_color: CORAL,
        pre_title: 'START',
        title: `Villa O'Higgins`,
        title_accent: '',
        title_accent_italic: false,
        title_after: '',
        title_after_italic: false,
        // .vy-scrub__b .stand — font:var(--disp); font-style:italic; font-size:19px
        description: `The road begins exactly where the next one gives up. End of the line, southbound. A single pump, a hand-painted sign, and 1,240 kilometres of gravel waiting to the north. We aired down the tyres and didn't speak for an hour.`,
        footer_value: 'Km 0',
        footer_label: 'START',
        separator_text: '→',
      },
      {
        counter: '02',
        tag_text: 'DAY 3 · KM 228',
        tag_dot_color: CORAL,
        media_label: 'CALETA TORTEL',
        media_type: 'placeholder',
        media_content: '',
        media_image: '',
        media_bg: PANEL2,
        media_color: SKY,
        pre_title: 'VILLAGE',
        title: 'Caleta Tortel',
        title_accent: '',
        title_accent_italic: false,
        title_after: '',
        title_after_italic: false,
        description: `A village with no streets — only cypress boardwalks. You leave the truck at the top and walk in. Every house on stilts, every path made of planks, the whole place smelling of woodsmoke and wet timber.`,
        footer_value: 'Km 228',
        footer_label: 'WAYPOINT',
        separator_text: '→',
      },
      {
        counter: '03',
        tag_text: 'DAY 6 · KM 612',
        tag_dot_color: CORAL,
        media_label: `RÍO TRANQUILO`,
        media_type: 'placeholder',
        media_content: '',
        media_image: '',
        media_bg: PANEL2,
        media_color: SKY,
        pre_title: 'MARBLE CAVES',
        title: `Río Tranquilo`,
        title_accent: '',
        title_accent_italic: false,
        title_after: '',
        title_after_italic: false,
        description: `Water so blue it looks edited. We rowed out at first light to the marble caves — caverns the lake has been polishing for six thousand years. The guide cut the motor and let us just listen to it drip.`,
        footer_value: 'Km 612',
        footer_label: 'WAYPOINT',
        separator_text: '→',
      },
      {
        counter: '04',
        tag_text: 'DAY 9 · KM 920',
        tag_dot_color: CORAL,
        media_label: 'CERRO CASTILLO',
        media_type: 'placeholder',
        media_content: '',
        media_image: '',
        media_bg: PANEL2,
        media_color: CORAL,
        pre_title: 'PEAK HIKE',
        title: 'Cerro Castillo',
        title_accent: '',
        title_accent_italic: false,
        title_after: '',
        title_after_italic: false,
        description: `A full day on foot to the lagoon beneath the spires. Wind that knocks you sideways, then ten minutes of total stillness that made the whole trip worth it.`,
        footer_value: 'Km 920',
        footer_label: 'WAYPOINT',
        separator_text: '→',
      },
      {
        counter: '05',
        tag_text: 'DAY 11 · KM 1,240',
        tag_dot_color: CORAL,
        media_label: 'COYHAIQUE',
        media_type: 'placeholder',
        media_content: '',
        media_image: '',
        media_bg: PANEL2,
        media_color: CORALX,
        pre_title: 'END',
        title: 'Coyhaique',
        title_accent: `— tarmac again`,
        title_accent_italic: true,
        title_after: '',
        title_after_italic: false,
        description: `Tarmac again — and a strange reluctance to reach it. Pavement, a proper coffee, phone signal. The road's over. We sat in the plaza too long, already plotting the way we'd come back and do it slower.`,
        footer_value: 'Km 1,240',
        footer_label: 'FINISH',
        separator_text: '',
      },
    ],
    show_timeline: true,
    timeline_line_color: LINE2,
    timeline_dot_color: CORAL,
    timeline_dot_size: 12,
    timeline_height: 2,
    timeline_margin_bottom: 40,
    counter_font_family: 'serif',
    counter_size: 80,
    counter_color: LINE2,
    counter_italic: true,
    counter_weight: '700',
    tag_size: 11,
    tag_color: TXT,
    media_aspect_ratio: '16/10',   // .vy-scrub__panel .media{aspect-ratio:16/10}
    media_radius: R(8),
    media_radius_hover: R(8),
    media_shadow: 'none',
    show_media_label: true,
    pre_title_size: 11,
    pre_title_color: DIM,
    title_font_family: 'serif',
    title_size: 26,
    title_weight: '700',
    title_color: CREAM,
    title_accent_color: CORAL,
    description_size: 14,          // .vy-scrub__b p{font-size:14.5px}
    description_color: TXT,        // color:var(--txt-dim)
    footer_icon: 'map-pin',
    footer_value_size: 16,
    footer_value_color: CREAM,
    footer_label_size: 10,
    footer_label_color: DIM,
    separator_color: CORAL,
    show_separator: true,
    columns: 5,
    gap: 24,
    items_align: 'start',
  })])]),
]));

// ─────────────────────────────────────────────────────────────────────────
// 4) BY DESTINATION (.vy-sec.panel / ArticleGrid)
//    Blueprint: section bg = var(--bg-2) (#172338) = BG2 — classe .panel
//    .vy-rule: h2 "By destination" + span.more "Atlas →"
//    3 × .vy-dest: media 4/5 + h3 + p — NO card bg, NO border; transparent
//    hover: h3 color→coral; media scale(1.04)
// ─────────────────────────────────────────────────────────────────────────
home.push(sec(BG2, 'large', [
  row([col('1-1', [tile('section-header', {
    eyebrow_show: false,
    headline_lines: [{ text: 'By destination', color: CREAM, italic: false }],
    headline_font_family: 'serif', headline_font_size: 38, headline_font_weight: '700',
    headline_align: 'left',
    tagline_show: true,
    tagline_text: 'Atlas →',
    tagline_text_color: CORAL,
    tagline_text_size: 12,
    tagline_text_italic: false,
    layout: 'split',
    gap: 0,
  })])]),
  row([col('1-1', [tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0,
    container_gap: 0,
    columns: 3,
    items_gap: 30,
    // .vy-dest — NO background, NO border; solo media+titolo+desc
    card_bg: { type: 'solid', color: 'transparent' },
    card_color: DIM,
    card_radius: R(0),
    card_padding: 0,
    card_border: '',
    card_hover_effect: 'lift',
    // .vy-dest__media — border-radius:8px; aspect-ratio:4/5
    show_media: true,
    media_aspect_ratio: '4/5',
    media_radius: R(8),
    media_radius_hover: R(8),
    media_position: 'top',
    show_icon: false,
    show_counter: false,
    show_arrow: false,
    show_footer: false,
    // .vy-dest h3 — font:var(--disp); font-size:22px; hover→coral
    title_color: CREAM,
    title_font_family: 'serif',
    title_size: 22,
    title_weight: '700',
    title_italic: false,
    // .vy-dest p — font-size:14px; color:var(--txt-dim); margin:6px 0 0
    description_size: 14,
    items: [
      {
        title: 'Portugal, slowly',
        description: 'Eight stories from the road between Porto and the south.',
        media_label: 'Lisbon',
      },
      {
        title: 'Japan by rail',
        description: `Beyond the bullet train — the slow lines that locals love.`,
        media_label: 'Japan rail',
      },
      {
        title: 'The Atlas & beyond',
        description: 'Mountains, medinas and the long way to the dunes.',
        media_label: 'Morocco',
      },
    ],
  })])]),
]));

// ─────────────────────────────────────────────────────────────────────────
// 5) COLUMNS + MOST READ (.vy-sec / HoverList)
//    Blueprint: .vy-cols{grid-template-columns:1.5fr 1fr; gap:48px}
//    Left (.vy-dests 2-col): 2 × .vy-dest (media 4/5 + h3 + p) — transparent
//    Right: .vy-rule h2 "Most read" + .vy-most (bordered list)
//      .vy-most{border-top:2px solid var(--coral)}
//      4 × .vy-most__i: .vy-most__n (serif 30px #37445c) + kicker + h4 + .meta
// ─────────────────────────────────────────────────────────────────────────
home.push(sec(BG, 'large', [
  row([
    // ── Left: Columns ──
    col('3-5', [
      tile('section-header', {
        eyebrow_show: false,
        headline_lines: [{ text: 'Columns', color: CREAM, italic: false }],
        headline_font_family: 'serif', headline_font_size: 36, headline_font_weight: '700',
        headline_align: 'left',
        tagline_show: false, layout: 'stack', gap: 16,
      }),
      tile('info-cards', {
        container_bg: { type: 'solid', color: 'transparent' },
        container_padding: 0,
        container_gap: 0,
        columns: 2,
        items_gap: 30,
        // .vy-dest — transparent, no border (stesso stile delle destinations)
        card_bg: { type: 'solid', color: 'transparent' },
        card_color: DIM,
        card_radius: R(0),
        card_padding: 0,
        card_border: '',
        card_hover_effect: 'lift',
        show_media: true,
        media_aspect_ratio: '4/5',
        media_radius: R(8),
        media_radius_hover: R(8),
        media_position: 'top',
        show_icon: false,
        show_counter: false,
        show_arrow: false,
        show_footer: false,
        title_color: CREAM,
        title_font_family: 'serif',
        title_size: 22,
        title_weight: '700',
        title_italic: false,
        description_size: 14,
        items: [
          {
            title: 'The carry-on',
            description: 'One bag, every trip. A column on travelling light.',
            media_label: 'packing',
          },
          {
            title: 'Lost in translation',
            description: 'The joy of getting the words almost right.',
            media_label: 'phrasebook',
          },
        ],
      }),
    ]),
    // ── Right: Most read ──
    col('2-5', [
      tile('section-header', {
        eyebrow_show: false,
        headline_lines: [{ text: 'Most read', color: CREAM, italic: false }],
        headline_font_family: 'serif', headline_font_size: 36, headline_font_weight: '700',
        headline_align: 'left',
        tagline_show: false, layout: 'stack', gap: 16,
      }),
      tile('info-cards', {
        container_bg: { type: 'solid', color: 'transparent' },
        container_padding: 0,
        container_gap: 0,
        columns: 1,
        items_gap: 0,
        // .vy-most__i — border-bottom:1px solid var(--line); padding:18px 0
        // .vy-most — border-top:2px solid var(--coral)
        card_bg: { type: 'solid', color: 'transparent' },
        card_color: TXT,
        card_radius: R(0),
        card_padding: 18,
        card_border: LINE,
        card_hover_effect: 'none',
        show_media: false,
        show_icon: false,
        show_counter: true,
        show_counter_label: true,
        // .vy-most__n — font:var(--disp); font-size:30px; color:#37445c; width:38px
        counter_size: 30,
        // counter color driven by card_accent_color
        card_accent_color: NUM_DIM,    // #37445c — colore esatto dal CSS
        show_arrow: false,
        show_footer: true,
        // .vy-most__c .kicker — come counter_label
        // .vy-most__c h4 — font:var(--disp); font-size:18px
        title_color: CREAM,
        title_font_family: 'serif',
        title_size: 18,
        title_weight: '700',
        title_italic: false,
        description_size: 0,
        // .vy-most__c .meta — font-size:11px; text-transform:uppercase; color:var(--txt-dim)
        footer_size: 11,
        items: [
          {
            counter: '1',
            counter_label: 'Rail',
            title: 'The 1,000-mile breakfast',
            footer_text: '6 min',
            footer_dot_color: CORAL,
          },
          {
            counter: '2',
            counter_label: 'Cities',
            title: 'How to be a regular for a week',
            footer_text: '8 min',
            footer_dot_color: CORAL,
          },
          {
            counter: '3',
            counter_label: 'Gear',
            title: 'The only five things you need',
            footer_text: '5 min',
            footer_dot_color: CORAL,
          },
          {
            counter: '4',
            counter_label: 'Essay',
            title: 'On coming home',
            footer_text: '10 min',
            footer_dot_color: CORAL,
          },
        ],
      }),
    ]),
  ], { gap: 48 }),
]));

// ─────────────────────────────────────────────────────────────────────────
// 6) NEWSLETTER (.vy-news)
//    Blueprint: .vy-news{bg:var(--coral); border-radius:14px; text-align:center}
//    h2 "Dispatches, every Sunday" + p + form (email input + Subscribe btn)
//    .vy-news__form: max-width:420px; input pill (border-radius:999px)
//    btn{bg:var(--ink); color:var(--cream)}
//    Tile: newsletter (layout horizontal, bg coral, input/btn stile esatto)
// ─────────────────────────────────────────────────────────────────────────
home.push(sec(BG2, 'large', [row([col('1-1', [tile('newsletter', {
  title: 'Dispatches, every Sunday',
  subtitle: 'One long read and three short ones from the road — free, no noise, unsubscribe whenever.',
  icon_type: 'none',
  icon_name: '',
  layout: 'horizontal',
  show_name: false,
  email_placeholder: 'you@example.com',
  button_text: 'Subscribe free',
  button_icon: false,
  privacy_text: '',
  privacy_required: false,
  // contenitore: .vy-news — bg:coral, radius:14px, padding:56px, center
  bg_color: CORAL,
  box_border: '',
  border_radius: 14,
  tile_padding: { top: 56, right: 56, bottom: 56, left: 56 },
  max_width: '0',   // full width
  alignment: 'center',
  // tipografia: h2 → font:var(--disp); font-size:46px; color:var(--ink)
  title_size: '46',
  title_weight: '700',
  title_color: INK,
  subtitle_size: '16',
  subtitle_color: 'rgba(12,19,32,.78)',
  // input: .vy-news__form input — bg:rgba(12,19,32,.1); border:1px solid rgba(12,19,32,.25); radius:999px; font-size:15px; color:var(--ink)
  input_bg: 'rgba(12,19,32,.10)',
  input_color: INK,
  input_border: 'rgba(12,19,32,.25)',
  input_focus_border: CORAL,
  input_radius: 999,
  input_height: '50',
  // btn: .vy-news__form .btn — bg:var(--ink); color:var(--cream); radius:999px
  btn_bg: INK,
  btn_color: CREAM,
  btn_hover_bg: PANEL,
  btn_radius: 999,
  btn_font_size: '13',
  btn_font_weight: '700',
  shadow: 'none',
})])])]));

// ─────────────────────────────────────────────────────────────────────────
// EMIT
// ─────────────────────────────────────────────────────────────────────────
K.emit({
  slug: 'voyage',
  name: 'Voyage',
  tags: ['travel', 'journal', 'editorial', 'magazine'],
  description: `Voyage — travel journal magazine. Navy + coral, Vollkorn (display) + Figtree. Featured story hero, dispatches grid, route step-timeline (5 tappe Carretera Austral), destinations, columns/most-read, newsletter. Riproduzione fedele dell'OLOtheme Voyage.`,
  colors: {
    primary: CORAL,
    primary_contrast: INK,
    secondary: SKY,
    secondary_contrast: INK,
    muted: BG2,
    muted_contrast: TXT,
    text: TXT,
    text_muted: DIM,
    background: BG,
    border: LINE,
    link: CORAL,
  },
  css_disp: `"Vollkorn", Georgia, serif`,
  css_sans: `"Figtree", -apple-system, sans-serif`,
  heading_weight: '700',
  heading_line_height: '1.1',
  google_fonts: ['Vollkorn', 'Figtree'],
  logo_variant: 'light',
  menu: [
    { title: 'Dispatches', url: '#dispatches' },
    { title: 'Destinations', url: '#destinations' },
    { title: 'Columns', url: '#columns' },
    { title: 'Guides', url: '#guides' },
  ],
  header: { bg: 'rgba(20,29,46,.85)', text_color: DIM, sticky_bg: 'rgba(20,29,46,.92)', logo_width: 130 },
  footer: {
    bg: BG2,
    headColor: CREAM,
    brand: { name: 'Voyage', tagline: 'A travel journal. Slow, photo-led dispatches from the road since 2017.' },
    columns: [
      { title: 'Read', links: ['Dispatches', 'Destinations', 'Columns', 'Guides'] },
      { title: 'Journal', links: ['About', 'Contributors', 'Submissions'] },
      { title: 'Subscribe', links: ['Newsletter', 'Print annual', 'Contact'] },
    ],
    bottom: { left: `© 2026 Voyage — an OLOtheme demo.`, right: 'Built with OLObuild' },
  },
  cursor: { blend_mode: 'exclusion', ring_color: CREAM, dot_color: CORAL },
}, home);
