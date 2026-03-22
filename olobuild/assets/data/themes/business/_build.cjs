const fs=require('fs');const u=()=>Math.random().toString(36).substr(2,8);
const T=(t,s,st,a,ch)=>({id:u(),type:t,settings:s||{},style:st||{},advanced:a||{},children:ch||[]});
const dir='assets/data/themes/business/';

// Row with columns as children (tree format, not legacy columns_data)
function R(layout, gap, colTiles, valign) {
  const widthMap = {
    '100': ['1-1'],
    '50-50': ['1-2','1-2'],
    '33-33-33': ['1-3','1-3','1-3'],
    '25-25-25-25': ['1-4','1-4','1-4','1-4'],
    '25-50-25': ['1-4','1-2','1-4'],
    '66-33': ['2-3','1-3'],
    '33-66': ['1-3','2-3'],
    '25-75': ['1-4','3-4'],
    '75-25': ['3-4','1-4'],
  };
  const widths = widthMap[layout] || ['1-1'];
  const columns = colTiles.map((tiles, i) => T('column', {
    width_default: '', width_small: '', width_medium: widths[i] || '1-1', width_large: ''
  }, {}, {}, tiles));
  return T('row', { layout, gap: gap||0, vertical_align: valign||'center', stack_mobile: true }, {}, {}, columns);
}

const S = (s,st,a,ch) => T('section', s, st, a||{}, ch);
const HL = (text,tag,size,color,align) => T('headline',{heading:text,tag:tag||'h2',heading_size:size||'lg',heading_color:color||'',alignment:align||'',decoration:'none'});
const TX = (text,color,size) => T('text',{content:'<p>'+text+'</p>',text_color:color||'',font_size:size||''});
const SP = (h) => T('spacer',{height:h||'16'});
const BTN = (text,url,bg,color,align) => T('button',{text,url:url||'#',bg_color:bg||'',text_color:color||'#FFFFFF',border_radius:'8',padding_x:'28',padding_y:'14',font_size:'16',font_weight:'600',alignment:align||''});

const hp = [
  // Hero
  S({style:'default',width:'expand',padding:'xlarge'},{bg:{type:'gradient',gradient_angle:135,gradient_from:'#1E3A5F',gradient_to:'#0F172A'},padding_top:'120',padding_bottom:'120'},{},[
    R('50-50',40,[
      [HL('Trasformiamo le tue idee in realta digitale','h1','xl','#FFFFFF','left'),SP('16'),TX('Creiamo soluzioni web innovative e su misura per far crescere il tuo business.','#94A3B8','18'),SP('28'),BTN('Scopri i servizi','#servizi','#2563EB','#FFFFFF','left')],
      [T('image',{image_url:'https://images.unsplash.com/photo-1551434678-e076c223a692?w=800&h=600&fit=crop',alt_text:'Team al lavoro',border_radius:16,height:'400px',object_fit:'cover'})]
    ])
  ]),
  // Servizi
  S({style:'default',width:'default',padding:'large'},{padding_top:'80',padding_bottom:'80'},{html_id:'servizi'},[
    R('100',0,[[HL('I nostri servizi','h2','lg','','center'),SP('8'),TX('Competenze digitali al servizio della tua crescita','#6B7280','16'),SP('40')]]),
    R('25-25-25-25',24,[
      [T('iconbox',{icon_emoji:'cog',title:'Sviluppo Web',description:'Siti web moderni, veloci e responsive.',alignment:'center',icon_size:'1.2',icon_color:'#2563EB'})],
      [T('iconbox',{icon_emoji:'palette',title:'UI/UX Design',description:'Design accattivante e interfacce intuitive.',alignment:'center',icon_size:'1.2',icon_color:'#2563EB'})],
      [T('iconbox',{icon_emoji:'bolt',title:'SEO & Marketing',description:'Strategie per aumentare visibilita.',alignment:'center',icon_size:'1.2',icon_color:'#2563EB'})],
      [T('iconbox',{icon_emoji:'cloud-upload',title:'Cloud & Hosting',description:'Infrastruttura cloud scalabile e sicura.',alignment:'center',icon_size:'1.2',icon_color:'#2563EB'})]
    ])
  ]),
  // About
  S({style:'default',width:'default',padding:'large'},{bg:{type:'solid',color:'#F9FAFB'},padding_top:'80',padding_bottom:'80'},{},[
    R('50-50',40,[
      [T('image',{image_url:'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&h=600&fit=crop',alt_text:'Il nostro team',border_radius:12,height:'380px',object_fit:'cover'})],
      [HL('Chi siamo','h2','lg','','left'),SP('12'),TX('Siamo un team di professionisti appassionati di tecnologia. Da oltre 10 anni aiutiamo aziende a realizzare progetti digitali su misura.','#4B5563','16'),SP('20'),BTN('Scopri di piu','/chi-siamo','transparent','#2563EB','left')]
    ])
  ]),
  // Contatori
  S({style:'default',width:'default',padding:'large'},{bg:{type:'solid',color:'#1E3A5F'},padding_top:'60',padding_bottom:'60'},{},[
    R('25-25-25-25',24,[
      [T('counter',{number:'150',suffix:'+',label:'Clienti soddisfatti',alignment:'center',text_color:'#FFFFFF',number_font_size:'48',label_color:'#94A3B8',icon_emoji:'users',icon_size:'24'})],
      [T('counter',{number:'320',suffix:'+',label:'Progetti completati',alignment:'center',text_color:'#FFFFFF',number_font_size:'48',label_color:'#94A3B8',icon_emoji:'check',icon_size:'24'})],
      [T('counter',{number:'12',label:'Anni di esperienza',alignment:'center',text_color:'#FFFFFF',number_font_size:'48',label_color:'#94A3B8',icon_emoji:'clock',icon_size:'24'})],
      [T('counter',{number:'24',label:'Membri del team',alignment:'center',text_color:'#FFFFFF',number_font_size:'48',label_color:'#94A3B8',icon_emoji:'users',icon_size:'24'})]
    ])
  ]),
  // Testimonial
  S({style:'default',width:'default',padding:'large'},{padding_top:'80',padding_bottom:'80'},{},[
    R('100',0,[[HL('Cosa dicono i nostri clienti','h2','lg','','center'),SP('40')]]),
    R('33-33-33',24,[
      [T('testimonial',{quote:'Un team eccezionale che ha trasformato la nostra presenza online.',author_name:'Laura Bianchi',author_role:'CEO, TechStart',rating:5})],
      [T('testimonial',{quote:'Professionali e creativi. Conversioni aumentate del 40%.',author_name:'Marco Rossi',author_role:'Marketing Director',rating:5})],
      [T('testimonial',{quote:'La migliore decisione per il nostro business digitale.',author_name:'Anna Verdi',author_role:'Founder, GreenTech',rating:5})]
    ])
  ]),
  // CTA
  S({style:'default',width:'expand',padding:'large'},{bg:{type:'gradient',gradient_angle:135,gradient_from:'#2563EB',gradient_to:'#1E40AF'},padding_top:'80',padding_bottom:'80'},{},[
    R('100',0,[[HL('Pronto a iniziare il tuo progetto?','h2','xl','#FFFFFF','center'),SP('12'),TX('Contattaci oggi per una consulenza gratuita.','rgba(255,255,255,0.85)','18'),SP('28'),BTN('Contattaci ora','/contatti','#FFFFFF','#1E40AF','center')]])
  ]),
  // Newsletter
  S({style:'default',width:'default',padding:'large'},{bg:{type:'solid',color:'#F9FAFB'},padding_top:'60',padding_bottom:'60'},{},[
    R('100',0,[[T('newsletter',{title:'Resta aggiornato',subtitle:'Iscriviti alla newsletter per novita e offerte esclusive.',layout:'horizontal',max_width:'560',alignment:'center',bg_color:'#FFFFFF',border_radius:16,padding:'40',btn_bg:'#2563EB'})]])
  ])
];

