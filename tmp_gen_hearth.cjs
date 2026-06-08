/* Hearth — ricomposizione TILE-PURE (image-free). Home & Living interior design studio.
   Palette: espresso/clay/oat/paper. Lora (display) + Work Sans (body). */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('he');

// ---- palette ----
const ESPRESSO = '#2a211c';
const ESPRESSO2 = '#3a2c24';
const CLAY    = '#b06a4f';
const CLAY_D  = '#97593f';
const OAT     = '#ece3d3';
const OAT2    = '#e2d6c1';
const PAPER   = '#faf5ec';
const TXT     = '#2c241d';
const TXT_SOFT= '#6a5f53';
const TXT_FAINT='#9a8e7e';
const LINE    = '#ddd0bb';
const LINE_DK = 'rgba(255,255,255,.15)';
const WHITE   = '#ffffff';
const CLAY_TINT = 'rgba(176,106,79,.14)';

const home = [];

// helper: section-header centrato (eyebrow + headline accentato)
const shead = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: CLAY_D, eyebrow_dot_color: CLAY_D, eyebrow_separator: '',
  headline_lines: [ {text: l1, color: TXT, italic: false}, {text: accent, color: CLAY_D, italic: true} ],
  headline_font_family: 'serif', headline_font_size: 46, headline_font_weight: '600', headline_align: 'center', headline_inline: true,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false, tagline_text_color: TXT_SOFT, tagline_text_size: 16.5,
  layout: 'center', gap: 16,
});

// helper: section-header left (eyebrow + headline)
const sheadLeft = (eyebrow, l1, accent) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: CLAY_D, eyebrow_dot_color: CLAY_D, eyebrow_separator: '',
  headline_lines: [ {text: l1, color: TXT, italic: false}, {text: accent, color: CLAY_D, italic: true} ],
  headline_font_family: 'serif', headline_font_size: 42, headline_font_weight: '600', headline_align: 'left', headline_inline: true,
  tagline_show: false, layout: 'stack', gap: 12,
});

// 1) HERO — image-free: hero-split senza media laterale, copy sopra sfondo espresso scuro
// Il tema ha un hero full-bleed con foto; approssimiamo con hero-split showcase astratto
home.push(sec(ESPRESSO, 'large', [ row([ col('1-1', [ tile('hero-split', {
  eyebrow_text: 'Interior design studio · Milan', eyebrow_dot_color: CLAY, eyebrow_color: OAT,
  headline_lines: [
    {text: 'Rooms that feel', color: WHITE, italic: false},
    {text: 'like home — not', color: CLAY, italic: true},
    {text: 'a showroom.', color: WHITE, italic: false},
  ],
  headline_font_family: 'serif', headline_font_size: 72, headline_line_height: 1.04,
  headline_font_weight: '600', headline_align: 'left',
  subhead: 'We design warm, lived-in spaces with character and longevity — full interiors, styling and renovation, start to finish.',
  subhead_color: 'rgba(255,255,255,.82)', subhead_size: 17, subhead_italic: false, subhead_max_width: 480,
  cta1_text: 'View our work', cta1_url: '#projects',
  cta1_bg: 'transparent', cta1_color: WHITE, cta1_border: 'rgba(255,255,255,.4)', cta1_size: 13, cta1_radius: R(2), cta1_radius_hover: R(2),
  stats: [],
  showcase_enabled: true, showcase_bg: {type: 'solid', color: ESPRESSO2}, showcase_padding: 28,
  showcase_radius: R(4), showcase_radius_hover: R(4),
  showcase_badge_text: 'HEARTH STUDIO · EST. 2011', showcase_badge_dot: CLAY,
  showcase_badge_bg: ESPRESSO, showcase_badge_color: OAT,
  showcase_items: [
    {number: 'Location', text: 'Milan, Italy', italic: false, text_color: CLAY, bg: {type: 'solid', color: ESPRESSO2}},
    {number: 'Speciality', text: 'Full interior design', italic: true, text_color: OAT, bg: {type: 'solid', color: ESPRESSO2}},
    {number: 'Studio since', text: '2011', italic: false, text_color: OAT, bg: {type: 'solid', color: ESPRESSO2}},
    {number: 'Projects', text: '120+ homes', italic: false, text_color: OAT, bg: {type: 'solid', color: ESPRESSO2}},
  ],
  showcase_card_radius: R(3), showcase_card_radius_hover: R(3), showcase_card_shadow: 'none',
  showcase_caption_left: 'HEARTH', showcase_caption_right: 'MILAN · 2026', showcase_hover_effect: 'none',
  split_ratio: '1.3fr .7fr', gap: 52, min_height: 0, tile_padding: {top: 0, right: 0, bottom: 0, left: 0},
}) ]) ]) ]));

