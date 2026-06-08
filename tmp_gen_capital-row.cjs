/* Capital Row — ricomposizione TILE-PURE (image-free, come Ledger). VC/PE deep-indigo + violet. */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('cr');

const BG='#1a1730', BG2='#1f1c38', PANEL='#252244', PANEL2='#2d2a52', INK='#100e22';
const VIOLET='#8f7aef', VIOLETL='#a895f5', VTINT='rgba(143,122,239,.14)', GOLD='#d8c08a';
const TXT='#b6b2cf', DIM='#7a769a', LINE='rgba(255,255,255,.09)', LINE2='rgba(143,122,239,.4)', WHITE='#ffffff';
const VIOLET_DIM='rgba(143,122,239,.5)';

const home=[];

// 1) HERO + FUND PANEL (showcase)
home.push(sec(BG,'large',[ row([ col('1-1',[ tile('hero-split',{
  eyebrow_text:'Early-stage & growth · since 2009', eyebrow_dot_color:VIOLET, eyebrow_color:VIOLET,
  headline_lines:[ {text:'We back the',color:WHITE,italic:false},{text:'unreasonable',color:VIOLET,italic:true},{text:'ones.',color:WHITE,italic:false} ],
  headline_font_family:'serif', headline_font_size:72, headline_line_height:1.1, headline_font_weight:'500', headline_align:'left',
  subhead:'Capital Row partners with founders building the infrastructure of the next decade — the unglamorous, foundational companies that quietly become indispensable.',
  subhead_color:TXT, subhead_size:18, subhead_italic:false, subhead_max_width:520,
  cta1_text:'Pitch us', cta1_url:'#pitch', cta1_bg:VIOLET, cta1_color:WHITE, cta1_size:15, cta1_radius:R(8), cta1_radius_hover:R(8),
  cta2_text:'See the portfolio', cta2_url:'#portfolio', cta2_bg:'rgba(255,255,255,.05)', cta2_color:WHITE, cta2_border:'rgba(255,255,255,.16)', cta2_size:15, cta2_radius:R(8), cta2_radius_hover:R(8),
  stats:[],
  showcase_enabled:true, showcase_bg:{type:'solid',color:PANEL}, showcase_padding:28, showcase_radius:R(16), showcase_radius_hover:R(16),
  showcase_badge_text:'FUND IV · AT A GLANCE', showcase_badge_dot:VIOLET, showcase_badge_bg:INK, showcase_badge_color:WHITE,
  showcase_items:[
    {number:'Assets under management',text:'€600M',italic:false,text_color:VIOLET,bg:{type:'solid',color:BG2}},
    {number:'Companies backed',text:'84',italic:false,text_color:WHITE,bg:{type:'solid',color:BG2}},
    {number:'Initial cheque',text:'€1–8M',italic:false,text_color:WHITE,bg:{type:'solid',color:BG2}},
    {number:'Notable exits',text:'11',italic:false,text_color:WHITE,bg:{type:'solid',color:BG2}},
  ],
  showcase_card_radius:R(11), showcase_card_radius_hover:R(11), showcase_card_shadow:'none',
  showcase_caption_left:'FUND IV', showcase_caption_right:'AS OF 2026', showcase_hover_effect:'none',
  split_ratio:'1.25fr .75fr', gap:52, min_height:0, tile_padding:{top:0,right:0,bottom:0,left:0},
}) ]) ]) ]));

