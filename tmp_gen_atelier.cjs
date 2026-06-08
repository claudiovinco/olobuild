/* Atelier Noir — ricomposizione TILE-PURE (image-free). Beauty & Fashion · black & gold luxury.
   RIFINITURA PIXEL-PERFECT v2:
   - Sezione 4 Featured Pieces → product-cards (lettera monogramma categoria)
   - Sezione 8 Newsletter → newsletter tile (form email nativo)
   - Ticker: separatore ✦, font serif, corsivo best-effort
   - Campaign: eyebrow "The Film" aggiunto
   - Sfondi alternati corretti: BG/BG2/PANEL per ogni sezione
   - Copy pixel-exact dal blueprint
*/
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('at');

const BG='#141414', BG2='#181818', PANEL='#1e1e1e', PANEL2='#262626', INK='#0c0c0c';
const GOLD='#c9a86a', GOLDL='#ddc089', IVORY='#efe9de';
const TXT='#b6b1a8', DIM='#7c776e', LINE='rgba(239,233,222,.12)', LINE2='rgba(201,168,106,.4)';
const WHITE='#ffffff', VTINT='rgba(201,168,106,.10)';

const home=[];

// helper: section-header centrato con eyebrow + headline accentato
const shead=(eyebrow,l1,accent,intro)=>tile('section-header',{
  eyebrow_show:true, eyebrow_text:eyebrow, eyebrow_color:GOLD, eyebrow_dot_color:GOLD, eyebrow_separator:'',
  headline_lines:[ {text:l1,color:IVORY,italic:false},{text:accent,color:GOLD,italic:true} ],
  headline_font_family:'serif', headline_font_size:52, headline_font_weight:'400', headline_align:'center', headline_inline:true,
  tagline_show:!!intro, tagline_text:intro||'', tagline_text_italic:false, tagline_text_color:DIM, tagline_text_size:16,
  layout:'center', gap:16,
});

// 1) HERO (image-free: showcase con stats atelier + copy editoriale)
home.push(sec(BG,'large',[ row([ col('1-1',[ tile('hero-split',{
  eyebrow_text:`Autumn / Winter ’26`, eyebrow_dot_color:GOLD, eyebrow_color:GOLD,
  headline_lines:[
    {text:'The',color:IVORY,italic:false},
    {text:'Nocturne',color:GOLD,italic:true},
    {text:'Collection',color:IVORY,italic:false}
  ],
  headline_font_family:'serif', headline_font_size:80, headline_line_height:1.0, headline_font_weight:'400', headline_align:'left',
  subhead:`Tailoring for the hours after dark. Cut in wool crêpe and silk, finished by hand in our Paris atelier.`,
  subhead_color:TXT, subhead_size:17, subhead_italic:false, subhead_max_width:480,
  cta1_text:'Shop the collection', cta1_url:'#collection', cta1_bg:GOLD, cta1_color:INK, cta1_size:12, cta1_radius:R(0), cta1_radius_hover:R(0),
  cta2_text:'View lookbook', cta2_url:'#lookbook', cta2_bg:'transparent', cta2_color:IVORY, cta2_border:LINE2, cta2_size:12, cta2_radius:R(0), cta2_radius_hover:R(0),
  stats:[],
  showcase_enabled:true, showcase_bg:{type:'solid',color:PANEL}, showcase_padding:32, showcase_radius:R(0), showcase_radius_hover:R(0),
  showcase_badge_text:'ATELIER · AT A GLANCE', showcase_badge_dot:GOLD, showcase_badge_bg:INK, showcase_badge_color:IVORY,
  showcase_items:[
    {number:'House founded',text:'1998',italic:false,text_color:GOLD,bg:{type:'solid',color:BG2}},
    {number:'Paris atelier',text:'16ème',italic:true,text_color:IVORY,bg:{type:'solid',color:BG2}},
    {number:'Pieces this season',text:'36',italic:false,text_color:IVORY,bg:{type:'solid',color:BG2}},
    {number:'Entirely hand-finished',text:'Yes',italic:false,text_color:IVORY,bg:{type:'solid',color:BG2}},
  ],
  showcase_card_radius:R(0), showcase_card_radius_hover:R(0), showcase_card_shadow:'none',
  showcase_caption_left:'NOCTURNE', showcase_caption_right:'A/W 26', showcase_hover_effect:'none',
  split_ratio:'1.2fr .8fr', gap:52, min_height:0, tile_padding:{top:0,right:0,bottom:0,left:0},
}) ]) ]) ]));

