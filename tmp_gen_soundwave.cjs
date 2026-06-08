/* Soundwave — ricomposizione TILE-PURE (image-free). Dark mint-neon + pink. Artist/musician. */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('sw');

// ── PALETTE (da :root soundwave.css) ────────────────────────────────────────
const BG     = '#0c0c10';
const BG2    = '#101016';
const PANEL  = '#16161d';
const PANEL2 = '#1d1d26';
const INK    = '#060608';
const MINT   = '#27e0a3';
const MINTD  = '#1fc78f';
const PINK   = '#ff5d9e';
const TXT    = '#b6b6c2';
const DIM    = '#74747f';
const LINE   = 'rgba(255,255,255,.09)';
const LINE2  = 'rgba(39,224,163,.4)';
const WHITE  = '#ffffff';

const home = [];

// ─── helpers ────────────────────────────────────────────────────────────────

const shead = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: MINT, eyebrow_dot_color: MINT, eyebrow_separator: '',
  headline_lines: [
    { text: l1,     color: WHITE, italic: false },
    { text: accent, color: MINT,  italic: false },
  ],
  headline_font_family: 'sans-serif', headline_font_size: 46, headline_font_weight: '700',
  headline_align: 'center', headline_inline: true,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false,
  tagline_text_color: DIM, tagline_text_size: 16.5,
  layout: 'center', gap: 16,
});

const sheadLeft = (eyebrow, h2) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: MINT, eyebrow_dot_color: MINT, eyebrow_separator: '',
  headline_lines: [{ text: h2, color: WHITE, italic: false }],
  headline_font_family: 'sans-serif', headline_font_size: 42, headline_font_weight: '700',
  headline_align: 'left', headline_inline: false,
  tagline_show: false,
  layout: 'stack', gap: 12,
});

// ─── 1) HERO — Album hero (AudioHero → hero-split) ───────────────────────────
// Blueprint: .sw-hero__in { grid-template-columns: 1.1fr .9fr }
// Testo a sinistra, art (cover + mini-player) a destra.
// SEGNALATO: AudioHero NEW tile candidate — mini-player waveform e EQ bar non riproducibili.
home.push(sec(BG, 'large', [row([col('1-1', [tile('hero-split', {
  eyebrow_text:         'New album · out now',
  eyebrow_dot_color:    MINT,
  eyebrow_color:        MINT,
  headline_lines: [
    { text: 'Night',  color: WHITE, italic: false },
    { text: 'glass',  color: MINT,  italic: false },
  ],
  headline_font_family: 'sans-serif',
  headline_font_size:   96,
  headline_line_height: 0.94,
  headline_font_weight: '700',
  headline_align:       'left',
  subhead:          `Eleven tracks recorded between Berlin and a cabin with bad wifi. Late-night electronics for headphones and dancefloors alike.`,
  subhead_color:    TXT,
  subhead_size:     18,
  subhead_italic:   false,
  subhead_max_width: 420,
  cta1_text:         'Play album',
  cta1_url:          '#listen',
  cta1_bg:           MINT,
  cta1_color:        INK,
  cta1_size:         15,
  cta1_radius:       R(999),
  cta1_radius_hover: R(999),
  cta2_text:         'See tour dates',
  cta2_url:          '#tour',
  cta2_bg:           'rgba(255,255,255,.05)',
  cta2_color:        WHITE,
  cta2_border:       'rgba(255,255,255,.18)',
  cta2_size:         15,
  cta2_radius:       R(999),
  cta2_radius_hover: R(999),
  stats: [],
  showcase_enabled:      true,
  showcase_bg:           { type: 'solid', color: PANEL },
  showcase_padding:      28,
  showcase_radius:       R(18),
  showcase_radius_hover: R(18),
  showcase_badge_text:   'NIGHTGLASS · KOVA · 2026',
  showcase_badge_dot:    MINT,
  showcase_badge_bg:     INK,
  showcase_badge_color:  WHITE,
  showcase_items: [
    { number: 'Tracklist',     text: '11 tracks',  italic: false, text_color: MINT,  bg: { type: 'solid', color: BG2 } },
    { number: 'Format',        text: 'LP',         italic: false, text_color: WHITE, bg: { type: 'solid', color: BG2 } },
    { number: 'Now streaming', text: 'Everywhere', italic: false, text_color: WHITE, bg: { type: 'solid', color: BG2 } },
    { number: 'Lead single',   text: 'Glasshouse', italic: false, text_color: WHITE, bg: { type: 'solid', color: BG2 } },
  ],
  showcase_card_radius:       R(11),
  showcase_card_radius_hover: R(11),
  showcase_card_shadow:       'none',
  showcase_caption_left:  'NIGHTGLASS',
  showcase_caption_right: 'OUT NOW',
  showcase_hover_effect:  'none',
  split_ratio:    '1.1fr .9fr',
  gap:            48,
  min_height:     0,
  tile_padding:   { top: 0, right: 0, bottom: 0, left: 0 },
})])])]));