// helper: caption-only centered header (per le label tipo "logo cloud")
const caption=(txt)=>tile('section-header',{
  eyebrow_show:false, headline_lines:[{text:txt,color:DIM,italic:false}],
  headline_font_family:'sans-serif', headline_font_size:13, headline_font_weight:'600', headline_align:'center',
  tagline_show:false, layout:'center', gap:0,
});
// helper: section-header centrato (eyebrow + headline accentato inline)
const shead=(eyebrow,l1,accent,intro)=>tile('section-header',{
  eyebrow_show:true, eyebrow_text:eyebrow, eyebrow_color:VIOLET, eyebrow_dot_color:VIOLET, eyebrow_separator:'',
  headline_lines:[ {text:l1,color:WHITE,italic:false},{text:accent,color:VIOLET,italic:true} ],
  headline_font_family:'serif', headline_font_size:46, headline_font_weight:'500', headline_align:'center', headline_inline:true,
  tagline_show:!!intro, tagline_text:intro||'', tagline_text_italic:false, tagline_text_color:DIM, tagline_text_size:16.5,
  layout:'center', gap:16,
});
const sheadLeft=(eyebrow,l1,accent)=>tile('section-header',{
  eyebrow_show:true, eyebrow_text:eyebrow, eyebrow_color:VIOLET, eyebrow_dot_color:VIOLET, eyebrow_separator:'',
  headline_lines:[ {text:l1,color:WHITE,italic:false},{text:accent,color:VIOLET,italic:true} ],
  headline_font_family:'serif', headline_font_size:44, headline_font_weight:'500', headline_align:'left', headline_inline:true,
  tagline_show:false, layout:'stack', gap:12,
});

// 2) LOGO CLOUD (caption + trust-strip pill)
home.push(sec(BG2,'small',[
  row([ col('1-1',[ caption(`A few of the companies we're proud to back`) ]) ]),
  row([ col('1-1',[ tile('trust-strip',{
    items:[ {text:'ARCLIGHT'},{text:'TOLLKEEP'},{text:'VELLUM'},{text:'NORTHWIND'},{text:'KESTREL'},{text:'AURIC'} ],
    variant:'pill', separator_char:'', align:'center', flow:'wrap', gap:16,
    font_family:'sans-serif', text_color:DIM, text_size:13,
    pill_bg:'rgba(255,255,255,.06)', pill_border:LINE, pill_text_color:DIM,
  }) ]) ], {gap:16}),
]));

// 3) STAT STRIP (counter ×4 — text_color=numero, number_color=unità violet)
const stat=(prefix,number,suffix,label)=>col('1-4',[ tile('counter',{
  number, suffix, prefix, label, icon_emoji:'',
  text_color:WHITE, number_color:VIOLET, number_font_size:'54', number_font_weight:'500', label_color:DIM, label_font_size:'13',
  bg_type:'color', bg_color:'transparent', padding:'8', border_radius:'0',
}) ]);
home.push(sec(BG,'small',[ row([
  stat('€','600','M','Under management'), stat('','84','','Companies backed'), stat('','11','','Exits & IPOs'), stat('','15','yr','Investing together'),
], {gap:24}) ]));

// 4) THESIS (section-header center + 3 info-cards check) — image-free
home.push(sec(BG2,'large',[
  row([ col('1-1',[ shead('Our thesis','Infrastructure beats ','hype','We invest where the picks and shovels are — developer tools, fintech rails, climate hardware, the boring middle layer that everything else depends on.') ]) ]),
  row([ col('1-1',[ tile('info-cards',{
    container_bg:{type:'solid',color:'transparent'}, container_padding:0, container_gap:16, columns:3, items_gap:16,
    card_bg:{type:'solid',color:PANEL}, card_color:DIM, card_radius:R(14), card_padding:26,
    show_icon:true, show_counter:false, show_arrow:false, show_footer:false, show_media:false,
    icon_color:VIOLET, icon_bg_color:VTINT, title_color:WHITE,
    title_font_family:'serif', title_size:20, title_weight:'500', title_italic:false, description_size:14.5,
    items:[
      {icon:'check', title:'Lead & co-lead', description:'We lead and co-lead at seed and Series A — conviction cheques, not spray-and-pray.'},
      {icon:'check', title:'Reserve & double down', description:'We reserve heavily for the ones that work, backing winners round after round.'},
      {icon:'check', title:'One partner, end to end', description:'One partner from first cheque to exit — no hand-offs, no call-centre queue.'},
    ],
    card_hover_effect:'lift',
  }) ]) ]),
]));

