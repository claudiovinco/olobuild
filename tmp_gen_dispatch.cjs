/* Dispatch — ricomposizione TILE-PURE (image-free). Media & News, carta + inchiostro.
   Newsreader (display serif) + Work Sans (body/label). Toni: paper/ink/red.
   Revisione pixel-perfect: newsletter tile nativo, Culture 5 items, sec-head inline,
   ticker border-bottom, Business headline rosso, palette/copy esatta dal blueprint. */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('di');

// ── Palette dal :root ──────────────────────────────────────────────────────
const PAPER   = '#f4f1ea';
const PAPER2  = '#ebe7dd';
const CARD    = '#ffffff';
const INK     = '#16161a';
const INK_S   = '#44444c';
const INK_F   = '#86848c';
const RED     = '#cf2e2e';
const RED_D   = '#b02525';
const LINE    = '#ddd8cc';
const LINE_D  = '#c9c3b4';

const home = [];

// ═══ 1) TICKER ─ newsticker (marquee continuo, label "Breaking" ink/red)
// CSS: .dp-ticker { background:var(--ink); border-bottom:3px solid var(--red) }
// label { background:var(--red); color:#fff; padding:0 14px }
// track span { color:rgba(255,255,255,.82) } separator ●
home.push(sec(INK, 'small', [
  row([ col('1-1', [ tile('newsticker', {
    show_label:       true,
    label_text:       'Breaking',
    label_shape:      'square',
    label_bg:         RED,
    label_color:      '#ffffff',
    label_position:   'left',
    animation_type:   'marquee',
    marquee_direction:'left',
    marquee_duration:  30,
    marquee_gap:       60,
    height:           '40',
    bg_color:         INK,
    text_color:       'rgba(255,255,255,.82)',
    font_size:        13,
    font_weight:      '400',
    separator:        `●`,
    items: [
      { id:'di-n1', title:'Central bank holds rates for a third straight meeting', url:'', badge:'', badge_bg:'', icon:'', timestamp:'' },
      { id:'di-n2', title:'City council approves the riverside transit plan',       url:'', badge:'', badge_bg:'', icon:'', timestamp:'' },
      { id:'di-n3', title:'National side names squad ahead of the qualifier',       url:'', badge:'', badge_bg:'', icon:'', timestamp:'' },
      { id:'di-n4', title:`Markets edge higher as energy prices cool`,              url:'', badge:'', badge_bg:'', icon:'', timestamp:'' },
    ],
    tile_padding: { top:0, right:0, bottom:0, left:0 },
    border_radius: R(0),
  }) ]) ]),
]));

