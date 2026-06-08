/* Verdano FC — ricomposizione TILE-PURE (image-free). Health & Fitness / Sports Club.
   Pitch green + volt lime. Archivo (display) + Work Sans (body). */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('vr');

const PITCH='#0a2a1e', PITCH2='#0f3a2a', PITCH3='#15543c';
const VOLT='#c8ff3c', VOLTD='#aee61f';
const CLARET='#d33a55';
const BONE='#f3f0e7', BONE2='#ebe6da', PAPER='#ffffff';
const TXT='#142019', TXTSOFT='#4f5b54', TXTFAINT='#8a948d';
const LINE='#e2ddd0', LINEDK='rgba(255,255,255,.1)';
const WHITE='#ffffff';

const home=[];

// helpers ---------------------------------------------------------------

const caption=(txt,color)=>tile('section-header',{
  eyebrow_show:false,
  headline_lines:[{text:txt,color:color||TXTFAINT,italic:false}],
  headline_font_family:'sans-serif', headline_font_size:12, headline_font_weight:'700',
  headline_align:'center', tagline_show:false, layout:'center', gap:0,
});

// section-header su sfondo chiaro: headline plain (singola riga TXT) + eyebrow claret
const shead=(eyebrow,h2,intro,align)=>tile('section-header',{
  eyebrow_show:true, eyebrow_text:eyebrow, eyebrow_color:CLARET, eyebrow_dot_color:CLARET, eyebrow_separator:'',
  headline_lines:[
    {text:h2,color:TXT,italic:false},
  ],
  headline_font_family:'sans-serif', headline_font_size:52, headline_font_weight:'900',
  headline_align:align||'left', headline_inline:false,
  tagline_show:!!intro, tagline_text:intro||'', tagline_text_italic:false,
  tagline_text_color:TXTSOFT, tagline_text_size:16,
  layout:align==='center'?'center':'stack', gap:14,
});

// section-header su sfondo scuro: headline plain bianca + eyebrow volt
const sheadDark=(eyebrow,h2,intro)=>tile('section-header',{
  eyebrow_show:true, eyebrow_text:eyebrow, eyebrow_color:VOLT, eyebrow_dot_color:VOLT, eyebrow_separator:'',
  headline_lines:[
    {text:h2,color:WHITE,italic:false},
  ],
  headline_font_family:'sans-serif', headline_font_size:52, headline_font_weight:'900',
  headline_align:'left', headline_inline:false,
  tagline_show:!!intro, tagline_text:intro||'', tagline_text_italic:false,
  tagline_text_color:'rgba(255,255,255,.62)', tagline_text_size:16,
  layout:'stack', gap:14,
});

