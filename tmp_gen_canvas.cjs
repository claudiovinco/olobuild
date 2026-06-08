/* Canvas — ricomposizione TILE-PURE (image-free). Artist / painter portfolio.
   Gilda Display + Mulish. Dark warm gallery + amber.
   Sezioni: hero (centrato), works-grid, about, exhibitions, mixer(best-effort), cta (2 bottoni). */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('cv');

const BG    = '#1c1a17';
const BG2   = '#211f1b';
const PANEL = '#272320';
const PANEL2= '#312c27';
const INK   = '#100e0c';
const AMBER = '#e0b13a';
const AMBERL= '#ecc35e';
const CREAM = '#f0ebe1';
const TXT   = '#b3aa9b';
const DIM   = '#857c6d';
const LINE  = 'rgba(240,235,225,.12)';
const LINE2 = 'rgba(224,177,58,.42)';

const home = [];

// ─── helper: section-header centrato ─────────────────────────────────────────
const shead = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: AMBER, eyebrow_dot_color: AMBER, eyebrow_separator: '',
  headline_lines: [
    { text: l1,     color: CREAM, italic: false },
    { text: accent, color: AMBER, italic: true  },
  ],
  headline_font_family: 'serif', headline_font_size: 46, headline_font_weight: '400', headline_align: 'center', headline_inline: true,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false, tagline_text_color: DIM, tagline_text_size: 16.5,
  layout: 'center', gap: 16,
});

// section-header allineato a sinistra
const sheadLeft = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: AMBER, eyebrow_dot_color: AMBER, eyebrow_separator: '',
  headline_lines: [
    { text: l1,     color: CREAM, italic: false },
    { text: accent, color: AMBER, italic: true  },
  ],
  headline_font_family: 'serif', headline_font_size: 44, headline_font_weight: '400', headline_align: 'left', headline_inline: true,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false, tagline_text_color: TXT, tagline_text_size: 16,
  layout: 'stack', gap: 12,
});

// ─── 1) HERO — SmearHero è NEW tile; riprodotto con hero-split showcase-off centrato ─
// Blueprint: centrato full-width, eyebrow + h1 + p, radial-gradient bg decorativo.
// CSS: text-align:center, max-width:760px, font-size clamp(52px,10vw,140px) per h1.
// hero-split con showcase_enabled:false rende solo la colonna headline centrata.
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('hero-split', {
  eyebrow_text: 'Painter · oil & pigment', eyebrow_dot_color: AMBER, eyebrow_color: AMBER,
  headline_lines: [
    { text: 'Jonah', color: CREAM, italic: false },
    { text: 'Veld',  color: AMBER, italic: true  },
  ],
  headline_font_family: 'serif', headline_font_size: 96, headline_line_height: 0.96, headline_font_weight: '400', headline_align: 'center',
  subhead: `Large-scale abstracts about colour, weather and memory.`,
  subhead_color: CREAM, subhead_size: 18, subhead_italic: false, subhead_max_width: 460, subhead_align: 'center',
  cta1_text: 'Enquire', cta1_url: '#contact',
  cta1_bg: AMBER, cta1_color: INK, cta1_size: 13, cta1_radius: R(2), cta1_radius_hover: R(2),
  cta2_text: 'View the series', cta2_url: '#works',
  cta2_bg: 'transparent', cta2_color: CREAM, cta2_border: LINE2, cta2_size: 13, cta2_radius: R(2), cta2_radius_hover: R(2),
  stats: [],
  showcase_enabled: false,
  split_ratio: '1fr 0fr', gap: 0, min_height: 0,
  tile_padding: { top: 60, right: 30, bottom: 60, left: 30 },
}) ]) ]) ]));

// ─── 2) SELECTED WORKS — WorkGrid (NEW tile) → info-cards 2 col ───────────
// CSS: .cv-works grid 2-col, ogni opera: media(aspect-ratio 4/3 o 4/5 per tall), h3 serif 24px, meta dim 12px, desc dim 14px.
// Info-cards con show_media:true, counter=anno+tipo, title=titolo opera, description=dimensioni + disponibilità.
// Background sezione BG2 (.cv-sec default), card bg PANEL, 1px solid LINE border.
home.push(sec(BG2, 'large', [
  row([ col('1-1', [ sheadLeft('Selected work', 'Recent ', 'paintings') ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' }, container_padding: 0, container_gap: 0, columns: 2, items_gap: 32,
    card_bg: { type: 'solid', color: PANEL }, card_color: DIM, card_radius: R(0), card_padding: 0,
    card_border: `1px solid ${LINE}`,
    show_icon: false, show_counter: true, show_counter_label: true, show_arrow: false, show_footer: false, show_media: true,
    media_aspect_ratio: '4/3', media_position: 'top',
    counter_shape: 'plain', counter_color: DIM, counter_size: 12,
    title_color: CREAM, title_font_family: 'serif', title_size: 24, title_weight: '400', title_italic: false,
    description_size: 14,
    items: [
      {
        counter: `'26 · Oil`, counter_label: '',
        title: 'Tideline No.4',
        description: '180 × 140 cm · private collection',
        media_label: 'Tideline No.4 — oil on linen',
      },
      {
        counter: `'25 · Pigment`, counter_label: '',
        title: 'Weatherfront',
        description: '120 × 90 cm · available',
        media_label: 'Weatherfront — pigment',
      },
      {
        counter: `'25 · Oil`, counter_label: '',
        title: 'Field Study 11',
        description: '60 × 60 cm · available',
        media_label: 'Field Study 11',
      },
      {
        counter: `'24 · Oil`, counter_label: '',
        title: 'Nightfall',
        description: '200 × 150 cm · on loan',
        media_label: 'Nightfall — oil on board',
      },
    ],
    card_hover_effect: 'lift',
  }) ]) ]),
]));

