/* Wander — ricomposizione TILE-PURE (image-free). Travel agency teal + sand + coral. */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('wa');

const TEAL   = '#0d3b3f';
const TEAL2  = '#114a4f';
const TEAL3  = '#16615f';
const SAND   = '#e9c46a';
const SANDD  = '#d9ab47';
const CORAL  = '#e76f51';
const CREAM  = '#f5f1e8';
const CREAM2 = '#ece5d6';
const PAPER  = '#ffffff';
const TXT    = '#1d2a29';
const TXTSOFT= '#566260';
const TXTFNT = '#8a948f';
const LINE   = '#e3ddcf';
const LINEDK = 'rgba(255,255,255,.14)';
const WHITE  = '#ffffff';

const home = [];

// ─── helper: section-header centrato (eyebrow + titolo + intro) ───
const shead = (eyebrow, title, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: CORAL, eyebrow_dot_color: CORAL, eyebrow_separator: '',
  headline_lines: [
    { text: title, color: TXT, italic: false },
  ],
  headline_font_family: 'serif', headline_font_size: 46, headline_font_weight: '600', headline_align: 'center', headline_inline: false,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false, tagline_text_color: TXTSOFT, tagline_text_size: 16.5,
  layout: 'center', gap: 16,
});

// ─── helper: section-header sinistra ───
const sheadLeft = (eyebrow, title, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: CORAL, eyebrow_dot_color: CORAL, eyebrow_separator: '',
  headline_lines: [{ text: title, color: TXT, italic: false }],
  headline_font_family: 'serif', headline_font_size: 44, headline_font_weight: '600', headline_align: 'left', headline_inline: false,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false, tagline_text_color: TXTSOFT, tagline_text_size: 16.5,
  layout: 'stack', gap: 12,
});

// ═══════════════════════════════════════════════════════════════════
// 1) HERO  (hero-split)
// TripFinder — NEW tile candidato. Approssimato con showcase_items
// (3 campi: Where / When / Trip type). BEST-EFFORT — SEGNALATO.
// ═══════════════════════════════════════════════════════════════════
home.push(sec(TEAL, 'large', [row([col('1-1', [tile('hero-split', {
  eyebrow_text: 'Small groups · big places · since 2009',
  eyebrow_dot_color: SAND, eyebrow_color: SAND,
  headline_lines: [
    { text: 'The world is wide.', color: WHITE, italic: false },
    { text: 'Go further.', color: SAND, italic: false },
  ],
  headline_font_family: 'serif', headline_font_size: 72, headline_line_height: 1.0, headline_font_weight: '600', headline_align: 'left',
  subhead: `Expert-led expeditions to the places worth the effort — twelve travellers, zero crowds, a footprint we keep light.`,
  subhead_color: 'rgba(255,255,255,.82)', subhead_size: 18, subhead_italic: false, subhead_max_width: 480,
  cta1_text: 'Plan my trip', cta1_url: '#cta', cta1_bg: SAND, cta1_color: TEAL, cta1_size: 15, cta1_radius: R(999), cta1_radius_hover: R(999),
  cta2_text: 'Browse trips', cta2_url: '#destinations', cta2_bg: 'rgba(255,255,255,.08)', cta2_color: WHITE, cta2_border: 'rgba(255,255,255,.34)', cta2_size: 15, cta2_radius: R(999), cta2_radius_hover: R(999),
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: PAPER },
  showcase_padding: 14,
  showcase_radius: R(16), showcase_radius_hover: R(16),
  showcase_badge_text: 'FIND YOUR TRIP', showcase_badge_dot: CORAL, showcase_badge_bg: TEAL, showcase_badge_color: WHITE,
  // 3 campi TripFinder: Where / When / Trip type + pulsante Find a trip (best-effort: 3 items)
  showcase_items: [
    { number: 'Where',     text: 'Anywhere wild', italic: false, text_color: TEAL,  bg: { type: 'solid', color: PAPER } },
    { number: 'When',      text: 'Flexible',      italic: false, text_color: TEAL,  bg: { type: 'solid', color: PAPER } },
    { number: 'Trip type', text: 'All',           italic: false, text_color: TEAL,  bg: { type: 'solid', color: PAPER } },
  ],
  showcase_card_radius: R(0), showcase_card_radius_hover: R(0), showcase_card_shadow: 'none',
  showcase_caption_left: 'WANDER EXPEDITIONS', showcase_caption_right: 'SINCE 2009',
  showcase_hover_effect: 'none',
  split_ratio: '1.2fr .8fr', gap: 52, min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
})])])]));

