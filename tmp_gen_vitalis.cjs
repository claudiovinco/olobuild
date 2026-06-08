/* Vitalis — ricomposizione TILE-PURE (image-free). Health & Fitness: teal profondo + mint.
   Rifinitura pixel-perfect: team tiles avatar-cerchio, stat numeri cream, membership CTA semplice,
   cta finale btn--cream, descrizioni pillars/finder allineate al blueprint. */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('vt');

/* ── PALETTE (:root vitalis.css) ── */
const BG      = '#0e3b3a';
const BG2     = '#103f3e';
const PANEL   = '#14504d';
const PANEL2  = '#1a5d59';
const INK     = '#072422';
const MINT    = '#6fd6c4';
const MINTD   = '#52c2ae';
const SAND    = '#e7d8b8';
const CREAM   = '#eaf6f2';
const TXT     = '#a9cfc7';
const DIM     = '#6f9a92';
const LINE    = 'rgba(234,246,242,.13)';
const LINE2   = 'rgba(111,214,196,.42)';
const MINTTINT= 'rgba(111,214,196,.13)';
const WHITE   = '#ffffff';

const home = [];

/* ─── helper ─── */
const shead = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: MINT, eyebrow_dot_color: MINT, eyebrow_separator: '',
  headline_lines: [{ text: l1, color: CREAM, italic: false }, { text: accent, color: MINT, italic: true }],
  headline_font_family: 'serif', headline_font_size: 50, headline_font_weight: '500', headline_align: 'center', headline_inline: true,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false, tagline_text_color: DIM, tagline_text_size: 16.5,
  layout: 'center', gap: 16,
});

/* ═══════════════════════════════════════════════════════
   1) HERO (hero-split) — image-free
   ═══════════════════════════════════════════════════════ */
home.push(sec(BG, 'large', [row([col('1-1', [tile('hero-split', {
  eyebrow_text: 'Wellness & recovery clinic', eyebrow_dot_color: MINT, eyebrow_color: MINT,
  headline_lines: [
    { text: 'Feel like',    color: CREAM, italic: false },
    { text: 'yourself',    color: MINT,  italic: true  },
    { text: 'again.',      color: CREAM, italic: false },
  ],
  headline_font_family: 'serif', headline_font_size: 78, headline_line_height: 1.04, headline_font_weight: '500', headline_align: 'left',
  subhead: `Physiotherapy, recovery suites and longevity care under one calm roof — evidence-led, unhurried, and built around you.`,
  subhead_color: TXT, subhead_size: 18, subhead_italic: false, subhead_max_width: 460,
  cta1_text: 'Book a first visit', cta1_url: '#membership', cta1_bg: MINT, cta1_color: INK, cta1_size: 15, cta1_radius: R(999), cta1_radius_hover: R(999),
  cta2_text: 'Explore services',   cta2_url: '#services',   cta2_bg: 'transparent', cta2_color: CREAM, cta2_border: LINE2, cta2_size: 15, cta2_radius: R(999), cta2_radius_hover: R(999),
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: PANEL },
  showcase_padding: 28,
  showcase_radius: R(18), showcase_radius_hover: R(18),
  showcase_badge_text: 'ALWAYS 1:1', showcase_badge_dot: MINT, showcase_badge_bg: INK, showcase_badge_color: CREAM,
  showcase_items: [
    { number: 'Your team',      text: '12 clinicians', italic: false, text_color: MINT,  bg: { type: 'solid', color: PANEL2 } },
    { number: 'Recovery suites',text: '4 suites',      italic: false, text_color: CREAM, bg: { type: 'solid', color: PANEL2 } },
    { number: 'Members',        text: '3,200+',        italic: false, text_color: CREAM, bg: { type: 'solid', color: PANEL2 } },
    { number: 'Google rating',  text: '4.9',           italic: false, text_color: CREAM, bg: { type: 'solid', color: PANEL2 } },
  ],
  showcase_card_radius: R(12), showcase_card_radius_hover: R(12), showcase_card_shadow: 'none',
  showcase_caption_left: 'VITALIS', showcase_caption_right: 'WELLNESS CLINIC', showcase_hover_effect: 'none',
  split_ratio: '1.05fr .95fr', gap: 48, min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
})])])]));

/* ═══════════════════════════════════════════════════════
   2) PILLARS — "Care, in three pillars"
   Blueprint: .vt-cat = card con border:1px solid --line, border-radius:16px,
   min-height:320px, bg panel, testo sovrapposto (z-index 2, padding 26px).
   Approssimato con info-cards 3 colonne (CategoryTiles non esiste).
   NB: descrizioni corte dal blueprint: "Injury, posture & movement" ecc.
   ═══════════════════════════════════════════════════════ */
