/* Gazette — ricomposizione TILE-PURE (image-free). Media & News / culture magazine.
   Palette cream/paper/ink/claret. Libre Caslon Display (headlines) + Mulish (body).
   Blueprint: OLOtheme - Gazette (Media & News).html + gazette.css */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('gz');

// ── PALETTE (:root gazette.css) ─────────────────────────────────────────────
const CREAM  = '#f3f0e9';
const CREAM2 = '#e9e4d8';
const PAPER  = '#faf8f2';
const INK    = '#16161a';
const INK2   = '#2c2c30';
const CLARET = '#9a2b22';
const TXT    = '#3a3a3e';
const DIM    = '#76746e';
const LINE   = '#d8d2c4';
const LINE2  = '#c7c0ae';
const WHITE  = '#ffffff';

const home = [];

// ─── 1) TICKER — labelled news marquee ─────────────────────────────────────
// .gz-ticker: bg=INK, label-bg=CLARET (pill/arrow), testo cream-82, font serif 13.5px
// marquee 38s, separatore " — " (::after content:"—")
home.push(sec(INK, 'none', [ row([ col('1-1', [ tile('newsticker', {
  show_label:        true,
  label_text:        'Latest',
  label_shape:       'arrow',
  label_position:    'left',
  label_bg:          CLARET,
  label_color:       WHITE,
  animation_type:    'marquee',
  marquee_direction: 'left',
  marquee_duration:  38,
  marquee_gap:       44,
  bg_color:          INK,
  text_color:        `rgba(243,240,233,.82)`,
  height:            '42',
  font_size:         13,
  font_family:       'serif',
  font_weight:       '400',
  separator:         ` — `,
  tile_padding:      { top: 0, right: 0, bottom: 0, left: 0 },
  items: [
    { id: 'gz-t1', title: `The slow return of the city night market`,  url: '#', badge: '', icon: '', timestamp: '' },
    { id: 'gz-t2', title: `A painter who only works at dawn`,           url: '#', badge: '', icon: '', timestamp: '' },
    { id: 'gz-t3', title: `What we lost when restaurants got quiet`,    url: '#', badge: '', icon: '', timestamp: '' },
    { id: 'gz-t4', title: `The last letterpress in the old quarter`,    url: '#', badge: '', icon: '', timestamp: '' },
  ],
}) ]) ]) ], { padding: 'none' }));

// ─── 2) FEATURED STORY — hero-split (showcase = dati editoriali copertina) ──
// .gz-lead bg=CREAM, split 1.15fr/.85fr, gap 48, h1 72px serif, stand 19px serif,
// byline uppercase. showcase simula il pannello info articolo.
home.push(sec(CREAM, 'large', [ row([ col('1-1', [ tile('hero-split', {
  eyebrow_text:           `The Essay · Cities`,
  eyebrow_dot_color:      CLARET,
  eyebrow_color:          CLARET,
  headline_lines: [
    { text: `The slow return of`,       color: INK, italic: false },
    { text: `the city night market`,    color: INK, italic: false },
  ],
  headline_font_family:   'serif',
  headline_font_size:     68,
  headline_line_height:   1.02,
  headline_font_weight:   '400',
  headline_align:         'left',
  subhead:                `For a decade they were left for dead. Now, under the same lanterns, a new generation is rebuilding the night market — one stall, one recipe, one argument at a time.`,
  subhead_color:          INK2,
  subhead_size:           19,
  subhead_italic:         false,
  subhead_max_width:      540,
  cta1_text:              'Read the essay',
  cta1_url:               '#',
  cta1_bg:                INK,
  cta1_color:             CREAM,
  cta1_size:              12,
  cta1_radius:            R(2),
  cta1_radius_hover:      R(2),
  cta2_text:              'All stories',
  cta2_url:               '#',
  cta2_bg:                'transparent',
  cta2_color:             INK,
  cta2_border:            LINE2,
  cta2_size:              12,
  cta2_radius:            R(2),
  cta2_radius_hover:      R(2),
  stats:                  [],
  showcase_enabled:       true,
  showcase_bg:            { type: 'solid', color: CREAM2 },
  showcase_padding:       28,
  showcase_radius:        R(3),
  showcase_radius_hover:  R(3),
  showcase_badge_text:    'COVER STORY · ISSUE 48',
  showcase_badge_dot:     CLARET,
  showcase_badge_bg:      CREAM,
  showcase_badge_color:   DIM,
  showcase_items: [
    { number: 'By Elena Russo', text: 'Author',  italic: false, text_color: INK,    bg: { type: 'solid', color: WHITE } },
    { number: '18 min read',    text: 'Length',  italic: false, text_color: INK,    bg: { type: 'solid', color: WHITE } },
    { number: 'Cities',         text: 'Section', italic: false, text_color: CLARET, bg: { type: 'solid', color: WHITE } },
    { number: 'June 2026',      text: 'Issue',   italic: false, text_color: DIM,    bg: { type: 'solid', color: WHITE } },
  ],
  showcase_card_radius:       R(2),
  showcase_card_radius_hover: R(2),
  showcase_card_shadow:       'none',
  showcase_caption_left:      'GAZETTE',
  showcase_caption_right:     'ISSUE 48',
  showcase_hover_effect:      'none',
  split_ratio:                '1.2fr .8fr',
  gap:                        48,
  min_height:                 0,
  tile_padding:               { top: 0, right: 0, bottom: 0, left: 0 },
}) ]) ]) ]));