// 2) TICKER — marquee serif italic (nomi collezione, sep ✦)
// ⚠️ BEST-EFFORT: il tile marquee non supporta font-style:italic nativo.
// Il CSS blueprint usa Bodoni Moda italic 24px. Qui usiamo font_size 24, font_weight 400,
// text_transform none. Il font serif viene dalla cascata del tema.
home.push(sec(BG2,'small',[ row([ col('1-1',[ tile('marquee',{
  content_type:'text',
  text_items:`Ready-to-wear — Made-to-measure — Hand-finished in Paris — Since 1998 — The Nocturne Collection`,
  separator:' ✶ ',
  speed:'40', direction:'left', pause_hover:false, gap:'48',
  bg_color:BG2, text_color:DIM,
  font_size:'24', font_weight:'400', letter_spacing:'0', text_transform:'none',
  height:'60',
  border_top:'1', border_bottom:'1', border_color:LINE,
  full_width:true,
}) ]) ]) ]));

// 3) LOOKBOOK GALLERY (image-free)
// ⚠️ BEST-EFFORT: ProductGallery (griglia asimmetrica 12-col con hover-caption) = NEW tile non esistente.
// Approssimazione con info-cards 3+2 col, pannelli astratti, counter = numero look, titolo serif.
home.push(sec(BG,'large',[
  row([ col('1-1',[ shead('The Lookbook','Dressed for the ','night','') ]) ]),
  row([ col('1-1',[ tile('info-cards',{
    container_bg:{type:'solid',color:'transparent'}, container_padding:0, container_gap:20, columns:3, items_gap:20,
    card_bg:{type:'solid',color:PANEL}, card_color:DIM, card_radius:R(0), card_padding:32,
    show_icon:false, show_counter:true, show_counter_label:true, show_arrow:false, show_footer:false, show_media:false,
    counter_shape:'plain', counter_color:GOLD, counter_bg:'transparent', title_color:IVORY,
    title_font_family:'serif', title_size:24, title_weight:'400', title_italic:true, description_size:14,
    counter_size:11,
    items:[
      {counter:'LOOK 01', counter_label:'', title:`The Crêpe Coat`, description:`Long-line tailored coat in double-faced wool crêpe. A clean shoulder, a fluid hem, a single button.`},
      {counter:'LOOK 02', counter_label:'', title:'Silk Column Dress', description:'Bias-cut silk, liquid drape. Worn to openings, premières, and anywhere the light is low.'},
      {counter:'LOOK 03', counter_label:'', title:'The Tuxedo', description:'Le Smoking revisited for the season. Peak lapel, fluid trouser, finished with hand-sewn seams.'},
      {counter:'LOOK 04', counter_label:'', title:'Cashmere Set', description:'Two-ply Mongolian cashmere, relaxed and refined. The weight of the real thing.'},
      {counter:'LOOK 05', counter_label:'', title:'Gilt Hardware', description:`Accessories in the house vocabulary — gold-toned hardware, hand-stitched leather, worn quietly.`},
    ],
    card_hover_effect:'lift',
  }) ]) ]),
]));

// 4) FEATURED PIECES — product-cards (lettera monogramma = iniziale categoria, prezzo nel CTA)
// ⚠️ BEST-EFFORT: ProductGrid fashion (media 3/4 + hover quick-add) = NEW tile non esistente.
// Approssimazione con product-cards: lettera monogramma = iniziale categoria, brand = category label,
// title = nome pezzo, description = copy, cta_text = prezzo.
home.push(sec(BG2,'large',[
  row([ col('1-1',[ shead('From the collection','The','pieces','') ]) ]),
  row([ col('1-1',[ tile('product-cards',{
    columns:4, gap:22,
    card_bg:{type:'solid',color:PANEL}, card_color:DIM, card_radius:R(0), card_radius_hover:R(0),
    card_shadow:'none', card_padding:24,
    top_aspect_ratio:'3/4', top_padding:24,
    letter_font_family:'serif', letter_size:140, letter_italic:true, letter_align:'center',
    show_screenshot_label:false,
    brand_size:10, brand_letter_spacing:0.16,
    title_font_family:'serif', title_size:21, title_weight:'400',
    description_size:13, cta_size:11, cta_arrow:true,
    card_hover_effect:'lift',
    items:[
      {
        letter:'O', letter_color:GOLD,
        top_bg:{type:'solid',color:BG}, screenshot_label:'',
        brand_label:'OUTERWEAR', brand_color:DIM,
        show_badge:true, badge_text:'New', badge_bg:INK, badge_color:GOLD,
        title:`Crêpe Tailored`, title_accent:'Coat', title_accent_italic:true,
        description:`Wool crêpe, hand-finished. A defining piece for the season.`,
        cta_text:'€1,290', cta_url:'#',
      },
      {
        letter:'S', letter_color:IVORY,
        top_bg:{type:'solid',color:BG}, screenshot_label:'',
        brand_label:'EVENINGWEAR', brand_color:DIM,
        show_badge:false, badge_text:'', badge_bg:INK, badge_color:GOLD,
        title:'Silk Column', title_accent:'Dress', title_accent_italic:true,
        description:'Bias-cut silk with a liquid fall. New in the collection.',
        cta_text:'€980', cta_url:'#',
      },
      {
        letter:'L', letter_color:IVORY,
        top_bg:{type:'solid',color:BG}, screenshot_label:'',
        brand_label:'TAILORING', brand_color:DIM,
        show_badge:false, badge_text:'', badge_bg:INK, badge_color:GOLD,
        title:'Le Smoking', title_accent:'Jacket', title_accent_italic:true,
        description:'Peak lapel, hand-sewn seams. The house tuxedo jacket.',
        cta_text:'€1,150', cta_url:'#',
      },
      {
        letter:'C', letter_color:IVORY,
        top_bg:{type:'solid',color:BG}, screenshot_label:'',
        brand_label:'KNITWEAR', brand_color:DIM,
        show_badge:true, badge_text:'Atelier', badge_bg:INK, badge_color:GOLD,
        title:'Cashmere', title_accent:'Roll-Neck', title_accent_italic:true,
        description:'Two-ply Mongolian cashmere. Atelier edition, limited run.',
        cta_text:'€620', cta_url:'#',
      },
    ],
  }) ]) ]),
]));