home.push(sec(BG, 'large', [
  row([col('1-1', [tile('section-header', {
    eyebrow_show: true, eyebrow_text: 'What we do', eyebrow_color: MINT, eyebrow_dot_color: MINT, eyebrow_separator: '',
    headline_lines: [{ text: 'Care, in', color: CREAM, italic: false }, { text: 'three pillars', color: MINT, italic: true }],
    headline_font_family: 'serif', headline_font_size: 50, headline_font_weight: '500', headline_align: 'center', headline_inline: true,
    tagline_show: false, layout: 'center', gap: 16,
  })])]),
  row([col('1-1', [tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' }, container_padding: 0, container_gap: 16,
    columns: 3, items_gap: 16,
    card_bg: { type: 'solid', color: PANEL }, card_color: TXT,
    card_radius: R(16), card_padding: 30,
    show_icon: true, show_counter: false, show_arrow: false, show_footer: false, show_media: false,
    icon_color: MINT, icon_bg_color: MINTTINT, title_color: CREAM,
    title_font_family: 'serif', title_size: 26, title_weight: '500', title_italic: false, description_size: 13.5,
    items: [
      { icon: 'activity',              title: 'Physiotherapy', description: 'Injury, posture & movement' },
      { icon: 'thermometer-snowflake', title: 'Recovery',      description: 'Sauna · ice · compression' },
      { icon: 'heart-pulse',           title: 'Longevity',     description: 'Bloods, plans & coaching' },
    ],
    card_hover_effect: 'lift',
  })])]),
]));

/* ═══════════════════════════════════════════════════════
   3) RECOVERY MENU — "Reset in an hour" (pricelist)
   Blueprint: .vt-menu-list 2 colonne, .vt-rec border-bottom:1px solid --line,
   nome serif 21px --cream, prezzo serif 20px --mint, descrizione 14px --txt-dim.
   ═══════════════════════════════════════════════════════ */
home.push(sec(BG2, 'large', [
  row([col('1-1', [shead('The recovery menu', 'Reset in an ', 'hour', '')])]),
  row([col('1-1', [tile('pricelist', {
    preset: 'custom',
    bg: { type: 'none' },
    show_image: false,
    price_position: 'right',
    separator_style: 'dotted',
    separator_color: LINE2,
    title_color: CREAM,
    price_color: MINT,
    description_color: DIM,
    card_bg: '',
    card_border_radius: R(0),
    hover_lift: false,
    gap: '12',
    tile_padding: { top: 8, right: 0, bottom: 8, left: 0 },
    items: [
      { id: 'vt-pl1', title: 'Infrared sauna',      description: '45 min · deep heat, deeper calm.',          price: '€35', image_url: '', highlighted: false, badge: '' },
      { id: 'vt-pl2', title: 'Ice bath & contrast',  description: 'Guided breathwork included.',               price: '€28', image_url: '', highlighted: false, badge: '' },
      { id: 'vt-pl3', title: 'Compression therapy',  description: 'Legs & hips, 30 min.',                     price: '€30', image_url: '', highlighted: false, badge: '' },
      { id: 'vt-pl4', title: 'Sports massage',        description: '60 min with a clinical therapist.',        price: '€70', image_url: '', highlighted: false, badge: '' },
      { id: 'vt-pl5', title: 'Physio assessment',     description: 'Full movement screen & plan.',             price: '€90', image_url: '', highlighted: false, badge: 'Most popular' },
      { id: 'vt-pl6', title: 'Recovery day pass',     description: 'All suites, all day.',                     price: '€55', image_url: '', highlighted: false, badge: '' },
    ],
  })])]),
]));

/* ═══════════════════════════════════════════════════════
   4) STAT STRIP — counter ×4
   Blueprint .vt-stats b: colore --cream (NON mint). Solo il suffisso "+" è mint.
   number_color: CREAM, suffix per "3200+" reso via counter con number_color CREAM,
   il tile counter non distingue suffix-color → usiamo CREAM per il numero principale.
   ═══════════════════════════════════════════════════════ */
const stat = (number, suffix, prefix, label) => col('1-4', [tile('counter', {
  number, suffix, prefix, label, icon_emoji: '',
  text_color: CREAM,
  number_color: CREAM,
  number_font_size: '56', number_font_weight: '500',
  label_color: DIM, label_font_size: '12.5',
  bg_type: 'color', bg_color: 'transparent', padding: '8', border_radius: '0',
})]);
home.push(sec(BG, 'small', [row([
  stat('12',   '',  '', 'Clinicians'),
  stat('4',    '',  '', 'Recovery suites'),
  stat('3200', '+', '', 'Members'),
  stat('4.9',  '',  '', 'Google rating'),
], { gap: 24 })]));