// ─── 3) ARTICLE GRID "This week" — .gz-sec (bg CREAM) + 3 card info-cards ──
// .gz-rule: h2 "This week" + link "All stories →" a destra
// .gz-art: 3 col, card_bg PAPER, radius 3px, padding 24, show_counter (kicker)
// card.lead ha span-2 nel CSS → info-cards non può farlo; usiamo 3 card uniformi
home.push(sec(CREAM, 'large', [
  row([ col('1-1', [ tile('section-header', {
    eyebrow_show:          false,
    headline_lines:        [ { text: 'This week', color: INK, italic: false } ],
    headline_font_family:  'serif',
    headline_font_size:    24,
    headline_font_weight:  '400',
    headline_align:        'left',
    headline_inline:       false,
    tagline_show:          true,
    tagline_text:          `All stories →`,
    tagline_text_color:    CLARET,
    tagline_text_size:     11.5,
    tagline_text_italic:   false,
    layout:                'stack',
    gap:                   0,
  }) ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg:      { type: 'solid', color: 'transparent' },
    container_padding: 0,
    container_gap:     0,
    columns:           3,
    items_gap:         30,
    card_bg:           { type: 'solid', color: PAPER },
    card_color:        DIM,
    card_radius:       R(3),
    card_padding:      24,
    show_icon:         false,
    show_counter:      true,
    show_counter_label: true,
    show_arrow:        false,
    show_footer:       true,
    show_media:        false,
    counter_shape:     'plain',
    counter_color:     CLARET,
    counter_size:      11,
    title_font_family: 'serif',
    title_size:        22,
    title_weight:      '400',
    title_italic:      false,
    title_color:       INK,
    description_size:  14.5,
    card_hover_effect: 'lift',
    items: [
      {
        counter:       `Profile · Art`,
        counter_label: '',
        title:         `The painter who only works at dawn`,
        description:   `Margit Halász hasn’t touched a brush after 9am in thirty years. We spent a week watching the light she chases — and the discipline behind it.`,
        footer_text:   `By Tomas Veber · 12 min`,
        footer_dot_color: CLARET,
      },
      {
        counter:       `Essay · Food`,
        counter_label: '',
        title:         'What we lost when restaurants got quiet',
        description:   'On noise, intimacy, and the strange grief of a half-empty dining room.',
        footer_text:   `By A. Demir · 9 min`,
        footer_dot_color: CLARET,
      },
      {
        counter:       `Craft · Cities`,
        counter_label: '',
        title:         'The last letterpress in the old quarter',
        description:   'One family, four tonnes of lead type, and a refusal to go digital.',
        footer_text:   `By J. Okoro · 7 min`,
        footer_dot_color: CLARET,
      },
    ],
  }) ]) ]),
]));

