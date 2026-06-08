/* Fjordline — ricomposizione TILE-PURE (image-free).
   Nordic deep-teal. Schibsted Grotesk (disp+body) + Instrument Serif (accento). */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('fj');

/* ─── PALETTE ─── */
const BG    = '#16273a';
const BG2   = '#1a2e44';
const PANEL = '#1f3550';
const PANEL2= '#264062';
const INK   = '#0e1c2c';
const ICE   = '#8fc7e0';
const ICED  = '#6bb1d4';
const SAND  = '#e8d9bf';
const SNOW  = '#eef4f8';
const TXT   = '#aec3d6';
const DIM   = '#6f87a0';
const LINE  = 'rgba(238,244,248,.12)';
const LINE2 = 'rgba(143,199,224,.4)';
const WHITE = '#ffffff';

const home = [];

/* ─── HELPER section-header centrato ─── */
const shead = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: ICE, eyebrow_dot_color: ICE, eyebrow_separator: '',
  headline_lines: [
    { text: l1,     color: SNOW, italic: false },
    { text: accent, color: ICE,  italic: true  },
  ],
  headline_font_family: 'sans-serif', headline_font_size: 46, headline_font_weight: '700',
  headline_align: 'center', headline_inline: true,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false,
  tagline_text_color: DIM, tagline_text_size: 16.5,
  layout: 'center', gap: 16,
});

/* ─── HELPER section-header sinistra ─── */
const sheadLeft = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: ICE, eyebrow_dot_color: ICE, eyebrow_separator: '',
  headline_lines: [
    { text: l1,     color: SNOW, italic: false },
    { text: accent, color: ICE,  italic: true  },
  ],
  headline_font_family: 'sans-serif', headline_font_size: 46, headline_font_weight: '700',
  headline_align: 'left', headline_inline: true,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false,
  tagline_text_color: DIM, tagline_text_size: 16,
  layout: 'stack', gap: 12,
});

/* ─── HELPER caption piccola (label cloud) ─── */
const caption = (txt) => tile('section-header', {
  eyebrow_show: false,
  headline_lines: [{ text: txt, color: DIM, italic: false }],
  headline_font_family: 'sans-serif', headline_font_size: 12, headline_font_weight: '600',
  headline_align: 'center',
  tagline_show: false, layout: 'center', gap: 0,
});

/* ═══════════════════════════════════════════════
   1) HERO — hero-split (image-free: pannello showcase astratto)
   Il CSS ha un hero full-bleed con gradient overlay. Usiamo
   hero-split con showcase astratto che simula i dati-chiave.
═══════════════════════════════════════════════ */
home.push(sec(BG, 'large', [row([col('1-1', [tile('hero-split', {
  eyebrow_text: 'Small-group expeditions · max 12',
  eyebrow_dot_color: ICE, eyebrow_color: ICE,
  headline_lines: [
    { text: 'Go where the',    color: WHITE, italic: false },
    { text: 'map turns',       color: WHITE, italic: false },
    { text: 'quiet.',          color: ICE,   italic: true  },
  ],
  headline_font_family: 'sans-serif', headline_font_size: 76,
  headline_line_height: 1.0, headline_font_weight: '700', headline_align: 'left',
  subhead: `Fjords, ice fields and aurora — on foot, by kayak and under canvas, led by guides who grew up here.`,
  subhead_color: SNOW, subhead_size: 18, subhead_italic: false, subhead_max_width: 440,
  cta1_text: 'Find a trip', cta1_url: '#trips',
  cta1_bg: ICE, cta1_color: INK, cta1_size: 14, cta1_radius: R(999), cta1_radius_hover: R(999),
  cta2_text: 'Our approach', cta2_url: '#why',
  cta2_bg: 'rgba(238,244,248,.06)', cta2_color: SNOW,
  cta2_border: LINE2, cta2_size: 14, cta2_radius: R(999), cta2_radius_hover: R(999),
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: PANEL },
  showcase_padding: 28, showcase_radius: R(18), showcase_radius_hover: R(18),
  showcase_badge_text: 'FJORDLINE · AT A GLANCE',
  showcase_badge_dot: ICE, showcase_badge_bg: INK, showcase_badge_color: SNOW,
  showcase_items: [
    { number: 'Destinations', text: 'Norway · Iceland · Greenland · Svalbard', italic: true, text_color: ICE,  bg: { type: 'solid', color: BG2 } },
    { number: 'Max group size', text: '12',       italic: false, text_color: SNOW, bg: { type: 'solid', color: BG2 } },
    { number: 'Expeditions',    text: '42',       italic: false, text_color: SNOW, bg: { type: 'solid', color: BG2 } },
    { number: 'Years guiding',  text: '18',       italic: false, text_color: SNOW, bg: { type: 'solid', color: BG2 } },
  ],
  showcase_card_radius: R(12), showcase_card_radius_hover: R(12), showcase_card_shadow: 'none',
  showcase_caption_left: 'FJORDLINE', showcase_caption_right: '2026 SEASON',
  showcase_hover_effect: 'none',
  split_ratio: '1.25fr .75fr', gap: 52, min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
})])])]));

