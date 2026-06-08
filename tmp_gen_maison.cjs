/* Maison — ricomposizione TILE-PURE (image-free). Home & Living oat + tan. PT Serif + Mulish. */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('ma');

const PAPER  = '#f3eee4';
const PAPER2 = '#e9e1d2';
const CARD   = '#faf6ee';
const INK    = '#2c2419';
const INK2   = '#4a3f2e';
const TAN    = '#a9794f';
const TAND   = '#946638';
const SAGE   = '#7d8466';
const TXT    = '#5d5142';
const DIM    = '#928572';
const LINE   = '#ddd1bd';
const LINE2  = '#c8b89e';
const WHITE  = '#ffffff';
const TANFG  = 'rgba(169,121,79,.13)';

const home = [];

// ─── 1) HERO ─────────────────────────────────────────────────────────────────
// hero-split: testo sinistra + showcase-panel destra (astratto, image-free)
home.push(sec(PAPER, 'large', [ row([ col('1-1', [ tile('hero-split', {
  eyebrow_text:    'Furniture & objects',
  eyebrow_dot_color: TAN,
  eyebrow_color:   TAN,
  headline_lines: [
    { text: `Pieces you’ll`, color: INK,  italic: false },
    { text: 'keep for ',          color: INK,  italic: false },
    { text: 'life.',              color: TAN,  italic: true  },
  ],
  headline_font_family:  'serif',
  headline_font_size:    72,
  headline_line_height:  1.06,
  headline_font_weight:  '400',
  headline_align:        'left',
  subhead:               `Solid materials, honest joinery and shapes that don’t date. Made in small runs to be lived with, not replaced.`,
  subhead_color:         TXT,
  subhead_size:          18,
  subhead_italic:        false,
  subhead_max_width:     420,
  cta1_text:    'Shop the collection',
  cta1_url:     '#shop',
  cta1_bg:      TAN,
  cta1_color:   WHITE,
  cta1_size:    14,
  cta1_radius:  R(2),
  cta1_radius_hover: R(2),
  cta2_text:    'Shop the room',
  cta2_url:     '#room',
  cta2_bg:      'transparent',
  cta2_color:   INK,
  cta2_border:  LINE2,
  cta2_size:    14,
  cta2_radius:  R(2),
  cta2_radius_hover: R(2),
  stats: [],
  showcase_enabled:      true,
  showcase_bg:           { type: 'solid', color: PAPER2 },
  showcase_padding:      32,
  showcase_radius:       R(6),
  showcase_radius_hover: R(6),
  showcase_badge_text:   'MAISON · MADE TO LAST',
  showcase_badge_dot:    TAN,
  showcase_badge_bg:     PAPER,
  showcase_badge_color:  INK,
  showcase_items: [
    { number: 'Solid timber & natural stone', text: 'Materials', italic: false, text_color: TAN, bg: { type: 'solid', color: CARD } },
    { number: 'Small-batch production',        text: 'Method',   italic: false, text_color: INK, bg: { type: 'solid', color: CARD } },
    { number: 'Repaired, not replaced',        text: 'Promise',  italic: false, text_color: INK, bg: { type: 'solid', color: CARD } },
    { number: `Free delivery over €500`,  text: 'Offer',    italic: false, text_color: TAN, bg: { type: 'solid', color: CARD } },
  ],
  showcase_card_radius:       R(4),
  showcase_card_radius_hover: R(4),
  showcase_card_shadow:       'none',
  showcase_caption_left:  'MAISON',
  showcase_caption_right: 'EST. 2018',
  showcase_hover_effect:  'none',
  split_ratio:  '1.1fr .9fr',
  gap:          48,
  min_height:   0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
}) ]) ]) ]));