// 2) FEATURED PROJECTS — ProjectShowcase (tile non esistente) → approssimato con info-cards
// Sezione con section-header left + 2 info-cards progetto (monogramma + metadati)
home.push(sec(OAT, 'large', [
  row([ col('1-1', [ sheadLeft('Selected projects', 'Places we’ve ', 'made') ]) ]),
  row([ col('1-1', [ tile('info-cards', {
    container_bg: {type: 'solid', color: 'transparent'}, container_padding: 0, container_gap: 20, columns: 2, items_gap: 24,
    card_bg: {type: 'solid', color: PAPER}, card_color: TXT_SOFT, card_radius: R(4), card_padding: 32,
    show_icon: false, show_counter: true, show_counter_label: true, show_arrow: true, show_footer: true, show_media: false,
    counter_shape: 'square', counter_color: CLAY, counter_bg: OAT2,
    title_color: ESPRESSO, title_font_family: 'serif', title_size: 28, title_weight: '600', title_italic: false,
    description_size: 15.5, counter_size: 20, footer_size: 12,
    card_hover_effect: 'lift',
    items: [
      {
        counter: 'CN', counter_label: 'Full interior',
        title: 'Casa Navigli',
        description: `A 1930s apartment brought back to warmth — original parquet, soft plaster walls and furniture that earns its keep.`,
        footer_text: 'Residential · Milan · 2025', footer_dot_color: CLAY,
        arrow_color: CLAY_D,
      },
      {
        counter: 'BT', counter_label: 'Hospitality',
        title: 'Bar Tonda',
        description: 'A neighbourhood café in tactile materials — terracotta, oak and brass that will only look better with use.',
        footer_text: 'Hospitality · Turin · 2024', footer_dot_color: CLAY,
        arrow_color: CLAY_D,
      },
    ],
  }) ]) ]),
]));

// 3) PHILOSOPHY SPLIT — espresso bg, media astratto a sinistra + testo/CTA a destra
// Blueprint: 2-col grid (1fr 1fr), media dark sx, eyebrow+h2+p1+p2+btn dx
// Approccio: hero-split con showcase astratto sx come pannello media espresso-2
home.push(sec(ESPRESSO, 'large', [ row([ col('1-1', [ tile('hero-split', {
  eyebrow_text: 'The studio',
  eyebrow_dot_color: CLAY, eyebrow_color: CLAY,
  headline_lines: [
    {text: 'We design for ', color: WHITE, italic: false},
    {text: 'how you live', color: CLAY, italic: true},
  ],
  headline_font_family: 'serif', headline_font_size: 46, headline_line_height: 1.12,
  headline_font_weight: '600', headline_align: 'left',
  subhead: 'A home shouldn’t perform for guests — it should hold up at 7am on a Tuesday. We start with how you actually use a space, then layer in the materials, light and pieces that make it yours.\n\nSmall studio, few projects at a time, every detail ours. That’s the deal.',
  subhead_color: 'rgba(255,255,255,.72)', subhead_size: 16.5, subhead_italic: false, subhead_max_width: 500,
  cta1_text: 'Work with us', cta1_url: '#contact',
  cta1_bg: CLAY, cta1_color: WHITE, cta1_border: CLAY, cta1_size: 13, cta1_radius: R(2), cta1_radius_hover: R(2),
  stats: [],
  showcase_enabled: true, showcase_bg: {type: 'solid', color: ESPRESSO2}, showcase_padding: 28,
  showcase_radius: R(4), showcase_radius_hover: R(4),
  showcase_badge_text: 'STUDIO & MATERIALS', showcase_badge_dot: CLAY,
  showcase_badge_bg: ESPRESSO, showcase_badge_color: OAT,
  showcase_items: [
    {number: 'Approach', text: 'How you live', italic: true, text_color: OAT, bg: {type: 'solid', color: ESPRESSO2}},
    {number: 'Projects', text: 'Few, ours fully', italic: false, text_color: OAT, bg: {type: 'solid', color: ESPRESSO2}},
    {number: 'Detail', text: 'Every last one', italic: false, text_color: CLAY, bg: {type: 'solid', color: ESPRESSO2}},
  ],
  showcase_card_radius: R(3), showcase_card_radius_hover: R(3), showcase_card_shadow: 'none',
  showcase_caption_left: 'THE STUDIO', showcase_caption_right: 'MILAN', showcase_hover_effect: 'none',
  split_ratio: '1fr 1fr', gap: 54, min_height: 0, tile_padding: {top: 0, right: 0, bottom: 0, left: 0},
}) ]) ]) ]));