// ─── 3) ABOUT — FeatureSplit → hero-split ────────────────────────────────────
// CSS: .cv-about grid 1fr 1.3fr; media (sinistra) aspect-ratio 4/5; testo (destra).
// hero-split: showcase (destra nel default) → inversione con split_ratio '1fr 1.3fr'
// ma la media è a sinistra nel blueprint. hero-split mette testo|showcase, non showcase|testo.
// Workaround: split_ratio '1.3fr 1fr' col testo (sinistra) PIÙ LARGO e showcase (destra) stretto.
// Corrisponde a: testo 1.3fr | showcase 1fr — vs CSS 1fr media | 1.3fr testo.
// Inversione speculare accettabile dato che lo showcase è image-free (pannello astratto).
// CSS headline: font-size clamp(30px,4.2vw,52px); due paragrafi + .sig italic serif 24px.
home.push(sec(PANEL, 'large', [ row([ col('1-1', [ tile('hero-split', {
  eyebrow_text: 'About', eyebrow_dot_color: AMBER, eyebrow_color: AMBER,
  headline_lines: [
    { text: 'Painting the ', color: CREAM, italic: false },
    { text: 'edges',        color: AMBER, italic: true  },
    { text: 'of weather',   color: CREAM, italic: false },
  ],
  headline_font_family: 'serif', headline_font_size: 48, headline_line_height: 1.06, headline_font_weight: '400', headline_align: 'left',
  subhead: `Jonah Veld (b. 1986) works in oil and hand-ground pigment from a harbour studio. His large abstracts begin outdoors, in notebooks and weather, and are finished slowly indoors over months.\n\nExhibited across Europe and held in private and public collections, his work is represented by Atelier 9.\n\n— studio notes`,
  subhead_color: TXT, subhead_size: 16, subhead_italic: false, subhead_max_width: 520,
  cta1_text: 'Enquire', cta1_url: '#contact',
  cta1_bg: AMBER, cta1_color: INK, cta1_size: 13, cta1_radius: R(2), cta1_radius_hover: R(2),
  cta2_text: '', cta2_url: '',
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: BG },
  showcase_padding: 0, showcase_radius: R(0), showcase_radius_hover: R(0),
  showcase_badge_text: '', showcase_badge_dot: AMBER, showcase_badge_bg: PANEL2, showcase_badge_color: CREAM,
  showcase_items: [],
  showcase_card_radius: R(0), showcase_card_radius_hover: R(0), showcase_card_shadow: 'none',
  showcase_caption_left: '', showcase_caption_right: '',
  showcase_hover_effect: 'none',
  split_ratio: '1.3fr 1fr', gap: 56, min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
}) ]) ]) ]));

// ─── 4) EXHIBITIONS — HoverList (NEW tile) → info-cards 1-col ────────────────
// CSS: .cv-exh border-top; ogni riga: yr amber serif 20px | ti cream serif 20px | pl dim 13px.
// Info-cards 1-col, card trasparente con border bottom, show_counter=true (anno amber),
// title (titolo mostra cream serif), description (luogo dim).
// Sezione background BG.
home.push(sec(BG, 'large', [
  row([ col('1-1', [ shead('Selected shows', 'Exhibitions', '') ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' }, container_padding: 0, container_gap: 0, columns: 1, items_gap: 0,
    card_bg: { type: 'solid', color: 'transparent' }, card_color: DIM, card_radius: R(0), card_padding: 20,
    card_border: `0 0 1px 0`,
    show_icon: false, show_counter: true, show_counter_label: false, show_arrow: false, show_footer: false, show_media: false,
    counter_shape: 'plain', counter_color: AMBER, counter_size: 20,
    title_color: CREAM, title_font_family: 'serif', title_size: 20, title_weight: '400', title_italic: false,
    description_size: 13,
    items: [
      { counter: `'26`, title: `Tideline — solo`,  description: 'Atelier 9, Amsterdam' },
      { counter: `'25`, title: `Weather Systems`,       description: 'Kunsthalle, Oslo' },
      { counter: `'25`, title: `Group: Pigment`,        description: 'Tate Exchange, London' },
      { counter: `'24`, title: `Nightfall — solo`, description: 'Galerie Sud, Paris' },
    ],
    card_hover_effect: 'lift',
  }) ]) ]),
]));