// 5) PORTFOLIO (section-header left + info-cards company cards)
home.push(sec(BG,'large',[
  row([ col('1-1',[ sheadLeft('Portfolio','Companies we ','believe in') ]) ]),
  row([ col('1-1',[ tile('info-cards',{
    container_bg:{type:'solid',color:'transparent'}, container_padding:0, container_gap:16, columns:3, items_gap:16,
    card_bg:{type:'solid',color:PANEL}, card_color:DIM, card_radius:R(14), card_padding:26,
    show_icon:false, show_counter:true, show_counter_label:true, show_arrow:false, show_footer:true, show_media:false,
    counter_shape:'square', counter_color:VIOLET, counter_bg:'rgba(255,255,255,.08)', title_color:WHITE,
    title_font_family:'serif', title_size:21, title_weight:'500', title_italic:false, description_size:14, counter_size:18, footer_size:12,
    items:[
      {counter:'Ar', counter_label:'Series A', title:'Arclight', description:'Climate · grid software. Balancing the grid for utilities running on renewables.', footer_text:'Led Series A · 2024', footer_dot_color:VIOLET},
      {counter:'Tk', counter_label:'Seed', title:'Tollkeep', description:'Fintech · payments rails. Settlement infrastructure for cross-border marketplaces.', footer_text:'Led Seed · 2025', footer_dot_color:VIOLET},
      {counter:'Vx', counter_label:'Series B', title:'Vellum', description:'Dev tools · data. The versioned data layer for AI teams in production.', footer_text:'Co-led Series A · 2023', footer_dot_color:VIOLET},
    ],
    card_hover_effect:'lift',
  }) ]) ]),
]));

// 6) APPROACH (section-header center + process-steps con card — 3 colonne)
// CSS: .cr-step { padding:30px; border:1px solid var(--line); border-radius:14px; background:var(--panel); }
// Numero: font-family:var(--disp); font-weight:500; font-size:40px; color:var(--violet); opacity:.5
home.push(sec(BG2,'large',[
  row([ col('1-1',[ shead('How we work','Conviction, then ','patience',`We move quickly to a decision and slowly out of a company. Here's what working with us actually looks like.`) ]) ]),
  row([ col('1-1',[ tile('process-steps',{
    columns:3, gap:18, align:'left', auto_number:false, item_gap:14,
    number_style:'plain', number_color:VIOLET_DIM, number_size:40, number_font:'serif', number_weight:'500',
    title_color:WHITE, title_size:21, title_font:'serif', title_weight:'500',
    desc_color:DIM, desc_size:14,
    card_bg:PANEL, card_border:LINE, card_radius:R(14), card_padding:30,
    items:[
      {number:'01', title:'A real answer in two weeks', description:`One partner owns your deal end to end. You get a clear yes or no — and the reasoning either way — fast.`},
      {number:'02', title:'Hands-on where it helps', description:`Hiring, follow-on rounds, first enterprise customers. We're on the cap table to be useful, not to spectate.`},
      {number:'03', title:'In it for the decade', description:`We reserve to keep backing you round after round, and we don't push for an exit that isn't yours to make.`},
    ],
  }) ]) ]),
]));

// 7) FOUNDER QUOTE (testimonial)
home.push(sec(BG,'large',[ row([ col('1-1',[ tile('testimonial',{
  quote:`“They wired the first cheque on a handshake and a napkin. Six years later they're still the first call I make — good news or bad.”`,
  author_name:'Founder, Arclight', author_role:'Series A portfolio', rating:'0',
  layout:'single', show_line:false, bg_color:'transparent', text_color:WHITE, border_radius:'0', avatar:'',
}) ]) ]) ]));