// ─── 2) LATEST RELEASE + TRACKLIST ───────────────────────────────────────────
// Blueprint: .sw-release { grid-template-columns: .85fr 1.15fr }
// Colonna sinistra: cover album placeholder (PANEL + border LINE + radius 18px)
// Colonna destra: eyebrow "The album" + h2 "Nightglass" + sub "LP · 11 tracks · 2026"
//   + tracklist (5 righe: numero, titolo, durata) + 2 CTA pill.
// SEGNALATO: Tracklist NEW tile candidate — play-on-hover e waveform non riproducibili.
// Resa: info-cards colonna intera lato destro, cover come info-cards 1-item a sinistra.
home.push(sec(BG2, 'large', [
  row([
    // Colonna sinistra: cover album placeholder
    col('1-3', [tile('info-cards', {
      container_bg:      { type: 'solid', color: 'transparent' },
      container_padding: 0,
      container_gap:     0,
      columns:           1,
      items_gap:         0,
      card_bg:           { type: 'solid', color: PANEL },
      card_color:        DIM,
      card_radius:       R(18),
      card_padding:      40,
      show_icon:         false,
      show_counter:      false,
      show_arrow:        false,
      show_footer:       true,
      show_media:        false,
      title_color:       DIM,
      title_font_family: 'sans-serif',
      title_size:        12,
      title_weight:      '600',
      title_italic:      false,
      description_size:  0,
      footer_size:       12,
      items: [
        { title: 'NIGHTGLASS', footer_text: 'Kova · 2026', footer_dot_color: MINT },
      ],
      card_hover_effect: 'none',
    })]),
    // Colonna destra: header + tracklist + CTA
    col('2-3', [
      tile('section-header', {
        eyebrow_show:      true,
        eyebrow_text:      'The album',
        eyebrow_color:     MINT,
        eyebrow_dot_color: MINT,
        eyebrow_separator: '',
        headline_lines: [{ text: 'Nightglass', color: WHITE, italic: false }],
        headline_font_family: 'sans-serif',
        headline_font_size:   42,
        headline_font_weight: '700',
        headline_align:       'left',
        headline_inline:      false,
        tagline_show:      true,
        tagline_text:      'LP · 11 tracks · 2026',
        tagline_text_italic: false,
        tagline_text_color: MINT,
        tagline_text_size:  14,
        layout: 'stack', gap: 10,
      }),
      // Tracklist — 5 righe: counter=numero, title=titolo, footer=durata
      // .sw-track { border-bottom: 1px solid var(--line); border-radius: 10px; padding: 13px 10px }
      // .sw-track:hover { background: var(--panel) }
      tile('info-cards', {
        container_bg:      { type: 'solid', color: 'transparent' },
        container_padding: 0,
        container_gap:     0,
        columns:           1,
        items_gap:         0,
        card_bg:           { type: 'solid', color: 'transparent' },
        card_color:        DIM,
        card_radius:       R(10),
        card_padding:      13,
        show_icon:         false,
        show_counter:      true,
        show_counter_label: false,
        show_arrow:        false,
        show_footer:       true,
        show_media:        false,
        counter_shape:     'plain',
        counter_color:     DIM,
        counter_size:      13,
        title_color:       WHITE,
        title_font_family: 'sans-serif',
        title_size:        15,
        title_weight:      '600',
        title_italic:      false,
        description_size:  0,
        footer_size:       13,
        items: [
          { counter: '1', title: 'Glasshouse',         description: '', footer_text: '3:48', footer_dot_color: DIM },
          { counter: '2', title: 'Low Tide',            description: '', footer_text: '4:12', footer_dot_color: DIM },
          { counter: '3', title: `Pyrite (feat. Mae)`,  description: '', footer_text: '3:31', footer_dot_color: DIM },
          { counter: '4', title: 'Static Bloom',        description: '', footer_text: '5:02', footer_dot_color: DIM },
          { counter: '5', title: 'Cabin Fever',         description: '', footer_text: '3:57', footer_dot_color: DIM },
        ],
        card_hover_effect: 'lift',
      }),
      // CTA sotto tracklist: "Play full album" (mint) + "Vinyl & merch" (ghost)
      tile('cta-banner', {
        headline:             '',
        headline_accent:      '',
        subtitle:             '',
        cta_text:             'Play full album',
        cta_url:              '#listen',
        cta2_text:            `Vinyl & merch`,
        cta2_url:             '#',
        cta2_bg:              'rgba(255,255,255,.05)',
        cta2_color:           WHITE,
        cta2_border:          LINE,
        bg:                   { type: 'solid', color: 'transparent' },
        text_color:           WHITE,
        accent_color:         MINT,
        subtitle_color:       TXT,
        cta_bg:               MINT,
        cta_color:            INK,
        cta_radius:           R(999),
        cta_size:             14,
        headline_font_family: 'sans-serif',
        headline_size:        0,
        headline_weight:      '700',
        subtitle_size:        0,
        layout:               'inline',
        vertical_align:       'center',
        banner_radius:        R(0),
        banner_padding:       0,
      }),
    ]),
  ], { gap: 48 }),
]));