/* ═══════════════════════════════════════════════════════
   5) TEAM — "People who listen first"
   Blueprint: .vt-mem = avatar cerchio (aspect 1:1, border-radius 50%), nome serif 21px --cream,
   ruolo sans 12.5px --mint. USA tile `team` (avatar circolare nativo).
   4 colonne, bg transparent, tile_padding zero.
   ═══════════════════════════════════════════════════════ */
const vtMember = (name, role) => col('1-4', [tile('team', {
  photo: '', name, role, bio: '', link_url: '', link_text: '',
  photo_size: '120', photo_shape: 'circle', photo_border_width: '0', photo_shadow: 'none', photo_gap: '16',
  info_bg_color: 'transparent', info_text_color: CREAM, role_color: MINT, info_align: 'center',
  name_size: '21', name_weight: '500', role_size: '12.5',
  bg_color: 'transparent', tile_padding: { top: 0, right: 0, bottom: 0, left: 0 }, border_radius: '0',
})]);
home.push(sec(BG2, 'large', [
  row([col('1-1', [shead('Your team', 'People who ', 'listen first', '')])]),
  row([
    vtMember('Dr. Anika Rao', 'Clinical lead'),
    vtMember('Sam Becker',    'Physiotherapist'),
    vtMember('Lina Ferri',    'Recovery coach'),
    vtMember('Dr. Omar Haddad', 'Longevity'),
  ], { gap: 16 }),
]));

/* ═══════════════════════════════════════════════════════
   6) MEMBERSHIP — "Care that compounds"
   Blueprint: sec-head + UNO solo pulsante centrato "Book a complimentary assessment".
   NON ha 3 piani pricing. Reso con section-header + cta-banner minimal stack.
   ═══════════════════════════════════════════════════════ */
home.push(sec(BG2, 'large', [
  row([col('1-1', [tile('section-header', {
    eyebrow_show: true, eyebrow_text: 'Membership', eyebrow_color: MINT, eyebrow_dot_color: MINT, eyebrow_separator: '',
    headline_lines: [{ text: 'Care that ', color: CREAM, italic: false }, { text: 'compounds', color: MINT, italic: true }],
    headline_font_family: 'serif', headline_font_size: 50, headline_font_weight: '500', headline_align: 'center', headline_inline: true,
    tagline_show: true,
    tagline_text: 'First assessment is complimentary. Memberships include recovery access and member rates on every treatment.',
    tagline_text_italic: false, tagline_text_color: DIM, tagline_text_size: 16.5,
    layout: 'center', gap: 16,
  })])]),
  row([col('1-1', [tile('cta-banner', {
    headline: '', headline_accent: '',
    subtitle: '',
    cta_text: 'Book a complimentary assessment', cta_url: '#membership',
    bg: { type: 'none' }, text_color: CREAM, accent_color: MINT, subtitle_color: TXT,
    cta_bg: MINT, cta_color: INK, cta_radius: R(999), cta_size: 15,
    headline_font_family: 'serif', headline_size: 0, headline_weight: '500', subtitle_size: 0,
    layout: 'stack', vertical_align: 'center', banner_radius: R(0), banner_padding: 0,
    tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
  })])]),
]));

/* ═══════════════════════════════════════════════════════
   7) TESTIMONIAL
   Blueprint: .vt-testi q — serif italic 40px --cream, autore mint uppercase.
   ═══════════════════════════════════════════════════════ */
home.push(sec(BG, 'large', [row([col('1-1', [tile('testimonial', {
  quote: `"Six weeks at Vitalis did more for my back than two years of guessing. Calm place, sharp people."`,
  author_name: 'Daniel R.', author_role: 'member since 2024', rating: '0',
  layout: 'single', show_line: false,
  bg_color: 'transparent', text_color: CREAM, border_radius: '0', avatar: '',
  font_family: 'serif', font_size: 38, font_style: 'italic', font_weight: '400',
})])])]));

/* ═══════════════════════════════════════════════════════
   8) FINDER — "What brings you in?" — tile finder
   Blueprint: --fx-zone-accent:#6fd6c4; --fx-zone-on:#072422
   ═══════════════════════════════════════════════════════ */