// ═══ 2) FEATURED STORY — hero-split (lead story + Most-Read come showcase)
// CSS: .dp-feat { grid: 1.7fr 1fr; gap:36px; padding clamp(32,4vw,52) 0 }
// .dp-lead h2 { font-size:clamp(32,4.6vw,54); line-height:1.04 }
// .dp-lead p { color:var(--ink-soft); font-size:17px }
// .dp-side { border-left:1px solid var(--line); padding-left:28px }
// .dp-si .n { font-family:var(--disp); font-size:26px; color:var(--red) }
// .dp-si h3 { font-size:18px }
home.push(sec(PAPER, 'large', [
  row([ col('1-1', [ tile('hero-split', {
    eyebrow_text:     'Politics · Analysis',
    eyebrow_dot_color: RED,
    eyebrow_color:    RED,
    headline_lines: [
      { text:`Inside the budget deal`, color:INK,   italic:false },
      { text:`that almost didn't happen`, color:INK, italic:true  },
    ],
    headline_font_family:  'serif',
    headline_font_size:    52,
    headline_line_height:  1.04,
    headline_font_weight:  '600',
    headline_align:        'left',
    subhead:         `For seventy-two hours the talks looked dead. Then a late-night compromise on housing rewrote the maths — and the coalition with it. We reconstruct the week.`,
    subhead_color:   INK_S,
    subhead_size:    17,
    subhead_italic:  false,
    subhead_max_width: 620,
    cta1_text:       'Read full story',
    cta1_url:        '#',
    cta1_bg:         INK,
    cta1_color:      PAPER,
    cta1_size:       13,
    cta1_radius:     R(2),
    cta1_radius_hover: R(2),
    cta2_text:       'By Elena Marchetti · 12 min read',
    cta2_url:        '#',
    cta2_bg:         'transparent',
    cta2_color:      INK_F,
    cta2_border:     'transparent',
    cta2_size:       12,
    cta2_radius:     R(0),
    cta2_radius_hover: R(0),
    stats: [],
    showcase_enabled:     true,
    showcase_bg:          { type:'solid', color:CARD },
    showcase_padding:     24,
    showcase_radius:      R(3),
    showcase_radius_hover: R(3),
    showcase_badge_text:  'MOST READ',
    showcase_badge_dot:   RED,
    showcase_badge_bg:    PAPER2,
    showcase_badge_color: INK,
    showcase_items: [
      { number:'1', text:`The quiet boom in small-town manufacturing`,       italic:false, text_color:INK, bg:{ type:'solid', color:PAPER } },
      { number:'2', text:`How a forgotten film became this year's obsession`, italic:false, text_color:INK, bg:{ type:'solid', color:PAPER } },
      { number:'3', text:`The border town learning to live with the river`,  italic:false, text_color:INK, bg:{ type:'solid', color:PAPER } },
      { number:'4', text:`A coach, a comeback, and one impossible season`,   italic:false, text_color:INK, bg:{ type:'solid', color:PAPER } },
    ],
    showcase_card_radius:      R(2),
    showcase_card_radius_hover: R(2),
    showcase_card_shadow:      'none',
    showcase_caption_left:  'This week',
    showcase_caption_right: 'Most read',
    showcase_hover_effect:  'none',
    split_ratio:  '1.7fr 1fr',
    gap:          36,
    min_height:   0,
    tile_padding: { top:0, right:0, bottom:0, left:0 },
  }) ]) ]),
]));

// ═══ 3) LATEST — section-header inline + info-cards 4 col
// CSS: .sec-head { display:flex; align-items:center; gap:16px; margin-bottom:28px }
// .sec-head h2 { font-size:24px } .sec-head .rule { flex:1; height:1px; background:var(--line-d) }
// .sec-head .more { color:var(--red); font-size:12px; font-weight:600; text-transform:uppercase }
// .art-grid { grid-template-columns:repeat(4,1fr); gap:24px }
// .art h3 { font-size:20px; line-height:1.12 } .art p { color:var(--ink-soft); font-size:13.5px }
// .art .byline { margin-top:10px }
// NOTA: sezione su PAPER (sfondo principale), NON su PAPER2
home.push(sec(PAPER, 'large', [
  row([ col('1-1', [ tile('section-header', {
    eyebrow_show:  false,
    headline_lines: [ { text:'Latest', color:INK, italic:false } ],
    headline_font_family: 'serif',
    headline_font_size:   24,
    headline_font_weight: '600',
    headline_align: 'left',
    tagline_show:  true,
    tagline_text:  'All stories →',
    tagline_text_color: RED,
    tagline_text_size:  12,
    tagline_text_italic: false,
    layout: 'split',
    gap: 16,
  }) ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg:      { type:'solid', color:'transparent' },
    container_padding: 0,
    container_gap:     0,
    columns:   4,
    items_gap: 24,
    card_bg:    { type:'solid', color:CARD },
    card_color: INK_S,
    card_radius:       R(0),
    card_radius_hover: R(0),
    card_padding: 16,
    show_icon:          false,
    show_counter:       true,
    show_counter_label: true,
    show_arrow:         false,
    show_footer:        true,
    show_media:         false,
    counter_shape:      'plain',
    counter_color:      RED,
    counter_size:       11,
    title_color:        INK,
    title_font_family:  'serif',
    title_size:         20,
    title_weight:       '600',
    description_size:   13.5,
    footer_size:        12,
    card_hover_effect:  'lift',
    items: [
      { counter:'Politics', counter_label:'Politics', title:'What the new housing bill actually changes',    description:'A clause-by-clause read of the plan landing next week.',          footer_text:'Tomas Rein · 6 min',  footer_dot_color:RED },
      { counter:'Business', counter_label:'Business', title:'Why the energy market just blinked',            description:'Prices cooled overnight. The reasons are messier than they look.', footer_text:'Priya Anand · 5 min', footer_dot_color:RED },
      { counter:'Science',  counter_label:'Science',  title:'The lab quietly rewriting battery chemistry',   description:'Inside a small team with an outsized claim.',                     footer_text:'Marco Villa · 9 min', footer_dot_color:RED },
      { counter:'World',    counter_label:'World',    title:`A city votes to slow itself down`,              description:'The 15-minute idea gets its biggest test yet.',                   footer_text:'Sofia Klein · 7 min', footer_dot_color:RED },
    ],
  }) ]) ]),
]));

