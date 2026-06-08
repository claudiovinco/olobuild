/* Ledger v2 — iterazione pixel-perfect. Tile vere, colori espliciti, font del tema. */
const fs=require('fs'), path=require('path');
const BG='#16202a', BG2='#192531', PANEL='#1f2d3a', INK='#0d141b';
const TEAL='#4fb3a6', TEALTINT='rgba(79,179,166,.14)', LIME='#a6d96a', CREAM='#eaf1f0', TXT='#a3b4bf', DIM='#697b88';
const R=(n)=>({tl:n,tr:n,br:n,bl:n,linked:true});
let n=0; const id=(p)=>`lg2-${p}-${++n}`;
const sec=(color,padding,children,extra={})=>({id:id('se'),type:'section',settings:Object.assign({style:'default',width:'large',padding},extra),style:{bg:{type:'solid',color}},advanced:{},children});
const row=(children,settings={})=>({id:id('ro'),type:'row',settings,style:{},advanced:{},children});
const col=(width,children,settings={})=>({id:id('co'),type:'column',settings:Object.assign({width},settings),style:{},advanced:{},children});
const tile=(type,settings)=>({id:id(type),type,settings,style:{},advanced:{},children:[]});
const home=[];

// 1) HERO — hero-split. showcase = KPI: number=ETICHETTA (piccola), text=VALORE (grande).
home.push(sec(BG,'large',[ row([ col('1-1',[ tile('hero-split',{
  eyebrow_text:'ACCOUNTING · TAX · ADVISORY', eyebrow_dot_color:TEAL, eyebrow_color:TEAL,
  headline_lines:[ {text:'Books done.',color:CREAM,italic:false},{text:'Tax sorted.',color:CREAM,italic:false},{text:'Stress gone.',color:TEAL,italic:false} ],
  headline_font_family:'sans-serif', headline_font_size:64, headline_line_height:1.04, headline_font_weight:'800', headline_align:'left',
  subhead:'Ledger pairs a dedicated accountant with software that handles the busywork — so your numbers are always right and you can get back to the business.',
  subhead_color:TXT, subhead_size:18, subhead_italic:false, subhead_max_width:460,
  cta1_text:'Get started', cta1_url:'#', cta1_bg:TEAL, cta1_color:INK, cta1_size:15, cta1_radius:R(10), cta1_radius_hover:R(10),
  cta2_text:'', cta2_url:'#', stats:[],
  showcase_enabled:true, showcase_bg:{type:'solid',color:PANEL}, showcase_padding:24, showcase_radius:R(16), showcase_radius_hover:R(16),
  showcase_badge_text:'LEDGER · DASHBOARD', showcase_badge_dot:TEAL, showcase_badge_bg:'#16202a', showcase_badge_color:CREAM,
  showcase_items:[
    {number:'Cash in bank',text:'£48,210',italic:false,text_color:CREAM,bg:{type:'solid',color:BG2}},
    {number:'Profit (YTD)',text:'£21,640',italic:false,text_color:LIME,bg:{type:'solid',color:BG2}},
    {number:'Tax set aside',text:'£9,180',italic:false,text_color:CREAM,bg:{type:'solid',color:BG2}},
    {number:'Next VAT due',text:'in 26d',italic:false,text_color:CREAM,bg:{type:'solid',color:BG2}},
  ],
  showcase_card_radius:R(11), showcase_card_radius_hover:R(11), showcase_card_shadow:'none',
  showcase_caption_left:'REAL-TIME', showcase_caption_right:'ALWAYS RECONCILED', showcase_hover_effect:'none',
  split_ratio:'1.1fr .9fr', gap:48, min_height:0, tile_padding:{top:0,right:0,bottom:0,left:0},
}) ]) ]) ]));

// 2) TRUST — label centrata + trust-strip
home.push(sec(BG2,'small',[
  row([ col('1-1',[ tile('section-header',{
    eyebrow_show:false,
    headline_lines:[ {text:'Trusted by 4,000+ small businesses',color:DIM,italic:false} ],
    headline_font_family:'sans-serif', headline_font_size:12, headline_font_weight:'700', headline_align:'center', headline_inline:false,
    tagline_show:false, layout:'center', gap:0,
  }) ]) ]),
  row([ col('1-1',[ tile('trust-strip',{
    items:[ {text:'NORTHWIND'},{text:'MAPLE & CO'},{text:'BRIGHTON'},{text:'KESTREL'},{text:'ASHBY'},{text:'LUMEN'} ],
    variant:'pill', separator_char:'', align:'center', flow:'wrap', gap:16,
    font_family:'sans-serif', text_color:DIM, text_size:13,
    pill_bg:'rgba(234,241,240,.05)', pill_border:'rgba(234,241,240,.1)', pill_text_color:DIM,
  }) ]) ]),
]));

