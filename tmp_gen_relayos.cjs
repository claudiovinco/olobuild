/* Relay OS — ricomposizione TILE-PURE (image-free). Software & Tech. Ink + green. */
const K = require('./tmp_theme_kit.cjs');
const { sec, row, col, tile, R } = K.builders('ro');

const BG='#0d1117', BG2='#10151c', PANEL='#161b22', PANEL2='#1c232c', INK='#080b0f';
const GREEN='#3fb950', GREEND='#2ea043', MINT='#56d364';
const TXT='#9ba6b4', DIM='#646e7c', LINE='rgba(255,255,255,.08)', LINE2='rgba(63,185,80,.4)';
const WHITE='#ffffff', GTINT='rgba(63,185,80,.14)', GTINT2='rgba(63,185,80,.08)';

const home=[];

// helper: section-header centrato
const shead=(eyebrow,l1,accent,intro)=>tile('section-header',{
  eyebrow_show:true, eyebrow_text:eyebrow, eyebrow_color:GREEN, eyebrow_dot_color:GREEN, eyebrow_separator:'',
  headline_lines:[ {text:l1,color:WHITE,italic:false},{text:accent,color:GREEN,italic:false} ],
  headline_font_family:'sans-serif', headline_font_size:46, headline_font_weight:'700', headline_align:'center', headline_inline:true,
  tagline_show:!!intro, tagline_text:intro||'', tagline_text_italic:false, tagline_text_color:DIM, tagline_text_size:16.5,
  layout:'center', gap:16,
});
// helper: caption piccola centrata
const caption=(txt)=>tile('section-header',{
  eyebrow_show:false, headline_lines:[{text:txt,color:DIM,italic:false}],
  headline_font_family:'sans-serif', headline_font_size:11.5, headline_font_weight:'500', headline_align:'center',
  tagline_show:false, layout:'center', gap:0,
});

// 1) HERO + FLOW DIAGRAM panel (showcase)
home.push(sec(BG,'large',[ row([ col('1-1',[ tile('hero-split',{
  eyebrow_text:'Relay OS · no-code automation', eyebrow_dot_color:GREEN, eyebrow_color:GREEN,
  headline_lines:[ {text:'Automate the',color:WHITE,italic:false},{text:'busywork.',color:GREEN,italic:false} ],
  headline_font_family:'sans-serif', headline_font_size:72, headline_line_height:1.0, headline_font_weight:'700', headline_align:'left',
  subhead:`Connect your apps, drag blocks into a flow, and let Relay run it — every time a trigger fires, day or night. No code, no cron jobs, no babysitting.`,
  subhead_color:DIM, subhead_size:18, subhead_italic:false, subhead_max_width:480,
  cta1_text:'Start free', cta1_url:'#pricing', cta1_bg:GREEN, cta1_color:INK, cta1_size:15, cta1_radius:R(8), cta1_radius_hover:R(8),
  cta2_text:'See it work', cta2_url:'#features', cta2_bg:'rgba(255,255,255,.05)', cta2_color:WHITE, cta2_border:'rgba(255,255,255,.16)', cta2_size:15, cta2_radius:R(8), cta2_radius_hover:R(8),
  stats:[],
  showcase_enabled:true, showcase_bg:{type:'solid',color:PANEL}, showcase_padding:24, showcase_radius:R(14), showcase_radius_hover:R(14),
  showcase_badge_text:'// FLOW CANVAS — LIVE', showcase_badge_dot:GREEN, showcase_badge_bg:INK, showcase_badge_color:WHITE,
  showcase_items:[
    {number:'New form submission',text:'trigger · Typeform',italic:false,text_color:DIM,bg:{type:'solid',color:BG2}},
    {number:'Enrich & score lead',text:'action · Clearbit',italic:false,text_color:DIM,bg:{type:'solid',color:BG2}},
    {number:'Add to CRM + Slack',text:'action · HubSpot, Slack',italic:false,text_color:DIM,bg:{type:'solid',color:BG2}},
  ],
  showcase_card_radius:R(10), showcase_card_radius_hover:R(10), showcase_card_shadow:'none',
  showcase_caption_left:'TRIGGERS', showcase_caption_right:'ACTIONS', showcase_hover_effect:'none',
  split_ratio:'1.05fr .95fr', gap:48, min_height:0, tile_padding:{top:0,right:0,bottom:0,left:0},
}) ]) ]) ]));