// 4) SERVICES — process-steps numerati a 1 colonna con bordi riga
// Blueprint: .svc-list border-top + .svc-i border-bottom (numero | titolo italic | desc destra)
// process-steps columns:1, card_border:LINE per separatori orizzontali
home.push(sec(OAT, 'large', [
  row([ col('1-1', [ tile('section-header', {
    eyebrow_show: true, eyebrow_text: 'What we do', eyebrow_color: CLAY_D, eyebrow_dot_color: CLAY_D, eyebrow_separator: '',
    headline_lines: [ {text: 'Services', color: TXT, italic: false} ],
    headline_font_family: 'serif', headline_font_size: 44, headline_font_weight: '600', headline_align: 'left',
    tagline_show: false, layout: 'stack', gap: 10,
  }) ]) ]),
  row([ col('1-1', [ tile('process-steps', {
    columns: 1, gap: 0, align: 'left', auto_number: false, item_gap: 10,
    number_style: 'plain', number_color: CLAY_D, number_size: 16, number_font: 'serif', number_weight: '600',
    title_color: TXT, title_size: 30, title_font: 'serif', title_weight: '600',
    desc_color: TXT_SOFT, desc_size: 14.5,
    card_bg: '', card_border: LINE, card_radius: R(0), card_padding: 28,
    items: [
      {number: '01', title: 'Full interior design', description: 'Concept to install — layout, materials, FF&E and styling.'},
      {number: '02', title: 'Renovation', description: 'Structural changes, joinery and trades, project-managed.'},
      {number: '03', title: 'Styling', description: 'The finishing layer — for homes, shoots and sales.'},
      {number: '04', title: 'Consultation', description: 'A focused session for the room you can’t quite crack.'},
    ],
  }) ]) ]),
]));

// 5) STATS — counter x4, sfondo PAPER con bordo sezione (border-block 1px solid LINE)
const stat = (prefix, number, suffix, label) => col('1-4', [ tile('counter', {
  number, suffix, prefix, label, icon_emoji: '',
  text_color: ESPRESSO, number_color: CLAY_D, number_font_size: '56', number_font_weight: '600',
  label_color: TXT_FAINT, label_font_size: '13',
  bg_type: 'color', bg_color: 'transparent', padding: '8', border_radius: '0',
}) ]);
home.push(sec(PAPER, 'small', [ row([
  stat('', '120', '+', 'Homes designed'),
  stat('', '14', '', 'Years in practice'),
  stat('', '8', '', 'Design awards'),
  stat('', '100', '%', 'Referral & repeat'),
], {gap: 24}) ]));

// 6) LOOKBOOK — progallery astratta (layout grid, no images — solo struttura)
// IMAGE-FREE: progallery senza immagini reali mostra i placeholder nel builder
home.push(sec(OAT, 'large', [
  row([ col('1-1', [ shead('Details', 'A closer ', 'look', '') ]) ]),
  row([ col('1-1', [ tile('progallery', {
    images: [],
    layout: 'grid',
    layout_family: 'classic',
    columns: '4',
    gap: '12',
    img_height: '200px',
    object_fit: 'cover',
    thumb_radius: '4',
    mobile_columns: '2',
    lightbox: true,
    show_caption: false,
  }) ]) ]),
]));

// 7) TESTIMONIAL — clay bg, citazione centrata (font serif italic)
home.push(sec(CLAY, 'large', [ row([ col('1-1', [ tile('testimonial', {
  quote: `“They listened more than they talked, and the result is a flat that finally feels like ours. Two years on, we still notice new details we love.”`,
  author_name: 'Chiara & Luca', author_role: 'Casa Navigli', rating: '0',
  layout: 'single', show_line: false, bg_color: 'transparent', text_color: WHITE, border_radius: '0', avatar: '',
}) ]) ]) ]));

