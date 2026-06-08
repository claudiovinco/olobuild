/* Contour — ricomposizione TILE-PURE (image-free). Health & Fitness — pilates & movement studio.
   Hedvig Letters Serif (display) + Outfit (body). LIGHT warm theme.
   Palette: --paper:#f4efe6 / --terra:#cf7e4f / --ink:#2c2a26 / --sage:#8a9a7b  */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('co');

// ── PALETTE (da :root contour.css) ──────────────────────────────────────────
const PAPER   = '#f4efe6';   // --paper (sfondo pagina)
const PAPER2  = '#ebe3d5';   // --paper-2 (surface alt)
const CARD    = '#fbf7f0';   // --card
const INK     = '#2c2a26';   // --ink (heading / dark bg)
const INK2    = '#4a463f';   // --ink-2
const TERRA   = '#cf7e4f';   // --terra (accent / primary)
const TERRAD  = '#bb6a3c';   // --terra-d
const SAGE    = '#8a9a7b';   // --sage
const TXT     = '#5e584e';   // --txt
const TXTDIM  = '#938c7e';   // --txt-dim
const LINE    = '#ddd3c2';   // --line
const LINE2   = '#cabfa9';   // --line-2
const WHITE   = '#ffffff';

// ── HELPERS ─────────────────────────────────────────────────────────────────

// section-header centrato con eyebrow + headline con parola accentata
const shead = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: TERRA, eyebrow_dot_color: TERRA, eyebrow_separator: '',
  headline_lines: [
    { text: l1, color: INK, italic: false },
    { text: accent, color: TERRA, italic: true },
  ],
  headline_font_family: 'serif', headline_font_size: 46, headline_font_weight: '400',
  headline_align: 'center', headline_inline: true,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false,
  tagline_text_color: TXTDIM, tagline_text_size: 16.5,
  layout: 'center', gap: 16,
});

const home = [];

// ── 1) HERO (hero-split, image-free: showcase laterale con badge statistiche) ─
// Blueprint: grid 1.05fr .95fr, headline "Move better, / breathe easier."
// CTA: "Start with an intro offer" (terra) + "See timetable" (outline btn--out)
// Art: co-hero__art con media + tag floating "Max 8 / per class"
home.push(sec(PAPER, 'large', [row([col('1-1', [tile('hero-split', {
  eyebrow_text: 'Reformer · mat · private', eyebrow_dot_color: TERRA, eyebrow_color: TERRA,
  headline_lines: [
    { text: 'Move better,', color: INK, italic: false },
    { text: 'breathe ', color: INK, italic: false },
    { text: 'easier.', color: TERRA, italic: true },
  ],
  headline_font_family: 'serif', headline_font_size: 68, headline_line_height: 1.04,
  headline_font_weight: '400', headline_align: 'left',
  subhead: 'A pilates studio built around small classes and good attention — so every session meets your body where it is today.',
  subhead_color: TXT, subhead_size: 18, subhead_italic: false, subhead_max_width: 440,
  cta1_text: 'Start with an intro offer', cta1_url: '#pricing',
  cta1_bg: TERRA, cta1_color: WHITE, cta1_size: 15, cta1_radius: R(999), cta1_radius_hover: R(999),
  cta2_text: 'See timetable', cta2_url: '#timetable',
  cta2_bg: 'transparent', cta2_color: INK, cta2_border: LINE2, cta2_size: 15,
  cta2_radius: R(999), cta2_radius_hover: R(999),
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: PAPER2 },
  showcase_padding: 28, showcase_radius: R(200), showcase_radius_hover: R(200),
  showcase_badge_text: 'STUDIO · AT A GLANCE', showcase_badge_dot: TERRA,
  showcase_badge_bg: CARD, showcase_badge_color: INK,
  showcase_items: [
    { number: 'Max per class', text: '8', italic: false, text_color: TERRA, bg: { type: 'solid', color: CARD } },
    { number: 'Classes weekly', text: '30+', italic: false, text_color: INK, bg: { type: 'solid', color: CARD } },
    { number: 'Instructors', text: '6', italic: false, text_color: INK, bg: { type: 'solid', color: CARD } },
    { number: 'Years open', text: '9', italic: false, text_color: INK, bg: { type: 'solid', color: CARD } },
  ],
  showcase_card_radius: R(14), showcase_card_radius_hover: R(14), showcase_card_shadow: 'sm',
  showcase_caption_left: 'Contour Studio', showcase_caption_right: 'Since 2017',
  showcase_hover_effect: 'none',
  split_ratio: '1.05fr .95fr', gap: 48, min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
})])])]));