// ═══ 4) CULTURE — band scura
// CSS: .dp-band { background:var(--ink); color:var(--paper) }
// .dp-band-grid { grid-template-columns:1.4fr 1fr 1fr; gap:28px }
// Lead: .dp-band-lead h3 { font-size:30px } .dp-band-lead .media { aspect-ratio:16/10 }
// Side: .dp-bi { grid:88px 1fr; gap:14px; border-bottom:1px solid rgba(255,255,255,.12) }
// .dp-bi h3 { font-size:16px }
// 4 articoli nella sidebar (Books/Music/Art/Food) + 1 lead (Film)
// Approssimazione: section-header + info-cards 3 col + colonna "lead" prominente
// info-cards: lead come prima card (title_size 28 col span), poi 4 mini con thumbnail
home.push(sec(INK, 'large', [
  row([ col('1-1', [ tile('section-header', {
    eyebrow_show:  false,
    headline_lines: [ { text:'Culture', color:'#ffffff', italic:false } ],
    headline_font_family: 'serif',
    headline_font_size:   24,
    headline_font_weight: '600',
    headline_align: 'left',
    tagline_show:  true,
    tagline_text:  'More culture →',
    tagline_text_color: RED,
    tagline_text_size:  12,
    tagline_text_italic: false,
    layout: 'split',
    gap: 16,
  }) ]) ]),
  // Lead card (Film) prominente in colonna sinistra 1.4fr
  row([
    col('7-12', [ tile('info-cards', {
      container_bg:      { type:'solid', color:'transparent' },
      container_padding: 0,
      container_gap:     0,
      columns:   1,
      items_gap: 0,
      card_bg:    { type:'solid', color:'#22222a' },
      card_color: 'rgba(255,255,255,.6)',
      card_radius:       R(3),
      card_radius_hover: R(3),
      card_padding: 22,
      show_icon:          false,
      show_counter:       true,
      show_counter_label: true,
      show_arrow:         false,
      show_footer:        true,
      show_media:         false,
      counter_shape:      'plain',
      counter_color:      RED,
      counter_size:       11,
      title_color:        '#ffffff',
      title_font_family:  'serif',
      title_size:         30,
      title_weight:       '600',
      description_size:   14,
      footer_size:        12,
      card_hover_effect:  'lift',
      items: [
        { counter:'Film', counter_label:'Film', title:'The slow cinema revival nobody predicted', description:`Three-hour films are selling out. We asked why audiences suddenly want to sit still.`, footer_text:'By Lina Okonkwo · 10 min read', footer_dot_color:RED },
      ],
    }) ]),
    // Sidebar 4 mini-articoli (Books/Music/Art/Food) occupano colonne 5-12
    col('5-12', [ tile('info-cards', {
      container_bg:      { type:'solid', color:'transparent' },
      container_padding: 0,
      container_gap:     0,
      columns:   1,
      items_gap: 18,
      card_bg:    { type:'solid', color:'transparent' },
      card_color: 'rgba(255,255,255,.6)',
      card_radius:       R(0),
      card_radius_hover: R(0),
      card_padding: 0,
      show_icon:          false,
      show_counter:       true,
      show_counter_label: true,
      show_arrow:         false,
      show_footer:        false,
      show_media:         false,
      counter_shape:      'plain',
      counter_color:      RED,
      counter_size:       10,
      title_color:        '#ffffff',
      title_font_family:  'serif',
      title_size:         16,
      title_weight:       '600',
      description_size:   13,
      card_hover_effect:  'none',
      items: [
        { counter:'Books', counter_label:'Books', title:`The debut novel everyone's arguing about`,  description:'' },
        { counter:'Music', counter_label:'Music', title:'How a 30-second clip rebuilt a career',         description:'' },
        { counter:'Art',   counter_label:'Art',   title:'The exhibition that sold out before opening',   description:'' },
        { counter:'Food',  counter_label:'Food',  title:'The neighbourhood that became a destination',   description:'' },
      ],
    }) ]),
  ], { gap:28 }),
]));