// ─── 2) SHOP THE ROOM — tile vero Hotspots (4 marker su foto stanza con prezzo). ─
// Blueprint: .ma-room data-hotspot, 4 dot con titolo + prezzo (.pr)
home.push(sec(CARD, 'large', [
  row([ col('1-1', [ tile('hotspots', {
    eyebrow:     'Shop the room',
    heading:     `See it in place`,
    intro:       'Hover the markers to shop each piece in the setting.',
    panel_label: 'styled living room — chair, sideboard, lamp, rug',
    aspect_ratio: '16/10',
    items: [
      { x: 24, y: 55, title: 'Linden Lounge Chair', text: 'Seating',  meta: '€890'   },
      { x: 58, y: 48, title: 'Oak Sideboard',       text: 'Storage',  meta: '€1,640' },
      { x: 78, y: 34, title: 'Paper Floor Lamp',    text: 'Lighting', meta: '€240'   },
      { x: 44, y: 80, title: 'Wool Flatweave Rug',  text: 'Textiles', meta: '€520'   },
    ],
    zone_accent: TAN,
    zone_on:     WHITE,
    panel_bg:    PAPER2,
    card_bg:     PAPER,
    card_border: LINE2,
    align:       'left',
  }) ]) ]),
]));

// ─── 3) COLLECTIONS (approssimazione info-cards — CategoryTiles non esiste) ──
// Blueprint: .ma-cats — 3 card immagine fullheight con overlay gradiente + titolo/sottotitolo
// SEGNALATO: category image cards non riproducibili; approssimazione con info-cards + icone tematiche
home.push(sec(PAPER, 'large', [
  row([ col('1-1', [ tile('section-header', {
    eyebrow_show:   true,
    eyebrow_text:   'By room',
    eyebrow_color:  TAN,
    eyebrow_dot_color: TAN,
    eyebrow_separator: '',
    headline_lines: [
      { text: 'The ',        color: INK, italic: false },
      { text: 'collections', color: TAN, italic: true  },
    ],
    headline_font_family: 'serif',
    headline_font_size:   46,
    headline_font_weight: '400',
    headline_align:       'center',
    headline_inline:      true,
    tagline_show:         false,
    layout:               'center',
    gap:                  16,
  }) ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg:      { type: 'solid', color: 'transparent' },
    container_padding: 0,
    container_gap:     16,
    columns:           3,
    items_gap:         16,
    card_bg:           { type: 'solid', color: CARD },
    card_color:        DIM,
    card_radius:       R(6),
    card_padding:      32,
    show_icon:         true,
    show_counter:      false,
    show_arrow:        true,
    show_footer:       false,
    show_media:        false,
    icon_color:        TAN,
    icon_bg_color:     TANFG,
    title_color:       INK,
    title_font_family: 'serif',
    title_size:        26,
    title_weight:      '400',
    title_italic:      false,
    description_size:  14,
    card_hover_effect: 'lift',
    items: [
      { icon: 'sofa',  title: 'Living',  description: 'Seating & storage' },
      { icon: 'table', title: 'Dining',  description: 'Tables & chairs'   },
      { icon: 'lamp',  title: 'Objects', description: 'Light & home'      },
    ],
  }) ]) ]),
]));

// ─── 4) PRODUCT GRID / NEW THIS SEASON (approssimazione — ProductGrid non esiste) ─
// Blueprint: .ma-prods — 4 colonne con immagine quadrata + categoria + titolo + prezzo
// SEGNALATO: ProductGrid con media nativa non disponibile; approssimazione con info-cards
home.push(sec(CARD, 'large', [
  row([ col('1-1', [ tile('section-header', {
    eyebrow_show:   true,
    eyebrow_text:   'New this season',
    eyebrow_color:  TAN,
    eyebrow_dot_color: TAN,
    eyebrow_separator: '',
    headline_lines: [
      { text: 'Just ',  color: INK, italic: false },
      { text: 'landed', color: TAN, italic: true  },
    ],
    headline_font_family: 'serif',
    headline_font_size:   46,
    headline_font_weight: '400',
    headline_align:       'center',
    headline_inline:      true,
    tagline_show:         false,
    layout:               'center',
    gap:                  16,
  }) ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg:      { type: 'solid', color: 'transparent' },
    container_padding: 0,
    container_gap:     16,
    columns:           4,
    items_gap:         22,
    card_bg:           { type: 'solid', color: PAPER },
    card_color:        DIM,
    card_radius:       R(4),
    card_padding:      24,
    show_icon:         false,
    show_counter:      false,
    show_arrow:        false,
    show_footer:       true,
    show_media:        false,
    title_color:       INK,
    title_font_family: 'serif',
    title_size:        20,
    title_weight:      '400',
    title_italic:      false,
    description_size:  11,
    card_hover_effect: 'lift',
    items: [
      { title: 'Linden Lounge Chair', description: 'Seating',  footer_text: `€890`,    footer_dot_color: TAN },
      { title: 'Oak Sideboard',       description: 'Storage',  footer_text: `€1,640`,  footer_dot_color: TAN },
      { title: 'Trestle Table',       description: 'Dining',   footer_text: `€1,280`,  footer_dot_color: TAN },
      { title: 'Paper Floor Lamp',    description: 'Lighting', footer_text: `€240`,    footer_dot_color: TAN },
    ],
  }) ]) ]),
]));

