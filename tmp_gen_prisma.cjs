/* Prisma — ricomposizione TILE-PURE (image-free). Creative agency. Ink + magenta. */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('pr');

const BG='#160a24', BG2='#1b0d2c', PANEL='#241338', INK='#0d0518';
const MAG='#c14bff', MAGL='#d175ff', CYAN='#4be0ff', CREAM='#f1e9f7';
const TXT='#b6a6c8', DIM='#7d6c92', LINE='rgba(241,233,247,.1)', LINE2='rgba(193,75,255,.42)', WHITE='#ffffff';
const MTINT='rgba(193,75,255,.14)';

const home=[];

// 1) HERO — hero-split: eyebrow + titolo 3 righe + subhead + 2 CTA
// CSS: .pr-hero h1 font-size clamp(48px,10vw,150px) line-height .9
// CTA: btn--mag (MAG bg), btn--ghost (transparent + LINE2 border) — entrambi pill (radius 999)
home.push(sec(BG,'large',[ row([ col('1-1',[ tile('hero-split',{
  eyebrow_text:'Brand & digital agency', eyebrow_dot_color:MAG, eyebrow_color:MAG,
  headline_lines:[
    {text:'We make',color:WHITE,italic:false},
    {text:'brands',color:WHITE,italic:false},
    {text:'impossible to ignore.',color:MAG,italic:false},
  ],
  headline_font_family:'serif', headline_font_size:80, headline_line_height:.9, headline_font_weight:'800', headline_align:'left',
  subhead:`Identity, product and campaigns for companies that would rather be loved or hated than forgotten.`,
  subhead_color:TXT, subhead_size:19, subhead_italic:false, subhead_max_width:440,
  cta1_text:'See the work', cta1_url:'#work', cta1_bg:MAG, cta1_color:WHITE, cta1_size:14, cta1_radius:R(999), cta1_radius_hover:R(999),
  cta2_text:'Start a project', cta2_url:'#contact', cta2_bg:'transparent', cta2_color:WHITE, cta2_border:LINE2, cta2_size:14, cta2_radius:R(999), cta2_radius_hover:R(999),
  stats:[],
  showcase_enabled:true, showcase_bg:{type:'solid',color:PANEL}, showcase_padding:28, showcase_radius:R(18), showcase_radius_hover:R(18),
  showcase_badge_text:'SELECTED WORK', showcase_badge_dot:MAG, showcase_badge_bg:INK, showcase_badge_color:WHITE,
  showcase_items:[
    {number:'Halcyon',text:`Rebrand & site · '26`,italic:false,text_color:CREAM,bg:{type:'solid',color:'rgba(193,75,255,.12)'}},
    {number:'Nimbus Air',text:`Identity · '25`,italic:false,text_color:CREAM,bg:{type:'solid',color:'rgba(75,224,255,.08)'}},
    {number:'Otto',text:`Campaign · '25`,italic:false,text_color:CREAM,bg:{type:'solid',color:'rgba(241,233,247,.06)'}},
    {number:'Fold',text:`Product · '24`,italic:false,text_color:CREAM,bg:{type:'solid',color:'rgba(241,233,247,.06)'}},
  ],
  showcase_card_radius:R(12), showcase_card_radius_hover:R(12), showcase_card_shadow:'none',
  showcase_caption_left:'Recent work', showcase_caption_right:'2024 – 2026', showcase_hover_effect:'none',
  split_ratio:'1.2fr .8fr', gap:52, min_height:0, tile_padding:{top:0,right:0,bottom:0,left:0},
}) ]) ]) ]));

// helpers
const shead=(eyebrow,l1,accent,intro,align)=>tile('section-header',{
  eyebrow_show:true, eyebrow_text:eyebrow, eyebrow_color:MAG, eyebrow_dot_color:MAG, eyebrow_separator:'',
  headline_lines:[ {text:l1,color:WHITE,italic:false},{text:accent,color:MAG,italic:false} ],
  headline_font_family:'serif', headline_font_size:56, headline_font_weight:'800', headline_align:align||'center', headline_inline:true,
  tagline_show:!!intro, tagline_text:intro||'', tagline_text_italic:false, tagline_text_color:DIM, tagline_text_size:16.5,
  layout:align==='left'?'stack':'center', gap:16,
});