// ── 2) STAT STRIP (counter ×4) ───────────────────────────────────────────────
// Blueprint: co-stats b → font-family:var(--disp) serif, font-size clamp(38px,4.6vw,58px), color:var(--ink)
// Suffix "+" su "30+" in color var(--terra) (.u)
const stat = (prefix, number, suffix, label) => col('1-4', [tile('counter', {
  number, suffix, prefix, label, icon_emoji: '',
  text_color: INK, number_color: INK, number_font_size: '54', number_font_weight: '400',
  number_font_family: 'serif',
  suffix_color: TERRA,
  label_color: TXTDIM, label_font_size: '13',
  bg_type: 'color', bg_color: 'transparent', padding: '8', border_radius: '0',
})]);
home.push(sec(CARD, 'small', [row([
  stat('', '8', '', 'Max per class'),
  stat('', '30', '+', 'Classes weekly'),
  stat('', '6', '', 'Instructors'),
  stat('', '9', '', 'Years open'),
], { gap: 24 })]));

// ── 3) CLASS TYPES (section-header + info-cards ×3 con card: bg+border) ──────
// Blueprint: .co-class ha background:var(--card) + border:1px solid var(--line) + border-radius:16px
// NO process-steps: le card hanno media (image placeholder) + level badge + titolo + desc
// La variante "card con media" è info-cards con show_media:true
home.push(sec(PAPER, 'large', [
  row([col('1-1', [shead('What we teach', 'Three ways to ', 'practise', '')])]),
  row([col('1-1', [tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' }, container_padding: 0,
    container_gap: 18, columns: 3, items_gap: 18,
    card_bg: { type: 'solid', color: CARD }, card_color: TXTDIM,
    card_radius: R(16), card_padding: 26,
    card_border: `1px solid ${LINE}`,
    show_icon: false, show_counter: true, show_counter_label: false,
    show_arrow: false, show_footer: false, show_media: false,
    counter_shape: 'plain', counter_color: TERRA, counter_size: 11,
    title_color: INK, title_font_family: 'serif', title_size: 25, title_weight: '400', title_italic: false,
    description_size: 14.5,
    items: [
      { counter: 'All levels', title: 'Reformer Flow', description: 'Spring-loaded resistance for strength, control and length. Our signature class.' },
      { counter: 'Beginner friendly', title: 'Mat & Breath', description: 'Classical mat work and breathwork — the foundations, slowed right down.' },
      { counter: 'One-to-one', title: 'Private Sessions', description: 'Fully tailored work for rehab, pregnancy or going deeper. Just you and a teacher.' },
    ],
    card_hover_effect: 'lift',
  })])])
]));

// ── 4) FINDER — tile dedicato (chip → result-card) ───────────────────────────
// Blueprint: co-finder #match · 4 opzioni chip + 4 result-card con titolo/testo/meta
// zone_accent #cf7e4f (TERRA) + zone_on #fff (da --fx-zone-accent/--fx-zone-on CSS inline)
home.push(sec(PAPER2, 'large', [
  row([col('1-1', [tile('finder', {
    eyebrow: `Not sure where to start?`,
    heading: `Where's your body today?`,
    intro: `Pick the option that fits — we've got a class to match.`,
    zone_accent: TERRA,
    zone_on: WHITE,
    card_bg: CARD,
    card_border: `1px solid ${LINE}`,
    align: 'center',
    items: [
      {
        option: 'Desk & screen tight',
        title: 'The Reset',
        text: `A Reformer Flow that undoes the desk slump — open the chest, free the hips and wake up a sleepy back. You'll stand taller by the time you reach the door.`,
        meta: 'Reformer Flow · all levels',
        cta_text: 'Try this class',
        cta_url: '#pricing',
        icon: 'monitor',
      },
      {
        option: 'New to pilates',
        title: 'The Foundations',
        text: `Classical Mat & Breath, slowed right down. Learn the principles, find your centre and build the habit before you ever touch a spring.`,
        meta: 'Mat & Breath · beginner friendly',
        cta_text: 'Try this class',
        cta_url: '#pricing',
        icon: 'compass',
      },
      {
        option: 'Easing back gently',
        title: 'One to One',
        text: `Coming back from injury, pregnancy or a long pause? A private session meets your body exactly where it is, with a plan that's entirely yours.`,
        meta: 'Private Sessions · fully tailored',
        cta_text: 'Book a private',
        cta_url: '#pricing',
        icon: 'heart',
      },
      {
        option: 'Want to build strength',
        title: 'Reformer, Twice a Week',
        text: `Our signature Reformer Flow on heavier springs, twice weekly. Real, controlled strength — the kind that quietly holds you up for everything else.`,
        meta: 'Reformer Flow · progressive',
        cta_text: 'Try this class',
        cta_url: '#pricing',
        icon: 'trending-up',
      },
    ],
  })])])
]));