// ─── 5) OUR STORY / FEATURE SPLIT ─────────────────────────────────────────────
// Blueprint: .ma-story — media SINISTRA, testo DESTRA
// hero-split ha testo a sinistra + showcase a destra (layout fisso, no flip).
// Approssimazione accettabile; showcase (destra) = panel del workshop.
home.push(sec(PAPER, 'large', [ row([ col('1-1', [ tile('hero-split', {
  eyebrow_text:     'Our story',
  eyebrow_dot_color: TAN,
  eyebrow_color:    TAN,
  headline_lines: [
    { text: 'Made in a ',  color: INK, italic: false },
    { text: 'workshop,',   color: TAN, italic: true  },
    { text: 'not a factory', color: INK, italic: false },
  ],
  headline_font_family: 'serif',
  headline_font_size:   52,
  headline_line_height: 1.1,
  headline_font_weight: '400',
  headline_align:       'left',
  subhead:              `Maison began with two cabinetmakers who were tired of furniture built to be thrown away. We work in solid timber, joined the old way, finished by hand. Everything is made in small batches in our own workshop — and built to be repaired, not replaced.`,
  subhead_color:        TXT,
  subhead_size:         16,
  subhead_italic:       false,
  subhead_max_width:    460,
  cta1_text:    'Meet the makers',
  cta1_url:     '#story',
  cta1_bg:      'transparent',
  cta1_color:   INK,
  cta1_border:  LINE2,
  cta1_size:    13,
  cta1_radius:  R(2),
  cta1_radius_hover: R(2),
  cta2_text:    '',
  cta2_url:     '',
  stats: [],
  showcase_enabled:      true,
  showcase_bg:           { type: 'solid', color: PAPER2 },
  showcase_padding:      32,
  showcase_radius:       R(6),
  showcase_radius_hover: R(6),
  showcase_badge_text:   'THE WORKSHOP',
  showcase_badge_dot:    TAN,
  showcase_badge_bg:     PAPER,
  showcase_badge_color:  INK,
  showcase_items: [
    { number: 'Solid timber',   text: 'Primary material',  italic: false, text_color: TAN, bg: { type: 'solid', color: CARD } },
    { number: 'Hand-finished',  text: 'Every piece',       italic: false, text_color: INK, bg: { type: 'solid', color: CARD } },
    { number: 'Small batches',  text: 'Production method', italic: false, text_color: INK, bg: { type: 'solid', color: CARD } },
    { number: 'Repair service', text: 'Lifetime support',  italic: false, text_color: TAN, bg: { type: 'solid', color: CARD } },
  ],
  showcase_card_radius:       R(4),
  showcase_card_radius_hover: R(4),
  showcase_card_shadow:       'none',
  showcase_caption_left:  'SINCE 2018',
  showcase_caption_right: 'LONDON',
  showcase_hover_effect:  'none',
  split_ratio:  '1fr 1fr',
  gap:          56,
  min_height:   0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
}) ]) ]) ]));

