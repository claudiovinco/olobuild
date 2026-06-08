/* Vela — ricomposizione TILE-PURE (image-free). Creative design studio.
   Syne (display) + Work Sans (body). Palette near-black + amber. */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('vl');

// ─── PALETTE (da :root vela.css) ──────────────────────────────────────────────
const BG    = '#0a0a0c';
const BG2   = '#121216';
const BG3   = '#1a1a20';
const AMBER = '#f4a23b';
const AMBERD= '#e08e26';
const CREAM = '#f3efe8';
const TXT   = '#ece9e3';
const DIM   = '#9b988f';
const FAINT = '#6a6862';
const LINE  = 'rgba(255,255,255,.1)';
const WHITE = '#ffffff';
const INK   = '#0a0a0c';

// border top amber per le stat (.aw{border-top:2px solid var(--amber)})
const BORDER_TOP_AMBER = { top:2, right:0, bottom:0, left:0, linked:false, style:'solid', color:AMBER };

const home = [];

// ─── 1) HERO (hero-split, solo testo — image-free) ────────────────────────────
// CSS: h1{font-size:clamp(54px,12vw,180px); line-height:.86; text-transform:uppercase}
// .vl-hero h1 .o{-webkit-text-stroke:2px var(--txt); color:transparent} — outline text (best-effort: reso come TXT)
// .vl-hero h1 .a{color:var(--amber)}
// eyebrow: 3 span con dot amber ciascuno
// subhead max-width:420px font-size:17px
home.push(sec(BG,'large',[ row([ col('1-1',[ tile('hero-split',{
  eyebrow_text:'Independent studio · Est. 2015 · Milan / everywhere',
  eyebrow_dot_color:AMBER, eyebrow_color:DIM,
  headline_lines:[
    {text:'DESIGN WITH',   color:TXT,   italic:false},
    {text:'A POINT',       color:AMBER, italic:false},
    {text:'OF VIEW.',      color:TXT,   italic:false},
  ],
  headline_font_family:`"Syne", -apple-system, sans-serif`,
  headline_font_size:80, headline_line_height:0.86,
  headline_font_weight:'800', headline_align:'left',
  subhead:`We build brands, identities and digital products for people who'd rather stand out than blend in.`,
  subhead_color:DIM, subhead_size:17, subhead_italic:false, subhead_max_width:420,
  cta1_text:`Let's talk`, cta1_url:'#contact',
  cta1_bg:AMBER, cta1_color:INK, cta1_size:14, cta1_radius:R(999), cta1_radius_hover:R(999),
  cta2_text:'See the work', cta2_url:'#work',
  cta2_bg:'transparent', cta2_color:TXT, cta2_border:LINE, cta2_size:14,
  cta2_radius:R(999), cta2_radius_hover:R(999),
  stats:[],
  showcase_enabled:false,
  split_ratio:'1fr 0fr', gap:0, min_height:0,
  tile_padding:{top:80,right:0,bottom:80,left:0},
}) ]) ]) ]));

// ─── 2) MARQUEE ──────────────────────────────────────────────────────────────
// CSS: .vl-marq__t span{font-family:var(--disp); font-size:26px; font-weight:700; text-transform:uppercase}
// border-block:1px solid var(--line); padding:22px 0
// separatore ✦ con colore amber
home.push(sec(BG,'none',[ row([ col('1-1',[ tile('marquee',{
  content_type:'text',
  text_items:'Branding · Identity · Digital · Art Direction · Motion · Strategy',
  separator:' ✦ ',
  speed:'28', direction:'left', pause_hover:true, gap:'26',
  bg_color:'transparent',
  text_color:TXT,
  font_size:'26', font_weight:'700', letter_spacing:'0',
  text_transform:'uppercase',
  height:'70',
  full_width:true,
  border_top:'1', border_bottom:'1', border_color:LINE,
  shadow:'none',
}) ]) ]) ]));

