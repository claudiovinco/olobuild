/* Aurora — ricomposizione TILE-PURE (image-free). Events studio · Plum + Blush + Gold.
   Italiana (display) + Tenor Sans (body). */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('au');

// Palette dal :root del CSS aurora.css
const BG     = '#241430';
const BG2    = '#2a1838';
const PANEL  = '#33203f';
const PANEL2 = '#3d2a4a';
const INK    = '#170c1f';
const BLUSH  = '#e0afca';
const BLUSHD = '#cf94b4';
const GOLD   = '#e6c98f';
const CREAM  = '#f3e9ef';
const TXT    = '#c8b3c6';
const DIM    = '#94809a';
const LINE   = 'rgba(243,233,239,.14)';
const LINE2  = 'rgba(224,175,202,.42)';
const WHITE  = '#ffffff';

const home = [];

// helper: section-header centrato con eyebrow + headline biriga accentata
const shead = (eyebrow, l1, accent, intro) => tile('section-header', {
  eyebrow_show: true, eyebrow_text: eyebrow, eyebrow_color: BLUSH, eyebrow_dot_color: BLUSH, eyebrow_separator: '',
  headline_lines: [
    { text: l1, color: CREAM, italic: false },
    { text: accent, color: BLUSH, italic: true },
  ],
  headline_font_family: 'serif', headline_font_size: 52, headline_font_weight: '400', headline_align: 'center', headline_inline: true,
  tagline_show: !!intro, tagline_text: intro || '', tagline_text_italic: false, tagline_text_color: DIM, tagline_text_size: 16.5,
  layout: 'center', gap: 16,
});

// 1) HERO centrato — layout centrato come il blueprint (au-hero: text-align:center)
// Blueprint: eyebrow + h1 centrato + p + 2 CTA + strip di 3 media sotto
home.push(sec(BG, 'large', [
  row([
    col('1-1', [
      tile('hero', {
        title: `Celebrations worth remembering.`,
        subtitle: `We design and produce weddings, galas and private events — the kind your guests talk about for years. From the first spark of an idea to the last dance.`,
        text_color: TXT,
        title_color: CREAM,
        title_font_family: `"Italiana", Didot, serif`,
        title_font_size: '80',
        title_font_weight: '400',
        title_line_height: '1.02',
        title_letter_spacing: '0.01',
        title_text_transform: 'none',
        title_tag: 'h1',
        subtitle_color: TXT,
        subtitle_font_size: '18',
        subtitle_font_weight: '400',
        subtitle_max_width: '520',
        text_align: 'center',
        horizontal_align: 'center',
        vertical_align: 'center',
        content_max_width: '880',
        min_height: '0',
        cta_text: `Start planning`,
        cta_url: '#enquire',
        cta_bg_color: BLUSH,
        cta_text_color: INK,
        cta_size: '13',
        cta_radius: R(999),
        cta_style: 'filled',
        cta2_text: `See our work`,
        cta2_url: '#gallery',
        cta2_bg_color: 'transparent',
        cta2_text_color: CREAM,
        cta2_style: 'outline',
        tile_padding: { top: 64, right: 20, bottom: 48, left: 20 },
        bg: { type: 'none' },
        preset: 'custom',
      }),
    ]),
  ]),
  // au-hero__strip: 3 pannelli portrait (aspect 3/4) — image-free: info-cards come strip
  row([
    col('1-1', [
      tile('info-cards', {
        container_bg: { type: 'solid', color: 'transparent' },
        container_padding: 0, container_gap: 14, columns: 3, items_gap: 14,
        card_bg: { type: 'solid', color: PANEL },
        card_color: BLUSH,
        card_radius: R(14),
        card_padding: 0,
        show_icon: false, show_counter: false, show_arrow: false, show_footer: false, show_media: false,
        title_color: CREAM, title_font_family: 'sans-serif', title_size: 11, title_weight: '500',
        title_italic: false,
        description_size: 10,
        card_hover_effect: 'none',
        // aspect-ratio 3/4 — altezza fissa simulata tramite padding bottom
        items: [
          { title: 'Tablescape · Candlelight', description: '' },
          { title: 'Ballroom · Florals',         description: '' },
          { title: 'Couple · Golden hour',        description: '' },
        ],
      }),
    ]),
  ], { gap: 0 }),
]));