// 8) PROJECT FINDER — tile 'finder' nativo (chip → result card)
// zone_accent = CLAY (#b06a4f), zone_on = WHITE (#fff) — dal CSS --fx-zone-accent/--fx-zone-on
// card_bg = CLAY_TINT (rgba 176,106,79,.1), card_border = LINE (#ddd0bb) — dal CSS --fx-zone-bg/--fx-zone-line
home.push(sec(OAT, 'large', [
  row([ col('1-1', [ tile('finder', {
    eyebrow: 'How can we help?',
    heading: 'Find your project',
    intro: '',
    zone_accent: CLAY,
    zone_on: WHITE,
    card_bg: CLAY_TINT,
    card_border: LINE,
    align: 'center',
    items: [
      {
        option: 'A whole home',
        title: 'Full Home Design',
        text: 'Concept to keys — layout, finishes, furniture and the day everything arrives and lands in its place. We hold the whole thing.',
        meta: 'from €12k · 12–20 weeks',
        cta_text: '',
        cta_url: '#',
        icon: '',
      },
      {
        option: 'A single room',
        title: 'Room by Room',
        text: 'One space, fully resolved — a living room that finally works, a bedroom that feels like rest. Sourced and styled.',
        meta: 'from €3.5k · 5–7 weeks',
        cta_text: '',
        cta_url: '#',
        icon: '',
      },
      {
        option: 'Just styling',
        title: 'Styling Day',
        text: 'You’ve got the bones; we bring the soul — art, textiles, lighting and the small things that make it feel finished.',
        meta: 'from €900 · one day',
        cta_text: '',
        cta_url: '#',
        icon: '',
      },
      {
        option: 'A quick consult',
        title: 'Power-Hour Consult',
        text: 'Two hours in your home with answers you can act on the same week — paint, layout, what to keep and what to lose.',
        meta: '€220 · 2 hours',
        cta_text: '',
        cta_url: '#',
        icon: '',
      },
    ],
  }) ]) ]),
]));

// 9) CTA FINALE — sfondo espresso, headline + accent OAT (italic), 1 pulsante clay
// Blueprint: .he-cta h2 .it { font-style:italic; color:var(--oat) } => accent = OAT
// Blueprint: 1 solo bottone "Start a project" (niente cta2)
home.push(sec(ESPRESSO, 'large', [ row([ col('1-1', [ tile('cta-banner', {
  headline: 'Tell us about', headline_accent: 'your space', headline_accent_italic: true,
  subtitle: 'We take on a handful of projects each season. Share a little about yours and we’ll be in touch.',
  cta_text: 'Start a project', cta_url: '#contact',
  bg: {type: 'solid', color: ESPRESSO2}, text_color: WHITE, accent_color: OAT, subtitle_color: 'rgba(255,255,255,.8)',
  cta_bg: CLAY, cta_color: WHITE, cta_radius: R(2), cta_size: 13,
  headline_font_family: 'serif', headline_size: 52, headline_weight: '600', subtitle_size: 17,
  layout: 'stack', vertical_align: 'center', banner_radius: R(4), banner_padding: 80,
  eyebrow_show: true, eyebrow_text: 'Let’s begin', eyebrow_color: CLAY,
}) ]) ]) ]));

K.emit({
  slug: 'hearth', name: 'Hearth',
  tags: ['interior', 'design', 'home', 'living', 'architecture', 'studio'],
  description: 'Hearth — interior design studio. Warm espresso/clay/oat palette, Lora (display) + Work Sans. Tile: hero-split, info-cards, process-steps, counter, progallery, testimonial, cta-banner. IMAGE-FREE.',
  colors: {
    primary: CLAY, primary_contrast: WHITE,
    secondary: ESPRESSO, secondary_contrast: OAT,
    muted: OAT, muted_contrast: TXT,
    text: TXT, text_muted: TXT_SOFT,
    background: OAT, border: LINE, link: CLAY_D,
  },
  css_disp: `"Lora", Georgia, serif`,
  css_sans: `"Work Sans", -apple-system, sans-serif`,
  heading_weight: '600', heading_line_height: '1.12',
  google_fonts: ['Lora', 'Work Sans'],
  logo_variant: 'dark',
  menu: [
    {title: 'Projects', url: '#projects'},
    {title: 'Studio', url: '#studio'},
    {title: 'Services', url: '#services'},
    {title: 'Contact', url: '#contact'},
  ],
  header: {bg: PAPER, text_color: TXT_SOFT, sticky_bg: 'rgba(250,245,236,.92)', logo_width: 138},
  footer: {
    bg: ESPRESSO, headColor: CLAY,
    brand: {name: 'Hearth', tagline: 'An interior design studio making warm, lived-in homes across Italy.'},
    columns: [
      {title: 'Studio', links: ['Projects', 'About', 'Services', 'Press']},
      {title: 'Connect', links: ['Instagram', 'Pinterest', 'Contact']},
      {title: 'Studio', links: ['Via Solferino 7, Milan', 'studio@hearth.design']},
    ],
    bottom: {left: '© 2026 Hearth Studio — an OLOtheme demo.', right: 'Built with OLObuild'},
  },
  cursor: false,
}, home);