// 2) LOGO CLOUD (caption + trust-strip pill)
// CSS: .ro-logos { padding:42px 0; border-bottom:1px solid var(--line); background: BG (sezione su BG)
home.push(sec(BG,'small',[
  row([ col('1-1',[ caption('Quietly running ops at teams of every size') ]) ]),
  row([ col('1-1',[ tile('trust-strip',{
    items:[ {text:'SLACK'},{text:'NOTION'},{text:'HUBSPOT'},{text:'STRIPE'},{text:'AIRTABLE'},{text:'SHOPIFY'} ],
    variant:'pill', separator_char:'', align:'center', flow:'wrap', gap:16,
    font_family:'sans-serif', text_color:DIM, text_size:12,
    pill_bg:GTINT2, pill_border:LINE, pill_text_color:DIM,
  }) ]) ], {gap:16}),
]));

// 3) STAT STRIP (counter x4)
// CSS: .ro-stats { border-block:1px solid var(--line); background:var(--bg-2) }
const stat=(prefix,number,suffix,label)=>col('1-4',[ tile('counter',{
  number, suffix, prefix, label, icon_emoji:'',
  text_color:WHITE, number_color:GREEN, number_font_size:'48', number_font_weight:'800', label_color:DIM, label_font_size:'11.5',
  bg_type:'color', bg_color:'transparent', padding:'8', border_radius:'0',
}) ]);
home.push(sec(BG2,'small',[ row([
  stat('','600','+','App connectors'), stat('','2','B','Tasks run / mo'), stat('','11','h','Saved / person / wk'), stat('','99.9','%','Run reliability'),
], {gap:24}) ]));

// 4) FEATURE SPLIT — "If you can draw it, you can automate it"
// CSS: .ro-feat { display:grid; grid-template-columns:1fr 1fr; gap:54px; align-items:center }
// Sinistra: eyebrow + h2 + p + lista check + btn ghost
// Destra: .ro-feat__media (border+bg+padding = browser mockup astratto)
// → colonna sinistra: section-header left + info-cards (lista check)
// → colonna destra: buildermock (pannello astratto browser)
home.push(sec(BG,'large',[
  row([
    col('1-2',[
      tile('section-header',{
        eyebrow_show:true, eyebrow_text:'// visual builder', eyebrow_color:GREEN, eyebrow_dot_color:GREEN, eyebrow_separator:'',
        headline_lines:[ {text:'If you can draw it, you can',color:WHITE,italic:false},{text:'automate it',color:GREEN,italic:false} ],
        headline_font_family:'sans-serif', headline_font_size:44, headline_font_weight:'700', headline_align:'left', headline_inline:true,
        tagline_show:true, tagline_text:`Drag triggers and actions onto the canvas, connect them, and Relay handles the rest — retries, errors, logs and all.`, tagline_text_italic:false, tagline_text_color:DIM, tagline_text_size:16,
        layout:'left', gap:16,
      }),
      tile('info-cards',{
        container_bg:{type:'solid',color:'transparent'}, container_padding:0, container_gap:10, columns:1, items_gap:10,
        card_bg:{type:'solid',color:'transparent'}, card_color:TXT, card_radius:R(0), card_padding:0,
        show_icon:true, show_counter:false, show_arrow:false, show_footer:false, show_media:false,
        icon_color:GREEN, icon_bg_color:'transparent', title_color:WHITE,
        title_font_family:'sans-serif', title_size:14.5, title_weight:'500', title_italic:false, description_size:0,
        items:[
          {icon:'check', title:'Branching, filters & loops', description:''},
          {icon:'check', title:'Built-in retries & error paths', description:''},
          {icon:'check', title:'Drop to code when you need to', description:''},
        ],
        card_hover_effect:'none',
      }),
      tile('button',{
        text:'Watch a 2-min demo', url:'#', bg:'rgba(255,255,255,.05)', color:WHITE,
        border:'rgba(255,255,255,.16)', radius:R(8), size:14, full_width:false,
      }),
    ]),
    col('1-2',[
      tile('buildermock',{
        bg_color:PANEL, border_color:LINE, border_radius:R(16),
        bar_bg:PANEL2, bar_dot_color:'rgba(255,255,255,.16)',
        content_label:'flow canvas — nodes & connectors',
        content_bg:PANEL, content_pattern:'grid', aspect_ratio:'16/10',
        label_color:'rgba(255,255,255,.4)', label_font:'mono',
      }),
    ]),
  ], {gap:54, vertical_align:'center'}),
]));