// ─── 3) TOUR DATES ────────────────────────────────────────────────────────────
// Blueprint: .sw-tour { border-top: 1px solid var(--line) }
// .sw-date { grid-template-columns: 130px 1fr auto; border-bottom: 1px solid var(--line) }
// .sw-date__when { font-family: var(--disp); font-weight: 600; font-size: 15px; color: var(--mint) }
// .sw-date__where b { font-family: var(--disp); font-weight: 600; font-size: 19px; color: #fff }
// .sw-date__where span { font-size: 13px; color: var(--txt-dim) }
// .sw-date__cta .avail { color: var(--ink); background: var(--mint); border-radius: 999px; padding: 9px 18px }
// .sw-date__cta .gone { color: var(--txt-dim); border: 1px solid var(--line); border-radius: 999px; padding: 9px 18px }
// Sfondo sezione: .sw-sec.panel → background: var(--bg-2) = #101016
// SEGNALATO: TourDates NEW tile candidate — status avail/sold-out distinti non riproducibili.
// Resa: counter=data (MINT 15px), title=città (WHITE 19px bold), description=venue (DIM 13px), footer=stato
home.push(sec(BG2, 'large', [
  row([col('1-1', [sheadLeft('On tour', 'Nightglass live')])]),
  row([col('1-1', [tile('info-cards', {
    container_bg:      { type: 'solid', color: 'transparent' },
    container_padding: 0,
    container_gap:     0,
    columns:           1,
    items_gap:         0,
    card_bg:           { type: 'solid', color: 'transparent' },
    card_color:        DIM,
    card_radius:       R(0),
    card_padding:      22,
    show_icon:         false,
    show_counter:      true,
    show_counter_label: false,
    show_arrow:        false,
    show_footer:       true,
    show_media:        false,
    counter_shape:     'plain',
    counter_color:     MINT,
    counter_size:      15,
    title_color:       WHITE,
    title_font_family: 'sans-serif',
    title_size:        19,
    title_weight:      '700',
    title_italic:      false,
    description_size:  13,
    footer_size:       13,
    items: [
      { counter: 'FRI 12 JUN', title: 'Berlin',    description: `Säälchen`,           footer_text: 'Sold out', footer_dot_color: DIM  },
      { counter: 'SAT 20 JUN', title: 'Amsterdam', description: 'Paradiso Noord',      footer_text: 'Tickets',  footer_dot_color: MINT },
      { counter: 'THU 02 JUL', title: 'London',    description: 'Village Underground', footer_text: 'Tickets',  footer_dot_color: MINT },
      { counter: 'SAT 11 JUL', title: 'Paris',     description: `La Gaîté Lyrique`,   footer_text: 'Sold out', footer_dot_color: DIM  },
    ],
    card_hover_effect: 'lift',
  })])]),
  // CTA "All 18 dates →" sotto la lista
  row([col('1-1', [tile('cta-banner', {
    headline:             '',
    headline_accent:      '',
    subtitle:             '',
    cta_text:             'All 18 dates',
    cta_url:              '#',
    bg:                   { type: 'solid', color: 'transparent' },
    text_color:           WHITE,
    accent_color:         MINT,
    subtitle_color:       TXT,
    cta_bg:               'rgba(255,255,255,.05)',
    cta_color:            WHITE,
    cta_radius:           R(999),
    cta_size:             14,
    headline_font_family: 'sans-serif',
    headline_size:        0,
    headline_weight:      '700',
    subtitle_size:        0,
    layout:               'inline',
    vertical_align:       'center',
    banner_radius:        R(0),
    banner_padding:       0,
  })])]),
]));