// ═══════════════════════════════════════════════════════════════════
// 2) FEATURED DESTINATIONS  (info-cards — 3 carte destinazione)
//    DestinationCard (full-bleed image overlay) → NEW tile candidato.
//    Approssimato con info-cards card su PAPER + footer prezzo/durata.
//    Copy corretto dal blueprint.
// ═══════════════════════════════════════════════════════════════════
home.push(sec(CREAM, 'large', [
  row([col('1-1', [sheadLeft('Where to next', 'Featured expeditions', '')])]),
  row([col('1-1', [tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' }, container_padding: 0, container_gap: 20, columns: 3, items_gap: 20,
    card_bg: { type: 'solid', color: TEAL2 }, card_color: 'rgba(255,255,255,.85)', card_radius: R(18), card_padding: 24,
    show_icon: true, show_counter: false, show_arrow: false, show_footer: true, show_media: false,
    icon_color: SAND, icon_bg_color: 'rgba(255,255,255,.08)',
    title_color: WHITE, title_font_family: 'serif', title_size: 26, title_weight: '600', title_italic: false, description_size: 13,
    footer_size: 13,
    items: [
      {
        icon: 'mountain-snow',
        title: 'Patagonia',
        description: `Eleven days across the wild Andes — torres, glaciers, and the silence between them.`,
        footer_text: '11 days · Chile & Argentina',
        footer_dot_color: SAND,
        footer_extra: '€3,200',
      },
      {
        icon: 'zap',
        title: 'Iceland Highlands',
        description: `Eight days on the F-roads — lava fields, geysers, and aurora-lit nights. Self-drive with a local guide.`,
        footer_text: '8 days · Self-drive + guide',
        footer_dot_color: SAND,
        footer_extra: '€2,450',
      },
      {
        icon: 'sun',
        title: 'Namib Desert',
        description: `Nine days in one of Earth\'s oldest deserts — towering dunes, starfields, and wildlife at dawn.`,
        footer_text: '9 days · Namibia',
        footer_dot_color: SAND,
        footer_extra: '€3,750',
      },
    ],
    card_hover_effect: 'lift',
  })])])
]));

// ═══════════════════════════════════════════════════════════════════
// 3) WHY WANDER  (split: media placeholder sx + section-header+iconlist+CTA dx)
//    Blueprint: 2 col, aspect-ratio 4/4.4 border-radius 20px media sx
//    + eyebrow/h2/p/list/btn dx. Titolo: "Travel that's worth the distance" (1 riga).
// ═══════════════════════════════════════════════════════════════════
home.push(sec(PAPER, 'large', [
  row([
    col('1-2', [tile('image', {
      image_url: '',
      alt_text: 'Guide with small group on a ridge',
      aspect_ratio: '4/4.4',
      object_fit: 'cover',
      object_position: 'center center',
      image_width: '100%',
      height: 'auto',
      max_width: '',
      image_alignment: 'left',
      border_radius: { tl: 20, tr: 20, br: 20, bl: 20 },
      bg_type: 'color', bg_color: CREAM2,
    })]),
    col('1-2', [
      tile('section-header', {
        eyebrow_show: true, eyebrow_text: 'Why Wander', eyebrow_color: CORAL, eyebrow_dot_color: CORAL, eyebrow_separator: '',
        headline_lines: [
          { text: 'Travel that\'s worth the distance', color: TXT, italic: false },
        ],
        headline_font_family: 'serif', headline_font_size: 44, headline_font_weight: '600', headline_align: 'left', headline_inline: false,
        tagline_show: true,
        tagline_text: `We keep groups to twelve, hire guides who actually live where they lead, and route around the crowds — not into them.`,
        tagline_text_italic: false, tagline_text_color: TXTSOFT, tagline_text_size: 16.5,
        layout: 'stack', gap: 12,
      }),
      tile('iconlist', {
        items: [
          { icon: 'users',   title: 'Groups of twelve',    description: 'Small enough to move differently.' },
          { icon: 'map-pin', title: 'Local expert guides', description: 'People who know the place by heart.' },
          { icon: 'leaf',    title: 'Light footprint',     description: 'Carbon-offset, leave-no-trace, always.' },
        ],
        icon_color: TEAL3, icon_bg_color: CREAM2, icon_size: 21,
        title_color: TXT, title_font_family: 'serif', title_weight: '600',
        description_color: TXTSOFT, description_size: 13.5,
        gap: 16, divider: false,
      }),
      tile('button', {
        text: 'Plan your trip',
        url: '#cta',
        target: '_self',
        alignment: 'left',
        full_width: false,
        bg_color: TEAL, text_color: CREAM,
        font_size: 15, font_weight: '600',
        border_radius: { tl: 999, tr: 999, br: 999, bl: 999 },
        shadow: 'none',
        hover_effect: 'lift',
        tile_padding: { top: 12, right: 0, bottom: 0, left: 0 },
      }),
    ]),
  ], { gap: 54, vertical_align: 'center' }),
]));

