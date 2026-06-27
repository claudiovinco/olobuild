/* ════════════════════════════════════════════════════════════════
   OLOthemes · FX — shared "wow" interaction layer for the theme demos.
   Dependency-free. Safe to add to any theme: drop <script src="olothemes/fx.js">
   and put data-fx on <body>. Honours prefers-reduced-motion & touch.

   Auto (when body[data-fx] + fine pointer + motion ok):
     · custom cursor (dot + trailing ring, blend-mode difference)
     · [data-magnetic]  → element eases toward the cursor
   Opt-in via attributes (work on touch too where sensible):
     · [data-menu-toggle="#id"] + [data-menu] → fullscreen creative menu,
       links stagger in; [data-menu] links get a hover sweep.
     · [data-tilt]      → 3D tilt toward cursor (desktop only)
     · [data-spotlight] → radial light follows cursor (--mx/--my set)
     · [data-peek] on a list; children [data-peek-item] with
       data-label → a floating placeholder panel follows the cursor.
   ════════════════════════════════════════════════════════════════ */
(function(){
  var mqFine = window.matchMedia('(pointer:fine)').matches;
  var mqMotion = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  function ready(fn){ if(document.readyState!=='loading') fn(); else document.addEventListener('DOMContentLoaded', fn); }

  ready(function(){
    injectCSS();
    setupMenu();
    setupSpotlight();
    setupPeek();
    setupHotspot();
    setupHscroll();
    setupCountdown();
    setupFinder();
    setupBuilder();
    setupMixer();
    setupProjector();
    setupTypeTester();
    setupScrub();
    setupTimezone();
    setupBakers();
    setupSequencer();
    setupPalette();
    setupContrast();
    setupRecipe();
    setupHeat();
    setupFloorplan();
    setupLookbook();
    setupSpin();
    if(mqFine && mqMotion){
      setupTilt();
      setupSmear();
      if(document.body.hasAttribute('data-fx')){ setupCursor(); setupMagnetic(); }
    }
  });

  /* ---------- image hotspots ("shop the room/look") ---------- */
  function setupHotspot(){
    [].forEach.call(document.querySelectorAll('[data-hotspot]'), function(zone){
      [].forEach.call(zone.querySelectorAll('[data-dot]'), function(dot){
        var card = dot.querySelector('.fx-hot__card');
        dot.addEventListener('mouseenter', function(){ if(card){ card.style.opacity=1; card.style.transform='translateY(0)'; card.style.pointerEvents='auto'; } dot.classList.add('on'); });
        dot.addEventListener('mouseleave', function(){ if(card){ card.style.opacity=0; card.style.transform='translateY(8px)'; card.style.pointerEvents='none'; } dot.classList.remove('on'); });
      });
    });
  }

  /* ---------- drag-scroll horizontal rails ---------- */
  function setupHscroll(){
    [].forEach.call(document.querySelectorAll('[data-hscroll]'), function(r){
      var down=false, sx, sl;
      r.addEventListener('pointerdown', function(e){ down=true; r.classList.add('grab'); sx=e.clientX; sl=r.scrollLeft; });
      addEventListener('pointerup', function(){ down=false; r.classList.remove('grab'); });
      r.addEventListener('pointermove', function(e){ if(!down) return; r.scrollLeft = sl - (e.clientX-sx); });
      r.addEventListener('wheel', function(e){ if(Math.abs(e.deltaY)>Math.abs(e.deltaX)){ r.scrollLeft += e.deltaY; e.preventDefault(); } }, {passive:false});
    });
  }

  /* ---------- live countdown ---------- */
  function setupCountdown(){
    [].forEach.call(document.querySelectorAll('[data-countdown]'), function(c){
      var target = new Date(c.getAttribute('data-countdown')).getTime();
      var units = {days:c.querySelector('[data-cd="days"]'),hours:c.querySelector('[data-cd="hours"]'),mins:c.querySelector('[data-cd="mins"]'),secs:c.querySelector('[data-cd="secs"]')};
      function pad(n){ return (n<10?'0':'')+n; }
      function tick(){
        var d=Math.max(0,target-Date.now()), s=Math.floor(d/1000);
        if(units.days) units.days.textContent = Math.floor(s/86400);
        if(units.hours) units.hours.textContent = pad(Math.floor(s%86400/3600));
        if(units.mins) units.mins.textContent = pad(Math.floor(s%3600/60));
        if(units.secs) units.secs.textContent = pad(s%60);
      }
      tick(); setInterval(tick, 1000);
    });
  }

  /* ---------- paint-smear cursor (for a [data-smear] zone) ---------- */
  function setupSmear(){
    [].forEach.call(document.querySelectorAll('[data-smear]'), function(z){
      var cols = (z.getAttribute('data-smear')||'#fff').split(','), last=0;
      z.addEventListener('pointermove', function(e){
        var now=Date.now(); if(now-last<36) return; last=now;
        var r=z.getBoundingClientRect(), b=el('span','fx-smear');
        b.style.left=(e.clientX-r.left)+'px'; b.style.top=(e.clientY-r.top)+'px';
        b.style.background=cols[Math.floor(Math.random()*cols.length)];
        var sz=24+Math.random()*40; b.style.width=b.style.height=sz+'px';
        z.appendChild(b); setTimeout(function(){ b.style.opacity=0; b.style.transform='translate(-50%,-50%) scale(2.2)'; }, 20);
        setTimeout(function(){ b.remove(); }, 900);
      });
    });
  }

  /* ---------- custom cursor ---------- */
  function setupCursor(){
    var dot = el('div','fx-cur fx-cur--dot'), ring = el('div','fx-cur fx-cur--ring');
    document.body.appendChild(dot); document.body.appendChild(ring);
    document.documentElement.classList.add('fx-cursor-on');
    var mx=innerWidth/2, my=innerHeight/2, rx=mx, ry=my, vis=false;
    addEventListener('mousemove', function(e){
      mx=e.clientX; my=e.clientY;
      dot.style.transform='translate('+mx+'px,'+my+'px)';
      if(!vis){ vis=true; dot.style.opacity=ring.style.opacity=1; }
      var t=e.target&&e.target.closest?e.target.closest('a,button,[data-cursor],.btn,input,textarea,select'):null;
      var typing = t && /^(INPUT|TEXTAREA|SELECT)$/.test(t.tagName);
      document.documentElement.classList.toggle('fx-cur-hot', !!t && !typing);
      document.documentElement.classList.toggle('fx-cur-text', !!typing);
      var lbl = t && t.getAttribute('data-cursor-label');
      ring.setAttribute('data-label', lbl||'');
      ring.classList.toggle('has-label', !!lbl);
    });
    addEventListener('mouseleave', function(){ vis=false; dot.style.opacity=ring.style.opacity=0; });
    (function loop(){ rx+=(mx-rx)*0.18; ry+=(my-ry)*0.18; ring.style.transform='translate('+rx+'px,'+ry+'px)'; requestAnimationFrame(loop); })();
  }

  /* ---------- magnetic ---------- */
  function setupMagnetic(){
    [].forEach.call(document.querySelectorAll('[data-magnetic]'), function(m){
      var s = parseFloat(m.getAttribute('data-magnetic'))||0.35;
      m.style.transition='transform .25s cubic-bezier(.2,.7,.3,1)';
      m.addEventListener('mousemove', function(e){
        var r=m.getBoundingClientRect();
        m.style.transform='translate('+((e.clientX-r.left-r.width/2)*s)+'px,'+((e.clientY-r.top-r.height/2)*s)+'px)';
      });
      m.addEventListener('mouseleave', function(){ m.style.transform=''; });
    });
  }

  /* ---------- fullscreen creative menu ---------- */
  function setupMenu(){
    [].forEach.call(document.querySelectorAll('[data-menu-toggle]'), function(btn){
      var sel = btn.getAttribute('data-menu-toggle');
      var menu = sel ? document.querySelector(sel) : document.querySelector('[data-menu]');
      if(!menu) return;
      var links = [].slice.call(menu.querySelectorAll('a'));
      links.forEach(function(a,i){ a.style.setProperty('--i', i); });
      function open(){ menu.classList.add('open'); document.documentElement.style.overflow='hidden'; }
      function close(){ menu.classList.remove('open'); document.documentElement.style.overflow=''; }
      btn.addEventListener('click', function(){ menu.classList.contains('open')?close():open(); });
      menu.addEventListener('click', function(e){ if(e.target.closest('a')||e.target.hasAttribute('data-menu-close')) close(); });
      document.addEventListener('keydown', function(e){ if(e.key==='Escape') close(); });
    });
  }

  /* ---------- spotlight ---------- */
  function setupSpotlight(){
    [].forEach.call(document.querySelectorAll('[data-spotlight]'), function(z){
      z.addEventListener('mousemove', function(e){
        var r=z.getBoundingClientRect();
        z.style.setProperty('--mx', ((e.clientX-r.left)/r.width*100)+'%');
        z.style.setProperty('--my', ((e.clientY-r.top)/r.height*100)+'%');
      });
    });
  }

  /* ---------- tilt ---------- */
  function setupTilt(){
    [].forEach.call(document.querySelectorAll('[data-tilt]'), function(c){
      var max = parseFloat(c.getAttribute('data-tilt'))||8;
      c.style.transformStyle='preserve-3d'; c.style.transition='transform .2s ease';
      c.addEventListener('mousemove', function(e){
        var r=c.getBoundingClientRect(), px=(e.clientX-r.left)/r.width-0.5, py=(e.clientY-r.top)/r.height-0.5;
        c.style.transform='perspective(900px) rotateY('+(px*max)+'deg) rotateX('+(-py*max)+'deg)';
      });
      c.addEventListener('mouseleave', function(){ c.style.transform=''; });
    });
  }

  /* ---------- peek (floating image follows cursor over a list) ---------- */
  function setupPeek(){
    [].forEach.call(document.querySelectorAll('[data-peek]'), function(list){
      if(!mqFine){ return; }
      var panel = el('div','fx-peek');
      list.appendChild(panel); var raf, tx=0, ty=0, cx=0, cy=0, on=false;
      [].forEach.call(list.querySelectorAll('[data-peek-item]'), function(it){
        it.addEventListener('mouseenter', function(){ panel.setAttribute('data-label', it.getAttribute('data-label')||''); panel.classList.add('show'); on=true; });
        it.addEventListener('mouseleave', function(){ panel.classList.remove('show'); on=false; });
      });
      list.addEventListener('mousemove', function(e){ var r=list.getBoundingClientRect(); tx=e.clientX-r.left; ty=e.clientY-r.top; });
      (function loop(){ cx+=(tx-cx)*0.14; cy+=(ty-cy)*0.14; if(on) panel.style.transform='translate('+cx+'px,'+cy+'px)'; requestAnimationFrame(loop); })();
    });
  }

  /* ---------- finder (one-tap recommender) ---------- */
  function setupFinder(){
    [].forEach.call(document.querySelectorAll('[data-finder]'), function(f){
      var opts=[].slice.call(f.querySelectorAll('[data-finder-opt]'));
      var res=[].slice.call(f.querySelectorAll('[data-finder-res]'));
      function pick(k){
        opts.forEach(function(o){ o.classList.toggle('on', o.getAttribute('data-finder-opt')===k); });
        res.forEach(function(r){ r.classList.toggle('show', r.getAttribute('data-finder-res')===k); });
      }
      opts.forEach(function(o){ o.addEventListener('click', function(){ pick(o.getAttribute('data-finder-opt')); }); });
      if(opts[0]) pick(opts[0].getAttribute('data-finder-opt'));
    });
  }

  /* ---------- builder (add items → live total) ---------- */
  function setupBuilder(){
    [].forEach.call(document.querySelectorAll('[data-builder]'), function(b){
      var cur=b.getAttribute('data-currency')||'€';
      var totalEl=b.querySelector('[data-bld-total]'), itemsEl=b.querySelector('[data-bld-items]');
      var cap=parseInt(b.getAttribute('data-cap'))||0;
      function count(){ var n=0; [].forEach.call(b.querySelectorAll('[data-bld-item]'),function(x){n+=parseInt(x.getAttribute('data-n'))||0;}); return n; }
      function render(){
        var total=0;
        [].forEach.call(b.querySelectorAll('[data-bld-item]'), function(it){
          var n=parseInt(it.getAttribute('data-n'))||0, p=parseFloat(it.getAttribute('data-price'))||0;
          total+=n*p;
          var c=it.querySelector('[data-bld-count]'); if(c) c.textContent=n;
          it.classList.toggle('on', n>0);
        });
        var items=count();
        if(totalEl) totalEl.textContent=cur+total.toFixed(2).replace(/\.00$/,'');
        if(itemsEl) itemsEl.textContent=items;
        b.classList.toggle('bld-full', !!cap&&items>=cap);
      }
      b.addEventListener('click', function(e){
        var inc=e.target.closest('[data-bld-inc]'), dec=e.target.closest('[data-bld-dec]');
        if(!inc&&!dec) return;
        var it=(inc||dec).closest('[data-bld-item]'); if(!it) return;
        var n=parseInt(it.getAttribute('data-n'))||0;
        if(inc){ if(cap&&count()>=cap) return; n++; } else { n=Math.max(0,n-1); }
        it.setAttribute('data-n',n); render();
      });
      render();
    });
  }

  /* ---------- mixer (blend swatches → live preview) ---------- */
  function setupMixer(){
    [].forEach.call(document.querySelectorAll('[data-mixer]'), function(m){
      var max=parseInt(m.getAttribute('data-max'))||3;
      var prev=m.querySelector('[data-mix-preview]'), nameEl=m.querySelector('[data-mix-out]');
      var chosen=[];
      function blend(){
        var r=0,g=0,b=0;
        chosen.forEach(function(x){ var h=x.c.replace('#',''); if(h.length===3) h=h.replace(/./g,'$&$&'); r+=parseInt(h.slice(0,2),16); g+=parseInt(h.slice(2,4),16); b+=parseInt(h.slice(4,6),16); });
        var n=chosen.length||1; return 'rgb('+Math.round(r/n)+','+Math.round(g/n)+','+Math.round(b/n)+')';
      }
      function syncSwatches(){ [].forEach.call(m.querySelectorAll('[data-mix]'), function(s){ s.classList.toggle('on', chosen.some(function(x){return x.c===s.getAttribute('data-mix');})); }); }
      function render(){
        if(prev){ prev.style.background = chosen.length? blend() : 'transparent'; }
        if(nameEl) nameEl.textContent = chosen.length? chosen.map(function(x){return x.n;}).join(' + ') : (m.getAttribute('data-empty')||'Pick to blend');
      }
      [].forEach.call(m.querySelectorAll('[data-mix]'), function(sw){
        sw.style.setProperty('--sw', sw.getAttribute('data-mix'));
        sw.addEventListener('click', function(){
          var c=sw.getAttribute('data-mix'), n=sw.getAttribute('data-mix-name')||c;
          var idx=chosen.map(function(x){return x.c;}).indexOf(c);
          if(idx>=0){ chosen.splice(idx,1); }
          else { if(chosen.length>=max) chosen.shift(); chosen.push({c:c,n:n}); }
          syncSwatches(); render();
        });
      });
      render();
    });
  }

  /* ---------- projector (slider → projected value) ---------- */
  function setupProjector(){
    [].forEach.call(document.querySelectorAll('[data-project]'), function(p){
      var input=p.querySelector('[data-project-input]'), out=p.querySelector('[data-project-out]'), contribOut=p.querySelector('[data-project-contrib]');
      var ra=p.getAttribute('data-rate'), ya=p.getAttribute('data-years');
      var rate=(ra==null||ra==='')?0.06:parseFloat(ra), years=(ya==null||ya==='')?10:parseFloat(ya);
      var cur, ca=p.getAttribute('data-currency'); cur=(ca==null)?'€':ca;
      var fmt=new Intl.NumberFormat('en-US',{maximumFractionDigits:0});
      function render(){
        var c=parseFloat(input.value)||0;
        var fv = rate===0 ? c*years : c*((Math.pow(1+rate,years)-1)/rate);
        if(out) out.textContent=cur+fmt.format(Math.round(fv));
        if(contribOut) contribOut.textContent=cur+fmt.format(c);
        input.style.setProperty('--pct', ((input.value-input.min)/(input.max-input.min)*100)+'%');
      }
      if(input){ input.addEventListener('input', render); render(); }
    });
  }

  /* ---------- type tester (live variable-type specimen) ---------- */
  function setupTypeTester(){
    [].forEach.call(document.querySelectorAll('[data-type-tester]'), function(tt){
      var spec=tt.querySelector('[data-tt-specimen]'); if(!spec) return;
      var axes=[].slice.call(tt.querySelectorAll('[data-tt-axis]'));
      function fmt(name,v){
        if(name==='letter-spacing') return (v/100).toFixed(2)+'em';
        if(name==='line-height') return (v/100).toFixed(2);
        if(name==='font-size') return Math.round(v)+'px';
        return Math.round(v);
      }
      function apply(a){
        var name=a.getAttribute('data-tt-axis'), v=parseFloat(a.value);
        var css = name==='letter-spacing' ? (v/100)+'em' : name==='line-height' ? (v/100) : name==='font-size' ? v+'px' : v;
        spec.style.setProperty(name, css);
        a.style.setProperty('--pct', ((a.value-a.min)/(a.max-a.min)*100)+'%');
        var out=tt.querySelector('[data-tt-val="'+name+'"]'); if(out) out.textContent=fmt(name,v);
      }
      axes.forEach(function(a){ a.addEventListener('input', function(){ apply(a); }); apply(a); });
    });
  }

  /* ---------- timeline scrubber (drag a handle across stops) ---------- */
  function setupScrub(){
    [].forEach.call(document.querySelectorAll('[data-scrub]'), function(z){
      var input=z.querySelector('[data-scrub-input]');
      var panels=[].slice.call(z.querySelectorAll('[data-scrub-panel]'));
      var stops=[].slice.call(z.querySelectorAll('[data-scrub-go]'));
      if(!panels.length) return;
      function show(i){
        i=Math.max(0,Math.min(panels.length-1, i|0));
        panels.forEach(function(p,j){ p.classList.toggle('show', j===i); });
        stops.forEach(function(s){ s.classList.toggle('on', parseInt(s.getAttribute('data-scrub-go'))===i); });
        if(input){ input.value=i; input.style.setProperty('--pct', (panels.length<2?0:i/(panels.length-1)*100)+'%'); }
      }
      if(input) input.addEventListener('input', function(){ show(parseInt(input.value)); });
      stops.forEach(function(s){ s.addEventListener('click', function(){ show(parseInt(s.getAttribute('data-scrub-go'))); }); });
      show(0);
    });
  }

  /* ---------- timezone meeting planner (24h slider → multi-city clocks) ---------- */
  function setupTimezone(){
    [].forEach.call(document.querySelectorAll('[data-timezone]'), function(z){
      var input=z.querySelector('[data-tz-input]');
      var cities=[].slice.call(z.querySelectorAll('[data-tz-city]'));
      var verdict=z.querySelector('[data-tz-verdict]');
      var baseLbl=z.querySelector('[data-tz-base]');
      if(!input||!cities.length) return;
      var base=parseFloat(cities[0].getAttribute('data-offset'))||0;
      function pad(n){ return (n<10?'0':'')+n; }
      function render(){
        var local0=parseInt(input.value)||0, utc=local0-base, work=0;
        cities.forEach(function(c){
          var off=parseFloat(c.getAttribute('data-offset'))||0;
          var h=((Math.round(utc+off)%24)+24)%24;
          var clk=c.querySelector('[data-tz-clock]'); if(clk) clk.textContent=pad(h)+':00';
          var state = (h>=9&&h<18)?'work' : ((h>=7&&h<9)||(h>=18&&h<22))?'ok' : 'sleep';
          c.setAttribute('data-state', state);
          if(state==='work') work++;
        });
        input.style.setProperty('--pct', (local0/23*100)+'%');
        if(baseLbl) baseLbl.textContent=pad(local0)+':00';
        if(verdict){
          var n=cities.length;
          verdict.textContent = work===n ? 'Everyone’s in working hours' : work+' of '+n+' in working hours';
          verdict.setAttribute('data-ok', work===n?'1':'0');
        }
      }
      input.addEventListener('input', render); render();
    });
  }

  /* ---------- baker's percentage (flour weight → scaled recipe) ---------- */
  function setupBakers(){
    [].forEach.call(document.querySelectorAll('[data-bakers]'), function(z){
      var input=z.querySelector('[data-bk-input]');
      var ings=[].slice.call(z.querySelectorAll('[data-bk-ing]'));
      var totalEl=z.querySelector('[data-bk-total]'), flourEl=z.querySelector('[data-bk-flour]');
      if(!input) return;
      function render(){
        var flour=parseFloat(input.value)||0, total=flour;
        ings.forEach(function(it){
          var pct=parseFloat(it.getAttribute('data-pct'))||0, g=Math.round(flour*pct/100);
          var out=it.querySelector('[data-bk-out]'); if(out) out.textContent=g+' g';
          total+=g;
        });
        if(flourEl) flourEl.textContent=Math.round(flour)+' g';
        if(totalEl) totalEl.textContent=Math.round(total)+' g';
        input.style.setProperty('--pct', ((flour-input.min)/(input.max-input.min)*100)+'%');
      }
      input.addEventListener('input', render); render();
    });
  }

  /* ---------- step sequencer (Web Audio drum grid) ---------- */
  function setupSequencer(){
    [].forEach.call(document.querySelectorAll('[data-seq]'), function(z){
      var rows=[].slice.call(z.querySelectorAll('[data-seq-row]'));
      var playBtn=z.querySelector('[data-seq-play]');
      var bpm=z.querySelector('[data-seq-bpm]'), bpmOut=z.querySelector('[data-seq-bpm-val]');
      if(!rows.length||!playBtn) return;
      var ctx=null, noise=null, timer=null, step=0, playing=false;
      var stepsN=rows[0].querySelectorAll('[data-seq-cell]').length;
      function tone(sound,t){
        var g=ctx.createGain(); g.connect(ctx.destination);
        if(sound==='kick'){
          var o=ctx.createOscillator(); o.type='sine';
          o.frequency.setValueAtTime(160,t); o.frequency.exponentialRampToValueAtTime(48,t+0.12);
          g.gain.setValueAtTime(0.9,t); g.gain.exponentialRampToValueAtTime(0.001,t+0.18);
          o.connect(g); o.start(t); o.stop(t+0.2);
        } else {
          var s=ctx.createBufferSource(); s.buffer=noise; var f=ctx.createBiquadFilter();
          if(sound==='hat'){ f.type='highpass'; f.frequency.value=7000; g.gain.setValueAtTime(0.3,t); g.gain.exponentialRampToValueAtTime(0.001,t+0.05); s.start(t); s.stop(t+0.06); }
          else if(sound==='clap'){ f.type='bandpass'; f.frequency.value=1600; f.Q.value=1.2; g.gain.setValueAtTime(0.45,t); g.gain.exponentialRampToValueAtTime(0.001,t+0.1); s.start(t); s.stop(t+0.11); }
          else { f.type='highpass'; f.frequency.value=1400; g.gain.setValueAtTime(0.5,t); g.gain.exponentialRampToValueAtTime(0.001,t+0.14); s.start(t); s.stop(t+0.15); }
          s.connect(f); f.connect(g);
        }
      }
      rows.forEach(function(r){
        [].forEach.call(r.querySelectorAll('[data-seq-cell]'), function(c){
          c.setAttribute('role','checkbox'); c.setAttribute('aria-checked','false');
          c.addEventListener('click', function(){
            var on=c.classList.toggle('on'); c.setAttribute('aria-checked', on?'true':'false');
            if(on && ctx) tone(r.getAttribute('data-sound'), ctx.currentTime);
          });
        });
      });
      function interval(){ return (60/(parseInt(bpm&&bpm.value)||120))/4*1000; }
      function tick(){
        rows.forEach(function(r){
          var cells=r.querySelectorAll('[data-seq-cell]');
          for(var i=0;i<cells.length;i++) cells[i].classList.toggle('play', i===step);
          if(cells[step] && cells[step].classList.contains('on')) tone(r.getAttribute('data-sound'), ctx.currentTime);
        });
        step=(step+1)%stepsN;
      }
      function start(){
        if(!ctx){ ctx=new (window.AudioContext||window.webkitAudioContext)(); var b=ctx.createBuffer(1,ctx.sampleRate*0.4,ctx.sampleRate),d=b.getChannelData(0); for(var i=0;i<d.length;i++) d[i]=Math.random()*2-1; noise=b; }
        if(ctx.state==='suspended') ctx.resume();
        playing=true; playBtn.classList.add('on'); playBtn.setAttribute('aria-pressed','true');
        step=0; tick(); timer=setInterval(tick, interval());
      }
      function stop(){
        playing=false; if(timer) clearInterval(timer); timer=null;
        playBtn.classList.remove('on'); playBtn.setAttribute('aria-pressed','false');
        rows.forEach(function(r){ [].forEach.call(r.querySelectorAll('[data-seq-cell]'), function(c){ c.classList.remove('play'); }); });
      }
      playBtn.addEventListener('click', function(){ playing?stop():start(); });
      if(bpm){
        bpm.addEventListener('input', function(){
          if(bpmOut) bpmOut.textContent=bpm.value+' BPM';
          bpm.style.setProperty('--pct', ((bpm.value-bpm.min)/(bpm.max-bpm.min)*100)+'%');
          if(playing){ clearInterval(timer); timer=setInterval(tick, interval()); }
        });
        bpm.dispatchEvent(new Event('input'));
      }
    });
  }

  /* ---------- colour helpers (palette + contrast) ---------- */
  function hexToRgb(h){ h=(h||'').replace('#',''); if(h.length===3) h=h[0]+h[0]+h[1]+h[1]+h[2]+h[2]; var n=parseInt(h,16)||0; return [(n>>16)&255,(n>>8)&255,n&255]; }
  function rgbToHex(r,g,b){ function c(x){ x=Math.round(Math.max(0,Math.min(255,x))).toString(16); return x.length<2?'0'+x:x; } return '#'+c(r)+c(g)+c(b); }
  function rgbToHsl(r,g,b){ r/=255;g/=255;b/=255; var mx=Math.max(r,g,b),mn=Math.min(r,g,b),h,s,l=(mx+mn)/2; if(mx===mn){h=s=0;} else { var d=mx-mn; s=l>0.5?d/(2-mx-mn):d/(mx+mn); if(mx===r)h=(g-b)/d+(g<b?6:0); else if(mx===g)h=(b-r)/d+2; else h=(r-g)/d+4; h*=60; } return [h,s*100,l*100]; }
  function hslToHex(h,s,l){ h=((h%360)+360)%360; s=Math.max(0,Math.min(100,s))/100; l=Math.max(0,Math.min(100,l))/100; var c=(1-Math.abs(2*l-1))*s, x=c*(1-Math.abs((h/60)%2-1)), m=l-c/2, r=0,g=0,b=0; if(h<60){r=c;g=x;}else if(h<120){r=x;g=c;}else if(h<180){g=c;b=x;}else if(h<240){g=x;b=c;}else if(h<300){r=x;b=c;}else{r=c;b=x;} return rgbToHex((r+m)*255,(g+m)*255,(b+m)*255); }
  function relLum(r,g,b){ var a=[r,g,b].map(function(v){ v/=255; return v<=0.03928? v/12.92 : Math.pow((v+0.055)/1.055,2.4); }); return 0.2126*a[0]+0.7152*a[1]+0.0722*a[2]; }

  /* ---------- palette harmony (seed colour → scheme) ---------- */
  function setupPalette(){
    [].forEach.call(document.querySelectorAll('[data-palette]'), function(z){
      var seeds=[].slice.call(z.querySelectorAll('[data-pal-seed]'));
      var schemes=[].slice.call(z.querySelectorAll('[data-pal-scheme]'));
      var out=z.querySelector('[data-pal-out]'); if(!out) return;
      var seed=seeds.length? seeds[0].getAttribute('data-pal-seed') : '#c14bff';
      var scheme=schemes.length? schemes[0].getAttribute('data-pal-scheme') : 'analogous';
      function cl(v){ return Math.max(5,Math.min(95,v)); }
      function build(){
        var hsl=rgbToHsl.apply(null,hexToRgb(seed)), h=hsl[0], s=hsl[1], l=hsl[2];
        function H(dh,ds,dl){ return hslToHex(h+dh, cl(s+(ds||0)), cl(l+(dl||0))); }
        var cols;
        if(scheme==='analogous') cols=[H(-40),H(-20),H(0),H(20),H(40)];
        else if(scheme==='complement') cols=[H(0,0,18),H(0,0,0),H(0,-22,-12),H(180,0,2),H(180,0,-16)];
        else if(scheme==='triad') cols=[H(0,0,16),H(0,0,0),H(120,0,2),H(240,0,2),H(0,0,-18)];
        else if(scheme==='tetrad') cols=[H(0,0,0),H(90,0,4),H(180,0,2),H(270,0,4),H(0,0,-18)];
        else cols=[H(0,0,30),H(0,0,15),H(0,0,0),H(0,0,-15),H(0,0,-28)];
        out.innerHTML='';
        cols.forEach(function(hex){
          var on=relLum.apply(null,hexToRgb(hex))>0.42?'#15131a':'#fff';
          var sw=el('button','pal-sw'); sw.type='button'; sw.style.setProperty('--c',hex); sw.style.setProperty('--on',on);
          sw.innerHTML='<span class="hx">'+hex.toUpperCase()+'</span><span class="cp">Copy</span>';
          sw.addEventListener('click', function(){ try{ if(navigator.clipboard) navigator.clipboard.writeText(hex.toUpperCase()); }catch(e){} sw.classList.add('copied'); setTimeout(function(){ sw.classList.remove('copied'); },900); });
          out.appendChild(sw);
        });
      }
      seeds.forEach(function(b){ b.addEventListener('click', function(){ seed=b.getAttribute('data-pal-seed'); seeds.forEach(function(x){x.classList.toggle('on',x===b);}); build(); }); });
      schemes.forEach(function(b){ b.addEventListener('click', function(){ scheme=b.getAttribute('data-pal-scheme'); schemes.forEach(function(x){x.classList.toggle('on',x===b);}); build(); }); });
      if(seeds[0]) seeds[0].classList.add('on'); if(schemes[0]) schemes[0].classList.add('on');
      build();
    });
  }

  /* ---------- contrast checker (WCAG ratio) ---------- */
  function setupContrast(){
    [].forEach.call(document.querySelectorAll('[data-contrast]'), function(z){
      var fgs=[].slice.call(z.querySelectorAll('[data-ct-fg]'));
      var bgs=[].slice.call(z.querySelectorAll('[data-ct-bg]'));
      var preview=z.querySelector('[data-ct-preview]'), ratioOut=z.querySelector('[data-ct-ratio]');
      var badges=[].slice.call(z.querySelectorAll('[data-ct-badge]'));
      var fg=fgs.length?fgs[0].getAttribute('data-ct-fg'):'#ffffff';
      var bg=bgs.length?bgs[0].getAttribute('data-ct-bg'):'#000000';
      function calc(){
        var l1=relLum.apply(null,hexToRgb(fg)), l2=relLum.apply(null,hexToRgb(bg));
        var ratio=(Math.max(l1,l2)+0.05)/(Math.min(l1,l2)+0.05);
        if(preview){ preview.style.color=fg; preview.style.background=bg; }
        if(ratioOut) ratioOut.textContent=ratio.toFixed(2)+':1';
        badges.forEach(function(b){
          var kind=b.getAttribute('data-ct-badge'), pass = kind==='aaa'?ratio>=7 : kind==='aa-lg'?ratio>=3 : ratio>=4.5;
          b.classList.toggle('pass',pass); b.classList.toggle('fail',!pass);
          var st=b.querySelector('[data-ct-state]'); if(st) st.textContent=pass?'Pass':'Fail';
        });
      }
      fgs.forEach(function(b){ b.addEventListener('click', function(){ fg=b.getAttribute('data-ct-fg'); fgs.forEach(function(x){x.classList.toggle('on',x===b);}); calc(); }); });
      bgs.forEach(function(b){ b.addEventListener('click', function(){ bg=b.getAttribute('data-ct-bg'); bgs.forEach(function(x){x.classList.toggle('on',x===b);}); calc(); }); });
      if(fgs[0])fgs[0].classList.add('on'); if(bgs[0])bgs[0].classList.add('on');
      calc();
    });
  }

  /* ---------- recipe scaler (servings → quantities w/ fractions) ---------- */
  function setupRecipe(){
    [].forEach.call(document.querySelectorAll('[data-recipe]'), function(z){
      var dec=z.querySelector('[data-rc-dec]'), inc=z.querySelector('[data-rc-inc]'), countEl=z.querySelector('[data-rc-count]');
      var ings=[].slice.call(z.querySelectorAll('[data-rc-ing]'));
      var base=parseFloat(z.getAttribute('data-base'))||4;
      var min=parseInt(z.getAttribute('data-min'))||1, max=parseInt(z.getAttribute('data-max'))||12;
      var servings=base;
      function frac(v){
        var whole=Math.floor(v+1e-6), f=v-whole;
        var table=[[0,''],[.25,'\u00bc'],[.333,'\u2153'],[.5,'\u00bd'],[.667,'\u2154'],[.75,'\u00be']];
        var best=table[0], bd=1; table.forEach(function(t){ var d=Math.abs(f-t[0]); if(d<bd){bd=d;best=t;} });
        if(1-f<bd){ whole++; best=table[0]; }
        if(!whole && !best[1]) return '0';
        return (whole?whole:'')+(best[1]?(whole?' ':'')+best[1]:'');
      }
      function render(){
        if(countEl) countEl.textContent=servings;
        ings.forEach(function(it){
          var q=parseFloat(it.getAttribute('data-qty'))||0, unit=it.getAttribute('data-unit')||'';
          var scaled=q*servings/base, out=it.querySelector('[data-rc-out]'); if(!out) return;
          var txt;
          if(it.hasAttribute('data-frac')) txt=frac(scaled)+(unit?' '+unit:'');
          else { var r= scaled<20?Math.round(scaled):Math.round(scaled/5)*5; txt=r+(unit?' '+unit:''); }
          out.textContent=txt;
        });
      }
      if(dec) dec.addEventListener('click', function(){ servings=Math.max(min,servings-1); render(); });
      if(inc) inc.addEventListener('click', function(){ servings=Math.min(max,servings+1); render(); });
      render();
    });
  }

  /* ---------- availability grid (week × slot → plan) ---------- */
  function setupHeat(){
    [].forEach.call(document.querySelectorAll('[data-heat]'), function(z){
      var cells=[].slice.call(z.querySelectorAll('[data-heat-cell]'));
      var countEl=z.querySelector('[data-heat-count]'), planEl=z.querySelector('[data-heat-plan]'), dayEl=z.querySelector('[data-heat-day]');
      if(!cells.length) return;
      function render(){
        var n=0, byDay={};
        cells.forEach(function(c){ if(c.classList.contains('on')){ n++; var d=c.getAttribute('data-day'); byDay[d]=(byDay[d]||0)+1; } });
        if(countEl) countEl.textContent=n;
        if(planEl) planEl.textContent= n===0?'\u2014' : n<=2?'Reset' : n<=4?'Build' : 'Peak';
        if(dayEl){ var best='\u2014',bv=0; for(var d in byDay){ if(byDay[d]>bv){bv=byDay[d];best=d;} } dayEl.textContent= n?best:'\u2014'; }
      }
      cells.forEach(function(c){
        c.setAttribute('role','checkbox'); c.setAttribute('aria-checked','false');
        c.addEventListener('click', function(){ var on=c.classList.toggle('on'); c.setAttribute('aria-checked',on?'true':'false'); render(); });
      });
      render();
    });
  }

  /* ---------- floor-plan picker (tap tables → reservation) ---------- */
  function setupFloorplan(){
    [].forEach.call(document.querySelectorAll('[data-floorplan]'), function(z){
      var tables=[].slice.call(z.querySelectorAll('[data-fp-table]'));
      var tCount=z.querySelector('[data-fp-tables]'), sCount=z.querySelector('[data-fp-seats]'), listEl=z.querySelector('[data-fp-list]');
      if(!tables.length) return;
      function render(){
        var n=0, seats=0, names=[];
        tables.forEach(function(t){ if(t.classList.contains('sel')){ n++; seats+=parseInt(t.getAttribute('data-cap'))||0; names.push(t.getAttribute('data-name')||''); } });
        if(tCount) tCount.textContent=n;
        if(sCount) sCount.textContent=seats;
        if(listEl) listEl.textContent= names.length? names.join(' \u00b7 ') : 'No tables selected yet';
      }
      tables.forEach(function(t){
        if(t.hasAttribute('data-taken')){ t.setAttribute('aria-disabled','true'); return; }
        t.setAttribute('role','checkbox'); t.setAttribute('aria-checked','false');
        t.addEventListener('click', function(){ var on=t.classList.toggle('sel'); t.setAttribute('aria-checked',on?'true':'false'); render(); });
      });
      render();
    });
  }

  /* ---------- lookbook mixer (per-slot prev/next → total) ---------- */
  function setupLookbook(){
    [].forEach.call(document.querySelectorAll('[data-lookbook]'), function(z){
      var slots=[].slice.call(z.querySelectorAll('[data-lb-slot]'));
      var totalEl=z.querySelector('[data-lb-total]');
      var currency=z.getAttribute('data-currency')||'';
      function total(){ var sum=0; slots.forEach(function(s){ if(s._cur) sum+=parseFloat(s._cur.getAttribute('data-price'))||0; }); if(totalEl) totalEl.textContent=currency+sum; }
      slots.forEach(function(slot){
        var opts=[].slice.call(slot.querySelectorAll('[data-lb-opt]'));
        var nameEl=slot.querySelector('[data-lb-name]'), priceEl=slot.querySelector('[data-lb-price]'), swEl=slot.querySelector('[data-lb-sw]');
        var idx=0;
        function show(){
          opts.forEach(function(o,j){ o.style.display=j===idx?'':'none'; });
          var o=opts[idx]; slot._cur=o;
          if(nameEl) nameEl.textContent=o.getAttribute('data-name');
          if(priceEl) priceEl.textContent=currency+(parseFloat(o.getAttribute('data-price'))||0);
          if(swEl){ var col=o.getAttribute('data-color'); if(col) swEl.style.background=col; }
          total();
        }
        var prev=slot.querySelector('[data-lb-prev]'), next=slot.querySelector('[data-lb-next]');
        if(prev) prev.addEventListener('click', function(){ idx=(idx-1+opts.length)%opts.length; show(); });
        if(next) next.addEventListener('click', function(){ idx=(idx+1)%opts.length; show(); });
        if(opts.length) show();
      });
      total();
    });
  }

  /* ---------- 360 spin viewer (drag to rotate frames) ---------- */
  function setupSpin(){
    [].forEach.call(document.querySelectorAll('[data-spin]'), function(z){
      var stage=z.querySelector('[data-spin-stage]')||z;
      var obj=z.querySelector('[data-spin-obj]');
      var angleEl=z.querySelector('[data-spin-angle]');
      var frameEl=z.querySelector('[data-spin-frame]');
      var dot=z.querySelector('[data-spin-dot]');
      var prog=z.querySelector('[data-spin-prog]');
      var frames=parseInt(z.getAttribute('data-frames'))||24;
      var frame=0, dragging=false, startX=0, startFrame=0;
      function render(){
        var deg=Math.round(frame*(360/frames));
        if(obj){ obj.style.setProperty('--f', frame); obj.style.setProperty('--deg', deg+'deg'); }
        if(angleEl) angleEl.textContent=deg+'\u00b0';
        if(frameEl) frameEl.textContent=(frame+1<10?'0':'')+(frame+1)+' / '+frames;
        if(dot) dot.style.setProperty('--deg', deg+'deg');
        if(prog) prog.style.setProperty('--pct', (frame/(frames-1)*100)+'%');
        stage.setAttribute('aria-valuenow', deg);
      }
      function setFrame(f){ frame=((f%frames)+frames)%frames; render(); }
      function cx(e){ return e.touches&&e.touches[0]?e.touches[0].clientX:e.clientX; }
      function down(e){ dragging=true; startX=cx(e); startFrame=frame; z.classList.add('grabbing'); if(e.cancelable) e.preventDefault(); }
      function move(e){ if(!dragging) return; var px=Math.max(6, stage.offsetWidth/frames); setFrame(startFrame + Math.round((cx(e)-startX)/px)); if(e.cancelable) e.preventDefault(); }
      function up(){ dragging=false; z.classList.remove('grabbing'); }
      stage.addEventListener('mousedown',down); window.addEventListener('mousemove',move); window.addEventListener('mouseup',up);
      stage.addEventListener('touchstart',down,{passive:false}); window.addEventListener('touchmove',move,{passive:false}); window.addEventListener('touchend',up);
      stage.setAttribute('tabindex','0'); stage.setAttribute('role','slider'); stage.setAttribute('aria-label','Drag to rotate the product'); stage.setAttribute('aria-valuemin','0'); stage.setAttribute('aria-valuemax','359');
      stage.addEventListener('keydown', function(e){ if(e.key==='ArrowLeft'){ setFrame(frame-1); e.preventDefault(); } else if(e.key==='ArrowRight'){ setFrame(frame+1); e.preventDefault(); } });
      var prev=z.querySelector('[data-spin-prev]'), next=z.querySelector('[data-spin-next]');
      if(prev) prev.addEventListener('click', function(){ setFrame(frame-1); });
      if(next) next.addEventListener('click', function(){ setFrame(frame+1); });
      render();
    });
  }

  /* ---------- helpers + injected CSS ---------- */
  function el(t,c){ var e=document.createElement(t); if(c) e.className=c; return e; }
  function injectCSS(){
    var css = ''+
    '.fx-cursor-on, .fx-cursor-on a, .fx-cursor-on button, .fx-cursor-on .btn{cursor:none;}'+
    '.fx-cursor-on.fx-cur-text, .fx-cursor-on.fx-cur-text *{cursor:auto;}'+
    '.fx-cur{position:fixed;top:0;left:0;z-index:9999;pointer-events:none;border-radius:50%;opacity:0;margin-left:0;}'+
    '.fx-cur--dot{width:8px;height:8px;background:var(--fx-cur,#fff);margin:-4px 0 0 -4px;transition:opacity .3s,transform .18s;box-shadow:0 0 12px 2px var(--fx-cur,#fff);}'+
    '.fx-cur--ring{width:46px;height:46px;border:2px solid var(--fx-cur,#fff);margin:-23px 0 0 -23px;transition:opacity .3s,width .3s cubic-bezier(.2,.7,.3,1),height .3s cubic-bezier(.2,.7,.3,1),margin .3s,background .3s,border-color .3s;display:grid;place-items:center;mix-blend-mode:exclusion;}'+
    '.fx-cur-hot .fx-cur--ring{width:80px;height:80px;margin:-40px 0 0 -40px;background:var(--fx-cur,#fff);mix-blend-mode:exclusion;}'+
    '.fx-cur-hot .fx-cur--dot{transform:scale(0);}'+
    '.fx-cur-text .fx-cur--ring,.fx-cur-text .fx-cur--dot{opacity:0!important;}'+
    '.fx-cur--ring.has-label::after{content:attr(data-label);font:600 11px/1 ui-sans-serif,system-ui,sans-serif;letter-spacing:.1em;text-transform:uppercase;color:#000;mix-blend-mode:exclusion;white-space:nowrap;}'+
    '.fx-peek{position:absolute;top:0;left:0;width:230px;height:300px;margin:-150px 0 0 -115px;border-radius:10px;pointer-events:none;z-index:5;opacity:0;transform:scale(.9);transition:opacity .3s,transform .3s;overflow:hidden;background:#2226;backdrop-filter:blur(2px);box-shadow:0 30px 70px -30px rgba(0,0,0,.6);background-image:repeating-linear-gradient(135deg,rgba(255,255,255,.08) 0 16px,transparent 16px 32px);}'+
    '.fx-peek.show{opacity:1;transform:scale(1);}'+
    '.fx-peek::after{content:attr(data-label);position:absolute;left:14px;bottom:12px;right:14px;font:600 10.5px/1.3 ui-sans-serif,system-ui,sans-serif;letter-spacing:.04em;text-transform:uppercase;color:rgba(255,255,255,.85);}'+
    '@media (pointer:coarse){.fx-peek{display:none;}}'+
    /* generic creative fullscreen menu (var-driven) */
    '.fx-menu{position:fixed;inset:0;z-index:95;background:var(--fx-menu-bg,#101012);color:var(--fx-menu-fg,#fff);display:flex;flex-direction:column;justify-content:center;padding:90px clamp(28px,8vw,120px);opacity:0;visibility:hidden;transition:opacity .5s;}'+
    '.fx-menu.open{opacity:1;visibility:visible;}'+
    '.fx-menu::before{content:"";position:absolute;inset:0;background:radial-gradient(58% 68% at 80% 16%,var(--fx-menu-glow,rgba(255,255,255,.10)),transparent 60%);pointer-events:none;}'+
    '.fx-menu__x{position:absolute;top:28px;right:34px;width:48px;height:48px;border:1px solid currentColor;border-radius:50%;background:none;color:inherit;font:300 26px/1 system-ui;cursor:pointer;opacity:.7;transition:opacity .2s,transform .3s;}'+
    '.fx-menu__x:hover{opacity:1;transform:rotate(90deg);}'+
    '.fx-menu__links{display:flex;flex-direction:column;gap:2px;position:relative;}'+
    '.fx-menu__links a{font-family:var(--fx-menu-font,inherit);font-weight:var(--fx-menu-weight,400);font-size:clamp(36px,7.4vw,86px);line-height:1.1;color:inherit;text-decoration:none;display:inline-flex;align-items:baseline;gap:18px;width:fit-content;opacity:0;transform:translateY(34px);transition:color .3s,transform .45s cubic-bezier(.2,.7,.3,1),opacity .45s;}'+
    '.fx-menu.open .fx-menu__links a{opacity:1;transform:none;transition-delay:calc(var(--i,0)*70ms + 140ms);}'+
    '.fx-menu__links a .n{font-family:ui-sans-serif,system-ui,sans-serif;font-size:13px;font-weight:700;letter-spacing:.12em;color:var(--fx-menu-accent,#c89b4e);}'+
    '.fx-menu__links a em{font-style:italic;}'+
    '.fx-menu__links a:hover{color:var(--fx-menu-accent,#c89b4e);transform:translateX(20px);}'+
    '.fx-menu__foot{position:absolute;left:clamp(28px,8vw,120px);right:clamp(28px,8vw,120px);bottom:34px;display:flex;justify-content:space-between;gap:20px;flex-wrap:wrap;border-top:1px solid currentColor;padding-top:18px;font:500 13px/1.4 ui-sans-serif,system-ui,sans-serif;}'+
    '.fx-menu__foot>*{opacity:.6;}'+'.fx-menu__foot a{color:var(--fx-menu-accent,#c89b4e);opacity:1;}'+
    '.fx-menu-trigger{display:inline-flex;align-items:center;gap:10px;background:none;border:0;color:inherit;font:600 12px/1 ui-sans-serif,system-ui,sans-serif;letter-spacing:.14em;text-transform:uppercase;cursor:pointer;}'+
    '.fx-menu-trigger .bars{display:flex;flex-direction:column;gap:4px;}'+
    '.fx-menu-trigger .bars i{width:22px;height:1.5px;background:currentColor;transition:transform .3s;}'+
    '.fx-menu-trigger:hover .bars i:first-child{transform:translateX(5px);}'+
    /* generic spotlight helper */
    '.fx-spot{position:relative;}'+
    '.fx-spot::before{content:"";position:absolute;inset:0;z-index:1;background:radial-gradient(circle 300px at var(--mx,50%) var(--my,50%),var(--fx-spot,rgba(255,255,255,.18)),transparent 55%);pointer-events:none;transition:background .12s;}'+
    /* hotspots */
    '[data-hotspot]{position:relative;}'+
    '[data-dot]{position:absolute;width:26px;height:26px;border-radius:50%;transform:translate(-50%,-50%);cursor:pointer;z-index:3;}'+
    '[data-dot]::before{content:"";position:absolute;inset:7px;border-radius:50%;background:#fff;box-shadow:0 0 0 0 rgba(255,255,255,.5);animation:fxping 2.4s infinite;}'+
    '[data-dot]::after{content:"";position:absolute;inset:0;border:1px solid rgba(255,255,255,.7);border-radius:50%;}'+
    '@keyframes fxping{0%{box-shadow:0 0 0 0 rgba(255,255,255,.45);}70%{box-shadow:0 0 0 14px rgba(255,255,255,0);}100%{box-shadow:0 0 0 0 rgba(255,255,255,0);}}'+
    '.fx-hot__card{position:absolute;left:50%;bottom:30px;transform:translateX(-50%) translateY(8px);width:180px;background:rgba(15,15,18,.92);backdrop-filter:blur(6px);color:#fff;border-radius:10px;padding:12px 14px;opacity:0;pointer-events:none;transition:opacity .25s,transform .25s;z-index:4;box-shadow:0 16px 40px -16px rgba(0,0,0,.6);}'+
    '.fx-hot__card b{display:block;font:600 13px/1.3 ui-sans-serif,system-ui,sans-serif;}'+
    '.fx-hot__card span{font:500 11px/1.4 ui-sans-serif,system-ui,sans-serif;opacity:.7;}'+
    '.fx-hot__card .pr{color:#fff;font-weight:700;}'+
    /* drag-scroll rails */
    '[data-hscroll]{overflow-x:auto;scrollbar-width:none;cursor:grab;-webkit-overflow-scrolling:touch;}'+
    '[data-hscroll]::-webkit-scrollbar{display:none;}'+
    '[data-hscroll].grab{cursor:grabbing;}'+
    '[data-hscroll]>*{scroll-snap-align:start;}'+
    /* paint smear */
    '.fx-smear{position:absolute;border-radius:50%;transform:translate(-50%,-50%) scale(1);pointer-events:none;mix-blend-mode:screen;filter:blur(2px);transition:opacity .9s ease,transform .9s ease;z-index:2;}'+
    /* interactive zones (finder / builder / mixer / projector) */
    '.fxf-opts{display:flex;flex-wrap:wrap;gap:10px;}'+
    '[data-finder-opt]{cursor:pointer;border:1px solid var(--fx-zone-line,rgba(127,127,127,.32));background:none;color:inherit;font:600 13px/1 ui-sans-serif,system-ui,sans-serif;letter-spacing:.02em;padding:13px 20px;border-radius:999px;transition:background .2s,color .2s,border-color .2s,transform .2s;}'+
    '[data-finder-opt]:hover{transform:translateY(-2px);}'+
    '[data-finder-opt].on{background:var(--fx-zone-accent,currentColor);color:var(--fx-zone-on,#fff);border-color:transparent;}'+
    '[data-finder-res]:not(.show){display:none;}'+
    '[data-finder-res].show{animation:fxfade .45s ease;}'+
    '@keyframes fxfade{from{opacity:0;transform:translateY(12px);}to{opacity:1;transform:none;}}'+
    '.fxb-step{display:inline-flex;align-items:center;border:1px solid var(--fx-zone-line,rgba(127,127,127,.32));border-radius:999px;overflow:hidden;}'+
    '[data-bld-inc],[data-bld-dec]{width:36px;height:36px;border:0;background:none;color:inherit;font:400 19px/1 system-ui;cursor:pointer;transition:background .2s;}'+
    '[data-bld-inc]:hover,[data-bld-dec]:hover{background:var(--fx-zone-bg,rgba(127,127,127,.12));}'+
    '[data-bld-count]{min-width:30px;text-align:center;font:700 15px/1 ui-sans-serif,system-ui,sans-serif;}'+
    '.fxm-swatches{display:flex;flex-wrap:wrap;gap:10px;}'+
    '[data-mix]{width:44px;height:44px;border-radius:50%;cursor:pointer;border:2px solid transparent;background:var(--sw);box-shadow:inset 0 0 0 1px rgba(0,0,0,.1);transition:transform .2s,border-color .2s;}'+
    '[data-mix]:hover{transform:scale(1.1);}'+
    '[data-mix].on{border-color:var(--fx-zone-accent,currentColor);transform:scale(1.12);}'+
    '[data-mix-preview]{transition:background .45s ease;}'+
    '[data-project-input]{-webkit-appearance:none;appearance:none;width:100%;height:6px;border-radius:999px;background:linear-gradient(90deg,var(--fx-zone-accent,currentColor) var(--pct,40%),var(--fx-zone-line,rgba(127,127,127,.3)) var(--pct,40%));outline:none;cursor:pointer;}'+
    '[data-project-input]::-webkit-slider-thumb{-webkit-appearance:none;width:22px;height:22px;border-radius:50%;background:var(--fx-zone-accent,currentColor);border:3px solid var(--fx-zone-thumb,#fff);box-shadow:0 2px 8px rgba(0,0,0,.25);cursor:grab;}'+
    '[data-project-input]::-moz-range-thumb{width:22px;height:22px;border-radius:50%;background:var(--fx-zone-accent,currentColor);border:3px solid var(--fx-zone-thumb,#fff);cursor:grab;}'+
    /* shared range slider (type-tester / scrub / timezone / bakers) */
    '.fx-range{-webkit-appearance:none;appearance:none;width:100%;height:6px;border-radius:999px;background:linear-gradient(90deg,var(--fx-zone-accent,currentColor) var(--pct,40%),var(--fx-zone-line,rgba(127,127,127,.3)) var(--pct,40%));outline:none;cursor:pointer;}'+
    '.fx-range::-webkit-slider-thumb{-webkit-appearance:none;width:20px;height:20px;border-radius:50%;background:var(--fx-zone-accent,currentColor);border:3px solid var(--fx-zone-thumb,#fff);box-shadow:0 2px 8px rgba(0,0,0,.25);cursor:grab;}'+
    '.fx-range::-moz-range-thumb{width:20px;height:20px;border-radius:50%;background:var(--fx-zone-accent,currentColor);border:3px solid var(--fx-zone-thumb,#fff);cursor:grab;}'+
    /* timeline scrubber panels */
    '[data-scrub-panel]:not(.show){display:none;}'+
    '[data-scrub-panel].show{animation:fxfade .4s ease;}';
    var s=document.createElement('style'); s.textContent=css; document.head.appendChild(s);
  }
})();