// ─── 4) DISCOGRAPHY ──────────────────────────────────────────────────────────
// Blueprint: .sw-disco { grid-template-columns: repeat(4,1fr); gap: 18px }
// .sw-album h3 { font-size: 17px; color: #fff }
// .sw-album .yr { font-size: 13px; color: var(--txt-dim) }
// .sw-album__cover { border-radius: 14px; border: 1px solid var(--line) }
// Resa: info-cards 4 col, no counter, title=nome album, footer=tipo·anno.
// Sfondo sezione: plain BG (#0c0c10).
home.push(sec(BG, 'large', [
  row([col('1-1', [shead('Discography', 'Everything ', 'else', '')])]),
  row([col('1-1', [tile('info-cards', {
    container_bg:      { type: 'solid', color: 'transparent' },
    container_padding: 0,
    container_gap:     18,
    columns:           4,
    items_gap:         18,
    card_bg:           { type: 'solid', color: PANEL },
    card_color:        DIM,
    card_radius:       R(14),
    card_padding:      24,
    show_icon:         false,
    show_counter:      false,
    show_counter_label: false,
    show_arrow:        false,
    show_footer:       true,
    show_media:        false,
    title_color:       WHITE,
    title_font_family: 'sans-serif',
    title_size:        17,
    title_weight:      '700',
    title_italic:      false,
    description_size:  0,
    footer_size:       13,
    items: [
      { title: 'Nightglass', description: '', footer_text: 'LP · 2026',      footer_dot_color: MINT },
      { title: 'Halflight',  description: '', footer_text: 'EP · 2024',      footer_dot_color: DIM  },
      { title: 'Drift',      description: '', footer_text: 'Single · 2023',  footer_dot_color: DIM  },
      { title: 'Ember',      description: '', footer_text: 'LP · 2021',      footer_dot_color: DIM  },
    ],
    card_hover_effect: 'lift',
  })])]),
]));

// ─── 5) STEP SEQUENCER ───────────────────────────────────────────────────────
// Blueprint: .sw-seq { border: 1px solid var(--line); border-radius: 18px;
//   background: var(--panel); box-shadow: 0 30px 70px -42px rgba(39,224,163,.45) }
// Sfondo sezione: .sw-sec.panel → background: var(--bg-2)
// SEGNALATO: StepSequencer NEW tile candidate — griglia interattiva 4×16 + Web Audio
//   non riproducibili in OLObuild. Resa statica: 4 voci come info-cards 4 col.
home.push(sec(BG2, 'large', [
  row([col('1-1', [shead(
    'Make something',
    'Build ',
    'a beat',
    `Tap the squares to switch steps on, hit play and ride the tempo. Four voices, sixteen steps — synthesised live in your browser.`,
  )])]),
  row([col('1-1', [tile('info-cards', {
    container_bg:      { type: 'solid', color: PANEL },
    container_padding: 32,
    container_gap:     16,
    columns:           4,
    items_gap:         16,
    card_bg:           { type: 'solid', color: BG2 },
    card_color:        DIM,
    card_radius:       R(8),
    card_padding:      22,
    show_icon:         true,
    show_counter:      false,
    show_arrow:        false,
    show_footer:       false,
    show_media:        false,
    icon_color:        MINT,
    icon_bg_color:     'rgba(39,224,163,.12)',
    title_color:       WHITE,
    title_font_family: 'sans-serif',
    title_size:        18,
    title_weight:      '700',
    title_italic:      false,
    description_size:  14,
    items: [
      { icon: 'music',    title: 'Kick',  description: `Four on the floor. Backbone of the pattern — beats 1, 5, 9, 13.` },
      { icon: 'zap',      title: 'Snare', description: `Off-beat snap. Hits on 5 and 13, keeping the swing alive.` },
      { icon: 'waves',    title: 'Hat',   description: `Eighth-note hi-hat running throughout the bar, driving the groove.` },
      { icon: 'sparkles', title: 'Clap',  description: `Syncopated hand clap, landing on steps 4, 12 for texture.` },
    ],
    card_hover_effect: 'lift',
  })])]),
]));