// 2) EVENT TYPES — CategoryTiles non esiste: info-cards con icona (4 card plum)
// Blueprint: 4 .au-type cards con bg panel, gradient overlay, titolo + sottotitolo
home.push(sec(BG, 'large', [
  row([col('1-1', [
    shead('What we do', 'Every kind of ', 'occasion'),
  ])]),
  row([col('1-1', [
    tile('info-cards', {
      container_bg: { type: 'solid', color: 'transparent' }, container_padding: 0, container_gap: 16, columns: 4, items_gap: 16,
      card_bg: { type: 'solid', color: PANEL }, card_color: DIM, card_radius: R(14), card_padding: 28,
      show_icon: true, show_counter: false, show_arrow: false, show_footer: false, show_media: false,
      icon_color: BLUSH, icon_bg_color: 'rgba(224,175,202,.12)',
      title_color: CREAM, title_font_family: 'serif', title_size: 22, title_weight: '400', title_italic: false,
      description_size: 12.5,
      card_hover_effect: 'lift',
      items: [
        { icon: 'heart',    title: 'Weddings',  description: 'The whole day' },
        { icon: 'star',     title: 'Galas',     description: `Fundraisers & awards` },
        { icon: 'users',    title: 'Corporate', description: `Launches & summits` },
        { icon: 'sparkles', title: 'Private',   description: `Milestones & soirées` },
      ],
    }),
  ])]),
]));

// 3) COUNTDOWN — tile 'countdown' (data-driven: target_date, etichette EN, stile plum)
// Blueprint: .au-count{panel}, numeri Italiana BLUSH, etichette DIM
home.push(sec(BG2, 'large', [
  row([col('1-1', [
    tile('section-header', {
      eyebrow_show: true, eyebrow_text: 'Next showcase', eyebrow_color: BLUSH, eyebrow_dot_color: BLUSH, eyebrow_separator: '',
      headline_lines: [
        { text: 'The Midsummer ', color: CREAM, italic: false },
        { text: 'Gala', color: BLUSH, italic: true },
      ],
      headline_font_family: 'serif', headline_font_size: 48, headline_font_weight: '400', headline_align: 'center', headline_inline: true,
      tagline_show: true, tagline_text: '21 June 2026 · Villa Speranza, Lake Como', tagline_text_italic: false, tagline_text_color: DIM, tagline_text_size: 13,
      layout: 'center', gap: 12,
    }),
  ])]),
  row([col('1-1', [
    tile('countdown', {
      countdown_type: 'date',
      target_date: '2026-06-21T20:00',
      show_days: true, show_hours: true, show_minutes: true, show_seconds: true,
      label_days: 'Days', label_hours: 'Hours', label_minutes: 'Minutes', label_seconds: 'Seconds',
      separator: ':',
      display_mode: 'block',
      expire_action: 'message',
      expired_message: `L'evento è iniziato!`,
      // stile: numeri blush (.au-count__u b = var(--blush)), etichette dim
      accent_color: BLUSH,
      text_color: DIM,
      separator_color: LINE2,
      number_font_size: '72',
      number_font_weight: '400',
      label_font_size: '11',
      label_font_weight: '500',
      separator_font_size: '56',
      item_min_width: '100',
      item_bg_color: 'transparent',
      item_radius: '0',
      item_padding: '20',
      tile_padding: { top: 16, right: 0, bottom: 16, left: 0 },
      bg: { type: 'none' },
      preset: 'custom',
      countdown_style: 'custom',
    }),
  ])]),
]));