// 5) THE ATELIER — hero-split senza showcase, testo a sinistra (firmato Camille Aubry)
home.push(sec(PANEL,'large',[ row([ col('1-1',[ tile('hero-split',{
  eyebrow_text:'The Atelier', eyebrow_dot_color:GOLD, eyebrow_color:GOLD,
  headline_lines:[
    {text:'Made by hand,',color:IVORY,italic:false},
    {text:'made to last',color:GOLD,italic:true}
  ],
  headline_font_family:'serif', headline_font_size:52, headline_line_height:1.05, headline_font_weight:'400', headline_align:'left',
  subhead:`Every Atelier Noir garment passes through a single pair of hands from first cut to final stitch. We make in small runs, in our own Paris workrooms, with cloth we can trace to the mill.\n\n— Camille Aubry, Founder`,
  subhead_color:TXT, subhead_size:16, subhead_italic:false, subhead_max_width:520,
  cta1_text:'Our craftsmanship', cta1_url:'#', cta1_bg:'transparent', cta1_color:IVORY, cta1_border:LINE2, cta1_size:12, cta1_radius:R(0), cta1_radius_hover:R(0),
  cta2_text:'', cta2_url:'', cta2_bg:'transparent', cta2_color:IVORY, cta2_border:'transparent', cta2_size:12, cta2_radius:R(0), cta2_radius_hover:R(0),
  stats:[],
  showcase_enabled:false,
  split_ratio:'1fr', gap:0, min_height:0, tile_padding:{top:0,right:0,bottom:0,left:0},
}) ]) ]) ]));

// 6) CAMPAIGN / FEATURED STORY — cta-banner scuro con eyebrow "The Film" + titolo serif gold
home.push(sec(INK,'large',[ row([ col('1-1',[ tile('cta-banner',{
  eyebrow:'The Film', eyebrow_color:GOLD,
  headline:'Nocturne,', headline_accent:'after hours', headline_accent_italic:true,
  subtitle:`Watch the collection come to life — shot across one night in the eighth arrondissement.`,
  cta_text:'Watch the film', cta_url:'#',
  bg:{type:'solid',color:INK}, text_color:WHITE, accent_color:GOLD, subtitle_color:TXT,
  cta_bg:IVORY, cta_color:INK, cta_radius:R(0), cta_size:12,
  headline_font_family:'serif', headline_size:68, headline_weight:'400', subtitle_size:16,
  layout:'stack', vertical_align:'center', banner_radius:R(0), banner_padding:80,
}) ]) ]) ]));