// 1) HERO — hero-split image-free (showcase con stat del club) ---------------
home.push(sec(PITCH,'large',[ row([ col('1-1',[ tile('hero-split',{
  eyebrow_text:`Next home game · Sat 14 Mar · 15:00`, eyebrow_dot_color:VOLT, eyebrow_color:VOLT,
  headline_lines:[
    {text:'Forged on',color:WHITE,italic:false},
    {text:'the',color:WHITE,italic:false},
    {text:'pitch.',color:VOLT,italic:false},
  ],
  headline_font_family:'sans-serif', headline_font_size:80, headline_line_height:.88,
  headline_font_weight:'900', headline_align:'left',
  subhead:`Eight teams, one badge. Verdano FC has played, fought and grown in this city for fifty years — and we're only getting started.`,
  subhead_color:'rgba(255,255,255,.72)', subhead_size:16.5, subhead_italic:false, subhead_max_width:430,
  cta1_text:'View fixtures', cta1_url:'#matches',
  cta1_bg:VOLT, cta1_color:PITCH, cta1_size:15, cta1_radius:R(999), cta1_radius_hover:R(999),
  cta2_text:'Become a member', cta2_url:'#membership',
  cta2_bg:'rgba(255,255,255,.06)', cta2_color:WHITE,
  cta2_border:'rgba(255,255,255,.26)', cta2_size:15, cta2_radius:R(999), cta2_radius_hover:R(999),
  stats:[],
  showcase_enabled:true, showcase_bg:{type:'solid',color:PITCH2},
  showcase_padding:26, showcase_radius:R(20), showcase_radius_hover:R(20),
  showcase_badge_text:'VERDANO FC · SEASON 2026', showcase_badge_dot:VOLT,
  showcase_badge_bg:PITCH, showcase_badge_color:WHITE,
  showcase_items:[
    {number:'Est.',text:'1974',italic:false,text_color:VOLT,bg:{type:'solid',color:PITCH3}},
    {number:'Competitive teams',text:'8',italic:false,text_color:WHITE,bg:{type:'solid',color:PITCH3}},
    {number:'Active members',text:'600+',italic:false,text_color:WHITE,bg:{type:'solid',color:PITCH3}},
    {number:'Years of football',text:'50',italic:false,text_color:WHITE,bg:{type:'solid',color:PITCH3}},
  ],
  showcase_card_radius:R(12), showcase_card_radius_hover:R(12), showcase_card_shadow:'none',
  showcase_caption_left:'Verdano FC', showcase_caption_right:'Home · Verdano Park',
  showcase_hover_effect:'none',
  split_ratio:'1.15fr .85fr', gap:52, min_height:0,
  tile_padding:{top:0,right:0,bottom:0,left:0},
}) ]) ]) ]));

// 2) LATEST NEWS — 4 card notizie con info-cards (show_media:true, image-free) ---
// Blueprint: .news{bg:paper, radius:16px, border:1px solid var(--line), hover lift}
// .news__cat: 11px claret uppercase; .news__t: 18px display 800 TXT
home.push(sec(BONE,'large',[
  row([ col('1-1',[
    shead('Straight from the club','Latest news',null,'left'),
  ]) ]),
  row([ col('1-1',[ tile('info-cards',{
    container_bg:{type:'solid',color:'transparent'}, container_padding:0, container_gap:16,
    columns:4, items_gap:16,
    card_bg:{type:'solid',color:PAPER}, card_color:TXTSOFT,
    card_radius:R(16), card_padding:0,
    show_icon:false, show_counter:false, show_arrow:false, show_footer:true, show_media:true,
    icon_color:CLARET, icon_bg_color:'rgba(211,58,85,.1)', title_color:TXT,
    title_font_family:'sans-serif', title_size:18, title_weight:'800', title_italic:false,
    description_size:11,
    items:[
      {media_label:'news photo', description:'Matches',
        title:'First team grind out a 2–1 win in the derby',
        footer_text:'Matches', footer_dot_color:CLARET},
      {media_label:'news photo', description:'People',
        title:'Academy keeper called up to the regional side',
        footer_text:'People', footer_dot_color:CLARET},
      {media_label:'news photo', description:'Events',
        title:`Family fun day returns to Verdano Park this spring`,
        footer_text:'Events', footer_dot_color:CLARET},
      {media_label:'news photo', description:'Club',
        title:'New community pitch opens its gates to the city',
        footer_text:'Club', footer_dot_color:CLARET},
    ],
    card_hover_effect:'lift',
    card_border:'1px solid '+LINE,
  }) ]) ]),
]));