// 4) FEATURED EVENT — hero-split con copy evento (image-free: showcase = pannello plum con dettagli)
// Blueprint: .au-feat (grid 2 col) — media sinistra, copy destra
home.push(sec(BG, 'large', [
  row([col('1-1', [
    tile('hero-split', {
      eyebrow_text: 'Featured', eyebrow_dot_color: BLUSH, eyebrow_color: BLUSH,
      headline_lines: [
        { text: 'Elena & Marco,', color: CREAM, italic: false },
        { text: 'on the water', color: BLUSH, italic: true },
      ],
      headline_font_family: 'serif', headline_font_size: 52, headline_line_height: 1.08, headline_font_weight: '400', headline_align: 'left',
      subhead: `Two hundred guests, one Venetian palazzo, and a sunset that refused to be upstaged. A weekend celebration built around long tables, candlelight and a string quartet on a boat.`,
      subhead_color: TXT, subhead_size: 16, subhead_italic: false, subhead_max_width: 480,
      cta1_text: 'See the full event', cta1_url: '#showcase', cta1_bg: 'transparent', cta1_color: CREAM, cta1_size: 12, cta1_radius: R(0), cta1_radius_hover: R(0),
      stats: [],
      showcase_enabled: true,
      showcase_bg: { type: 'solid', color: PANEL2 },
      showcase_padding: 32, showcase_radius: R(14), showcase_radius_hover: R(14),
      showcase_badge_text: 'LAKE COMO · 2025',
      showcase_badge_dot: GOLD, showcase_badge_bg: INK, showcase_badge_color: GOLD,
      showcase_items: [
        { number: 'Location', text: 'Venetian palazzo',          italic: false, text_color: CREAM, bg: { type: 'solid', color: BG2 } },
        { number: 'Guests',   text: '200+',                      italic: false, text_color: BLUSH, bg: { type: 'solid', color: BG2 } },
        { number: 'Highlight',text: 'String quartet on a boat',  italic: true,  text_color: CREAM, bg: { type: 'solid', color: BG2 } },
        { number: 'Season',   text: 'Golden hour · sunset',      italic: false, text_color: CREAM, bg: { type: 'solid', color: BG2 } },
      ],
      showcase_card_radius: R(10), showcase_card_radius_hover: R(10), showcase_card_shadow: 'none',
      showcase_caption_left: 'WEDDING', showcase_caption_right: 'FULL WEEKEND',
      showcase_hover_effect: 'none',
      split_ratio: '.85fr 1.15fr', gap: 56, min_height: 0,
      tile_padding: { top: 0, right: 0, bottom: 0, left: 0 },
    }),
  ])]),
]));

// 5) GALLERY — ProductGallery non esiste; overlaygrid (pannelli colorati, image-free best-effort)
// Blueprint: masonry grid .au-gal (4 col, 6 pannelli con tall/wide)
// SEGNALATO: sezione gallery image-free — overlaygrid senza immagini mostra solo sfondo panel
home.push(sec(BG2, 'large', [
  row([col('1-1', [
    shead('The gallery', `Moments we’ve `, 'made'),
  ])]),
  row([col('1-1', [
    tile('overlaygrid', {
      preset: 'editorial-grid',
      bg: { type: 'none' },
      columns: '4',
      columns_mobile: '2',
      gap: 'small',
      height: '180',
      match_height: true,
      overlay_position: 'bottom',
      overlay_style: 'overlay-primary',
      overlay_color: `rgba(36,20,48,.7)`,
      overlay_gradient: true,
      overlay_padding: 'small',
      title_size: 'h4',
      title_color: CREAM,
      title_weight: '400',
      title_letter_spacing: 0,
      title_uppercase: false,
      subtitle_color: BLUSH,
      subtitle_size: 11,
      hover_effect: 'zoom',
      hover_overlay: 'always',
      item_radius: 10,
      show_cta: false,
      items: [
        { image: '', title: 'Ceremony aisle',     subtitle: 'Florals & candlelight' },
        { image: '', title: 'Reception table',    subtitle: 'Long tables & linen' },
        { image: '', title: 'Place setting',      subtitle: 'Every card, every stem' },
        { image: '', title: 'First dance',        subtitle: 'The room holds its breath' },
        { image: '', title: 'Champagne tower',    subtitle: 'Every gala deserves one' },
        { image: '', title: 'Golden hour',        subtitle: 'The light that makes it right' },
      ],
    }),
  ])]),
]));