// 5) FLOW DIAGRAM spotlight — "One flow, a hundred hands off your plate"
// CSS: .ro-builder { border:1px solid LINE2; border-radius:18px; background:INK; padding:72px 56px; text-align:center }
// Chip inline: Trigger→Filter→Enrich→Notify→Done
// → Approssimato con process-steps card (sfondo INK, bordo LINE2)
// ⚠️ SEGNALATO: FlowDiagram interattivo con spotlight non riproducibile con tile statici
home.push(sec(BG2,'large',[
  row([ col('1-1',[ tile('section-header',{
    eyebrow_show:true, eyebrow_text:'// from trigger to done', eyebrow_color:GREEN, eyebrow_dot_color:GREEN, eyebrow_separator:'',
    headline_lines:[ {text:'One flow,',color:WHITE,italic:false},{text:'a hundred hands off your plate',color:GREEN,italic:false} ],
    headline_font_family:'sans-serif', headline_font_size:44, headline_font_weight:'700', headline_align:'center', headline_inline:false,
    tagline_show:true, tagline_text:`Every step you'd do by hand becomes a block that runs itself.`, tagline_text_italic:false, tagline_text_color:DIM, tagline_text_size:16,
    layout:'center', gap:16,
  }) ]) ]),
  row([ col('1-1',[ tile('process-steps',{
    columns:5, gap:14, align:'center', auto_number:false, item_gap:8,
    number_style:'circle', number_color:INK, number_bg:GREEN, number_size:14, number_font:'sans-serif', number_weight:'500',
    title_color:WHITE, title_size:14, title_font:'sans-serif', title_weight:'600',
    desc_color:'', desc_size:0,
    card_bg:PANEL, card_border:LINE, card_radius:R(999), card_padding:'10px 16px',
    items:[
      {number:'', title:'Trigger', description:''},
      {number:'', title:'Filter', description:''},
      {number:'', title:'Enrich', description:''},
      {number:'', title:'Notify', description:''},
      {number:'', title:'Done', description:''},
    ],
  }) ]) ]),
]));

// 6) INTEGRATIONS (section-header center + griglia 6 col card quadrate)
// CSS: .ro-intg { display:grid; grid-template-columns:repeat(6,1fr); gap:14px }
// .ro-intg__c { aspect-ratio:1/1; border:1px solid LINE; border-radius:12px; background:PANEL }
// Usiamo info-cards a 6 col (card quadrate con icona placeholder + nome)
home.push(sec(BG,'large',[
  row([ col('1-1',[ shead('// 600+ connectors','Connected to your ','stack','If it has an API, Relay probably already speaks to it.') ]) ]),
  row([ col('1-1',[ tile('info-cards',{
    container_bg:{type:'solid',color:'transparent'}, container_padding:0, container_gap:14, columns:6, items_gap:14,
    card_bg:{type:'solid',color:PANEL}, card_color:DIM, card_radius:R(12), card_padding:10,
    show_icon:true, show_counter:false, show_arrow:false, show_footer:false, show_media:false,
    icon_color:GREEN, icon_bg_color:'rgba(255,255,255,.07)', title_color:TXT,
    title_font_family:'sans-serif', title_size:9, title_weight:'400', title_italic:false, description_size:0,
    icon_size:20,
    items:[
      {icon:'message-square', title:'Slack', description:''},
      {icon:'mail', title:'Gmail', description:''},
      {icon:'trending-up', title:'HubSpot', description:''},
      {icon:'file-text', title:'Notion', description:''},
      {icon:'grid', title:'Sheets', description:''},
      {icon:'credit-card', title:'Stripe', description:''},
      {icon:'table', title:'Airtable', description:''},
      {icon:'git-branch', title:'Jira', description:''},
      {icon:'shopping-bag', title:'Shopify', description:''},
      {icon:'link', title:'Webhooks', description:''},
      {icon:'cpu', title:'OpenAI', description:''},
      {icon:'plus-circle', title:'+590', description:''},
    ],
    card_hover_effect:'lift',
  }) ]) ]),
]));