// ── 5) TIMETABLE (tile dedicato "schedule" — griglia settimanale vera) ────────
// co-slot.on = TERRA accent (zone_accent) · co-slot.alt = PAPER2 (cell_bg)
// co-slot base (no class) = studio rest / slot vuoto → cella senza contenuto
home.push(sec(CARD, 'large', [
  row([col('1-1', [shead('This week', 'Find your ', 'time', '')])]),
  row([col('1-1', [tile('schedule', {
    eyebrow: 'This week',
    heading: 'Find your time',
    days: 'Mon, Tue, Wed, Thu, Fri',
    corner_label: 'Time',
    rows: [
      { time: '07:00', cells: '!Reformer · Ana | Mat · Jo | !Reformer · Ana | Mat · Jo | !Reformer · Lena' },
      { time: '12:30', cells: 'Express · 30m | !Reformer · Lena | Express · 30m | !Reformer · Lena | Express · 30m' },
      { time: '18:30', cells: '!Reformer · Ana | Mat & Breath · Jo | !Reformer · Ana | Mat & Breath · Jo | ' },
    ],
    zone_accent: TERRA,
    zone_on: WHITE,
    cell_bg: PAPER2,
    card_border: LINE,
    head_color: INK,
    align: 'left',
  })])])
]));

// ── 6) INSTRUCTORS (section-header + 4 team tiles con avatar circolare) ────────
// Blueprint: .co-mem → avatar circolare (border-radius:50%), nome serif 21px, ruolo terra 12.5px bold
// CORREZIONE: era info-cards con monogramma → ora tile 'team' (pattern sterling)
const instructor = (name, role) => col('1-4', [tile('team', {
  photo: '', name, role, bio: '', link_url: '', link_text: '',
  photo_size: '120', photo_shape: 'circle', photo_border_width: '0', photo_shadow: 'none', photo_gap: '16',
  info_bg_color: 'transparent', info_text_color: INK, role_color: TERRA, info_align: 'center',
  name_size: '21', name_weight: '400', name_font: 'serif',
  role_size: '13', role_weight: '600',
  bg_color: 'transparent', tile_padding: { top: 0, right: 0, bottom: 0, left: 0 }, border_radius: '0',
})]);
home.push(sec(PAPER, 'large', [
  row([col('1-1', [shead('Your teachers', 'Trained, patient, ', 'present', '')])]),
  row([
    instructor('Ana Reyes', 'Studio lead'),
    instructor('Jo Park', 'Mat & breath'),
    instructor('Lena Voss', 'Reformer'),
    instructor('Marco Sala', 'Rehab & private'),
  ], { gap: 16 }),
]));

// ── 7) PRICING (×3 in row) ────────────────────────────────────────────────────
// Blueprint: .co-plan ha background:var(--card) + border:1px solid var(--line) + border-radius:18px + padding:32px
// Plan featured (.co-plan.feat): background:var(--ink)
// Intro Offer + Class Pack: btn--out (outline LINE2); Unlimited: btn--terra
home.push(sec(CARD, 'large', [
  row([col('1-1', [shead(
    'Memberships', 'Simple, ', 'flexible plans',
    `New here? Two weeks of unlimited classes for €49. No contract, freeze anytime.`
  )])]),
  row([
    col('1-3', [tile('pricing', {
      plan_name: 'Intro Offer', price: '49', currency: '€', period: '/2 weeks',
      features: 'All group classes\nIntro reformer workshop\nNo commitment',
      is_popular: false, badge_text: '',
      bg_color: CARD, border_color: LINE, price_color: INK, accent_color: TERRA,
      cta_text: 'Claim offer', cta_url: '#', cta_bg: 'transparent', cta_color: INK,
      cta_border_color: LINE2,
      border_radius: R(18),
      description: 'Unlimited classes for your first fortnight.',
    })]),
    col('1-3', [tile('pricing', {
      plan_name: 'Unlimited', price: '129', currency: '€', period: '/month',
      features: 'Unlimited group classes\nPriority booking\n10% off privates',
      is_popular: true, badge_text: 'Most popular', badge_bg_color: TERRA,
      bg_color: INK, price_color: PAPER, accent_color: TERRA,
      cta_text: 'Join now', cta_url: '#', cta_bg: TERRA, cta_color: WHITE,
      border_radius: R(18),
      description: 'For a steady, regular practice.',
    })]),
    col('1-3', [tile('pricing', {
      plan_name: 'Class Pack', price: '160', currency: '€', period: '/10 classes',
      features: '10 group classes\nShareable with a friend\nFlexible scheduling',
      is_popular: false, badge_text: '',
      bg_color: CARD, border_color: LINE, price_color: INK, accent_color: TERRA,
      cta_text: 'Buy a pack', cta_url: '#', cta_bg: 'transparent', cta_color: INK,
      cta_border_color: LINE2,
      border_radius: R(18),
      description: 'Come at your own pace, valid 4 months.',
    })]),
  ], { gap: 16, vertical_align: 'stretch' }),
]));