/* ═══════════════════════════════════════════════
   2) TRIP FINDER — approssimato con info-cards statiche
   ⚠️ SEGNALATA: tile TripFinder non esiste. Blueprint ha form overlapping 3 dropdown
   (Destination / When / Activity) + submit btn. Qui riprodotto come barra di filtri
   sotto l'hero tramite 3 info-cards con opzioni principali + bottone search.
   CSS: .fj-finder — bg panel, border line-2, radius 16px, grid 1.4fr 1fr 1fr auto.
═══════════════════════════════════════════════ */
home.push(sec(PANEL, 'small', [
  row([col('1-1', [tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0, container_gap: 10, columns: 4, items_gap: 10,
    card_bg: { type: 'solid', color: BG2 }, card_color: DIM,
    card_radius: R(10), card_padding: 16,
    show_icon: true, show_counter: false, show_arrow: false, show_footer: false, show_media: false,
    icon_color: ICE, icon_bg_color: 'rgba(143,199,224,.13)',
    title_color: DIM, title_font_family: 'sans-serif', title_size: 10, title_weight: '600',
    description_size: 15, description_color: SNOW, description_weight: '500',
    items: [
      { icon: 'map-pin',   title: 'DESTINATION',  description: 'Anywhere north' },
      { icon: 'calendar',  title: 'WHEN',         description: 'Any month' },
      { icon: 'activity',  title: 'ACTIVITY',     description: 'Any' },
      { icon: 'search',    title: '',             description: 'Search expeditions' },
    ],
    card_hover_effect: 'lift',
  })])]),
]));

/* ═══════════════════════════════════════════════
   3) STAT STRIP — counter ×4
   CSS: fj-stats b = color snow (numero), span = color txt-dim (label)
   Layout: 4 colonne, border-block, bg-2
═══════════════════════════════════════════════ */
const stat = (number, suffix, label) => col('1-4', [tile('counter', {
  number, suffix, prefix: '', label,
  icon_emoji: '',
  text_color: SNOW,
  number_font_size: '54', number_font_weight: '700',
  label_color: DIM, label_font_size: '13',
  bg_type: 'color', bg_color: 'transparent',
  tile_padding: { top: 36, right: 16, bottom: 36, left: 16 },
  border_radius: '0',
})]);

home.push(sec(BG2, 'small', [row([
  stat('42', '', 'Expeditions'),
  stat('12', '', 'Max group size'),
  stat('6',  '', 'Nordic countries'),
  stat('18', '', 'Years guiding'),
], { gap: 24 })]));

