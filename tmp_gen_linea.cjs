/* Linea — ricomposizione TILE-PURE (image-free). Beauty & Fashion concept boutique.
   Warm near-black + camel. Cardo (display) + Epilogue (body). */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('ln');

// ── PALETTE (da :root linea.css) ─────────────────────────────────────────────
const BG='#211f1d', BG2='#262320', PANEL='#2c2925', PANEL2='#36322d', INK='#161410';
const CAMEL='#bd9a6f', CAMELL='#cfb088', CREAM='#efe9df', TXT='#b2a99c', DIM='#827a6d';
const LINE='rgba(239,233,223,.13)', LINE2='rgba(189,154,111,.42)', WHITE='#ffffff';
const CAMELT='rgba(189,154,111,.14)';

const home=[];

// helper: section-header centrato con eyebrow + headline accentata (2 righe inline)
const shead=(eyebrow,l1,accent,intro)=>tile('section-header',{
  eyebrow_show:true, eyebrow_text:eyebrow, eyebrow_color:CAMEL, eyebrow_dot_color:CAMEL, eyebrow_separator:'',
  headline_lines:[ {text:l1,color:CREAM,italic:false},{text:accent,color:CAMEL,italic:true} ],
  headline_font_family:'serif', headline_font_size:50, headline_font_weight:'400', headline_align:'center', headline_inline:true,
  tagline_show:!!intro, tagline_text:intro||'', tagline_text_italic:false, tagline_text_color:DIM, tagline_text_size:16,
  layout:'center', gap:16,
});

// 1) HERO
// Blueprint: .ln-hero — media fullscreen (aspect 21/10, min-h 560px) con overlay
// gradient, testo sovrapposto assoluto. Image-free → hero-split col pannello destra
// come placeholder media, testo a sinistra. Best-effort per la composizione image-free.
home.push(sec(BG,'large',[ row([ col('1-1',[ tile('hero-split',{
  eyebrow_text:'Concept boutique', eyebrow_dot_color:CAMEL, eyebrow_color:CAMEL,
  headline_lines:[
    {text:'Fewer things,',color:WHITE,italic:false},
    {text:'chosen',color:CAMEL,italic:true},
    {text:'well.',color:WHITE,italic:false},
  ],
  headline_font_family:'serif', headline_font_size:80, headline_line_height:1.08, headline_font_weight:'400', headline_align:'left',
  subhead:`A tight edit of clothing, objects and scent from independent makers — considered, never crowded.`,
  subhead_color:CREAM, subhead_size:17, subhead_italic:false, subhead_max_width:400,
  cta1_text:'Shop the edit', cta1_url:'#edit', cta1_bg:CAMEL, cta1_color:INK, cta1_size:12, cta1_radius:R(0), cta1_radius_hover:R(0),
  cta2_text:'Our story', cta2_url:'#story', cta2_bg:'transparent', cta2_color:CREAM, cta2_border:LINE2, cta2_size:12, cta2_radius:R(0), cta2_radius_hover:R(0),
  stats:[],
  showcase_enabled:true, showcase_bg:{type:'solid',color:PANEL}, showcase_padding:32, showcase_radius:R(2), showcase_radius_hover:R(2),
  showcase_badge_text:'EDITORIAL · SS 2026', showcase_badge_dot:CAMEL, showcase_badge_bg:INK, showcase_badge_color:CREAM,
  showcase_items:[
    {number:'Boiled Wool Coat',text:`€420`,italic:false,text_color:CAMEL,bg:{type:'solid',color:BG2}},
    {number:'Stoneware Vase',text:`€86`,italic:false,text_color:CREAM,bg:{type:'solid',color:BG2}},
    {number:'Washed Linen Shirt',text:`€140`,italic:false,text_color:CREAM,bg:{type:'solid',color:BG2}},
    {number:'No. 4 Eau de Parfum',text:`€95`,italic:false,text_color:CREAM,bg:{type:'solid',color:BG2}},
  ],
  showcase_card_radius:R(2), showcase_card_radius_hover:R(2), showcase_card_shadow:'none',
  showcase_caption_left:'NEW IN', showcase_caption_right:'THIS WEEK', showcase_hover_effect:'none',
  split_ratio:'1.2fr .8fr', gap:56, min_height:0, tile_padding:{top:0,right:0,bottom:0,left:0},
}) ]) ]) ]));