// 3) CLUB INTRO — section-header sinistro + contatori inline + media col destra -----
// Blueprint: left col 1.05fr (eyebrow + h2 + lead + stats-row + btn), right col .95fr (media+badge)
// Approx: 3-5 left (header + counters) | 2-5 right (badge/showcase placeholder)
home.push(sec(BONE,'large',[
  row([
    col('3-5',[
      tile('section-header',{
        eyebrow_show:true, eyebrow_text:'One unit · since 1974', eyebrow_color:CLARET,
        eyebrow_dot_color:CLARET, eyebrow_separator:'',
        headline_lines:[
          {text:'A regional club',color:TXT,italic:false},
          {text:'with a',color:TXT,italic:false},
          {text:'rich history',color:CLARET,italic:false},
        ],
        headline_font_family:'sans-serif', headline_font_size:56, headline_font_weight:'900',
        headline_align:'left', headline_inline:false,
        tagline_show:true,
        tagline_text:`From a handful of friends on a muddy field to eight competitive teams across men's, women's and youth football — Verdano FC is built on the people who keep showing up.`,
        tagline_text_italic:false, tagline_text_color:TXTSOFT, tagline_text_size:16.5,
        layout:'stack', gap:20,
      }),
      tile('counter',{
        number:'50', suffix:'', prefix:'', label:'Years of football',
        text_color:TXT, number_color:PITCH, number_font_size:'46', number_font_weight:'900',
        label_color:TXTFAINT, label_font_size:'12',
        bg_type:'color', bg_color:'transparent', padding:'0', border_radius:'0',
      }),
    ]),
    col('2-5',[
      tile('section-header',{
        eyebrow_show:false,
        headline_lines:[
          {text:'Est.',color:PITCH,italic:false},
          {text:'1974',color:VOLT,italic:false},
        ],
        headline_font_family:'sans-serif', headline_font_size:64, headline_font_weight:'900',
        headline_align:'center',
        tagline_show:true, tagline_text:'Established',
        tagline_text_italic:false, tagline_text_color:TXTFAINT, tagline_text_size:12,
        layout:'center', gap:8,
      }),
    ]),
  ],{gap:52, vertical_align:'center'}),
  row([
    col('1-3',[ tile('counter',{
      number:'50', suffix:'', prefix:'', label:'Years of football',
      text_color:TXT, number_color:PITCH, number_font_size:'46', number_font_weight:'900',
      label_color:TXTFAINT, label_font_size:'12',
      bg_type:'color', bg_color:'transparent', padding:'8', border_radius:'0',
    }) ]),
    col('1-3',[ tile('counter',{
      number:'8', suffix:'', prefix:'', label:'Competitive teams',
      text_color:TXT, number_color:PITCH, number_font_size:'46', number_font_weight:'900',
      label_color:TXTFAINT, label_font_size:'12',
      bg_type:'color', bg_color:'transparent', padding:'8', border_radius:'0',
    }) ]),
    col('1-3',[ tile('counter',{
      number:'600', suffix:'+', prefix:'', label:'Active members',
      text_color:TXT, number_color:PITCH, number_font_size:'46', number_font_weight:'900',
      label_color:TXTFAINT, label_font_size:'12',
      bg_type:'color', bg_color:'transparent', padding:'8', border_radius:'0',
    }) ]),
  ],{gap:24}),
]));

// 4) TEAMS — 3 card squadre con info-cards (image-free, overlay-style)
// Blueprint: .team{radius:20, aspect 3/3.5, overlay grad, team__k=volt label, team__t=display 900 34px}
home.push(sec(BONE,'large',[
  row([ col('1-1',[
    shead('One team. One mission. One colour.','Our teams',null,'left'),
  ]) ]),
  row([ col('1-1',[ tile('info-cards',{
    container_bg:{type:'solid',color:'transparent'}, container_padding:0, container_gap:16,
    columns:3, items_gap:16,
    card_bg:{type:'solid',color:PITCH}, card_color:'rgba(255,255,255,.62)',
    card_radius:R(20), card_padding:30,
    show_icon:false, show_counter:true, show_counter_label:true, show_arrow:true,
    show_footer:false, show_media:false,
    counter_shape:'circle', counter_color:VOLT, counter_bg:PITCH3,
    title_color:WHITE,
    title_font_family:'sans-serif', title_size:34, title_weight:'900', title_italic:false,
    description_size:13.5, counter_size:12,
    items:[
      {counter:'3', counter_label:'squads', title:'Men',
        description:`First, Second and Third men's teams. Competing in regional and provincial leagues.`},
      {counter:'1', counter_label:'squad',  title:'Women',
        description:`Our first women's squad, fighting for promotion in the regional women's league.`},
      {counter:'4', counter_label:'U14–U21', title:'Youth',
        description:'Four youth teams from Under 14 to Under 21, developing the next generation of Verdano players.'},
    ],
    card_hover_effect:'lift',
  }) ]) ]),
]));