// 7) LOOK FINDER — tile finder nativo (chip → result card)
// Sfondo BG2 come da CSS .an-find-sec{background:var(--bg-2)}.
// zone_accent / zone_on dal CSS inline --fx-zone-accent / --fx-zone-on.
// card_bg = PANEL, card_border = LINE2.
home.push(sec(BG2,'large',[
  row([ col('1-1',[ tile('finder',{
    eyebrow:'Styled for you',
    heading:'Find your look',
    intro:'',
    zone_accent:'#c9a86a',
    zone_on:'#0c0c0c',
    card_bg:PANEL,
    card_border:LINE2,
    align:'center',
    items:[
      {option:'An evening',     title:'After Dark',    text:`Bias-cut silk, liquid drape and a single point of gold. Made to be the last thing remembered at the party.`,          meta:`made to order · from €1,200`,   cta_text:'', cta_url:'#', icon:''},
      {option:'Tailoring',      title:'The Suit',      text:`A precise shoulder, a clean trouser break, wool that holds its line from morning to midnight. Power, quietly.`,        meta:`made to measure · from €1,650`, cta_text:'', cta_url:'#', icon:''},
      {option:'Bridal',         title:`La Mariée`, text:`Sculpted ivory, hand-finished over months in the atelier. One fitting at a time, entirely yours.`,                   meta:'private commission · by appointment', cta_text:'', cta_url:'#', icon:''},
      {option:'Everyday luxe',  title:'Quiet Luxury',  text:`Bouclé knits, fluid trousers and a camel coat that does the talking. The wardrobe you reach for without thinking.`, meta:`ready to wear · from €420`,    cta_text:'', cta_url:'#', icon:''},
    ],
  }) ]) ]),
]));

// 8) NEWSLETTER / MEMBERSHIP — newsletter tile nativo (form email reale, eyebrow La Maison)
home.push(sec(BG,'large',[ row([ col('1-1',[ tile('newsletter',{
  title:`Join the <em>house list</em>`,
  subtitle:`Early access to collections, private appointments and invitations to our seasonal presentations.`,
  layout:'horizontal',
  show_name:false,
  email_placeholder:'Email address',
  button_text:'Subscribe',
  button_icon:false,
  privacy_text:'',
  max_width:'600',
  alignment:'center',
  bg_color:'transparent',
  box_border:'',
  border_radius:0,
  tile_padding:{top:64,right:32,bottom:64,left:32},
  title_size:'52',
  title_weight:'400',
  title_color:IVORY,
  subtitle_size:'16',
  subtitle_color:DIM,
  input_bg:'transparent',
  input_color:IVORY,
  input_border:LINE2,
  input_focus_border:GOLD,
  input_radius:0,
  input_height:'52',
  btn_bg:GOLD,
  btn_color:INK,
  btn_hover_bg:GOLDL,
  btn_radius:0,
  btn_font_size:'12',
  btn_font_weight:'500',
  shadow:'none',
  icon_type:'none',
}) ]) ]) ]));

K.emit({
  slug:'atelier', name:'Atelier Noir',
  tags:['fashion','beauty','luxury','e-commerce','editorial'],
  description:`Atelier Noir — Parisian fashion house. Black & gold luxury: Bodoni Moda (display serif) + Jost (sans). Hero editoriale, marquee, lookbook, product-cards collezione, sezione atelier, campagna film, look finder (tile finder nativo), newsletter form. Riproduzione fedele dell’OLOtheme Atelier Noir.`,
  colors:{
    primary:GOLD, primary_contrast:INK, secondary:IVORY, secondary_contrast:INK,
    muted:BG2, muted_contrast:TXT, text:TXT, text_muted:DIM,
    background:BG, border:LINE, link:GOLD
  },
  css_disp:`"Bodoni Moda", Didot, Georgia, serif`, css_sans:`"Jost", -apple-system, sans-serif`,
  heading_weight:'400', heading_line_height:'1.05', google_fonts:['Bodoni+Moda:ital,opsz,wght@0,6..96,400;0,6..96,500;1,6..96,400','Jost:wght@300;400;500;600'],
  logo_variant:'light',
  menu:[
    {title:'Collection',url:'#collection'},
    {title:'Lookbook',url:'#lookbook'},
    {title:'The Atelier',url:'#atelier'},
  ],
  header:{ bg:'rgba(20,20,20,.85)', text_color:TXT, sticky_bg:'rgba(20,20,20,.92)', logo_width:140 },
  footer:{
    bg:INK, headColor:IVORY,
    brand:{name:'Atelier Noir', tagline:`A Parisian fashion house. Ready-to-wear and made-to-measure, finished by hand since 1998.`},
    columns:[
      {title:'Shop',links:['The Collection','Outerwear','Eveningwear','Made-to-measure']},
      {title:'The House',links:['The Atelier','Craftsmanship','Boutiques','Careers']},
      {title:'Client Care',links:['Shipping & returns','Book an appointment','Size guide','Contact']},
    ],
    bottom:{left:`© 2026 Atelier Noir — an OLOtheme demo.`, right:'Built with OLObuild'}
  },
  cursor:{ blend_mode:'exclusion', ring_color:'#c9a86a', dot_color:'#c9a86a' },
}, home);