// 6) PROCESS STEPS — tile 'process-steps' con number_style:'outline' (cerchio bordato)
// Blueprint: .au-step__n { border:1px solid var(--line-2); border-radius:50% }
// Migrato da info-cards numerati → process-steps (tile dedicato)
home.push(sec(BG, 'large', [
  row([col('1-1', [
    shead('How we work', 'From idea to ', 'last dance'),
  ])]),
  row([col('1-1', [
    tile('process-steps', {
      columns: 3, gap: 20, align: 'center', auto_number: false, item_gap: 10,
      number_style: 'outline',
      number_color: BLUSH,
      number_bg: LINE2,
      number_size: 26,
      number_font: 'serif',
      number_weight: '400',
      title_color: CREAM,
      title_size: 23,
      title_font: 'serif',
      title_weight: '400',
      desc_color: DIM,
      desc_size: 14.5,
      card_bg: '',
      card_border: '',
      card_padding: 0,
      items: [
        { number: 'I',   title: 'Dream',   description: `We start with a long conversation and a blank page — your story, your guests, the feeling you want in the room.` },
        { number: 'II',  title: 'Design',  description: `Concept, mood, floor plan and a budget that’s honest. You approve every detail before a thing is booked.` },
        { number: 'III', title: 'Deliver', description: `On the day, our team runs everything so you don’t lift a finger — just arrive, and be a guest at your own party.` },
      ],
    }),
  ])]),
]));

// 7) TESTIMONIAL
// Blueprint: .au-testi (serif italic, testo grande, autore uppercase blush)
home.push(sec(BG2, 'large', [
  row([col('1-1', [
    tile('testimonial', {
      quote: `“Aurora didn’t just plan our wedding — they understood it. Every guest told us it was the most beautiful day they’d ever been to. So did we.”`,
      author_name: 'Elena & Marco', author_role: 'Lake Como, 2025',
      rating: '0', layout: 'single', show_line: false,
      bg_color: 'transparent', text_color: CREAM, border_radius: '0', avatar: '',
    }),
  ])]),
]));

// 8) ENQUIRE — form reale (RSVPForm → tile 'form')
// Blueprint: .au-enq (grid 2 col) — copy sinistra, <form> destra con panel
// Migrato: info-cards contatti → tile 'form' con campi dell'enquiry
home.push(sec(BG, 'large', [
  row([
    col('1-2', [
      tile('section-header', {
        eyebrow_show: true, eyebrow_text: 'Enquire', eyebrow_color: BLUSH, eyebrow_dot_color: BLUSH, eyebrow_separator: '',
        headline_lines: [
          { text: `Let’s plan`, color: CREAM, italic: false },
          { text: 'something beautiful', color: BLUSH, italic: true },
        ],
        headline_font_family: 'serif', headline_font_size: 48, headline_font_weight: '400', headline_align: 'left', headline_inline: false,
        tagline_show: true,
        tagline_text: `Tell us a little about your celebration and we’ll be in touch within two days to arrange a first conversation.`,
        tagline_text_italic: false, tagline_text_color: DIM, tagline_text_size: 16,
        layout: 'stack', gap: 16,
      }),
    ]),
    col('1-2', [
      tile('form', {
        preset: 'custom',
        // Campi: tipo evento, data appross., n. ospiti, nome, email + invio
        fields: [
          { id: 'au-f1', field_type: 'select', label: 'Event type', name: 'event_type', required: true, width: '1-2', placeholder: '',
            options: 'Wedding\nGala\nCorporate\nPrivate party', icon: '' },
          { id: 'au-f2', field_type: 'date', label: 'Approx. date', name: 'event_date', required: false, width: '1-2', placeholder: '' },
          { id: 'au-f3', field_type: 'select', label: 'Guests', name: 'guests', required: false, width: '1-2', placeholder: '',
            options: 'Under 50\n50-150\n150-300\n300+', icon: '' },
          { id: 'au-f4', field_type: 'text',  label: 'Your name', name: 'name',       required: true,  width: '1-2', placeholder: 'Full name', icon: 'user' },
          { id: 'au-f5', field_type: 'email', label: 'Email',     name: 'email',      required: true,  width: '1-1', placeholder: 'you@example.com', icon: 'mail' },
        ],
        submit_text: 'Send enquiry',
        submit_full_width: true,
        submit_alignment: 'left',
        submit_bg: BLUSH,
        submit_color: INK,
        submit_radius: '999',
        submit_font_size: '13',
        submit_font_weight: '600',
        submit_letter_spacing: '0.14',
        submit_text_transform: 'uppercase',
        submit_hover_bg: BLUSHD,
        email_subject: 'New enquiry from Aurora website',
        success_message: `Thank you! We’ll be in touch within two days.`,
        input_bg: BG,
        input_color: CREAM,
        input_border_color: LINE2,
        input_border_width: '1',
        input_border_style: 'box',
        input_radius: { tl: 8, tr: 8, br: 8, bl: 8, linked: true },
        input_size: 'default',
        input_focus_border: BLUSH,
        input_focus_shadow: false,
        label_color: DIM,
        label_size: '11',
        label_weight: '500',
        label_transform: 'uppercase',
        label_letter_spacing: '0.12',
        gap: '18',
        form_layout: 'stacked',
        honeypot: true,
        tile_padding: { top: 32, right: 32, bottom: 32, left: 32 },
        bg: { type: 'solid', color: PANEL },
      }),
    ]),
  ], { gap: 56, vertical_align: 'center' }),
]));