// ─── 5) MIXER (palette interactive zone) ─────────────────────────────────────
// CSS: .cv-mix grid 1.1fr .9fr, sezione su BG2.
home.push(sec(BG2, 'large', [
  row([ col('1-1', [ tile('mixer', {
    eyebrow: 'On the palette',
    heading: 'Mix a colour',
    intro: `Painting is mostly mixing. Tap two or three pigments from my working palette and watch the colour settle — the same muddy, hopeful guesswork I do every morning.`,
    max: 3,
    empty_label: 'Pick pigments to mix',
    zone_accent: '#e0b13a',
    zone_on: '#100e0c',
    card_bg: PANEL,
    card_border: LINE,
    align: 'left',
    items: [
      {name: 'Cadmium Red',    color: '#c9352a'},
      {name: 'Yellow Ochre',   color: '#e0b13a'},
      {name: 'Ultramarine',    color: '#2f5fa8'},
      {name: 'Viridian',       color: '#3c6b4a'},
      {name: 'Burnt Umber',    color: '#7a4a2b'},
      {name: 'Titanium White', color: '#efe9dc'},
    ],
  }) ]) ]),
]));

// ─── 6) CTA — 2 bottoni ──────────────────────────────────────────────────────
// CSS: .cv-cta__box border:1px solid var(--line-2), border-radius:6px, bg:var(--ink).
// .cv-cta__cta: 2 btn — btn--amber (Enquire about a work) + btn--out (View the series).
// Sezione BG (outer), box INK con border LINE2, centrato.
home.push(sec(BG, 'large', [ row([ col('1-1', [ tile('cta-banner', {
  headline: 'Acquire or ', headline_accent: 'enquire', headline_accent_italic: true,
  subtitle: `For available works, commissions or press, get in touch with the studio or Atelier 9.`,
  cta_text: 'Enquire about a work', cta_url: '#contact',
  cta2_text: 'View the series', cta2_url: '#works',
  cta2_bg: 'transparent', cta2_color: CREAM, cta2_border: LINE2,
  bg: { type: 'solid', color: INK }, text_color: CREAM, accent_color: AMBER, subtitle_color: TXT,
  cta_bg: AMBER, cta_color: INK, cta_radius: R(2), cta_size: 13,
  headline_font_family: 'serif', headline_size: 52, headline_weight: '400', subtitle_size: 17,
  layout: 'stack', vertical_align: 'center', banner_radius: R(6), banner_padding: 80,
  border: { top: { width: 1, style: 'solid', color: LINE2 }, right: { width: 1, style: 'solid', color: LINE2 }, bottom: { width: 1, style: 'solid', color: LINE2 }, left: { width: 1, style: 'solid', color: LINE2 } },
}) ]) ]) ]));

// ─── EMIT ─────────────────────────────────────────────────────────────────────
K.emit({
  slug: 'canvas', name: 'Canvas',
  tags: ['artist', 'portfolio', 'creative', 'painter'],
  description: `Canvas — Jonah Veld, painter. Dark warm gallery palette (ink/amber/cream). Gilda Display (display) + Mulish. Riproduzione fedele dell'OLOtheme Canvas (Artist).`,
  colors: {
    primary: AMBER, primary_contrast: INK,
    secondary: AMBERL, secondary_contrast: INK,
    muted: BG2, muted_contrast: TXT,
    text: TXT, text_muted: DIM,
    background: BG, border: LINE, link: AMBER,
  },
  css_disp:  `"Gilda Display", Georgia, serif`,
  css_sans:  `"Mulish", -apple-system, sans-serif`,
  heading_weight: '400', heading_line_height: '1.08',
  google_fonts: ['Gilda Display', 'Mulish'],
  logo_variant: 'light',
  menu: [
    { title: 'Work',        url: '#works'       },
    { title: 'About',       url: '#about'       },
    { title: 'Exhibitions', url: '#exhibitions' },
    { title: 'Contact',     url: '#contact'     },
  ],
  header: { bg: 'rgba(28,26,23,.86)', text_color: TXT, sticky_bg: 'rgba(28,26,23,.92)', logo_width: 138 },
  footer: {
    bg: BG2, headColor: CREAM,
    brand: { name: 'Jonah Veld', tagline: 'Painter, working in oil & pigment. Represented by Atelier 9.' },
    columns: [
      { title: 'Work',    links: ['Paintings', 'Exhibitions', 'Press'] },
      { title: 'Studio',  links: ['About', 'Commissions', 'Studio visits'] },
      { title: 'Contact', links: ['studio@jonahveld.art', 'Atelier 9, Amsterdam', '@jonahveld'] },
    ],
    bottom: { left: '© 2026 Jonah Veld — an OLOtheme demo.', right: 'Built with OLObuild' },
  },
  cursor: { blend_mode: 'normal', ring_color: AMBER, dot_color: AMBER },
}, home);