// 2) PRODUCT GRID
// Blueprint: 4 card prodotto (.ln-prod) — media 3/4 ratio + cat label + nome + prezzo.
// product-cards tile usa monogramma lettera (non adatto). Best-effort con info-cards.
// .ln-prod — no card bg/border; .ln-prod h3 serif 22px; .ln-prod__cat 10px uppercase;
// .ln-prod__price camel 14px. 4 colonne, gap 24px.
home.push(sec(BG,'large',[
  row([ col('1-1',[ shead('New in',`This week's `,`arrivals`,'') ]) ]),
  row([ col('1-1',[ tile('info-cards',{
    container_bg:{type:'solid',color:'transparent'}, container_padding:0, container_gap:24, columns:4, items_gap:24,
    card_bg:{type:'solid',color:'transparent'}, card_color:DIM, card_radius:R(0), card_padding:0,
    show_icon:false, show_counter:true, show_counter_label:false, show_arrow:false, show_footer:true, show_media:false,
    counter_shape:'plain', counter_color:DIM, counter_size:10, counter_bg:'transparent',
    title_color:CREAM, title_font_family:'serif', title_size:22, title_weight:'400', title_italic:false,
    description_size:13, description_color:DIM,
    items:[
      {counter:'Outerwear', title:'Boiled Wool Coat', description:`Made in Portugal — boiled wool, bone buttons, unlined. Will outlast everything else in your wardrobe.`, footer_text:`€420`, footer_dot_color:CAMEL, show_footer:true},
      {counter:'Object', title:'Stoneware Vase', description:`Wheel-thrown by a solo maker in Lisbon. Each piece unique, reactive glaze in warm stone.`, footer_text:`€86`, footer_dot_color:CAMEL, show_footer:true},
      {counter:'Ready-to-wear', title:'Washed Linen Shirt', description:`Prewashed Italian linen, slight oversized, works as layer or standalone. Ivory.`, footer_text:`€140`, footer_dot_color:CAMEL, show_footer:true},
      {counter:'Scent', title:`No. 4 Eau de Parfum`, description:`Cedar, vetiver and quiet smoke. An independent perfumer's fourth release — sparse, precise, lasting.`, footer_text:`€95`, footer_dot_color:CAMEL, show_footer:true},
    ],
    card_hover_effect:'lift',
  }) ]) ]),
]));

// 3) CATEGORY TILES
// Blueprint: 3 card .ln-cat — min-height 380px, sfondo media (overlay gradient),
// testo in basso (padding 26px). Senza card bg visibile (dipende dall'immagine).
// Image-free → info-cards con icone. .ln-cat__b: h3 serif 28px, p 12px camel uppercase.
// Sfondo sezione .panel → BG2.
home.push(sec(BG2,'large',[
  row([ col('1-1',[ shead('By category','The ',`edit`,'') ]) ]),
  row([ col('1-1',[ tile('info-cards',{
    container_bg:{type:'solid',color:'transparent'}, container_padding:0, container_gap:16, columns:3, items_gap:16,
    card_bg:{type:'solid',color:PANEL2}, card_color:DIM, card_radius:R(0), card_padding:26,
    show_icon:true, show_counter:false, show_arrow:true, show_footer:false, show_media:false,
    icon_color:CAMEL, icon_bg_color:CAMELT,
    title_color:CREAM, title_font_family:'serif', title_size:28, title_weight:'400', title_italic:false,
    description_size:12, description_color:CAMEL,
    items:[
      {icon:'shirt', title:'Wear', description:'Clothing'},
      {icon:'lamp', title:'Live', description:`Objects & home`},
      {icon:'sparkles', title:'Scent', description:`Fragrance & skin`},
    ],
    card_hover_effect:'lift',
  }) ]) ]),
]));