// ── 8) TESTIMONIAL ────────────────────────────────────────────────────────────
// Blueprint: .co-testi q → serif italic, font-size clamp(24px,3.4vw,40px), color:var(--ink)
// .co-testi__by → sans 600, 13px, letter-spacing, uppercase, color:var(--terra)
home.push(sec(PAPER, 'large', [row([col('1-1', [tile('testimonial', {
  quote: `"I came in with a cranky back and left, three months later, standing taller than I have in years. The teaching here is just exceptional."`,
  author_name: 'Federica M.', author_role: 'Member since 2024', rating: '0',
  layout: 'single', show_line: false,
  bg_color: 'transparent', text_color: INK, border_radius: '0', avatar: '',
})])])]));

// ── 9) CTA FINALE ─────────────────────────────────────────────────────────────
// Blueprint: h2 "Your first <em>two weeks,</em><br/>€49." — p "Unlimited classes, no contract..."
// UN solo bottone "Claim the intro offer" (btn--terra btn--lg)
// Sfondo: var(--ink), h2 color:var(--paper), em color:var(--terra), p color:#cfc7b9
home.push(sec(INK, 'large', [row([col('1-1', [tile('cta-banner', {
  headline: 'Your first ', headline_accent: 'two weeks,', headline_accent_italic: true,
  headline_line2: '€49.',
  subtitle: `Unlimited classes, no contract. Come find out what good movement feels like.`,
  cta_text: 'Claim the intro offer', cta_url: '#pricing',
  bg: { type: 'solid', color: INK }, text_color: PAPER, accent_color: TERRA,
  subtitle_color: '#cfc7b9',
  cta_bg: TERRA, cta_color: WHITE, cta_radius: R(999), cta_size: 15,
  headline_font_family: 'serif', headline_size: 56, headline_weight: '400',
  subtitle_size: 17,
  layout: 'stack', vertical_align: 'center', banner_radius: R(0), banner_padding: 80,
})])])]));

// ── EMIT ──────────────────────────────────────────────────────────────────────
K.emit({
  slug: 'contour', name: 'Contour',
  tags: ['health', 'fitness', 'pilates', 'wellness', 'studio'],
  description: 'Contour — pilates & movement studio. Warm light theme, Hedvig Letters Serif (display) + Outfit (body). Terra accent (#cf7e4f) su carta calda (#f4efe6). Small reformer classes, mat work e one-to-one sessions.',
  colors: {
    primary: TERRA, primary_contrast: WHITE,
    secondary: SAGE, secondary_contrast: WHITE,
    muted: PAPER2, muted_contrast: TXT,
    text: TXT, text_muted: TXTDIM,
    background: PAPER, border: LINE, link: TERRA,
  },
  css_disp: `"Hedvig Letters Serif", Georgia, serif`,
  css_sans: `"Outfit", -apple-system, sans-serif`,
  heading_weight: '400', heading_line_height: '1.1',
  google_fonts: ['Hedvig+Letters+Serif:opsz@12..24', 'Outfit:wght@300;400;500;600;700'],
  logo_variant: 'dark',
  menu: [
    { title: 'Classes', url: '#classes' },
    { title: 'Timetable', url: '#timetable' },
    { title: 'Instructors', url: '#team' },
    { title: 'Pricing', url: '#pricing' },
  ],
  header: {
    bg: 'rgba(244,239,230,.88)', text_color: TXT,
    sticky_bg: 'rgba(244,239,230,.95)', logo_width: 130,
  },
  footer: {
    bg: PAPER,
    headColor: INK,
    brand: {
      name: 'Contour',
      tagline: 'Pilates & movement studio. Small classes, good attention, a calm room.',
    },
    columns: [
      { title: 'Studio', links: ['Classes', 'Timetable', 'Instructors', 'Pricing'] },
      { title: 'About', links: ['Our studio', 'Teacher training', 'Gift cards'] },
      { title: 'Visit', links: ['22 Linden Mews', 'Mon–Sat from 7am', 'hello@contour.studio'] },
    ],
    bottom: { left: '© 2026 Contour — an OLOtheme demo.', right: 'Built with OLObuild' },
  },
  cursor: false,
}, home);