// helper section-header (2 righe: cream + accent teal)
const shead=(eyebrow,l1,l2)=>tile('section-header',{
  eyebrow_show:true, eyebrow_text:eyebrow, eyebrow_color:TEAL, eyebrow_dot_color:TEAL, eyebrow_separator:'',
  headline_lines:[ {text:l1,color:CREAM,italic:false},{text:l2,color:TEAL,italic:false} ],
  headline_font_family:'sans-serif', headline_font_size:46, headline_font_weight:'800', headline_align:'center', headline_inline:true,
  tagline_show:false, layout:'center', gap:18,
});

// 3) SERVICES — section-header + info-cards (icona nel quadrato teal, titolo cream)
home.push(sec(BG,'large',[
  row([ col('1-1',[ shead('WHAT WE HANDLE','Everything','numbers') ]) ]),
  row([ col('1-1',[ tile('info-cards',{
    container_bg:{type:'solid',color:'transparent'}, container_padding:0, container_gap:16, columns:3, items_gap:16,
    card_bg:{type:'solid',color:PANEL}, card_color:DIM, card_radius:R(14), card_padding:28,
    show_icon:true, show_counter:false, show_arrow:false, show_footer:false, show_media:false,
    icon_color:TEAL, icon_bg_color:TEALTINT, title_color:CREAM,
    title_font_family:'sans-serif', title_size:21, title_weight:'800', title_italic:false, description_size:15,
    items:[
      {icon:'book', title:'Bookkeeping', description:'Receipts in, reconciled automatically, checked by a human. Always up to date.'},
      {icon:'shield-check', title:'Tax & filing', description:'VAT, self-assessment, corporation tax — filed on time, optimised, no nasty surprises.'},
      {icon:'trending-up', title:'Advisory', description:'Quarterly calls with your accountant to plan, forecast and make better calls.'},
    ],
    card_hover_effect:'lift',
  }) ]) ]),
]));

// 4) STAT STRIP — counter (sfondo BG continuo, bordi laterali via sezione, suffisso teal)
const stat=(number,suffix,label)=>col('1-4',[ tile('counter',{
  number, suffix, prefix:'', label, icon_emoji:'',
  text_color:CREAM, number_color:TEAL, number_font_size:'48', number_font_weight:'800', label_color:DIM, label_font_size:'13',
  bg_type:'color', bg_color:'transparent', padding:'8', border_radius:'0',
}) ]);
home.push(sec(BG,'small',[ row([
  stat('4,000','+','Businesses'), stat('48','h','Avg. switch time'), stat('98','%','Would recommend'), stat('0','','Late filings, ever'),
], {gap:24}) ]));

// 5) HOW IT WORKS — process-steps con card (bg panel + bordo line + radius 14 + padding 28)
// Il CSS .lg-step ha background:var(--panel) + border:1px solid var(--line) + border-radius:14px + padding:28px
// Il numero è un cerchio 36px con bg rgba(79,179,166,.14) e colore teal → number_style:'circle'
// Sezione su sfondo BG2 (.lg-sec.panel = background:var(--bg-2))
home.push(sec(BG2,'large',[
  row([ col('1-1',[ shead('HOW IT WORKS','Up and running','in a day') ]) ]),
  row([ col('1-1',[ tile('process-steps',{
    columns:3, gap:18, align:'left', auto_number:true, item_gap:14,
    number_style:'circle', number_color:TEAL, number_bg:TEALTINT, number_size:36,
    number_font:'sans-serif', number_weight:'800',
    title_color:CREAM, title_size:20, title_font:'sans-serif', title_weight:'800',
    desc_color:DIM, desc_size:14,
    card_bg:PANEL, card_border:'rgba(234,241,240,.1)', card_radius:R(14), card_padding:28,
    items:[
      {number:'1', title:'Connect', description:`Link your bank and tools in minutes. We pull in the history and tidy it up.`},
      {number:'2', title:'Meet your accountant', description:`A real, named person who knows your business — not a call-centre queue.`},
      {number:'3', title:'Relax', description:`We keep the books, file on time, and nudge you only when it matters.`},
    ],
  }) ]) ]),
]));