// ─── 3) SELECTED WORK (WorkGrid non esiste → info-cards, project cards astratte) ─
// SEGNALATO BEST-EFFORT: WorkGrid ha card full-width (big) + griglia 2 col con aspect-ratio,
// image zoom su hover, tag pill, arrow amber, footer nome+anno. NON riproducibile con info-cards.
// info-cards con counter=anno, counter_label=categoria, title=nome progetto.
home.push(sec(BG2,'large',[
  row([ col('1-1',[ tile('section-header',{
    eyebrow_show:true, eyebrow_text:'Selected work',
    eyebrow_color:AMBER, eyebrow_dot_color:AMBER, eyebrow_separator:'',
    headline_lines:[
      {text:'Recent',   color:TXT, italic:false},
      {text:'projects', color:TXT, italic:false},
    ],
    headline_font_family:`"Syne", -apple-system, sans-serif`,
    headline_font_size:56, headline_font_weight:'800', headline_align:'left',
    headline_inline:false,
    tagline_show:false, layout:'stack', gap:10,
  }) ]) ]),
  row([ col('1-1',[ tile('info-cards',{
    container_bg:{type:'solid',color:'transparent'}, container_padding:0,
    container_gap:24, columns:3, items_gap:24,
    card_bg:{type:'solid',color:BG3}, card_color:FAINT,
    card_radius:R(14), card_padding:28,
    show_icon:false, show_counter:true, show_counter_label:true,
    show_arrow:true, show_footer:true, show_media:false,
    counter_shape:'plain', counter_color:FAINT,
    counter_size:13, title_color:TXT,
    title_font_family:`"Syne", -apple-system, sans-serif`,
    title_size:22, title_weight:'700', title_italic:false, description_size:14,
    arrow_color:AMBER, footer_size:12,
    items:[
      {counter:'2025', counter_label:'Branding · Campaign',
       title:'Helio — solar brand',
       description:'Full-bleed brand campaign and identity system for a solar energy challenger rewriting the category.',
       footer_text:'View project →', footer_dot_color:AMBER},
      {counter:'2025', counter_label:'Identity',
       title:`Møya`,
       description:'Minimal Scandinavian identity for a contemporary womenswear label. Wordmark, colour and materials.',
       footer_text:'View project →', footer_dot_color:AMBER},
      {counter:'2024', counter_label:'Digital · App',
       title:'Pace fitness',
       description:'Product design and full-stack delivery for a fitness tracking app with 80k users at launch.',
       footer_text:'View project →', footer_dot_color:AMBER},
      {counter:'2024', counter_label:'Packaging',
       title:'Forma coffee',
       description:'Structural packaging and print system for a specialty roaster entering the premium grocery market.',
       footer_text:'View project →', footer_dot_color:AMBER},
      {counter:'2023', counter_label:'Art Direction',
       title:'Atlas journal',
       description:'Editorial art direction and digital edition for an independent travel journal now distributed in 24 countries.',
       footer_text:'View project →', footer_dot_color:AMBER},
    ],
    card_hover_effect:'lift',
  }) ]) ]),
]));

// ─── 4) MANIFESTO (blendtext) ────────────────────────────────────────────────
// CSS: .vl-mani{border-block:1px solid var(--line)}
// .vl-mani__in{grid-template-columns:.4fr 1fr}
// .vl-mani__lab{font-size:12px; letter-spacing:.12em; text-transform:uppercase; color:var(--txt-dim)}
// .vl-mani p{font-family:var(--disp); font-weight:600; font-size:clamp(24px,3.4vw,44px); line-height:1.18}
// "strong opinions, held loosely." = span.a amber
home.push(sec(BG,'large',[ row([
  col('1-3',[ tile('section-header',{
    eyebrow_show:false,
    headline_lines:[{text:'The studio',color:FAINT,italic:false}],
    headline_font_family:`"Work Sans", -apple-system, sans-serif`,
    headline_font_size:11, headline_font_weight:'600', headline_align:'left',
    headline_inline:false, tagline_show:false, layout:'stack', gap:0,
  }) ]),
  col('2-3',[ tile('blendtext',{
    text:`We're a small team that believes the best work comes from strong opinions, held loosely. We make brands that move, systems that scale, and ideas worth arguing about — then we build them properly.`,
    tag:'p',
    font_size:'44', font_size_tablet:'30', font_size_mobile:'22',
    font_weight:'600',
    font_family:`"Syne", -apple-system, sans-serif`,
    text_transform:'none', letter_spacing:'0', line_height:'1.18',
    text_align:'left', text_color:TXT,
    blend_mode:'normal', mode:'text',
    tile_padding:{top:0,right:0,bottom:0,left:0},
  }) ]),
]) ]));

