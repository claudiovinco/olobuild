/* Loft — ricomposizione TILE-PURE (image-free). Architecture studio. Charcoal + stone. */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('lo');

const BG='#1c1c1e', BG2='#202022', PANEL='#26262a', PANEL2='#2f2f34', INK='#121214';
const STONE='#c9c2b6', STONEL='#d8d2c8', CLAY='#b08968', CREAM='#eeece6';
const TXT='#9a958c', DIM='#646058', LINE='rgba(238,236,230,.12)', LINE2='rgba(201,194,182,.4)';
const WHITE='#ffffff';

/* ─── helper: section-header centrato ─── */
const shead = (eyebrow, l1, l2, intro) => tile('section-header', {
  eyebrow_show: !!eyebrow, eyebrow_text: eyebrow||'', eyebrow_color: STONE, eyebrow_dot_color: STONE, eyebrow_separator:'',
  headline_lines: l2
    ? [{ text: l1, color: CREAM, italic: false }, { text: l2, color: STONE, italic: true }]
    : [{ text: l1, color: CREAM, italic: false }],
  headline_font_family: 'sans-serif', headline_font_size: 42, headline_font_weight: '600', headline_align: 'center',
  headline_inline: !!l2,
  tagline_show: !!intro, tagline_text: intro||'', tagline_text_italic: false, tagline_text_color: TXT, tagline_text_size: 16,
  layout: 'center', gap: 16,
});

/* ─── helper: section-header sinistra con regola ─── */
const sheadLeft = (l1) => tile('section-header', {
  eyebrow_show: false,
  headline_lines: [{ text: l1, color: CREAM, italic: false }],
  headline_font_family: 'sans-serif', headline_font_size: 38, headline_font_weight: '600', headline_align: 'left',
  tagline_show: false,
  layout: 'stack', gap: 0,
});

const home = [];

/* ─── 1) HERO SPLIT ─── */
/* blueprint: "Quiet buildings, made of light and material." + eyebrow + p + cta stone */
home.push(sec(BG, 'large', [row([col('1-1', [tile('hero-split', {
  eyebrow_text: 'Architecture & interiors', eyebrow_dot_color: STONE, eyebrow_color: STONE,
  headline_lines: [
    { text: 'Quiet buildings,', color: CREAM, italic: false },
    { text: 'made of light', color: STONE, italic: true },
    { text: 'and material.', color: CREAM, italic: false },
  ],
  headline_font_family: 'sans-serif', headline_font_size: 72, headline_line_height: 1.0,
  headline_font_weight: '600', headline_align: 'left',
  subhead: 'A small studio designing homes and workspaces where structure, daylight and a few honest materials do all the talking.',
  subhead_color: TXT, subhead_size: 18, subhead_italic: false, subhead_max_width: 480,
  cta1_text: 'Start a project', cta1_url: '#contact',
  cta1_bg: STONE, cta1_color: INK, cta1_size: 13, cta1_radius: R(0), cta1_radius_hover: R(0),
  cta2_text: 'See the work', cta2_url: '#projects',
  cta2_bg: 'transparent', cta2_color: CREAM, cta2_border: LINE2, cta2_size: 13, cta2_radius: R(0), cta2_radius_hover: R(0),
  stats: [],
  /* showcase: abstract panel con materiali/studio info — image-free */
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: PANEL },
  showcase_padding: 28, showcase_radius: R(0), showcase_radius_hover: R(0),
  showcase_badge_text: 'LOFT STUDIO · EST. 2008', showcase_badge_dot: STONE, showcase_badge_bg: INK, showcase_badge_color: CREAM,
  showcase_items: [
    { number: 'Based in', text: 'Milan', italic: false, text_color: CREAM, bg: { type: 'solid', color: BG2 } },
    { number: 'Projects built', text: '60+', italic: false, text_color: STONE, bg: { type: 'solid', color: BG2 } },
    { number: 'Typologies', text: 'Residential · Workspace', italic: false, text_color: CREAM, bg: { type: 'solid', color: BG2 } },
    { number: 'Practice', text: 'Architecture & Interiors', italic: false, text_color: CREAM, bg: { type: 'solid', color: BG2 } },
  ],
  showcase_card_radius: R(0), showcase_card_radius_hover: R(0), showcase_card_shadow: 'none',
  showcase_caption_left: 'LOFT', showcase_caption_right: 'STUDIO', showcase_hover_effect: 'none',
  split_ratio: '1.2fr .8fr', gap: 52, min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
})])])]));

/* ─── 2) SELECTED PROJECTS (blueprint: project rail — SEGNALATO best-effort).
   Il blueprint ha data-hscroll drag-scroll rail (ProjectRail — NEW tile candidato).
   Approssimo con section-header sinistra + info-cards 4 carte progetto (colonne card). */