// 7) TIMEZONE PLANNER — tile timezone (interattivo, slider 24h + calcolo UTC live)
// Blueprint: data-offset=[-8,0,1,8] · value="9" · --fx-zone-accent:#3fb950
home.push(sec(BG,'large',[
  row([ col('1-1',[ shead('// scheduling','Fire it when the whole team is ','awake','Drag the trigger time and watch every region\'s clock move. Schedule reports, syncs and pings for the window where your people actually overlap.') ]) ]),
  row([ col('1-1',[ tile('timezone',{
    eyebrow:'// trigger · San Francisco',
    heading:'Fire it when the whole team is awake',
    intro:'',
    base_label:'San Francisco',
    input_value:9,
    work_start:9,
    work_end:18,
    items:[
      {city:'San Francisco', offset:-8, label:'UTC−8'},
      {city:'London',        offset: 0, label:'UTC+0'},
      {city:'Berlin',        offset: 1, label:'UTC+1'},
      {city:'Singapore',     offset: 8, label:'UTC+8'},
    ],
    zone_accent:GREEN,
    work_color:GTINT,
    ok_color:GREEN,
    sleep_color:DIM,
    card_bg:BG2,
    card_border:LINE,
    align:'center',
  }) ]) ]),
]));

// 8) PRICING (3 piani)
// CSS: .ro-price { background:PANEL; border:1px solid LINE; border-radius:18px; padding:30px }
// .ro-price.feat { border-color:GREEN; box-shadow:0 0 0 1px GREEN, 0 20px 60px -30px rgba(63,185,80,.6) }
// .ro-price__tag { color:GREEN; background:rgba(63,185,80,.12) }
const feats=(list)=>list.join('\n');
home.push(sec(PANEL,'large',[
  row([ col('1-1',[ shead('// pricing','Pay per ','task, not per seat','') ]) ]),
  row([
    col('1-3',[ tile('pricing',{
      plan_name:'Free', price:'0', currency:'$', period:'/ mo',
      description:'For personal automations.',
      features:feats(['500 tasks / mo','Unlimited flows','5-min polling']),
      is_popular:false, bg_color:PANEL2, price_color:WHITE, accent_color:GREEN,
      cta_text:'Start free', cta_url:'#pricing', cta_bg:'rgba(255,255,255,.05)', cta_color:WHITE, cta_border:'rgba(255,255,255,.16)',
      border_radius:R(18), title_color:WHITE, feature_color:TXT, feature_icon_color:GREEN,
      card_border_color:LINE,
    }) ]),
    col('1-3',[ tile('pricing',{
      plan_name:'Team', price:'49', currency:'$', period:'/ mo',
      description:'For teams running real ops.',
      features:feats(['50k tasks / mo','Real-time triggers','Shared team workspace','Version history']),
      is_popular:true, badge_text:'Most popular', badge_bg_color:'rgba(63,185,80,.12)', badge_text_color:GREEN,
      bg_color:PANEL2, price_color:WHITE, accent_color:GREEN,
      cta_text:'Start 14-day trial', cta_url:'#pricing', cta_bg:GREEN, cta_color:INK,
      border_radius:R(18), title_color:WHITE, feature_color:TXT, feature_icon_color:GREEN,
      card_border_color:GREEN,
    }) ]),
    col('1-3',[ tile('pricing',{
      plan_name:'Business', price:'Custom', currency:'', period:'',
      description:'Scale, SSO & governance.',
      features:feats(['Unlimited tasks','SSO & audit log','Dedicated support']),
      is_popular:false, bg_color:PANEL2, price_color:WHITE, accent_color:GREEN,
      cta_text:'Talk to sales', cta_url:'#', cta_bg:'rgba(255,255,255,.05)', cta_color:WHITE, cta_border:'rgba(255,255,255,.16)',
      border_radius:R(18), title_color:WHITE, feature_color:TXT, feature_icon_color:GREEN,
      card_border_color:LINE,
    }) ]),
  ],{gap:18,vertical_align:'stretch'}),
]));