// 9) CTA FINALE — un solo pulsante (blueprint: solo .btn--blush)
home.push(sec(BG2, 'large', [
  row([col('1-1', [
    tile('cta-banner', {
      headline: `Every celebration`, headline_accent: 'deserves to be remembered.', headline_accent_italic: true,
      subtitle: `From the first conversation to the last dance — our studio handles every detail. Let’s create something your guests will never forget.`,
      cta_text: 'Start planning', cta_url: '#enquire',
      bg: { type: 'solid', color: '#2e1840' }, text_color: CREAM, accent_color: BLUSH, subtitle_color: TXT,
      cta_bg: BLUSH, cta_color: INK, cta_radius: R(999), cta_size: 13,
      headline_font_family: 'serif', headline_size: 46, headline_weight: '400', subtitle_size: 16,
      layout: 'stack', vertical_align: 'center', banner_radius: R(20), banner_padding: 80,
    }),
  ])]),
]));

K.emit({
  slug: 'aurora', name: 'Aurora',
  tags: ['events', 'wedding', 'gala', 'celebrations', 'luxury'],
  description: 'Aurora — full-service events studio. Weddings, galas and private celebrations. Plum + blush + gold palette. Italiana (display) + Tenor Sans (body). Countdown evento, featured event split, process steps, form enquiry, testimonial.',
  colors: {
    primary: BLUSH, primary_contrast: INK,
    secondary: GOLD, secondary_contrast: INK,
    muted: BG2, muted_contrast: TXT,
    text: TXT, text_muted: DIM,
    background: BG, border: LINE, link: BLUSH,
  },
  css_disp: `"Italiana", Didot, serif`,
  css_sans: `"Tenor Sans", -apple-system, sans-serif`,
  heading_weight: '400', heading_line_height: '1.08',
  google_fonts: ['Italiana', 'Tenor Sans'],
  logo_variant: 'light',
  menu: [
    { title: 'What we do', url: '#services' },
    { title: 'Gallery',    url: '#gallery'  },
    { title: 'Process',    url: '#process'  },
    { title: 'Showcase',   url: '#showcase' },
  ],
  header: { bg: 'rgba(36,20,48,.84)', text_color: TXT, sticky_bg: 'rgba(36,20,48,.92)', logo_width: 140 },
  footer: {
    bg: BG2, headColor: CREAM,
    brand: { name: 'Aurora', tagline: 'A full-service events studio. Weddings, galas and private celebrations since 2011.' },
    columns: [
      { title: 'Studio',    links: ['What we do', 'Process', 'Gallery', 'Showcase'] },
      { title: 'Occasions', links: ['Weddings', 'Galas', 'Corporate', 'Private'] },
      { title: 'Contact',   links: ['hello@aurora.events', '+39 02 8841', 'Milan · Como · worldwide'] },
    ],
    bottom: { left: '© 2026 Aurora — an OLOtheme demo.', right: 'Built with OLObuild' },
  },
  cursor: { blend_mode: 'exclusion', ring_color: BLUSH, dot_color: BLUSH },
}, home);
