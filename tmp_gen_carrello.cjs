/* Carrello — ricomposizione TILE-PURE (image-free). Marketplace makers. Ink + coral. Mona Sans.
   Pixel-perfect pass v2: Countdown tile per flash sale, copy esatto, palette `:root`, bordi corretti. */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('ca');

const BG='#1a1a22', BG2='#1e1e28', PANEL='#26262f', PANEL2='#30303b', INK='#101015';
const CORAL='#ff5a5f', CORALD='#ec474c', MINT='#3fd6a8', CREAM='#f0f0f4';
const TXT='#a6a6b4', DIM='#6c6c7c', LINE='rgba(255,255,255,.09)', WHITE='#ffffff';
const CORAL_TINT='rgba(255,90,95,.14)', CORAL_LINE='rgba(255,90,95,.4)';

const home=[];

// helper: section-header centrato
const shead=(eyebrow,l1,accent,intro)=>tile('section-header',{
  eyebrow_show:true, eyebrow_text:eyebrow, eyebrow_color:CORAL, eyebrow_dot_color:CORAL, eyebrow_separator:'',
  headline_lines:[ {text:l1,color:WHITE,italic:false},{text:accent,color:CORAL,italic:false} ],
  headline_font_family:'sans-serif', headline_font_size:42, headline_font_weight:'800', headline_align:'center', headline_inline:true,
  tagline_show:!!intro, tagline_text:intro||'', tagline_text_italic:false, tagline_text_color:TXT, tagline_text_size:17,
  layout:'center', gap:14,
});

// helper: section-header sinistra (con due parole — l1 bianca + accent coral inline)
const sheadLeft=(eyebrow,l1,accent)=>tile('section-header',{
  eyebrow_show:!!eyebrow, eyebrow_text:eyebrow||'', eyebrow_color:CORAL, eyebrow_dot_color:CORAL, eyebrow_separator:'',
  headline_lines:[ {text:l1,color:WHITE,italic:false},{text:accent,color:CORAL,italic:false} ],
  headline_font_family:'sans-serif', headline_font_size:38, headline_font_weight:'800', headline_align:'left', headline_inline:true,
  tagline_show:false, layout:'stack', gap:10,
});

// 1) HERO — SearchHero approx. hero-split (SearchHero tile non esiste in OLObuild)
// Segnalato come best-effort: hero-split con showcase categorie, senza barra ricerca live + chip.
home.push(sec(BG,'large',[ row([ col('1-1',[ tile('hero-split',{
  eyebrow_text:'Marketplace for independent makers', eyebrow_dot_color:CORAL, eyebrow_color:CORAL,
  headline_lines:[ {text:'Everything good,',color:WHITE,italic:false},{text:`from small shops.`,color:CORAL,italic:false} ],
  headline_font_family:'sans-serif', headline_font_size:72, headline_line_height:1.0, headline_font_weight:'800', headline_align:'left',
  subhead:`Thousands of independent sellers, one cart, one checkout. Find the thing — and support the person who made it.`,
  subhead_color:TXT, subhead_size:18, subhead_italic:false, subhead_max_width:520,
  cta1_text:'Browse categories', cta1_url:'#categories', cta1_bg:CORAL, cta1_color:WHITE, cta1_size:15, cta1_radius:R(10), cta1_radius_hover:R(10),
  cta2_text:'Open a shop', cta2_url:'#vendors', cta2_bg:'rgba(255,255,255,.06)', cta2_color:WHITE, cta2_border:'rgba(255,255,255,.16)', cta2_size:15, cta2_radius:R(10), cta2_radius_hover:R(10),
  stats:[],
  showcase_enabled:true, showcase_bg:{type:'solid',color:PANEL}, showcase_padding:24, showcase_radius:R(16), showcase_radius_hover:R(16),
  showcase_badge_text:`SEARCH · 90,000+ ITEMS`, showcase_badge_dot:CORAL, showcase_badge_bg:INK, showcase_badge_color:WHITE,
  showcase_items:[
    {number:'Ceramics',text:'2.1k items',italic:false,text_color:CORAL,bg:{type:'solid',color:BG2}},
    {number:`Art & Prints`,text:'4.6k items',italic:false,text_color:WHITE,bg:{type:'solid',color:BG2}},
    {number:'Jewellery',text:'8.9k items',italic:false,text_color:WHITE,bg:{type:'solid',color:BG2}},
    {number:'Homeware',text:'3.4k items',italic:false,text_color:WHITE,bg:{type:'solid',color:BG2}},
  ],
  showcase_card_radius:R(11), showcase_card_radius_hover:R(11), showcase_card_shadow:'none',
  showcase_caption_left:'TRENDING NOW', showcase_caption_right:'MAKERS WEEK', showcase_hover_effect:'none',
  split_ratio:'1.3fr .7fr', gap:48, min_height:0, tile_padding:{top:0,right:0,bottom:0,left:0},
}) ]) ]) ]));

