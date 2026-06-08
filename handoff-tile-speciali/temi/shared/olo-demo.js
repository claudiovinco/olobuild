/* ============================================================
   OLObuild demo collection — shared behaviour
   nav solidify · scroll reveals · counters · tile inspector
   ============================================================ */
(function(){
  function init(){
    /* ---- nav solidify ---- */
    var nav=document.getElementById('nav');
    if(nav){
      var raw=nav.getAttribute('data-solid-at')||'40';
      var compute=function(){
        if(raw.indexOf('vh')>-1) return window.innerHeight*(parseFloat(raw)/100);
        return parseFloat(raw);
      };
      var onScroll=function(){nav.classList.toggle('solid',(window.scrollY||0)>compute());};
      window.addEventListener('scroll',onScroll,{passive:true});onScroll();
    }

    /* ---- reveals ---- */
    var io=new IntersectionObserver(function(es){
      es.forEach(function(e){if(e.isIntersecting){e.target.classList.add('in');io.unobserve(e.target);}});
    },{threshold:.12,rootMargin:'0px 0px -8% 0px'});
    document.querySelectorAll('.reveal,.reveal-stagger').forEach(function(el,i){
      el.style.transitionDelay=(el.classList.contains('reveal-stagger')?0:(i%6)*40)+'ms';
      io.observe(el);
    });
    // stagger children delays
    document.querySelectorAll('.reveal-stagger').forEach(function(g){
      [].forEach.call(g.children,function(c,i){c.style.transitionDelay=(i*70)+'ms';});
    });

    /* ---- counters ---- */
    var cio=new IntersectionObserver(function(es){
      es.forEach(function(e){
        if(!e.isIntersecting)return;cio.unobserve(e.target);
        var el=e.target,end=parseFloat(el.dataset.count),suffix=el.dataset.suffix||'',
            dec=(el.dataset.count.indexOf('.')>-1)?1:0,t0=null,dur=1400;
        function step(t){if(!t0)t0=t;var p=Math.min(1,(t-t0)/dur);
          var v=(end*(1-Math.pow(1-p,3)));
          el.textContent=(dec?v.toFixed(1):Math.round(v))+(p===1?suffix:'');
          if(p<1)requestAnimationFrame(step);}
        requestAnimationFrame(step);
      });
    },{threshold:.5});
    document.querySelectorAll('.count[data-count]').forEach(function(el){cio.observe(el);});

    /* ---- tile inspector ---- */
    var btn=document.getElementById('tiletoggle');
    if(btn){
      var lbl=btn.querySelector('.t');
      btn.addEventListener('click',function(){
        var on=document.body.classList.toggle('show-tiles');
        btn.classList.toggle('on',on);
        if(lbl)lbl.textContent=on?'Nascondi tile':'Mostra tile';
      });
    }
  }
  if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',init);else init();
})();