home.push(sec(BG2, 'large', [
  row([col('1-1', [sheadLeft('Selected projects')])]),
  row([col('1-1', [tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' }, container_padding: 0, container_gap: 20, columns: 4, items_gap: 20,
    card_bg: { type: 'solid', color: PANEL }, card_color: DIM, card_radius: R(0), card_padding: 24,
    show_icon: false, show_counter: true, show_counter_label: true, show_arrow: false, show_footer: true, show_media: false,
    counter_shape: 'plain', counter_color: STONE, title_color: CREAM,
    title_font_family: 'sans-serif', title_size: 23, title_weight: '600', title_italic: false,
    description_size: 12, counter_size: 13,
    items: [
      { counter: `'25`, counter_label: 'Private residence', title: 'House on the Slope', description: 'Private residence · 240 m²', footer_text: '', footer_dot_color: STONE },
      { counter: `'24`, counter_label: 'Workspace', title: 'Riverside Studio', description: 'Workspace · adaptive reuse', footer_text: '', footer_dot_color: STONE },
      { counter: `'24`, counter_label: 'Residence', title: 'Courtyard House', description: 'Residence · new build', footer_text: '', footer_dot_color: STONE },
      { counter: `'23`, counter_label: 'Public', title: 'The Reading Room', description: 'Public · library extension', footer_text: '', footer_dot_color: STONE },
    ],
    card_hover_effect: 'lift',
  })])])
]));

/* ─── 3) FLOOR-PLAN HOTSPOT — tile vero Hotspots (data-hotspot, 3 marker su planimetria). */
home.push(sec(BG, 'large', [
  row([col('1-1', [tile('hotspots', {
    eyebrow: '',
    heading: 'House on the Slope · in plan',
    intro: '',
    panel_label: 'ground-floor plan — concrete spine, glazed south face',
    aspect_ratio: '16/10',
    items: [
      { x: 24, y: 40, title: 'Concrete spine',    text: 'Holds the hill back and anchors every room.',                          meta: '' },
      { x: 58, y: 60, title: 'Glazed south face',  text: 'Full-height glass for low winter sun, shaded in summer.',              meta: '' },
      { x: 80, y: 34, title: 'Roof light',          text: 'A single slot pulls daylight to the core.',                           meta: '' },
    ],
    zone_accent: STONE,
    zone_on:     INK,
    panel_bg:    PANEL,
    card_bg:     PANEL2,
    card_border: LINE,
    align:       'left',
  })])])
]));

/* ─── 4) HOW WE WORK — process-steps CON CARD (lo-appr: grid 3 col, border 1px LINE, bg BG, padding 36px) ─── */
/* CSS: .lo-appr{background:var(--line);border:1px solid var(--line)} .lo-appr__c{background:var(--bg);padding:36px} */
home.push(sec(BG, 'large', [
  row([col('1-1', [sheadLeft('How we work')])]),
  row([col('1-1', [tile('process-steps', {
    columns: 3, gap: 1, align: 'left', auto_number: false, item_gap: 10,
    number_style: 'plain', number_color: STONE, number_size: 13, number_font: 'sans-serif', number_weight: '600',
    title_color: CREAM, title_size: 24, title_font: 'sans-serif', title_weight: '600',
    desc_color: DIM, desc_size: 14.5,
    card_bg: BG, card_border: LINE, card_radius: R(0), card_padding: 36,
    items: [
      { number: '01', title: 'Listen', description: `We start on site, with how you live and how the light moves — long before a line is drawn.` },
      { number: '02', title: 'Reduce', description: `Fewer materials, fewer moves. We design until there's nothing left to take away.` },
      { number: '03', title: 'Build well', description: `We stay on through construction so the detail that mattered on paper survives the site.` },
    ],
  })])])
]));

/* ─── 5) STAT STRIP (counter ×4) — sfondo BG2 (.panel) ─── */
/* CSS: .lo-sec.panel{background:var(--bg-2)} — sezione stats ha classe .panel */
/* CSS: .lo-stats b → color var(--cream), numero grande. label → var(--txt-dim) */
const stat = (number, suffix, label) => col('1-4', [tile('counter', {
  number, suffix, prefix: '', label,
  icon_emoji: '',
  text_color: CREAM, number_color: CREAM,
  number_font_size: '52', number_font_weight: '600', label_color: DIM, label_font_size: '12',
  bg_type: 'color', bg_color: 'transparent', padding: '8', border_radius: '0',
})]);
home.push(sec(BG2, 'small', [row([
  stat('60', '+', 'Projects built'),
  stat('18', '', 'Years'),
  stat('9', '', 'Awards'),
  stat('7', '', 'In the studio'),
], { gap: 24 })]));

