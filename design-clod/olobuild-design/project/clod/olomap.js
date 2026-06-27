/* ════════════════════════════════════════════════════════════════
   OLOmap · infografica animata della mappa del sistema OLObuild.
   Una "camera" si addentra di livello in livello, in un unico spazio
   coordinato (zoom infinito): Sistema → Tema → Sezione → Controlli →
   Token colore. Didascalia e indicatore di profondità seguono il dive.
   Palette del sito (inchiostro · osso · lime); i token reali OLObuild
   compaiono solo in fondo, come "premio" dell'esplorazione.
   ════════════════════════════════════════════════════════════════ */
(function(){
  var svg=document.querySelector('.olomap__svg');
  if(!svg) return;
  var NS='http://www.w3.org/2000/svg';
  var reduce=matchMedia('(prefers-reduced-motion: reduce)').matches;
  var cap=document.querySelector('.hero .cap');
  var depthEl=document.querySelector('.olomap__depth');

  function el(t,attrs){ var e=document.createElementNS(NS,t); if(attrs) for(var k in attrs) e.setAttribute(k,attrs[k]); return e; }
  function pol(c,r,deg){ var a=deg*Math.PI/180; return {x:c.x+r*Math.cos(a), y:c.y+r*Math.sin(a)}; }

  // ── geometria (spazio 1000×1000) ──
  var C0={x:500,y:500};
  var R1=300;
  var themes=[
    {label:'Forge',deg:-110},
    {label:'Prisma',deg:-32,focus:true},
    {label:'Saffron',deg:38},
    {label:'Soundwave',deg:110},
    {label:'+46\ntemi',deg:175}
  ];
  var F1=pol(C0,R1,-32);
  var R2=64;
  var sezioni=[
    {label:'Hero',deg:-150,focus:true},
    {label:'Galleria',deg:-60},
    {label:'Griglia',deg:30},
    {label:'CTA',deg:120}
  ];
  var F2=pol(F1,R2,-150);
  var R3=15;
  var controlli=[
    {label:'Spazi',deg:-140},
    {label:'Bordi',deg:-50},
    {label:'Ombra',deg:40},
    {label:'Colore',deg:130,focus:true}
  ];
  var F3=pol(F2,R3,130);
  var R4=3.4;
  var tokens=[
    {label:'Primario',col:'#e1474f',deg:-135},
    {label:'Accento',col:'#f4a23b',deg:-45},
    {label:'Lime',col:'#C6F24E',deg:45},
    {label:'Scuro',col:'#16263d',deg:135}
  ];

  // ── costruzione DOM ──
  var gZoom=el('g',{'class':'zoom'}); svg.appendChild(gZoom);

  function addLink(layer,a,b,focus){
    var p=el('path',{'class':'lk'+(focus?' is-focus':''),
      d:'M'+a.x+' '+a.y+'L'+b.x+' '+b.y});
    layer.appendChild(p);
  }
  function addText(layer,x,y,label,fs,cls){
    var lines=String(label).split('\n');
    var t=el('text',{x:x,y:y,'font-size':fs}); if(cls) t.setAttribute('class',cls);
    var start=-(lines.length-1)*0.5*fs;
    lines.forEach(function(ln,i){
      var ts=el('tspan',{x:x,dy:(i===0?start:fs)}); ts.textContent=ln; t.appendChild(ts);
    });
    layer.appendChild(t);
  }
  function addNode(layer,c,r,label,fs,opt){
    opt=opt||{};
    var g=el('g',{'class':'nd'+(opt.focus?' is-focus':'')+(opt.root?' is-root':'')});
    if(opt.chip){
      g.appendChild(el('circle',{'class':'chip',cx:c.x,cy:c.y,r:r,fill:opt.chip}));
      addText(g,c.x,c.y+r*2.5,label,fs);
    }else{
      g.appendChild(el('circle',{cx:c.x,cy:c.y,r:r}));
      addText(g,c.x,c.y,label,fs);
    }
    layer.appendChild(g);
    return g;
  }

  // layer per livello (con scala "nativa" per il fade legato allo zoom)
  function layer(nat){ var g=el('g',{'class':'layer'}); g._nat=nat; gZoom.appendChild(g); return g; }

  var L1=layer(1), L2=layer(5.15), L3=layer(22), L4=layer(97), L0=layer(0.85);

  // L1 · temi (link dal root, nodi)
  var f1pos=null;
  themes.forEach(function(n){
    var p=pol(C0,R1,n.deg);
    addLink(L1,C0,p,n.focus);
    addNode(L1,p,54,n.label,19,{focus:n.focus});
    if(n.focus) f1pos=p;
  });
  // L2 · sezioni del tema in focus
  sezioni.forEach(function(n){
    var p=pol(F1,R2,n.deg);
    addLink(L2,F1,p,n.focus);
    addNode(L2,p,11,n.label,4.0,{focus:n.focus});
  });
  // L3 · controlli del tile in focus
  controlli.forEach(function(n){
    var p=pol(F2,R3,n.deg);
    addLink(L3,F2,p,n.focus);
    addNode(L3,p,2.55,n.label,0.98,{focus:n.focus});
  });
  // L4 · token colore (premio finale)
  tokens.forEach(function(n){
    var p=pol(F3,R4,n.deg);
    addLink(L4,F3,p,false);
    addNode(L4,p,0.62,n.label,0.2,{chip:n.col});
  });
  // L0 · nodo radice (sopra le linee di L1)
  addNode(L0,C0,58,'OLObuild',16,{root:true});

  // ── camera ──
  var kf=[
    {t:0.00,c:C0,s:1.00},
    {t:0.13,c:C0,s:1.07},
    {t:0.30,c:F1,s:5.15},
    {t:0.45,c:F1,s:5.45},
    {t:0.60,c:F2,s:22},
    {t:0.72,c:F2,s:23.2},
    {t:0.84,c:F3,s:97},
    {t:0.90,c:F3,s:100},
    {t:1.00,c:C0,s:1.00}
  ];
  function smooth(u){ return u<0?0:u>1?1:u*u*(3-2*u); }
  function camAt(tt){
    var a=kf[0],b=kf[kf.length-1];
    for(var i=0;i<kf.length-1;i++){ if(tt>=kf[i].t && tt<=kf[i+1].t){ a=kf[i]; b=kf[i+1]; break; } }
    var u=(tt-a.t)/((b.t-a.t)||1), e=smooth(u);
    var s=Math.exp(Math.log(a.s)+(Math.log(b.s)-Math.log(a.s))*e);
    return {x:a.c.x+(b.c.x-a.c.x)*e, y:a.c.y+(b.c.y-a.c.y)*e, s:s};
  }
  function lvlOp(s,nat){
    var lr=Math.log(s/nat);
    if(lr<-1.6||lr>1.75) return 0;
    if(lr<-0.55) return (lr+1.6)/1.05;
    if(lr<=0.9) return 1;
    return 1-(lr-0.9)/0.85;
  }
  var layers=[L0,L1,L2,L3,L4];

  var lastPath='';
  function setReadout(s){
    var path,depth;
    if(s<2.6){ path='OLObuild · sistema'; depth=1; }
    else if(s<11){ path='OLObuild / Prisma'; depth=2; }
    else if(s<50){ path='OLObuild / Prisma / Hero'; depth=3; }
    else { path='OLObuild / Prisma / Hero / Colore'; depth=4; }
    if(path!==lastPath){
      lastPath=path;
      if(cap) cap.textContent=path;
      if(depthEl) depthEl.innerHTML='<i></i>L'+depth+' / L4';
    }
  }

  function applyCam(cam){
    gZoom.setAttribute('transform','translate('+(500-cam.s*cam.x).toFixed(2)+','+(500-cam.s*cam.y).toFixed(2)+') scale('+cam.s.toFixed(4)+')');
    for(var i=0;i<layers.length;i++){ layers[i].setAttribute('opacity',lvlOp(cam.s,layers[i]._nat).toFixed(3)); }
    setReadout(cam.s);
  }

  if(reduce){
    applyCam({x:C0.x,y:C0.y,s:1.0});
    return;
  }

  var DUR=21000, t0=performance.now();
  function frame(now){
    var tt=((now-t0)%DUR)/DUR;
    applyCam(camAt(tt));
    requestAnimationFrame(frame);
  }
  requestAnimationFrame(frame);
})();