// 6) PRICING — section-header + 3 pricing
const plan=(plan_name,price,features,cta_text,popular)=>col('1-3',[ tile('pricing',{
  plan_name, price, currency:'£', currency_size:'16', currency_position:'before', period:'/mo',
  features, feature_dividers:false, check_style:'checkmark', check_size:'15',
  is_popular:popular, badge_text:'Most popular', badge_bg_color:TEAL, badge_text_color:INK,
  bg_color:PANEL, text_color:TXT, price_color:CREAM, accent_color:TEAL,
  cta_text, cta_url:'#', cta_bg_color: popular?TEAL:'rgba(255,255,255,.05)', cta_text_color: popular?INK:'#ffffff', cta_radius:'9', cta_width:'100', border_radius:'16',
}) ]);
home.push(sec(BG,'large',[
  row([ col('1-1',[ shead('PRICING','One flat fee,','no shocks') ]) ]),
  row([
    plan('Sole trader','45','Bookkeeping\nSelf-assessment\nThe app','Start',false),
    plan('Limited company','95','Everything in Sole trader\nVAT & payroll\nQuarterly advisory call\nDedicated accountant','Get started',true),
    plan('Scale','180','Everything in Limited\nManagement accounts\nMonthly check-ins','Talk to us',false),
  ], {gap:32,vertical_align:'stretch'}),
]));

// 7) TESTIMONIAL — (.lg-sec.panel = BG2)
// Blueprint: <q>”I went from dreading my accounts to not thinking about them at all. That's worth every penny.”</q>
// By: Owner, Maple & Co · Ledger client since 2023
home.push(sec(BG2,'large',[ row([ col('1-1',[ tile('testimonial',{
  quote:`“I went from dreading my accounts to not thinking about them at all. That’s worth every penny.”`,
  author_name:'Owner, Maple & Co', author_role:'Ledger client since 2023', rating:'0',
  layout:'single', show_line:false, bg_color:'transparent', text_color:CREAM, border_radius:'0', avatar:'',
  quote_font_size:'36', quote_font_weight:'700',
}) ]) ]) ]));

// 8) PROJECTOR
home.push(sec(BG,'large',[ row([ col('1-1',[ tile('projector',{
  eyebrow:'Quick maths', heading:'What should I set aside?',
  intro:"Drag to your monthly profit. We'll show roughly what to park for tax so quarter-end is never a surprise — sole-trader rule of thumb, ~27%.",
  min:'1000', max:'20000', step:'500', value:'6000', rate:'0', years:'0.27', currency:'£',
  input_label:'Monthly profit', out_caption:'Set aside each month',
  note:'Indicative only — your real rate depends on allowances & structure. Ledger sets the exact figure for you.',
  show_contrib:true, zone_accent:TEAL, align:'left', tile_padding:{top:52,right:52,bottom:52,left:52}, border_radius:'16', shadow:'sm',
}) ]) ]) ]));

// 9) CTA — 2 bottoni: Get started (teal) + See services (ghost)
// Blueprint: headline "Switch in a <span class="t">day</span>, not a quarter"
//   Il tile supporta headline + headline_accent (due parti consecutive).
//   Resa più fedele: headline = "Switch in a" (cream), headline_accent = "day, not a quarter" (teal)
//   NOTA: nel blueprint solo "day" è teal; il tile colora l'intera accent → approssimazione accettabile.
//   Box: background = linear-gradient(160deg,rgba(79,179,166,.16),rgba(166,217,106,.05)) su sfondo BG.
home.push(sec(BG,'large',[ row([ col('1-1',[ tile('cta-banner',{
  headline:`Switch in a`,
  headline_accent:`day, not a quarter`,
  headline_accent_italic:false,
  subtitle:`We do the heavy lifting of moving your accounts across — free, and without the drama.`,
  cta_text:`Get started`, cta_url:`#`,
  cta2_text:`See services`, cta2_url:`#`, cta2_bg:`rgba(255,255,255,.05)`, cta2_color:`#ffffff`, cta2_border:`rgba(255,255,255,.16)`,
  bg:{type:'gradient',gradient:`linear-gradient(160deg,rgba(79,179,166,.16),rgba(166,217,106,.05))`},
  text_color:CREAM, accent_color:TEAL, subtitle_color:TXT,
  cta_bg:TEAL, cta_color:INK, cta_radius:R(9), cta_size:15,
  headline_font_family:`sans-serif`, headline_size:44, headline_weight:`800`, subtitle_size:17,
  layout:`stack`, vertical_align:`center`, banner_radius:R(20), banner_padding:64,
}) ]) ]) ]));

const dir=path.join(__dirname,'assets','data','themes','ledger');
fs.writeFileSync(path.join(dir,'homepage.json'), JSON.stringify(home));
const count=(arr)=>{let c=0;const w=(ns)=>ns.forEach(x=>{c++;if(x.children)w(x.children)});w(arr);return c;};
const types={}; const wt=(ns)=>ns.forEach(x=>{types[x.type]=(types[x.type]||0)+1;if(x.children)wt(x.children)}); wt(home);
console.log(`Ledger v2 (iter): ${home.length} sez | nodi ${count(home)} | text-block ${types['text-block']||0}`);
console.log('tile:', JSON.stringify(types));