// ═══════════════════════════════════════════════════════════════════
// 4) TRIP TYPES  (info-cards — 4 card tipologia viaggio)
//    Blueprint: background var(--paper) + border-block 1px solid var(--line)
//    Titolo una riga: "Trips for every kind of traveller"
//    Card .ttype: image-full con veil gradient — approssimato info-cards dark-teal
//    (image-free: nessun tile cover-image disponibile per griglia 4 col)
// ═══════════════════════════════════════════════════════════════════
home.push(sec(PAPER, 'large', [
  row([col('1-1', [shead('Find your pace', 'Trips for every kind of traveller', '')])]),
  row([col('1-1', [tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' }, container_padding: 0, container_gap: 16, columns: 4, items_gap: 16,
    card_bg: { type: 'solid', color: TEAL }, card_color: 'rgba(255,255,255,.75)', card_radius: R(16), card_padding: 28,
    show_icon: true, show_counter: false, show_arrow: false, show_footer: false, show_media: false,
    icon_color: SAND, icon_bg_color: 'rgba(255,255,255,.08)',
    title_color: WHITE, title_font_family: 'serif', title_size: 20, title_weight: '600', title_italic: false, description_size: 13.5,
    items: [
      { icon: 'mountain',  title: 'Trekking',         description: 'Multi-day trails through high-altitude wilderness.' },
      { icon: 'binoculars',title: 'Wildlife & safari', description: 'Dawn drives, expert trackers, no overcrowded reserves.' },
      { icon: 'anchor',    title: 'Sailing',           description: 'Island-hopping under sail with a seasoned crew.' },
      { icon: 'utensils',  title: 'Culture & food',    description: 'Markets, kitchens, and the people behind the flavours.' },
    ],
    card_hover_effect: 'lift',
  })])]),
], { border_top: `1px solid ${LINE}`, border_bottom: `1px solid ${LINE}` }));

// ═══════════════════════════════════════════════════════════════════
// 5) STATS BAND  (counter ×4 su sfondo teal)
//    blueprint: 60 / 14000+ / 12 / 4.9  — colori SAND + rgba(255,255,255,.7)
// ═══════════════════════════════════════════════════════════════════
const stat = (number, suffix, label) => col('1-4', [tile('counter', {
  number, suffix, prefix: '', label,
  icon_emoji: '',
  text_color: SAND, number_color: SAND, number_font_size: '56', number_font_weight: '700', label_color: 'rgba(255,255,255,.7)', label_font_size: '13',
  bg_type: 'color', bg_color: 'transparent', padding: '8', border_radius: '0',
})]);

home.push(sec(TEAL, 'small', [row([
  stat('60',    '',   'Countries'),
  stat('14000', '+',  'Travellers guided'),
  stat('12',    '',   'Max group size'),
  stat('4.9',   '',   'Avg. rating'),
], { gap: 24 })]));