// ─── 4) "More from the issue" + "Most read" — side-by-side ──────────────────
// .gz-sec.paper (bg=PAPER), layout: .gz-aside grid 1.6fr / 1fr gap 48
// Colonna sinistra: header "More from the issue" + 2 card (griglia 1fr 1fr)
// Colonna destra:   header "Most read" + lista ranked 4 voci
// .gz-most: border-top 2px solid INK; item: border-bottom LINE, numero serif 34px LINE2→CLARET on hover
home.push(sec(PAPER, 'large', [
  row([ col('1-1', [ tile('section-header', {
    eyebrow_show:         false,
    headline_lines:       [ { text: 'More from the issue', color: INK, italic: false } ],
    headline_font_family: 'serif',
    headline_font_size:   24,
    headline_font_weight: '400',
    headline_align:       'left',
    headline_inline:      false,
    tagline_show:         false,
    layout:               'stack',
    gap:                  0,
  }) ]) ]),
  row([
    col('2-3', [ tile('info-cards', {
      container_bg:      { type: 'solid', color: 'transparent' },
      container_padding: 0,
      container_gap:     0,
      columns:           2,
      items_gap:         20,
      card_bg:           { type: 'solid', color: WHITE },
      card_color:        DIM,
      card_radius:       R(3),
      card_padding:      22,
      show_icon:         false,
      show_counter:      true,
      show_counter_label: false,
      show_arrow:        false,
      show_footer:       true,
      show_media:        false,
      counter_shape:     'plain',
      counter_color:     CLARET,
      counter_size:      11,
      title_font_family: 'serif',
      title_size:        20,
      title_weight:      '400',
      title_italic:      false,
      title_color:       INK,
      description_size:  14,
      card_hover_effect: 'lift',
      items: [
        {
          counter:       'History',
          counter_label: '',
          title:         'A short history of the public bath',
          description:   'How cities learned to be clean together — and what they traded for it.',
          footer_text:   `By R. Bianchi · 11 min`,
          footer_dot_color: CLARET,
        },
        {
          counter:       'Ideas',
          counter_label: '',
          title:         'The case for the second-hand bookshop',
          description:   `Against the algorithm: in praise of finding what you weren’t looking for.`,
          footer_text:   `By L. Haddad · 8 min`,
          footer_dot_color: CLARET,
        },
      ],
    }) ]),
    col('1-3', [
      tile('section-header', {
        eyebrow_show:         false,
        headline_lines:       [ { text: 'Most read', color: INK, italic: false } ],
        headline_font_family: 'serif',
        headline_font_size:   24,
        headline_font_weight: '400',
        headline_align:       'left',
        headline_inline:      false,
        tagline_show:         false,
        layout:               'stack',
        gap:                  0,
      }),
      tile('info-cards', {
        container_bg:      { type: 'solid', color: 'transparent' },
        container_padding: 0,
        container_gap:     0,
        columns:           1,
        items_gap:         0,
        card_bg:           { type: 'solid', color: 'transparent' },
        card_color:        DIM,
        card_radius:       R(0),
        card_padding:      18,
        show_icon:         false,
        show_counter:      true,
        show_counter_label: false,
        show_arrow:        false,
        show_footer:       true,
        show_media:        false,
        counter_shape:     'plain',
        counter_color:     LINE2,
        counter_size:      34,
        counter_font:      'serif',
        title_font_family: 'serif',
        title_size:        17,
        title_weight:      '400',
        title_italic:      false,
        title_color:       INK,
        description_size:  0,
        card_hover_effect: 'none',
        items: [
          { counter: '1', title: `Why your grandmother’s kitchen had no recipes`, description: '', footer_text: `Food · 6 min`,    footer_dot_color: CLARET },
          { counter: '2', title: 'The roundabout that started a revolution',          description: '', footer_text: `Cities · 10 min`, footer_dot_color: CLARET },
          { counter: '3', title: 'Forgery, and the men who loved being fooled',       description: '', footer_text: `Art · 14 min`,    footer_dot_color: CLARET },
          { counter: '4', title: 'In defence of doing one thing slowly',              description: '', footer_text: `Ideas · 5 min`,   footer_dot_color: CLARET },
        ],
      }),
    ]),
  ], { gap: 48, vertical_align: 'stretch' }),
]));

// ─── 5) DEPARTMENTS — 4 card con bordo + hover-invert (bg INK, testo CREAM) ─
// .gz-dep: border 1px solid LINE2, radius 3px, padding 24x20
// hover: bg INK, h3+.n color CREAM
// Usiamo info-cards con card_border + card_hover_effect 'fill' o 'none'
// (il tile info-cards non ha hover fill-to-ink nativo, usiamo 'none' + stile bordo)
home.push(sec(CREAM, 'large', [
  row([ col('1-1', [ tile('section-header', {
    eyebrow_show:         false,
    headline_lines:       [ { text: 'Departments', color: INK, italic: false } ],
    headline_font_family: 'serif',
    headline_font_size:   24,
    headline_font_weight: '400',
    headline_align:       'left',
    headline_inline:      false,
    tagline_show:         false,
    layout:               'stack',
    gap:                  0,
  }) ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg:       { type: 'solid', color: 'transparent' },
    container_padding:  0,
    container_gap:      0,
    columns:            4,
    items_gap:          14,
    card_bg:            { type: 'solid', color: 'transparent' },
    card_border:        `1px solid ${LINE2}`,
    card_color:         DIM,
    card_radius:        R(3),
    card_padding:       24,
    show_icon:          false,
    show_counter:       false,
    show_counter_label: false,
    show_arrow:         false,
    show_footer:        false,
    show_media:         false,
    title_font_family:  'serif',
    title_size:         24,
    title_weight:       '400',
    title_italic:       false,
    title_color:        INK,
    description_size:   12,
    description_color:  DIM,
    card_hover_effect:  'fill',
    card_hover_bg:      INK,
    card_hover_text:    CREAM,
    items: [
      { title: 'Art',    description: 'Exhibitions, studios, the made world' },
      { title: 'Cities', description: 'How we live close together' },
      { title: 'Food',   description: 'Kitchens, markets, the table' },
      { title: 'Ideas',  description: 'Arguments worth the time' },
    ],
  }) ]) ]),
]));