// SINGLE POST
const sp=[S({style:'default',width:'default',padding:'large'},{padding_top:'40',padding_bottom:'40'},{},[
  R('66-33',30,[
    [T('content',{source:'dynamic'})],
    [HL('Articoli recenti','h3','sm','','left'),SP('12'),T('postgrid',{post_type:'post',posts_per_page:4,columns:1,show_excerpt:false,show_image:true,image_ratio:'16:9',gap:16})]
  ],'start')
])];

// 404
const p404=[S({style:'default',width:'expand',padding:'xlarge'},{padding_top:'120',padding_bottom:'120'},{},[
  R('100',0,[[HL('404','h1','xl','#E5E7EB','center'),HL('Pagina non trovata','h2','lg','','center'),SP('12'),TX('La pagina che cerchi potrebbe essere stata spostata o non esiste.','#6B7280','16'),SP('28'),BTN('Torna alla homepage','/','#2563EB','#FFFFFF','center')]])
])];

// HEADER
const header=[S({style:'default',width:'expand',padding:'none'},{bg:{type:'solid',color:'#FFFFFF'},padding_top:'0',padding_bottom:'0'},{},[
  R('25-75',0,[
    [T('sitelogo',{source:'auto',max_height:42,link_home:true,alignment:'left'})],
    [T('megamenu',{menu_id:'auto',layout:'horizontal',alignment:'right',font_size:'15',font_weight:'500',text_color:'#374151',sticky:true,sticky_bg:'#FFFFFF',sticky_shadow:true,social_icons:true,search_icon:true})]
  ])
])];

// FOOTER
const footer=[S({style:'default',width:'expand',padding:'large'},{bg:{type:'solid',color:'#111827'},padding_top:'60',padding_bottom:'40'},{},[
  R('33-33-33',30,[
    [T('sitelogo',{source:'auto',max_height:36,link_home:true}),SP('12'),TX('Soluzioni innovative per il tuo business digitale.','#9CA3AF','14'),SP('16'),T('social',{style:'minimal',color:'#9CA3AF',hover_color:'#FFFFFF',size:'18',gap:'12'})],
    [HL('Link utili','h4','sm','#FFFFFF','left'),SP('12'),T('list',{items:[{text:'Chi siamo',url:'/chi-siamo'},{text:'Servizi',url:'/servizi'},{text:'Portfolio',url:'/portfolio'},{text:'Blog',url:'/blog'},{text:'Contatti',url:'/contatti'}],style:'none',color:'#9CA3AF',hover_color:'#FFFFFF',font_size:'14',gap:'8'})],
    [HL('Contatti','h4','sm','#FFFFFF','left'),SP('12'),TX('Via Roma 123, 38100 Trento\n+39 0461 123456\ninfo@example.com','#9CA3AF','14')]
  ],'start'),
  R('100',0,[[T('divider',{color:'#374151',width:'100',margin_top:'24',margin_bottom:'24'}),TX('2026 La Tua Azienda. Tutti i diritti riservati.','#6B7280','13')]])
])];

fs.writeFileSync(dir+'homepage.json',JSON.stringify(hp));
fs.writeFileSync(dir+'single-post.json',JSON.stringify(sp));
fs.writeFileSync(dir+'404.json',JSON.stringify(p404));
fs.writeFileSync(dir+'header.json',JSON.stringify(header));
fs.writeFileSync(dir+'footer.json',JSON.stringify(footer));
console.log('All built! homepage:',hp.length,'single:',sp.length,'404:',p404.length);