// 2) CATEGORY RAIL — trust-strip pill (6 categorie: Ceramics / Art & Prints / Jewellery / Homeware / Vintage / Stationery)
// La .ca-rail ha border-radius:12px, aspect-ratio:4/5, border:1px solid var(--line) per ogni catc.
// Tile trust-strip pill replica le categorie come chip navigabili (la rail drag-scroll non esiste come tile).
home.push(sec(BG2,'small',[
  row([ col('1-1',[ tile('section-header',{
    eyebrow_show:false,
    headline_lines:[{text:'Shop by ',color:WHITE,italic:false},{text:'category',color:CORAL,italic:false}],
    headline_font_family:'sans-serif', headline_font_size:32, headline_font_weight:'800', headline_align:'left', headline_inline:true,
    tagline_show:false, layout:'stack', gap:0,
  }) ]) ]),
  row([ col('1-1',[ tile('trust-strip',{
    items:[
      {text:'Ceramics'},{text:`Art & Prints`},{text:'Jewellery'},
      {text:'Homeware'},{text:'Vintage'},{text:'Stationery'},
    ],
    variant:'pill', separator_char:'', align:'center', flow:'wrap', gap:14,
    font_family:'sans-serif', text_color:TXT, text_size:14,
    pill_bg:PANEL, pill_border:LINE, pill_text_color:TXT,
  }) ]) ], {gap:16}),
]));

// 3) FLASH SALE — sezione coral: cta-banner (testo Maker's Week) + countdown affiancato
// Blueprint: box gradient coral con h2 + p a sx, countdown (Days/Hrs/Min/Sec) a dx.
// Layout: row 2-col — col-sx section-header, col-dx countdown tile.
// Il box coral ha border-radius:16px; usare sezione BG con bg interno al cta-banner.
// NOTA: la sezione nel blueprint ha bg trasparente (il colore è nell'inner .ca-deal).
home.push(sec(BG,'medium',[
  row([ col('1-1',[ tile('cta-banner',{
    headline:`Up to 40% off,`, headline_accent:`this week only`, headline_accent_italic:false,
    subtitle:`Hand-picked deals across thousands of shops. Maker's Week ends soon.`,
    cta_text:'Shop deals', cta_url:'#trending',
    bg:{type:'gradient',gradient_from:CORAL,gradient_to:'#ff8a5f',gradient_angle:100},
    text_color:WHITE, accent_color:WHITE, subtitle_color:'rgba(255,255,255,.85)',
    cta_bg:INK, cta_color:WHITE, cta_radius:R(10), cta_size:14,
    headline_font_family:'sans-serif', headline_size:40, headline_weight:'800', subtitle_size:16,
    layout:'split-2', vertical_align:'center', banner_radius:R(16), banner_padding:40,
  }) ]) ]),
  row([ col('1-1',[ tile('countdown',{
    countdown_style:'custom',
    countdown_type:'date',
    target_date:'2026-06-30T23:59',
    show_days:true, show_hours:true, show_minutes:true, show_seconds:true,
    label_days:'Days', label_hours:'Hrs', label_minutes:'Min', label_seconds:'Sec',
    separator:'',
    display_mode:'inline',
    bg:{type:'none'},
    accent_color:WHITE,
    text_color:'rgba(255,255,255,.8)',
    item_bg_color:'rgba(16,16,21,.25)',
    item_radius:10, item_padding:14,
    number_font_size:'30', number_font_weight:'800',
    label_font_size:'10', label_font_weight:'500',
    item_min_width:'64',
    tile_padding:{top:8,right:0,bottom:16,left:0},
    preset:'custom',
  }) ]) ]),
]));