// 2) WORK — Selected work: HoverList → info-cards numerato 1 col (nessuna card, bordini LINE)
// CSS: .pr-work a — grid row con n/t/cat/yr; hover → sfondo panel-s; border-bottom LINE
home.push(sec(BG,'large',[
  row([ col('1-1',[ shead('Selected work','Recent ','projects','', 'left') ]) ]),
  row([ col('1-1',[ tile('info-cards',{
    container_bg:{type:'solid',color:'transparent'}, container_padding:0, container_gap:0, columns:1, items_gap:0,
    card_bg:{type:'solid',color:'transparent'}, card_color:DIM, card_radius:R(0), card_padding:24,
    show_icon:false, show_counter:true, show_counter_label:true, show_arrow:true, show_footer:true, show_media:false,
    counter_shape:'plain', counter_color:DIM, title_color:WHITE,
    title_font_family:'serif', title_size:36, title_weight:'800', title_italic:false, description_size:13, counter_size:13, footer_size:12,
    card_border_bottom:LINE, card_hover_bg:PANEL, card_hover_effect:'lift',
    items:[
      {counter:'01', title:'Halcyon',        description:'Rebrand · web',      footer_text:`'26`, footer_dot_color:MAG},
      {counter:'02', title:'Nimbus Air',     description:'Identity',           footer_text:`'25`, footer_dot_color:MAG},
      {counter:'03', title:'Otto',           description:'Campaign',           footer_text:`'25`, footer_dot_color:MAG},
      {counter:'04', title:'Fold',           description:'Product design',     footer_text:`'24`, footer_dot_color:MAG},
      {counter:'05', title:'Vero',           description:'Packaging',          footer_text:`'24`, footer_dot_color:MAG},
    ],
  }) ]) ]),
]));

// 3) SERVICES — Three disciplines
// CSS: .pr-svc → background:var(--panel-s) #241338; border:1px solid var(--line); border-radius:14px; padding:30px
// .pr-svc .num → font-size:14px color:var(--mag) — counter plain piccolo
// .pr-svc h3 → 26px; .pr-svc p → 14.5px color:var(--txt-dim)
// CORREZIONE: aggiunto card_border:LINE mancante nella versione precedente
home.push(sec(BG2,'large',[
  row([ col('1-1',[ shead('What we do','Three ','disciplines','',  'center') ]) ]),
  row([ col('1-1',[ tile('info-cards',{
    container_bg:{type:'solid',color:'transparent'}, container_padding:0, container_gap:16, columns:3, items_gap:16,
    card_bg:{type:'solid',color:PANEL}, card_color:DIM, card_radius:R(14), card_padding:30,
    card_border:LINE,
    show_icon:false, show_counter:true, show_counter_label:false, show_arrow:false, show_footer:false, show_media:false,
    counter_shape:'plain', counter_color:MAG, title_color:WHITE,
    title_font_family:'serif', title_size:26, title_weight:'800', title_italic:false, description_size:14.5, counter_size:14,
    card_hover_effect:'lift',
    items:[
      {counter:'01', title:'Brand',     description:'Strategy, identity and the systems that keep you consistent everywhere — from logo to tone of voice.'},
      {counter:'02', title:'Digital',   description:'Websites and products designed and built to convert, perform and feel unmistakably yours.'},
      {counter:'03', title:'Campaigns', description:`Ideas big enough to travel — art direction, film and social that people actually share.`},
    ],
  }) ]) ]),
]));

// 4) STAT STRIP — Years loud, Projects shipped, Awards, Humans
// CSS: .pr-stats b → color:#fff (WHITE, non MAG!) — .pr-stats b .u → color:var(--mag) (suffix)
// CORREZIONE: text_color:WHITE (era MAG — il numero principale è bianco nel CSS)
// suffix "+" — il tile counter non ha suffix_color separato; best-effort WHITE per tutto
const stat=(prefix,number,suffix,label)=>col('1-4',[ tile('counter',{
  number, suffix, prefix, label, icon_emoji:'',
  text_color:WHITE, number_font_size:'58', number_font_weight:'800', label_color:DIM, label_font_size:'12',
  bg_type:'color', bg_color:'transparent', padding:'8', border_radius:'0',
  tile_padding:{top:24,right:8,bottom:24,left:8},
}) ]);
// CSS: .pr-stats { border-block:1px solid var(--line) } padding:40px 0 — sfondo BG con bordi top/bottom
home.push(sec(BG,'small',[ row([
  stat('','14','','Years loud'),
  stat('','240','+','Projects shipped'),
  stat('','30','','Awards'),
  stat('','18','','Humans'),
], {gap:24}) ]));