// 4) BRAND STORY
// Blueprint: .ln-story — grid 1fr 1fr. Sinistra: pannello media (media = BG placeholder).
// Destra: .ln-story__c background:var(--panel), padding 40-80px, flex col, h2 serif 52px,
// due <p>, firma italic serif 23px, link-u "Read more".
// hero-split: split_ratio '1fr 1fr', gap:0 → pannello destra = testo, sinistra = showcase.
// La split_ratio '1fr 1fr' con showcase a sinistra approssima bene la struttura.
home.push(sec(BG,'large',[ row([ col('1-1',[ tile('hero-split',{
  eyebrow_text:'About Linea', eyebrow_dot_color:CAMEL, eyebrow_color:CAMEL,
  headline_lines:[
    {text:'A shop that ',color:CREAM,italic:false},
    {text:'says no',color:CAMEL,italic:true},
    {text:'for you',color:CREAM,italic:false},
  ],
  headline_font_family:'serif', headline_font_size:52, headline_line_height:1.1, headline_font_weight:'400', headline_align:'left',
  subhead:`We see thousands of things and choose a few. Everything in Linea is here because it's beautifully made, will last, and earns its place on your shelf.\n\nWe work directly with small makers, in small runs, and we'll tell you the story behind every piece.\n\n— Sora & Idris, founders`,
  subhead_color:TXT, subhead_size:16, subhead_italic:false, subhead_max_width:460,
  cta1_text:'Read more', cta1_url:'#', cta1_bg:'transparent', cta1_color:CREAM, cta1_border:LINE2, cta1_size:12, cta1_radius:R(0), cta1_radius_hover:R(0),
  cta2_text:'', cta2_url:'', cta2_bg:'transparent', cta2_color:CREAM,
  stats:[],
  showcase_enabled:true, showcase_bg:{type:'solid',color:PANEL}, showcase_padding:40, showcase_radius:R(0), showcase_radius_hover:R(0),
  showcase_badge_text:'THE SHOP', showcase_badge_dot:CAMEL, showcase_badge_bg:INK, showcase_badge_color:CREAM,
  showcase_items:[
    {number:'Small runs',text:"Every piece is made in limited quantity — when it's gone, it's gone.",italic:true,text_color:TXT,bg:{type:'solid',color:BG2}},
    {number:'Est. 2018',text:"Eight years curating what's worth owning.",italic:false,text_color:CREAM,bg:{type:'solid',color:BG2}},
    {number:'Independent makers',text:'We work directly with craftspeople, no middlemen.',italic:false,text_color:CREAM,bg:{type:'solid',color:BG2}},
  ],
  showcase_card_radius:R(0), showcase_card_radius_hover:R(0), showcase_card_shadow:'none',
  showcase_caption_left:'OUR STORY', showcase_caption_right:'EST. 2018',
  showcase_hover_effect:'none',
  split_ratio:'1fr 1fr', gap:0, min_height:0, tile_padding:{top:0,right:0,bottom:0,left:0},
}) ]) ]) ]));