// 4) GIFT FINDER — tile finder (chip "chi cerchi" → result card collezione curata)
home.push(sec(BG,'large',[
  row([ col('1-1',[ tile('finder',{
    eyebrow:'Gift mode',
    heading:'Who are you shopping for?',
    intro:'Four curated edits from our independent makers — pick yours and discover pieces that mean something.',
    zone_accent:CORAL,
    zone_on:'#ffffff',
    card_bg:PANEL,
    card_border:LINE,
    align:'center',
    items:[
      {
        option:'A partner',
        icon:'heart',
        title:'The Thoughtful Edit',
        text:'Pieces made to last and to mean something — hand-thrown, hand-set, hand-finished by makers who sign their work.',
        meta:'Jewellery · Ceramics · Leather',
        cta_text:'Shop the edit · from €24',
        cta_url:'#',
      },
      {
        option:'A close friend',
        icon:'gift',
        title:'Small Joys',
        text:"The little luxuries that always land — a riso print, a good candle, the notebook they'll actually use. All under forty euro.",
        meta:'Prints · Candles · Stationery',
        cta_text:'Shop the edit · from €12',
        cta_url:'#',
      },
      {
        option:'A new home',
        icon:'home',
        title:'The Housewarming',
        text:'Everything that makes a new place feel lived-in fast — linen for the table, wool for the sofa, something warm for the kitchen.',
        meta:'Homeware · Textiles · Kitchen',
        cta_text:'Shop the edit · from €18',
        cta_url:'#',
      },
      {
        option:'Treat yourself',
        icon:'star',
        title:'Just Because',
        text:"The treat you'd never quite buy at full price — a vintage find, a small original, the self-care thing you keep almost adding to cart.",
        meta:'Vintage · Art · Self-care',
        cta_text:'Shop the edit · from €15',
        cta_url:'#',
      },
    ],
  }) ]) ]),
]));

// 5) TRENDING NOW — product-cards non calza (design editoriale monogramma/lettera).
// info-cards è la scelta migliore: counter_label = store, title = nome prodotto,
// description = rating (★ + score + count), footer_text = prezzo (+ vecchio prezzo sbarrato).
// 4 prodotti, griglia 4-col, bg PANEL, border 1px LINE, border-radius:12px.
// Prodotti dal blueprint: Speckled Tea Mug / Riso Print A2 / Brass Arc Earrings / Linen Napkins
home.push(sec(BG2,'large',[
  row([ col('1-1',[ sheadLeft('','Trending ','now') ]) ]),
  row([ col('1-1',[ tile('info-cards',{
    container_bg:{type:'solid',color:'transparent'}, container_padding:0, container_gap:16, columns:4, items_gap:16,
    card_bg:{type:'solid',color:PANEL}, card_color:TXT, card_radius:R(12), card_padding:16,
    card_border:`1px solid ${LINE}`,
    show_icon:false, show_counter:true, show_counter_label:true, show_arrow:false, show_footer:true, show_media:false,
    counter_shape:'pill', counter_color:WHITE, counter_bg:CORAL, title_color:WHITE,
    title_font_family:'sans-serif', title_size:15, title_weight:'700', title_italic:false, description_size:12, counter_size:11,
    footer_size:18,
    items:[
      {counter:'-25%', counter_label:'Kiln Studio', title:'Speckled Tea Mug', description:`★ 4.9 (212)`, footer_text:`€24`, footer_dot_color:CORAL},
      {counter:'', counter_label:'Press Yan', title:'Riso Print, A2', description:`★ 5.0 (88)`, footer_text:'€34', footer_dot_color:MINT},
      {counter:'-15%', counter_label:'Mara Made', title:'Brass Arc Earrings', description:`★ 4.8 (340)`, footer_text:'€42', footer_dot_color:CORAL},
      {counter:'', counter_label:`Flax & Co`, title:'Linen Napkins, 4', description:`★ 4.9 (156)`, footer_text:'€38', footer_dot_color:MINT},
    ],
    card_hover_effect:'lift',
  }) ]) ]),
]));

// 6) VENDORS / SHOPS TO FOLLOW — info-cards (3 shop card, bg panel, .ca-vendor)
// Blueprint: ogni card = avatar 48x48px border-radius:12px (PANEL2) + nome + categoria/sales.
// Nessun footer nel blueprint per i vendor. icon = Lucide pertinente per categoria.
// Sezione ha classe .panel → bg BG2.
home.push(sec(BG2,'large',[
  row([ col('1-1',[ sheadLeft('','Shops to ','follow') ]) ]),
  row([ col('1-1',[ tile('info-cards',{
    container_bg:{type:'solid',color:'transparent'}, container_padding:0, container_gap:16, columns:3, items_gap:16,
    card_bg:{type:'solid',color:PANEL}, card_color:DIM, card_radius:R(12), card_padding:22,
    card_border:`1px solid ${LINE}`,
    show_icon:true, show_counter:false, show_arrow:false, show_footer:false, show_media:false,
    icon_color:WHITE, icon_bg_color:PANEL2, icon_size:20,
    title_color:WHITE,
    title_font_family:'sans-serif', title_size:16, title_weight:'700', title_italic:false, description_size:12,
    items:[
      {icon:'coffee', title:'Kiln Studio', description:`Ceramics · 2.1k sales`},
      {icon:'palette', title:'Press Yan', description:`Prints · 4.6k sales`},
      {icon:'scissors', title:'Mara Made', description:`Jewellery · 8.9k sales`},
    ],
    card_hover_effect:'lift',
  }) ]) ]),
]));