home.push(sec(BG2, 'large', [
  row([col('1-1', [tile('finder', {
    eyebrow: `Where to begin`,
    heading: `What brings you in?`,
    intro: ``,
    zone_accent: MINT,
    zone_on: INK,
    card_bg: PANEL,
    card_border: `1px solid ${LINE2}`,
    align: `center`,
    items: [
      {
        option: `Always tired`,
        title: `Energy & Bloodwork`,
        text: `A full panel, a sleep and nutrition review, and a plan to find what\x27s draining you \x2014 not just mask it with caffeine.`,
        meta: `90-min assessment \xB7 then monthly`,
        cta_text: ``,
        cta_url: `#`,
        icon: ``,
      },
      {
        option: `Nagging pain`,
        title: `Movement & Recovery`,
        text: `Physio-led assessment, hands-on treatment and a corrective programme to settle the pain and keep it gone.`,
        meta: `60-min sessions \xB7 6-week block`,
        cta_text: ``,
        cta_url: `#`,
        icon: ``,
      },
      {
        option: `Stressed & wired`,
        title: `Nervous-System Reset`,
        text: `Breathwork, guided recovery and HRV tracking to bring you down from constant fight-or-flight.`,
        meta: `45-min sessions \xB7 weekly`,
        cta_text: ``,
        cta_url: `#`,
        icon: ``,
      },
      {
        option: `A full reset`,
        title: `The Foundations Programme`,
        text: `Our flagship — bloodwork, movement, sleep and nutrition woven into one coached three-month plan.`,
        meta: `12 weeks \xB7 fully guided`,
        cta_text: ``,
        cta_url: `#`,
        icon: ``,
      },
    ],
  })])]),
]));

/* ═══════════════════════════════════════════════════════
   9) CTA FINALE — "Start feeling better"
   Blueprint: btn--mint + btn--cream (background:--cream, color:--ink).
   cta2_bg: CREAM (non transparent), cta2_color: INK.
   ═══════════════════════════════════════════════════════ */
home.push(sec(BG, 'large', [row([col('1-1', [tile('cta-banner', {
  headline: 'Start feeling ', headline_accent: 'better', headline_accent_italic: true,
  subtitle: `Your first assessment is on us. Come in, get a plan, and we'll take it from there.`,
  cta_text: 'Book your visit',  cta_url: '#membership',
  cta2_text: 'See services',    cta2_url: '#services', cta2_bg: CREAM, cta2_color: INK, cta2_radius: R(999), cta2_border: '',
  bg: { type: 'solid', color: INK }, text_color: CREAM, accent_color: MINT, subtitle_color: TXT,
  cta_bg: MINT, cta_color: INK, cta_radius: R(999), cta_size: 15,
  headline_font_family: 'serif', headline_size: 54, headline_weight: '500', subtitle_size: 17,
  layout: 'stack', vertical_align: 'center', banner_radius: R(24), banner_padding: 80,
})])])]));

/* ═══════════════════════════════════════════════════════
   EMIT
   ═══════════════════════════════════════════════════════ */
K.emit({
  slug: 'vitalis', name: 'Vitalis',
  tags: ['health', 'fitness', 'wellness', 'clinic', 'physiotherapy'],
  description: `Vitalis — Wellness & recovery clinic. Physiotherapy, recovery suites and longevity care. Deep teal + mint, Crimson Pro (display) + Lexend. Riproduzione fedele dell'OLOtheme Vitalis.`,
  colors: {
    primary: MINT, primary_contrast: INK,
    secondary: SAND, secondary_contrast: INK,
    muted: BG2, muted_contrast: TXT,
    text: TXT, text_muted: DIM,
    background: BG, border: LINE, link: MINT,
  },
  css_disp: `"Crimson Pro",Georgia,serif`,
  css_sans: `"Lexend",-apple-system,sans-serif`,
  heading_weight: '500', heading_line_height: '1.1',
  google_fonts: ['Crimson Pro', 'Lexend'],
  logo_variant: 'light',
  menu: [
    { title: 'Services',   url: '#services'   },
    { title: 'Recovery',   url: '#recovery'   },
    { title: 'Team',       url: '#team'       },
    { title: 'Membership', url: '#membership' },
  ],
  header: { bg: BG, text_color: TXT, sticky_bg: 'rgba(14,59,58,.86)', logo_width: 130 },
  footer: {
    bg: BG2, headColor: CREAM,
    brand: { name: 'Vitalis', tagline: 'Wellness & recovery clinic. Physiotherapy, recovery and longevity care under one roof.' },
    columns: [
      { title: 'Clinic', links: ['Services', 'Recovery', 'Team', 'Membership'] },
      { title: 'About',  links: ['Our approach', 'Facilities', 'Careers'] },
      { title: 'Visit',  links: ['5 Cedar Court', 'Mon–Sat · by appt', 'care@vitalis.clinic'] },
    ],
    bottom: { left: '© 2026 Vitalis — an OLOtheme demo.', right: 'Built with OLObuild' },
  },
  cursor: { blend_mode: 'exclusion', ring_color: '#6fd6c4', dot_color: '#6fd6c4' },
}, home);
