/* Vows — ricomposizione TILE-PURE (image-free). Wedding planner. Taupe + gold. Frank Ruhl Libre + Mulish. */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('vo');

const BG    = '#2a2620';
const BG2   = '#312c25';
const PANEL = '#383228';
const INK   = '#1b1813';
const GOLD  = '#cbab6a';
const GOLDL = '#d9bd82';
const CREAM = '#f3ecdf';
const TXT   = '#bcb09b';
const DIM   = '#8a7f6c';
const LINE  = 'rgba(243,236,223,.13)';
const LINE2 = 'rgba(203,171,106,.42)';
const GTINT = 'rgba(203,171,106,.14)';
const SAGE  = '#9aa088';

const home = [];

// helper: section-header centrato con eyebrow + headline (1 riga accentata italic inline)
const shead = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: GOLD, eyebrow_dot_color: GOLD, eyebrow_separator: '',
  headline_lines: [
    { text: l1, color: CREAM, italic: false },
    { text: accent, color: GOLD, italic: true },
  ],
  headline_font_family: 'serif', headline_font_size: 52, headline_font_weight: '500',
  headline_align: 'center', headline_inline: true,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false,
  tagline_text_color: DIM, tagline_text_size: 16,
  layout: 'center', gap: 16,
});

// 1) HERO — hero-split centrato con showcase astratto (image-free)
home.push(sec(BG, 'large', [row([col('1-1', [tile('hero-split', {
  eyebrow_text: 'Wedding planning & design',
  eyebrow_dot_color: GOLD, eyebrow_color: GOLD,
  headline_lines: [
    { text: 'A day that feels', color: CREAM, italic: false },
    { text: 'like you two.', color: GOLD, italic: true },
  ],
  headline_font_family: 'serif', headline_font_size: 80, headline_line_height: 1.04,
  headline_font_weight: '500', headline_align: 'center',
  subhead: 'We plan and design weddings that are unmistakably yours — beautiful, calm, and run so smoothly you actually get to enjoy them.',
  subhead_color: TXT, subhead_size: 18, subhead_italic: false, subhead_max_width: 500,
  cta1_text: 'Check our date', cta1_url: '#enquire',
  cta1_bg: GOLD, cta1_color: INK, cta1_size: 12, cta1_radius: R(999), cta1_radius_hover: R(999),
  cta2_text: 'Real weddings', cta2_url: '#weddings',
  cta2_bg: 'transparent', cta2_color: CREAM, cta2_border: LINE2, cta2_size: 12, cta2_radius: R(999), cta2_radius_hover: R(999),
  stats: [],
  showcase_enabled: true,
  showcase_bg: { type: 'solid', color: PANEL },
  showcase_padding: 32, showcase_radius: R(16), showcase_radius_hover: R(16),
  showcase_badge_text: 'NEXT CELEBRATION', showcase_badge_dot: GOLD, showcase_badge_bg: INK, showcase_badge_color: CREAM,
  showcase_items: [
    { number: 'A & J · Villa Lume', text: '19 Sep 2026', italic: true, text_color: GOLD, bg: { type: 'solid', color: BG2 } },
    { number: 'Lake Garda · Italy', text: '120 guests', italic: false, text_color: CREAM, bg: { type: 'solid', color: BG2 } },
    { number: 'Package', text: 'Full planning', italic: false, text_color: CREAM, bg: { type: 'solid', color: BG2 } },
  ],
  showcase_card_radius: R(10), showcase_card_radius_hover: R(10), showcase_card_shadow: 'none',
  showcase_caption_left: 'VOWS STUDIO', showcase_caption_right: '2026',
  showcase_hover_effect: 'none',
  split_ratio: '1fr 1fr', gap: 52, min_height: 0,
  tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
})])])]));