// ─── 5) SERVICES (HoverList → process-steps numerati) ─────────────────────────
// CSS: .hlist__n{font-size:clamp(26px,4vw,52px); font-weight:700; text-transform:uppercase}
// .hlist__n .num{font-size:14px; color:var(--txt-faint)} — numero piccolo faint
// .hlist__tags{font-size:13px; color:var(--txt-dim)} — tag a destra (best-effort: desc sotto)
// border-top/bottom:1px solid var(--line) per ogni riga
// SEGNALATO BEST-EFFORT: HoverList ha layout flex space-between con tag a colonna destra +
// hover indent/recolor. process-steps con columns:1 approssima il layout verticale a lista.
home.push(sec(BG2,'large',[
  row([ col('1-1',[ tile('section-header',{
    eyebrow_show:true, eyebrow_text:'What we do',
    eyebrow_color:AMBER, eyebrow_dot_color:AMBER, eyebrow_separator:'',
    headline_lines:[{text:'SERVICES',color:TXT,italic:false}],
    headline_font_family:`"Syne", -apple-system, sans-serif`,
    headline_font_size:56, headline_font_weight:'800', headline_align:'left',
    tagline_show:false, layout:'stack', gap:10,
  }) ]) ]),
  row([ col('1-1',[ tile('process-steps',{
    items:[
      {number:'01', title:'Branding',      description:'Strategy · Naming · Identity systems'},
      {number:'02', title:'Digital',       description:'Websites · Products · Design systems'},
      {number:'03', title:'Motion',        description:'Brand motion · Film · 3D'},
      {number:'04', title:'Art Direction', description:'Campaigns · Photography · Editorial'},
      {number:'05', title:'Packaging',     description:'Structure · Print · Production'},
    ],
    columns:1, gap:0,
    auto_number:false,
    number_style:'plain',
    number_color:FAINT,
    number_size:14, number_font:'sans-serif', number_weight:'600',
    title_color:TXT,
    title_size:48, title_font:'sans-serif', title_weight:'700',
    desc_color:DIM, desc_size:13,
    align:'left', item_gap:8,
    card_bg:'', card_border:`1px solid ${LINE}`, card_radius:R(0), card_padding:30,
  }) ]) ]),
]));

// ─── 6) AWARDS (counter ×4 in row) ───────────────────────────────────────────
// CSS: .aw{border-top:2px solid var(--amber); padding-top:18px}
// .aw b{font-family:var(--disp); font-weight:700; font-size:40px; line-height:1}
// .aw span{font-size:13px; color:var(--txt-dim); margin-top:8px}
// h2 su 2 righe: "A few nice" / "surprises"
const stat=(prefix,number,suffix,label)=>col('1-4',[ tile('counter',{
  number, suffix, prefix, label, icon_emoji:'',
  text_color:TXT, number_color:AMBER,
  number_font_size:'40', number_font_weight:'700', label_color:DIM, label_font_size:'13',
  bg_type:'color', bg_color:'transparent',
  padding:'8', border_radius:'0',
  border:BORDER_TOP_AMBER,
  tile_padding:{top:18,right:0,bottom:0,left:0},
}) ]);

home.push(sec(BG,'large',[
  row([ col('1-1',[ tile('section-header',{
    eyebrow_show:true, eyebrow_text:'Recognition',
    eyebrow_color:AMBER, eyebrow_dot_color:AMBER, eyebrow_separator:'',
    headline_lines:[
      {text:'A few nice',  color:TXT, italic:false},
      {text:'surprises',   color:TXT, italic:false},
    ],
    headline_font_family:`"Syne", -apple-system, sans-serif`,
    headline_font_size:48, headline_font_weight:'800', headline_align:'left',
    tagline_show:false, layout:'stack', gap:10,
  }) ]) ]),
  row([
    stat('','14','','Awwwards & FWA'),
    stat('','40','+','Brands shipped'),
    stat('','9','','Years independent'),
    stat('','6','','People, no suits'),
  ], {gap:24}),
]));

// ─── 7) SERVICE FINDER (tile finder — chip interattivi) ───────────────────────
// CSS: .vl-find-sec{background:var(--bg-2)}
// --fx-zone-accent:#f4a23b (AMBER) --fx-zone-on:#0a0a0c (INK)
// .vl-find__card{background:var(--bg-3); border:1px solid var(--line); border-radius:16px; padding:34px 38px}
// .vl-find__card .k{font-size:11px; letter-spacing:.14em; color:var(--amber)} — "We'd run" (eyebrow tile)
// .vl-find__card h3 → title; .vl-find__card p → text; .vl-find__card .t → meta (timing/price)
// h2: "What are you making?" con em italic (heading tile)
// Opzioni (chip): A brand / A website / Motion & film / A campaign
home.push(sec(BG2,'large',[
  row([ col('1-1',[ tile('finder',{
    eyebrow:`Where do we start?`,
    heading:`What are you making?`,
    intro:'',
    zone_accent:AMBER,
    zone_on:INK,
    card_bg:BG3,
    card_border:LINE,
    align:'center',
    items:[
      {
        option:`A brand`,
        icon:'',
        title:`Brand Identity`,
        text:`Strategy, naming, a full visual system and the guidelines to keep it alive. Everything from the logo to the way you sound.`,
        meta:`6–9 weeks · from €18k`,
        cta_text:'',
        cta_url:'#contact',
      },
      {
        option:`A website`,
        icon:'',
        title:`Site Design & Build`,
        text:`Art direction, design and a fast, hand-built front end. Marketing sites and product surfaces that feel like the brand.`,
        meta:`8–12 weeks · from €24k`,
        cta_text:'',
        cta_url:'#contact',
      },
      {
        option:`Motion & film`,
        icon:'',
        title:`Motion & Film`,
        text:`Launch films, social cutdowns and the animated identity that ties it together. Storyboard to final grade.`,
        meta:`4–8 weeks · from €12k`,
        cta_text:'',
        cta_url:'#contact',
      },
      {
        option:`A campaign`,
        icon:'',
        title:`Campaign`,
        text:`A single big idea, art-directed across every channel — from the hero film to the last story frame. Concept to rollout.`,
        meta:`Scoped per brief · from €30k`,
        cta_text:'',
        cta_url:'#contact',
      },
    ],
  }) ]) ]),
]));