/* ═══════════════════════════════════════════════
   4) FEATURED EXPEDITIONS (DestinationCard → info-cards)
   Blueprint: 3 card (fj-dest) con location uppercase, titolo, tag duration/difficulty,
   attività e prezzo. Griglia 3 col, card radius 16px, bg PANEL, border LINE.
   Sotto le card: link "Browse all 42 expeditions" centrato.
═══════════════════════════════════════════════ */
home.push(sec(BG, 'large', [
  row([col('1-1', [sheadLeft(
    'Featured journeys',
    `Trips for people who'd rather `,
    'walk than watch',
    '',
  )])]),
  row([col('1-1', [tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0, container_gap: 20, columns: 3, items_gap: 20,
    card_bg: { type: 'solid', color: PANEL }, card_color: TXT,
    card_radius: R(16), card_padding: 24,
    show_icon: false, show_counter: true, show_counter_label: true,
    show_arrow: false, show_footer: true, show_media: false,
    counter_shape: 'pill', counter_color: SNOW,
    counter_bg: 'rgba(14,28,44,.55)',
    title_color: SNOW, title_font_family: 'sans-serif',
    title_size: 22, title_weight: '700', title_italic: false,
    description_size: 13, counter_size: 11, footer_size: 18,
    items: [
      {
        counter: '8 days', counter_label: 'Moderate',
        title: 'The Lofoten Traverse',
        description: 'Norway · Lofoten — Hiking · kayak',
        footer_text: `€2,150/pp`,  footer_dot_color: ICE,
      },
      {
        counter: '6 days', counter_label: 'Easy',
        title: `Fire & Ice Crossing`,
        description: 'Iceland · Highlands — Trek · hot springs',
        footer_text: `€1,890/pp`,  footer_dot_color: ICE,
      },
      {
        counter: '10 days', counter_label: 'Challenging',
        title: 'Scoresby Sound by Sea',
        description: 'East Greenland — Expedition cruise',
        footer_text: `€4,300/pp`,  footer_dot_color: ICE,
      },
    ],
    card_hover_effect: 'lift',
  })])]),
  row([col('1-1', [tile('cta-banner', {
    headline: '', headline_accent: '', headline_accent_italic: false,
    subtitle: '',
    cta_text: 'Browse all 42 expeditions',
    cta_url: '#',
    bg: { type: 'solid', color: 'transparent' },
    text_color: SNOW, accent_color: ICE, subtitle_color: TXT,
    cta_bg: 'rgba(238,244,248,.06)', cta_color: SNOW,
    cta_radius: R(999), cta_size: 14, cta_padding_y: 14, cta_padding_x: 26,
    headline_size: 0, subtitle_size: 0,
    layout: 'stack', vertical_align: 'center',
    banner_radius: R(0), banner_padding: 0,
  })])]),
]));

/* ═══════════════════════════════════════════════
   5) WHY FJORDLINE (FeatureSplit → 2-col: sinistra testo+checklist, destra pannello astratto)
   CSS: .fj-feat — grid 1fr 1fr, gap 56px; .fj-feat__list con icone check
   tondo 30px bg rgba(143,199,224,.13). Blueprint: eyebrow + h2 + p + 3 item + btn "Our approach".
   .fj-sec.panel → BG2. Sfondo media: PANEL, border-radius 16px, aspect 5/4.
═══════════════════════════════════════════════ */
home.push(sec(BG2, 'large', [
  row([
    /* — Colonna sinistra: titolo + intro + checklist + bottone — */
    col('1-2', [
      sheadLeft(
        'Why Fjordline',
        `Small groups, `,
        'local guides, no crowds',
        `We cap every departure at twelve and never run the same trail as the tour buses. Our guides are born-and-raised locals who read the weather and know where the light falls.`,
      ),
      tile('list', {
        items: [
          { text: 'Maximum 12 travellers per departure', icon: 'check' },
          { text: 'Carbon-offset travel, low-impact camps', icon: 'check' },
          { text: 'Flexible weather days built into every trip', icon: 'check' },
        ],
        icon_default: 'check',
        icon_color: ICE,
        icon_size: '16',
        icon_gap: '13',
        text_color: TXT,
        text_align: 'left',
        spacing: '14',
        tile_padding: { top: 16, right: 0, bottom: 24, left: 0 },
        bg: { type: 'none' },
        preset: 'custom',
      }),
      tile('cta-banner', {
        headline: '', headline_accent: '', headline_accent_italic: false,
        subtitle: '',
        cta_text: 'Our approach',
        cta_url: '#',
        bg: { type: 'solid', color: 'transparent' },
        text_color: SNOW, accent_color: ICE, subtitle_color: TXT,
        cta_bg: 'rgba(238,244,248,.06)', cta_color: SNOW,
        cta_radius: R(999), cta_size: 14, cta_padding_y: 14, cta_padding_x: 26,
        headline_size: 0, subtitle_size: 0,
        layout: 'stack', vertical_align: 'center',
        banner_radius: R(0), banner_padding: 0,
      }),
    ]),
    /* — Colonna destra: pannello astratto (media image-free) — */
    col('1-2', [
      tile('info-cards', {
        container_bg: { type: 'solid', color: 'transparent' },
        container_padding: 0, container_gap: 0, columns: 1, items_gap: 0,
        card_bg: { type: 'solid', color: PANEL }, card_color: TXT,
        card_radius: R(16), card_padding: 0,
        show_icon: false, show_counter: false, show_arrow: false,
        show_footer: false, show_media: false,
        title_color: DIM, title_font_family: 'sans-serif', title_size: 12,
        items: [
          { title: 'FJORDLINE · LOCAL GUIDES', description: '' },
        ],
        card_hover_effect: 'none',
        card_min_height: 340,
      }),
    ]),
  ], { gap: 56 }),
]));