// ─── 6) BIO — split (hero-split image-free, showcase con stats) ──────────────
// Blueprint: .sw-bio { grid-template-columns: 1fr 1fr; gap: 52px }
// Media a sinistra (.sw-bio__media .media { aspect-ratio: 4/5; border-radius: 18px })
// Stats: 2.4M Monthly listeners · 3 Albums · 140+ Shows played  (3 voci, NON 4)
// Sfondo sezione: .sw-sec.panel → var(--bg-2) = #101016 (non --panel!)
home.push(sec(BG2, 'large', [row([col('1-1', [tile('hero-split', {
  eyebrow_text:         'About',
  eyebrow_dot_color:    MINT,
  eyebrow_color:        MINT,
  headline_lines: [
    { text: 'Kova', color: WHITE, italic: false },
  ],
  headline_font_family: 'sans-serif',
  headline_font_size:   46,
  headline_line_height: 1.02,
  headline_font_weight: '700',
  headline_align:       'left',
  subhead: `Producer, multi-instrumentalist and live performer. Kova builds tracks the slow way — field recordings, modular synths and a refusal to quantise the life out of anything. Since 2019, three records, two continents, and one very patient sound engineer.`,
  subhead_color:    TXT,
  subhead_size:     16,
  subhead_italic:   false,
  subhead_max_width: 500,
  cta1_text:         'Streaming',
  cta1_url:          '#listen',
  cta1_bg:           MINT,
  cta1_color:        INK,
  cta1_size:         14,
  cta1_radius:       R(999),
  cta1_radius_hover: R(999),
  cta2_text:         'Press kit',
  cta2_url:          '#',
  cta2_bg:           'rgba(255,255,255,.05)',
  cta2_color:        WHITE,
  cta2_border:       LINE,
  cta2_size:         14,
  cta2_radius:       R(999),
  cta2_radius_hover: R(999),
  stats: [],
  showcase_enabled:      true,
  showcase_bg:           { type: 'solid', color: PANEL },
  showcase_padding:      32,
  showcase_radius:       R(18),
  showcase_radius_hover: R(18),
  showcase_badge_text:   'KOVA · AT A GLANCE',
  showcase_badge_dot:    MINT,
  showcase_badge_bg:     INK,
  showcase_badge_color:  WHITE,
  // 3 voci come nel blueprint (sw-bio__stats ha 3 div)
  showcase_items: [
    { number: 'Monthly listeners', text: '2.4M', italic: false, text_color: MINT,  bg: { type: 'solid', color: BG2 } },
    { number: 'Albums',            text: '3',    italic: false, text_color: WHITE, bg: { type: 'solid', color: BG2 } },
    { number: 'Shows played',      text: '140+', italic: false, text_color: WHITE, bg: { type: 'solid', color: BG2 } },
  ],
  showcase_card_radius:       R(11),
  showcase_card_radius_hover: R(11),
  showcase_card_shadow:       'none',
  showcase_caption_left:  'KOVA',
  showcase_caption_right: '2019 – NOW',
  showcase_hover_effect:  'none',
  split_ratio:    '1fr 1fr',
  gap:            52,
  min_height:     0,
  tile_padding:   { top: 0, right: 0, bottom: 0, left: 0 },
})])])]));