// ─── 8) CONTACT CTA ──────────────────────────────────────────────────────────
// CSS: .vl-cta{text-align:center; padding:clamp(80px,12vw,170px) 0}
// .vl-cta h2{font-size:clamp(44px,9vw,140px); text-transform:uppercase; line-height:.9}
// eyebrow: "Got something in mind?" — layout:stack centrato
// solo 1 bottone (mail link) — niente cta2
home.push(sec(BG,'large',[ row([ col('1-1',[ tile('cta-banner',{
  headline:`LET'S`,
  headline_accent:'MAKE IT MOVE.',
  headline_accent_italic:false,
  subtitle:`Got something in mind? We read every brief that comes through the door — no warm intro required. Tell us what you're making.`,
  cta_text:'hello@vela.studio',
  cta_url:'#contact',
  cta2_text:'',
  bg:{type:'solid',color:BG},
  text_color:TXT,
  accent_color:AMBER,
  subtitle_color:DIM,
  cta_bg:AMBER, cta_color:INK,
  cta_radius:R(999), cta_size:14,
  headline_font_family:`"Syne", -apple-system, sans-serif`,
  headline_size:80, headline_weight:'800',
  subtitle_size:16,
  layout:'stack', vertical_align:'center',
  banner_radius:R(0), banner_padding:80,
}) ]) ]) ]));

// ─── EMIT ─────────────────────────────────────────────────────────────────────
K.emit({
  slug:'vela', name:'Vela',
  tags:['creative','design','portfolio','studio'],
  description:`Vela — independent creative design studio. Near-black + amber, Syne (display) + Work Sans (body). Tile-pure ricomposizione. Best-effort: WorkGrid→info-cards (5 project cards), HoverList→process-steps (no hover-indent/tag-colonna destra). Service Finder = tile finder nativa (4 chip: A brand / A website / Motion & film / A campaign).`,
  colors:{
    primary:AMBER, primary_contrast:INK,
    secondary:CREAM, secondary_contrast:INK,
    muted:BG2, muted_contrast:TXT,
    text:TXT, text_muted:DIM,
    background:BG, border:LINE, link:AMBER,
  },
  css_disp:`"Syne", -apple-system, sans-serif`,
  css_sans:`"Work Sans", -apple-system, sans-serif`,
  heading_weight:'800', heading_line_height:'0.86',
  google_fonts:['Syne','Work Sans'],
  logo_variant:'light',
  menu:[
    {title:'Work',     url:'#work'},
    {title:'Studio',   url:'#studio'},
    {title:'Services', url:'#services'},
    {title:'Contact',  url:'#contact'},
  ],
  header:{
    bg:BG, text_color:DIM,
    sticky_bg:'rgba(10,10,12,.9)', logo_width:130,
  },
  footer:{
    bg:BG2, headColor:FAINT,
    brand:{name:'Vela Studio', tagline:'Independent design studio building brands, identities and digital products with a point of view.'},
    columns:[
      {title:'Menu',   links:['Work','Studio','Services','Contact']},
      {title:'Social', links:['Instagram','Behance','LinkedIn']},
      {title:'Studio', links:['Via Tortona 9, Milan','hello@vela.studio']},
    ],
    bottom:{left:`© 2026 Vela Studio — an OLOtheme demo.`, right:'Built with OLObuild'},
  },
  cursor:{ blend_mode:'exclusion', ring_color:'#ffffff', dot_color:'#ffffff' },
}, home);