// 2) COUNTDOWN — "Next on our calendar: A & J, married at Villa Lume"
home.push(sec(BG2, 'large', [row([col('1-1', [
  tile('section-header', {
    eyebrow_show: true, eyebrow_text: 'Next on our calendar',
    eyebrow_color: GOLD, eyebrow_dot_color: GOLD, eyebrow_separator: '',
    headline_lines: [
      { text: 'A & J, married at', color: CREAM, italic: false },
      { text: 'Villa Lume', color: GOLD, italic: true },
    ],
    headline_font_family: 'serif', headline_font_size: 48, headline_font_weight: '500',
    headline_align: 'center', headline_inline: true,
    tagline_show: true,
    tagline_text: '19 September 2026 · Lake Garda',
    tagline_text_italic: false, tagline_text_color: DIM, tagline_text_size: 13,
    layout: 'center', gap: 12,
  }),
  tile('countdown', {
    countdown_type: 'date',
    target_date: '2026-09-19T15:00',
    show_days: true, show_hours: true, show_minutes: true, show_seconds: true,
    label_days: 'Days', label_hours: 'Hours', label_minutes: 'Minutes', label_seconds: 'Seconds',
    separator: '',
    display_mode: 'inline',
    accent_color: GOLD,
    text_color: DIM,
    separator_color: 'transparent',
    number_font_size: '72', number_font_weight: '500',
    label_font_size: '11', label_font_weight: '500',
    item_min_width: '90',
    item_bg_color: 'transparent',
    item_radius: '0', item_padding: '0',
    bg_color: 'transparent',
    tile_padding: { top: 24, right: 0, bottom: 0, left: 0 },
    expire_action: 'none',
  }),
])])]));

// 3) SERVICES — "Three ways to say yes" — process-steps ×3 con card (bg/border/radius/padding)
//    Blueprint: .vw-svc = bg:--panel + border:1px --line + radius:14px + padding:32px
//    Numeri romani italic gold, h3, p dim, prezzo "from €X,000" in desc
home.push(sec(BG, 'large', [
  row([col('1-1', [shead('How we help', 'Three ways to ', 'say yes')])]),
  row([col('1-1', [tile('process-steps', {
    columns: 3, gap: 16, align: 'center', auto_number: false, item_gap: 10,
    number_style: 'plain', number_color: GOLD, number_size: 20, number_font: 'serif', number_weight: '500',
    title_color: CREAM, title_size: 25, title_font: 'serif', title_weight: '500',
    desc_color: DIM, desc_size: 14,
    card_bg: PANEL, card_border: `1px solid ${LINE}`, card_radius: R(14), card_padding: 32,
    items: [
      {
        number: 'i.',
        title: 'Full planning',
        description: 'From the first idea to the last dance — venue, design, suppliers and the day itself, all handled.\n\nfrom €9,000',
      },
      {
        number: 'ii.',
        title: 'Partial planning',
        description: `You've made a start; we bring the structure, the contacts and the calm to carry it home.\n\nfrom €4,500`,
      },
      {
        number: 'iii.',
        title: 'On-the-day',
        description: 'You plan it, we run it — so you and your family are guests, not coordinators.\n\nfrom €1,800',
      },
    ],
  })])]),
]));