// ─── 7) LISTEN CTA + piattaforme ─────────────────────────────────────────────
// Blueprint: .sw-cta__box {
//   border: 1px solid var(--line-2); border-radius: 24px; padding: clamp(48px,7vw,84px);
//   text-align: center; background: linear-gradient(150deg, rgba(39,224,163,.16), rgba(255,93,158,.06)) }
// h2 "Press play." · p "Stream Nightglass..." · 2 btn (mint + ghost) · piattaforme
home.push(sec(BG, 'large', [
  row([col('1-1', [tile('cta-banner', {
    headline:               'Press',
    headline_accent:        'play.',
    headline_accent_italic: false,
    subtitle:               `Stream Nightglass wherever you listen, or come hear it the way it's meant to be — loud.`,
    cta_text:               'Stream the album',
    cta_url:                '#',
    cta2_text:              'Get tickets',
    cta2_url:               '#tour',
    cta2_bg:                'rgba(255,255,255,.05)',
    cta2_color:             WHITE,
    cta2_border:            LINE,
    bg:                     { type: 'gradient', direction: '150deg', stops: [
      { color: 'rgba(39,224,163,.16)', position: 0 },
      { color: 'rgba(255,93,158,.06)', position: 100 },
    ]},
    text_color:             WHITE,
    accent_color:           MINT,
    subtitle_color:         TXT,
    cta_bg:                 MINT,
    cta_color:              INK,
    cta_radius:             R(999),
    cta_size:               15,
    headline_font_family:   'sans-serif',
    headline_size:          64,
    headline_weight:        '700',
    subtitle_size:          17,
    layout:                 'stack',
    vertical_align:         'center',
    banner_radius:          R(24),
    banner_padding:         80,
  })])]),
  // Piattaforme streaming: pill come nel blueprint
  // .sw-platforms span { border: 1px solid var(--line); border-radius: 999px; padding: 8px 16px }
  row([col('1-1', [tile('trust-strip', {
    items: [
      { text: 'Spotify' },
      { text: 'Apple Music' },
      { text: 'Bandcamp' },
      { text: 'SoundCloud' },
      { text: 'YouTube' },
    ],
    variant:         'pill',
    separator_char:  '',
    align:           'center',
    flow:            'wrap',
    gap:             12,
    font_family:     'sans-serif',
    text_color:      TXT,
    text_size:       13,
    pill_bg:         'rgba(255,255,255,.04)',
    pill_border:     LINE,
    pill_text_color: TXT,
  })])]),
]));

// ─── EMIT ─────────────────────────────────────────────────────────────────────
K.emit({
  slug: 'soundwave',
  name: 'Soundwave',
  tags: ['music', 'artist', 'musician', 'band', 'electronic'],
  description: `Soundwave — Artist/musician homepage. Dark #0c0c10 + neon mint #27e0a3 + pink #ff5d9e. Unbounded (display) + Figtree (body). Kova — album Nightglass. Tile-pure: hero-split, info-cards, cta-banner, trust-strip, section-header. SEGNALATO: AudioHero→hero-split (mini-player waveform non riproducibile), Tracklist→split col+info-cards (hover-play non riproducibile), StepSequencer→info-cards statico (nessun tile Web Audio in OLObuild).`,
  colors: {
    primary:           MINT,
    primary_contrast:  INK,
    secondary:         PINK,
    secondary_contrast: WHITE,
    muted:             BG2,
    muted_contrast:    TXT,
    text:              TXT,
    text_muted:        DIM,
    background:        BG,
    border:            LINE,
    link:              MINT,
  },
  css_disp:            `"Unbounded", -apple-system, sans-serif`,
  css_sans:            `"Figtree", -apple-system, sans-serif`,
  heading_weight:      '700',
  heading_line_height: '1.02',
  google_fonts:        ['Unbounded', 'Figtree'],
  logo_variant:        'light',
  menu: [
    { title: 'Music',  url: '#release' },
    { title: 'Tour',   url: '#tour'    },
    { title: 'About',  url: '#bio'     },
    { title: 'Listen', url: '#listen'  },
  ],
  header: {
    bg:         'rgba(12,12,16,.84)',
    text_color: DIM,
    sticky_bg:  'rgba(12,12,16,.92)',
    logo_width: 120,
  },
  footer: {
    bg:        BG2,
    headColor: WHITE,
    brand: {
      name:    'Kova',
      tagline: 'Electronic producer & live act. New album Nightglass out now.',
    },
    columns: [
      { title: 'Music',   links: ['Nightglass', 'Discography', 'Streaming', 'Remixes'] },
      { title: 'Live',    links: ['Tour dates', 'Past shows', 'Booking'] },
      { title: 'Connect', links: ['Instagram', 'Bandcamp', 'Newsletter', 'Press kit'] },
    ],
    bottom: {
      left:  `© 2026 Kova — an OLOtheme demo.`,
      right: 'Built with OLObuild',
    },
  },
  cursor: {
    blend_mode:  'exclusion',
    ring_color:  WHITE,
    dot_color:   MINT,
  },
}, home);