// 7) CART BUILDER — tile `builder` interattivo con stepper +/- e totale live.
// Blueprint: section#cart data-builder data-currency="€" — 6 item, nessun cap.
// zone_accent/zone_on dal CSS --fx-zone-accent:#ff5a5f / --fx-zone-on:#fff.
home.push(sec(BG,'large',[
  row([ col('1-1',[ tile('builder',{
    eyebrow:'Try the cart',
    heading:`Build a basket`,
    intro:`Tap to add a few finds from makers on Carrello — the total ticks up live, like the real thing.`,
    currency:'€',
    cap:0,
    total_label:'Total',
    count_label:'items',
    cta_text:'Go to cart',
    cta_url:'#',
    zone_accent:'#ff5a5f',
    zone_on:'#ffffff',
    card_bg:PANEL,
    card_border:`1px solid ${LINE}`,
    align:'left',
    items:[
      {name:'Hand-thrown Mug',   price:32,   note:'Clayfolk',     start:false},
      {name:'Linen Tote',        price:58,   note:'Norra Goods',  start:false},
      {name:'Beeswax Candle',    price:24,   note:`Hearth & Co`, start:false},
      {name:'Notebook Set',      price:45,   note:'Paper Lab',    start:false},
      {name:'Wool Throw',        price:76,   note:`Loom & Field`, start:false},
      {name:'Herb Seed Kit',     price:19,   note:'Sprout',       start:false},
    ],
  }) ]) ]),
]));

// 8) CTA FINALE — cta-banner a 2 bottoni (CTA primaria coral + CTA ghost outline)
// Blueprint: box con border:1px solid var(--line-2) coral e background gradient coral-tint+mint-tint.
// Headline: "Make something?" + accent "Sell it here."
// cta1: "Open a shop" (coral), cta2: "Browse deals" (ghost/outline).
home.push(sec(BG,'large',[ row([ col('1-1',[ tile('cta-banner',{
  headline:`Make something?`, headline_accent:`Sell it here.`, headline_accent_italic:false,
  subtitle:`Open a shop in minutes, reach millions of buyers, and only pay when you sell.`,
  cta_text:'Open a shop', cta_url:'#vendors',
  cta2_text:'Browse deals', cta2_url:'#trending', cta2_bg:'rgba(255,255,255,.06)', cta2_color:WHITE, cta2_border:'rgba(255,255,255,.16)',
  bg:{type:'gradient',gradient_from:'rgba(255,90,95,.16)',gradient_to:'rgba(63,214,168,.05)',gradient_angle:160},
  text_color:WHITE, accent_color:CORAL, subtitle_color:TXT,
  cta_bg:CORAL, cta_color:WHITE, cta_radius:R(10), cta_size:15,
  headline_font_family:'sans-serif', headline_size:50, headline_weight:'800', subtitle_size:17,
  layout:'stack', vertical_align:'center', banner_radius:R(20), banner_padding:72,
}) ]) ]) ]));

K.emit({
  slug:'carrello', name:'Carrello',
  tags:['ecommerce','marketplace','shop','makers','independent'],
  description:`Carrello — marketplace for independent makers. Dark ink + coral, Mona Sans 800. Sezioni: hero/categories/flash-sale/gift-finder/trending/vendors/cart-builder/CTA. Riproduzione tile-pure dell'OLOtheme Carrello.`,
  colors:{ primary:CORAL, primary_contrast:WHITE, secondary:MINT, secondary_contrast:INK, muted:BG2, muted_contrast:TXT, text:TXT, text_muted:DIM, background:BG, border:LINE, link:CORAL },
  css_disp:`"Mona Sans",-apple-system,sans-serif`, css_sans:`"Mona Sans",-apple-system,sans-serif`,
  heading_weight:'800', heading_line_height:'1.04', google_fonts:['Mona Sans'],
  logo_variant:'light',
  menu:[ {title:'Deals',url:'#deals'},{title:'Categories',url:'#categories'},{title:'Trending',url:'#trending'},{title:'Sell',url:'#vendors'} ],
  header:{ bg:BG, text_color:DIM, sticky_bg:'rgba(26,26,34,.86)', logo_width:140 },
  footer:{ bg:BG2, headColor:WHITE, brand:{name:'Carrello', tagline:`The marketplace for independent makers. Thousands of shops, one cart.`},
    columns:[
      {title:'Shop',links:['Categories','Deals','Trending','Gift cards']},
      {title:'Sell',links:['Open a shop','Seller fees','Seller handbook']},
      {title:'Help',links:['Orders & shipping','Returns','Contact']},
    ],
    bottom:{left:`© 2026 Carrello — an OLOtheme demo.`, right:'Built with OLObuild'} },
  cursor:{ blend_mode:'exclusion', ring_color:CORAL, dot_color:CORAL },
}, home);