// ═══ 5) OPINION — testimonial (quote editoriale)
// CSS: .dp-op { border-block:1px solid var(--line); padding clamp(40,5vw,64) 0 }
// .dp-op__in { max-width:840px; text-align:center }
// .dp-op q { font-family:var(--disp); font-style:italic; font-size:clamp(24,3.4vw,38) }
// .dp-op__by { color:var(--ink-faint); letter-spacing:.04em; text-transform:uppercase; font-size:13px }
// Sezione su PAPER con bordi top/bottom
home.push(sec(PAPER, 'large', [
  row([ col('1-1', [ tile('testimonial', {
    quote:       `“A free press isn't a business model. It's the thing the business model is supposed to protect.”`,
    author_name: 'Editorial Board',
    author_role: 'Opinion',
    avatar:      '',
    rating:      '0',
    layout:      'single',
    show_line:   true,
    line_color:  LINE_D,
    bg_color:    'transparent',
    text_color:  INK,
    border_radius: '0',
    author_position: 'bottom-center',
  }) ]) ]),
]));

// ═══ 6) BUSINESS — section-header (titolo h2 in rosso) + info-cards 4 col
// CSS: .sec-head--red h2 { color:var(--red) }  ← titolo ROSSO
// Sezione su PAPER2 (non PAPER), stessa struttura di LATEST
home.push(sec(PAPER2, 'large', [
  row([ col('1-1', [ tile('section-header', {
    eyebrow_show:  false,
    headline_lines: [ { text:'Business', color:RED, italic:false } ],
    headline_font_family: 'serif',
    headline_font_size:   24,
    headline_font_weight: '600',
    headline_align: 'left',
    tagline_show:  true,
    tagline_text:  'More business →',
    tagline_text_color: RED,
    tagline_text_size:  12,
    tagline_text_italic: false,
    layout: 'split',
    gap: 16,
  }) ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg:      { type:'solid', color:'transparent' },
    container_padding: 0,
    container_gap:     0,
    columns:   4,
    items_gap: 24,
    card_bg:    { type:'solid', color:CARD },
    card_color: INK_S,
    card_radius:       R(0),
    card_radius_hover: R(0),
    card_padding: 16,
    show_icon:          false,
    show_counter:       true,
    show_counter_label: true,
    show_arrow:         false,
    show_footer:        true,
    show_media:         false,
    counter_shape:      'plain',
    counter_color:      RED,
    counter_size:       11,
    title_color:        INK,
    title_font_family:  'serif',
    title_size:         20,
    title_weight:       '600',
    description_size:   13.5,
    footer_size:        12,
    card_hover_effect:  'lift',
    items: [
      { counter:'Markets', counter_label:'Markets', title:'The index fund that broke its own rules',      description:'A passive giant made an active bet. It paid off.',              footer_text:'R. Castellano · 8 min', footer_dot_color:RED },
      { counter:'Tech',    counter_label:'Tech',    title:'Inside the startup betting against the cloud', description:'On-prem is back, and investors are listening.',                 footer_text:'J. Okada · 6 min',     footer_dot_color:RED },
      { counter:'Labour',  counter_label:'Labour',  title:'The four-day week, two years on',              description:`The firms that kept it — and the ones that quietly didn't.`, footer_text:'M. Iqbal · 11 min',    footer_dot_color:RED },
      { counter:'Trade',   counter_label:'Trade',   title:`A small port's very big year`,            description:'How a rerouted shipping lane changed a town.',                  footer_text:'D. Moreau · 7 min',    footer_dot_color:RED },
    ],
  }) ]) ]),
]));