// 4) "Real Weddings" gallery — IMAGE-FREE: section-header + info-cards con nomi/luoghi (astratto)
home.push(sec(BG2, 'large', [
  row([col('1-1', [shead(`A few we've loved`, 'Recently ', 'celebrated')])]),
  row([col('1-1', [tile('info-cards', {
    container_bg: { type: 'solid', color: 'transparent' },
    container_padding: 0, container_gap: 16, columns: 3, items_gap: 16,
    card_bg: { type: 'solid', color: PANEL }, card_color: DIM,
    card_radius: R(12), card_padding: 28,
    show_icon: false, show_counter: true, show_counter_label: true,
    show_arrow: false, show_footer: true, show_media: false,
    counter_shape: 'plain', counter_color: GOLD,
    counter_size: 40, counter_bg: 'transparent',
    title_color: CREAM,
    title_font_family: 'serif', title_size: 22, title_weight: '500', title_italic: true,
    description_size: 14,
    card_hover_effect: 'lift',
    items: [
      {
        counter: 'i.',
        counter_label: '2025',
        title: 'Amara & James',
        description: 'Villa Lume, Lake Garda · Full planning · 110 guests · summer ceremony at golden hour.',
        footer_text: 'Full planning · Jun 2025',
        footer_dot_color: GOLD,
      },
      {
        counter: 'ii.',
        counter_label: '2025',
        title: 'Elena & Marco',
        description: 'Masseria Tre Torri, Puglia · Partial planning · 80 guests · candlelit courtyard reception.',
        footer_text: 'Partial · Sep 2025',
        footer_dot_color: GOLD,
      },
      {
        counter: 'iii.',
        counter_label: '2024',
        title: 'Charlotte & Luca',
        description: 'Chateau de Varennes, Burgundy · Full planning · 140 guests · vineyard dinner under the stars.',
        footer_text: 'Full planning · Oct 2024',
        footer_dot_color: GOLD,
      },
      {
        counter: 'iv.',
        counter_label: '2024',
        title: 'Sofia & Andrei',
        description: 'Palazzo Venezia, Rome · On-the-day · 60 guests · intimate civil ceremony and garden aperitivo.',
        footer_text: 'On-the-day · May 2024',
        footer_dot_color: GOLD,
      },
      {
        counter: 'v.',
        counter_label: '2024',
        title: 'Nadia & Pierre',
        description: 'Domaine des Pins, Provence · Full planning · 200 guests · lavender-field dinner and fireworks.',
        footer_text: 'Full planning · Jul 2024',
        footer_dot_color: GOLD,
      },
      {
        counter: 'vi.',
        counter_label: '2023',
        title: 'Giulia & Tom',
        description: 'Villa Balbianello, Lake Como · Partial planning · 75 guests · lakeside ceremony at sunset.',
        footer_text: 'Partial · Sep 2023',
        footer_dot_color: GOLD,
      },
    ],
  })])]),
]));

// 5) TESTIMONIAL — Amara & James
home.push(sec(BG, 'large', [row([col('1-1', [tile('testimonial', {
  quote: `"From the first call, it felt like handing the worry to a friend with impeccable taste. Our day was perfect, and we were actually present for it."`,
  author_name: 'Amara & James',
  author_role: 'married 2025',
  rating: '0',
  layout: 'single',
  show_line: false,
  bg_color: 'transparent',
  text_color: CREAM,
  border_radius: '0',
  avatar: '',
})])])]));