// ═══════════════════════════════════════════════════════════════════
// 6) TESTIMONIAL
//    Sfondo CREAM. Blueprint: stelle ★★★★★, citazione, autore "Hannah & Theo · Patagonia expedition"
// ═══════════════════════════════════════════════════════════════════
home.push(sec(CREAM, 'large', [row([col('1-1', [tile('testimonial', {
  quote: `Eleven days in Patagonia and not one moment felt packaged. We went where the weather let us, and our guide made every detour the best part.`,
  author_name: 'Hannah & Theo', author_role: 'Patagonia expedition',
  rating: '5',
  layout: 'single', show_line: false,
  bg_color: 'transparent', text_color: TXT, border_radius: '0', avatar: '',
  quote_font_family: 'serif', quote_size: 36, quote_weight: '500',
  quote_color: TXT, author_name_color: TXT, author_role_color: TXTFNT,
})])])]));

// ═══════════════════════════════════════════════════════════════════
// 7) CTA FINALE
//    Blueprint: UN solo bottone sand "Plan my trip". Sfondo con immagine + gradient
//    teal → approssimato solid TEAL2 (image-free). Testo: "Let's plan it" / "Where will you wander?"
// ═══════════════════════════════════════════════════════════════════
home.push(sec(TEAL, 'large', [row([col('1-1', [tile('cta-banner', {
  headline: 'Where will you', headline_accent: 'wander?', headline_accent_italic: false,
  subtitle: `Tell us roughly where and when. We\'ll send back two routes you can actually picture yourself on — no obligation.`,
  cta_text: 'Plan my trip', cta_url: '#',
  bg: { type: 'solid', color: TEAL2 }, text_color: WHITE, accent_color: SAND, subtitle_color: 'rgba(255,255,255,.8)',
  cta_bg: SAND, cta_color: TEAL, cta_radius: R(999), cta_size: 15,
  headline_font_family: 'serif', headline_size: 56, headline_weight: '600', subtitle_size: 17,
  layout: 'stack', vertical_align: 'center', banner_radius: R(24), banner_padding: 80,
  eyebrow_show: true, eyebrow_text: `Let's plan it`, eyebrow_color: SAND,
})])])]));

// ═══════════════════════════════════════════════════════════════════
K.emit({
  slug: 'wander', name: 'Wander',
  tags: ['travel', 'tours', 'expedition', 'adventure'],
  description: `Wander — small-group expeditions to wild places. Teal + sand + coral, Bricolage Grotesque (display) + Work Sans (body). Riproduzione fedele OLOtheme Wander (Travel agency).`,
  colors: {
    primary: CORAL, primary_contrast: WHITE,
    secondary: SAND, secondary_contrast: TEAL,
    muted: CREAM2, muted_contrast: TXT,
    text: TXT, text_muted: TXTSOFT,
    background: CREAM, border: LINE, link: CORAL,
  },
  css_disp:  `"Bricolage Grotesque", -apple-system, sans-serif`,
  css_sans:  `"Work Sans", -apple-system, sans-serif`,
  heading_weight: '600', heading_line_height: '1.02',
  google_fonts: ['Bricolage Grotesque', 'Work Sans'],
  logo_variant: 'light',
  menu: [
    { title: 'Destinations', url: '#destinations' },
    { title: 'Trip types',   url: '#trips'        },
    { title: 'Why Wander',   url: '#why'          },
    { title: 'Journal',      url: '#'             },
  ],
  header: { bg: TEAL, text_color: 'rgba(255,255,255,.82)', sticky_bg: `rgba(13,59,63,.9)`, logo_width: 140 },
  footer: {
    bg: TEAL, headColor: WHITE,
    brand: {
      name: 'Wander',
      tagline: `Small-group expeditions to the world\'s wild places. Expert-led, low impact, far from the crowds.`,
    },
    columns: [
      { title: 'Travel', links: ['Destinations', 'Trip types', 'Group trips', 'Private trips'] },
      { title: 'Wander', links: ['Why us', 'Our guides', 'Sustainability', 'Journal'] },
      { title: 'Help',   links: ['Booking & payments', 'Travel insurance', 'FAQ', 'Contact'] },
    ],
    bottom: { left: `© 2026 Wander — an OLOtheme demo.`, right: 'Built with OLObuild' },
  },
  cursor: false,
}, home);