/* ═══════════════════════════════════════════════
   6) BUDGET PROJECTOR — tile projector (rate=0 → lineare)
   Slider notti (3–14), per-unit cost ≈ €295/notte
═══════════════════════════════════════════════ */
home.push(sec(BG, 'large', [row([col('1-1', [tile('projector', {
  eyebrow: 'Plan your budget',
  heading: `What should I <em>budget?</em>`,
  intro: `Drag to the length of trip you have in mind. We'll show a rough all-in figure so there are no surprises before you ever email us.`,
  min: '3', max: '14', step: '1', value: '8',
  rate: '0', years: '295', currency: '€',
  input_label: 'Nights away',
  out_caption: 'Indicative all-in · per person',
  note: `Guiding, lodging, meals and local transfers included — flights aren't. Your real quote depends on season, route and group size.`,
  show_contrib: true, zone_accent: ICE,
  align: 'left',
  tile_padding: { top: 44, right: 44, bottom: 44, left: 44 },
  border_radius: '18', shadow: 'sm',
})])])]));

/* ═══════════════════════════════════════════════
   7) TESTIMONIAL
   CSS: .fj-testi → fj-sec (body bg = var(--bg) = BG, nessun panel).
   Quote centrata, font disp bold, color snow; author dim.
   Testo: identico carattere per carattere dal blueprint (apostrofo curvo conservato).
═══════════════════════════════════════════════ */
home.push(sec(BG, 'large', [row([col('1-1', [tile('testimonial', {
  quote: `”Twelve strangers on day one, friends by day eight. The guiding was flawless — and we saw the aurora three nights running.”`,
  author_name: `Priya & Tom`,
  author_role: 'The Lofoten Traverse, March',
  rating: '0',
  layout: 'single', show_line: false,
  bg_color: 'transparent', text_color: SNOW,
  quote_font_family: 'sans-serif', quote_size: 32, quote_weight: '600',
  border_radius: '0', avatar: '',
})])])]));

/* ═══════════════════════════════════════════════
   8) EXPEDITION FINDER ("Which kind of north?")
   Tile Finder non esiste → approssimato con info-cards (4 opzioni)
═══════════════════════════════════════════════ */
home.push(sec(BG, 'large', [
  row([col('1-1', [shead(
    'Plan your north',
    'Which kind of ',
    'north?',
    '',
  )])]),
  row([col('1-1', [tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0, container_gap: 20, columns: 4, items_gap: 20,
    card_bg: { type: 'solid', color: PANEL }, card_color: TXT,
    card_radius: R(14), card_padding: 28,
    show_icon: true, show_counter: false, show_arrow: true,
    show_footer: true, show_media: false,
    icon_color: ICE, icon_bg_color: 'rgba(143,199,224,.13)',
    title_color: SNOW, title_font_family: 'sans-serif',
    title_size: 18, title_weight: '700', title_italic: false,
    description_size: 13.5, footer_size: 13,
    items: [
      {
        icon: 'moon',
        title: 'The Silent Fjords',
        description: `A cabin at the water's edge, a wood-fired sauna and a rowboat. Days measured in tides, not hours.`,
        footer_text: '7 nights · from €1,980', footer_dot_color: ICE,
      },
      {
        icon: 'mountain',
        title: 'The High Traverse',
        description: 'Guided ridge hikes, a glacier crossing and kayaking the inlets. Earn every view, sleep like the dead.',
        footer_text: '9 nights · from €2,640', footer_dot_color: ICE,
      },
      {
        icon: 'sparkles',
        title: 'Chasing the Aurora',
        description: `North of the circle in winter — dog-sled by day, aurora-watch by night from a glass-roofed lodge.`,
        footer_text: '6 nights · from €2,310', footer_dot_color: ICE,
      },
      {
        icon: 'compass',
        title: 'The Coastal Hop',
        description: 'Gentle island-hopping by ferry, fishing-village harbours and short walks everyone can manage — even the small ones.',
        footer_text: '8 nights · from €1,740', footer_dot_color: ICE,
      },
    ],
    card_hover_effect: 'lift',
  })])]),
]));

