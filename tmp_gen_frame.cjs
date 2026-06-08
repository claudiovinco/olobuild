/* Frame — ricomposizione TILE-PURE (image-free, editoriale content-first).
   Media & News: near-monochrome, Archivo display+mono, photo-journal. */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('fr');

// ─── Palette dal :root di frame.css ───────────────────────────────────────
const BG    = '#0c0c0c';
const BG2   = '#121212';
const PANEL = '#1a1a1a';
const PANEL2= '#222222';
const INK   = '#000000';
const ACCENT= '#e8e8e8';
const HOT   = '#ff4438';
const CREAM = '#f4f4f2';
const TXT   = '#9a9a9a';
const DIM   = '#666666';
const LINE  = 'rgba(255,255,255,.12)';
const LINE2 = 'rgba(255,255,255,.3)';
const WHITE = '#ffffff';
const VTINT = 'rgba(255,68,56,.13)';

const home = [];

// ─── HELPER section-header ─────────────────────────────────────────────────
const shead = (eyebrow, line1, accent, intro) => tile('section-header', {
  eyebrow_show: true,
  eyebrow_text: eyebrow,
  eyebrow_color: HOT,
  eyebrow_dot_color: HOT,
  eyebrow_separator: '',
  headline_lines: [
    { text: line1, color: CREAM, italic: false },
    { text: accent, color: CREAM, italic: false },
  ],
  headline_font_family: 'sans-serif',
  headline_font_size: 42,
  headline_font_weight: '800',
  headline_align: 'left',
  headline_inline: true,
  tagline_show: !!intro,
  tagline_text: intro || '',
  tagline_text_italic: false,
  tagline_text_color: TXT,
  tagline_text_size: 16,
  layout: 'stack',
  gap: 12,
});

// ─── 1) COVER HERO (PhotoCover → hero-split, pannello astratto a destra) ──
// Approssimazione: PhotoCover (NEW tile non esiste) → hero-split con
// showcase panel che simula il full-bleed: griglia-crosshatch tramite
// showcase_items con label categoria/autore/frames.
home.push(sec(BG, 'large', [
  row([
    col('1-1', [
      tile('hero-split', {
        eyebrow_text: `Photo Essay · Issue 41`,
        eyebrow_dot_color: HOT,
        eyebrow_color: HOT,
        headline_lines: [
          { text: 'The City', color: CREAM, italic: false },
          { text: 'After Rain', color: CREAM, italic: false },
        ],
        headline_font_family: 'sans-serif',
        headline_font_size: 80,
        headline_line_height: 0.9,
        headline_font_weight: '800',
        headline_align: 'left',
        subhead: `Photographs · Yuki Mori · 28 frames · 12 min`,
        subhead_color: TXT,
        subhead_size: 14,
        subhead_italic: false,
        subhead_max_width: 480,
        cta1_text: 'Read the essay',
        cta1_url: '#essays',
        cta1_bg: HOT,
        cta1_color: WHITE,
        cta1_size: 12,
        cta1_radius: R(0),
        cta1_radius_hover: R(0),
        cta2_text: 'Browse archive',
        cta2_url: '#grid',
        cta2_bg: 'transparent',
        cta2_color: CREAM,
        cta2_border: LINE2,
        cta2_size: 12,
        cta2_radius: R(0),
        cta2_radius_hover: R(0),
        stats: [],
        showcase_enabled: true,
        showcase_bg: { type: 'solid', color: PANEL },
        showcase_padding: 24,
        showcase_radius: R(0),
        showcase_radius_hover: R(0),
        showcase_badge_text: `COVER · ISSUE 41`,
        showcase_badge_dot: HOT,
        showcase_badge_bg: INK,
        showcase_badge_color: CREAM,
        showcase_items: [
          { number: 'Category',      text: 'Photo Essay',            italic: false, text_color: HOT,   bg: { type: 'solid', color: BG2 } },
          { number: 'Photographer',  text: 'Yuki Mori',              italic: true,  text_color: CREAM, bg: { type: 'solid', color: BG2 } },
          { number: 'Frames',        text: '28',                     italic: false, text_color: CREAM, bg: { type: 'solid', color: BG2 } },
          { number: 'Read time',     text: '12 min',                 italic: false, text_color: CREAM, bg: { type: 'solid', color: BG2 } },
        ],
        showcase_card_radius: R(0),
        showcase_card_radius_hover: R(0),
        showcase_card_shadow: 'none',
        showcase_caption_left: 'FRAME',
        showcase_caption_right: '2026',
        showcase_hover_effect: 'none',
        split_ratio: '1.1fr .9fr',
        gap: 48,
        min_height: 0,
        tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
      }),
    ]),
  ]),
]));