// 6) ENQUIRE — layout 2 colonne: section-header sinistra + form vero destra
//    Blueprint: form con 5 campi (names/date/guests/service/email) + submit "Check our availability"
//    Container form: bg:--panel + border:1px --line + border-radius:16px + padding:32px
home.push(sec(BG2, 'large', [
  row([
    col('1-2', [tile('section-header', {
      eyebrow_show: true, eyebrow_text: 'Enquire', eyebrow_color: GOLD, eyebrow_dot_color: GOLD, eyebrow_separator: '',
      headline_lines: [
        { text: 'Is your date', color: CREAM, italic: false },
        { text: 'free?', color: GOLD, italic: true },
      ],
      headline_font_family: 'serif', headline_font_size: 52, headline_font_weight: '500',
      headline_align: 'left', headline_inline: true,
      tagline_show: true,
      tagline_text: `We take a limited number of weddings each year so every couple gets our full attention. Tell us a little and we'll be in touch within two days.`,
      tagline_text_italic: false, tagline_text_color: DIM, tagline_text_size: 16,
      layout: 'stack', gap: 20,
    })]),
    col('1-2', [tile('form', {
      fields: [
        { id: 'f-1', field_type: 'text',  label: 'Your names',  placeholder: 'You & yours',       name: 'names',   required: true,  width: '1-2', options: '', icon: '' },
        { id: 'f-2', field_type: 'date',  label: 'Wedding date', placeholder: '',                 name: 'date',    required: false, width: '1-2', options: '', icon: '' },
        { id: 'f-3', field_type: 'select', label: 'Guests',      placeholder: '',                 name: 'guests',  required: false, width: '1-2',
          options: 'Under 50\n50–120\n120–200\n200+', icon: '' },
        { id: 'f-4', field_type: 'select', label: 'Service',     placeholder: '',                 name: 'service', required: false, width: '1-2',
          options: 'Full planning\nPartial\nOn-the-day', icon: '' },
        { id: 'f-5', field_type: 'email', label: 'Email',        placeholder: 'you@example.com',  name: 'email',   required: true,  width: '1-1', options: '', icon: 'mail' },
      ],
      email_to: 'hello@vows.studio',
      email_subject: 'New wedding enquiry',
      success_message: `Thank you! We'll be in touch within two days.`,
      submit_text: 'Check our availability',
      submit_alignment: 'center',
      submit_full_width: true,
      form_layout: 'stacked',
      // stile campi — taupe dark
      input_bg: BG,
      input_color: CREAM,
      input_border_color: LINE2,
      input_border_width: '1',
      input_border_style: 'box',
      input_radius: '8',
      input_focus_border: GOLD,
      input_focus_shadow: false,
      input_size: 'default',
      label_color: DIM,
      label_size: '11',
      label_weight: '500',
      label_transform: 'uppercase',
      label_letter_spacing: '0.12em',
      gap: '18',
      // stile pulsante
      submit_bg: GOLD,
      submit_color: INK,
      submit_hover_bg: GOLDL,
      submit_radius: '999',
      submit_font_size: '12',
      submit_font_weight: '600',
      submit_letter_spacing: '0.14',
      submit_text_transform: 'uppercase',
      // contenitore form
      bg: { type: 'solid', color: PANEL },
      tile_padding: { top: 32, right: 32, bottom: 32, left: 32 },
    })]),
  ], { gap: 56 }),
]));

K.emit({
  slug: 'vows',
  name: 'Vows',
  tags: ['wedding', 'events', 'planning', 'luxury'],
  description: `Vows — Wedding planning & design studio. Taupe + gold, Frank Ruhl Libre (display) + Mulish. Countdown nativo, services process-steps card, gallery tile-pure, form RSVP. Riproduzione fedele dell'OLOtheme Vows.`,
  colors: {
    primary: GOLD,
    primary_contrast: INK,
    secondary: CREAM,
    secondary_contrast: INK,
    muted: BG2,
    muted_contrast: TXT,
    text: TXT,
    text_muted: DIM,
    background: BG,
    border: LINE,
    link: GOLD,
  },
  css_disp: `"Frank Ruhl Libre", Georgia, serif`,
  css_sans: `"Mulish", -apple-system, sans-serif`,
  heading_weight: '500',
  heading_line_height: '1.1',
  google_fonts: ['Frank Ruhl Libre', 'Mulish'],
  logo_variant: 'light',
  menu: [
    { title: 'Services', url: '#services' },
    { title: 'Real weddings', url: '#weddings' },
    { title: 'About', url: '#about' },
    { title: 'Enquire', url: '#enquire' },
  ],
  header: {
    bg: 'rgba(42,38,32,.86)',
    text_color: TXT,
    sticky_bg: 'rgba(42,38,32,.92)',
    logo_width: 130,
  },
  footer: {
    bg: BG2,
    headColor: CREAM,
    brand: {
      name: 'Vows',
      tagline: 'Wedding planning & design studio. Beautiful days, calmly run, unmistakably yours.',
    },
    columns: [
      { title: 'Studio', links: ['Services', 'Real weddings', 'About'] },
      { title: 'Plan', links: ['Enquire', 'Guide & pricing', 'FAQ'] },
      { title: 'Contact', links: ['hello@vows.studio', 'Worldwide · based in Milan', '@vows.studio'] },
    ],
    bottom: {
      left: '© 2026 Vows — an OLOtheme demo.',
      right: 'Built with OLObuild',
    },
  },
  cursor: {
    blend_mode: 'normal',
    ring_color: GOLD,
    dot_color: GOLD,
  },
}, home);