/* ═══════════════════════════════════════════════
   9) CTA finale — 2 bottoni
   Blueprint: btn--ice "Find your expedition" + btn--snow "Request the brochure"
   CSS .fj-cta__box: border-radius 24px, padding clamp(48,7vw,88)px,
   overlay gradient rgba(14,28,44,.7→.85), layout stack centrato.
═══════════════════════════════════════════════ */
home.push(sec(BG, 'large', [row([col('1-1', [tile('cta-banner', {
  headline: 'The north is',
  headline_accent: 'waiting',
  headline_accent_italic: true,
  subtitle: `Departures fill months ahead — secure a place with a deposit, balance due 60 days out.`,
  cta_text: 'Find your expedition', cta_url: '#trips',
  cta2_text: 'Request the brochure', cta2_url: '#',
  cta2_bg: SNOW, cta2_color: INK, cta2_border: '',
  bg: { type: 'solid', color: '#122133' },
  text_color: WHITE, accent_color: ICE, subtitle_color: SNOW,
  cta_bg: ICE, cta_color: INK, cta_radius: R(999), cta_size: 15, cta_padding_y: 16, cta_padding_x: 30,
  headline_font_family: 'sans-serif', headline_size: 56,
  headline_weight: '700', subtitle_size: 17,
  layout: 'stack', vertical_align: 'center',
  banner_radius: R(24), banner_padding: 88,
})])])]));

/* ═══════════════════════════════════════════════
   EMIT
═══════════════════════════════════════════════ */
K.emit({
  slug: 'fjordline',
  name: 'Fjordline',
  tags: ['travel', 'adventure', 'expeditions', 'nordic'],
  description: `Fjordline — small-group adventure expeditions to the Nordic north. Deep-teal + ice blue, Schibsted Grotesk bold (display+body) + Instrument Serif (accento corsivo). Zona interattiva Budget Projector (notti × €295). Trip Finder e Expedition Finder approssimati con info-cards statiche (tile TripFinder/Finder non esistono). Riproduzione fedele dell'OLOtheme Fjordline.`,
  colors: {
    primary: ICE,
    primary_contrast: INK,
    secondary: SAND,
    secondary_contrast: INK,
    muted: BG2,
    muted_contrast: TXT,
    text: TXT,
    text_muted: DIM,
    background: BG,
    border: LINE,
    link: ICE,
  },
  css_disp:  `"Schibsted Grotesk", -apple-system, sans-serif`,
  css_sans:  `"Schibsted Grotesk", -apple-system, sans-serif`,
  heading_weight: '700',
  heading_line_height: '1.05',
  google_fonts: ['Schibsted+Grotesk:wght@400;500;600;700;800', 'Instrument+Serif:ital@0;1'],
  logo_variant: 'light',
  menu: [
    { title: 'Expeditions', url: '#trips' },
    { title: 'Itineraries', url: '#itineraries' },
    { title: 'Why Fjordline', url: '#why' },
    { title: 'Journal', url: '#journal' },
  ],
  header: {
    bg: 'rgba(22,39,58,.84)',
    text_color: DIM,
    sticky_bg: 'rgba(22,39,58,.92)',
    logo_width: 140,
  },
  footer: {
    bg: BG2,
    headColor: SNOW,
    brand: {
      name: 'Fjordline',
      tagline: 'Small-group adventure expeditions across the Nordic north, led by local guides.',
    },
    columns: [
      { title: 'Travel',   links: ['Expeditions', 'Itineraries', 'Private trips', 'Gift vouchers'] },
      { title: 'Company',  links: ['Why Fjordline', 'Our guides', 'Sustainability', 'Journal'] },
      { title: 'Plan',     links: ['How to book', 'Kit lists', 'Travel insurance', 'Contact'] },
    ],
    bottom: {
      left:  `© 2026 Fjordline — an OLOtheme demo.`,
      right: 'Built with OLObuild',
    },
  },
  cursor: {
    blend_mode: 'normal',
    ring_color: ICE,
    dot_color:  ICE,
  },
}, home);