// 9) RECIPE FINDER — tile finder (chip selector → result card)
home.push(sec(BG,'large',[
  row([ col('1-1',[ tile('finder',{
    eyebrow:'// recipes',
    heading:'What should we automate first?',
    intro:'',
    zone_accent:GREEN,
    zone_on:INK,
    card_bg:PANEL,
    card_border:LINE,
    align:'center',
    items:[
      {
        option:'New leads',
        icon:'zap',
        title:'Lead → CRM → Slack',
        text:'Every new lead enriched, deduped and in front of the right rep in seconds — no copy-paste, no missed follow-ups.',
        meta:'Prebuilt recipe',
        cta_text:'Use this recipe',
        cta_url:'#',
      },
      {
        option:'Support tickets',
        icon:'cpu',
        title:'Triage & Route',
        text:'Inbound tickets read, tagged and routed by intent and urgency the moment they land.',
        meta:'Prebuilt recipe',
        cta_text:'Use this recipe',
        cta_url:'#',
      },
      {
        option:'Ops & reporting',
        icon:'calendar',
        title:'Nightly Rollup',
        text:"Yesterday's numbers, compiled and in every inbox before the first coffee. Runs while you sleep.",
        meta:'Prebuilt recipe',
        cta_text:'Use this recipe',
        cta_url:'#',
      },
      {
        option:'Onboarding',
        icon:'users',
        title:'Day-One Setup',
        text:'New starters fully provisioned and scheduled before they walk in — IT, access and intros, automatically.',
        meta:'Prebuilt recipe',
        cta_text:'Use this recipe',
        cta_url:'#',
      },
    ],
  }) ]) ]),
]));

// 10) CTA — 2 pulsanti: "Start free" (green) + "Browse templates" (ghost)
// CSS: .ro-cta__box { border:1px solid LINE2; border-radius:20px; text-align:center; background:linear-gradient(160deg,...) }
// Blueprint CTA ha DUE pulsanti: Start free (btn--green) + Browse templates (btn--ghost)
home.push(sec(BG,'large',[ row([ col('1-1',[ tile('cta-banner',{
  headline:'Give the robots the', headline_accent:'boring bits', headline_accent_italic:false,
  subtitle:`Build your first automation in five minutes — free, no card, and it'll keep running long after you close the tab.`,
  cta_text:'Start free', cta_url:'#pricing',
  cta2_text:'Browse templates', cta2_url:'#templates', cta2_bg:'rgba(255,255,255,.05)', cta2_color:WHITE, cta2_border:'rgba(255,255,255,.16)',
  bg:{type:'gradient',gradient:'linear-gradient(160deg, rgba(63,185,80,.16), rgba(86,211,100,.05))'}, text_color:WHITE, accent_color:GREEN, subtitle_color:TXT,
  cta_bg:GREEN, cta_color:INK, cta_radius:R(8), cta_size:15,
  headline_font_family:'sans-serif', headline_size:52, headline_weight:'700', subtitle_size:17,
  layout:'stack', vertical_align:'center', banner_radius:R(20), banner_padding:80,
  border:{top:{width:1,style:'solid',color:LINE2},right:{width:1,style:'solid',color:LINE2},bottom:{width:1,style:'solid',color:LINE2},left:{width:1,style:'solid',color:LINE2}},
}) ]) ]) ]));

K.emit({
  slug:'relayos', name:'Relay OS',
  tags:['software','tech','automation','saas','startup'],
  description:`Relay OS — visual workflow automation platform. Ink + green GitHub-style, Wix Madefor Display (display) + Wix Madefor Text. Zone interattive FlowDiagram/TimezonePlanner/RecipeFinder approssimate con tile statici. Riproduzione fedele dell'OLOtheme Relay OS.`,
  colors:{ primary:GREEN, primary_contrast:INK, secondary:MINT, secondary_contrast:INK, muted:BG2, muted_contrast:TXT, text:TXT, text_muted:DIM, background:BG, border:LINE, link:GREEN },
  css_disp:`"Wix Madefor Display",-apple-system,sans-serif`, css_sans:`"Wix Madefor Text",-apple-system,sans-serif`,
  heading_weight:'700', heading_line_height:'1.05', google_fonts:['Wix Madefor Display','Wix Madefor Text'],
  logo_variant:'light',
  menu:[ {title:'Product',url:'#features'},{title:'Integrations',url:'#integrations'},{title:'Templates',url:'#templates'},{title:'Pricing',url:'#pricing'} ],
  header:{ bg:BG, text_color:DIM, sticky_bg:'rgba(13,17,23,.84)', logo_width:140 },
  footer:{ bg:BG2, headColor:WHITE, brand:{name:'Relay OS', tagline:'Visual automation for everyone. Connect apps, build flows, let the busywork run itself.'},
    columns:[ {title:'Product',links:['Product','Integrations','Templates','Pricing']}, {title:'Developers',links:['Docs','API','Status']}, {title:'Company',links:['About','Blog','Careers']} ],
    bottom:{left:'© 2026 Relay OS — an OLOtheme demo.', right:'Built with OLObuild'} },
  cursor:{ blend_mode:'exclusion', ring_color:WHITE, dot_color:WHITE },
}, home);