// ─── 2) PHOTO-ESSAY RAIL (PhotoRail → info-cards 4 essay-cards) ────────────
// Approssimazione: drag-scroll rail non esiste → info-cards 4 colonne
// card-tipo con kicker=categoria, title=titolo, footer=autore+frames.
// fr-sec senza .panel = BG (#0c0c0c)
home.push(sec(BG, 'large', [
  row([
    col('1-1', [
      tile('section-header', {
        eyebrow_show: false,
        eyebrow_text: '',
        eyebrow_color: HOT,
        eyebrow_dot_color: HOT,
        eyebrow_separator: '',
        headline_lines: [
          { text: 'Photo essays', color: CREAM, italic: false },
        ],
        headline_font_family: 'sans-serif',
        headline_font_size: 34,
        headline_font_weight: '800',
        headline_align: 'left',
        headline_inline: false,
        tagline_show: false,
        layout: 'stack',
        gap: 8,
      }),
    ]),
  ]),
  row([
    col('1-1', [
      tile('info-cards', {
        container_bg: { type: 'solid', color: 'transparent' },
        container_padding: 0,
        container_gap: 0,
        columns: 4,
        items_gap: 16,
        card_bg: { type: 'solid', color: PANEL },
        card_color: TXT,
        card_radius: R(0),
        card_padding: 22,
        show_icon: true,
        show_counter: false,
        show_counter_label: false,
        show_arrow: false,
        show_footer: true,
        show_media: false,
        icon_color: HOT,
        icon_bg_color: VTINT,
        title_color: CREAM,
        title_font_family: 'sans-serif',
        title_size: 18,
        title_weight: '800',
        title_italic: false,
        description_size: 12,
        footer_size: 11,
        card_hover_effect: 'lift',
        items: [
          {
            icon: 'camera',
            counter: '01',
            counter_label: 'Documentary',
            title: 'Last of the Harbour',
            description: 'Documentary',
            footer_text: `A. Costa · 22 frames`,
            footer_dot_color: HOT,
            link_url: '#essays',
          },
          {
            icon: 'camera',
            counter: '02',
            counter_label: 'Portrait',
            title: 'Faces of the Night Shift',
            description: 'Portrait',
            footer_text: `R. Singh · 18 frames`,
            footer_dot_color: HOT,
            link_url: '#essays',
          },
          {
            icon: 'mountain',
            counter: '03',
            counter_label: 'Landscape',
            title: 'Two Lanes, No Signal',
            description: 'Landscape',
            footer_text: `M. Reyes · 30 frames`,
            footer_dot_color: HOT,
            link_url: '#essays',
          },
          {
            icon: 'map-pin',
            counter: '04',
            counter_label: 'Street',
            title: `Six A.M. Market`,
            description: 'Street',
            footer_text: `Y. Mori · 24 frames`,
            footer_dot_color: HOT,
            link_url: '#essays',
          },
        ],
      }),
    ]),
  ], { gap: 24 }),
]));