// 5) STYLE FINDER — tile finder nativo (chip → result card)
// zone_accent / zone_on dal CSS inline --fx-zone-accent / --fx-zone-on.
// card_bg = PANEL, card_border = LINE2.
home.push(sec(BG2,'large',[
  row([ col('1-1',[ tile('finder',{
    eyebrow:'Personal edit',
    heading:'Find your edit',
    intro:'',
    zone_accent:'#bd9a6f',
    zone_on:'#161410',
    card_bg:PANEL,
    card_border:LINE2,
    align:'center',
    items:[
      {option:'Quiet minimal',    title:'The Essentials', text:`Stone, ecru and ink. Clean lines, the right weight of cotton, nothing shouting. Pieces that quietly do everything.`,                              meta:`12 pieces · from €90`,  cta_text:'', cta_url:'#', icon:''},
      {option:'Sharp & tailored', title:'The Tailored',   text:`A precise blazer, straight wool trouser and a crisp poplin shirt. Structure that reads considered, never stiff.`,                               meta:`9 pieces · from €140`,  cta_text:'', cta_url:'#', icon:''},
      {option:'Soft & relaxed',   title:'The Soft Edit',  text:`Washed linen, dropped shoulders and easy knits. Made for slow weekends and warm light.`,                                                         meta:`10 pieces · from €85`,  cta_text:'', cta_url:'#', icon:''},
      {option:'A statement',      title:'The One Thing',  text:`A single sculptural piece to build a look around — the coat, the dress, the bag people remember.`,                                               meta:`Hero pieces · from €220`, cta_text:'', cta_url:'#', icon:''},
    ],
  }) ]) ]),
]));

// 6) NEWSLETTER / CTA FINALE
// Blueprint: .ln-sec (no .panel) → sfondo BG. .ln-news__in centrato, max-w 600px.
// h2: "The <em>quiet</em> list" — serif, clamp(32px,4.6vw,56px).
// p: colore txt-dim, 16px, margin 16px 0 28px.
// form: input email + btn camel "Join". Bordo 1px LINE2 sul form.
// cta-banner layout:'stack' centrato. NO secondo bottone.
home.push(sec(BG,'large',[ row([ col('1-1',[ tile('cta-banner',{
  headline:'The', headline_accent:`quiet list`,headline_accent_italic:true,
  subtitle:`One considered email a month — new arrivals, maker stories and the occasional private preview.`,
  cta_text:'Join', cta_url:'#newsletter',
  cta2_text:'',
  bg:{type:'solid',color:BG}, text_color:CREAM, accent_color:CAMEL, subtitle_color:DIM,
  cta_bg:CAMEL, cta_color:INK, cta_radius:R(0), cta_size:12,
  headline_font_family:'serif', headline_size:56, headline_weight:'400', subtitle_size:16,
  layout:'stack', vertical_align:'center', banner_radius:R(0), banner_padding:80,
}) ]) ]) ]));

K.emit({
  slug:'linea', name:'Linea',
  tags:['beauty','fashion','ecommerce','boutique','lifestyle'],
  description:`Linea — concept boutique Beauty & Fashion. Warm near-black + camel. Cardo (display) + Epilogue (body). Editorial minimal, sezioni: hero, prodotti, categorie, brand story, style finder (tile finder nativo), newsletter.`,
  colors:{
    primary:CAMEL, primary_contrast:INK,
    secondary:CREAM, secondary_contrast:INK,
    muted:BG2, muted_contrast:TXT,
    text:TXT, text_muted:DIM,
    background:BG, border:LINE, link:CAMEL,
  },
  css_disp:`"Cardo", Georgia, serif`,
  css_sans:`"Epilogue", -apple-system, sans-serif`,
  heading_weight:'400', heading_line_height:'1.1',
  google_fonts:['Cardo','Epilogue'],
  logo_variant:'light',
  menu:[
    {title:'Shop',url:'#edit'},
    {title:'The edit',url:'#edit'},
    {title:'About',url:'#story'},
    {title:'Stockists',url:'#stockists'},
  ],
  header:{ bg:BG, text_color:DIM, sticky_bg:`rgba(33,31,29,.86)`, logo_width:120 },
  footer:{
    bg:INK, headColor:CREAM,
    brand:{name:'Linea', tagline:`A concept boutique. A tight edit of clothing, objects and scent from independent makers.`},
    columns:[
      {title:'Shop', links:['All','Wear','Live','Scent']},
      {title:'Linea', links:['About','Makers','Visit us']},
      {title:'Care', links:['Shipping','Returns','Contact']},
    ],
    bottom:{left:`© 2026 Linea — an OLOtheme demo.`, right:'Built with OLObuild'},
  },
  cursor:{ blend_mode:'exclusion', ring_color:CREAM, dot_color:CAMEL },
}, home);