// 5) PROCESS — From brief to bang (4 passi)
// CSS: .pr-step → padding:26px; border:1px solid var(--line); border-radius:14px; background:var(--panel-s)
// .pr-step::before → counter auto, font-size:30px, color:var(--mag)
// .pr-step h3 → 19px; .pr-step p → 13.5px color:var(--txt-dim)
// CORREZIONE: rimpiazzo info-cards con process-steps (tile dedicato per passi numerati)
//             card_bg=PANEL, card_border=LINE, card_radius=14, card_padding=26
//             auto_number:true (il CSS usa CSS counter — equivalente)
//             number_font:'serif', number_size:30, number_color:MAG, number_weight:'800'
//             title_font:'serif', title_size:19, title_weight:'800', title_color:WHITE
//             desc_color:DIM, desc_size:13.5 (13 nel range)
home.push(sec(BG,'large',[
  row([ col('1-1',[ shead('How we work','From brief to ','bang','','center') ]) ]),
  row([ col('1-1',[ tile('process-steps',{
    columns:4, gap:16, align:'left', auto_number:true, item_gap:12,
    number_style:'plain', number_color:MAG, number_size:30, number_font:'serif', number_weight:'800',
    title_color:WHITE, title_size:19, title_font:'serif', title_weight:'800',
    desc_color:DIM, desc_size:13,
    card_bg:PANEL, card_border:LINE, card_radius:R(14), card_padding:26,
    items:[
      {number:'01', title:'Dig',    description:'We get under the skin of your business, audience and category.'},
      {number:'02', title:'Define', description:'A sharp strategy and a creative platform everything hangs from.'},
      {number:'03', title:'Design', description:`We make it — identity, product, the lot — in tight, visible loops.`},
      {number:'04', title:'Deploy', description:`We launch it loudly and stick around to make it land.`},
    ],
  }) ]) ]),
]));

// 6) BRAND MIXER — tile mixer nativo
home.push(sec(BG,'large',[ row([ col('1-1',[ tile('mixer',{
  eyebrow: '// play',
  heading: 'Mix a brand',
  intro: `Every identity starts as a clash of colours that shouldn't work. Tap a few and find the one that does — then book us to make it real.`,
  max: 3,
  empty_label: 'Tap colours to mix',
  zone_accent: '#c14bff',
  zone_on: '#0d0518',
  card_bg: PANEL,
  card_border: LINE,
  align: 'left',
  items: [
    { name: 'Ultra', color: '#c14bff' },
    { name: 'Cyan',  color: '#4be0ff' },
    { name: 'Punch', color: '#ff5a8a' },
    { name: 'Solar', color: '#ffd34b' },
    { name: 'Volt',  color: '#2bd87a' },
    { name: 'Paper', color: '#f1e9f7' },
  ],
}) ]) ]) ]));


// 7) PALETTE HARMONY — best-effort (tile "PaletteHarmony" non esiste)
// SEGNALATA: PaletteHarmony interattivo (seed + harmony rule → 5-stop scheme) non ha tile nativo.
// CSS: .pr-pal → border:1px solid var(--line); border-radius:18px; background:var(--panel-s); padding:42px
// Approssimazione: section-header centrato + info-cards palette statica 5 colonne
home.push(sec(BG2,'large',[
  row([ col('1-1',[ tile('section-header',{
    eyebrow_show:true, eyebrow_text:'Brand toolkit', eyebrow_color:MAG, eyebrow_dot_color:MAG, eyebrow_separator:'',
    headline_lines:[{text:'Generate a ',color:WHITE,italic:false},{text:'palette',color:MAG,italic:false}],
    headline_font_family:'serif', headline_font_size:52, headline_font_weight:'800', headline_align:'center', headline_inline:true,
    tagline_show:true,
    tagline_text:`Pick a seed and a harmony rule — we build a five-stop scheme you can lift straight into a brand. Tap any swatch to copy the hex.`,
    tagline_text_italic:false, tagline_text_color:DIM, tagline_text_size:16,
    layout:'center', gap:16,
  }) ]) ]),
  row([ col('1-1',[ tile('info-cards',{
    container_bg:{type:'solid',color:PANEL}, container_padding:32, container_gap:12, columns:5, items_gap:12,
    card_bg:{type:'solid',color:PANEL}, card_color:DIM, card_radius:R(14), card_padding:20,
    card_border:LINE,
    show_icon:false, show_counter:false, show_counter_label:false, show_arrow:false, show_footer:false, show_media:false,
    title_color:WHITE,
    title_font_family:'sans-serif', title_size:13, title_weight:'800', title_italic:false, description_size:11,
    card_hover_effect:'lift',
    items:[
      {title:'#c14bff', description:'Seed — Ultra'},
      {title:'#a030e0', description:'Shade 1'},
      {title:'#7a1cb8', description:'Shade 2'},
      {title:'#d175ff', description:'Tint 1'},
      {title:'#f1e9f7', description:'Tint 2 — Cream'},
    ],
  }) ]) ]),
  // Nota: .pr-pal ha border:LINE, border-radius:18px, background:panel-s, padding:42px
  // Il container_bg + card_border lo avvicina — i swatches .pal-sw hanno aspect-ratio 3/4 non riproducibile con info-cards
]));