/* ─── 6) THE STUDIO — team ×4 (avatar cerchio, nome/ruolo) ─── */
/* CSS: .lo-mem b{font-size:18px;color:var(--cream)} .lo-mem span{font-family:var(--mono);font-size:12px;color:var(--stone)} */
/* blueprint: .lo-team grid 4 colonne, sfondo BG2 (nessuna classe .panel ma sezione #studio è .lo-sec) */
/* Layout: sequenza hero(BG)→projects(BG2)→hotspot(BG)→approach(BG)→stats(BG2)→studio(BG2)? */
/* HTML: #studio è .lo-sec senza .panel → sfondo BG */
const loMember = (name, role) => col('1-4', [tile('team', {
  photo: '', name, role, bio: '', link_url: '', link_text: '',
  photo_size: '120', photo_shape: 'circle', photo_border_width: '0', photo_shadow: 'none', photo_gap: '14',
  info_bg_color: 'transparent', info_text_color: CREAM, role_color: STONE, info_align: 'left',
  name_size: '18', name_weight: '600', role_size: '12',
  bg_color: 'transparent', tile_padding: { top: 0, right: 0, bottom: 0, left: 0 }, border_radius: '0',
})]);
home.push(sec(BG, 'large', [
  row([col('1-1', [sheadLeft('The studio')])]),
  row([
    loMember('Ana Roca', 'Founding partner'),
    loMember('Ito Vesa', 'Partner'),
    loMember('Priya Nair', 'Associate'),
    loMember('Karl Weiss', 'Interiors lead'),
  ], { gap: 16 }),
]));

/* ─── 7) MATERIAL STUDY — tile mixer nativo ─── */
home.push(sec(BG2, 'large', [row([col('1-1', [tile('mixer', {
  eyebrow: 'Material study',
  heading: 'Compose a palette',
  intro: 'Every project starts as a few materials on the bench. Tap two or three and see how the tones sit together before we ever draw a line.',
  max: 3,
  empty_label: 'Tap materials to blend',
  zone_accent: '#c9c2b6',
  zone_on: '#121214',
  card_bg: PANEL,
  card_border: LINE,
  align: 'left',
  items: [
    { name: 'Concrete', color: '#b8b2a6' },
    { name: 'Oak',      color: '#b08968' },
    { name: 'Clay',     color: '#9c6b4a' },
    { name: 'Brass',    color: '#c9a76a' },
    { name: 'Charcoal', color: '#3a3a3e' },
    { name: 'Linen',    color: '#e3ddd0' },
  ],
})])])]));


/* ─── 8) CTA FINALE — 2 pulsanti (Start a project + See the work) ─── */
/* CSS: .lo-cta__cta → .btn--stone (bg stone, ink) + .btn--out (transparent, cream, border LINE2) */
home.push(sec(BG, 'large', [row([col('1-1', [tile('cta-banner', {
  headline: 'Building something?',
  headline_accent: '',
  headline_accent_italic: false,
  subtitle: `We take on a handful of projects a year. Tell us about the site and the idea — we'll tell you honestly if we're the right studio.`,
  cta_text: 'Start a project', cta_url: '#contact',
  bg: { type: 'solid', color: PANEL },
  text_color: CREAM, accent_color: STONE, subtitle_color: TXT,
  cta_bg: STONE, cta_color: INK, cta_radius: R(0), cta_size: 13,
  cta2_text: 'See the work', cta2_url: '#projects',
  cta2_bg: 'transparent', cta2_color: CREAM, cta2_border: LINE2, cta2_radius: R(0),
  headline_font_family: 'sans-serif', headline_size: 56, headline_weight: '600',
  subtitle_size: 17,
  layout: 'stack', vertical_align: 'center', banner_radius: R(0), banner_padding: 80,
})])])]));

K.emit({
  slug: 'loft', name: 'Loft',
  tags: ['architecture', 'design', 'interiors', 'home-living', 'studio'],
  description: `Loft — Architecture & interiors studio. Minimal concrete/stone. Archivo sans. Charcoal + stone + clay. Riproduzione tile-pure dell'OLOtheme Loft (Home & Living).`,
  colors: {
    primary: STONE,
    primary_contrast: INK,
    secondary: CLAY,
    secondary_contrast: CREAM,
    muted: BG2,
    muted_contrast: TXT,
    text: TXT,
    text_muted: DIM,
    background: BG,
    border: LINE,
    link: STONE,
  },
  css_disp: `"Archivo", -apple-system, sans-serif`,
  css_sans: `"Archivo", -apple-system, sans-serif`,
  heading_weight: '600',
  heading_line_height: '1.04',
  google_fonts: ['Archivo', 'Archivo Narrow'],
  logo_variant: 'light',
  menu: [
    { title: 'Projects', url: '#projects' },
    { title: 'Approach', url: '#approach' },
    { title: 'Studio', url: '#studio' },
    { title: 'Contact', url: '#contact' },
  ],
  header: { bg: 'rgba(28,28,30,.86)', text_color: DIM, sticky_bg: 'rgba(28,28,30,.86)', logo_width: 120 },
  footer: {
    bg: BG2,
    headColor: CREAM,
    brand: { name: 'Loft', tagline: 'Architecture & interiors studio. Calm, material-led buildings for living and working.' },
    columns: [
      { title: 'Studio', links: ['Projects', 'Approach', 'Team'] },
      { title: 'About', links: ['Awards', 'Press', 'Careers'] },
      { title: 'Contact', links: ['studio@loft.archi', '+39 02 7700', 'Milan'] },
    ],
    bottom: { left: '© 2026 Loft — an OLOtheme demo.', right: 'Built with OLObuild' },
  },
  cursor: { blend_mode: 'exclusion', ring_color: STONE, dot_color: STONE },
}, home);