// ─── 6) NEWSLETTER — tile newsletter (form email nativo) ────────────────────
// .gz-news: bg INK, text CREAM, kicker "The Saturday letter" (claret rosato #d8a59f),
// h2 50px serif, p 16px cream-70, form horizontal (input + btn CREAM/INK)
// form: input bg rgba(243,240,233,.08), border rgba(243,240,233,.22), radius 3px
// btn: bg CREAM, color INK
home.push(sec(INK, 'large', [ row([ col('1-1', [ tile('newsletter', {
  title:              'One long read, every weekend',
  subtitle:           `No noise, no notifications — just one carefully chosen story in your inbox each Saturday morning, free.`,
  icon_type:          'emoji',
  icon_name:          'The Saturday letter',
  layout:             'horizontal',
  show_name:          false,
  email_placeholder:  'you@example.com',
  button_text:        'Subscribe',
  button_icon:        false,
  privacy_text:       '',
  max_width:          '640',
  alignment:          'center',
  bg_color:           'transparent',
  border_radius:      0,
  tile_padding:       { top: 0, right: 0, bottom: 0, left: 0 },
  title_size:         '46',
  title_weight:       '400',
  title_color:        CREAM,
  subtitle_size:      '16',
  subtitle_color:     `rgba(243,240,233,.70)`,
  icon_size:          '13',
  icon_color:         '#d8a59f',
  input_bg:           `rgba(243,240,233,.08)`,
  input_color:        CREAM,
  input_border:       `rgba(243,240,233,.22)`,
  input_focus_border: CREAM,
  input_radius:       3,
  input_height:       '50',
  btn_bg:             CREAM,
  btn_color:          INK,
  btn_hover_bg:       CREAM2,
  btn_radius:         2,
  btn_font_size:      '12',
  btn_font_weight:    '700',
}) ]) ]) ]));

// ─── EMIT ──────────────────────────────────────────────────────────────────
K.emit({
  slug:  'gazette',
  name:  'Gazette',
  tags:  ['magazine', 'news', 'editorial', 'culture', 'media'],
  description: `Gazette — culture magazine editorial. Cream/paper/ink/claret palette, Libre Caslon Display (headlines) + Mulish (body). Ticker titoli, featured story hero-split, griglia articoli, sezione More+Most-read a 2 colonne, departments card-bordo, newsletter form. Tema chiaro.`,
  colors: {
    primary:           CLARET,
    primary_contrast:  WHITE,
    secondary:         INK,
    secondary_contrast: CREAM,
    muted:             CREAM2,
    muted_contrast:    TXT,
    text:              TXT,
    text_muted:        DIM,
    background:        CREAM,
    border:            LINE,
    link:              CLARET,
  },
  css_disp: `"Libre Caslon Display", Georgia, serif`,
  css_sans: `"Mulish", -apple-system, sans-serif`,
  heading_weight:      '400',
  heading_line_height: '1.06',
  google_fonts:        ['Libre Caslon Display', 'Libre Caslon Text', 'Mulish'],
  logo_variant:        'dark',
  cursor:              false,
  menu: [
    { title: 'Front page', url: '/'     },
    { title: 'Long reads', url: '#'     },
    { title: 'Art',        url: '#'     },
    { title: 'Cities',     url: '#'     },
    { title: 'Food',       url: '#'     },
    { title: 'Ideas',      url: '#'     },
  ],
  header: {
    bg:         CREAM,
    text_color: INK,
    sticky_bg:  `rgba(243,240,233,.95)`,
    logo_width: 130,
  },
  footer: {
    bg:       INK,
    headColor: CREAM,
    brand: {
      name:    'Gazette',
      tagline: 'A culture review, published weekly. Long reads on art, cities, food and ideas.',
    },
    columns: [
      { title: 'Read',      links: ['Art', 'Cities', 'Food', 'Ideas']                           },
      { title: 'Magazine',  links: ['About', 'Contributors', 'Submissions', 'Archive']           },
      { title: 'Subscribe', links: ['Newsletter', 'Print edition', 'Gift a subscription', 'Contact'] },
    ],
    bottom: {
      left:  '© 2026 Gazette — an OLOtheme demo.',
      right: 'Built with OLObuild',
    },
  },
}, home);