// 8) FUND PROJECTOR
home.push(sec(BG2,'large',[ row([ col('1-1',[ tile('projector',{
  eyebrow:'Illustrative model', heading:'What a decade of <em>discipline</em> compounds to',
  intro:'Drag to your annual commitment. This models a steady allocation across our funds at a 15% net IRR over a ten-year horizon — the case for showing up every vintage.',
  min:'50000', max:'2000000', step:'50000', value:'500000', rate:'0.15', years:'10', currency:'€',
  input_label:'Committed each year', out_caption:'Projected after 10 years',
  note:'Illustrative only · 15% net IRR, annual commitments. Private markets are illiquid; capital is at risk and past performance is not indicative.',
  show_contrib:true, zone_accent:VIOLET, align:'left', tile_padding:{top:52,right:52,bottom:52,left:52}, border_radius:'16', shadow:'sm',
}) ]) ]) ]));

// 9) CTA — 2 bottoni: "Send us your deck" (violet solid) + "See who we back" (ghost outline)
// CSS: .cr-cta__box { border:1px solid var(--line-2); border-radius:24px; padding:…; background:linear-gradient(160deg,rgba(143,122,239,.18),rgba(143,122,239,.04)) }
home.push(sec(BG,'large',[ row([ col('1-1',[ tile('cta-banner',{
  headline:'Building something', headline_accent:'foundational?', headline_accent_italic:true,
  subtitle:'We read every deck that comes through the front door — no warm intro required. Tell us what you\'re making.',
  cta_text:'Send us your deck', cta_url:'#pitch',
  cta2_text:'See who we back', cta2_url:'#portfolio', cta2_bg:'rgba(255,255,255,.05)', cta2_color:WHITE, cta2_border:'rgba(255,255,255,.16)',
  bg:{type:'gradient',gradient:'linear-gradient(160deg, rgba(143,122,239,.18), rgba(143,122,239,.04))'}, text_color:WHITE, accent_color:VIOLET, subtitle_color:TXT,
  cta_bg:VIOLET, cta_color:WHITE, cta_radius:R(8), cta_size:15,
  headline_font_family:'serif', headline_size:48, headline_weight:'500', subtitle_size:17,
  layout:'stack', vertical_align:'center', banner_radius:R(24), banner_padding:80,
}) ]) ]) ]));

K.emit({
  slug:'capital-row', name:'Capital Row',
  tags:['consulting','finance','venture','private-equity'],
  description:`Capital Row — early-stage venture & growth fund. Deep indigo + violet, Source Serif 4 (display) + Manrope. Zona interattiva Projector (montante composto). Riproduzione fedele dell'OLOtheme Capital Row.`,
  colors:{ primary:VIOLET, primary_contrast:WHITE, secondary:GOLD, secondary_contrast:INK, muted:BG2, muted_contrast:TXT, text:TXT, text_muted:DIM, background:BG, border:LINE, link:VIOLET },
  css_disp:`"Source Serif 4", Georgia, serif`, css_sans:`"Manrope", -apple-system, sans-serif`,
  heading_weight:'500', heading_line_height:'1.08', google_fonts:['Source Serif 4','Manrope'],
  logo_variant:'light',
  menu:[ {title:'Thesis',url:'#thesis'},{title:'Portfolio',url:'#portfolio'},{title:'Approach',url:'#approach'},{title:'Team',url:'#team'} ],
  header:{ bg:BG, text_color:DIM, sticky_bg:'rgba(26,23,48,.84)', logo_width:140 },
  footer:{ bg:BG2, headColor:WHITE, brand:{name:'Capital Row', tagline:'An early-stage venture & growth fund backing the infrastructure of the next decade.'},
    columns:[ {title:'Firm',links:['Thesis','Approach','Team','News']}, {title:'Founders',links:['Portfolio','Pitch us','Playbooks','Jobs at portfolio']}, {title:'Contact',links:['LP relations','Press','Frankfurt · London']} ],
    bottom:{left:'© 2026 Capital Row — an OLOtheme demo.', right:'Built with OLObuild'} },
  cursor:{ blend_mode:'exclusion', ring_color:'#ffffff', dot_color:'#ffffff' },
}, home);