// ─── 3) ARTICLE GRID / LATEST (ArticleGrid → section-header + info-cards 5 items) ─
// Approssimazione: griglia asimmetrica 7+5+4+4+4 → info-cards 5 card.
// fr-sec.panel = background: var(--bg-2) = BG2 (#121212)
home.push(sec(BG2, 'large', [
  row([
    col('1-1', [
      tile('section-header', {
        eyebrow_show: false,
        eyebrow_text: '',
        eyebrow_color: HOT,
        eyebrow_dot_color: HOT,
        eyebrow_separator: '',
        headline_lines: [
          { text: 'Latest', color: CREAM, italic: false },
        ],
        headline_font_family: 'sans-serif',
        headline_font_size: 34,
        headline_font_weight: '800',
        headline_align: 'left',
        headline_inline: false,
        tagline_show: false,
        layout: 'stack',
        gap: 8,
      }),
    ]),
  ]),
  row([
    col('1-1', [
      tile('info-cards', {
        container_bg: { type: 'solid', color: 'transparent' },
        container_padding: 0,
        container_gap: 0,
        columns: 3,
        items_gap: 16,
        card_bg: { type: 'solid', color: PANEL },
        card_color: TXT,
        card_radius: R(0),
        card_padding: 26,
        show_icon: false,
        show_counter: true,
        show_counter_label: true,
        show_arrow: false,
        show_footer: false,
        show_media: false,
        counter_shape: 'plain',
        counter_color: HOT,
        counter_bg: 'transparent',
        counter_size: 11,
        title_color: CREAM,
        title_font_family: 'sans-serif',
        title_size: 22,
        title_weight: '800',
        title_italic: false,
        description_size: 13,
        card_hover_effect: 'lift',
        items: [
          {
            counter: 'Documentary',
            counter_label: 'Documentary',
            title: 'The Square at Dusk',
            description: '',
            link_url: '#grid',
          },
          {
            counter: 'Portrait',
            counter_label: 'Portrait',
            title: 'Backstage',
            description: '',
            link_url: '#grid',
          },
          {
            counter: 'Landscape',
            counter_label: 'Landscape',
            title: 'White Out',
            description: '',
            link_url: '#grid',
          },
          {
            counter: 'Street',
            counter_label: 'Street',
            title: 'Wet Neon',
            description: '',
            link_url: '#grid',
          },
          {
            counter: 'Documentary',
            counter_label: 'Documentary',
            title: 'The Early Boat',
            description: '',
            link_url: '#grid',
          },
        ],
      }),
    ]),
  ], { gap: 24 }),
]));

// ─── 4) ABOUT / FEATURE SPLIT (FeatureSplit → hero-split testo puro) ──────
// Approssimazione: lato media = pannello PANEL astratto (image-free).
// fr-sec senza .panel = sfondo BG (#0c0c0c)
home.push(sec(BG, 'large', [
  row([
    col('1-1', [
      tile('hero-split', {
        eyebrow_text: 'About Frame',
        eyebrow_dot_color: HOT,
        eyebrow_color: HOT,
        headline_lines: [
          { text: 'The story behind', color: CREAM, italic: false },
          { text: 'the image',        color: CREAM, italic: false },
        ],
        headline_font_family: 'sans-serif',
        headline_font_size: 46,
        headline_line_height: 1.0,
        headline_font_weight: '800',
        headline_align: 'left',
        subhead: `Frame is an independent photo journal. We publish long-form visual essays — documentary, portrait, street and landscape — and the words that earn their place beside them.\n\nNo stock, no filler. Every issue is a handful of photographers given the room to say something.`,
        subhead_color: TXT,
        subhead_size: 16,
        subhead_italic: false,
        subhead_max_width: 520,
        cta1_text: 'Read our story',
        cta1_url: '#about',
        cta1_bg: 'transparent',
        cta1_color: CREAM,
        cta1_border: LINE2,
        cta1_size: 12,
        cta1_radius: R(0),
        cta1_radius_hover: R(0),
        cta2_text: '',
        cta2_url: '',
        cta2_bg: 'transparent',
        cta2_color: CREAM,
        stats: [],
        showcase_enabled: true,
        showcase_bg: { type: 'solid', color: PANEL2 },
        showcase_padding: 28,
        showcase_radius: R(0),
        showcase_radius_hover: R(0),
        showcase_badge_text: `THE DARKROOM`,
        showcase_badge_dot: HOT,
        showcase_badge_bg: INK,
        showcase_badge_color: CREAM,
        showcase_items: [
          { number: 'Est.',         text: '2015',             italic: false, text_color: CREAM, bg: { type: 'solid', color: PANEL } },
          { number: 'Issues',       text: '41',               italic: false, text_color: CREAM, bg: { type: 'solid', color: PANEL } },
          { number: 'Photographers',text: '120+',             italic: false, text_color: HOT,   bg: { type: 'solid', color: PANEL } },
          { number: 'Format',       text: 'Print + Digital',  italic: true,  text_color: TXT,   bg: { type: 'solid', color: PANEL } },
        ],
        showcase_card_radius: R(0),
        showcase_card_radius_hover: R(0),
        showcase_card_shadow: 'none',
        showcase_caption_left: 'FRAME',
        showcase_caption_right: 'DARKROOM',
        showcase_hover_effect: 'none',
        split_ratio: '1fr 1fr',
        gap: 48,
        min_height: 0,
        tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
      }),
    ]),
  ]),
]));