// ═══ 7) NEWSLETTER — tile newsletter nativo (sfondo red)
// CSS: .dp-news { background:var(--red); color:#fff; text-align:center; padding clamp(50,7vw,84) 0 }
// .dp-news h2 { font-size:clamp(28,4vw,46); color:#fff }
// .dp-news p { color:rgba(255,255,255,.85); font-size:16px; margin:12px 0 24px }
// Form: input border-radius:2px + btn--ink (sfondo ink, color paper)
home.push(sec(RED, 'large', [
  row([ col('1-1', [ tile('newsletter', {
    title:             'The Dispatch, every morning',
    subtitle:          'One email. The five stories that matter, explained — before your first coffee.',
    icon_type:         'none',
    layout:            'vertical',
    show_name:         false,
    email_placeholder: 'Your email address',
    button_text:       'Subscribe free',
    button_icon:       false,
    privacy_text:      '',
    max_width:         '420',
    alignment:         'center',
    bg_color:          RED,
    border_radius:     R(0),
    tile_padding:      { top:72, right:28, bottom:72, left:28 },
    title_size:        '40',
    title_weight:      '600',
    title_color:       '#ffffff',
    subtitle_size:     '16',
    subtitle_color:    'rgba(255,255,255,.85)',
    input_bg:          '#ffffff',
    input_color:       INK,
    input_border:      '#ffffff',
    input_radius:      2,
    input_height:      '48',
    btn_bg:            INK,
    btn_color:         PAPER,
    btn_hover_bg:      '#2a2a30',
    btn_radius:        2,
    btn_font_size:     '14',
    btn_font_weight:   '600',
    shadow:            'none',
  }) ]) ]),
]));

K.emit({
  slug:        'dispatch',
  name:        'Dispatch',
  tags:        ['news', 'magazine', 'media', 'editorial'],
  description: 'Dispatch — testata indipendente. Carta e inchiostro: paper/ink/red. Newsreader serif + Work Sans. Ticker breaking news, featured story, griglie articoli (Latest 4 col + Business 4 col), band Culture scura (lead + 4 mini), citazione editoriale Opinion, newsletter nativa. Riproduzione fedele OLOtheme Dispatch.',
  colors: {
    primary:           RED,
    primary_contrast:  '#ffffff',
    secondary:         INK,
    secondary_contrast: PAPER,
    muted:             PAPER2,
    muted_contrast:    INK_S,
    text:              INK,
    text_muted:        INK_F,
    background:        PAPER,
    border:            LINE,
    link:              RED,
  },
  css_disp:          `"Newsreader", Georgia, serif`,
  css_sans:          `"Work Sans", -apple-system, sans-serif`,
  heading_weight:    '600',
  heading_line_height: '1.08',
  google_fonts:      ['Newsreader', 'Work Sans'],
  logo_variant:      'dark',
  menu: [
    { title:'Politics', url:'#latest' },
    { title:'Culture',  url:'#culture' },
    { title:'Business', url:'#business' },
    { title:'World',    url:'#' },
    { title:'Sport',    url:'#' },
    { title:'Opinion',  url:'#' },
  ],
  header: {
    bg:         PAPER,
    text_color: INK_S,
    sticky_bg:  `rgba(244,241,234,.95)`,
    logo_width: 140,
  },
  footer: {
    bg:        INK,
    headColor: '#ffffff',
    brand: {
      name:    'The Dispatch',
      tagline: 'Independent reporting since 1998. Member-funded, ad-light, beholden to no one.',
    },
    columns: [
      { title:'Sections',   links:['Politics', 'Culture', 'Business', 'World'] },
      { title:'The paper',  links:['About us', 'Masthead', 'Ethics', 'Careers'] },
      { title:'Subscribe',  links:['Newsletters', 'Membership', 'Gift a sub', 'Contact'] },
    ],
    bottom: {
      left:  `© 2026 The Dispatch — an OLOtheme demo.`,
      right: 'Built with OLObuild',
    },
  },
  cursor: false,
}, home);
