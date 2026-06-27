/* ════════════════════════════════════════════════════════════════
   CLOD · EVOLUZIONE v2 — effetti "folle" · sala di regia spinta.
   Ogni IIFE è autonoma: mappa 1:1 su una tile OLObuild (o su uno
   script di tema per il chrome globale). Nessuna dipendenza tra
   blocchi; tutti rispettano prefers-reduced-motion / pointer:fine.
   ════════════════════════════════════════════════════════════════ */
(function(){
  var reduce=matchMedia('(prefers-reduced-motion: reduce)').matches;
  var fine=matchMedia('(pointer: fine)').matches;
  function clamp(v,a,b){ return Math.max(a,Math.min(b,v)); }
  function pad2(n){ return (n<10?'0':'')+n; }
  function pad4(n){ n=Math.max(0,Math.round(n)); var s=String(n); while(s.length<4) s='0'+s; return s; }

  /* ── [2] HUD mirino · coordinate + sezione corrente ── */
  (function(){
    if(!fine||reduce) return;
    var hud=document.createElement('div'); hud.className='fx-hud';
    hud.innerHTML='<i class="h"></i><i class="v"></i><span class="tag"><b>0000 \u00b7 0000</b><em>\u2014</em></span>';
    document.body.appendChild(hud);
    var tagB=hud.querySelector('b'), tagE=hud.querySelector('em');
    var secs=[].slice.call(document.querySelectorAll('[data-screen-label]'));
    var mx=0,my=0,tk=false;
    function upd(){
      tk=false;
      hud.style.setProperty('--mx',mx+'px'); hud.style.setProperty('--my',my+'px');
      tagB.textContent=pad4(mx)+' \u00b7 '+pad4(my+(window.scrollY||0));
      var lab='\u2014';
      for(var i=0;i<secs.length;i++){
        var r=secs[i].getBoundingClientRect();
        if(my>=r.top&&my<=r.bottom){ lab=secs[i].getAttribute('data-screen-label'); break; }
      }
      tagE.textContent=lab;
    }
    addEventListener('pointermove',function(e){
      mx=e.clientX; my=e.clientY; hud.classList.add('on');
      if(!tk){ tk=true; requestAnimationFrame(upd); }
    },{passive:true});
    document.documentElement.addEventListener('pointerleave',function(){ hud.classList.remove('on'); });
  })();

  /* ── [3] timecode + hairline di progresso nella nav ── */
  (function(){
    var nav=document.querySelector('.nav'); if(!nav) return;
    var tc=document.getElementById('fxTc');
    var DUR=90, FPS=25, tk=false;
    function upd(){
      tk=false;
      var max=document.documentElement.scrollHeight-innerHeight;
      var p=max>0?clamp((window.scrollY||0)/max,0,1):0;
      nav.style.setProperty('--p',p.toFixed(4));
      if(tc){
        var fr=Math.round(p*DUR*FPS), s=Math.floor(fr/FPS), f=fr%FPS;
        tc.textContent='TC 00:'+pad2(Math.floor(s/60))+':'+pad2(s%60)+':'+pad2(f);
      }
    }
    addEventListener('scroll',function(){ if(!tk){ tk=true; requestAnimationFrame(upd); } },{passive:true});
    addEventListener('resize',upd); upd();
  })();

  /* ── [4] hero · split lettere "Visual" ── */
  (function(){
    if(reduce) return;
    var h=document.querySelector('.hero__h'); if(!h) return;
    var tn=h.firstChild; if(!tn||tn.nodeType!==3) return;
    var txt=tn.textContent, frag=document.createDocumentFragment();
    for(var i=0;i<txt.length;i++){
      var s=document.createElement('span'); s.className='fx-lt';
      s.style.setProperty('--i',i); s.textContent=txt[i]; frag.appendChild(s);
    }
    h.replaceChild(frag,tn);
  })();

  /* ── [5] manifesto · scrub parola-per-parola allo scroll ── */
  (function(){
    if(reduce) return;
    var targets=[].slice.call(document.querySelectorAll('.manifesto p'));
    if(!targets.length) return;
    function split(root){
      var ws=[];
      (function walk(n){
        [].slice.call(n.childNodes).forEach(function(ch){
          if(ch.nodeType===3){
            if(!ch.textContent.trim()) return;
            var parts=ch.textContent.split(/(\s+)/), fr=document.createDocumentFragment();
            parts.forEach(function(p){
              if(!p) return;
              if(/^\s+$/.test(p)){ fr.appendChild(document.createTextNode(p)); return; }
              var s=document.createElement('span'); s.className='fx-w'; s.textContent=p;
              fr.appendChild(s); ws.push(s);
            });
            n.replaceChild(fr,ch);
          }else if(ch.nodeType===1&&ch.tagName!=='BR'){ walk(ch); }
        });
      })(root);
      return ws;
    }
    var sets=targets.map(function(t){ t.setAttribute('data-fx-scrub',''); return {el:t,ws:split(t)}; });
    var tk=false;
    function upd(){
      tk=false;
      sets.forEach(function(st){
        var r=st.el.getBoundingClientRect();
        var p=clamp((innerHeight*0.9-r.top)/(innerHeight*0.6),0,1);
        var n=Math.round(p*st.ws.length);
        st.ws.forEach(function(w,i){ w.classList.toggle('on',i<n); });
      });
    }
    addEventListener('scroll',function(){ if(!tk){ tk=true; requestAnimationFrame(upd); } },{passive:true});
    addEventListener('resize',upd); upd();
  })();

  /* ── [6] servizi · monitor di anteprima che segue il cursore ── */
  (function(){
    if(!fine||reduce) return;
    var rows=[].slice.call(document.querySelectorAll('.srv__row')); if(!rows.length) return;
    var mon=document.createElement('div'); mon.className='fx-mon';
    mon.innerHTML='<div class="scr"><i></i><i></i><i></i><i></i><span class="rec">\u25cf STILL</span></div>'+
      '<div class="lab"><b>01</b><span>Consulenza</span></div>';
    document.body.appendChild(mon);
    var labB=mon.querySelector('.lab b'), labS=mon.querySelector('.lab span');
    rows.forEach(function(row){
      row.addEventListener('pointerenter',function(){
        var n=row.querySelector('.srv__n'), nm=row.querySelector('.srv__name');
        labB.textContent=n?n.textContent:'';
        labS.textContent=nm?nm.textContent:'';
        mon.classList.add('on');
      });
      row.addEventListener('pointerleave',function(){ mon.classList.remove('on'); });
      row.addEventListener('pointermove',function(e){
        mon.style.left=e.clientX+'px'; mon.style.top=e.clientY+'px';
      });
    });
  })();

  /* ── [7a] strip · wobble da velocità di scroll ── */
  (function(){
    if(reduce) return;
    var strip=document.querySelector('.strip'); if(!strip) return;
    var lastY=window.scrollY||0, lastT=performance.now(), vel=0, sk=0, raf=0;
    function loop(){
      sk+=(clamp(vel*0.006,-3.5,3.5)-sk)*0.1;
      vel*=0.9;
      if(Math.abs(sk)<0.03&&Math.abs(vel)<2){ strip.style.transform=''; raf=0; return; }
      strip.style.transform='skewX('+(-sk).toFixed(2)+'deg)';
      raf=requestAnimationFrame(loop);
    }
    addEventListener('scroll',function(){
      var now=performance.now(), dt=Math.max(16,now-lastT);
      vel=((window.scrollY||0)-lastY)/dt*1000; lastY=window.scrollY||0; lastT=now;
      if(!raf) raf=requestAnimationFrame(loop);
    },{passive:true});
  })();

  /* ── [7b] reel · i fotogrammi si inclinano con la velocità del drag ── */
  (function(){
    if(reduce) return;
    var sc=document.querySelector('[data-reel-scroller]'); if(!sc) return;
    var items=[].slice.call(sc.querySelectorAll('.reel__item')); if(!items.length) return;
    var last=sc.scrollLeft, sk=0, raf=0;
    function loop(){
      var v=sc.scrollLeft-last; last=sc.scrollLeft;
      sk+=(clamp(v*0.18,-7,7)-sk)*0.14;
      if(Math.abs(sk)<0.04&&v===0){
        items.forEach(function(i){ i.style.transform=''; }); raf=0; return;
      }
      items.forEach(function(i){ i.style.transform='skewX('+(-sk).toFixed(2)+'deg)'; });
      raf=requestAnimationFrame(loop);
    }
    sc.addEventListener('scroll',function(){ if(!raf) raf=requestAnimationFrame(loop); },{passive:true});
  })();

  /* ── [8] CTA · onda di lettere + mail magnetica ── */
  (function(){
    if(!fine||reduce) return;
    var cta=document.querySelector('.cta'); if(!cta) return;
    var h=cta.querySelector('h2'), lts=[];
    if(h){
      var txt=h.textContent; h.textContent='';
      for(var i=0;i<txt.length;i++){
        var s=document.createElement('span'); s.className='fx-ct'; s.textContent=txt[i];
        h.appendChild(s); lts.push(s);
      }
    }
    var mail=cta.querySelector('.cta__mail');
    cta.addEventListener('pointermove',function(e){
      lts.forEach(function(s){
        var r=s.getBoundingClientRect(), cx=r.left+r.width/2, cy=r.top+r.height/2;
        var d=Math.hypot(e.clientX-cx,e.clientY-cy);
        var y=-26*Math.exp(-(d*d)/(2*150*150));
        s.style.transform='translateY('+y.toFixed(1)+'px)';
        s.classList.toggle('up',y<-10);
      });
      if(mail){
        var r2=mail.getBoundingClientRect();
        var dx=e.clientX-(r2.left+r2.width/2), dy=e.clientY-(r2.top+r2.height/2);
        var d2=Math.hypot(dx,dy);
        if(d2<170){ var k=(1-d2/170)*0.3; mail.style.transform='translate('+(dx*k).toFixed(1)+'px,'+(dy*k).toFixed(1)+'px)'; }
        else{ mail.style.transform=''; }
      }
    });
    cta.addEventListener('pointerleave',function(){
      lts.forEach(function(s){ s.style.transform=''; s.classList.remove('up'); });
      if(mail) mail.style.transform='';
    });
  })();
})();