// 8) CTA — "Got something to say?" — 2 bottoni: btn--mag + btn--ghost
// CSS: .pr-cta__box → border:1px solid LINE2; border-radius:24px; padding:96px 64px
//      background:linear-gradient(150deg,rgba(193,75,255,.2),rgba(75,224,255,.06))
//      .pr-cta h2 → font-size clamp(40px,7vw,108px); .pr-cta p → 17px
// 2 CTA: cta2_text/cta2_url/cta2_bg/cta2_color/cta2_border
home.push(sec(BG,'large',[ row([ col('1-1',[ tile('cta-banner',{
  headline:`Got something`, headline_accent:'to say?', headline_accent_italic:false,
  subtitle:`Tell us what you're building. We'll tell you how we'd make it impossible to ignore.`,
  cta_text:'Start a project', cta_url:'#contact',
  cta2_text:'See the work', cta2_url:'#work',
  bg:{type:'gradient',gradient:`linear-gradient(150deg,rgba(193,75,255,.2) 0%,rgba(75,224,255,.06) 100%)`},
  text_color:WHITE, accent_color:MAG, subtitle_color:TXT,
  cta_bg:MAG, cta_color:WHITE, cta_radius:R(999), cta_size:14,
  cta2_bg:'transparent', cta2_color:WHITE, cta2_border:LINE2, cta2_radius:R(999),
  headline_font_family:'serif', headline_size:80, headline_weight:'800', subtitle_size:17,
  layout:'stack', vertical_align:'center', banner_radius:R(24), banner_padding:80,
  border_color:LINE2,
}) ]) ]) ]));

K.emit({
  slug:'prisma', name:'Prisma',
  tags:['creative','agency','branding','design','portfolio'],
  description:`Prisma — brand & digital agency. Deep ink + magenta, Anybody (display) + Hanken Grotesk (body). Agency creativa: lavori/portfolio, servizi, processo, CTA. Riproduzione fedele dell'OLOtheme Prisma.`,
  colors:{
    primary:MAG, primary_contrast:WHITE,
    secondary:CYAN, secondary_contrast:INK,
    muted:BG2, muted_contrast:TXT,
    text:TXT, text_muted:DIM,
    background:BG, border:LINE, link:MAG,
  },
  css_disp:`"Anybody", -apple-system, sans-serif`,
  css_sans:`"Hanken Grotesk", -apple-system, sans-serif`,
  heading_weight:'800', heading_line_height:'0.98', google_fonts:['Anybody','Hanken Grotesk'],
  logo_variant:'light',
  menu:[
    {title:'Work',     url:'#work'},
    {title:'Services', url:'#services'},
    {title:'Process',  url:'#process'},
    {title:'Contact',  url:'#contact'},
  ],
  header:{ bg:BG, text_color:DIM, sticky_bg:'rgba(22,10,36,.84)', logo_width:130 },
  footer:{
    bg:BG2, headColor:WHITE,
    brand:{ name:'Prisma', tagline:'Brand & digital agency. Identity, product and campaigns for the unforgettable.' },
    columns:[
      {title:'Agency',  links:['Work','Services','Process','Studio']},
      {title:'Connect', links:['Instagram','LinkedIn','Behance']},
      {title:'Say hi',  links:['hello@prisma.agency','+44 20 4538','London · Berlin']},
    ],
    bottom:{ left:'© 2026 Prisma — an OLOtheme demo.', right:'Built with OLObuild' },
  },
  cursor:{ blend_mode:'exclusion', ring_color:WHITE, dot_color:MAG },
}, home);