// ─── 5) NEWSLETTER / SUBSCRIBE ─────────────────────────────────────────────
// fr-sec.panel = background: var(--bg-2) = BG2 (#121212)
// kicker "The print & digital edition" resa come section-header eyebrow sopra la newsletter
home.push(sec(BG2, 'large', [
  row([
    col('1-1', [
      tile('section-header', {
        eyebrow_show: true,
        eyebrow_text: 'The print & digital edition',
        eyebrow_color: HOT,
        eyebrow_dot_color: HOT,
        eyebrow_separator: '',
        headline_lines: [],
        headline_font_family: 'sans-serif',
        headline_font_size: 0,
        headline_font_weight: '800',
        headline_align: 'center',
        headline_inline: false,
        tagline_show: false,
        layout: 'stack',
        gap: 0,
      }),
    ]),
  ]),
  row([
    col('1-1', [
      tile('newsletter', {
        layout: 'minimal',
        title: 'See more, scroll less',
        subtitle: `A new visual essay every week in your inbox, and four print issues a year. No ads, ever.`,
        icon_type: 'none',
        show_name: false,
        email_placeholder: 'you@example.com',
        button_text: 'Subscribe',
        button_icon: false,
        privacy_text: '',
        max_width: '520',
        alignment: 'center',
        bg_color: PANEL,
        border_radius: 0,
        tile_padding: { top: 56, right: 48, bottom: 56, left: 48 },
        title_size: '42',
        title_weight: '800',
        title_color: CREAM,
        subtitle_size: '15',
        subtitle_color: TXT,
        input_bg: PANEL2,
        input_color: CREAM,
        input_border: LINE2,
        input_focus_border: HOT,
        input_radius: 0,
        btn_bg: HOT,
        btn_color: WHITE,
        btn_hover_bg: '#e23a30',
        btn_radius: 0,
        btn_font_size: '12',
        btn_font_weight: '700',
        shadow: 'none',
      }),
    ]),
  ]),
]));

// ─── EMIT ──────────────────────────────────────────────────────────────────
K.emit({
  slug: 'frame',
  name: 'Frame',
  tags: ['media', 'news', 'photo-journal', 'editorial', 'magazine'],
  description: `Frame — independent photo journal. Near-monochrome (near-black + hot red #ff4438), Archivo 800 uppercase. Cover essay + photo-rail + article-grid + about-split + newsletter. Riproduzione fedele dell'OLOtheme Frame.`,
  colors: {
    primary:           HOT,
    primary_contrast:  WHITE,
    secondary:         CREAM,
    secondary_contrast:INK,
    muted:             BG2,
    muted_contrast:    TXT,
    text:              TXT,
    text_muted:        DIM,
    background:        BG,
    border:            LINE,
    link:              HOT,
  },
  css_disp:  `"Archivo", -apple-system, sans-serif`,
  css_sans:  `"Archivo", -apple-system, sans-serif`,
  heading_weight:      '800',
  heading_line_height: '1.0',
  google_fonts: ['Archivo', 'Archivo Narrow'],
  logo_variant: 'light',
  menu: [
    { title: 'Essays',    url: '#essays' },
    { title: 'Latest',    url: '#grid' },
    { title: 'About',     url: '#about' },
    { title: 'Subscribe', url: '#subscribe' },
  ],
  header: {
    bg:        'rgba(12,12,12,.86)',
    text_color: TXT,
    sticky_bg: 'rgba(12,12,12,.92)',
    logo_width: 130,
  },
  footer: {
    bg: BG2,
    headColor: CREAM,
    brand: {
      name: 'Frame',
      tagline: `An independent photo journal. Visual essays and the stories behind the image.`,
    },
    columns: [
      { title: 'Read',      links: ['Essays', 'Latest', 'Archive', 'Issues'] },
      { title: 'Journal',   links: ['About', 'Photographers', 'Submit'] },
      { title: 'Subscribe', links: ['Newsletter', 'Print', 'Contact'] },
    ],
    bottom: {
      left:  `© 2026 Frame — an OLOtheme demo.`,
      right: 'Built with OLObuild',
    },
  },
  cursor: {
    blend_mode: 'exclusion',
    ring_color: WHITE,
    dot_color:  WHITE,
  },
}, home);