// 5) NEXT MATCHES — info-cards su sfondo PITCH
// Blueprint: .fix{bg:pitch-2, border:1px line-dk, radius:18, padding:22}
// Segnalato: MatchFixtures non esiste come tile nativo — approssimazione con info-cards
home.push(sec(PITCH,'large',[
  row([ col('1-1',[
    sheadDark('Greenfield Park & away','Next matches',null),
  ]) ]),
  row([ col('1-1',[ tile('info-cards',{
    container_bg:{type:'solid',color:'transparent'}, container_padding:0, container_gap:16,
    columns:3, items_gap:16,
    card_bg:{type:'solid',color:PITCH2}, card_color:'rgba(255,255,255,.55)',
    card_radius:R(18), card_padding:24,
    show_icon:true, show_counter:false, show_arrow:false, show_footer:true, show_media:false,
    icon_color:VOLT, icon_bg_color:'rgba(200,255,60,.16)', title_color:WHITE,
    title_font_family:'sans-serif', title_size:16, title_weight:'800', title_italic:false,
    description_size:13, counter_size:13,
    items:[
      {icon:'shield-check', title:'Verdano FC vs Real Alta',
        description:'Sat, 14.03 · 15:00 · Verdano Park · Super League Matchday 04',
        footer_text:`First Men's Team`, footer_dot_color:VOLT},
      {icon:'shield-check', title:'SC Bendfeld vs Verdano FC',
        description:`Sat, 14.03 · 17:30 · Walter Field · Women's League Matchday 04`,
        footer_text:`First Women's Team`, footer_dot_color:VOLT},
      {icon:'shield-check', title:'Redwood City vs Verdano FC',
        description:'Sun, 08.03 · 13:30 · Redwood Park · Youth Elite Matchday 03 · 4:3',
        footer_text:'Under 14 Team · Full time', footer_dot_color:CLARET},
    ],
    card_hover_effect:'none',
    card_border:'1px solid rgba(255,255,255,.1)',
  }) ]) ]),
]));

// 6) MATCHDAY BUILDER — tile Builder (stepper +/− interattivi, totale live)
// zone_accent: #c8ff3c (VOLT), zone_on: #0a2a1e (PITCH) — dal blueprint HTML.
// card_bg: PAPER (bianco), card_border: LINE. Sfondo BONE.
home.push(sec(BONE,'large',[
  row([ col('1-1',[ tile('builder',{
    eyebrow:     'Saturday, 3pm',
    heading:     'Build your matchday',
    intro:       `Sort the whole crew before kick-off — tickets, a scarf, a pie at half-time. The total adds up as you go.`,
    currency:    `€`,
    cap:         0,
    total_label: 'Total',
    count_label: 'items',
    cta_text:    'Go to checkout',
    cta_url:     '#matchday',
    zone_accent: VOLT,
    zone_on:     PITCH,
    card_bg:     PAPER,
    card_border: LINE,
    align:       'left',
    items:[
      { name:'Adult Ticket',     price:'32', note:'South Stand',    start:0 },
      { name:'Junior Ticket',    price:'14', note:'Under 16',       start:0 },
      { name:'Home Scarf',       price:'22', note:`This season's`,  start:0 },
      { name:'Matchday Pie',     price:'5',  note:'Steak & ale',    start:0 },
      { name:'Programme',        price:'4',  note:`Collector's`,    start:0 },
      { name:'Pint & Soft Drink',price:'9',  note:'Concourse bar',  start:0 },
    ],
  }) ]) ]),
]));