// ─── 6) FINDER / ROOM FINDER (tile nativo finder) ────────────────────────────
// Blueprint: .ma-find — chip selettori interattivi + card risultato (tile vero: finder)
// 4 opzioni: Living room / Bedroom / Dining / Study / work → 4 result card
home.push(sec(PAPER2, 'large', [
  row([ col('1-1', [ tile('finder', {
    eyebrow:     'Styling help',
    heading:     'Find your room',
    intro:       'Four curated room edits — everything you need, chosen together.',
    zone_accent: TAN,
    zone_on:     INK,
    card_bg:     CARD,
    card_border: LINE,
    align:       'center',
    items: [
      {
        option:   'Living room',
        icon:     'sofa',
        title:    'The Slow Sunday',
        text:     `A deep oak-framed sofa, a bouclé lounge chair and a travertine coffee table — built to be lived in, not looked at.`,
        meta:     '12 pieces · from €2,400',
        cta_text: 'Shop this room',
        cta_url:  '#living',
      },
      {
        option:   'Bedroom',
        icon:     'bed',
        title:    'The Quiet Room',
        text:     `An ash bed frame, washed-linen everything and a pair of softly-turned nightstands. Made for long mornings.`,
        meta:     '9 pieces · from €1,850',
        cta_text: 'Shop this room',
        cta_url:  '#bedroom',
      },
      {
        option:   'Dining',
        icon:     'utensils',
        title:    'The Long Table',
        text:     `A solid oak table that seats eight, spindle chairs and a low sideboard. The room everyone ends up in.`,
        meta:     '10 pieces · from €2,100',
        cta_text: 'Shop this room',
        cta_url:  '#dining',
      },
      {
        option:   'Study / work',
        icon:     'monitor',
        title:    'The Good Desk',
        text:     `A walnut writing desk, a proper task chair and open shelving — a corner that makes you want to start.`,
        meta:     '7 pieces · from €1,300',
        cta_text: 'Shop this room',
        cta_url:  '#study',
      },
    ],
  }) ]) ]),
]));

// ─── 7) CTA ───────────────────────────────────────────────────────────────────
// Blueprint: .ma-cta — sfondo INK, testo PAPER, 1 bottone TAN "Shop the collection"
home.push(sec(INK, 'large', [ row([ col('1-1', [ tile('cta-banner', {
  headline:               'Bring it',
  headline_accent:        'home.',
  headline_accent_italic: true,
  subtitle:               `Free delivery on orders over €500, and a 100-day trial in your own space.`,
  cta_text:               'Shop the collection',
  cta_url:                '#shop',
  bg:                     { type: 'solid', color: '#2c2419' },
  text_color:             PAPER,
  accent_color:           TAN,
  subtitle_color:         '#c9bba6',
  cta_bg:                 TAN,
  cta_color:              WHITE,
  cta_radius:             R(2),
  cta_size:               14,
  headline_font_family:   'serif',
  headline_size:          60,
  headline_weight:        '400',
  subtitle_size:          17,
  layout:                 'stack',
  vertical_align:         'center',
  banner_radius:          R(0),
  banner_padding:         96,
}) ]) ]) ]));

K.emit({
  slug:        'maison',
  name:        'Maison',
  tags:        ['homeware', 'decor', 'furniture', 'ecommerce', 'shop'],
  description: `Maison — Home & Living. Furniture & objects for living. Warm oat + tan palette, PT Serif (display) + Mulish. Riproduzione fedele dell’OLOtheme Maison.`,
  colors: {
    primary:          TAN,
    primary_contrast: WHITE,
    secondary:        TAND,
    secondary_contrast: WHITE,
    muted:            PAPER2,
    muted_contrast:   TXT,
    text:             TXT,
    text_muted:       DIM,
    background:       PAPER,
    border:           LINE,
    link:             TAN,
  },
  css_disp:            '"PT Serif", Georgia, serif',
  css_sans:            '"Mulish", -apple-system, sans-serif',
  heading_weight:      '400',
  heading_line_height: '1.12',
  google_fonts:        ['PT Serif', 'Mulish'],
  logo_variant:        'dark',
  menu: [
    { title: 'Shop',          url: '#shop'        },
    { title: 'Shop the room', url: '#room'        },
    { title: 'Collections',   url: '#collections' },
    { title: 'Our story',     url: '#story'       },
  ],
  header: {
    bg:         'rgba(243,238,228,.92)',
    text_color: TXT,
    sticky_bg:  'rgba(243,238,228,.95)',
    logo_width: 130,
  },
  footer: {
    bg:        PAPER,
    headColor: INK,
    brand: {
      name:    'Maison',
      tagline: 'Considered furniture & objects for the home. Solid materials, made to last.',
    },
    columns: [
      { title: 'Shop',   links: ['All', 'Living', 'Dining', 'Objects'] },
      { title: 'Maison', links: ['Our story', 'Materials', 'Trade']    },
      { title: 'Care',   links: ['Delivery', '100-day trial', 'Repairs', 'Contact'] },
    ],
    bottom: {
      left:  `© 2026 Maison — an OLOtheme demo.`,
      right: 'Built with OLObuild',
    },
  },
  cursor: false,
}, home);