// 7) MEMBERSHIP CTA — singolo pulsante (blueprint: 1 sola CTA) -----------------
// Blueprint: .vd-cta h2 "Become a member / of our club" con .v=volt su "club"
home.push(sec(PITCH,'large',[ row([ col('1-1',[ tile('cta-banner',{
  headline:'Become a member', headline_accent:'of our club', headline_accent_italic:true,
  subtitle:null,
  cta_text:'Go to membership', cta_url:'#membership',
  bg:{type:'solid',color:PITCH}, text_color:WHITE, accent_color:VOLT, subtitle_color:'rgba(255,255,255,.62)',
  cta_bg:VOLT, cta_color:PITCH, cta_radius:R(999), cta_size:15,
  headline_font_family:'sans-serif', headline_size:72, headline_weight:'900',
  subtitle_size:16,
  layout:'stack', vertical_align:'center', banner_radius:R(0), banner_padding:80,
  eyebrow_text:'Membership', eyebrow_color:VOLT,
}) ]) ]) ]));

// 8) PARTNERS — trust-strip pill ------------------------------------------
// Blueprint: .vd-partners{bg:paper, border-block:1px solid line}; partner-row flex label|logos
// 5 partner placeholder logo boxes (bone-2 bg, opacity .8)
home.push(sec(PAPER,'small',[
  row([ col('1-1',[ caption('Main partners',TXTFAINT) ]) ]),
  row([ col('1-1',[ tile('trust-strip',{
    items:[
      {text:'Greenfield Energy'},{text:'Verdano Motors'},{text:'SportLife Group'},
      {text:'City Council'},{text:'MediaNord'},
    ],
    variant:'pill', separator_char:'', align:'center', flow:'wrap', gap:16,
    font_family:'sans-serif', text_color:TXTFAINT, text_size:13,
    pill_bg:BONE2, pill_border:LINE, pill_text_color:TXTSOFT,
  }) ]) ], {gap:16}),
]));

// EMIT -------------------------------------------------------------------
K.emit({
  slug:'verdano', name:'Verdano FC',
  tags:['sports','football','club','fitness','health'],
  description:`Verdano FC — regional football club. Pitch green + volt lime, Archivo (display) + Work Sans (body). Hero showcasing club stats, news cards, club intro con counter, teams, fixtures e matchday builder (tile Builder nativo, stepper +/−, totale live). Tema Health & Fitness.`,
  colors:{
    primary:VOLT, primary_contrast:PITCH,
    secondary:CLARET, secondary_contrast:WHITE,
    muted:BONE2, muted_contrast:TXT,
    text:TXT, text_muted:TXTFAINT,
    background:BONE, border:LINE, link:CLARET,
  },
  css_disp:`"Archivo", -apple-system, sans-serif`,
  css_sans:`"Work Sans", -apple-system, sans-serif`,
  heading_weight:'900', heading_line_height:'1.0',
  google_fonts:['Archivo','Work Sans'],
  logo_variant:'dark',
  menu:[
    {title:'News',url:'#news'},
    {title:'Teams',url:'#teams'},
    {title:'Matches',url:'#matches'},
    {title:'The Club',url:'#intro'},
    {title:'Facility',url:'#'},
    {title:'Contact',url:'#'},
  ],
  header:{ bg:PITCH, text_color:'rgba(255,255,255,.66)', sticky_bg:'rgba(10,42,30,.9)', logo_width:130 },
  footer:{
    bg:PITCH,
    headColor:WHITE,
    brand:{
      name:'Verdano FC',
      tagline:'Mainstreet 123, 20100 Verdano. A regional football club, powered by its community.',
    },
    columns:[
      {title:'The Club', links:['History','Sponsors','Membership','Facility']},
      {title:'News',     links:['Club','Events','Matches','People']},
      {title:'Men teams',links:['First Team','Second Team','Third Team']},
      {title:'Youth teams',links:['Under 21','Under 18','Under 16','Under 14']},
    ],
    bottom:{left:'© 2026 Verdano FC — an OLOtheme demo.', right:'Built with OLObuild'},
  },
  cursor:{blend_mode:'exclusion', ring_color:VOLT, dot_color:VOLT},
}, home);
